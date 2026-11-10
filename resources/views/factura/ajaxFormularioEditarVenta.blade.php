<form id="formulario_editar_venta">
    @csrf
    <input type="hidden" name="factura_id" value="{{ $factura->id }}">
    <input type="hidden" name="version" value="{{ $version }}">
    <input type="hidden" id="edicion_pagado" value="{{ $pagado }}">
    <input type="hidden" id="edicion_descuento" value="{{ $factura->descuento_adicional ?? 0 }}">
    <h4>Venta / recibo N.º {{ $factura->numero_recibo }}</h4>
    <p>Cliente: {{ trim($factura->cliente?->nombres.' '.$factura->cliente?->ap_paterno.' '.$factura->cliente?->ap_materno) }} · Fecha: {{ $factura->fecha }}</p>
    <div class="row g-3 mb-5">
        <div class="col-md-9">
            <label class="form-label">Agregar producto</label>
            <select id="producto_edicion" class="form-select form-select-sm">
                <option value="">Seleccione un producto</option>
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}" data-nombre="{{ $producto->nombre }}" data-duenio="{{ $producto->duenio?->name }}">{{ $producto->codigo }} — {{ $producto->nombre }} — {{ $producto->duenio?->name }} — {{ number_format($producto->precio_venta, 2) }} Bs</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end"><button type="button" class="btn btn-sm btn-success w-100" onclick="agregarItemEdicionVenta()"><i class="fa fa-plus"></i>Agregar ítem</button></div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-7">
            <thead><tr><th>Producto</th><th>Hermano</th><th>Precio (Bs)</th><th>Cantidad</th><th>Descuento (Bs)</th><th>Importe (Bs)</th><th>Quitar</th></tr></thead>
            <tbody id="items_edicion_venta">
                @foreach ($factura->detalles as $detalle)
                    <tr data-detalle="{{ $detalle->id }}" data-producto="{{ $detalle->producto_id }}" data-precio="{{ (int) round($detalle->precio * 100) }}" data-descuento="{{ (int) round($detalle->descuento * 100) }}">
                        <td>{{ $detalle->nombre_producto }}<small class="d-block text-muted">{{ $detalle->descripcion_adicional }}</small></td>
                        <td>{{ $detalle->duenio?->name ?? 'Sin asignar' }}</td><td>{{ number_format($detalle->precio, 2) }}</td>
                        <td><input type="number" class="cantidad_edicion form-control form-control-sm" style="min-width:90px" value="{{ $detalle->cantidad }}" min="0.01" max="9999999999.99" step="0.01" required></td>
                        <td>{{ number_format($detalle->descuento, 2) }}</td><td class="importe_edicion">{{ number_format($detalle->importe, 2) }}</td>
                        <td><button type="button" class="quitar_edicion btn btn-sm btn-icon btn-danger" title="Quitar producto"><i class="fa fa-trash"></i></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row text-end my-5">
        <div class="col-md-3">Descuento general: <strong>{{ number_format($factura->descuento_adicional ?? 0, 2) }} Bs</strong></div>
        <div class="col-md-3">Nuevo total: <strong id="edicion_total"></strong> Bs</div>
        <div class="col-md-3">Ya cobrado: <strong>{{ number_format($pagado, 2) }} Bs</strong></div>
        <div class="col-md-3">Pendiente: <strong id="edicion_saldo"></strong> Bs</div>
    </div>
    <div id="edicion_aviso" class="alert alert-danger d-none"></div>
    <p class="text-muted">Se conserva el número de venta y lo ya cobrado. El total, stock y reparto por hermano se actualizan al guardar. Los importes añadidos quedan pendientes de cobro.</p>
    <div class="text-end"><button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button> <button type="submit" id="boton_guardar_edicion" class="btn btn-primary btn-sm">Guardar cambios</button></div>
</form>
