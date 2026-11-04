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
    <div class="modal fade" id="modalSucusal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE ROL <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioSucursal">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                    <div class="text-danger error-message" id="error-nombre"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Codigo Sucursal</label>
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        id="codigo_sucursal" name="codigo_sucursal">
                                    <div class="text-danger error-message" id="error-codigo_sucursal"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Direccion</label>
                                    <input type="text" class="form-control form-control-sm" id="direccion"
                                        name="direccion">
                                    <div class="text-danger error-message" id="error-direccion"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarSucursal()">Guardar</button>
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
                            LISTADO DE SUCURSALES</h3>
                        <div class="card-toolbar">
                            <a class="btn btn-sm fw-bold btn-primary" onclick="modalNuevoSucursal()"><i
                                    class="fa fa-plus"></i>Nuevo Sucursal</a>
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
        });


        function ajaxListado() {
            let datos = {};
            $.ajax({
                url: "{{ route('sucursal.ajaxListado') }}",
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

        function modalNuevoSucursal() {
            limpiarErorres();
            $('#id').val(0)
            $('#nombre').val('')
            $('#codigo_sucursal').val('')
            $('#direccion').val('')
            $('#modalSucusal').modal('show')
        }

        function limpiarErorres() {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function guardarSucursal() {
            let datos = $('#formularioSucursal').serializeArray();
            $.ajax({
                url: "{{ route('sucursal.guardarSucursal') }}",
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
                        $('#modalSucusal').modal('hide')
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: resultado.statusText,
                            text: resultado.data,
                        });
                    }
                },
                error: function(xhr) {
                    limpiarErorres();
                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;

                        for (let campo in errores) {
                            let mensaje = errores[campo][0];

                            let input = $(`[name="${campo}"]`);
                            input.addClass("is-invalid");
                            input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: xhr.statusText,
                            text: xhr.responseText,
                        });
                    }
                }
            });
        }

        function editarSucursal(sucursal) {
            limpiarErorres();

            Object.keys(sucursal).forEach(key => {
                let input = $(`#${key}`);
                if (input.length) {
                    input.val(sucursal[key]);
                }
            });
            $('#modalSucusal').modal('show')
        }

        function eliminarSucursal(sucursal) {
            Swal.fire({
                title: "Quieres eliminar "+sucursal.nombre,
                text: "Ya no podras recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('sucursal.eliminarSucursal') }}",
                        method: "POST",
                        data: sucursal,
                        success: function (resultado) {
                            if(resultado.estado){
                                ajaxListado();
                            }
                        },
                        error: function (xhr) {
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

    </script>
@endsection
