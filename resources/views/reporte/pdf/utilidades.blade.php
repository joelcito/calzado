<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .fecha {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .resumen {
            margin-bottom: 15px;
        }

        .resumen td {
            font-size: 10px;
            padding: 6px;
        }

        .total {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h2>
        REPORTE DE UTILIDADES
    </h2>

    <div class="fecha">

        Desde:
        <strong>
            {{ $request->fecha_inicio ?? '' }}
        </strong>

        &nbsp;&nbsp;

        Hasta:
        <strong>
            {{ $request->fecha_fin ?? '' }}
        </strong>

    </div>


    {{-- ==========================================
    RESUMEN
    =========================================== --}}

    <table class="resumen">

        <tr>

            <td>
                <strong>Total vendido</strong>
            </td>

            <td class="text-right">
                Bs {{ number_format($totalVenta, 2) }}
            </td>


            <td>
                <strong>Costo total</strong>
            </td>

            <td class="text-right">
                Bs {{ number_format($totalCosto, 2) }}
            </td>


            <td>
                <strong>Utilidad total</strong>
            </td>

            <td class="text-right">
                Bs {{ number_format($totalUtilidad, 2) }}
            </td>

        </tr>


        <tr>

            <td>
                <strong>Utilidad productos</strong>
            </td>

            <td class="text-right">
                Bs {{ number_format($utilidadProductos, 2) }}
            </td>


            <td>
                <strong>Utilidad servicios</strong>
            </td>

            <td class="text-right">
                Bs {{ number_format($utilidadServicios, 2) }}
            </td>

            <td colspan="2"></td>

        </tr>

    </table>


    {{-- ==========================================
    DETALLE
    =========================================== --}}

    <table>

        <thead>

            <tr>

                <th>N°</th>

                <th>Fecha</th>

                <th>Documento</th>

                <th>Código</th>

                <th>Producto / Servicio</th>

                <th>Tipo</th>

                <th>Sucursal</th>

                <th>Cant.</th>

                <th>P. Compra</th>

                <th>P. Venta</th>

                <th>Descuento</th>

                <th>Venta Neta</th>

                <th>Costo</th>

                <th>Utilidad</th>

            </tr>

        </thead>


        <tbody>

            @foreach ($datos as $i => $dato)

            <tr>

                <td class="text-center">
                    {{ $i + 1 }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($dato->fecha)->format('d/m/Y H:i') }}
                </td>

                <td class="text-center">

                    {{ $dato->numero_factura
                    ?? $dato->numero_recibo }}

                </td>

                <td>
                    {{ $dato->codigo }}
                </td>

                <td>
                    {{ $dato->nombre_producto }}
                </td>

                <td class="text-center">
                    {{ $dato->tipo }}
                </td>

                <td>
                    {{ $dato->sucursal }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->cantidad, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->precio_compra ?? 0, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->precio, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->descuento ?? 0, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->venta_neta, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($dato->costo, 2) }}
                </td>

                <td class="text-right">
                    <strong>
                        {{ number_format($dato->utilidad, 2) }}
                    </strong>
                </td>

            </tr>

            @endforeach

        </tbody>


        <tfoot>

            <tr class="total">

                <td colspan="11" class="text-right">

                    TOTALES

                </td>

                <td class="text-right">
                    {{ number_format($totalVenta, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($totalCosto, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($totalUtilidad, 2) }}
                </td>

            </tr>

        </tfoot>

    </table>

</body>

</html>
