@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton{
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalDeuda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE CUENTA POR COBRAR <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y" id="formulario_deuda">
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" id="botonGuardarPagoDeuda" onclick="guardarDeuda()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

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

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <h3 class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">CUENTAS POR COBRAR</h3>
                        <!--begin::Actions-->
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

                    <div class="card-body py-4">
                        <form id="formulario-busqueda-factura">
                            <div class="row">
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">Sucursal</label>
                                    <select class="form-control form-control-sm" name="sucursal_id" id="sucursal_id">
                                        <option value="">Seleccione</option>
                                        @foreach ( $sucursales as $sucursal)
                                            <option value="{{$sucursal->id}}">{{$sucursal->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">Vendedor</label>
                                     <select class="form-control form-control-sm" name="vendedor_id" id="vendedor_id">
                                        <option value="">Seleccione</option>
                                        @foreach ( $usuarios as $usuario)
                                            <option value="{{$usuario->id}}">{{$usuario->nombres.' '.$usuario->ap_paterno.' '.$usuario->ap_materno}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="fw-semibold fs-6 mb-2">C. Nombres</label>
                                            <input type="text" class="form-control form-control-sm" name="buscar_nombre_cliente" id="buscar_nombre_cliente">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-semibold fs-6 mb-2">C. Ap. Paterno</label>
                                            <input type="text" class="form-control form-control-sm" name="buscar_ap_paterno_cliente" id="buscar_ap_paterno_cliente">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-semibold fs-6 mb-2">C. Ap. Materno</label>
                                            <input type="text" class="form-control form-control-sm" name="buscar_ap_materno_cliente" id="buscar_ap_materno_cliente">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">Celuar</label>
                                    <input type="number" class="form-control form-control-sm" name="buscar_numero_celular" id="buscar_numero_celular">
                                </div>
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">C. Cedula</label>
                                    <input type="number" class="form-control form-control-sm" name="buscar_nro_cedula"
                                        id="buscar_nro_cedula">
                                </div>
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio"
                                        id="buscar_fecha_inicio" value="">
                                </div>
                                <div class="col-md-1">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin"
                                        id="buscar_fecha_fin" value="">
                                </div>
                                <div class="col-md-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" id="botom_genera_buscar"
                                                class="btn btn-success btn-sm w-100 mt-8 btn-icon"
                                                onclick="ajaxListado()"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
        });

        function ajaxListado(){
            let datos = $('#formulario-busqueda-factura').serializeArray();
            $.ajax({
                url: "{{ route('pago.ajaxListadoDeuda') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if(resultado.estado){
                        $('#table_listado').html(resultado.data.listado)
                    }else{

                    }
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    // Swal.close();
                }
            })
        }

        function limpiarErorres(){
            $('.error-message').html('');
            $('.is-invalid').removeClass('is-invalid');
        }

        //FORMULARIO DEUDAS
        function registrarPago(factura){
            $('#formulario_deuda').html('');
            limpiarErorres();

            datos = {factura_id: factura.id}
            $.ajax({
                url: "{{ route('pago.ajaxFormPagoDeuda') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if(resultado.estado){
                        $('#formulario_deuda').html(resultado.data.formulario)
                        $('#modalDeuda').modal('show')
                    }
                }
            });
        }

        function guardarDeuda(){
            let datos = $('#formularioDeuda').serializeArray();
            $('#botonGuardarPagoDeuda').attr('disabled', true);
            $.ajax({
                url: "{{ route('pago.guardarPagoDeuda') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if(resultado.estado){
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 2000, // Se cierra en 2 segundos
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalDeuda').modal('hide')
                        $('#botonGuardarPagoDeuda').attr('disabled', false);
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: JSON.stringify(resultado.data),
                        });
                    }
                },
                error: function (xhr) {
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

                    $('#botonGuardarPagoDeuda').attr('disabled', false);
                }
            });
        }

        function modalCerrarCaja() {
            $('#modalCerrarCaja').modal('show')
        }

        function modalAperturaCaja() {
            // $('#nombre').val('')
            $('#monto_apertura').val(0)
            $('#descripcion').val('')
            $('#modalAperturaCaja').modal('show')
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
                    }
                });
            } else {
                $('#formularioCerrarCaja')[0].reportValidity()
            }
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

        function eliminarPago(pago){

            Swal.fire({
                title: "Esta seguro de eliminar el pago?",
                text: "No podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {

                    let datos = {pago:pago};
                    $.ajax({
                        url: "{{ url('pago/eliminarPago') }}",
                        method: "POST",
                        data: datos,
                        success: function(resultado) {
                            if (resultado.estado) {

                                $('#formulario_deuda').html("")

                                Swal.fire({
                                    title: "EL REGISTRO FUE ELIMINADO.",
                                    icon: "success",
                                    timer: 3000, // Se cierra en 3 segundos
                                    showConfirmButton: false
                                });

                                $('#formulario_deuda').html(resultado.data.formulario)

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error inesperado.' + xhr,
                                });
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
                }
            });
        }

   </script>
@endsection
