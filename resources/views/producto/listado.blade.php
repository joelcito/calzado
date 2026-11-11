@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton {
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE PRODUCTO <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioProducto">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Categoria</label>
                                    <select class="form-select form-select-sm" name="categoria_producto_id" id="categoria_producto_id">
                                        <option value="">Seleccione</option>
                                        @foreach ( $categorias as $categoria)
                                            <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-message" id="error-categoria_producto_id"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Dueño</label>
                                    <select class="form-select form-select-sm" name="usuario_duenio_id" id="usuario_duenio_id">
                                        <option value="">Seleccione</option>
                                        @foreach ( $usuarios as $usuario)
                                            <option value="{{$usuario->id}}">{{$usuario->nombres." ".$usuario->ap_paterno." ".$usuario->ap_materno}}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-message" id="error-usuario_duenio_id"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                    <div class="text-danger error-message" id="error-nombre"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Codigo</label>
                                    <input type="text" class="form-control form-control-sm" id="codigo"
                                        name="codigo">
                                    <div class="text-danger error-message" id="error-codigo"></div>
                                </div>
                            </div>
                            {{-- <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tipo</label>
                                    <select name="tipo" id="tipo" class="form-control form-control-sm"
                                        required>
                                        <option value="">Seleccione</option>
                                        <option value="PRODUCTO">PRODUCTO</option>
                                        <option value="SERVICIO">SERVICIO</option>
                                    </select>
                                    <div class="text-danger error-message" id="error-tipo"></div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Compra</label>
                                    <input type="number" class="form-control form-control-sm" id="precio_compra"
                                        name="precio_compra">
                                    <div class="text-danger error-message" id="error-precio_compra"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Venta</label>
                                    <input type="number" class="form-control form-control-sm" id="precio_venta"
                                        name="precio_venta">
                                    <div class="text-danger error-message" id="error-precio_venta"></div>
                                </div>
                            </div>
                            {{-- <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Minimo Stock</label>
                                    <input type="text" class="form-control form-control-sm" id="minimo_stock"
                                        name="minimo_stock">
                                    <div class="text-danger error-message" id="error-minimo_stock"></div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <label class="fw-semibold fs-6 mb-2">Imagen del Producto</label>
                                <input type="file" class="form-control form-control-sm" name="imagen_producto" id="imagen_producto"
                                    accept="image/*">
                                <div class="text-danger error-message" id="error-imagen_producto"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" id="btn-guardar" onclick="guardarProducto()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->
    <!--Modal Stock de Sucursal-->
    <div class="modal fade" id="modalStockSucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">CANTIDAD STOCK POR SUCUSAL DEL PRODUCTO: <span class="text-info"
                            id="nombre_producto"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <div id="tabla_stock">
                    </div>
                </div>
                <div class="modal-footer">
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>

    <div class="modal fade" id="modalStockSucursalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">INGRESO DE STOCK: <span class="text-info" id="nombre_producto_stock"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioStockSucursal">
                        <input type="hidden" name="producto_id" id="producto_id">
                        <input type="hidden" name="sucursal_id" id="sucursal_id">
                        <div class="row">
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_sucursal"
                                        name="nombre_sucursal" @readonly(true)>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha de Registro</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha"
                                        name="fecha" value="{{ date('Y-m-d') }}" @readonly(true)>
                                    <div class="text-danger error-message" id="error-fecha"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ingreso la cantidad</label>
                                    <input type="number" min="1" step="any" class="form-control form-control-sm" id="cantidad_ingreso" name="cantidad_ingreso">
                                    <div class="text-danger error-message" id="error-cantidad_ingreso"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Compra</label>
                                    <input type="number" class="form-control form-control-sm" id="f_precio_compra" name="f_precio_compra">
                                    <div class="text-danger error-message" id="error-f_precio_compra"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Venta</label>
                                    <input type="number" class="form-control form-control-sm" id="f_precio_venta" name="f_precio_venta">
                                    <div class="text-danger error-message" id="error-f_precio_venta"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fw-semibold fs-6 mb-2">Descripcion</label>
                                <textarea class="form-control form-control-sm" name="descripcion" id="descripcion" cols="30" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarStockSucursal()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>

    <div class="modal fade" id="modalSalidaSucursalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">SALIDA DE STOCK: <span class="text-info" id="nombre_producto_salida"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioSalidaSucursal">
                        <input type="hidden" name="salida_producto_id" id="salida_producto_id">
                        <input type="hidden" name="salida_sucursal_id" id="salida_sucursal_id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                    <input type="text" class="form-control form-control-sm" id="salida_nombre_sucursal"
                                        name="salida_nombre_sucursal" @readonly(true)>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Salida de la cantidad</label>
                                    <input type="number" min="1" step="any" class="form-control form-control-sm" id="cantidad_salida" name="cantidad_salida">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha de Registro</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha"
                                        name="salida_fecha" value="{{ date('Y-m-d') }}" @readonly(true)>
                                    <div class="text-danger error-message" id="error-salida_fecha"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-semibold fs-6 mb-2">Descripcion</label>
                                <textarea class="form-control form-control-sm" name="salida_descripcion" id="salida_descripcion" cols="30" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarSalidaSucursal()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--Fin modal Stock de Sucursal-->
    <!--Modal tranferencia de sucursal-->
    <div class="modal fade" id="modalTransferenciaSucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">TRANSFERENCIA DE PRODUCTO: <span class="text-info"
                            id="nombre_producto_transferencia"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y" id="formulario_transferencia">

                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success"
                                onclick="guardarTransferenciaSucursal()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--Fin modal transferencia de Sucursal-->
    <!--Modal de reportes-->
    <div class="modal fade" id="modalReporte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">REPORTES<span class="text-info"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="mb-5 hover-scroll-x">
                                <div class="d-grid">
                                    <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                        <li class="nav-item w-100">
                                            <a class="nav-link active btn btn-active-light-info btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                                data-bs-toggle="tab" href="#kt_tab_pane_1">INGRESO</a>
                                        </li>
                                        <li class="nav-item w-100">
                                            <a class="nav-link btn btn-active-light-info btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                                data-bs-toggle="tab" href="#kt_tab_pane_2">SALIDA</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <form method="POST" action="{{ route('producto.generarReporteIngreso') }}"
                                        id="formularioReporteIngreso" target="_blank">
                                        @csrf
                                        <input type="hidden" name="ingreso_producto_id" id="ingreso_producto_id">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label
                                                        class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                                    <select data-control="select2" data-placeholder="Seleccione"
                                                        data-dropdown-parent="#formularioReporteIngreso"
                                                        class="form-select form-select-solid fw-bold" name="ingreso_tipo"
                                                        id="ingreso_tipo">
                                                        <option></option>
                                                        <option value="SUCURSAL">SUCURSAL</option>
                                                        <option value="USUARIO">USUARIO</option>
                                                    </select>
                                                    <div class="text-danger error-message" id="error-ingreso_tipo"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="required fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        id="ingreso_fecha_ini" name="ingreso_fecha_ini" max="{{ date('Y-m-d') }}">
                                                    <div class="text-danger error-message" id="error-ingreso_fecha_ini">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        id="ingreso_fecha_fin" name="ingreso_fecha_fin" max="{{ date('Y-m-d') }}">
                                                    <div class="text-danger error-message" id="error-ingreso_fecha_fin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                {{-- <button class="btn btn-sm w-100 btn-success" onclick="generarReporteIngreso()">Imprimir</button> --}}
                                                <button type="submit"
                                                    class="btn btn-sm w-100 btn-success">Imprimir</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                    <form method="POST" action="{{ route('producto.generarReporteSalida') }}"
                                        id="formularioReporteSalida" target="_blank">
                                        @csrf
                                        <input type="hidden" name="salida_producto_id" id="salida_producto_id">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label
                                                        class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                                    <select data-control="select2" data-placeholder="Seleccione"
                                                        data-dropdown-parent="#formularioReporteSalida"
                                                        class="form-select form-select-solid fw-bold" name="salida_tipo"
                                                        id="salida_tipo">
                                                        <option></option>
                                                        <option value="SUCURSAL">SUCURSAL</option>
                                                        <option value="USUARIO">USUARIO</option>
                                                    </select>
                                                    <div class="text-danger error-message" id="error-salida_tipo"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="required fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        id="salida_fecha_ini" name="salida_fecha_ini" max="{{ date('Y-m-d') }}">
                                                    <div class="text-danger error-message" id="error-salida_fecha_ini">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        id="salida_fecha_fin" name="salida_fecha_fin" max="{{ date('Y-m-d') }}">
                                                    <div class="text-danger error-message" id="error-salida_fecha_fin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                {{-- <button class="btn btn-sm w-100 btn-success" onclick="generarReporteIngreso()">Imprimir</button> --}}
                                                <button type="submit"
                                                    class="btn btn-sm w-100 btn-success">Imprimir</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--Fin modal de reportes-->

    <!--Modal recargar masivo -->
    <div class="modal fade" id="modalRegistroMasivo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">REGISTRO DE PRODUCTO MASIVO: <span class="text-info"
                            id="nombre_registro_masivo"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_importar_servicios_productos_excel">
                        <div class="row">
                            <div class="col-md-11">
                                <input type="file" class="form-control form-control-sm" name="excel_producto_masivo" id="excel_producto_masivo">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success"
                                onclick="importarServiciosProductosExcel()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--Fin modal recargar masivo-->

    <div class="modal fade" id="modalIngresoStockMasivo" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-xl">

            <div class="modal-content">

                <div class="modal-header">

                    <h3 class="fw-bold">
                        INGRESO MASIVO DE STOCK
                    </h3>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    {{-- CABECERA --}}

                    <div class="row mb-4">

                        <div class="col-md-4">

                            <label class="required fw-semibold mb-2">
                                Sucursal
                            </label>

                            <select id="sucursal_stock_masivo" class="form-control">

                                <option value="">
                                    SELECCIONE SUCURSAL
                                </option>

                                @foreach($sucursales as $sucursal)

                                <option value="{{ $sucursal->id }}">
                                    {{ $sucursal->nombre }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-5">

                            <label class="fw-semibold mb-2">
                                Descripción
                            </label>

                            <input type="text" id="descripcion_stock_masivo" class="form-control"
                                placeholder="Ej: Compra proveedor / reposición de stock">

                        </div>


                        <div class="col-md-3">

                            <label class="fw-semibold mb-2">
                                Buscar producto
                            </label>

                            <input type="text" id="buscar_stock_masivo" class="form-control" placeholder="Buscar...">

                        </div>

                    </div>


                    <div class="table-responsive">

                        <table class="table table-bordered table-striped align-middle" id="tabla_stock_masivo">

                            <thead>

                                <tr>

                                    <th width="5%">
                                        #
                                    </th>

                                    <th>
                                        Producto
                                    </th>

                                    <th width="10%">
                                        Stock
                                    </th>

                                    <th width="14%">
                                        Precio Compra
                                    </th>

                                    <th width="14%">
                                        Precio Venta
                                    </th>

                                    <th width="14%">
                                        Cantidad Ingreso
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="tbody_stock_masivo">

                                {{-- AJAX --}}

                            </tbody>

                        </table>

                    </div>


                    <div class="alert alert-info mt-3">

                        Solo se registrarán los productos cuya
                        <strong>cantidad de ingreso sea mayor a 0.</strong>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="button" class="btn btn-success" id="btnGuardarStockMasivo" onclick="guardarStockMasivo()">

                        <i class="fa fa-save"></i>

                        Guardar ingreso masivo

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <h3 class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            LISTADO DE PRODUCTO</h3>
                        <div class="card-toolbar">
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <a class="btn btn-sm fw-bold btn-danger ml-5" onclick="modalStockMasivo()"><i class="fa fa-upload"></i>Registro Stock Masivo</a>
                                </div>
                                <div class="col-md-4">
                                    <a class="btn btn-sm fw-bold btn-danger ml-5" onclick="modalProductoMasivo()"><i class="fa fa-upload"></i>Registro Masivo</a>
                                </div> --}}
                            </div>
                            {{-- <a class="btn btn-sm fw-bold btn-success my-2" onclick="modalIngresoStockMasivo()"> <i class="fa fa-boxes"></i> Ingreso Stock Masivo</a> --}}
                            <a class="btn btn-sm fw-bold btn-primary my-2" onclick="modalNuevoProducto()"><i class="fa fa-plus"></i>Nuevo</a>
                            {{-- <a class="btn btn-sm fw-bold btn-danger" href="{{ route('producto.pdfProductoStock') }}" target="_blank">Imprimir Productos</a> --}}
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div id="table_listado">

                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).ready(function() {
            ajaxListado();

            $('#sucursal_stock_masivo').on('change', function() {
                let sucursalId = $(this).val();
                if (!sucursalId) {
                    $('#tbody_stock_masivo').html(`
                    <tr>
                        <td colspan="6" class="text-center">

                            Seleccione una sucursal.

                        </td>
                    </tr>
                    `);
                    return;
                }
                cargarProductosStockMasivo(sucursalId);

            });

            $('#buscar_stock_masivo').on('keyup', function() {

                let buscar = $(this)
                    .val()
                    .toLowerCase()
                    .trim();


                $('.fila-stock-masivo').each(function() {

                    let producto = $(this)
                        .data('producto');


                    if (producto.includes(buscar)) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            });
        });

        function ajaxListado() {

            let datos = {};
            $.ajax({
                url: "{{ route('producto.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    // Swal.close();
                }
            })
        }

        function limpiarErorres() {
            $('.error-message').html('');
            $('.is-invalid').removeClass('is-invalid');
        }

        function modalNuevoProducto() {
            limpiarErorres();

            $('#id').val(0);
            $('#nombre').val('');
            $('#codigo').val('');
            $('#precio_compra').val('');
            $('#precio_venta').val('');
            $('#minimo_stock').val('');
            $('#tipo').val('');
            $('#categoria_producto_id').val('');
            $('#usuario_duenio_id').val('');
            $('#btn-guardar').attr('disabled', false);

            $('#modalProducto').modal('show')
        }

        function guardarProducto() {

            let formulario = $('#formularioProducto')[0];
            let datos = new FormData(formulario); // Aquí usamos FormData
            $('#btn-guardar').attr('disabled', true);

            // let datos = $('#formularioProducto').serializeArray();
            $.ajax({
                url: "{{ route('producto.guardarProducto') }}",
                method: "POST",
                data: datos,
                processData: false, // Muy importante
                contentType: false, // Muy importante
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 3000, // Se cierra en 3 segundos
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalProducto').modal('hide')
                    } else {
                        $('#btn-guardar').attr('disabled', false);
                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            let errorDiv = $('#error-' + key);

                            if (input.length > 0) {
                                input.addClass('is-invalid'); // Agregar clase de error
                                errorDiv.html('<span>' + messages[0] + '</span>'); // Mostrar mensaje
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                    $('#btn-guardar').attr('disabled', false);
                }
            });
        }

        function editarProducto(producto) {
            limpiarErorres();

            Object.keys(producto).forEach(key => {
                let input = $(`#${key}`);

                // Saltar los inputs de tipo file
                if (input.attr('type') === 'file') {
                    return; // ignorar este campo
                }

                if (input.is(':checkbox')) {
                    // Marcar si el valor es 1, true o "on"
                    input.prop('checked', producto[key] == 1 || producto[key] === true || producto[key] === "on");
                } else if (input.is('select')) {
                    // Para selects con librerías como Select2
                    input.val(producto[key]).trigger('change');
                } else if (input.length) {
                    // Para inputs normales (text, number, email, etc.)
                    input.val(producto[key]);
                }
            });
            $('#modalProducto').modal('show');
        }

        function eliminarProducto(producto) {
            Swal.fire({
                title: "Quieres eliminar " + producto.nombre,
                text: "Ya no podras recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('producto.eliminarProducto') }}",
                        method: "POST",
                        data: producto,
                        success: function(resultado) {
                            if (resultado.estado) {
                                ajaxListado();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',

                            });
                        }
                    });
                } else if (result.dismiss === "cancel") {
                    Swal.fire(
                        "Cancelado",
                        "La operacion fue cancelada",
                        "error"
                    )
                }
            });

        }
        //ADICIONAR STOCK
        function adicionarStockSucursal(producto) {

            $('#nombre_producto').html('')
            $('#tabla_stock').html('');

            $('#nombre_producto_salida').html('')
            $.ajax({
                url: "{{ route('producto.ajaxStockSucursal') }}",
                method: "POST",
                data: {
                    producto_id: producto.id
                },
                success: function(resultado) {

                    if (resultado.estado) {
                        $('#nombre_producto').html(producto.nombre);
                        $('#tabla_stock').html(resultado.data.listado);

                        $('#nombre_producto_salida').html(producto.nombre);

                        $('#modalStockSucursal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            })
        }

        function adicionarStockSucursalProducto(sucursal, producto) {
            $('#nombre_producto_stock').html('');
            limpiarErorres();

            $('#nombre_producto_stock').html(producto.nombre);
            $('#nombre_sucursal').val(sucursal.nombre);
            $('#producto_id').val(producto.id);
            $('#sucursal_id').val(sucursal.id);
            $('#cantidad_ingreso').val('');
            $('#f_precio_compra').val('');
            $('#f_precio_venta').val('');
            $('#descripcion').val('');
            $('#modalStockSucursalProducto').modal('show');

        }

        function guardarStockSucursal() {
            let datos = $('#formularioStockSucursal').serializeArray();
            $.ajax({
                url: "{{ route('producto.guardarStockSucursal') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        adicionarStockSucursal(resultado.data);
                        //ajaxListado();//nuevo
                        $('#modalStockSucursalProducto').modal('hide');
                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            let errorDiv = $('#error-' + key);

                            if (input.length > 0) {
                                input.addClass('is-invalid'); // Agregar clase de error
                                errorDiv.html('<span>' + messages[0] + '</span>'); // Mostrar mensaje
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });
        }

        //ADICIONAR SALIDA
        function adicionarSalidaSucursalProducto(sucursal, producto) {
            $('#nombre_producto_salida').html('')
            limpiarErorres();

            $('#nombre_producto_salida').html(producto.nombre)
            $('#salida_nombre_sucursal').val(sucursal.nombre)
            $('#salida_producto_id').val(producto.id)
            $('#salida_sucursal_id').val(sucursal.id)
            $('#salida_stock').val('')
            // $('#fecha').val('')
            $('#salida_descripcion').val('')
            $('#modalSalidaSucursalProducto').modal('show')

        }

        function guardarSalidaSucursal() {
            let datos = $('#formularioSalidaSucursal').serializeArray();
            $.ajax({
                url: "{{ route('producto.guardarSalidaSucursal') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 2000, // Se cierra en 3 segundos
                            showConfirmButton: false
                        });
                        adicionarStockSucursal(resultado.data);
                        //ajaxListado();//nuevo
                        $('#modalSalidaSucursalProducto').modal('hide');
                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            let errorDiv = $('#error-' + key);

                            if (input.length > 0) {
                                input.addClass('is-invalid'); // Agregar clase de error
                                errorDiv.html('<span>' + messages[0] + '</span>'); // Mostrar mensaje
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });
        }

        //TRANFERENCIA SUCURSALES
        function transferenciaSucursal(producto) {
            $('#nombre_producto_transferencia').html('')
            $('#mensaje-salida').html('')
            limpiarErorres();

            $('#nombre_producto_transferencia').html(producto.nombre)
            datos = {
                producto_id: producto.id
            }
            $.ajax({
                url: "{{ route('producto.ajaxFormTransferencia') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#formulario_transferencia').html(resultado.data.formulario)
                        // Re-inicializamos Select2 en los nuevos selects
                        $('#sucursal1_id, #sucursal2_id').select2({
                            dropdownParent: $('#modalTransferenciaSucursal')
                        });

                        // Configuramos el evento después de insertar el HTML
                        configurarEventoSucursal();

                        $('#modalTransferenciaSucursal').modal('show')
                    }
                }
            });
        }

        function configurarEventoSucursal() {
            const selectSucursal1 = $('#sucursal1_id');
            const selectSucursal2 = $('#sucursal2_id');

            // Guardamos las opciones originales por única vez
            const opcionesSucursal2 = selectSucursal2.html();

            // Evento cuando se selecciona una sucursal de salida
            selectSucursal1.off('select2:select').on('select2:select', function (e) {
                const sucursalSeleccionada = e.params.data.id;

                // Restauramos las opciones originales
                selectSucursal2.html(opcionesSucursal2);

                // Removemos la opción seleccionada en sucursal1 del select sucursal2
                selectSucursal2.find('option[value="' + sucursalSeleccionada + '"]').remove();

                // Refrescamos Select2
                selectSucursal2.val(null).trigger('change');
            });
        }

        function guardarTransferenciaSucursal() {
            let datos = $('#formularioTransferenciaSucursal').serializeArray();
            $.ajax({
                url: "{{ route('producto.guardarTransferenciaSucursal') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 2000, // Se cierra en 2 segundos
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalTransferenciaSucursal').modal('hide')
                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            let errorDiv = $('#error-' + key);

                            if (input.length > 0) {
                                input.addClass('is-invalid'); // Agregar clase de error
                                errorDiv.html('<span>' + messages[0] + '</span>'); // Mostrar mensaje
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });
        }

        //REPORTES
        function obtenerReporte(producto) {
            limpiarErorres();

            $('#ingreso_producto_id').val(producto.id)
            $('#ingreso_fecha_ini').val('')
            $('#ingreso_fecha_fin').val('')
            $('#ingreso_tipo').val(null).trigger('change')
            $('#salida_producto_id').val(producto.id)
            $('#salida_fecha_ini').val('')
            $('#salida_fecha_fin').val('')
            $('#salida_tipo').val(null).trigger('change')
            $('#modalReporte').modal('show')
        }

        //IMPORTAR EXCEL
        function modalRegistroMasivo() {
            $('#excel_producto_masivo').val('');
            $('#modalRegistroMasivo').modal('show');
        }

        function importarServiciosProductosExcel() {

            if ($("#formulario_importar_servicios_productos_excel")[0].checkValidity()) {
                let datos = new FormData($("#formulario_importar_servicios_productos_excel")[0]);
                $.ajax({
                    url: "{{ route('producto.importarServiciosProductosExcel') }}",
                    method: "POST",
                    data: datos,
                    contentType: false,
                    processData: false,
                    success: function(data) {

                        if (data.estado === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE REGISTRO CON EXITO",
                            })
                            $('#modalRegistroMasivo').modal('hide');
                        } else if (data.estado === 'warning') {

                            // Supongamos que `datosErroneos` es tu array con las observaciones
                            const datosErroneos = data.errores;

                            // Crea un string HTML a partir del array
                            const erroresHtml = datosErroneos.map(error =>
                                `<li>${error.texto} en el fila [ ${error.fila} ]</li>`).join('');

                            Swal.fire({
                                icon: 'warning',
                                title: data.titulo,
                                text: data.text,
                                html: `
                                    SE REGISTRÓ, PERO HAY OBSERVACIONES:<br>
                                    <ul>${erroresHtml}</ul>
                                `,
                            })

                            $('#modalRegistroMasivo').modal('hide');

                        } else if (data.estado === 'error') {
                            Swal.fire({
                                icon: 'error',
                                title: "ERROR!",
                                text: data.text,
                            })
                            $('#modalRegistroMasivo').modal('hide');
                        }

                        ajaxListado();

                    }
                })

            } else {
                $("#formulario_importar_servicios_productos_excel")[0].reportValidity();
            }
        }

        function modalIngresoStockMasivo() {

            $('#sucursal_stock_masivo').val('');

            $('#descripcion_stock_masivo').val('');

            $('#buscar_stock_masivo').val('');

            $('#tbody_stock_masivo').html(`
            <tr>
                <td colspan="6" class="text-center text-muted">

                    Seleccione una sucursal.

                </td>
            </tr>
            `);

            $('#modalIngresoStockMasivo').modal('show');
        }

        function cargarProductosStockMasivo(sucursalId) {

            $('#tbody_stock_masivo').html(`
                <tr>
                    <td colspan="6"
                        class="text-center">

                        Cargando productos...

                    </td>
                </tr>
            `);


            $.ajax({

                url: "{{ route('producto.ajaxProductosStockMasivo') }}",

                method: "POST",

                data: {
                    sucursal_id: sucursalId
                },

                success: function(resultado) {

                    if (!resultado.estado) {
                        return;
                    }


                    let html = '';


                    resultado.productos.forEach(function(producto, index) {

                        html += `

                            <tr class="fila-stock-masivo"
                                data-producto="${producto.nombre.toLowerCase()}">

                                <td>
                                    ${index + 1}
                                </td>


                                <td>

                                    <strong>
                                        ${producto.nombre}
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        ${producto.codigo ?? ''}
                                    </small>

                                    <input type="hidden"
                                        class="producto-id"
                                        value="${producto.id}">

                                </td>


                                <td class="text-center">

                                    <span class="badge badge-light-primary">

                                        ${parseFloat(
                                            producto.stock_actual ?? 0
                                        )}

                                    </span>

                                </td>


                                <td>

                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control form-control-sm precio-compra"
                                        value="${producto.precio_compra ?? 0}">

                                </td>


                                <td>

                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control form-control-sm precio-venta"
                                        value="${producto.precio_venta ?? 0}">

                                </td>


                                <td>

                                    <input type="number"
                                        step="any"
                                        min="0"
                                        class="form-control form-control-sm cantidad-ingreso"
                                        value="">

                                </td>

                            </tr>

                        `;

                    });


                    $('#tbody_stock_masivo').html(html);

                },

                error: function() {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los productos.'
                    });

                }

            });
        }

        function guardarStockMasivo() {

            let sucursalId =
                $('#sucursal_stock_masivo').val();

            let descripcion =
                $('#descripcion_stock_masivo').val();


            if (!sucursalId) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debe seleccionar una sucursal.'
                });

                return;
            }


            let productos = [];


            $('.fila-stock-masivo').each(function() {

                let cantidad =
                    parseFloat(
                        $(this)
                            .find('.cantidad-ingreso')
                            .val()
                    ) || 0;


                // SOLO TOMAMOS LOS QUE TIENEN INGRESO
                if (cantidad > 0) {

                    productos.push({

                        producto_id:
                            $(this)
                                .find('.producto-id')
                                .val(),

                        cantidad_ingreso:
                            cantidad,

                        precio_compra:
                            parseFloat(
                                $(this)
                                    .find('.precio-compra')
                                    .val()
                            ) || 0,

                        precio_venta:
                            parseFloat(
                                $(this)
                                    .find('.precio-venta')
                                    .val()
                            ) || 0

                    });

                }

            });


            if (productos.length === 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debe ingresar cantidad al menos a un producto.'
                });

                return;
            }


            Swal.fire({

                icon: 'question',

                title: '¿Registrar ingreso?',

                html:
                    'Se registrará el ingreso de <b>' +
                    productos.length +
                    '</b> productos.',

                showCancelButton: true,

                confirmButtonText: 'Sí, registrar',

                cancelButtonText: 'Cancelar'

            }).then(function(result) {

                if (!result.isConfirmed) {
                    return;
                }


                $('#btnGuardarStockMasivo')
                    .prop('disabled', true);


                $.ajax({

                    url: "{{ route('producto.guardarStockMasivo') }}",

                    method: "POST",

                    data: {

                        sucursal_id: sucursalId,

                        descripcion: descripcion,

                        productos: productos

                    },

                    success: function(resultado) {

                        if (resultado.estado) {

                            Swal.fire({

                                icon: 'success',

                                title: 'Registro exitoso',

                                text:
                                    resultado.cantidad +
                                    ' productos ingresados correctamente.'

                            });


                            $('#modalIngresoStockMasivo')
                                .modal('hide');


                            ajaxListado();

                        }

                    },

                    error: function(xhr) {

                        let mensaje =
                            xhr.responseJSON?.message ??
                            'Ocurrió un error al registrar el stock.';


                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: mensaje
                        });

                    },

                    complete: function() {

                        $('#btnGuardarStockMasivo')
                            .prop('disabled', false);

                    }

                });

            });

        }

    </script>
@endsection
