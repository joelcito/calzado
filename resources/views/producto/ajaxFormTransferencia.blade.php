<form id="formularioTransferenciaSucursal">
    <input type="hidden" name="producto_transferencia_id" id="producto_transferencia_id" value="{{ $producto_id }}">
    <div class="row">
        <div class="col-md-6">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 required">De Sucursal</label>
                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modalTransferenciaSucursal"
                    class="form-select form-select-solid fw-bold" name="sucursal1_id" id="sucursal1_id">
                    <option></option>
                    @foreach ($sucursalesSalida as $su)
                    @php

                        $cantidadAlmacen = optional($su->movimientos[0])->cantidaDisponile($su->id, $producto_id);
                        $cantidadDisponible = $cantidadAlmacen;

                        // $cantidadDisponible = optional($su->movimientos[0])->cantidaDisponile($su->id, $producto_id);
                    @endphp
                        {{-- <option value="{{ $su->id }}">{{ $su->nombre.' (Cantidad Maxima: '.optional($su->movimientos[0])->cantidaDisponile($su->id, $producto_id).')' }}</option> --}}
                        <option value="{{ $su->id }}">{{ $su->nombre.' (Cantidad Maxima: '.$cantidadDisponible.')' }}</option>
                    @endforeach
                </select>
                <div class="text-danger error-message" id="error-sucursal1_id"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 required">A Sucursal</label>
                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modalTransferenciaSucursal"
                    class="form-select form-select-solid fw-bold" name="sucursal2_id" id="sucursal2_id">
                    <option></option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
                <div class="text-danger error-message" id="error-sucursal2_id"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Salida</label>
                <input type="number" min="1" class="form-control form-control-sm" id="salida" name="salida" step="any">
                <div class="text-info" id="mensaje-salida"></div>
            </div>
        </div>
    </div>
</form>
