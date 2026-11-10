<?php

namespace App\Utils;

use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Support\Collection;

class CuentaHermano
{
    public const MEDIOS = ['EFECTIVO', 'QR', 'TRANSFERENCIA'];

    /** Distribuye centavos usando mayores residuos, con desempate estable por dueño. */
    public static function repartir(int $total, array $pesos): array
    {
        ksort($pesos);
        $pesos = array_map(fn ($peso) => max(0, (int) $peso), $pesos);
        $suma = array_sum($pesos);
        $resultado = array_fill_keys(array_keys($pesos), 0);
        if ($total <= 0 || $suma === 0) {
            return $resultado;
        }
        $residuos = [];
        foreach ($pesos as $id => $peso) {
            $resultado[$id] = intdiv($total * $peso, $suma);
            $residuos[$id] = ($total * $peso) % $suma;
        }
        arsort($residuos, SORT_NUMERIC);
        $faltante = $total - array_sum($resultado);
        foreach ($residuos as $id => $residuo) {
            if ($faltante-- <= 0) {
                break;
            }
            $resultado[$id]++;
        }

        return $resultado;
    }

    public function obtener(int $sucursalId, string $inicio, string $fin): array
    {
        $facturas = Factura::with('detalles.duenio')
            ->where('sucursal_id', $sucursalId)->whereNull('estado')
            ->where('fecha', '<=', $fin)->orderBy('id')->get();
        $pagos = Pago::with('duenio', 'usuario')
            ->where('sucursal_id', $sucursalId)->where('fecha', '<=', $fin)
            ->whereIn('estado', ['INGRESO', 'SALIDA'])
            ->orderBy('fecha')->orderBy('id')->get();

        return $this->calcular($facturas, $pagos, $inicio, $fin);
    }

    /** Calcula los saldos con todo el historial y muestra los movimientos del período. */
    public function calcular(Collection $facturas, Collection $pagos, string $inicio, string $fin): array
    {
        $resumen = [];
        $movimientos = collect();
        $ventas = [];
        $saldos = [];
        $vendidos = [];
        $nombres = [0 => 'Sin asignar'];
        $crear = function (int $id) use (&$resumen, &$nombres): void {
            if (! isset($resumen[$id])) {
                $resumen[$id] = [
                    'hermano_id' => $id, 'hermano' => $nombres[$id] ?? 'Hermano #'.$id,
                    'anterior' => 0, 'vendido' => 0, 'pendiente' => 0,
                    'cobrado' => 0, 'ingresos' => 0, 'salidas' => 0, 'disponible' => 0,
                    'medios' => array_fill_keys(self::MEDIOS, 0),
                ];
            }
        };
        foreach ($facturas as $factura) {
            if ($factura->fecha > $fin || $factura->estado !== null || $factura->deleted_at !== null) {
                continue;
            }
            $pesos = [];
            foreach ($factura->detalles as $detalle) {
                $id = (int) $detalle->usuario_duenio_id;
                $duenio = $detalle->duenio;
                if ($duenio) {
                    $nombres[$id] = $duenio->name ?: trim($duenio->nombres.' '.$duenio->ap_paterno.' '.$duenio->ap_materno);
                }
                $pesos[$id] = ($pesos[$id] ?? 0) + max(0, (int) round($detalle->importe * 100));
            }
            if (array_sum($pesos) === 0) {
                $pesos = array_fill_keys(array_keys($pesos) ?: [0], 1);
            }
            $ventas[$factura->id] = $factura;
            $saldos[$factura->id] = self::repartir(max(0, (int) round($factura->total * 100)), $pesos);
            $vendidos[$factura->id] = $saldos[$factura->id];
            if ($factura->fecha >= $inicio) {
                foreach ($saldos[$factura->id] as $id => $monto) {
                    $crear($id);
                    $resumen[$id]['vendido'] += $monto;
                }
            }
        }
        foreach ($pagos as $pago) {
            if (strtoupper((string) $pago->apertura_caja) === 'SI') {
                continue;
            }
            if ($pago->fecha > $fin || $pago->deleted_at !== null || ! in_array($pago->estado, ['INGRESO', 'SALIDA'], true)) {
                continue;
            }
            $monto = max(0, (int) round($pago->monto * 100));
            $esVenta = $pago->factura_id !== null;
            if ($esVenta) {
                if ($pago->estado !== 'INGRESO' || ! isset($ventas[$pago->factura_id])) {
                    continue;
                }
                $pendientes = $saldos[$pago->factura_id];
                $reparto = self::repartir(min($monto, array_sum($pendientes)), $pendientes);
                foreach ($reparto as $id => $importe) {
                    $saldos[$pago->factura_id][$id] -= $importe;
                }
                $exceso = $monto - array_sum($reparto);
                if ($exceso > 0) {
                    $reparto[0] = ($reparto[0] ?? 0) + $exceso;
                }
            } else {
                $id = (int) $pago->usuario_duenio_id;
                if ($pago->duenio) {
                    $nombres[$id] = $pago->duenio->name ?: trim($pago->duenio->nombres.' '.$pago->duenio->ap_paterno.' '.$pago->duenio->ap_materno);
                }
                $reparto = [$id => $monto];
            }
            foreach ($reparto as $id => $importe) {
                if ($importe === 0) {
                    continue;
                }
                $crear($id);
                $signo = $pago->estado === 'SALIDA' ? -1 : 1;
                $medio = $pago->tipo_pago ?: 'SIN MEDIO';
                $resumen[$id]['medios'][$medio] = ($resumen[$id]['medios'][$medio] ?? 0) + $signo * $importe;
                $resumen[$id]['disponible'] += $signo * $importe;
                if ($pago->fecha < $inicio) {
                    $resumen[$id]['anterior'] += $signo * $importe;
                } else {
                    $campo = $pago->estado === 'SALIDA' ? 'salidas' : ($esVenta ? 'cobrado' : 'ingresos');
                    $resumen[$id][$campo] += $importe;
                    $movimientos->push([
                        'pago_id' => $pago->id, 'hermano_id' => $id, 'hermano' => $resumen[$id]['hermano'],
                        'fecha' => $pago->fecha, 'recibo' => $esVenta ? $ventas[$pago->factura_id]->numero_recibo : null,
                        'descripcion' => $pago->descripcion, 'tipo_pago' => $medio,
                        'tipo' => $esVenta ? 'COBRO VENTA' : $pago->estado,
                        'ingreso' => $signo > 0 ? $importe : 0, 'salida' => $signo < 0 ? $importe : 0,
                        'usuario' => $pago->usuario?->name,
                    ]);
                }
            }
        }
        foreach ($saldos as $facturaId => $pendientes) {
            if ($ventas[$facturaId]->fecha >= $inicio) {
                foreach ($pendientes as $id => $monto) {
                    $crear($id);
                    $resumen[$id]['pendiente'] += $monto;
                }
            }
        }

        $detalleVentas = collect();
        foreach ($vendidos as $facturaId => $importes) {
            foreach ($importes as $id => $importe) {
                $detalleVentas->push([
                    'factura_id' => $facturaId, 'hermano_id' => $id,
                    'hermano' => $nombres[$id] ?? 'Hermano #'.$id,
                    'vendido' => $importe, 'cobrado' => $importe - $saldos[$facturaId][$id],
                    'pendiente' => $saldos[$facturaId][$id],
                ]);
            }
        }

        return ['resumen' => collect($resumen), 'movimientos' => $movimientos->sortByDesc('fecha')->values(), 'ventas' => $detalleVentas];
    }
}
