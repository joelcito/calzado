{{-- <style>
    @page {
        size: 7cm 15cm;
        margin: 0cm;
    }

    body {
        margin-left: 0.3cm;
        margin-right: 0cm;
        margin-top: 0cm;
        margin-bottom: 0cm;
        padding: 0px;
        font-family: sans-serif;
        font-size: 10px;
        box-sizing: border-box;
    }

    .container {
        margin: 5px;
    }

    .header {
        display: flex;
        align-items: center;
    }

    .header img {
        width: 70px;
    }

    .header-text {
        flex-grow: 1;
        text-align: center;
    }

    table {
        width: 100%;
        margin-bottom: 5px;
        border-collapse: collapse;
        box-sizing: border-box;
    }

    th, td {
        border: 1px solid #000;
        padding: 3px;
        text-align: left;
        font-size: 9px;
        overflow-wrap: break-word;
    }

    th {
        background-color: #f2f2f2;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    p {
        margin: 0;
        padding: 0;
    }
</style>


@php
    use Carbon\Carbon;

   /*  $path = public_path('img/logo_deportivo.png');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); */

    $cliente = $factura->cliente;
    $sucursal = $factura->sucursal;
    $detalles = $factura->detalles;

@endphp
<div class="container">
    <div style="position: relative; width: 100%; margin-bottom: 1px; height: 90px;">
        <!-- Logo a la izquierda -->

        <!-- Texto completamente centrado -->
        <div style="text-align: center;">
            <p style="font-size: 9pt;">
                <strong>{{ 'VENTA N°' }} {{ $factura->numero_recibo }}</strong>
            </p>
            <p style="margin: 1pt 0; font-size: 9pt;"><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>
            <p style="margin: 1pt 0; font-size: 9pt;"><strong>Fecha:</strong> {{ Carbon::parse($factura->fecha)->format('d/m/Y H:i:s') }}</p>
            <p style="margin: 1pt 0; font-size: 9pt;"><strong>Fecha Impresion:</strong> {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <p style="margin: 1pt 0; font-size: 9pt;"><strong>Vendedor:</strong> {{ $factura->vendedor->nombres }}</p>
    <p style="margin: 1pt 0; font-size: 9pt;"><strong>Cliente:</strong> {{ $cliente->nombres." ".($cliente->ap_paterno ?? '')." ".($cliente->ap_materno ?? '') }}</p>

    @php
        $totalSumaSubTotales = 0;
    @endphp
    <table class="table">
        <thead>
            <tr>
                <th>DETALLE</th>
                <th>PRECIO</th>
                <th>CANT.</th>
                <th>TOTAL</th>
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
                    <td>{{ number_format($d->total - $d->descuento, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="table">
        <tbody>
            <tr>
                <td colspan="2"><strong>SUBTOTAL</strong></td>
            </tr>
            @foreach ($factura->pagos as $pago)
                <tr>
                    <td>-    {{ $pago->tipo_pago }}</td>
                    <td>{{ number_format($pago->monto, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>DESCUENTO</strong></td>
                <td>{{ number_format($factura->descuento_adicional, 2) }}</td>
            </tr>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td>{{ number_format($factura->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div> --}}

<style>
    @page {
        size: 7cm 15cm;
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 3mm;
        font-family: DejaVu Sans, sans-serif;
        font-size: 8px;
        color: #000;
    }

    .container {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    /* ================================
       ENCABEZADO
    ================================= */

    .header {
        text-align: center;
        margin-bottom: 4px;
    }

    .header-title {
        font-size: 12px;
        font-weight: bold;
        margin: 0 0 3px 0;
    }

    .header p {
        margin: 1px 0;
        padding: 0;
        font-size: 8px;
        line-height: 1.2;
    }

    /* ================================
       SEPARADORES
    ================================= */

    .separator {
        border-top: 1px dashed #000;
        margin: 4px 0;
        height: 1px;
    }

    /* ================================
       INFORMACIÓN
    ================================= */

    .info {
        margin-bottom: 3px;
    }

    .info p {
        margin: 1px 0;
        padding: 0;
        font-size: 8px;
        line-height: 1.25;
    }

    /* ================================
       DETALLE DE PRODUCTOS
    ================================= */

    .detalle {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin: 0;
    }

    .detalle th {
        border: none;
        border-bottom: 1px solid #000;
        padding: 2px 1px;
        font-size: 7px;
        font-weight: bold;
    }

    .detalle td {
        border: none;
        padding: 2px 1px;
        font-size: 7px;
        vertical-align: top;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .detalle .col-detalle {
        width: 43%;
        text-align: left;
    }

    .detalle .col-precio {
        width: 20%;
        text-align: right;
    }

    .detalle .col-cantidad {
        width: 15%;
        text-align: center;
    }

    .detalle .col-total {
        width: 22%;
        text-align: right;
    }

    /* ================================
       TOTALES
    ================================= */

    .totales {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2px;
    }

    .totales td {
        border: none;
        padding: 1px 0;
        font-size: 8px;
    }

    .totales .label {
        text-align: right;
        padding-right: 5px;
        font-weight: bold;
        width: 65%;
    }

    .totales .valor {
        text-align: right;
        width: 35%;
        white-space: nowrap;
    }

    .total-final td {
        font-size: 10px;
        font-weight: bold;
        border-top: 1px solid #000;
        padding-top: 3px;
    }

    .saldo td {
        font-size: 9px;
        font-weight: bold;
    }

    /* ================================
       PIE
    ================================= */

    .footer {
        text-align: center;
        margin-top: 5px;
        font-size: 7px;
    }

    .footer p {
        margin: 1px 0;
    }
</style>

@php
    use Carbon\Carbon;

    $cliente  = $factura->cliente;
    $sucursal = $factura->sucursal;
    $detalles = $factura->detalles;

    $subtotal = 0;

    foreach ($detalles as $detalle) {
        $subtotal += ((float) $detalle->total - (float) $detalle->descuento);
    }

    $totalPagado = 0;

    foreach ($factura->pagos as $pago) {
        $totalPagado += (float) $pago->monto;
    }

    $saldoPendiente = (float) $factura->total - $totalPagado;

    if ($saldoPendiente < 0) {
        $saldoPendiente = 0;
    }
@endphp

<div class="container">

    {{-- ==========================================
        ENCABEZADO
    =========================================== --}}

    <div class="header">

        <p class="header-title">
            VENTA N° {{ $factura->numero_recibo }}
        </p>

        <p>
            <strong>{{ $sucursal->nombre }}</strong>
        </p>

        <p>
            Fecha:
            {{ Carbon::parse($factura->fecha)->format('d/m/Y H:i:s') }}
        </p>

        <p>
            Impresión:
            {{ date('d/m/Y H:i:s') }}
        </p>

    </div>

    <div class="separator"></div>


    {{-- ==========================================
        DATOS DE LA VENTA
    =========================================== --}}

    <div class="info">

        <p>
            <strong>Vendedor:</strong>
            {{ $factura->vendedor->nombres ?? '' }}
        </p>

        <p>
            <strong>Cliente:</strong>
            {{ $cliente->nombres ?? '' }}
            {{ $cliente->ap_paterno ?? '' }}
            {{ $cliente->ap_materno ?? '' }}
        </p>

        @if (!empty($factura->descripcion))
            <p>
                <strong>Descripción:</strong>
                {{ $factura->descripcion }}
            </p>
        @endif

    </div>

    <div class="separator"></div>


    {{-- ==========================================
        DETALLE
    =========================================== --}}

    <table class="detalle">

        <thead>
            <tr>
                <th class="col-detalle">DETALLE</th>
                <th class="col-precio">PRECIO</th>
                <th class="col-cantidad">CANT.</th>
                <th class="col-total">TOTAL</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($detalles as $d)

                <tr>

                    <td class="col-detalle">

                        @if ($d->producto && $d->producto->codigo)
                            {{ $d->producto->codigo }} -
                        @endif

                        {{ $d->nombre_producto }}

                        @if (!empty($d->descripcion_adicional))
                            <br>
                            <span style="font-size: 6px;">
                                {{ $d->descripcion_adicional }}
                            </span>
                        @endif

                    </td>

                    <td class="col-precio">
                        {{ number_format((float) $d->precio, 2) }}
                    </td>

                    <td class="col-cantidad">
                        {{ number_format((float) $d->cantidad, 2) }}
                    </td>

                    <td class="col-total">
                        {{ number_format(
                            (float) $d->total - (float) $d->descuento,
                            2
                        ) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    <div class="separator"></div>


    {{-- ==========================================
        TOTALES
    =========================================== --}}

    <table class="totales">

        <tbody>

            <tr>
                <td class="label">
                    SUBTOTAL:
                </td>

                <td class="valor">
                    Bs {{ number_format($subtotal, 2) }}
                </td>
            </tr>


            @if ((float) $factura->descuento_adicional > 0)

                <tr>
                    <td class="label">
                        DESCUENTO:
                    </td>

                    <td class="valor">
                        Bs {{ number_format(
                            (float) $factura->descuento_adicional,
                            2
                        ) }}
                    </td>
                </tr>

            @endif


            <tr class="total-final">

                <td class="label">
                    TOTAL:
                </td>

                <td class="valor">
                    Bs {{ number_format(
                        (float) $factura->total,
                        2
                    ) }}
                </td>

            </tr>

        </tbody>

    </table>


    {{-- ==========================================
        PAGOS
    =========================================== --}}

    @if ($factura->pagos->count() > 0)

        <div class="separator"></div>

        <div style="
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
        ">
            PAGOS REALIZADOS
        </div>

        <table class="totales">

            <tbody>

                @foreach ($factura->pagos as $pago)

                    <tr>

                        <td class="label">
                            {{ $pago->tipo_pago }}:
                        </td>

                        <td class="valor">
                            Bs {{ number_format(
                                (float) $pago->monto,
                                2
                            ) }}
                        </td>

                    </tr>

                @endforeach


                <tr>

                    <td class="label">
                        TOTAL PAGADO:
                    </td>

                    <td class="valor">
                        Bs {{ number_format(
                            $totalPagado,
                            2
                        ) }}
                    </td>

                </tr>

            </tbody>

        </table>

    @endif


    {{-- ==========================================
        SALDO
    =========================================== --}}

    @if ($saldoPendiente > 0)

        <div class="separator"></div>

        <table class="totales">

            <tbody>

                <tr class="saldo">

                    <td class="label">
                        SALDO PENDIENTE:
                    </td>

                    <td class="valor">
                        Bs {{ number_format(
                            $saldoPendiente,
                            2
                        ) }}
                    </td>

                </tr>

            </tbody>

        </table>

    @endif


    <div class="separator"></div>


    {{-- ==========================================
        ESTADO
    =========================================== --}}

    <div class="footer">

        @if ($saldoPendiente <= 0)

            <p style="
                font-size: 9px;
                font-weight: bold;
            ">
                VENTA PAGADA
            </p>

        @else

            <p style="
                font-size: 9px;
                font-weight: bold;
            ">
                VENTA CON SALDO PENDIENTE
            </p>

        @endif

        <p>
            Gracias por su preferencia
        </p>

    </div>

</div>
