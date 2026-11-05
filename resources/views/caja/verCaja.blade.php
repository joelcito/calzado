<div class="modal-content">
    <div class="modal-header bg-light-info" id="kt_modal_add_user_header">
        <h3 class="fw-bold">DETALLE DE CAJA</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body scroll-y">
        <table class="table table-bordered align-middle fs-6 gy-5 bg-light-success">
            <tr>
                <td rowspan="5"><b>TOTAL:</b> {{ number_format(($totalIngresosVenta + $totalAperturaCaja + $totalOtrosIngresos),2) }}</td>
                <td><b>APERTURA CAJA:</b> {{ number_format($totalAperturaCaja,2) }}</td>
                <td><b>EFECTIVO:</b> {{ number_format($totalAperturaCaja,2) }}</td>
            </tr>
            <tr>
                <td rowspan="3"><b>VENTA:</b> {{ number_format($totalIngresosVenta,2) }}</td>
                <td><b>EFECTIVO:</b> {{ number_format($totalIngresosVentaEfectivo,2) }}</td>
            </tr>
            <tr>
                <td><b>TRANSFERENCIA:</b> {{ number_format($totalIngresosVentaTramsferencia,2) }}</td>
            </tr>
            <tr>
                <td><b>QR:</b> {{ number_format($totalIngresosVentaEQr,2) }}</td>
            </tr>
            <tr>
                <td><b>OTROS:</b> {{ number_format($totalOtrosIngresos,2) }}</td>
                <td><b>EFECTIVO:</b> {{ number_format($totalOtrosIngresos,2) }}</td>
            </tr>
        </table>
        <table class="table table-bordered align-middle fs-6 gy-5 bg-light-danger">
            <tr>
                <td><b>TOTAL:</b> {{ number_format(($totalSalidas),2) }}</td>
                <td><b>SALIDA:</b> {{ number_format($totalSalidas,2) }}</td>
                <td><b>EFECTIVO:</b> {{ number_format($totalSalidas,2) }}</td>
            </tr>
        </table>

        <table class="table table-bordered align-middle fs-6 gy-5 bg-light-warning">
            <tr>
                <td><b>TOTAL CAJA:</b> {{ number_format((($totalIngresosVenta + $totalAperturaCaja + $totalOtrosIngresos) - $totalSalidas),2) }}</td>
            </tr>
        </table>
    </div>
    <!--end::Modal body-->
</div>
