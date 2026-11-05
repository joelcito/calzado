@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- CABECERA --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Reportes
                    </h4>

                    <small class="text-muted">
                        Generación de reportes del sistema
                    </small>
                </div>

            </div>

        </div>

        <div class="card-body">

            {{-- ================================================= --}}
            {{-- FILTROS GENERALES --}}
            {{-- ================================================= --}}

            <div class="row">

                <div class="col-md-3 mb-3">

                    <label>
                        Fecha inicio
                    </label>

                    <input type="date" id="fecha_inicio" class="form-control"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}">

                </div>


                <div class="col-md-3 mb-3">

                    <label>
                        Fecha fin
                    </label>

                    <input type="date" id="fecha_fin" class="form-control" value="{{ now()->format('Y-m-d') }}">

                </div>


                <div class="col-md-4 mb-3">

                    <label>
                        Sucursal
                    </label>

                    <select id="sucursal_id" class="form-control">

                        <option value="">
                            TODAS LAS SUCURSALES
                        </option>

                        @foreach($sucursales as $sucursal)

                        <option value="{{ $sucursal->id }}">
                            {{ $sucursal->nombre }}
                        </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2 mb-3">

                    <label>&nbsp;</label>

                    <button type="button" class="btn btn-secondary btn-block" id="btnLimpiar">

                        <i class="fas fa-eraser"></i>
                        Limpiar

                    </button>

                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- REPORTES --}}
    {{-- ========================================================= --}}

    <div class="row">


        {{-- ===================================================== --}}
        {{-- VENTAS --}}
        {{-- ===================================================== --}}

        @rol(1)
        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">

                            <i class="fas fa-shopping-cart fa-3x text-primary"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Reporte de Ventas
                            </h5>

                            <small class="text-muted">
                                Ventas realizadas en un rango de fechas.
                            </small>

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-6">

                            <button type="button" class="btn btn-danger btn-block" onclick="generarReporte(
                                        '{{ route('reporte.ventas.pdf') }}'
                                    )">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </button>

                        </div>


                        <div class="col-6">

                            <button type="button" class="btn btn-success btn-block" onclick="generarReporte(
                                        '{{ route('reporte.ventas.excel') }}'
                                    )">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        @endrol



        {{-- ===================================================== --}}
        {{-- CUENTAS POR COBRAR --}}
        {{-- ===================================================== --}}

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">

                            <i class="fas fa-hand-holding-usd fa-3x text-warning"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Cuentas por Cobrar
                            </h5>

                            <small class="text-muted">
                                Ventas y servicios con saldos pendientes.
                            </small>

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-6">

                            <button type="button" class="btn btn-danger btn-block" onclick="generarReporte(
                                        '{{ route('reporte.cuentasCobrar.pdf') }}'
                                    )">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </button>

                        </div>


                        <div class="col-6">

                            <button type="button" class="btn btn-success btn-block" onclick="generarReporte(
                                        '{{ route('reporte.cuentasCobrar.excel') }}'
                                    )">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- INVENTARIO --}}
        {{-- ===================================================== --}}

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">

                            <i class="fas fa-boxes fa-3x text-info"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Inventario
                            </h5>

                            <small class="text-muted">
                                Stock actual y productos con stock bajo.
                            </small>

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-6">

                            <button type="button" class="btn btn-danger btn-block" onclick="generarReporte(
                                        '{{ route('reporte.inventario.pdf') }}'
                                    )">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </button>

                        </div>


                        <div class="col-6">

                            <button type="button" class="btn btn-success btn-block" onclick="generarReporte(
                                        '{{ route('reporte.inventario.excel') }}'
                                    )">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- KARDEX --}}
        {{-- ===================================================== --}}

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">
                            <i class="fas fa-exchange-alt fa-3x text-success"></i>
                        </div>

                        <div>

                            <h5 class="mb-1">
                                Kardex
                            </h5>

                            <small class="text-muted">
                                Historial de ingresos y salidas por producto.
                            </small>

                        </div>

                    </div>

                    <hr>


                    {{-- =============================================== --}}
                    {{-- PRODUCTO --}}
                    {{-- =============================================== --}}

                    <div class="form-group mb-3">

                        <label for="producto_id_kardex">
                            Producto <span class="text-danger">*</span>
                        </label>

                        <select id="producto_id_kardex" class="form-control">

                            <option value="">
                                SELECCIONE PRODUCTO
                            </option>

                            @foreach($productos as $producto)

                            <option value="{{ $producto->id }}">

                                {{ $producto->codigo }}
                                -
                                {{ $producto->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =============================================== --}}
                    {{-- BOTONES --}}
                    {{-- =============================================== --}}

                    <div class="row">

                        <div class="col-6">

                            <button type="button" class="btn btn-danger btn-block"
                                onclick="generarKardex('{{ route('reporte.kardex.pdf') }}')">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </button>

                        </div>


                        <div class="col-6">

                            <button type="button" class="btn btn-success btn-block"
                                onclick="generarKardex('{{ route('reporte.kardex.excel') }}')">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- FLUJO DE EFECTIVO --}}
        {{-- ===================================================== --}}

        @rol(1)
        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">

                            <i class="fas fa-money-bill-wave fa-3x text-success"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Flujo de Efectivo
                            </h5>

                            <small class="text-muted">
                                Ingresos y salidas de dinero.
                            </small>

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-6">

                            <button type="button" class="btn btn-danger btn-block" onclick="generarReporte(
                                        '{{ route('reporte.flujoEfectivo.pdf') }}'
                                    )">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </button>

                        </div>


                        <div class="col-6">

                            <button type="button" class="btn btn-success btn-block" onclick="generarReporte(
                                        '{{ route('reporte.flujoEfectivo.excel') }}'
                                    )">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        @endrol

        {{-- ===================================================== --}}
        {{-- UTILIDADES --}}
        {{-- ===================================================== --}}

        @rol(1)

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="mr-3">

                            <i class="fas fa-chart-line fa-3x text-success"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Reporte de Utilidades
                            </h5>

                            <small class="text-muted">
                                Utilidad generada por productos y servicios vendidos.
                            </small>

                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" onclick="generarReporte('{{ route('reporte.utilidades.pdf') }}')"><i class="fas fa-file-pdf"></i> PDF</button>
                        </div>

                        <div class="col-6">
                            <button type="button" class="btn btn-success btn-block" onclick="generarReporte('{{ route('reporte.utilidades.excel') }}')"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        @endrol


    </div>

</div>

@endsection



@section('js')

<script>
    // ============================================================
    // GENERAR REPORTE
    // ============================================================

    function generarReporte(url) {

        let fechaInicio =
            $('#fecha_inicio').val();

        let fechaFin =
            $('#fecha_fin').val();

        let sucursalId =
            $('#sucursal_id').val();


        // ========================================================
        // VALIDACIONES
        // ========================================================

        if (!fechaInicio) {

            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccione la fecha inicial.'
            });

            return;
        }


        if (!fechaFin) {

            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccione la fecha final.'
            });

            return;
        }


        if (fechaInicio > fechaFin) {

            Swal.fire({
                icon: 'warning',
                title: 'Fechas incorrectas',
                text: 'La fecha inicial no puede ser mayor a la fecha final.'
            });

            return;
        }


        // ========================================================
        // PARÁMETROS
        // ========================================================

        let parametros =
            '?fecha_inicio=' + encodeURIComponent(fechaInicio) +
            '&fecha_fin=' + encodeURIComponent(fechaFin);


        if (sucursalId) {

            parametros +=
                '&sucursal_id=' +
                encodeURIComponent(sucursalId);

        }


        // ========================================================
        // ABRIR REPORTE
        // ========================================================

        window.open(
            url + parametros,
            '_blank'
        );

    }



    // ============================================================
    // LIMPIAR FILTROS
    // ============================================================

    $('#btnLimpiar').on('click', function () {

        $('#fecha_inicio').val(
            '{{ now()->startOfMonth()->format('Y-m-d') }}'
        );

        $('#fecha_fin').val(
            '{{ now()->format('Y-m-d') }}'
        );

        $('#sucursal_id').val('');

    });

    function generarKardex(url) {

        let productoId = $('#producto_id_kardex').val();

        let fechaInicio = $('#fecha_inicio').val();

        let fechaFin = $('#fecha_fin').val();

        let sucursalId = $('#sucursal_id').val();


        // PRODUCTO OBLIGATORIO
        if (!productoId) {

            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe seleccionar un producto para generar el Kardex.'
            });

            return;
        }


        // FECHA INICIO
        if (!fechaInicio) {

            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccione la fecha inicial.'
            });

            return;
        }


        // FECHA FIN
        if (!fechaFin) {

            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccione la fecha final.'
            });

            return;
        }


        // VALIDAR RANGO
        if (fechaInicio > fechaFin) {

            Swal.fire({
                icon: 'warning',
                title: 'Fechas incorrectas',
                text: 'La fecha inicial no puede ser mayor a la fecha final.'
            });

            return;
        }


        // ARMAMOS LOS PARÁMETROS
        let parametros =
            '?producto_id=' + encodeURIComponent(productoId) +
            '&fecha_inicio=' + encodeURIComponent(fechaInicio) +
            '&fecha_fin=' + encodeURIComponent(fechaFin);


        // SUCURSAL OPCIONAL
        if (sucursalId) {

            parametros +=
                '&sucursal_id=' +
                encodeURIComponent(sucursalId);

        }


        // ABRIR PDF O EXCEL
        window.open(
            url + parametros,
            '_blank'
        );
    }

</script>

@endsection
