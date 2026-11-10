<?php

namespace Tests\Feature;

use App\Models\User;
use App\Utils\CuentaHermano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CuentaHermanoFlujoTest extends TestCase
{
    use RefreshDatabase;

    private User $joel;

    private User $hermana;

    private int $sucursalId;

    private int $categoriaIngreso;

    private int $categoriaSalida;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 10)->setTime(12, 0));
        $this->sucursalId = DB::table('sucursales')->insertGetId(['nombre' => 'Central']);
        $this->joel = User::factory()->create(['name' => 'Joel Flores', 'sucursal_id' => $this->sucursalId]);
        $this->hermana = User::factory()->create(['name' => 'Hermana', 'sucursal_id' => $this->sucursalId]);
        $this->categoriaIngreso = DB::table('categoria_ingreso_salidas')->insertGetId(['nombre' => 'Devolución', 'tipo' => 'INGRESO']);
        $this->categoriaSalida = DB::table('categoria_ingreso_salidas')->insertGetId(['nombre' => 'Taxi', 'tipo' => 'SALIDA']);
        DB::table('cajas')->insert(['sucursal_id' => $this->sucursalId, 'estado' => 'Abierta']);
        $facturaId = DB::table('facturas')->insertGetId(['sucursal_id' => $this->sucursalId, 'total' => 5000, 'fecha' => '2026-11-10 09:00:00', 'numero_recibo' => '1']);
        DB::table('detalles')->insert(['factura_id' => $facturaId, 'usuario_duenio_id' => $this->joel->id, 'importe' => 5000]);
        DB::table('pagos')->insert(['sucursal_id' => $this->sucursalId, 'factura_id' => $facturaId, 'monto' => 2000, 'estado' => 'INGRESO', 'tipo_pago' => 'EFECTIVO', 'fecha' => '2026-11-10 09:00:00']);
        $this->actingAs($this->hermana);
    }

    private function movimiento(string $tipo, string $medio = 'EFECTIVO', int $monto = 100): array
    {
        return ['usuario_duenio_id' => $this->joel->id, 'tipo' => $tipo, 'tipo_pago' => $medio, 'monto' => $monto, 'categoria_id' => $tipo === 'SALIDA' ? $this->categoriaSalida : $this->categoriaIngreso, 'descripcion' => 'Taxi'];
    }

    private function resumen(): array
    {
        return (new CuentaHermano)->obtener($this->sucursalId, '2026-11-10 00:00:00', '2026-11-10 23:59:59')['resumen']->get($this->joel->id);
    }

    public function test_salida_y_devolucion_guardan_el_duenio_distinto_del_cajero(): void
    {
        $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('SALIDA'), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('estado', true);
        $this->assertSame(190000, $this->resumen()['disponible']);
        $this->assertDatabaseHas('pagos', ['estado' => 'SALIDA', 'usuario_duenio_id' => $this->joel->id, 'usuario_creador_id' => $this->hermana->id, 'categoria_ingreso_salida_id' => $this->categoriaSalida, 'monto' => 100]);
        $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('INGRESO'), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('estado', true);
        $this->assertSame(200000, $this->resumen()['disponible']);
        $this->assertSame(300000, $this->resumen()['pendiente']);
    }

    public function test_no_permite_sacar_mas_de_lo_cobrado_ni_usar_otro_medio(): void
    {
        foreach ([$this->movimiento('SALIDA', 'EFECTIVO', 2001), $this->movimiento('SALIDA', 'QR')] as $datos) {
            $this->postJson('/pago/guardarTipoIngresoSalida', $datos, ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('estado', false);
        }
        $this->assertDatabaseCount('pagos', 1);
        $this->assertSame(200000, $this->resumen()['disponible']);
    }

    public function test_qr_y_transferencia_tienen_saldos_independientes(): void
    {
        foreach (['QR', 'TRANSFERENCIA'] as $medio) {
            $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('INGRESO', $medio, 200), ['X-Requested-With' => 'XMLHttpRequest'])->assertJsonPath('estado', true);
            $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('SALIDA', $medio, 100), ['X-Requested-With' => 'XMLHttpRequest'])->assertJsonPath('estado', true);
            $this->assertSame(10000, $this->resumen()['medios'][$medio]);
        }
        $this->assertSame(200000, $this->resumen()['medios']['EFECTIVO']);
    }

    public function test_exige_duenio_categoria_correcta_y_caja_abierta(): void
    {
        $datos = $this->movimiento('SALIDA');
        unset($datos['usuario_duenio_id']);
        $this->postJson('/pago/guardarTipoIngresoSalida', $datos, ['X-Requested-With' => 'XMLHttpRequest'])->assertUnprocessable()->assertJsonValidationErrors('usuario_duenio_id');
        $datos = $this->movimiento('SALIDA');
        $datos['categoria_id'] = $this->categoriaIngreso;
        $this->postJson('/pago/guardarTipoIngresoSalida', $datos, ['X-Requested-With' => 'XMLHttpRequest'])->assertUnprocessable()->assertJsonValidationErrors('categoria_id');
        DB::table('cajas')->update(['estado' => 'Cerrada']);
        $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('SALIDA'), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('estado', false);
        $this->assertDatabaseCount('pagos', 1);
    }

    public function test_vista_y_ajax_siguen_el_flujo_del_listado_y_filtran_duenio(): void
    {
        $this->get('/pago/listado')->assertOk()->assertSee('movimiento_duenio_id');
        $this->get('/pago/listadoHermanos')->assertOk()->assertSee('INGRESOS Y SALIDAS POR HERMANO')->assertSee('formularioIngresoSalida');
        $respuesta = $this->postJson('/pago/ajaxListadoHermanos', ['fecha_ini' => '2026-11-10', 'fecha_fin' => '2026-11-10', 'usuario_duenio_id' => $this->joel->id], ['X-Requested-With' => 'XMLHttpRequest']);
        $respuesta->assertOk()->assertJsonPath('estado', true);
        $this->assertStringContainsString('Joel Flores', $respuesta->json('data.listado'));
        $this->assertStringContainsString('2,000.00', $respuesta->json('data.listado'));
        $this->assertStringContainsString('3,000.00', $respuesta->json('data.listado'));
    }

    public function test_cambiar_duenio_del_producto_no_cambia_la_venta_ya_registrada(): void
    {
        $productoId = DB::table('productos')->insertGetId(['nombre' => 'Calzado', 'usuario_duenio_id' => $this->joel->id]);
        DB::table('detalles')->update(['producto_id' => $productoId]);
        DB::table('productos')->where('id', $productoId)->update(['usuario_duenio_id' => $this->hermana->id]);
        $this->assertSame(200000, $this->resumen()['disponible']);
    }

    public function test_migracion_asigna_ventas_antiguas_sin_inventar_duenios_de_salidas(): void
    {
        $productoId = DB::table('productos')->insertGetId(['nombre' => 'Calzado', 'usuario_duenio_id' => $this->joel->id]);
        DB::table('detalles')->update(['producto_id' => $productoId]);
        $migracion = require database_path('migrations/2026_11_10_192136_add_usuario_duenio_id_to_pagos_and_detalles_tables.php');
        $migracion->down();
        $migracion->up();
        $this->assertDatabaseHas('detalles', ['producto_id' => $productoId, 'usuario_duenio_id' => $this->joel->id]);
        $this->assertDatabaseHas('pagos', ['usuario_duenio_id' => null]);
    }

    public function test_movimientos_de_otra_sucursal_no_permiten_gastar_su_dinero(): void
    {
        $otraSucursal = DB::table('sucursales')->insertGetId(['nombre' => 'Otra']);
        DB::table('pagos')->insert(['sucursal_id' => $otraSucursal, 'usuario_duenio_id' => $this->joel->id, 'monto' => 10000, 'estado' => 'INGRESO', 'tipo_pago' => 'QR', 'fecha' => '2026-11-10 09:00:00']);
        $this->postJson('/pago/guardarTipoIngresoSalida', $this->movimiento('SALIDA', 'QR'), ['X-Requested-With' => 'XMLHttpRequest'])->assertJsonPath('estado', false);
    }
}
