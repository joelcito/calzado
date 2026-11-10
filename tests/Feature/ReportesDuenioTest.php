<?php

namespace Tests\Feature;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ReportesDuenioTest extends TestCase
{
    use RefreshDatabase;

    private array $duenios;

    private int $sucursal;

    private int $venta;

    private array $pdf = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 10)->setTime(12, 0));
        $this->sucursal = DB::table('sucursales')->insertGetId(['nombre' => 'Central']);
        DB::table('roles')->insert(['id' => 1, 'nombre' => 'Administrador']);
        $this->duenios = [];
        foreach (['Joel Flores', 'Luis Flores', 'Nayana Flores'] as $nombre) {
            $this->duenios[] = User::factory()->create(['name' => $nombre, 'sucursal_id' => $this->sucursal, 'rol_id' => 1]);
        }
        $cliente = DB::table('clientes')->insertGetId(['nombres' => 'Cliente', 'cedula' => '123']);
        $this->venta = DB::table('facturas')->insertGetId(['sucursal_id' => $this->sucursal, 'cliente_id' => $cliente, 'numero_recibo' => 10, 'total' => 900, 'descuento_adicional' => 100, 'fecha' => '2026-11-10 09:00:00']);
        foreach ([5, 3, 2] as $i => $cantidad) {
            $costo = [40, 30, 25][$i];
            $producto = DB::table('productos')->insertGetId(['nombre' => 'Calzado '.$i, 'codigo' => 'C'.$i, 'usuario_duenio_id' => $this->duenios[$i]->id, 'precio_compra' => $costo, 'tipo' => 'PRODUCTO']);
            DB::table('detalles')->insert(['factura_id' => $this->venta, 'sucursal_id' => $this->sucursal, 'usuario_duenio_id' => $this->duenios[$i]->id, 'producto_id' => $producto, 'nombre_producto' => 'Calzado '.$i, 'cantidad' => $cantidad, 'precio' => 100, 'precio_compra' => $costo, 'descuento' => 0, 'total' => $cantidad * 100, 'importe' => $cantidad * 100, 'fecha' => '2026-11-10 09:00:00']);
        }
        $this->pago(['factura_id' => $this->venta, 'monto' => 300, 'tipo_pago' => 'QR']);
        $anterior = DB::table('facturas')->insertGetId(['sucursal_id' => $this->sucursal, 'total' => 300, 'fecha' => '2026-11-09 09:00:00']);
        DB::table('detalles')->insert(['factura_id' => $anterior, 'usuario_duenio_id' => $this->duenios[0]->id, 'importe' => 300]);
        $this->pago(['factura_id' => $anterior, 'monto' => 300, 'fecha' => '2026-11-09 09:00:00']);
        $this->pago(['usuario_duenio_id' => $this->duenios[0]->id, 'monto' => 100, 'estado' => 'SALIDA', 'descripcion' => 'Taxi']);
        $this->pago(['usuario_duenio_id' => $this->duenios[0]->id, 'monto' => 100, 'descripcion' => 'Devolución taxi']);
        $this->pago(['monto' => 300, 'apertura_caja' => 'Si']);
        $this->actingAs($this->duenios[0]);
    }

    private function pago(array $valores): void
    {
        DB::table('pagos')->insert(array_merge(['sucursal_id' => $this->sucursal, 'estado' => 'INGRESO', 'tipo_pago' => 'EFECTIVO', 'fecha' => '2026-11-10 10:00:00', 'apertura_caja' => 'No', 'descripcion' => 'VENTA'], $valores));
    }

    private function parametros(?int $duenio = null): array
    {
        return array_filter(['fecha_inicio' => '2026-11-10', 'fecha_fin' => '2026-11-10', 'usuario_duenio_id' => $duenio], fn ($valor) => $valor !== null);
    }

    private function excel(string $reporte, array $filtros): Spreadsheet
    {
        $respuesta = $this->get('/reporte/'.$reporte.'/excel?'.http_build_query($filtros))->assertOk();
        $archivo = $respuesta->baseResponse->getFile()->getPathname();
        $libro = IOFactory::load($archivo);
        $this->assertCount(1, $libro->getActiveSheet()->getDrawingCollection());
        $this->assertSame('Logo San Cayetano Importaciones', $libro->getActiveSheet()->getDrawingCollection()[0]->getName());
        unlink($archivo);

        return $libro;
    }

    private function capturarPdf(): void
    {
        Pdf::shouldReceive('loadView')->andReturnUsing(function ($vista, $datos) {
            $this->pdf = $datos;
            $this->assertStringContainsString('Dueño:', view($vista, $datos)->render());
            $documento = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
            $documento->shouldReceive('setPaper')->with('letter', 'landscape')->andReturnSelf();
            $documento->shouldReceive('stream')->andReturn(response('%PDF-test', 200, ['Content-Type' => 'application/pdf']));

            return $documento;
        });
    }

    public function test_selector_muestra_todos_y_duenios(): void
    {
        $this->get('/reporte/listado')->assertOk()->assertSee('usuario_duenio_id')->assertSee('TODOS')->assertSee('Joel Flores')->assertSee('Luis Flores')->assertSee('Nayana Flores');
    }

    public function test_ventas_y_deudas_reparten_importes_en_pdf_y_excel(): void
    {
        $this->capturarPdf();
        foreach (['ventas', 'cuentas-cobrar'] as $reporte) {
            foreach ([null, $this->duenios[0]->id, $this->duenios[1]->id] as $id) {
                $filtros = $this->parametros($id);
                $this->get('/reporte/'.$reporte.'/pdf?'.http_build_query($filtros))->assertOk();
                $esperado = $id === null ? 900 : ($id === $this->duenios[0]->id ? 450 : 270);
                $this->assertEquals($esperado, $this->pdf['total']);
                $this->assertCount($id === null ? 3 : 1, $this->pdf['datos']);
                if ($reporte === 'cuentas-cobrar') {
                    $this->assertEquals($esperado / 3, $this->pdf['pagado']);
                    $this->assertEquals($esperado * 2 / 3, $this->pdf['saldo']);
                }
                $hoja = $this->excel($reporte, $filtros)->getActiveSheet();
                $filaTotal = $hoja->getHighestRow();
                $this->assertEquals($esperado, $hoja->getCell(($reporte === 'ventas' ? 'I' : 'G').$filaTotal)->getValue());
                $this->assertSame('Dueño', $hoja->getCell('J3')->getValue());
            }
        }
    }

    public function test_utilidad_incluye_descuento_y_conserva_duenio_y_costo_historico(): void
    {
        $this->capturarPdf();
        DB::table('productos')->update(['usuario_duenio_id' => $this->duenios[2]->id, 'precio_compra' => 999]);
        foreach ([null, $this->duenios[0]->id] as $id) {
            $filtros = $this->parametros($id);
            $this->get('/reporte/utilidades/pdf?'.http_build_query($filtros))->assertOk();
            $venta = $id === null ? 900 : 450;
            $costo = $id === null ? 340 : 200;
            $this->assertEquals($venta, $this->pdf['totalVenta']);
            $this->assertEquals($costo, $this->pdf['totalCosto']);
            $this->assertEquals($venta - $costo, $this->pdf['totalUtilidad']);
            $hoja = $this->excel('utilidades', $filtros)->getActiveSheet();
            $ultima = $hoja->getHighestRow();
            $this->assertEquals($venta, $hoja->getCell('L'.$ultima)->getValue());
            $this->assertEquals($costo, $hoja->getCell('M'.$ultima)->getValue());
            $this->assertSame('Dueño', $hoja->getCell('O3')->getValue());
        }
    }

    public function test_flujo_arrastra_saldo_y_respeta_devolucion_medios_y_duenio(): void
    {
        $this->capturarPdf();
        foreach ([null, $this->duenios[0]->id, $this->duenios[1]->id] as $id) {
            $filtros = $this->parametros($id);
            $this->get('/reporte/flujo-efectivo/pdf?'.http_build_query($filtros))->assertOk();
            $esLuis = $id === $this->duenios[1]->id;
            $this->assertEquals($esLuis ? 0 : 300, $this->pdf['saldoAnterior']);
            $this->assertEquals($esLuis ? 90 : ($id === null ? 400 : 250), $this->pdf['totalIngresos']);
            $this->assertEquals($esLuis ? 0 : 100, $this->pdf['totalSalidas']);
            $this->assertEquals($esLuis ? 90 : ($id === null ? 600 : 450), $this->pdf['saldo']);
            $this->assertEquals($esLuis ? 90 : ($id === null ? 300 : 150), $this->pdf['medios']['QR']);
            $hoja = $this->excel('flujo-efectivo', $filtros)->getActiveSheet();
            $resumen = collect($hoja->toArray())->filter(fn ($fila) => in_array($fila[0], ['Saldo anterior', 'Total ingresos', 'Total salidas', 'Disponible final'], true))->mapWithKeys(fn ($fila) => [$fila[0] => $fila[1]]);
            $this->assertEquals($this->pdf['saldo'], $resumen['Disponible final']);
            $this->assertEquals($this->pdf['totalIngresos'], $resumen['Total ingresos']);
            $this->assertSame('Dueño', $hoja->getCell('C3')->getValue());
        }
    }

    public function test_corte_de_pagos_y_ventas_anuladas(): void
    {
        $this->capturarPdf();
        $this->pago(['factura_id' => $this->venta, 'monto' => 600, 'fecha' => '2026-11-11 10:00:00']);
        $this->get('/reporte/cuentas-cobrar/pdf?'.http_build_query($this->parametros()))->assertOk();
        $this->assertEquals(600, $this->pdf['saldo']);
        DB::table('facturas')->where('id', $this->venta)->update(['estado' => 'Anulado']);
        foreach (['ventas', 'cuentas-cobrar', 'utilidades'] as $reporte) {
            $this->get('/reporte/'.$reporte.'/pdf?'.http_build_query($this->parametros()))->assertOk();
            $this->assertCount(0, $this->pdf['datos']);
        }
    }

    public function test_flujo_incluye_abono_recibido_hoy_de_venta_anterior(): void
    {
        $this->capturarPdf();
        $anterior = DB::table('facturas')->where('id', '!=', $this->venta)->first();
        DB::table('facturas')->where('id', $anterior->id)->update(['total' => 500]);
        DB::table('detalles')->where('factura_id', $anterior->id)->update(['importe' => 500]);
        $this->pago(['factura_id' => $anterior->id, 'monto' => 100]);
        $this->get('/reporte/flujo-efectivo/pdf?'.http_build_query($this->parametros($this->duenios[0]->id)))->assertOk();
        $this->assertEquals(350, $this->pdf['totalIngresos']);
        $this->assertEquals(550, $this->pdf['saldo']);
    }

    public function test_importes_con_centavos_cuadran_entre_ventas_y_utilidad(): void
    {
        $this->capturarPdf();
        DB::table('facturas')->where('id', $this->venta)->update(['total' => 899.99, 'descuento_adicional' => 100.01]);
        $this->get('/reporte/ventas/pdf?'.http_build_query($this->parametros()))->assertOk();
        $vendido = $this->pdf['total'];
        $this->get('/reporte/utilidades/pdf?'.http_build_query($this->parametros()))->assertOk();
        $this->assertEqualsWithDelta(899.99, $vendido, 0.001);
        $this->assertEqualsWithDelta($vendido, $this->pdf['totalVenta'], 0.001);
    }

    public function test_productos_totalmente_descontados_conservan_su_costo_y_duenio(): void
    {
        $this->capturarPdf();
        DB::table('pagos')->where('factura_id', $this->venta)->delete();
        DB::table('facturas')->where('id', $this->venta)->update(['total' => 0, 'descuento_adicional' => 0]);
        DB::table('detalles')->where('factura_id', $this->venta)->update(['importe' => 0, 'descuento' => DB::raw('total')]);
        $this->get('/reporte/utilidades/pdf?'.http_build_query($this->parametros($this->duenios[0]->id)))->assertOk();
        $this->assertEquals(0, $this->pdf['totalVenta']);
        $this->assertEquals(200, $this->pdf['totalCosto']);
        $this->assertEquals(-200, $this->pdf['totalUtilidad']);
    }

    public function test_sin_asignar_y_filtro_de_sucursal(): void
    {
        $this->capturarPdf();
        $this->pago(['monto' => 50]);
        $this->get('/reporte/flujo-efectivo/pdf?'.http_build_query($this->parametros(0)))->assertOk();
        $this->assertEquals(50, $this->pdf['saldo']);
        $otra = DB::table('sucursales')->insertGetId(['nombre' => 'Otra']);
        foreach (['ventas', 'cuentas-cobrar', 'utilidades', 'flujo-efectivo'] as $reporte) {
            $this->get('/reporte/'.$reporte.'/pdf?'.http_build_query($this->parametros() + ['sucursal_id' => $otra]))->assertOk();
            $this->assertCount(0, $this->pdf['datos']);
        }
    }

    public function test_generacion_pdf_real_y_excel_sin_datos(): void
    {
        foreach (['ventas', 'cuentas-cobrar', 'utilidades', 'flujo-efectivo'] as $reporte) {
            $respuesta = $this->get('/reporte/'.$reporte.'/pdf?'.http_build_query($this->parametros($this->duenios[0]->id)))->assertOk()->assertHeader('content-type', 'application/pdf');
            $this->assertStringStartsWith('%PDF-', $respuesta->getContent());
            $this->assertStringContainsString('/Subtype /Image', $respuesta->getContent());
            $hoja = $this->excel($reporte, ['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-02'])->getActiveSheet();
            $this->assertNotEmpty($hoja->getCell('A1')->getValue());
        }
    }

    public function test_valida_filtros_en_todas_las_exportaciones(): void
    {
        foreach (['ventas', 'cuentas-cobrar', 'utilidades', 'flujo-efectivo'] as $reporte) {
            foreach (['pdf', 'excel'] as $formato) {
                $this->getJson('/reporte/'.$reporte.'/'.$formato.'?'.http_build_query(['usuario_duenio_id' => 999999, 'fecha_inicio' => '2026-11-10', 'fecha_fin' => '2026-11-09']))->assertUnprocessable();
            }
        }
    }
}
