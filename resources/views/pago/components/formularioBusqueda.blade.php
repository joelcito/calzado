<form id="formulario-busqueda-factura">
    <div class="row">
        <div class="col-md-2">
            <label class="fw-semibold fs-6 mb-2">Sucursal</label>
            <select class="form-control form-control-sm" name="sucursal_id" id="sucursal_id">
                <option value="">Seleccione</option>
                @foreach ( $sucursales as $sucursal)
                    <option value="{{$sucursal->id}}">{{$sucursal->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-4">
                    <label class="fw-semibold fs-6 mb-2">Nombres</label>
                    <input type="text" class="form-control form-control-sm" name="buscar_nombre_cliente" id="buscar_nombre_cliente">
                </div>
                <div class="col-md-4">
                    <label class="fw-semibold fs-6 mb-2">Ap. Paterno</label>
                    <input type="text" class="form-control form-control-sm" name="buscar_ap_paterno_cliente" id="buscar_ap_paterno_cliente">
                </div>
                <div class="col-md-4">
                    <label class="fw-semibold fs-6 mb-2">Ap. Materno</label>
                    <input type="text" class="form-control form-control-sm" name="buscar_ap_materno_cliente" id="buscar_ap_materno_cliente">
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <label class="fw-semibold fs-6 mb-2">Celuar</label>
            <input type="number" class="form-control form-control-sm" name="buscar_numero_celular" id="buscar_numero_celular">
        </div>
        <div class="col-md-1">
            <label class="fw-semibold fs-6 mb-2">C.I. Persona</label>
            <input type="number" class="form-control form-control-sm" name="buscar_nro_cedula"
                id="buscar_nro_cedula">
        </div>
        <div class="col-md-2">
            <label class="fw-semibold fs-6 mb-2">NIT</label>
            <input type="number" class="form-control form-control-sm" name="buscar_nit"
                id="buscar_nit">
        </div>
        <div class="col-md-1">
            <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
            <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio"
                id="buscar_fecha_inicio" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-1">
            <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
            <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin"
                id="buscar_fecha_fin" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-1">
            <div class="row">
                <div class="col-md-4">
                    <button type="button" id="botom_genera_buscar"
                        class="btn btn-success btn-sm w-100 mt-8 btn-icon"
                        onclick="ajaxListado()"><i class="fa fa-search"></i></button>
                </div>
                <div class="col-md-4">
                    <button type="button" id="botom_genera_pdf"
                        class="btn btn-danger btn-sm w-100 btn-icon mt-8" title="Expotar en PDF"
                        onclick="reportePDF()"><i class="fa fa-file-pdf"></i></button>
                </div>
                <div class="col-md-4">
                    <button type="button" id="botom_genera_excel"
                        class="btn btn-success btn-sm w-100 btn-icon mt-8" title="Expotar en Excel"
                        onclick="exportarExcel()"><i class="fa fa-file-excel"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
