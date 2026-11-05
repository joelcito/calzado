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

    <!--begin::Modal PAGO DEUDAS-->
    <div class="modal fade" id="modalFacturacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">DATOS DE LA FACTURA</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <div id="datosFacturacion"></div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal PAGO DEUDAS-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modmodalContingenciaFueraLinea" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">FORMULARIO DE CONTINGENCIA</h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">FECHA</label>
                                    <input type="date" class="form-control" id="fecha_contingencia"
                                        name="fecha_contingencia" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="fv-row mb-7">
                                    <button class="btn btn-success w-100 mt-4 btn-sm btn-icon"
                                        onclick="buscarEventosSignificativos()" type="button"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">EVENTO SIGNIFICATIVO</label>
                                    <select name="evento_significativo_contingencia_select"
                                        id="evento_significativo_contingencia_select" class="form-control"
                                        onchange="muestraTableFacturaPaquete()">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div id="tablas_facturas_offline" style="display: none">

                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
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
                        <h3
                            class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            LISTADO DE VENTAS</h3>
                        <div class="card-toolbar">
                            <a class="btn btn-sm fw-bold btn-primary" href="{{ url('factura/formulario') }}"><i
                                    class="fa fa-plus"></i>Nueva Venta Compra Venta</a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <form id="formulario-busqueda-factura">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Sucursal</label>
                                    <select class="form-select form-select-sm" name="buscar_sucursal_id" id="buscar_sucursal_id">
                                        <option value="">Seleccione un Sucursal</option>
                                        @foreach ($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @rol(1,5)
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Cliente</label>
                                    <select class="form-select form-select-sm" name="buscar_cliente_id" id="buscar_cliente_id">
                                        <option value="">Seleccione un Cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Vendedor</label>
                                    <select class="form-select form-select-sm" name="buscar_vendedor_id" id="buscar_vendedor_id">
                                        <option value="">Seleccione un Vendedor</option>
                                        @foreach ($vendedores as $vendedor)
                                            <option value="{{ $vendedor->id }}">{{ $vendedor->nombres." ".$vendedor->ap_paterno." ".$vendedor->ap_materno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endrol
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio"
                                        id="buscar_fecha_inicio">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin"
                                        id="buscar_fecha_fin">
                                </div>
                                <div class="col-md-2">
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
                                            {{-- <button type="button" id="botom_genera_excel"
                                                class="btn btn-success btn-sm w-100 btn-icon mt-8"
                                                title="Expotar en Excel" onclick="exportarExcel()"><i
                                                    class="fa fa-file-excel"></i></button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="tabla_facturas">

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

            // $("#facturacion_datos_tipo_metodo_pago, #facturacion_datos_tipo_moneda, #tipo_documento").select2();

        });

        function ajaxListado() {

            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando Listado...',
                text: 'Por favor espera mientras generamos el listado.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            let datos = $('#formulario-busqueda-factura').serializeArray();
            $.ajax({
                url: "{{ url('factura/ajaxListadoFacturas') }}",
                method: "POST",
                data: datos,
                success: function(data) {
                    if (data.estado) {
                        $('#tabla_facturas').html(data.data.listado)
                    } else {

                    }

                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    Swal.close();

                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.close();
                    Swal.fire('Error', xhr.responseText, 'error', 5000);
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                }
            })
        }

        function reportePDF() {

            let datos = $('#formulario-busqueda-factura').serializeArray();

            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            $.ajax({
                url: "{{ url('factura/reportePDF') }}",
                method: "POST",
                data: datos,
                xhrFields: {
                    responseType: 'blob' // Esto le dice a jQuery que espere un archivo binario (PDF)
                },
                success: function(data, status, xhr) {
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    Swal.close();

                    // Crear un enlace temporal para iniciar la descarga
                    var blob = new Blob([data], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "reporte_facturas.pdf"; // Nombre del archivo
                    link.click();
                },
                error: function(xhr, status, error) {
                    // Mostrar error si algo falla
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });

        }

        function exportarExcel() {
            let datos = $('#formulario-busqueda-factura').serializeArray();

            // // Mostrar SweetAlert2 antes de enviar la solicitud
            // Swal.fire({
            //     title: 'Generando PDF...',
            //     text: 'Por favor espera mientras generamos el archivo.',
            //     allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
            //     didOpen: () => {
            //         Swal.showLoading(); // Mostrar el spinner de carga
            //     }
            // });

            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando Excel...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            $.ajax({
                url: "{{ url('factura/reporteExcel') }}",
                method: "POST",
                data: datos,
                xhrFields: {
                    responseType: 'blob' // Esto le dice a jQuery que espere un archivo binario (PDF)
                },
                success: function(data, status, xhr) {
                    // // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    Swal.close();

                    // Assume `data` contains the binary response from the server
                    var blob = new Blob([data], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'reporte_facturas.xlsx'; // Nombre del archivo Excel
                    document.body.appendChild(link); // Required for Firefox
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(xhr, status, error) {
                    // Mostrar error si algo falla
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });
        }

        function desanularFacturaAnulado(factura) {
            Swal.fire({
                title: "Estas seguro de Revertir la Factura anulada?",
                text: "Esta accion no se podra revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('factura/desanularFacturaAnulado') }}",
                        method: "POST",
                        data: {
                            factura: factura
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.estado) {
                                ajaxListado();
                                Swal.fire({
                                    icon: 'success',
                                    title: "EXITO",
                                    text: JSON.stringify(data.msg),
                                    showConfirmButton: false, // No mostrar botón de confirmación
                                    // timer            : 2000,        // 5 segundos
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: JSON.stringify(data.msg),
                                    title: "ERROR",
                                    showConfirmButton: false, // No mostrar botón de confirmación
                                    // timer            : 2000,        // 5 segundos
                                    timerProgressBar: true
                                });
                            }
                        }
                    })
                }
            });
        }

        //FORMULARIO DEUDAS
        function limpiarErorres() {
            $('.error-message').html('');
            $('.is-invalid').removeClass('is-invalid');
        }

        function cambiaEstadoVenta(factura, estado) {

            let estados = [];

            if (estado == 'RECEPCIONADO') {
                estados = {
                    'TRABAJANDO': 'TRABAJANDO',
                    'TERMINADO': 'TERMINADO'
                }
            } else if (estado == 'TRABAJANDO') {
                // } else if (estado == 'TRABAJANDO' || estado == 'TERMINADO') {
                estados = {
                    'TERMINADO': 'TERMINADO'
                }
            } else if (estado == 'TERMINADO') {
                // } else if (estado == 'TRABAJANDO' || estado == 'TERMINADO') {
                estados = {
                    'ENTREGADO': 'ENTREGADO'
                }
            }

            Swal.fire({
                title: "SELECCIONE UN ESTADO",
                input: "select",
                // inputOptions: {
                //     octocat: 'octocat',
                //     torvalds: 'torvalds',
                //     gaearon: 'gaearon',
                //     jjjoelcito123: 'jjjoelcito123'
                // },
                inputOptions: estados,
                inputPlaceholder: 'Selecciona',
                showCancelButton: true,
                confirmButtonText: "Buscar",
                showLoaderOnConfirm: true,
                icon: 'question',
                preConfirm: async (login) => {

                    let datos = {
                        estado: login,
                        factura: factura
                    }

                    $.ajax({
                        url: "{{ url('factura/cambioEstadoVenta') }}",
                        method: "POST",
                        data: datos,
                        success: function(data) {

                            if (data.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "EXITO!",
                                    text: "SE CAMBIO DE ESTADO CON EXITO",
                                })
                                ajaxListado();
                                // $('#modalAnular').modal('hide')
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.mensaje.descripcion.codigoDescripcion,
                                    text: JSON.stringify(data.mensaje.descripcion
                                        .mensajesList),
                                    // timer:1500
                                })
                                // $('#modalAnular').modal('hide')
                            }

                            // // Habilita el botón y oculta el icono de carga después de completar
                            // boton.attr("disabled", false);
                            // iconoCarga.hide();
                        }
                    })

                    // try {
                    //     const githubUrl = `https://api.github.com/users/${login}`;
                    //     const response = await fetch(githubUrl);
                    //     if (!response.ok) {
                    //         return Swal.showValidationMessage(
                    //             `Usuario no encontrado: ${login}`
                    //         );
                    //     }
                    //     return response.json();
                    // } catch (error) {
                    //     Swal.showValidationMessage(`Error: ${error}`);
                    // }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                // if (result.isConfirmed) {
                //     Swal.fire({
                //         title: `Avatar de ${result.value.login}`,
                //         imageUrl: result.value.avatar_url
                //     });
                // }
            });


        }

        function imprimeREcibo(recibo) {
            href = "{{ url('factura/imprimeTicked') }}/" + recibo;
            // window.location.href = href;
            window.open(href, '_blank');
        }

        {{--
        function ajaxFacturaVenta(factura) {
            $('#datosFacturacion').html('');
            datos = {
                factura_id: factura
            }
            $.ajax({
                url: "{{ route('factura.ajaxFacturaVenta') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#datosFacturacion').html(resultado.data.formularioFacturacion);

                        $('#datosFacturacion').find('[data-control="select2"]').select2({
                            dropdownParent: $('#modalFacturacion')
                        });


                        $('#modalFacturacion').modal('show');
                    }
                }
            });
        }

        function cambiarUnidaMedida(detalle, select) {
            datos = {
                detalle: detalle,
                unidad: select.value
            }
            $.ajax({
                url: "{{ route('factura.cambiarUnidaMedida') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#text_unidad_' + resultado.data.detalle).show('toggle');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error(xhr.responseText);
                    Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                        'error');
                }
            });
        }

        function cambiarProductoServicio(detalle, select) {
            let option = select.options[select.selectedIndex];
            // Recuperamos los datos
            let idProducto = option.value;
            let codigoProducto = option.dataset.codigo_producto;
            let codigoActividad = option.dataset.codigo_actividad;

            let datos = {
                detalle: detalle,
                idProducto: idProducto,
                codigoProducto: codigoProducto,
                codigoActividad: codigoActividad
            }
            $.ajax({
                url: "{{ route('factura.cambiarProductoServicio') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#text_producto_servicio_' + resultado.data.detalle).show('toggle');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error(xhr.responseText);
                    Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                        'error');
                }
            });
        }

        function emitirFactura() {

            if ($("#formularioFacturacion")[0].checkValidity()) {

                let datos = $('#formularioFacturacion').serializeArray();

                $.ajax({
                url: "{{ route('factura.emitirFactura') }}",
                method: "POST",
                data: datos,
                success: function(data) {
                    if (data.estado === "VALIDADA") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excelente!',
                            text: 'LA FACTURA FUE VALIDADA',
                            timer: 3000
                        })
                        if (data.numero != null && data.numero != '') {
                            window.open("{{ url('factura/generaPdfFacturaNewCv') }}/" + data.numero,
                                "_blank", "width=800,height=600");
                            // window.location.reload();
                            $('#modalFacturacion').modal('hide')
                            ajaxListado();
                        } else {
                            window.location.href = "{{ url('factura/listado') }}"
                        }
                    } else if (data.estado === "error_email") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.text,
                        })
                        // Habilita el botón y oculta el icono de carga después de completar
                        boton.attr("disabled", false);
                        iconoCarga.hide();
                    } else if (data.estado === "OFFLINE") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Exito!',
                            text: 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                            timer: 2000
                        })
                        window.location.href = "{{ url('factura/listado') }}"
                    } else if (data.estado === "error_firma") {
                        Swal.fire({
                            icon: 'error',
                            title: data.text,
                            text: 'EMISION DE FACTURA RECHAZADO',
                        })
                        // Habilita el botón y oculta el icono de carga después de completar
                        boton.attr("disabled", false);
                        iconoCarga.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.text + " " + data.data,
                            text: 'LA FACTURA FUE RECHAZADA',
                        })
                        // Habilita el botón y oculta el icono de carga después de completar
                        boton.attr("disabled", false);
                        iconoCarga.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error(xhr.responseText);
                    Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                        'error');
                }
            });

            } else {
                $("#formularioFacturacion")[0].reportValidity();
            }
        }
        --}}

        function verificaNit() {
            if ($('#tipo_documento').val() === "5") {
                let nit = $('#nit_factura').val();
                $.ajax({
                    url: "{{ url('factura/verificarNit') }}",
                    data: {
                        nit: nit
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.estado) {
                            if (data.data.estadoSiat) {
                                $('#execpcion').prop('checked', false);
                                $('#nitsiexiste').show('toggle')
                                $('#nitnoexiste').hide('toggle')
                            } else {
                                $('#nitnoexiste').show('toggle')
                                $('#nitsiexiste').hide('toggle')
                                $('#execpcion').prop('checked', true);
                            }
                        } else {
                            // $('#errorValidar').show('toggle')
                            // Swal.fire('Error al Validar', JSON.stringify(data.mensaje) ,'error');
                        }
                    }
                });

                $('#complemento').val(null)
                $('#bloque_complemento').hide('toggle')

            } else if ($('#tipo_documento').val() === "1") {

                $('#bloque_complemento').show('toggle')
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

            } else {
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#bloque_complemento').hide('toggle')

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

        function anularRecibo(numero, venta){
            Swal.fire({
                title: "Está seguro de eliminar la venta "+numero+"?",
                text: "No podra revertir esta accion!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, anular!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ url('factura/anularRecibo') }}",
                        method: "POST",
                        data: {venta:venta},
                        success: function(data) {
                            if (data.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Exito!",
                                    text: "Se elimino con exito la venta!",
                                })
                                ajaxListado();
                            } else {
                                // $('#select_cufd_vigentes').html('')
                                // $('#bloque_cufd_offline').hide('toggle');

                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error AJAX:', error);
                            console.error(xhr.responseText);
                            Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                                'error');
                        }
                    })

                    // Swal.fire({
                    //     title: "Deleted!",
                    //     text: "Your file has been deleted.",
                    //     icon: "success"
                    // });
                }
            });
        }

    </script>
@endsection
