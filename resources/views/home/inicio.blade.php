@extends('layouts.app')
@section('css')

@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<!--begin::Row-->
<div class="row g-5 gx-xl-10 mb-5 mb-xl-10">

    <div class="col-xxl-12">
        <div class="card card-flush h-md-100">
            <div class="card-body d-flex flex-column justify-content-between mt-9 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0" style="background-position: 100% 50%; background-image:url('assets/media/stock/900x600/42.png')">
                <div class="mb-10">
                    <div class="fs-2hx fw-bold text-gray-800 text-center mb-13">
                        <span class="me-2">Sistema de Control de Invetarios.
                        <br />
                    </div>
                </div>

                <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container container-fluid">
                        <!--begin::Row-->
                        <div class="row">
                            <!--begin::Col-->
                            <div class="col-md-3">
                                <!--begin::Card widget 20-->
                                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end" style="background-color: #F1416C;background-image:url('assets/media/patterns/vector-1.png');">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">
                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">1</span>
                                            <!--end::Amount-->
                                            <!--begin::Subtitle-->
                                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Facturas del Día</span>
                                            <!--end::Subtitle-->
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex align-items-end pt-0">
                                        <!--begin::Progress-->
                                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                                {{-- <span>43 Pending</span>
                                                <span>72%</span> --}}
                                            </div>
                                            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                                <div class="bg-white rounded h-8px" role="progressbar" style="width: 72%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card widget 20-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-3">
                                <!--begin::Card widget 20-->
                                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end" style="background-color: #4d41f1;background-image:url('assets/media/patterns/vector-1.png');">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">
                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">2</span>
                                            <!--end::Amount-->
                                            <!--begin::Subtitle-->
                                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Recibos del Día</span>
                                            <!--end::Subtitle-->
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex align-items-end pt-0">
                                        <!--begin::Progress-->
                                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                                {{-- <span>43 Pending</span>
                                                <span>72%</span> --}}
                                            </div>
                                            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                                <div class="bg-white rounded h-8px" role="progressbar" style="width: 41%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card widget 20-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-3">
                                <!--begin::Card widget 20-->
                                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end" style="background-color: #f18241;background-image:url('assets/media/patterns/vector-1.png');">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">
                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">3</span>
                                            <!--end::Amount-->
                                            <!--begin::Subtitle-->
                                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Ventas del Día</span>
                                            <!--end::Subtitle-->
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex align-items-end pt-0">
                                        <!--begin::Progress-->
                                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                                {{-- <span>43 Pending</span>
                                                <span>72%</span> --}}
                                            </div>
                                            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                                <div class="bg-white rounded h-8px" role="progressbar" style="width: 20%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card widget 20-->
                            </div>
                            <!--end::Col-->


                            <!--begin::Col-->
                            <div class="col-md-3">
                                <!--begin::Card widget 20-->
                                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end" style="background-color: #3e7213;background-image:url('assets/media/patterns/vector-1.png');">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">
                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">4</span>
                                            <!--end::Amount-->
                                            <!--begin::Subtitle-->
                                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Cantidad de Usuarios Registrados</span>
                                            <!--end::Subtitle-->
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex align-items-end pt-0">
                                        <!--begin::Progress-->
                                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                                {{-- <span>43 Pending</span>
                                                <span>72%</span> --}}
                                            </div>
                                            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                                <div class="bg-white rounded h-8px" role="progressbar" style="width: 80%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card widget 20-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="fw-bold text-gray-800 text-center mb-3" style="font-size: 20px">
                                            <span class="me-2">Top 10 Mejores Vendedores</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                            <input type="date" class="form-control form-control-sm" id="vendedor_fecha_ini" name="vendedor_fecha_ini" value="{{ $inicioSemana }}" max="{{ $fechaActual }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                            <input type="date" class="form-control form-control-sm" id="vendedor_fecha_fin" name="vendedor_fecha_fin" value="{{ $fechaActual }}" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="fw-semibold fs-6 mb-2 text-white">.</label>
                                        <button type="button" class="btn btn-primary btn-sm w-100 btn-icon" title="Buscar" onclick="ajaxMejorVendedor()"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="table_mejor_vendedor"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="fw-bold text-gray-800 text-center mb-3" style="font-size: 20px">
                                            <span class="me-2">Productos Mas Vendidos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                            <input type="date" class="form-control form-control-sm" id="producto_fecha_ini" name="producto_fecha_ini" value="{{ $inicioSemana }}" max="{{ $fechaActual }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                            <input type="date" class="form-control form-control-sm" id="producto_fecha_fin" name="producto_fecha_fin" value="{{ $fechaActual }}" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="fw-semibold fs-6 mb-2 text-white">.</label>
                                        <button type="button" class="btn btn-primary btn-sm w-100 btn-icon" title="Buscar" onclick="ajaxProductoMasVendido()"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="table_producto_mas_vendido"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="chartVentasSucursal" style="width:100%; height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $(document).ready(function() {

        ajaxMejorVendedor();
        ajaxProductoMasVendido();

        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(dibujarGraficos);

        $(window).resize(function() {
            dibujarGraficos();
        });
    });

    let chartVentasSucursal;
    let dataVentasSucursal;

    function dibujarGraficos() {

        // Datos enviados directamente desde el controlador
        let datos = @JSON($dataVentasSucursal);

        dataVentasSucursal = google.visualization.arrayToDataTable(datos);

        chartVentasSucursal = new google.visualization.ColumnChart(
            document.getElementById('chartVentasSucursal')
        );

        chartVentasSucursal.draw(
            dataVentasSucursal,
            opcionesVentasSucursal()
        );
    }

    // function opcionesVentasSucursal() {
    //     return {
    //         title: 'Ventas por Sucursal (Últimos 6 Meses)',
    //         height: 400,
    //         legend: { position: 'top' },
    //         bar: { groupWidth: '65%' },
    //         hAxis: { title: 'Mes' },
    //         vAxis: {
    //             title: 'Cantidad de Ventas',
    //             minValue: 0
    //         }
    //     };
    // }

    function opcionesVentasSucursal() {
        return {
            title: 'Ingresos Netos por Sucursal (Últimos 6 Meses)',
            height: 400,

            legend: {
                position: 'top'
            },

            bar: {
                groupWidth: '65%'
            },

            hAxis: {
                title: 'Mes'
            },

            vAxis: {
                title: 'Monto Neto (Bs)',
                format: '#,##0.00'
            },

            tooltip: {
                textStyle: {
                    fontSize: 13
                }
            },

            chartArea: {
                width: '85%',
                height: '65%'
            }
        };
    }
</script>
@endsection
