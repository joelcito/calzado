<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #343a40;
            color: white;
            padding: 6px;
        }

        td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        th {
            border: 1px solid #555;
        }

        .numero {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .total {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h2>CUENTAS POR COBRAR</h2>

    <table>

        <thead>

            <tr>

                <th>N°</th>
                <th>Fecha</th>
                <th>Documento</th>
                <th>Cliente</th>
                <th>Celular</th>
                <th>Sucursal</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Saldo</th>

            </tr>

        </thead>

        <tbody>

            @foreach($datos as $i => $dato)

            @php

            $cliente = trim(
            ($dato->nombres ?? '') . ' ' .
            ($dato->ap_paterno ?? '') . ' ' .
            ($dato->ap_materno ?? '')
            );

            @endphp

            <tr>

                <td class="centro">
                    {{ $i + 1 }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($dato->fecha)->format('d/m/Y') }}
                </td>

                <td>
                    {{ $dato->numero_factura ?? $dato->numero_recibo }}
                </td>

                <td>
                    {{ $cliente }}
                </td>

                <td>
                    {{ $dato->numero_celular }}
                </td>

                <td>
                    {{ $dato->sucursal }}
                </td>

                <td class="numero">
                    {{ number_format($dato->total,2) }}
                </td>

                <td class="numero">
                    {{ number_format($dato->pagado,2) }}
                </td>

                <td class="numero">
                    {{ number_format($dato->saldo,2) }}
                </td>

            </tr>

            @endforeach

        </tbody>

        <tfoot>

            <tr class="total">

                <td colspan="6" class="numero">
                    TOTALES
                </td>

                <td class="numero">
                    {{ number_format($total,2) }}
                </td>

                <td class="numero">
                    {{ number_format($pagado,2) }}
                </td>

                <td class="numero">
                    {{ number_format($saldo,2) }}
                </td>

            </tr>

        </tfoot>

    </table>

</body>

</html>
