<?php

namespace Tests\Unit;

use App\Utils\CuentaHermano;
use PHPUnit\Framework\TestCase;

class CuentaHermanoTest extends TestCase
{
    private function factura(array $importes, int $total = 90000, string $fecha = '2026-11-09 10:00:00'): object
    {
        $detalles = collect();
        foreach ($importes as $id => $importe) {
            $detalles->push((object) ['usuario_duenio_id' => $id, 'importe' => $importe / 100, 'duenio' => (object) ['name' => 'Hermano '.$id]]);
        }

        return (object) ['id' => 1, 'total' => $total / 100, 'fecha' => $fecha, 'detalles' => $detalles, 'numero_recibo' => 1, 'estado' => null, 'deleted_at' => null];
    }

    private function pago(int $id, int $monto, array $extra = []): object
    {
        return (object) array_merge([
            'id' => $id, 'monto' => $monto / 100, 'factura_id' => 1, 'estado' => 'INGRESO', 'deleted_at' => null,
            'fecha' => '2026-11-10 10:00:00', 'tipo_pago' => 'EFECTIVO', 'usuario_duenio_id' => null,
            'duenio' => null, 'usuario' => (object) ['name' => 'Cajero'], 'descripcion' => 'VENTA', 'apertura_caja' => 'No',
        ], $extra);
    }

    public function test_venta_compartida_reparte_cobros_de_ventas_anteriores_en_el_dia_del_abono(): void
    {
        $datos = (new CuentaHermano)->calcular(collect([$this->factura([1 => 10000, 2 => 50000, 3 => 30000])]), collect([$this->pago(1, 50000)]), '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(50000, $datos['resumen']->sum('cobrado'));
        $this->assertSame(0, $datos['resumen']->sum('vendido'));
        $this->assertSame(5555, $datos['resumen'][1]['disponible']);
        $this->assertSame(27778, $datos['resumen'][2]['disponible']);
        $this->assertSame(16667, $datos['resumen'][3]['disponible']);
    }

    public function test_salida_y_devolucion_reponen_solo_el_saldo_del_duenio_y_medio(): void
    {
        $factura = $this->factura([1 => 500000, 2 => 600000], 1100000, '2026-11-10 09:00:00');
        $pagos = collect([
            $this->pago(1, 440000),
            $this->pago(2, 10000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'estado' => 'SALIDA', 'descripcion' => 'Taxi']),
        ]);
        $cuenta = new CuentaHermano;
        $datos = $cuenta->calcular(collect([$factura]), $pagos, '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(190000, $datos['resumen'][1]['disponible']);
        $this->assertSame(300000, $datos['resumen'][1]['pendiente']);
        $this->assertSame(240000, $datos['resumen'][2]['disponible']);
        $pagos->push($this->pago(3, 10000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'descripcion' => 'Devolución taxi']));
        $datos = $cuenta->calcular(collect([$factura]), $pagos, '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(200000, $datos['resumen'][1]['disponible']);
        $this->assertSame(200000, $datos['resumen'][1]['medios']['EFECTIVO']);
        $this->assertSame(0, $datos['resumen'][1]['medios']['QR']);
        $this->assertSame(240000, $datos['resumen'][2]['disponible']);
    }

    public function test_descuento_y_multiples_abonos_cuadran_hasta_el_ultimo_centavo(): void
    {
        $factura = $this->factura([1 => 10000, 2 => 50000, 3 => 30000], 81000, '2026-11-10 09:00:00');
        $pagos = collect([$this->pago(1, 50000), $this->pago(2, 31000, ['tipo_pago' => 'QR'])]);
        $datos = (new CuentaHermano)->calcular(collect([$factura]), $pagos, '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(81000, $datos['resumen']->sum('vendido'));
        $this->assertSame(81000, $datos['resumen']->sum('cobrado'));
        $this->assertSame(0, $datos['resumen']->sum('pendiente'));
        $this->assertSame(9000, $datos['resumen'][1]['disponible']);
        $this->assertSame(45000, $datos['resumen'][2]['disponible']);
        $this->assertSame(27000, $datos['resumen'][3]['disponible']);
        $this->assertSame(50000, $datos['resumen']->sum(fn ($fila) => $fila['medios']['EFECTIVO']));
        $this->assertSame(31000, $datos['resumen']->sum(fn ($fila) => $fila['medios']['QR']));
    }

    public function test_saldo_anterior_excluye_pagos_futuros_eliminados_y_aperturas(): void
    {
        $pagos = collect([
            $this->pago(1, 20000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'fecha' => '2026-11-09 10:00:00']),
            $this->pago(2, 5000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'estado' => 'SALIDA']),
            $this->pago(3, 10000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'deleted_at' => '2026-11-10 11:00:00']),
            $this->pago(4, 10000, ['factura_id' => null, 'usuario_duenio_id' => 1, 'fecha' => '2026-11-11 10:00:00']),
            $this->pago(5, 20000, ['factura_id' => null, 'apertura_caja' => 'Si']),
        ]);
        $datos = (new CuentaHermano)->calcular(collect(), $pagos, '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(20000, $datos['resumen'][1]['anterior']);
        $this->assertSame(15000, $datos['resumen'][1]['disponible']);
        $this->assertCount(1, $datos['movimientos']);
        $this->assertFalse($datos['resumen']->has(0));
    }

    public function test_cobros_de_ventas_anuladas_no_cuentan_y_manuales_sin_duenio_no_se_atribuyen(): void
    {
        $factura = $this->factura([1 => 90000]);
        $factura->estado = 'Anulado';
        $datos = (new CuentaHermano)->calcular(collect([$factura]), collect([$this->pago(1, 50000), $this->pago(2, 10000, ['factura_id' => null])]), '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertCount(1, $datos['resumen']);
        $this->assertSame(10000, $datos['resumen'][0]['disponible']);
        $this->assertSame('Sin asignar', $datos['resumen'][0]['hermano']);
    }

    public function test_abonos_de_centavos_no_generan_saldo_negativo(): void
    {
        $pagos = collect();
        for ($id = 1; $id <= 100; $id++) {
            $pagos->push($this->pago($id, 1));
        }
        $datos = (new CuentaHermano)->calcular(collect([$this->factura([1 => 1, 2 => 29, 3 => 70], 100, '2026-11-10 09:00:00')]), $pagos, '2026-11-10 00:00:00', '2026-11-10 23:59:59');
        $this->assertSame(100, $datos['resumen']->sum('cobrado'));
        $this->assertSame(0, $datos['resumen']->sum('pendiente'));
        $this->assertSame(1, $datos['resumen'][1]['disponible']);
        $this->assertSame(29, $datos['resumen'][2]['disponible']);
        $this->assertSame(70, $datos['resumen'][3]['disponible']);
    }
}
