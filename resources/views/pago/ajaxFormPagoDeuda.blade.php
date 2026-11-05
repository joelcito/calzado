<form id="formularioDeuda">
    <input type="hidden" name="factura_id" id="factura_id" value="{{ $factura->id }}">
    <div class="row">
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 ">Sucursal</label>
                <input type="text" class="form-control form-control-sm" id="sucursal" name="sucursal" value="{{ optional($factura->sucursal)->nombre }}" @readonly(true) disabled>
                <div class="text-danger error-message" id="error-sucursal"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 ">Cliente</label>
                <input type="text" class="form-control form-control-sm" id="cliente" name="cliente" value="{{ optional($factura->cliente)->nombres.' '.optional($factura->cliente)->ap_paterno.' '. optional($factura->cliente)->ap_materno }}" @readonly(true) disabled>
                <div class="text-danger error-message" id="error-cliente"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">N° Fac/Rec</label>
                <input type="number" class="form-control form-control-sm" id="numero" name="numero" value="{{ $factura->facturado == 'Si' ? $factura->numero_factura : $factura->numero_recibo }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-numero"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Total</label>
                <input type="number" class="form-control form-control-sm" id="total" name="total" value="{{ $factura->total }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-total"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Pagado</label>
                <input type="number" class="form-control form-control-sm" id="pagado" name="pagado" value="{{ $pagado }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-pagado"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Saldo</label>
                <input type="number" class="form-control form-control-sm" id="saldo" name="saldo" value="{{ $factura->total - $pagado }}" @readonly(true)>
                <div class="text-info" id="mensaje-saldo"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2 required">Tipo Pago</label>
                <select name="tipo_pago" id="tipo_pago" class="form-control form-control-sm">
                    <option value="">Seleccione</option>
                    <option value="EFECTIVO">EFECTIVO</option>
                    <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                    <option value="QR">QR</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2 required">Importe a Pagar</label>
                <input type="number" class="form-control form-control-sm" id="importe_pago" name="importe_pago" min="1" max="{{ $factura->total - $pagado }}">
                <div class="text-info" id="mensaje-importe_pago"></div>
            </div>
        </div>
    </div>
</form>
<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Fecha</th>
                <th>Tipo Pago</th>
                <th>Monto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($pagos as $pago)
                <tr>
                    <td>{{ $pago->fecha }}</td>
                    <td>{{ $pago->tipo_pago }}</td>
                    <td>{{ $pago->monto }}</td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-danger" onclick="eliminarPago({{ $pago->id }})"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
    <!--end::Table-->
</div>
