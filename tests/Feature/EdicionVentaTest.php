<?php

namespace Tests\Feature;

use App\Models\Factura;
use App\Models\Movimiento;
use App\Models\User;
use App\Utils\CuentaHermano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EdicionVentaTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;

    private User $hermana;

    private int $sucursal;

    private int $venta;

    private int $producto;

    private int $otroProducto;

    private int $detalle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 10)->setTime(12, 0));
        $this->sucursal = DB::table('sucursales')->insertGetId(['nombre' => 'Central']);
        $this->usuario = User::factory()->create(['sucursal_id' => $this->sucursal, 'name' => 'Joel']);
        $this->hermana = User::factory()->create(['sucursal_id' => $this->sucursal, 'name' => 'Hermana']);
        $cliente = DB::table('clientes')->insertGetId(['nombres' => 'Cliente']);
        DB::table('cajas')->insert(['sucursal_id' => $this->sucursal, 'estado' => 'Abierta']);
        $this->producto = DB::table('productos')->insertGetId(['nombre' => 'Zapato Joel', 'usuario_duenio_id' => $this->usuario->id, 'precio_compra' => 40, 'precio_venta' => 100, 'tipo' => 'PRODUCTO', 'control_stock' => true]);
        $this->otroProducto = DB::table('productos')->insertGetId(['nombre' => 'Zapato hermana', 'usuario_duenio_id' => $this->hermana->id, 'precio_compra' => 20, 'precio_venta' => 50, 'tipo' => 'PRODUCTO', 'control_stock' => true]);
        $this->venta = DB::table('facturas')->insertGetId(['usuario_creador_id' => $this->usuario->id, 'cliente_id' => $cliente, 'sucursal_id' => $this->sucursal, 'fecha' => '2026-11-10 09:00:00', 'numero_recibo' => 10, 'facturado' => 'No', 'total' => 200, 'descuento_adicional' => 0, 'estado_pago' => 'DEUDA']);
        $this->detalle = DB::table('detalles')->insertGetId(['factura_id' => $this->venta, 'producto_id' => $this->producto, 'usuario_duenio_id' => $this->usuario->id, 'nombre_producto' => 'Zapato Joel', 'precio_compra' => 40, 'precio' => 100, 'cantidad' => 2, 'descuento' => 0, 'total' => 200, 'importe' => 200, 'fecha' => '2026-11-10 09:00:00']);
        foreach ([$this->producto, $this->otroProducto] as $id) {
            DB::table('movimientos')->insert(['sucursal_id' => $this->sucursal, 'producto_id' => $id, 'ingreso' => 10, 'salida' => 0, 'fecha' => '2026-11-10 08:00:00']);
        }
        DB::table('movimientos')->insert(['sucursal_id' => $this->sucursal, 'producto_id' => $this->producto, 'detalle_id' => $this->detalle, 'ingreso' => 0, 'salida' => 2, 'fecha' => '2026-11-10 09:00:00']);
        foreach (['EFECTIVO' => 60, 'QR' => 40] as $medio => $monto) {
            DB::table('pagos')->insert(['sucursal_id' => $this->sucursal, 'factura_id' => $this->venta, 'monto' => $monto, 'estado' => 'INGRESO', 'tipo_pago' => $medio, 'fecha' => '2026-11-10 09:00:00']);
        }
        $this->actingAs($this->usuario);
    }

    private function formulario(): array
    {
        $respuesta = $this->postJson('/factura/ajaxFormularioEditarVenta', ['factura_id' => $this->venta], ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('estado', true);
        preg_match('/name="version" value="([^"]+)"/', $respuesta->json('data.formulario'), $coincidencia);

        return ['factura_id' => $this->venta, 'version' => $coincidencia[1], 'items' => [['detalle_id' => $this->detalle, 'producto_id' => $this->producto, 'cantidad' => 2]]];
    }

    private function guardar(array $datos): TestResponse
    {
        return $this->postJson('/factura/guardarEdicionVenta', $datos, ['X-Requested-With' => 'XMLHttpRequest']);
    }

    public function test_administrador_puede_editar_ventas_ajenas_y_vendedor_solo_las_propias(): void
    {
        DB::table('roles')->insert([['id' => 1, 'nombre' => 'Administrador'], ['id' => 2, 'nombre' => 'Vendedor']]);
        $this->usuario->forceFill(['rol_id' => 2])->save();
        $datos = $this->formulario();
        $this->guardar($datos)->assertOk();

        $this->hermana->forceFill(['rol_id' => 2])->save();
        $this->actingAs($this->hermana);
        $this->postJson('/factura/ajaxFormularioEditarVenta', ['factura_id' => $this->venta], ['X-Requested-With' => 'XMLHttpRequest'])->assertForbidden();
        $this->guardar($datos)->assertForbidden();
        $listado = $this->postJson('/factura/ajaxListadoFacturas', [], ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $this->assertStringNotContainsString('onclick="editarVenta(', $listado->json('data.listado'));

        $this->hermana->forceFill(['rol_id' => 1])->save();
        $datos = $this->formulario();
        $this->guardar($datos)->assertOk()->assertJsonPath('estado', true);
    }

    public function test_listado_y_modal_muestran_la_edicion_y_los_detalles(): void
    {
        $this->get('/factura/listado')->assertOk()->assertSee('modalEditarVenta');
        $respuesta = $this->postJson('/factura/ajaxListadoFacturas', [], ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $this->assertStringContainsString('Editar venta', $respuesta->json('data.listado'));
        $this->formulario();
    }

    public function test_agregar_producto_y_aumentar_cantidad_recalcula_sin_crear_otra_venta_ni_pagos(): void
    {
        $datos = $this->formulario();
        $datos['items'][0]['cantidad'] = 3;
        $datos['items'][] = ['producto_id' => $this->otroProducto, 'cantidad' => 2];
        $this->guardar($datos)->assertOk()->assertJsonPath('estado', true)->assertJsonPath('data.total', 400)->assertJsonPath('data.saldo', 300);
        $this->assertDatabaseCount('facturas', 1);
        $this->assertDatabaseCount('pagos', 2);
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'numero_recibo' => 10, 'total' => 400, 'estado_pago' => 'DEUDA']);
        $this->assertSame(7.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->producto));
        $this->assertSame(8.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->otroProducto));
        $this->assertDatabaseHas('detalles', ['factura_id' => $this->venta, 'producto_id' => $this->otroProducto, 'usuario_duenio_id' => $this->hermana->id, 'cantidad' => 2]);
        $resumen = (new CuentaHermano)->obtener($this->sucursal, '2026-11-10 00:00:00', '2026-11-10 23:59:59')['resumen'];
        $this->assertSame(7500, $resumen[$this->usuario->id]['cobrado']);
        $this->assertSame(2500, $resumen[$this->hermana->id]['cobrado']);
    }

    public function test_quitar_producto_devuelve_stock_y_actualiza_pagado(): void
    {
        $datos = $this->formulario();
        $datos['items'] = [['producto_id' => $this->otroProducto, 'cantidad' => 2]];
        $this->guardar($datos)->assertOk()->assertJsonPath('data.total', 100)->assertJsonPath('data.saldo', 0);
        $this->assertSoftDeleted('detalles', ['id' => $this->detalle]);
        $this->assertSame(10.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->producto));
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'estado_pago' => 'PAGADO']);
    }

    public function test_agregar_a_venta_pagada_abre_deuda_solo_por_lo_agregado(): void
    {
        DB::table('pagos')->insert(['factura_id' => $this->venta, 'sucursal_id' => $this->sucursal, 'monto' => 100, 'estado' => 'INGRESO', 'tipo_pago' => 'TRANSFERENCIA', 'fecha' => now()]);
        DB::table('facturas')->where('id', $this->venta)->update(['estado_pago' => 'PAGADO']);
        $datos = $this->formulario();
        $datos['items'][] = ['producto_id' => $this->otroProducto, 'cantidad' => 1];
        $this->guardar($datos)->assertOk()->assertJsonPath('data.total', 250)->assertJsonPath('data.saldo', 50);
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'estado_pago' => 'DEUDA']);
        $this->assertDatabaseCount('pagos', 3);
    }

    public function test_no_permite_quitar_lo_ya_cobrado_ni_dejar_venta_vacia(): void
    {
        $datos = $this->formulario();
        $datos['items'][0]['cantidad'] = 0.5;
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('items');
        $datos['items'] = [];
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'total' => 200]);
        $this->assertSame(8.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->producto));
    }

    public function test_stock_insuficiente_y_producto_repetido_se_validan_en_conjunto(): void
    {
        $datos = $this->formulario();
        $datos['items'][0]['cantidad'] = 9;
        $datos['items'][] = ['producto_id' => $this->producto, 'cantidad' => 2];
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'total' => 200]);
        $this->assertSame(8.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->producto));
    }

    public function test_conserva_precio_duenio_y_descuentos_originales(): void
    {
        DB::table('productos')->where('id', $this->producto)->update(['precio_venta' => 120, 'usuario_duenio_id' => $this->hermana->id]);
        DB::table('detalles')->where('id', $this->detalle)->update(['descuento' => 10, 'importe' => 190]);
        DB::table('facturas')->where('id', $this->venta)->update(['descuento_adicional' => 20, 'total' => 170]);
        $datos = $this->formulario();
        $datos['items'][0]['cantidad'] = 3;
        $datos['items'][0]['precio'] = 1;
        $this->guardar($datos)->assertOk()->assertJsonPath('data.total', 270);
        $this->assertDatabaseHas('detalles', ['id' => $this->detalle, 'precio' => 100, 'usuario_duenio_id' => $this->usuario->id, 'descuento' => 10]);
    }

    public function test_rechaza_edicion_desactualizada_o_con_detalles_ajenos(): void
    {
        $datos = $this->formulario();
        DB::table('pagos')->insert(['factura_id' => $this->venta, 'sucursal_id' => $this->sucursal, 'monto' => 10, 'estado' => 'INGRESO', 'tipo_pago' => 'QR', 'fecha' => now()]);
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('venta');
        $datos = $this->formulario();
        $datos['items'][0]['detalle_id'] = 99999;
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('items');
    }

    public function test_anulada_facturada_otra_sucursal_y_caja_cerrada_no_se_editan(): void
    {
        $datos = $this->formulario();
        DB::table('facturas')->where('id', $this->venta)->update(['estado' => 'Anulado']);
        $this->guardar($datos)->assertUnprocessable();
        DB::table('facturas')->where('id', $this->venta)->update(['estado' => null, 'facturado' => 'Si']);
        $this->guardar($datos)->assertUnprocessable();
        DB::table('facturas')->where('id', $this->venta)->update(['facturado' => 'No']);
        $otra = DB::table('sucursales')->insertGetId(['nombre' => 'Otra']);
        DB::table('facturas')->where('id', $this->venta)->update(['sucursal_id' => $otra]);
        $this->guardar($datos)->assertForbidden();
        DB::table('facturas')->where('id', $this->venta)->update(['sucursal_id' => $this->sucursal]);
        DB::table('cajas')->update(['estado' => 'Cerrada']);
        $this->guardar($datos)->assertUnprocessable();
    }

    public function test_repartir_de_nuevo_no_puede_dejar_sin_respaldo_salidas_de_un_hermano(): void
    {
        DB::table('pagos')->insert(['sucursal_id' => $this->sucursal, 'usuario_duenio_id' => $this->usuario->id, 'monto' => 60, 'estado' => 'SALIDA', 'tipo_pago' => 'EFECTIVO', 'fecha' => now()]);
        $datos = $this->formulario();
        $datos['items'][] = ['producto_id' => $this->otroProducto, 'cantidad' => 2];
        $this->guardar($datos)->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseHas('facturas', ['id' => $this->venta, 'total' => 200]);
        $this->assertSame(8.0, (float) (new Movimiento)->cantidaDisponile($this->sucursal, $this->producto));
        $this->assertSame(1, Factura::find($this->venta)->detalles()->count());
    }
}
