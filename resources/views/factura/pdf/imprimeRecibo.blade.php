<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
    <style>
        body {
            margin-top: 100px;
            /* deja espacio para la cabecera */
            font-family: sans-serif;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #444;
            padding: 2px;
            text-align: center;
        }

        .table th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 5px;
            text-align: right;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0 1px 0;
            color: #34495e;
        }

        .contenedor-tablas {
            display: flex;
            align-items: flex-start;
            /* Alinea arriba */
            gap: 10px;
            /* Espacio entre tablas */
        }

        .fondp-verde {
            background-color: rgb(77, 246, 77);
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 1px solid #444;
        }

        /* LOGO */
        .header img.logo {
            position: absolute;
            top: 5px;
            left: 5px;
            height: 80px;
        }

        /* CONTENIDO IZQUIERDO */
        .header-left {
            position: absolute;
            top: 5px;
            left: 100px; /* deja espacio para el logo */
            text-align: left;
        }

        .header-left h2 {
            margin: 0;
            font-size: 13pt;
        }

        .header-left p {
            margin: 1pt 0;
            font-size: 11pt;
        }

        /* TEXTO DERECHO */
        .header-right {
            position: absolute;
            top: 5px;
            right: 10px;
            /* font-size: 11pt; */
            /* font-weight: bold; */
            text-align: right;
        }

        .table_cliente{
            width: 100%;
        }

    </style>
</head>

<body>

    <div class="header">
        {{-- <img class="logo" src="{{ $base64 }}" alt="Logo"> --}}
        <div class="header-left">
            <h2>{{ 'VENTA N°' }} {{ $factura->numero_recibo }}</h2>
            <p>Fecha de venta: {{ $factura->fecha }}</p>
            <p>Fecha de impresión: {{ date('d/m/Y H:i:s') }}</p>
        </div>

        <div class="header-right">
            <p>{{ $factura->sucursal->direccion }}</p>
        </div>
    </div>
    <table class="table_cliente">
        <tbody>
            <tr>
                <td style="background-color: #d8d8d8"><b>Vendedor</b></td>
                <td>{{ $factura->vendedor->nombres }}</td>
                <td style="background-color: #d8d8d8"><b>Cliente</b></td>
                @if (isset($factura->cliente->nombres))
                    <td>{{ $factura->cliente->nombres." ".($factura->cliente->ap_paterno ?? '')." ".($factura->cliente->ap_materno ?? '') }}</td>
                @else
                    <td>SIN NOMBRE</td>
                @endif
            </tr>
        </tbody>
    </table>
    @if ($factura->mascota)
        <table class="table_cliente">
            <tbody>
                <tr>
                    <td style="background-color: #d8d8d8"><b>Mascota</b></td>
                    <td>{{ $factura->mascota->nombre }}</td>
                    <td style="background-color: #d8d8d8"><b>Raza</b></td>
                    <td>{{ $factura->mascota->raza->nombre ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    @endif
    @php
        $detalles = $factura->detalles;
        $totalSumaSubTotales = 0;
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th>DETALLE</th>
                <th>PRECIO</th>
                <th>CANT.</th>
                <th>DESC.</th>
                <th>TOTAL</th>
                <th>SUB TOT.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $d)
                @php
                    $totalSumaSubTotales = $totalSumaSubTotales + ((float)$d->total - (float)$d->descuento);
                @endphp
                <tr>
                    <td>{{ $d->producto->codigo ? $d->producto->codigo . ' - ' : ''}}{{ $d->nombre_producto }}</td>
                    <td>{{ number_format($d->precio, 2) }}</td>
                    <td>{{ number_format($d->cantidad, 2) }}</td>
                    <td>{{ number_format($d->descuento, 2) }}</td>
                    <td>{{ number_format($d->total, 2) }}</td>
                    <td>{{ number_format($d->total - $d->descuento, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right"><strong>TOTAL</strong></td>
                <td colspan="1">{{ number_format($totalSumaSubTotales, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right"><strong>DESCUENTO ADICIONAL</strong></td>
                <td colspan="1">{{ number_format($factura->descuento_adicional, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right"><strong>TOTAL VENTA</strong></td>
                <td colspan="1">{{ number_format($factura->total, 2) }}</td>
            </tr>
            {{-- <tr>
                <td colspan="3"><strong>A CUENTA</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3"><strong>SALDO</strong></td>
                <td colspan="2"></td>
            </tr> --}}
        </tfoot>
    </table>

    <table class="table_cliente">
        <tbody>
            <tr>
                <td><b>Descripcion</b></td>
                <td>{{ $factura->descripcion }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
