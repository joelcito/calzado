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
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .datos-producto {
            margin-bottom: 15px;
        }

        .datos-producto td {
            padding: 2px 10px 2px 0;
        }

        table.kardex {
            width: 100%;
            border-collapse: collapse;
        }

        table.kardex th {
            background: #343a40;
            color: white;
            padding: 6px;
            border: 1px solid #555;
        }

        table.kardex td {
            padding: 5px;
            border: 1px solid #ccc;
        }

        .numero {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .entrada {
            font-weight: bold;
        }

        .salida {
            font-weight: bold;
        }

        .saldo {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h2>
        KARDEX DE PRODUCTO
    </h2>


    <table class="datos-producto">

        <tr>

            <td>
                <strong>Producto:</strong>
                {{ $producto->nombre }}
            </td>

            <td>
                <strong>Código:</strong>
                {{ $producto->codigo }}
            </td>

            <td>
                <strong>Desde:</strong>
                {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }}
            </td>

            <td>
                <strong>Hasta:</strong>
                {{ \Carbon\Carbon::parse($request->fecha_fin)->format('d/m/Y') }}
            </td>

        </tr>

    </table>


    <table class="kardex">

        <thead>

            <tr>

                <th>N°</th>

                <th>Fecha</th>

                <th>Movimiento</th>

                <th>Documento</th>

                <th>Sucursal</th>

                <th>Descripción</th>

                <th>Entrada</th>

                <th>Salida</th>

                <th>Saldo</th>

            </tr>

        </thead>


        <tbody>

            @php
            $saldo = $saldoAnterior;
            @endphp


            {{-- SALDO ANTERIOR --}}

            <tr>

                <td></td>

                <td>
                    {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }}
                </td>

                <td colspan="5">
                    <strong>SALDO ANTERIOR</strong>
                </td>

                <td></td>

                <td class="numero saldo">
                    {{ number_format($saldoAnterior, 2) }}
                </td>

            </tr>


            @foreach($datos as $i => $dato)

            @php

            $entrada = (float) ($dato->ingreso ?? 0);
            $salida = (float) ($dato->salida ?? 0);

            $saldo += $entrada - $salida;


            /*
            |--------------------------------------------------------------------------
            | IDENTIFICAR MOVIMIENTO
            |--------------------------------------------------------------------------
            */

            if ($entrada > 0) {

            $tipoMovimiento = 'INGRESO';

            } elseif ($salida > 0 && $dato->factura_id) {

            $tipoMovimiento = 'VENTA';

            } elseif ($salida > 0) {

            $tipoMovimiento = 'SALIDA / BAJA';

            } else {

            $tipoMovimiento = 'MOVIMIENTO';
            }


            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO
            |--------------------------------------------------------------------------
            */

            $documento = '';

            if ($dato->numero_factura) {

            $documento =
            'Factura N° ' .
            $dato->numero_factura;

            } elseif ($dato->numero_recibo) {

            $documento =
            'Recibo N° ' .
            $dato->numero_recibo;
            }

            @endphp


            <tr>

                <td class="centro">
                    {{ $i + 1 }}
                </td>


                <td class="centro">

                    {{ \Carbon\Carbon::parse($dato->fecha)
                    ->format('d/m/Y H:i') }}

                </td>


                <td class="centro">

                    <strong>
                        {{ $tipoMovimiento }}
                    </strong>

                </td>


                <td>
                    {{ $documento }}
                </td>


                <td>
                    {{ $dato->sucursal }}
                </td>


                <td>
                    {{ $dato->descripcion }}
                </td>


                <td class="numero entrada">

                    @if($entrada > 0)

                    {{ number_format($entrada, 2) }}

                    @endif

                </td>


                <td class="numero salida">

                    @if($salida > 0)

                    {{ number_format($salida, 2) }}

                    @endif

                </td>


                <td class="numero saldo">

                    {{ number_format($saldo, 2) }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>
