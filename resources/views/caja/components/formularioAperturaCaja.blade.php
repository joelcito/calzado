<div class="modal-content">
    <div class="modal-header bg-light-info" id="kt_modal_add_user_header">
        <h3 class="fw-bold">FORMULARIO DE APERTURA DE CAJA</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body scroll-y">
        <form id="formularioAperturaCaja">
            <input type="hidden" name="id" id="id">
            <div class="row">
                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">usuario</label>
                        <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" value="{{ $usuario->name }}" readonly>
                        <input type="hidden" id="usuario_id" name="usuario_id" value="{{ $usuario->id }}" required>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Monto Apertura</label>
                        <input type="number" class="form-control form-control-sm" id="monto_apertura" name="monto_apertura" min="0" required >
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Descripcion Apertura</label>
                        <input type="text" class="form-control form-control-sm" id="descripcion" name="descripcion" required>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm w-100 btn-success" id="boton_abrir_caja" onclick="guardarAperturaCaja()">Guardar</button>
            </div>
        </div>
    </div>
    <!--end::Modal body-->
</div>
