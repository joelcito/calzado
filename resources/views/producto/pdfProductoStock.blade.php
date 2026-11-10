<!DOCTYPE html>
<html lang="es-BO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REPORTE</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .sucursal-title {
            font-size: 13pt;
            margin-top: 3px;
            margin-bottom: 2px;
            color: #34495e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        thead {
            background-color: #27ae60;
            color: white;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 2px;
            text-align: center;
            font-size: 10pt;
        }

        th {
            background-color: #2ecc71;
            color: white;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            color: red;
            font-weight: bold;
            font-size: 12pt;
            padding: 10px;
        }
    </style>
</head>

@php
    use Carbon\Carbon;

    /* $path = public_path('assets/img/logo.jpg');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); */
@endphp

<body>
    <table style="width: 100%; margin-bottom: 14px; border-collapse: collapse; border-bottom: 1px solid #17354f;">
    <tr>
        <td style="width: 80px; padding: 0 12px 8px 0; border: 0; background: white; vertical-align: middle;">
            @include('partials.logoDocumento', ['soloImagen' => true, 'alto' => 70])
        </td>
        <td style="padding: 0 0 8px; border: 0; background: white; text-align: center; vertical-align: middle; color: #17354f;">

            <h2 style="margin: 0; font-size: 11pt;">
                STOCK - PRODUCTOS POR SUCURSAL
            </h2>
            <p style="margin: 2pt 0; font-size: 10pt;">Fecha de Impresion {{ date('d/m/Y H:i:s') }}</p>
            <p style="margin: 2pt 0; font-size: 10pt;"><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>

        </td>
    </tr>
</table>

    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Stock Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $prod)
                <tr>
                    <td>{{ $prod->codigo }}</td>
                    <td>{{ $prod->nombre }}</td>
                    <td>{{ number_format((float)$prod->movimientos_sum_ingreso - (float)$prod->movimientos_sum_salida, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
