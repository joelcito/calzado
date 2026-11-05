<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización</title>
    <style>
        /* body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .header img {
            width: 90px;
        }
        .header-text {
            flex-grow: 1;
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
        } */

        @page {
            margin: 20px 25px;
        }

        body{
            font-family: sans-serif;
            font-size: 11px;
        }

        /* 🔒 evita que el contenido genere otra página */
        html, body {
            height: 100%;
        }

        /* 🔴 EVITA SALTOS DE PAGINA */
        .table,
        .table_firmas,
        .footer {
            page-break-inside: avoid !important;
        }

        /* 🔥 CONTROL DE COLUMNAS */
        .table {
            width: 100%;
            table-layout: fixed; /* CLAVE */
            border-collapse: collapse;
        }

        /* tamaños de columnas */
        .table th:nth-child(1),
        .table td:nth-child(1){ width:10%; }

        .table th:nth-child(2),
        .table td:nth-child(2){ width:12%; }

        .table th:nth-child(3),
        .table td:nth-child(3){ width:12%; }

        .table th:nth-child(4),
        .table td:nth-child(4){ width:10%; }

        .table th:nth-child(5),
        .table td:nth-child(5){ width:10%; }

        /* ⭐ COLUMNA DESCRIPCION */
        .table th:nth-child(6),
        .table td:nth-child(6){
            width:26%;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            line-height: 12px;
            max-height: 50px;      /* 🔥 limita altura */
            overflow: hidden;      /* 🔥 corta texto extra */
        }

        .table th:nth-child(7),
        .table td:nth-child(7){ width:10%; }

        .table th:nth-child(8),
        .table td:nth-child(8){ width:10%; }

        /* 🔴 FIRMAS FIJAS ABAJO */
        .table_firmas{
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .table th, .table td {
            border: 1px solid #444;
            padding: 2px;
            text-align: center;
        }

        .table_firmas{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: center;
        }

    </style>
</head>
<body>
    <div style="position: relative; width: 100%; margin-bottom: 1px; height: 90px;">
        <!-- Logo a la izquierda -->
        <div style="position: absolute; top: 0; left: 0;">
            <img src="" alt="Logo" width="90">
        </div>

        <!-- Texto completamente centrado -->
        <div style="text-align: center;">
            <h2 style="margin: 0; font-size: 13pt;">
                COMPROBANTE DE {{ $pago->estado }}
            </h2>
            <p style="margin: 1pt 0; font-size: 11pt;">Fecha: {{ $pago->fecha }}</p>
            {{-- <p style="margin: 1pt 0; font-size: 11pt;">Telefono: 77299506 - 22846050</p> --}}
        </div>
        <div style="text-align: end;">
            <p style="font-size: 10pt;">Vendedor: {{ $pago->usuario->name }}</p>
        </div>
    </div>
    {{-- <div class="section-title">Señor(es): </div> --}}
    <table class="table">
        <thead>
            <tr>
                <th>SUCURSAL</th>
                <th>CATEGORIA</th>
                <th>NRO. RECIBO</th>
                <th>FECHA</th>
                <th>DESCRIPCION</th>
                <th>TIPO PAGO</th>
                <th>MONTO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pago?->sucursal?->nombre }}</td>
                <td>{{ $pago->categoria?->nombre }}</td>
                <td>{{ $pago->factura?->numero_recibo }}</td>
                <td>{{ $pago->fecha }}</td>
                <td>{{ $pago->descripcion }}</td>
                <td>{{ $pago->tipo_pago }}</td>
                <td>{{ number_format($pago->monto, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total: </strong>{{ number_format($pago->monto, 2) }} </p>
    </div>
    <br>
    <table class="table_firmas">
        <tr>
            <td>-------------------------------</td>
            <td>-------------------------------</td>
        </tr>
        <tr>
            <td>ENTREGE CONFORME</td>
            <td>RECIBI CONFORME</td>
        </tr>
    </table>
</body>
</html>
