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
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE CLIENTES</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formularioCliente">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="nombres" name="nombres">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Ap. Paterno</label>
                                <input type="text" class="form-control form-control-sm" id="ap_paterno" name="ap_paterno">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Ap. Materno</label>
                                <input type="text" class="form-control form-control-sm" id="ap_materno" name="ap_materno">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                                <input type="text" class="form-control form-control-sm" id="cedula" name="cedula">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Complemento</label>
                                <input type="text" class="form-control form-control-sm" id="complemento" name="complemento">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Nit</label>
                                <input type="text" class="form-control form-control-sm" id="nit" name="nit">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Razon Social</label>
                                <input type="text" class="form-control form-control-sm" id="razon_social" name="razon_social">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Correo</label>
                                <input type="text" class="form-control form-control-sm" id="correo" name="correo">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Celular</label>
                                <input type="text" class="form-control form-control-sm" id="numero_celular" name="numero_celular">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Direccion</label>
                                <input type="text" class="form-control form-control-sm" id="direccion" name="direccion">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarCliente()">Guardar</button>
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
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <!--begin::Card-->
            <div class="card">
                <div class="card-header flex-wrap bg-light-info py-4">
                    <h3 class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        LISTADO DE CLIENTES</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-sm fw-bold btn-primary" onclick="modalNuevoCliente()"><i class="fa fa-plus"></i>Nuevo Cliente</a>
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

        function ajaxListado(){

            let datos = {};
            $.ajax({
                url: "{{ route('cliente.ajaxListado') }}",
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
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoCliente(){
            limpiarErorres();

            $('#id').val(0);
            $('#nombres').val('');
            $('#ap_paterno').val('');
            $('#ap_materno').val('');
            $('#cedula').val('');
            $('#complemento').val('');
            $('#nit').val('');
            $('#razon_social').val('');
            $('#correo').val('');
            $('#numero_celular').val('');
            $('#direccion').val('');
            $('#zona_id').val(null).trigger('change');
            $('#sucursal_id').val(null).trigger('change');
            $('#modalCliente').modal('show');
        }

        function guardarCliente(){

            if($('#formularioCliente')[0].checkValidity()){
                let datos = $('#formularioCliente').serializeArray();
                $.ajax({
                    url: "{{ route('cliente.guardarCliente') }}",
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
                            $('#modalCliente').modal('hide')
                        }else{

                        }
                    },
                    error: function (xhr) {
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
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',
                            });
                        }
                    }
                });
            }else{
                $('#formularioCliente')[0].reportValidity();
            }

        }

        function editarCliente(cliente){
            limpiarErorres();

            $('#zona_id').val(cliente.zona_id ?? null).trigger('change')
            $('#sucursal_id').val(cliente.sucursal_id ?? null).trigger('change')

            Object.keys(cliente).forEach(key => {
                let input = $(`#${key}`);
                if (input.length) {
                    input.val(cliente[key]);
                }
            });
            $('#modalCliente').modal('show')
        }

        function eliminarCliente(cliente){
            Swal.fire({
                title: "Quieres eliminar "+cliente.nombres,
                text: "Ya no podras recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('cliente.eliminarCliente') }}",
                        method: "POST",
                        data: cliente,
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
