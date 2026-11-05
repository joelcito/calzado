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

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalVerCaja" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div id="contenidoCaja"></div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalEdicionCaja" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light-info" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE EDICION DE CAJA</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioEdicionCaja">
                        <input type="hidden" id="caja_editar_id" name="caja_editar_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">usuario Apertura</label>
                                    <select name="usuario_apertura_id" id="usuario_apertura_id"
                                        class="form-select form-select-sm">
                                        @foreach ($usuarios as $usuario)
                                        <option value="{{$usuario->id}}">{{ $usuario->nombres." ".$usuario->ap_paterno."
                                            ".$usuario->ap_materno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion apertura</label>
                                    <input type="text" class="form-control form-control-sm" id="descripcion_apertura"
                                        name="descripcion_apertura">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto Apertura</label>
                                    <input type="number" class="form-control form-control-sm" id="monto_apertura_edicion"
                                        name="monto_apertura_edicion" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">usuario Cierre</label>
                                    <select name="usuario_cierre_id_edicion" id="usuario_cierre_id_edicion"
                                        class="form-select form-select-sm">
                                        @foreach ($usuarios as $usuario)
                                        <option value="{{$usuario->id}}">{{ $usuario->nombres." ".$usuario->ap_paterno."
                                            ".$usuario->ap_materno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion Cierre</label>
                                    <input type="text" class="form-control form-control-sm" id="descripcion_cierre_edicion"
                                        name="descripcion_cierre_edicion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto Cierre</label>
                                    <input type="number" class="form-control form-control-sm" id="monto_cierre_edicion"
                                        name="monto_cierre_edicion" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Venta Total</label>
                                    <input type="number" class="form-control form-control-sm" id="venta_total"
                                        name="venta_total" min="1" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Venta Efectivo</label>
                                    <input type="text" class="form-control form-control-sm" id="venta_efectivo"
                                        name="venta_efectivo" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Venta Transferecina</label>
                                    <input type="text" class="form-control form-control-sm" id="venta_transferencia"
                                        name="venta_transferencia" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Venta Qr</label>
                                    <input type="text" class="form-control form-control-sm" id="venta_qr" name="venta_qr"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Total Salida</label>
                                    <input type="number" class="form-control form-control-sm" id="total_salida"
                                        name="total_salida" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Estado</label>
                                    <select class="form-select form-select-sm" id="estado_caja" name="estado_caja">
                                        <option value="Abierta">Abierta</option>
                                        <option value="Cerrado">Cerrado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" id="boton_abrir_caja"
                                onclick="guardarEdicionCaja()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
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
                        <h3
                            class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            LISTADO DE CAJAS</h3>
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

            $('#monto_apertura').on('focus', function() {
                if ($(this).val() == '0' || $(this).val() === 0) {
                    $(this).val("");
                }
            });
        });

        function ajaxListado() {

            let datos = {};
            $.ajax({
                url: "{{ url('caja/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }
                }
            })
        }

        function modalAperturaCaja() {
            $('#monto_apertura').val(0);
            $('#descripcion').val('')
            $('#modalAperturaCaja').modal('show')
        }

        function guardarAperturaCaja() {
            if($('#formularioAperturaCaja')[0].checkValidity()){
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
            }else{
                $('#formularioAperturaCaja')[0].reportValidity()
            }
        }

        function modalCerrarCaja() {
            $('#modalCerrarCaja').modal('show')
        }

        function guardarCerrarCaja() {
            if($('#formularioCerrarCaja')[0].checkValidity()){
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
            }else{
                $('#formularioCerrarCaja')[0].reportValidity()
            }
        }

        // function limpiarErorres(){
        //     $(".invalid-feedback").remove();
        //     $(".is-invalid").removeClass("is-invalid");
        // }

        // function modalNuevoRol(){
        //     limpiarErorres();

        //     $('#id').val(0)
        //     $('#nombre').val('')
        //     $('#modalRol').modal('show')
        // }

        // function guardarRol(){
        //     let datos = $('#formularioRol').serializeArray();
        //     $.ajax({
        //         url: "{{ route('rol.guardarRol') }}",
        //         method: "POST",
        //         data: datos,
        //         success: function (resultado) {
        //             if(resultado.estado){
        //                 Swal.fire({
        //                     title: "EL REGISTRO FUE EXITOSO.",
        //                     icon: "success",
        //                     timer: 3000, // Se cierra en 3 segundos
        //                     showConfirmButton: false
        //                 });
        //                 ajaxListado();
        //                 $('#modalRol').modal('hide')
        //             }else{

        //             }
        //         },
        //         error: function (xhr) {
        //             limpiarErorres();

        //             if (xhr.status === 422) {
        //                 let errores = xhr.responseJSON.errors;

        //                 for (let campo in errores) {
        //                     let mensaje = errores[campo][0];

        //                     let input = $(`[name="${campo}"]`);
        //                     input.addClass("is-invalid");
        //                     input.after(`<div class="invalid-feedback">${mensaje}</div>`);
        //                 }
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Error',
        //                     text: 'Ocurrió un error inesperado.',
        //                 });
        //             }
        //         }
        //     });
        // }

        // function editarRol(rol){
        //     limpiarErorres();

        //     Object.keys(rol).forEach(key => {
        //         let input = $(`#${key}`);
        //         if (input.length) {
        //             input.val(rol[key]);
        //         }
        //     });
        //     $('#modalRol').modal('show')
        // }

        function editarCaja(caja){

            $('#usuario_apertura_id').val(caja.usuario_apertura_id);
            $('#descripcion_apertura').val(caja.descripcion);
            $('#monto_apertura_edicion').val(caja.monto_apertura);
            $('#usuario_cierre_id_edicion').val(caja.usuario_cierre_id);
            $('#descripcion_cierre_edicion').val(caja.descripcion_cierre);
            $('#monto_cierre_edicion').val(caja.monto_cierre);
            $('#venta_total').val(caja.total_venta);
            $('#venta_efectivo').val(caja.venta_contado);
            $('#venta_transferencia').val(caja.total_transferencia);
            $('#venta_qr').val(caja.total_qr);
            $('#total_salida').val(caja.total_salida);
            $('#estado_caja').val(caja.estado);
            $('#caja_editar_id').val(caja.id);

            $('#modalEdicionCaja').modal('show')
        }

        function guardarEdicionCaja(){
            if($('#formularioEdicionCaja')[0].checkValidity()){
                $('#boton_cerrar_caja').attr('disabled', true);
                let datos = $('#formularioEdicionCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/formularioEdicionCaja') }}",
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resultado.data,
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
            }else{
                $('#formularioEdicionCaja')[0].reportValidity()
            }
        }

        function verCaja(caja){
            $.ajax({
                url: "{{ url('caja/verCaja') }}",
                method: "POST",
                data: {caja:caja},
                success: function(resultado) {
                    if (resultado.estado) {

                        $('#contenidoCaja').html(resultado.data.listado)
                        $('#modalVerCaja').modal('show')

                        // Swal.fire({
                        //     title: "EL REGISTRO FUE EXITOSO.",
                        //     icon: "success",
                        //     timer: 3000, // Se cierra en 3 segundos
                        //     showConfirmButton: false
                        // });

                        // location.reload();
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
        }
    </script>
@endsection
