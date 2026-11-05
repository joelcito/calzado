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
    <div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de <span id="text_tipoo_modal" class="text-info"></span></h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioIngresoSalida">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Categoria</label>
                                    <select class="form-select form-select-sm" name="categoria_id" id="categoria_id" onchange="sacarSubCategorias()"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2 required">Tipo Pago</label>
                                    <select name="tipo_pago" id="tipo_pago" class="form-control form-control-sm" required>
                                        <option value="">Seleccione</option>
                                        <option value="EFECTIVO">EFECTIVO</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                        <option value="QR">QR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto</label>
                                    <input type="number" id="monto" name="monto" class="form-control form-control-solid mb-3 mb-lg-0" min="0.1"
                                        step="0.01" value="0" required onclick="this.select()">
                                    <input type="hidden" id="tipo" name="tipo" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion"
                                        class="form-control form-control-solid mb-3 mb-lg-0">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" id="boton-agrega-sale-dinero" onclick="guardarTipoIngresoSalida()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
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
                        <h3 class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">LISTADO DE PAGOS</h3>
                        <div class="card-toolbar">
                            @if ($cajaAbierta)
                                <button type="button" class="btn btn-sm fw-bold btn-success ml-5"
                                    onclick="modalIngresoSalida('INGRESO')">
                                    <i class="fas fa-money-bill"></i><i class="fas fa-arrow-down"></i>Nuevo Ingreso</button>

                                <button type="button" class="btn btn-sm fw-bold btn-danger ml-5 m-3"
                                    onclick="modalIngresoSalida('SALIDA')">
                                    <i class="fas fa-money-bill"></i><i class="fas fa-arrow-up"></i>Nuevo Salida</button>

                                <a class="btn btn-sm fw-bold btn-danger" onclick="modalCerrarCaja()"><i
                                        class="fa fa-plus"></i>Cerrar Caja</a>
                            @else
                                    <a class="btn btn-sm fw-bold btn-success" onclick="modalAperturaCaja()"><i
                                            class="fa fa-plus"></i>Abrir Caja</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <form id="formulario_busqueda">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Sucursal</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="sucursal_id" id="sucursal_id" onchange="filtrarCajasScurusal()">
                                            <option value="">SELECCIONE</option>
                                            @foreach ($sucursales as $sucursal)
                                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Fecha Ini</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_ini"
                                            name="fecha_ini" value="{{ $fechaIni }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_fin"
                                            name="fecha_fin" value="{{ $fechaFin }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Usuario</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="usuario_busqueda_id" id="usuario_busqueda_id" onchange="filtarCajas()">
                                            <option value="">SELECCIONE</option>
                                            @foreach ($usuarios as $user)
                                                <option data-sucursal="{{ $user->sucursal->id }}" value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Cajas</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="caja_id" id="caja_id">
                                            <option value="">SELECCIONE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button onclick="ajaxListado()" type="button"
                                        class="btn btn-sm w-100 btn-success mt-8"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-md-1">
                                    <button onclick="ajaxDescargarReportePago()" type="button" class="btn btn-sm w-100 btn-danger mt-8" title="Genera Reporte"><i class="fa fa-file-pdf"></i></button>
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

        function ajaxListado() {
            // Mostrar SweetAlert2 antes de enviar la solicitud
            // Swal.fire({
            //     title: 'Generando Listado...',
            //     text: 'Por favor espera mientras generamos el listado.',
            //     allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
            //     didOpen: () => {
            //         Swal.showLoading(); // Mostrar el spinner de carga
            //     }
            // });

            let datos = $('#formulario_busqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

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

        function limpiarErorres() {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoRol() {
            limpiarErorres();

            $('#id').val(0)
            $('#nombre').val('')
            $('#modalRol').modal('show')
        }

        function modalIngresoSalida(tipo) {
            $('#tipo').val(tipo)
            $('#text_tipoo_modal').text(tipo)
            $('#monto').val(0)
            $('#descripcion').val('')

            let categoriasIngreso = (tipo === "INGRESO") ? @json($categoriasIngreso) : @json($categoriasSalida);

            $('#categoria_id').empty();
            $('#categoria_id').append('<option value="">Seleccione</option>');

            categoriasIngreso.forEach(c => {
                $('#categoria_id').append(
                    `<option value="${c.id}">${c.nombre}</option>`
                );
            })

            $("#boton-agrega-sale-dinero").prop("disabled", false).html('Guardar');

            $('#modalIngreso').modal('show')
        }

        function guardarTipoIngresoSalida() {
            $("#boton-agrega-sale-dinero").prop("disabled", true)            .html('<span class="spinner-border spinner-border-sm"></span> Procesando...');
            if ($("#formularioIngresoSalida")[0].checkValidity()) {
                datos = $("#formularioIngresoSalida").serializeArray()
                $.ajax({
                    url: "{{ url('pago/guardarTipoIngresoSalida') }}",
                    data: datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Se registro con exito',
                                showConfirmButton: false, // No mostrar botón de confirmación
                                timer: 2000, // 5 segundos
                                timerProgressBar: true
                            });
                            $('#modalIngreso').modal('hide')
                            ajaxListado();
                        }
                    }
                });
            } else {
                $("#formularioIngresoSalida")[0].reportValidity()
            }
        }

        function ajaxDescargarReportePago(){
            let datos = $('#formulario_busqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxDescargarReportePago') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    const link = document.createElement('a');
                    link.href = 'data:application/pdf;base64,' + resultado.data.base64;
                    link.download = resultado.data.nombre;
                    link.click();
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

        function filtarCajas(){
            let cajas = @json($cajas);
            let usuarioOption = $('#usuario_busqueda_id option:selected');
            let sucursalId = usuarioOption.data('sucursal');
            $('#caja_id').empty();
            $('#caja_id').append('<option value="">SELECCIONE</option>');
            if (!sucursalId) {
                $('#caja_id').trigger('change');
                return;
            }
            let filtradas = cajas.filter(c => c.sucursal_id == sucursalId);
            filtradas.forEach(c => {
                let texto = "["+c.id+"]["+c.estado + "] | " + c.fecha_apertura + " - " + c.fecha_cierre;
                $('#caja_id').append(`<option value="${c.id}">${texto}</option>`);
            });
            $('#caja_id').trigger('change');
        }

        function filtrarCajasScurusal(){
            let cajas = @json($cajas);
            let sucursal_id = $('#sucursal_id').val()
            $('#caja_id').empty();
            $('#caja_id').append('<option value="">SELECCIONE</option>');
            if (!sucursal_id) {
                $('#caja_id').trigger('change');
                return;
            }
            let filtradas = cajas.filter(c => c.sucursal_id == sucursal_id);
            filtradas.forEach(c => {
                let texto = "["+c.id+"]["+c.estado + "] | " + c.fecha_apertura + " - " + c.fecha_cierre;
                $('#caja_id').append(`<option value="${c.id}">${texto}</option>`);
            });
            $('#caja_id').trigger('change');
        }

    </script>
@endsection
