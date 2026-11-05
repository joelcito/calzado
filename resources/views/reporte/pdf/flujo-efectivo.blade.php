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

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .fecha-generacion {
            text-align: center;
            color: #666;
            margin-bottom: 15px;
        }

        .resumen {
            width: 100%;
            margin-bottom: 15px;
        }

        .resumen td {
            padding: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
        }

        table.detalle th {
            background: #343a40;
            color: white;
            padding: 6px;
            border: 1px solid #555;
        }

        table.detalle td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .numero {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .ingreso {
            color: #198754;
            font-weight: bold;
        }

        .salida {
            color: #dc3545;
            font-weight: bold;
        }

        .saldo {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h2>
        REPORTE DE FLUJO DE EFECTIVO
    </h2>

    <div class="fecha-generacion">
        Generado:
        {{ now()->format('d/m/Y H:i') }}
    </div>


    {{-- RESUMEN --}}

    <table class="resumen">

        <tr>

            <td>
                TOTAL INGRESOS<br>

                <span class="ingreso">
                    Bs {{ number_format($totalIngresos, 2) }}
                </span>
            </td>

            <td>
                TOTAL SALIDAS<br>

                <span class="salida">
                    Bs {{ number_format($totalSalidas, 2) }}
                </span>
            </td>

            <td>
                SALDO<br>

                <span class="saldo">
                    Bs {{ number_format($saldo, 2) }}
                </span>
            </td>

        </tr>

    </table>


    {{-- DETALLE --}}

    <table class="detalle">

        <thead>

            <tr>

                <th width="4%">
                    N°
                </th>

                <th width="12%">
                    Fecha
                </th>

                <th width="10%">
                    Tipo
                </th>

                <th width="18%">
                    Categoría
                </th>

                <th>
                    Descripción
                </th>

                <th width="12%">
                    Forma Pago
                </th>

                <th width="13%">
                    Sucursal
                </th>

                <th width="10%">
                    Monto
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($datos as $i => $dato)

            <tr>

                <td class="centro">
                    {{ $i + 1 }}
                </td>

                <td class="centro">

                    {{ \Carbon\Carbon::parse($dato->fecha)
                    ->format('d/m/Y H:i') }}

                </td>

                <td class="centro">

                    @if($dato->tipo_movimiento == 'INGRESO')

                    <span class="ingreso">
                        INGRESO
                    </span>

                    @elseif($dato->tipo_movimiento == 'SALIDA')

                    <span class="salida">
                        SALIDA
                    </span>

                    @else

                    {{ $dato->tipo_movimiento }}

                    @endif

                </td>

                <td>
                    {{ $dato->categoria }}
                </td>

                <td>
                    {{ $dato->descripcion }}
                </td>

                <td class="centro">
                    {{ $dato->tipo_pago }}
                </td>

                <td>
                    {{ $dato->sucursal }}
                </td>

                <td class="numero">

                    {{ number_format(
                    $dato->monto,
                    2
                    ) }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="8" class="centro">

                    No existen movimientos
                    en el periodo seleccionado.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
