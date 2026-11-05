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
            padding: 5px;
            border: 1px solid #ccc;
        }
    </style>

</head>

<body>

    <h2>REPORTE DE AGENDA</h2>

    <table>

        <thead>

            <tr>

                <th>N°</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Título</th>
                <th>Cliente</th>
                <th>Celular</th>
                <th>Responsable</th>
                <th>Sucursal</th>
                <th>Estado</th>

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

                <td>{{ $i + 1 }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($dato->fecha_inicio)->format('d/m/Y H:i') }}
                </td>

                <td>

                    @if($dato->fecha_fin)

                    {{ \Carbon\Carbon::parse($dato->fecha_fin)->format('d/m/Y H:i') }}

                    @endif

                </td>

                <td>
                    {{ $dato->titulo }}
                </td>

                <td>
                    {{ $cliente }}
                </td>

                <td>
                    {{ $dato->numero_celular }}
                </td>

                <td>
                    {{ $dato->responsable }}
                </td>

                <td>
                    {{ $dato->sucursal }}
                </td>

                <td>
                    {{ $dato->estado }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>
