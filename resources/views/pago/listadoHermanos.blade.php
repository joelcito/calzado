@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
    @include('pago.components.formularioIngresoSalida')
    <div class="card">
        <div class="card-header flex-wrap bg-light-info py-4">
            <h3 class="card-title fw-bold">INGRESOS Y SALIDAS POR HERMANO</h3>
            <div class="card-toolbar gap-2">
                @if ($cajaAbierta)
                    <button class="btn btn-sm btn-success" type="button" onclick="modalIngresoSalida('INGRESO')"><i class="fa fa-arrow-down"></i>Nuevo ingreso / devolución</button>
                    <button class="btn btn-sm btn-danger" type="button" onclick="modalIngresoSalida('SALIDA')"><i class="fa fa-arrow-up"></i>Nueva salida</button>
                @else
                    <a class="btn btn-sm btn-success" href="{{ route('pago.listado') }}">Abrir caja en Ventas del Día</a>
                @endif
            </div>
        </div>
        <div class="card-body py-4">
            <form id="formulario_busqueda" class="row g-3 mb-5">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Sucursal</label>
                    <select class="form-select form-select-sm" name="sucursal_id">
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" @selected($sucursal->id == $usuario->sucursal_id)>{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hermano / dueño</label>
                    <select class="form-select form-select-sm" name="usuario_duenio_id">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $hermano)
                            <option value="{{ $hermano->id }}">{{ $hermano->name ?: trim($hermano->nombres.' '.$hermano->ap_paterno.' '.$hermano->ap_materno) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_ini" class="form-control form-control-sm" value="{{ $fechaIni }}" required></div>
                <div class="col-md-2"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $fechaFin }}" max="{{ $fechaFin }}" required></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" id="boton_buscar" class="btn btn-sm btn-success w-100"><i class="fa fa-search"></i>Buscar</button></div>
            </form>
            <div class="alert alert-info">
                Disponible = saldo anterior + cobros + otros ingresos − salidas. Se controla por hermano y medio de pago.
                Las fechas filtran movimientos y ventas; los cobros incluyen abonos recibidos en ese período de ventas anteriores.
                El pendiente corresponde a las ventas del período, al cierre de la fecha fin. Disponible es dinero, no utilidad.
            </div>
            <p class="text-muted">Las operaciones nuevas se registran en su sucursal: <strong>{{ $usuario->sucursal?->nombre }}</strong>. Los movimientos antiguos sin dueño figuran como “Sin asignar”. Las aperturas de caja no se suman nuevamente al saldo de los hermanos.</p>
            <div id="table_listado" aria-live="polite"></div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        let solicitudListado;
        $(document).ready(function() {
            $('#formulario_busqueda').on('submit', function(evento) { evento.preventDefault(); ajaxListado(); });
            ajaxListado();
        });
        function ajaxListado() {
            if (solicitudListado) solicitudListado.abort();
            $('#boton_buscar').prop('disabled', true);
            solicitudListado = $.ajax({
                url: "{{ route('pago.ajaxListadoHermanos') }}",
                type: 'POST',
                data: $('#formulario_busqueda').serialize(),
                success: function(resultado) {
                    if (resultado.estado) $('#table_listado').html(resultado.data.listado);
                    else Swal.fire({ icon: 'error', text: resultado.mensaje });
                },
                error: function(xhr, estado) {
                    if (estado === 'abort') return;
                    const errores = xhr.responseJSON?.errors;
                    Swal.fire({ icon: 'error', text: errores ? Object.values(errores).flat().join('\n') : 'No se pudo cargar el listado.' });
                },
                complete: function() { $('#boton_buscar').prop('disabled', false); }
            });
        }
    </script>
    @include('pago.components.ingresoSalidaJs')
@endsection
