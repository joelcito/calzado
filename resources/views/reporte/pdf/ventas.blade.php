<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }

        .titulo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitulo {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #343a40;
            color: white;
            padding: 6px 4px;
            border: 1px solid #555;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total {
            font-size: 11px;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <div class="titulo">
        REPORTE DE VENTAS
    </div>

    <div class="subtitulo">
        Generado: {{ now()->format('d/m/Y H:i') }}
    </div>


    <table>

        <thead>

            <tr>

                <th width="4%">N°</th>

                <th width="10%">Fecha</th>

                <th width="9%">Documento</th>

                <th>Cliente</th>

                <th width="11%">NIT/CI</th>

                <th width="12%">Sucursal</th>

                <th width="10%">Pago</th>

                <th width="9%">Estado</th>

                <th width="10%">Total</th>

            </tr>

        </thead>


        <tbody>

            @forelse($datos as $index => $dato)

            @php
                $cliente = trim( ($dato->nombres ?? '') . ' ' . ($dato->ap_paterno ?? '') . ' ' . ($dato->ap_materno ?? '') );
            @endphp

            <tr>

                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($dato->fecha)->format('d/m/Y H:i') }}
                </td>

                <td class="text-center">
                    {{ $dato->numero_factura ?? $dato->numero_recibo }}
                </td>

                <td>
                    {{ $cliente ?: $dato->razon_social }}
                </td>

                <td>
                    {{ $dato->nit }}
                </td>

                <td>
                    {{ $dato->sucursal }}
                </td>

                <td class="text-center">
                    {{ $dato->estado_pago }}
                </td>

                <td class="text-center">
                    {{ $dato->estado }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->total, 2) }}
                </td>

            </tr>

            @empty

            <tr>
                <td colspan="9" class="text-center">
                    No existen registros.
                </td>
            </tr>

            @endforelse

        </tbody>


        <tfoot>

            <tr>

                <td colspan="8" class="text-right total">

                    TOTAL Bs

                </td>

                <td class="text-right total">

                    {{ number_format($total, 2) }}

                </td>

            </tr>

        </tfoot>

    </table>

</body>

</html>
