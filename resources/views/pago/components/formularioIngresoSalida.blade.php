<div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header">
            <h2 class="fw-bold">Formulario de <span id="text_tipoo_modal" class="text-info"></span></h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <p class="text-muted">Se registra en la caja abierta de su sucursal. Un ingreso también permite devolver dinero al hermano.</p>
            <form id="formularioIngresoSalida">
                @csrf
                <input type="hidden" id="tipo" name="tipo">
                <div class="mb-5">
                    <label class="required form-label">Hermano / dueño del dinero</label>
                    <select class="form-select form-select-sm" name="usuario_duenio_id" id="movimiento_duenio_id" required>
                        <option value="">Seleccione</option>
                        @foreach ($usuarios as $hermano)
                            <option value="{{ $hermano->id }}">{{ $hermano->name ?: trim($hermano->nombres.' '.$hermano->ap_paterno.' '.$hermano->ap_materno) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-5"><label class="required form-label">Categoría</label><select class="form-select form-select-sm" name="categoria_id" id="categoria_id" required></select></div>
                    <div class="col-md-6 mb-5">
                        <label class="required form-label">Tipo de pago</label>
                        <select class="form-select form-select-sm" name="tipo_pago" id="tipo_pago" required>
                            <option value="">Seleccione</option>
                            @foreach (['EFECTIVO', 'QR', 'TRANSFERENCIA'] as $medio)
                                <option value="{{ $medio }}">{{ $medio }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-5"><label class="required form-label">Monto (Bs)</label><input type="number" id="monto" name="monto" class="form-control form-control-sm" min="0.01" max="9999999999.99" step="0.01" required></div>
                <div class="mb-5"><label class="required form-label">Descripción</label><input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm" maxlength="1000" required placeholder="Ej.: Taxi o devolución de dinero de taxi"></div>
            </form>
            <button type="button" class="btn btn-success w-100" id="boton-agrega-sale-dinero" onclick="guardarTipoIngresoSalida()">Guardar</button>
        </div>
    </div></div>
</div>
