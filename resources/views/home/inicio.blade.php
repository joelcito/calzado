@extends('layouts.app')

@section('css')
<style>
    .dashboard-banner { background: linear-gradient(115deg, #17354f, #247b91); color: #fff; border-radius: 16px; padding: 28px; }
    .dashboard-stat { border-top: 4px solid var(--accent); }
    .dashboard-bars { display: flex; align-items: stretch; gap: 12px; height: 245px; overflow-x: auto; padding: 12px 0; }
    .dashboard-column { flex: 1 0 42px; display: flex; flex-direction: column; text-align: center; justify-content: flex-end; font-size: 11px; }
    .dashboard-column .bar { background: #3699ff; border-radius: 5px 5px 0 0; min-height: 2px; width: 70%; margin: 5px auto; }
    .dashboard-donut { width: 210px; height: 210px; border-radius: 50%; position: relative; flex-shrink: 0; margin: 15px auto; }
    .dashboard-donut::after { content: ''; position: absolute; inset: 44px; border-radius: 50%; background: var(--bs-body-bg, white); }
    .dashboard-dot { display: inline-block; width: 11px; height: 11px; border-radius: 50%; margin-right: 8px; }
</style>
@endsection

@section('content')
<div class="dashboard-banner mb-7 d-flex flex-wrap justify-content-between align-items-center gap-4">
    <div><div class="opacity-75 mb-2">RESUMEN DEL NEGOCIO</div><h1 class="text-white mb-2">Panel de ventas</h1><span>Ventas y productos destacados de un vistazo.</span></div>
    <a href="{{ route('reporte.listado') }}" class="btn btn-light">Ver reportes completos</a>
</div>
<form action="{{ route('home') }}" method="GET" class="card card-body mb-7">
    <div class="row g-4 align-items-end">
        <div class="col-sm-3"><label for="fecha_inicio" class="form-label">Desde</label><input id="fecha_inicio" name="fecha_inicio" type="date" class="form-control" value="{{ $fechaInicio }}" required></div>
        <div class="col-sm-3"><label for="fecha_fin" class="form-label">Hasta</label><input id="fecha_fin" name="fecha_fin" type="date" class="form-control" value="{{ $fechaFin }}" required></div>
        <div class="col-sm-3"><label for="sucursal_id" class="form-label">Sucursal</label><select id="sucursal_id" name="sucursal_id" class="form-select"><option value="">Todas las sucursales</option>@foreach ($sucursales as $sucursal)<option value="{{ $sucursal->id }}" @selected($sucursalId == $sucursal->id)>{{ $sucursal->nombre }}</option>@endforeach</select></div>
        <div class="col-sm-3"><button class="btn btn-primary" type="submit">Actualizar</button> <a href="{{ route('home') }}" class="btn btn-light">Restablecer</a></div>
    </div>
    @if ($errors->any())<div class="text-danger mt-4" role="alert">{{ $errors->first() }}</div>@endif
    <small class="text-muted mt-3">Ventas del período seleccionado (máximo 366 días).</small>
</form>
<div class="row g-5 mb-7">
    @foreach ([['Ventas del período', 'Bs '.number_format($totalVentas, 2), '#3699ff'], ['Ventas registradas', number_format($cantidadVentas), '#50cd89']] as [$titulo, $valor, $color])
        <div class="col-sm-6"><div class="card dashboard-stat h-100" style="--accent: {{ $color }}"><div class="card-body"><div class="text-muted fw-semibold mb-3">{{ $titulo }}</div><div class="fs-2x fw-bold">{{ $valor }}</div></div></div></div>
    @endforeach
</div>
<div class="row g-5 mb-7">
    <div class="col-xl-7"><div class="card h-100"><div class="card-header"><h2 class="card-title">Ventas por día</h2><span class="card-toolbar text-muted">Bolivianos (Bs)</span></div><div class="card-body">
        @if ($cantidadVentas > 0)
            <div class="dashboard-bars" role="img" aria-label="Gráfico de barras de ventas diarias en bolivianos">
                @foreach ($dias as $dia)<div class="dashboard-column" title="{{ $dia['fecha'] }}: Bs {{ number_format($dia['total'], 2) }}"><span>{{ number_format($dia['total'], 2) }}</span><div class="bar" style="height: {{ max(0, $dia['total']) / max(1, $dias->max('total')) * 175 }}px"></div><span>{{ $dia['fecha'] }}</span></div>@endforeach
            </div>
        @else <p class="text-muted py-15 text-center">No hay ventas en el período seleccionado.</p> @endif
    </div></div></div>
    <div class="col-xl-5"><div class="card h-100"><div class="card-header"><h2 class="card-title">Ventas por sucursal</h2></div><div class="card-body">
        @php
            $colores = ['#3699ff', '#50cd89', '#7239ea', '#ffc700', '#f1416c', '#009ef7', '#a1a5b7'];
            $segmentos = [];
            $acumulado = 0;
            foreach ($ventasSucursal as $indice => $venta) {
                $fin = $acumulado + ($totalVentas > 0 ? $venta['total'] / $totalVentas * 100 : 0);
                $segmentos[] = $colores[$indice % count($colores)].' '.$acumulado.'% '.$fin.'%';
                $acumulado = $fin;
            }
        @endphp
        @if ($totalVentas > 0)
            <div class="dashboard-donut" style="background: conic-gradient({{ implode(', ', $segmentos) }})" role="img" aria-label="Gráfico de torta: distribución de ventas por sucursal"></div>
            @foreach ($ventasSucursal as $indice => $venta)<div class="d-flex justify-content-between gap-3 mb-3"><span><span class="dashboard-dot" style="background: {{ $colores[$indice % count($colores)] }}"></span>{{ $venta['nombre'] }}</span><strong>Bs {{ number_format($venta['total'], 2) }} <small class="text-muted">({{ number_format($venta['total'] / $totalVentas * 100, 1) }}%)</small></strong></div>@endforeach
        @else <p class="text-muted py-15 text-center">Sin ventas para mostrar la distribución.</p> @endif
    </div></div></div>
    <div class="col-12"><div class="card h-100"><div class="card-header"><h2 class="card-title">Productos más vendidos</h2><span class="card-toolbar text-muted">Unidades del período</span></div><div class="card-body">
        @forelse ($productosVendidos as $producto)
            <div class="mb-5"><div class="d-flex justify-content-between gap-3 mb-2"><span>{{ $producto->nombre_producto ?: 'Producto sin nombre' }}</span><strong>{{ number_format($producto->cantidad) }}</strong></div><div class="bg-light rounded" style="height: 10px"><div class="bg-primary rounded h-100" style="width: {{ max(0, $producto->cantidad) / max(1, $productosVendidos->max('cantidad')) * 100 }}%"></div></div></div>
        @empty <p class="text-muted text-center py-10">No hay productos vendidos en este período.</p> @endforelse
    </div></div></div>
</div>
<div class="card"><div class="card-header"><h2 class="card-title">Reportes rápidos</h2></div><div class="card-body d-flex flex-wrap gap-3">
    @foreach (['ventas' => 'Ventas del período', 'cuentasCobrar' => 'Cuentas por cobrar', 'utilidades' => 'Utilidades del período'] as $reporte => $nombre)
        <a class="btn btn-light-primary" href="{{ route('reporte.'.$reporte.'.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'sucursal_id' => $sucursalId]) }}">{{ $nombre }} · PDF</a>
    @endforeach
</div></div>
@endsection
