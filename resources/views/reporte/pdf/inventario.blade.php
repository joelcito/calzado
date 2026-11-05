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
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #ccc;
        }

        .numero {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .bajo {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h2>REPORTE DE INVENTARIO</h2>
    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Código</th>
                <th>Producto</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Stock mínimo</th>
                <th>Stock actual</th>
                <th>Estado</th>
                <th>Total P. Compra</th>
                <th>Total P. Venta</th>
            </tr>

        </thead>

        <tbody>
            @php
                $TPrecioCompra = 0;
                $TPrecioVenta = 0;
            @endphp

            @foreach($datos as $i => $dato)

            @php
                $precioCompra = $dato->precio_compra * $dato->stock;
                $precioVenta  = $dato->precio_venta * $dato->stock;

                $TPrecioCompra+=$precioCompra;
                $TPrecioVenta+=$precioVenta;
            @endphp

            <tr>
                <td class="centro"> {{ $i + 1 }} </td>
                <td> {{ $dato->codigo }} </td>
                <td> {{ $dato->nombre }} </td>
                <td class="numero"> {{ number_format($dato->precio_compra,2) }} </td>
                <td class="numero"> {{ number_format($dato->precio_venta,2) }} </td>
                <td class="numero"> {{ number_format($dato->minimo_stock,2) }} </td>
                <td class="numero"> {{ number_format($dato->stock,2) }} </td>
                <td class="centro">
                    @if($dato->stock <= $dato->minimo_stock)
                        <span class="bajo"> STOCK BAJO </span>
                    @else
                        DISPONIBLE
                    @endif
                </td>

                <td class="numero">{{ number_format(($precioCompra) , 2) }}</td>
                <td class="numero">{{ number_format(($precioVenta) , 2) }}</td>

            </tr>

            @endforeach

            <tr>
                <td colspan="8"><b>TOTAL</b></td>
                <td class="numero">{{ number_format($TPrecioCompra,2) }}</td>
                <td class="numero">{{ number_format($TPrecioVenta,2) }}</td>
            </tr>
        </tbody>

    </table>

</body>

</html>
