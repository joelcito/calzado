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

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            font-size: 16pt;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header p {
            font-size: 12pt;
            margin: 3px 0;
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

@endphp
<body>
    {{-- <div class="header">
        <h2>TOTAL {{ strtoupper($operativa) }}S DE PRODUCTOS POR SUCURSAL</h2>
        <p>Desde {{ Carbon::parse($fecha_ini)->format('d/m/Y') }} hasta {{ Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
        <p><strong>Producto:</strong> {{ $producto->nombre }}</p>
    </div> --}}
    <table style="width: 100%; margin-bottom: 14px; border-collapse: collapse; border-bottom: 1px solid #17354f;">
    <tr>
        <td style="width: 80px; padding: 0 12px 8px 0; border: 0; background: white; vertical-align: middle;">
            @include('partials.logoDocumento', ['soloImagen' => true, 'alto' => 70])
        </td>
        <td style="padding: 0 0 8px; border: 0; background: white; text-align: center; vertical-align: middle; color: #17354f;">

            <h2 style="margin: 0; font-size: 13pt;">
                TOTAL {{ strtoupper($operativa) }}S DE PRODUCTOS POR SUCURSAL
            </h2>
            <p style="margin: 2pt 0; font-size: 12pt;">Desde {{ Carbon::parse($fecha_ini)->format('d/m/Y') }} hasta {{ Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
            <p style="margin: 2pt 0; font-size: 12pt;"><strong>Producto:</strong> {{ $producto->nombre }}</p>

        </td>
    </tr>
</table>
    @foreach ($sucursales as $su)
        <div class="sucursal-title">{{ $su->nombre }}</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($su->movimientos as $mov)
                    <tr>
                        <td>{{ Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $mov->total }}</td>
                        <td>{{ $operativa }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="no-data">NO EXISTEN DATOS</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
