<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos / Movimientos</title>
    <style>
        /* body { font-family: DejaVu Sans, sans-serif; font-size: 12px; } */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        /* table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; } */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px; /* reduce tamaño de fuente solo en tablas */
        }
        th, td {
            border: 1px solid #000;
            padding: 3px; /* reduce padding para que se vea más compacto */
            text-align: left;
        }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Reporte de Pagos / Movimientos</h2>

    <table>
        <thead>
            <tr>
                <th>SUCURSAL</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>USURIO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if ($sucursal_id != null)
                        {{ $sucursal->nombre }}
                    @else
                        TODOS
                    @endif
                </td>
                <td>
                    @if ($fecha_ini != null)
                        {{ $fecha_ini }}
                    @else
                        SIN FECHA
                    @endif
                </td>
                <td>
                    @if ($fecha_fin != null)
                        {{ $fecha_fin }}
                    @else
                        SIN FECHA
                    @endif
                </td>
                <td>
                    @if ($usuario_id != null)
                        {{ $usuarioSelect->nombres." ".$usuarioSelect->ap_paterno." ".$usuarioSelect->ap_materno }}
                    @else
                        TODOS
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sucursal</th>
                <th>Fecha</th>
                <th>Descripcion</th>
                <th>Tipo Pago</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @php
                $monto_apertura      = 0;
                $monto_efectivo      = 0;
                $monto_qr            = 0;
                $monto_tramsferencia = 0;
                $monto_total_venta   = 0;
                $monto_total_salida  = 0;
            @endphp
            @foreach($pagos as $key => $pago)
                @php
                    if($pago->apertura_caja == "Si")
                        $monto_apertura = $monto_apertura + $pago->monto;

                    if($pago->estado === 'INGRESO' && $pago->tipo_pago === 'EFECTIVO' && $pago->apertura_caja == "No")
                        $monto_efectivo = $monto_efectivo + $pago->monto;

                    if($pago->estado === 'INGRESO' && $pago->tipo_pago === 'QR' && $pago->apertura_caja == "No")
                        $monto_qr = $monto_qr + $pago->monto;

                    if($pago->estado === 'INGRESO' && $pago->tipo_pago === 'TRANSFERENCIA' && $pago->apertura_caja == "No")
                        $monto_tramsferencia = $monto_tramsferencia + $pago->monto;

                    if($pago->apertura_caja == "No" && $pago->estado === 'INGRESO')
                        $monto_total_venta = $monto_total_venta + $pago->monto;

                    if($pago->apertura_caja == "No" && $pago->estado === 'SALIDA')
                        $monto_total_salida = $monto_total_salida + $pago->monto;

                @endphp
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $pago->puntoVenta?->sucursal?->nombre }}</td>
                    <td>{{ $pago->fecha }}</td>
                    <td>{{ $pago->descripcion }}</td>
                    <td>{{ $pago->tipo_pago }}</td>
                    <td>{{ number_format($pago->monto, 2, ',', '.') }}</td>
                    <td>{{ $pago->estado }}</td>
                    <td>{{ $pago->usuario->nombres." ".$pago->usuario->ap_paterno." ".$pago->usuario->ap_materno ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>APERTURA CAJA</th>
                <th>TOTAL SALIDA </th>
                <th colspan="2">MONTO EFECTIVO</th>
                <th colspan="2">MONTO QR</th>
                <th>MONTO TRANSFERENCIA</th>
                <th>VENTA TOTAL</th>
            </tr>
            <tr>
                <td>{{ number_format($monto_apertura, 2, ',', '.') }}</td>
                <td>{{ number_format($monto_total_salida, 2, ',', '.') }}</td>
                <td colspan="2">{{ number_format($monto_efectivo, 2, ',', '.') }}</td>
                <td colspan="2">{{ number_format($monto_qr, 2, ',', '.') }}</td>
                <td>{{ number_format($monto_tramsferencia, 2, ',', '.') }}</td>
                <td>{{ number_format($monto_total_venta, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
