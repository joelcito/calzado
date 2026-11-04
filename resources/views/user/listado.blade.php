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
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE USUARIOS</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioUsuario">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombres"
                                        name="nombres">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap. Paterno</label>
                                    <input type="text" class="form-control form-control-sm" id="ap_paterno"
                                        name="ap_paterno">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap. Materno</label>
                                    <input type="text" class="form-control form-control-sm" id="ap_materno"
                                        name="ap_materno">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                                    <input type="text" class="form-control form-control-sm" id="cedula"
                                        name="cedula">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Direccion</label>
                                    <input type="text" class="form-control form-control-sm" id="direccion"
                                        name="direccion">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">E-mail (USUARIO DE INGRESO)</label>
                                    <input type="text" class="form-control form-control-sm" id="email"
                                        name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Celular</label>
                                    <input type="text" class="form-control form-control-sm" id="celular"
                                        name="celular">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Rol</label>
                                    <select name="rol_id" id="rol_id" class="form-control form-control-sm" required>
                                        @foreach ($roles as $rol)
                                            <option value={{ $rol->id }}>{{ $rol->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Sucursal</label>
                                    <select name="sucursal_id" id="sucursal_id" class="form-control form-control-sm"
                                        required>
                                        <option value="">Seleccione</option>
                                        @foreach ($sucursales as $sucursal)
                                            <option value={{ $sucursal->id }}>{{ $sucursal->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Punto Venta</label>
                                    <div id="select_puntos_ventas">

                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarUsuario()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->
    {{-- Modal contrasenia --}}
    <div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">Restablecer Contraseña</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formResetPassword">
                        @csrf
                        <input type="hidden" id="usuario_id_reset" name="usuario_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nueva Contraseña</label>
                                    <input type="password" class="form-control form-control-sm" id="password"
                                        name="password">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Confirmar Contraseña</label>
                                    <input type="password" class="form-control form-control-sm"
                                        id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="resetPasswordUser()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    {{-- Fin Modal contrasenia --}}

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <h3
                            class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            LISTADO DE USUARIOS</h3>
                        <div class="card-toolbar">
                            <a class="btn btn-sm fw-bold btn-primary" onclick="modalNuevoUsuario()"><i
                                    class="fa fa-plus"></i>Nuevo usuario</a>
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
                url: "{{ route('usuario.ajaxListado') }}",
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
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoUsuario() {
            limpiarErorres();

            $('#id').val(0)
            $('#nombres').val('')
            $('#ap_paterno').val('')
            $('#ap_materno').val('')
            $('#cedula').val('')
            $('#direccion').val('')
            $('#email').val('')
            $('#celular').val('')
            $('#rol_id').val('')
            $('#sucursal_id').val('')
            $('#select_puntos_ventas').html('')
            $('#modalUsuario').modal('show')
        }

        function guardarUsuario() {

            if ($('#formularioUsuario')[0].checkValidity()) {
                let datos = $('#formularioUsuario').serializeArray();
                $.ajax({
                    url: "{{ route('usuario.guardarUsuario') }}",
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
                            $('#modalUsuario').modal('hide')
                        } else {

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
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',
                            });
                        }
                    }
                });
            } else {
                $('#formularioUsuario')[0].reportValidity();
            }

        }

        function editarUsuario(usuario) {
            limpiarErorres();

            Object.keys(usuario).forEach(key => {
                let input = $(`#${key}`);
                if (input.length) {
                    input.val(usuario[key]);
                }
            });

            $('#modalUsuario').modal('show');
        }

        function eliminarUsuario(usuario) {
            Swal.fire({
                title: "Quieres eliminar " + usuario.nombres,
                text: "Ya no podras recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('usuario.eliminarUsuario') }}",
                        method: "POST",
                        data: usuario,
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

        function abrirModalResetPassword(usuarioId) {
            $('#usuario_id_reset').val(usuarioId);
            $('#password').val('');
            $('#password_confirmation').val('');
            $('#modalResetPassword').modal('show');
        }

        function resetPasswordUser() {
            let usuarioId = $('#usuario_id_reset').val();
            let password = $('#password').val();
            let passwordConfirmation = $('#password_confirmation').val();

            if (password !== passwordConfirmation) {
                Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('usuario.resetPassword') }}",
                type: "POST",
                data: {
                    usuario_id: usuarioId,
                    password: password,
                    password_confirmation: passwordConfirmation,
                },
                success: function(response) {
                    Swal.fire({
                        title: "Contraseña Actualizada con Exito!",
                        icon: "success",
                        timer: 2000, // Se cierra en 2 segundos
                        showConfirmButton: false
                    });
                    $('#modalResetPassword').modal('hide');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Hubo un problema al actualizar la contraseña', 'error');
                }
            });
        }
    </script>
@endsection
