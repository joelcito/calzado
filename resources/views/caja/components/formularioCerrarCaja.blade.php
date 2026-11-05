<div class="modal-content">
    <div class="modal-header bg-light-info" id="kt_modal_add_user_header">
        <h3 class="fw-bold">FORMULARIO DE CIERRE DE CAJA</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body scroll-y">
        <form id="formularioCerrarCaja">
            <input type="hidden" name="id" id="id">
            <div class="row">
                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">usuario</label>
                        <input type="text" class="form-control form-control-sm" id="nombre_cierre" name="nombre_cierre" value="{{ $usuario->name }}" readonly>
                        <input type="hidden" id="usuario_id_cierre" name="usuario_id_cierre" value="{{ $usuario->id }}">
                        <input type="hidden" id="caja_id_cierre" name="caja_id_cierre" value="{{ $cajaAbierta != null ?$cajaAbierta->id : 0  }}">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Monto Efectivo</label>
                        <input type="number" class="form-control form-control-sm" id="monto_cierre" name="monto_cierre" min="0" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Descripcion Cerrar</label>
                        <input type="text" class="form-control form-control-sm" id="descripcion_cierre" name="descripcion_cierre" required>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm w-100 btn-success" id="boton_cerrar_caja" onclick="guardarCerrarCaja()">Guardar</button>
            </div>
        </div>
    </div>
    <!--end::Modal body-->
</div>
