@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_cliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Cliente</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_cliente">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombres</label>
                                <input type="text" class="form-control fw-bold form-control-solid"
                                    name="nombres_cliente_new_usuaio_empresa" id="nombres_cliente_new_usuaio_empresa"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Ap Paterno</label>
                                <input type="text" class="form-control fw-bold form-control-solid"
                                    name="ap_paterno_cliente_new_usuaio_empresa" id="ap_paterno_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                <input type="text" class="form-control fw-bold form-control-solid"
                                    name="ap_materno_cliente_new_usuaio_empresa" id="ap_materno_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Numero de Celular</label>
                                <input type="number" class="form-control fw-bold form-control-solid"
                                    name="num_ceular_cliente_new_usuaio_empresa" id="num_ceular_cliente_new_usuaio_empresa">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2">Cedula</label>
                                <input type="number" class="form-control fw-bold form-control-solid"
                                    name="cedula_cliente_new_usuaio_empresa" id="cedula_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2">Complemento</label>
                                <input type="number" class="form-control fw-bold form-control-solid"
                                    name="complemento_cliente_new_usuaio_empresa"
                                    id="complemento_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2">Nit</label>
                                <input type="number" class="form-control fw-bold form-control-solid"
                                    name="nit_cliente_new_usuaio_empresa" id="nit_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Razon Social</label>
                                <input type="text" class="form-control fw-bold form-control-solid"
                                    name="razon_social_cliente_new_usuaio_empresa"
                                    id="razon_social_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Correo</label>
                                <input type="text" class="form-control fw-bold form-control-solid"
                                    name="correo_cliente_new_usuaio_empresa" id="correo_cliente_new_usuaio_empresa">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm"
                                    onclick="guardarClienteEmpresa()">Agregar Usuario</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalAperturaCaja" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            @include('caja.components.formularioAperturaCaja')
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->


    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalCerrarCaja" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            @include('caja.components.formularioCerrarCaja', ['cajaAbierta' => $cajaAbierta])
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    @if ($cajaAbierta)
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxlg">
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-body py-4">
                            <div class="row">
                                <div class="col-md-10">
                                    <h1
                                        class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                        Formulario de Venta</h1>
                                </div>
                                <div class="col-md-2">
                                    <a class="btn btn-sm fw-bold btn-danger w-100" onclick="modalCerrarCaja()"><i
                                            class="fa fa-plus"></i>Cerrar Caja</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="tabla_clientes">
                                    </div>
                                    <hr>
                                    <div id="tabla_ventas">
                                        <form id="formulario_venta">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="required fw-semibold fs-6 mb-2">Producto / Servicio</label>
                                                            <select name="serivicio_id_venta" id="serivicio_id_venta"
                                                                class="form-control form-control-sm" onchange="identificaSericio(this)"
                                                                required>
                                                                <option value="">SELECCIONE</option>
                                                                @foreach ($servicios as $s)
                                                                    <option value="{{ $s }}">{{ $s->codigo ? $s->codigo . " - " : "" }}{{ $s->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="row">
                                                                <div class="col-md">
                                                                    <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        id="cantidad_venta" name="cantidad_venta" required
                                                                        onchange="calcularPrecioTotal()" onclick="this.select();">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2" id="div_control_stock">
                                                            <label class="required fw-semibold fs-6 mb-2">Stock en Sucursal</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="stock_sucursal" name="stock_sucursal" required readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                                            <input type="text" class="form-control form-control-sm" id="precio_venta" onclick="this.value='';" name="precio_venta" onchange="calcularPrecioTotal()" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="required fw-semibold fs-6 mb-2">Total</label>
                                                            <input type="number" class="form-control form-control-sm" id="total_venta"
                                                                name="total_venta" value="0" min="1" required readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <div class="d-flex justify-content-center gap-2 w-100">
                                                        {{-- <button class="btn btn-info btn-circle btn-sm btn-icon"
                                                            type="button" onclick="modalAgregarProducto()" title="Agregar Producto">
                                                            <i class="fa fa-cubes"></i> +
                                                        </button> --}}
                                                        {{-- <button class="btn btn-primary btn-circle btn-sm btn-icon" type="button"
                                                            onclick="mostraBloqueMasDatosProdcuto()" title="Mostrar más opción">
                                                            <i class="fa fa-note-sticky"></i> +
                                                        </button> --}}
                                                        <button class="btn btn-success btn-circle btn-sm btn-icon" type="button"
                                                            onclick="agregarProducto()" title="Agregar al Carro de compras"
                                                            id="boton-agrega-producto">
                                                            <i class="fa fa-xs fa-shopping-cart"></i> +
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="display: none;" id="bloque_mas_datos_productos">
                                                <div class="col-md-6">
                                                    <label class="fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                                    <textarea class="form-control form-control-sm" name="descripcion_adicional" id="descripcion_adicional"
                                                        cols="30" rows="1"></textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class=" fw-semibold fs-6 mb-2">Codigo Imei</label>
                                                    <input type="number" class="form-control form-control-sm" id="codigo_imei"
                                                        name="codigo_imei" min="1">
                                                </div>
                                            </div>
                                        </form>
                                        <hr>
                                        <div id="tabla_detalles" style="display: none;">
                                            <h2 class="text-center">CARRITO DE COMPRAS</h2>
                                            <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                                                <table id="carrito" class="table align-middle table-row-dashed fs-6 gy-5">
                                                    <thead>
                                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                            <th>Producto</th>
                                                            <th>Precio</th>
                                                            <th>Cantidad </th>
                                                            <th>Total</th>
                                                            <th>Descuento</th>
                                                            <th>Sub Total</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-semibold">
                                                        <!-- Aquí se agregarán las filas del carrito -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="4">Descuento Adicional</th>
                                                            <th colspan="3">Monto Total</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4">
                                                                <input class="form-control form-control-sm" name="descuento_adicional"
                                                                    id="descuento_adicional" type="number" value="0"
                                                                    onchange="ejecutarDescuentoAdicional()">
                                                            </td>
                                                            <td colspan="3">
                                                                <input class="form-control form-control-sm" name="monto_total"
                                                                    id="monto_total" type="number" readonly value="0">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">
                                                                <input class="form-control form-control-sm" name="descripcion_adicional_total" id="descripcion_adicional_total" type="text" placeholder="Descripcion adicional de la venta">
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row" id="bloque_seleccionar_cliente" style="display: none">
                                            <div class="col-md">
                                                {{-- <p><strong>NOTA:</strong> El Cliente se seleccionara automaticamente cuando se escoja un cachorro.</p> --}}
                                                <button class="btn btn-info btn-sm w-100" onclick="mostrarFormularioClientes()"><span
                                                        id="nombre_cliente"></span> <i class="fa fa-user-alt"></i></button>
                                                <input type="hidden" name="cliente_id_escogido" id="cliente_id_escogido">
                                            </div>
                                            {{-- <div class="col-md-1">
                                                <button title="Mostrar carro de compras"
                                                    class="btn btn-dark btn-sm btn-circle btn-icon"
                                                    onclick="mostrarCarritoVentas()"><i class="fa fa-shopping-basket"></i></button>
                                            --}}
                                                <button title="Agregar cliente" class="btn btn-primary btn-sm btn-circle btn-icon"
                                                    onclick="modalAgregarCliente()"><i class="fa fa-user-plus"></i></button>
                                            </div>
                                        </div>
                                        <form id="formulario_cliente_escogido" style="display: none">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Cedula / Nit</label>
                                                    <input type="number" class="form-control form-control-sm buscar-persona"
                                                        name="nit_escogido" id="nit_escogido">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Nombre</label>
                                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                                        name="nombre_escogido" id="nombre_escogido">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Ap Paterno</label>
                                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                                        name="ap_paterno_escogido" id="ap_paterno_escogido">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                                        name="ap_materno_escogido" id="ap_materno_escogido">
                                                </div>
                                            </div>
                                        </form>
                                        <div id="tabla-clientes-buscados">

                                        </div>
                                    </div>
                                    <hr>
                                    {{-- <div class="row" id="bloque-botones-emisiones" style="display: none">
                                        <div class="col-md-12">
                                            <button class="btn btn-dark w-100 btn-sm" onclick="escogerVentaTipo('RECIBO')">TICKED RECEPCION</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-success w-100 btn-sm"
                                                onclick="escogerVentaTipo('FACTURA')">FACTURA</button>
                                        </div>
                                    </div>
                                    <hr> --}}
                                    <div class="row" id="bloque_recibo" style="display: none">
                                        <div class="col-md-12 bg-light-success">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h2 class="text-center text-success">DATOS DE VENTA Y PAGO</h2>
                                                </div>
                                            </div>
                                            <form id="formularioGeneraRecibo">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="required">Tipo Pago</label>
                                                        <select name="tipo_pago_pagado_recibo" id="tipo_pago_pagado_recibo" class="form-control form-control-sm" onchange="validarCamposRecibo()">
                                                            <option value="">Seleccione</option>
                                                            <option value="EFECTIVO">EFECTIVO</option>
                                                            <option value="QR">QR</option>
                                                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="required">Monto Pagado</label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="monto_pagado_recibo" name="monto_pagado_recibo" value="0"
                                                            onkeyup="caluclarCambioRecibo(this)">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="required">Monto Venta</label>
                                                        <input type="number" class="form-control form-control-sm bg-success text-white" readonly
                                                            id="monto_total_pagado_recibo" name="monto_total_pagado_recibo"
                                                            value="0">
                                                    </div>
                                                </div>
                                                {{-- <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="">Tipo Pago 2</label>
                                                        <select name="tipo_pago_pagado_recibo_2" id="tipo_pago_pagado_recibo_2" class="form-control form-control-sm" onchange="validarCamposRecibo2()">
                                                            <option value="">Seleccione</option>
                                                            <option value="QR">QR</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="">Monto Pagado 2</label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="monto_pagado_recibo_2" name="monto_pagado_recibo_2" value="0"
                                                            onkeyup="caluclarCambioRecibo2(this)">
                                                    </div>
                                                </div> --}}
                                            </form>
                                            <div class="row mt-3">
                                                <div class="col-md">
                                                    <button class="btn btn-sm w-100 btn-success" onclick="emitirRecibo()" id="boton_enviar_recibo"> <i class="fa fa-spinner fa-spin" style="display:none;"></i>Enviar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
    @else
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxlg">
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-header flex-wrap py-4">
                            <h3
                                class="card-title page-heading d-flex text-danger fw-bold fs-3 flex-column justify-content-center my-0">
                                NO SE APERTURO LA CAJA</h3>
                            <div class="card-toolbar">
                                @if ($cajaAbierta)
                                    <a class="btn btn-sm fw-bold btn-danger" onclick="modalCerrarCaja()"><i
                                            class="fa fa-plus"></i>Cerrar Caja</a>
                                @else
                                    <a class="btn btn-sm fw-bold btn-success" onclick="modalAperturaCaja()"><i
                                            class="fa fa-plus"></i>Abrir Caja</a>
                                @endif
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
    @endif

@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        var arrayProductos = [];
        var arrayPagos = [];
        var table;
        var arrayProductoCar = [];

        $(document).ready(function() {

            $("#serivicio_id_venta, #documento_sector_siat_id_new_servicio, #actividad_economica_siat_id_new_servicio, #producto_servicio_siat_id_new_servicio, #unidad_medida_siat_id_new_servicio, #facturacion_datos_tipo_metodo_pago, #facturacion_datos_tipo_moneda, #tipo_documento")
                .select2();

            // Inicializa el DataTable
            table = $('#carrito').DataTable({
                lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
                // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
                dom: '<"dt-head row"><"clear">t', // Use dom for basic layout
                language: {
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    emptyTable: 'No hay datos disponibles'
                },
                order: [],
                responsive: true
            });


            let debounceTimer;
            $('.buscar-persona').on('keyup', function() {

                //ajaxListadoClientes();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    ajaxListadoClientes();
                }, 300); // Espera 300 ms antes de ejecutar la función
            });


            $('input[name="uso_cafc"]').on('change', function() {
                verificarRadioSeleccionado();
            });

            $('#nombre_cliente').text('SELECCIONAR CLIENTE')

            $('#monto_apertura').on('focus', function() {
                if ($(this).val() == '0' || $(this).val() === 0) {
                    $(this).val("");
                }
            });

            $('#monto_pagado_recibo').on('focus', function() {
                if ($(this).val() == '0' || $(this).val() === 0) {
                    $(this).val("");
                }
            });
            $('#monto_pagado_recibo_2').on('focus', function() {
                if ($(this).val() == '0' || $(this).val() === 0) {
                    $(this).val("");
                }
            });
        });

        function ajaxListadoServicios() {
            let datos = {}
            $.ajax({
                url: "{{ url('factura/ajaxListadoServicios') }}",
                method: "POST",
                data: datos,
                success: function(data) {
                    if (data.estado === 'success') {
                        $('#tabla_clientes').html(data.listado)
                    } else {

                    }
                }
            })
        }

        function identificaSericio(selected) {

            if (selected.value != '') {
                var json = JSON.parse(selected.value);

                console.log(json);

                let cantidad_venta = 1;
                let precio_venta = json.precio_venta;
                let stock = json.stock;

                $('#cantidad_venta').val(cantidad_venta);
                $('#precio_venta').val((cantidad_venta * precio_venta));
                $('#total_venta').val(precio_venta * cantidad_venta);
                $('#stock_sucursal').val(stock);

                if (parseInt(stock) > 0) {
                    $("#stock_sucursal").addClass("is-valid").removeClass("is-invalid");
                    $('#boton-agrega-producto').attr('disabled', false)
                } else {
                    $("#stock_sucursal").addClass("is-invalid").removeClass("is-valid");
                    $('#boton-agrega-producto').attr('disabled', true)
                }

                if(json.tipo == 'SERVICIO'){
                    $('#div_control_stock').removeClass('d-block');
                    $('#div_control_stock').addClass('d-none');
                }else{
                    $('#div_control_stock').removeClass('d-none');
                    $('#div_control_stock').addClass('d-block');
                }

            }
        }

        function agregarProducto() {

            if ($("#formulario_venta")[0].checkValidity()) {

                var servicioDatos = JSON.parse($("#serivicio_id_venta").val());

                console.log(servicioDatos);

                let id                    = servicioDatos.id;
                var filaExistente         = table.row("#producto-" + id);
                var precio                = parseFloat($('#precio_venta').val()).toFixed(2);
                var cantidad              = parseFloat($('#cantidad_venta').val());
                var total                 = parseFloat(precio * cantidad).toFixed(2);
                var subTotal              = (precio * cantidad) - 0;
                var descripcion_adicional = $('#descripcion_adicional').val();
                var monto_total           = $('#monto_total').val();
                var codigo_imei           = $("#codigo_imei").val();

                let servicio = {
                    servicio_id: servicioDatos.id,
                    descripcion: servicioDatos.nombre,
                    tipo: servicioDatos.tipo,
                    precio: parseFloat(precio).toFixed(2),
                    numero_imei: codigo_imei,
                    empresa_id: servicioDatos.empresa_id,
                    cantidad: parseFloat(cantidad),
                    total: parseFloat(total).toFixed(2),
                    descuento: parseFloat(0).toFixed(2),
                    subTotal: parseFloat(subTotal.toFixed(2)),
                    descripcion_adicional: descripcion_adicional,
                }

                if (filaExistente.node()) {

                    // // Si el producto ya está en el carrito, aumenta la cantidad en 2
                    // var cantidadCell = $(filaExistente.node()).find('.cantidad');
                    // var cantidadActual = parseFloat(cantidadCell.text());
                    // var nuevaCantidad = cantidadActual + parseFloat(cantidad);
                    // cantidadCell.text(nuevaCantidad);

                    // // Actualiza el total
                    // nuevoTotal = nuevaCantidad * precio
                    // var totalCell = $(filaExistente.node()).find('.total');
                    // totalCell.text((nuevoTotal).toFixed(2));

                    // var subTotalCell = $(filaExistente.node()).find('.subTotal');
                    // var valorSubTotal = parseFloat(subTotalCell.text())
                    // var nuevoSubTotal = nuevoTotal - parseFloat($('#descuento_' + id).val())
                    // subTotalCell.text((nuevoSubTotal).toFixed(2));

                    // let servicio = arrayProductoCar.find(s => s.servicio_id === servicioDatos.id);
                    // if (servicio) {

                    //     servicio.cantidad = parseFloat(servicio.cantidad) + parseFloat(cantidad);
                    //     servicio.total = parseFloat(nuevoTotal);
                    //     servicio.subTotal = parseFloat(nuevoSubTotal);
                    //     servicio.descripcion_adicional = $('#descripcion_adicional').val();

                    //     let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    //     let descuentoAdicional = $('#descuento_adicional').val()

                    //     $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) -
                    //         parseFloat(descuentoAdicional))
                    //     $('#monto_gift_card').attr('max', parseFloat(sumaTotal) - parseFloat(descuentoAdicional));

                    // } else {
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: "ERROR!",
                    //         text: "Error al actualizar el descuento.",
                    //         timer: 4000
                    //     })
                    // }

                } else {
                    var subTotal = (precio * cantidad).toFixed(2);

                    let btnEliminar = `<button class='eliminar btn btn-icon btn-danger btn-circle btn-sm'
                                            title='Eliminar del carro'
                                            onclick='eliminarItem(${id})'>
                                                <i class='fa fa-trash'></i>
                                        </button>`;


                    table.row.add([
                        servicioDatos.nombre + " " + descripcion_adicional,
                        precio,
                        "<span class='cantidad'>" + cantidad + "</span>",
                        "<span class='total'>" + total + "</span>",
                        '<input class="form-control form-control-sm" type="text" name="descuento_' + id +
                        '" id="descuento_' + id + '" value="0" onchange="ejecutarDescuento(this)">',
                        "<span class='subTotal'>" + subTotal + "</span>",
                        btnEliminar
                    ]).node().id = 'producto-' + id;
                    table.draw(false);

                    // AGREGAMOS AL CARRO LOS PRODUSTOS
                    arrayProductoCar.push(servicio);
                    // AGREGAMOS AL CARRO LOS PRODUSTOS

                    var monto_total_r = parseFloat(monto_total) + parseFloat(servicio.subTotal);

                    $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(monto_total_r.toFixed(2));

                    //VALIDAMOS QUE EL MONTO DE GITCAD NO SOBREPASE EL MONTO DEL PRODUCTO
                    $('#monto_gift_card').attr('max', monto_total_r);

                    $('#cantidad_venta').val(0)
                    $('#precio_venta').val(0)
                    $('#total_venta').val(0)
                    $('#stock_sucursal').val(0)

                    $("#stock_sucursal").removeClass("is-invalid is-valid");

                }

                // BORRAMOS LOS ITEM QUE AGREGAMOS
                $('#serivicio_id_venta').val(null).trigger('change');
                $('#tabla_detalles').show('toggle')
                $('#bloque_seleccionar_cliente').show('toggle')
                $("#cantidad_stock").val(0)


                // PARA LOS DATOS DEL PAGO
                $('#bloque_recibo').show('toggle');
                $('#bloqueDatosFactura').hide('toggle');
                $('#bloque_facturacion').hide('toggle');

            } else {
                $("#formulario_venta")[0].reportValidity();
            }

        }

        function ejecutarDescuento(valor) {

            let valorDescuento = valor.value;
            let valorId = valor.id;
            let id = valorId.split("_")[1]
            var filaExistente = table.row("#producto-" + id);

            if (filaExistente.node()) {
                var totalCell = $(filaExistente.node()).find('.total');
                var valorTotal = parseFloat(totalCell.text());
                var subTotalCell = $(filaExistente.node()).find('.subTotal');

                if (parseFloat(valorDescuento) >= 0 && parseFloat(valorDescuento) <= 100) {
                // if (parseFloat(valorDescuento) > -1) {

                    let montoDescuento = (valorTotal * valorDescuento) / 100;
                    let nuevoSubTotal = valorTotal - montoDescuento;

                    // if (valorDescuento < valorTotal) {
                        // subTotalCell.text((valorTotal - valorDescuento).toFixed(2));
                        subTotalCell.text((nuevoSubTotal).toFixed(2));
                        let servicio = arrayProductoCar.find(s => s.servicio_id === parseInt(id));
                        if (servicio) {
                            servicio.descuento = parseFloat(montoDescuento);
                            servicio.subTotal = parseFloat(servicio.total) - parseFloat(montoDescuento);

                            // EJECUTAMOS EL DESCUENTO
                            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                            let descuentoAdicional = $('#descuento_adicional').val()
                            $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) - parseFloat(
                                descuentoAdicional))
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "ERROR!",
                                text: "Error al actualizar el descuento",
                                timer: 4000
                            })
                        }

                    // } else {
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: "ERROR!",
                    //         text: "El valor de descuento no debe ser mayor al valor Total",
                    //         timer: 4000
                    //     })
                    //     $('#descuento_' + id).val(valorTotal - parseFloat(subTotalCell.text()))
                    // }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "ERROR!",
                        text: "El valor de descuento debe ser mayor a 0!",
                        timer: 4000
                    })
                    $('#descuento_' + id).val(valorTotal - parseFloat(subTotalCell.text()))
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "ERROR!",
                    text: "Servicion no encontrado",
                    timer: 4000
                })
            }
        }

        function ajaxListadoClientes() {

            // if (
            //     $('#nit_escogido').val().length > 3 ||
            //     $('#nombre_escogido').val().length >= 3 ||
            //     $('#ap_paterno_escogido').val().length > 3 ||
            //     $('#ap_materno_escogido').val().length > 3
            // ) {
            let datos = $('#formulario_cliente_escogido').serializeArray();
            $.ajax({
                url: "{{ url('factura/ajaxListadoClientesBusqueda') }}",
                method: "POST",
                data: datos,
                success: function(data) {
                    if (data.estado) {
                        if (data.data.cantidad > 0)
                            $('#tabla-clientes-buscados').show('toogle')

                        $('#tabla-clientes-buscados').html(data.data.listado)
                    } else {

                    }
                }
            })
            // }
        }

        function mostraBloqueMasDatosProdcuto() {

            $('#bloque_mas_datos_productos').toggle('show')
        }

        function escogerCliente(cliente, nombres, ap_paterno, ap_materno, cedula, nit, razon_social) {

            $('#cliente_id_escogido').val(cliente);

            $('#nombre_escogido').val('');
            $('#ap_paterno_escogido').val('');
            $('#ap_materno_escogido ').val('');
            $('#cedula_escogido').val('');

            $('#nit_factura').val(nit);
            $('#razon_factura').val(razon_social);

            $('#tabla-clientes-buscados').hide('toggle')

            let nombreusuario = "CLIENTE ESCOGIDO: " + cedula + " | " + nombres + " | " + ap_paterno + " | " + ap_materno;

            $('#nombre_cliente').text(nombreusuario)

            $('#formulario_cliente_escogido').toggle('hide');

            $('#bloque-botones-emisiones').show('toggle');

            // $('#bloque_recibo').hide('toggle');
            // $('#bloqueDatosFactura').hide('toggle');
            // $('#bloque_facturacion').hide('toggle');
        }

        function escogerVentaTipo(tipo) {

            if (tipo === 'RECIBO') {
                $('#bloque_recibo').show('toggle');
                $('#bloqueDatosFactura').hide('toggle');
                $('#bloque_facturacion').hide('toggle');
            } else {
                $('#bloqueDatosFactura').show('toggle');
                $('#bloque_facturacion').show('toggle');
                $('#bloque_recibo').hide('toggle');
            }

        }

        function mostrarFormularioClientes() {
            $('#formulario_cliente_escogido').toggle('show');
            // $('#tabla_detalles').hide('toggle');
        }

        function muestraDatosFactura() {

            $('#bloqueDatosFactura').show('toogle')

        }

        function ejecutarDescuentoAdicional() {

            let descuentoAdcional = parseFloat($('#descuento_adicional').val())
            let montoTotal = parseFloat($('#monto_total').val())

            if (descuentoAdcional > -1) {
                if (descuentoAdcional < montoTotal) {
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = $('#descuento_adicional').val();
                    $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        text: 'El descuento Adicional no debe ser mayor al monto total!',
                    })
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = 0;
                    $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                    $('#descuento_adicional').val(0);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Error",
                    text: 'El descuento debe ser mayor a 0!',
                })
                let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                let descuentoAdicional = 0;
                $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                $('#descuento_adicional').val(0);
            }
        }

        function bloqueCAFC() {
            if ($('#tipo_facturacion').val() === "offline") {

                let tipo_documento = $('#tipo_documento').val();
                let emision = $('#tipo_facturacion').val();

                verificarExcepcion(tipo_documento, emision);

            } else {
                $('#numero_factura_cafc').val(null)
                $('#bloque_cafc, #numero_fac_cafc').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#select_cufd_vigentes').html('')
                $('#bloque_cufd_offline').hide('toggle');

                // Marcar el radio button con value="No" usando name
                $('input[name="uso_cafc"][value="No"]').prop('checked', true);
            }
        }

        function verificarRadioSeleccionado() {
            var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();
            if (valorSeleccionado === 'No') {

                $('#numero_fac_cafc').hide('toggle');
                $('#numero_factura_cafc').val(0)

            } else if (valorSeleccionado === 'Si') {
                $.ajax({
                    url: "{{ url('factura/sacaNumeroCafcUltimo') }}",
                    method: "POST",
                    dataType: 'json',
                    success: function(data) {
                        if (data.estado) {
                            $("#numero_factura_cafc").val(data.data.numero);
                            $('#numero_fac_cafc').show('toggle');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "Algo fallo"
                            })
                        }
                    }
                })
            }
        }

        function eliminarItem(id) {

            var fila = table.row("#producto-" + id);
            // var cantidadCell = $(fila.node()).find('.cantidad');
            // var cantidadActual = parseInt(cantidadCell.text());

            // Reducir la cantidad en 1
            // var nuevaCantidad = cantidadActual - 1;
            // cantidadCell.text(nuevaCantidad);

            // if (nuevaCantidad <= 0) {
            //     // Si la cantidad es 0 o menos, elimina la fila de la tabla
            table.row(fila).remove().draw(false);

            // Elimina el producto del array
            arrayProductoCar = arrayProductoCar.filter(s => s.servicio_id !== id);
            // } else {
            //     // Si la cantidad sigue siendo mayor que 0, actualiza el total y el subTotal
            //     var precio = parseFloat($(fila.node()).find('.total').text()) / cantidadActual;
            //     var nuevoTotal = nuevaCantidad * precio;
            //     $(fila.node()).find('.total').text(nuevoTotal.toFixed(2));

            //     var subTotalCell = $(fila.node()).find('.subTotal');
            //     var descuento = parseFloat($('#descuento_' + id).val());
            //     var nuevoSubTotal = nuevoTotal - descuento;
            //     subTotalCell.text(nuevoSubTotal.toFixed(2));

            //     // Actualiza los valores en el array
            //     let servicio = arrayProductoCar.find(s => s.servicio_id === id);
            //     if (servicio) {
            //         servicio.cantidad = nuevaCantidad;
            //         servicio.total = nuevoTotal;
            //         servicio.subTotal = nuevoSubTotal;
            //     }
            // }

            // Actualizar el monto total
            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
            let descuentoAdicional = $('#descuento_adicional').val();
            $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional));

        }

        function mostrarCarritoVentas() {
            $('#tabla_detalles').toggle('show')
        }

        function calcularPrecioTotal() {
            let precio = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            let total = parseFloat(precio) * parseFloat(cantidad);

            // CALCULAMOS LAS CANTIDAD DE CAJAS Y PIEZAS
            let equivalente_unidadM2 = parseFloat($('#equivalente_unidad').val());
            let cantidad_por_caja = parseFloat($('#cantidad_por_caja').val());

            let cantidadTotalPiezas = parseFloat(cantidad) / equivalente_unidadM2;
            let cantidadTotalCajas = cantidadTotalPiezas / cantidad_por_caja;
            let cantidadTotalPiezasSueltas = cantidadTotalPiezas % cantidad_por_caja;

            if (Math.round(cantidadTotalPiezasSueltas) == cantidad_por_caja) {
                $('#nro_cajas').val(Math.floor(cantidadTotalCajas) + 1);
                $('#nro_piezas').val(0);
            } else {
                $('#nro_cajas').val(Math.floor(cantidadTotalCajas));
                $('#nro_piezas').val(cantidadTotalPiezasSueltas);
            }

            $('#total_venta').val(total.toFixed(2))
        }

        function modalAgregarCliente() {
            $('#modal_new_cliente').modal('show');
        }

        function guardarClienteEmpresa() {
            if ($("#formulario_new_cliente")[0].checkValidity()) {
                let datos = {
                    id            : 0,
                    nombres       : $('#nombres_cliente_new_usuaio_empresa').val(),
                    ap_paterno    : $('#ap_paterno_cliente_new_usuaio_empresa').val(),
                    ap_materno    : $('#ap_materno_cliente_new_usuaio_empresa').val(),
                    cedula        : $('#cedula_cliente_new_usuaio_empresa').val(),
                    complemento   : $('#complemento_cliente_new_usuaio_empresa').val(),
                    nit           : $('#nit_cliente_new_usuaio_empresa').val(),
                    razon_social  : $('#razon_social_cliente_new_usuaio_empresa').val(),
                    correo        : $('#correo_cliente_new_usuaio_empresa').val(),
                    numero_celular: $('#num_ceular_cliente_new_usuaio_empresa').val()
                };
                $.ajax({
                    url: "{{ url('cliente/guardarCliente') }}",
                    method: "POST",
                    data: datos,
                    success: function(data) {
                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE REGISTRO CON EXITO",
                            })

                            console.log(data.data.cliente);

                            $('#cliente_id_escogido').val(data.data.cliente.id);

                            let cedula = $('#cedula_cliente_new_usuaio_empresa').val();
                            let nombres = $('#nombres_cliente_new_usuaio_empresa').val();
                            let ap_paterno = $('#ap_paterno_cliente_new_usuaio_empresa').val();
                            let ap_materno = $('#ap_materno_cliente_new_usuaio_empresa').val();

                            let nombreusuario = cedula + " | " + nombres + " | " + ap_paterno + " | " +
                                ap_materno;
                            $('#nombre_cliente').text(nombreusuario)

                            $('#nit_factura').val($('#nit_cliente_new_usuaio_empresa').val());
                            $('#razon_factura').val($('#razon_social_cliente_new_usuaio_empresa').val());

                            $('#bloqueDatosFactura, #bloque_facturacion, #bloque-botones-emisiones').show('toggle');

                            //ajaxListado();
                        } else if (data.estado === 'error') {
                            Swal.fire({
                                icon: 'warning',
                                title: "ALTO!",
                                text: data.text,
                            })
                        } else {

                        }
                        $('#modal_new_cliente').modal('hide');
                    },
                    error: function(xhr) {

                        limpiarErorres();

                        if (xhr.status === 422) {

                            let errores = xhr.responseJSON.errors;

                            // Relacionamos el campo de Laravel con el ID del input
                            let campos = {
                                nombres: '#nombres_cliente_new_usuaio_empresa',
                                ap_paterno: '#ap_paterno_cliente_new_usuaio_empresa',
                                ap_materno: '#ap_materno_cliente_new_usuaio_empresa',
                                cedula: '#cedula_cliente_new_usuaio_empresa',
                                complemento: '#complemento_cliente_new_usuaio_empresa',
                                nit: '#nit_cliente_new_usuaio_empresa',
                                razon_social: '#razon_social_cliente_new_usuaio_empresa',
                                correo: '#correo_cliente_new_usuaio_empresa',
                                numero_celular: '#num_ceular_cliente_new_usuaio_empresa'
                            };

                            for (let campo in errores) {

                                let mensaje = errores[campo][0];

                                console.log(campo, " <+> ", mensaje);

                                // Buscamos el input correspondiente
                                let input = $(campos[campo]);

                                if (input.length) {

                                    input.addClass('is-invalid');

                                    // Evitamos duplicar mensajes
                                    input.next('.invalid-feedback').remove();

                                    input.after(`
                                        <div class="invalid-feedback">
                                            ${mensaje}
                                        </div>
                                    `);
                                }
                            }

                        } else {

                            console.log(xhr.responseText);

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',
                            });
                        }
                    }
                })
            } else {
                $("#formulario_new_cliente")[0].reportValidity();
            }
        }

        function limpiarErorres(){
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalAgregarProducto() {
            $('#modal_new_servicio').modal('show');
        }

        function guardarNewServioEmpresa() {
            if ($("#formulario_new_servicio")[0].checkValidity()) {
                // let datos = $('#formulario_new_servicio').serializeArray();
                let formData = new FormData($("#formulario_new_servicio")[0]);
                $.ajax({
                    url: "{{ url('empresa/guardarNewServioEmpresaFormularioFacturacion') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.estado === 'success') {


                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE REGISTRO CON EXITO",
                            })

                            let nuevosServicios = data.servicio;

                            var select = $('#serivicio_id_venta');

                            // Vaciar el select
                            select.empty();

                            // Añadir la opción por defecto
                            select.append('<option value="">SELECCIONE</option>');

                            // Volver a llenar el select con las nuevas opciones
                            $.each(nuevosServicios, function(index, servicio) {
                                let d = JSON.stringify(servicio)
                                select.append('<option value=\'' + d + '\'>' + servicio.descripcion +
                                    '</option>');
                            });

                            //ajaxListado();
                        } else if (data.estado === 'error') {
                            Swal.fire({
                                icon: 'warning',
                                title: "ALTO!",
                                text: data.text,
                            })
                        }
                        $('#modal_new_servicio').modal('hide');
                    }
                })
            } else {
                $("#formulario_new_servicio")[0].reportValidity();
            }
        }

        function verificarExcepcion(tipo_documento, emision, uso_cafse) {
            if (emision === "offline") {
                if (tipo_documento == "5") { //VERIFICAMOS QUE SEA NIT
                    $('#execpcion').prop('checked', true);
                } else {
                    $('#execpcion').prop('checked', false);
                }
                $('#bloque_cafc').show('toggle')

                $.ajax({
                    url: "{{ url('eventoSignificativo/sacarCufdsPorTipoEvento') }}",
                    method: "POST",
                    data: {},
                    success: function(data) {
                        if (data.estado) {
                            // REMPLAZAR LOS CUFDS VIGENTES
                            $('#select_cufd_vigentes').html(data.data.select)
                            $('#bloque_cufd_offline').show('toggle');
                        } else {
                            $('#select_cufd_vigentes').html('')
                            $('#bloque_cufd_offline').hide('toggle');
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: data.text,
                            })
                        }
                    }
                })
            }
        }

        {{--

        function filtrarPorActividad(datos) {

            var productosServicios = @JSON($productoServicio);
            var codigo = $(datos).find(':selected').data('codigo');
            var listadoFiltrado = productosServicios.filter(pro => parseInt(pro.codigo_actividad) === codigo)

            //Llenar el segundo <select>
            var selectProducto = $("#producto_servicio_siat_id_new_servicio");
            selectProducto.empty().append('<option></option>'); // Limpiar y agregar opción vacía

            listadoFiltrado.forEach(pro => {
                selectProducto.append(`<option value="${pro.id}">${pro.descripcion_producto}</option>`);
            });

            // Refrescar el select2 si lo usas
            selectProducto.trigger('change');

        }
        --}}

        function modalAperturaCaja() {
            // $('#nombre').val('')
            $('#monto_apertura').val(0)
            $('#descripcion').val('')
            $('#modalAperturaCaja').modal('show')
        }

        function guardarAperturaCaja() {
            if ($('#formularioAperturaCaja')[0].checkValidity()) {
                $('#boton_abrir_caja').attr('disabled', true);
                let datos = $('#formularioAperturaCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarAperturaCaja') }}",
                    method: "POST",
                    data: datos,
                    success: function(resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000, // Se cierra en 3 segundos
                                showConfirmButton: false
                            });

                            location.reload();
                        } else {

                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });
                    }
                });
            } else {
                $('#formularioAperturaCaja')[0].reportValidity()
            }
        }

        function caluclarCambio(select) {

            let monto_total_pagado = parseFloat($('#monto_total_pagado').val())
            let monto_pagado = parseFloat(select.value)

            if (monto_pagado > monto_total_pagado) {
                $('#cambio_pagado').val(monto_pagado - monto_total_pagado)
            } else if (monto_pagado <= monto_total_pagado) {
                $('#cambio_pagado').val(0)
            }

            if (monto_pagado === 0) {
                $('#tipo_pago_pagado').prop('required', false)
                $('#realizo_pago').prop('required', false)
            } else {
                $('#tipo_pago_pagado').prop('required', true)
                $('#realizo_pago').prop('required', true)
            }

        }

        function modalCerrarCaja() {
            $('#modalCerrarCaja').modal('show')
        }

        function guardarCerrarCaja() {
            if ($('#formularioCerrarCaja')[0].checkValidity()) {
                $('#boton_cerrar_caja').attr('disabled', true);
                let datos = $('#formularioCerrarCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarCerrarCaja') }}",
                    method: "POST",
                    data: datos,
                    success: function(resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000, // Se cierra en 3 segundos
                                showConfirmButton: false
                            });

                            location.reload();
                        } else {

                        }
                    },
                    error: function(xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });

                        // limpiarErorres();

                        // if (xhr.status === 422) {
                        //     let errores = xhr.responseJSON.errors;

                        //     for (let campo in errores) {
                        //         let mensaje = errores[campo][0];

                        //         let input = $(`[name="${campo}"]`);
                        //         input.addClass("is-invalid");
                        //         input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        //     }
                        // } else {
                        //     Swal.fire({
                        //         icon: 'error',
                        //         title: 'Error',
                        //         text: 'Ocurrió un error inesperado.',
                        //     });
                        // }
                    }
                });
            } else {
                $()[0].reportValidity();
            }
        }

        function validarCampos() {
            let tipo_pago = $('#tipo_pago_pagado').val()

            if (tipo_pago === 'EFECTIVO' || tipo_pago === 'TRANSFERENCIA' || tipo_pago === 'QR') {
                $('#realizo_pago').prop('required', true);
                $('#monto_pagado').prop('required', true);
                $('#cambio_pagado').prop('required', true);
                $('#realizo_pago').prop('checked', true);
                $('#monto_pagado').attr('min', 1);
            } else {
                $('#realizo_pago').prop('required', false);
                $('#monto_pagado').prop('required', false);
                $('#cambio_pagado').prop('required', false);
                $('#realizo_pago').prop('checked', false);
                $('#monto_pagado').attr('min', 0);
            }
        }

        function validarCamposRecibo() {
            // let tipo_pago = $('#tipo_pago_pagado_recibo').val()
            let tipo_pago = $('#tipo_pago_pagado_recibo option:selected').data('tipo');
            console.log("TIPO:", tipo_pago); // Para verificar

            if (tipo_pago === 'EFECTIVO' || tipo_pago === 'TRANSFERENCIA' || tipo_pago === 'QR') {
                $('#monto_pagado_recibo').prop('required', true);
                $('#cambio_pagado_recibo').prop('required', true);
                $('#monto_pagado_recibo').attr('min', 1);
            } else {
                $('#monto_pagado_recibo').prop('required', false);
                $('#cambio_pagado_recibo').prop('required', false);
                $('#monto_pagado_recibo').attr('min', 0);
            }
        }

        function validarCamposRecibo2() {
            // let tipo_pago = $('#tipo_pago_pagado_recibo').val()
            let tipo_pago = $('#tipo_pago_pagado_recibo_2 option:selected').data('tipo');

            if (tipo_pago === 'EFECTIVO' || tipo_pago === 'TRANSFERENCIA' || tipo_pago === 'QR') {
                $('#monto_pagado_recibo_2').prop('required', true);
                $('#monto_pagado_recibo_2').attr('min', 1);
            } else {
                $('#monto_pagado_recibo_2').prop('required', false);
                $('#monto_pagado_recibo_2').attr('min', 0);
            }
        }

        function caluclarCambioRecibo(select) {

            let monto_total_pagado = parseFloat($('#monto_total_pagado_recibo').val());
            let monto_pagado = parseFloat(select.value);
            let monto2 = parseFloat($('#monto_pagado_recibo_2').val()) || 0;
            monto_pagado = monto_pagado + monto2;

            if (monto_pagado > monto_total_pagado) {
                let final = monto_pagado - monto_total_pagado;
                $('#cambio_pagado_recibo').val(final.toFixed(2))
            } else if (monto_pagado <= monto_total_pagado) {
                $('#cambio_pagado_recibo').val(0)
            }

            if (monto_pagado === 0) {
                $('#tipo_pago_pagado_recibo').prop('required', false)
            } else {
                $('#tipo_pago_pagado_recibo').prop('required', true)
            }
        }

        function caluclarCambioRecibo2(select) {

            let monto_total_pagado = parseFloat($('#monto_total_pagado_recibo').val());
            let monto_pagado = parseFloat(select.value);
            let monto2 = parseFloat($('#monto_pagado_recibo').val()) || 0;
            monto_pagado = monto_pagado + monto2;

            if (monto_pagado > monto_total_pagado) {
                let final = monto_pagado - monto_total_pagado;
                $('#cambio_pagado_recibo').val(final.toFixed(2))
            } else if (monto_pagado <= monto_total_pagado) {
                $('#cambio_pagado_recibo').val(0)
            }

            if (monto_pagado === 0) {
                $('#tipo_pago_pagado_recibo_2').prop('required', false)
            } else {
                $('#tipo_pago_pagado_recibo_2').prop('required', true)
            }
        }

        function emitirRecibo() {
            if ($("#formularioGeneraRecibo")[0].checkValidity()) {

                if (arrayProductoCar.length > 0) {

                    if(
                        $('#cliente_id_escogido').val() != null &&
                        $('#cliente_id_escogido').val() != ''
                    ){

                        // Obtén el botón y el icono de carga
                        var boton = $("#boton_enviar_recibo");
                        var iconoCarga = boton.find("i");
                        // Deshabilita el botón y muestra el icono de carga
                        boton.attr("disabled", true);
                        iconoCarga.show();

                        $.ajax({
                            url: "{{ url('factura/emitirRecibo') }}",
                            method: "POST",
                            data: {
                                cliente_id         : $('#cliente_id_escogido').val(),
                                mascota_id         : $('#mascota_id').val(),
                                carrito            : arrayProductoCar,
                                nit_factura        : $('#nit_factura').val(),
                                razon_factura      : $('#razon_factura').val(),
                                descuento_adicional: $('#descuento_adicional').val(),
                                monto_total        : $('#monto_total').val(),
                                tipo_pago_pagado   : $('#tipo_pago_pagado_recibo').val(),
                                monto_total_pagado : $('#monto_total_pagado_recibo').val(),
                                monto_pagado       : $('#monto_pagado_recibo').val(),
                                tipo_pago_pagado_2 : $('#tipo_pago_pagado_recibo_2').val(),
                                monto_pagado_2     : $('#monto_pagado_recibo_2').val(),
                                cambio_pagado      : $('#cambio_pagado_recibo').val(),
                                descripcion        : $('#descripcion_adicional_total').val(),
                                tipo_atencion      : $('#tipo_atencion').val(),
                                hora_llegada       : $('#hora_llegada').val(),
                                hora_entrega       : $('#hora_entrega').val(),
                            },
                            success: function(data) {
                                if (data.estado) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Excelente!',
                                        text: 'EL TICKED FUE VALIDADA',
                                        timer: 3000
                                    })
                                    if (data.data.numero != null && data.data.numero != '') {
                                        window.open("{{ url('factura/imprimeReciboRollo') }}/" + data.data.numero,
                                            "_blank", "width=800,height=600");
                                        window.location.reload();
                                    } else {
                                        window.location.href = "{{ url('factura/listado') }}"
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: JSON.stringify(data),
                                        text: 'EL RECIBO RECHAZADA',
                                    })
                                    // Habilita el botón y oculta el icono de carga después de completar
                                    boton.attr("disabled", false);
                                    iconoCarga.hide();
                                }
                            },
                            error: function(error) {

                            }
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: "Debe seleccionar un cliente!",
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        })
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Debe tener al menos un producto agregado al carrito!",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    })
                }
            } else {
                $("#formularioGeneraRecibo")[0].reportValidity();
            }
        }

        function calcularCajasPiezas() {
            let nro_cajas = parseFloat($('#nro_cajas').val());
            let nro_piezas = parseFloat($('#nro_piezas').val());
            let cantidad_por_caja = parseFloat($('#cantidad_por_caja').val());
            let equivalente_unidad = parseFloat($('#equivalente_unidad').val());

            if (nro_piezas == cantidad_por_caja) {
                nro_piezas = 0;
                nro_cajas = nro_cajas + 1
                $('#nro_piezas').val(nro_piezas);
                $('#nro_cajas').val(nro_cajas);
            } else if (nro_piezas > cantidad_por_caja) {

                let calcula_nro_cajas = Math.floor(nro_piezas / cantidad_por_caja);
                let calcula_nro_piezas_sobrantes = nro_piezas % cantidad_por_caja;

                nro_piezas = calcula_nro_piezas_sobrantes;
                nro_cajas = nro_cajas + calcula_nro_cajas

                $('#nro_piezas').val(nro_piezas);
                $('#nro_cajas').val(nro_cajas);

            }

            let cantidad_piezas_totales = (cantidad_por_caja * nro_cajas) + nro_piezas;
            let cantidad_metro_cuadrado = cantidad_piezas_totales * equivalente_unidad;

            $('#cantidad_venta').val(cantidad_metro_cuadrado.toFixed(2));

            // PARA CALCULAR EL PRECIO
            let precio = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            let total = parseFloat(precio) * parseFloat(cantidad);
            $('#total_venta').val(total.toFixed(2))

        }

    </script>
@endsection
