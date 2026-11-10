<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\Factura;
use App\Models\Sucursal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_inicio'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
        ]);
        $fechaInicio = $filtros['fecha_inicio'] ?? now()->startOfMonth()->toDateString();
        $fechaFin = $filtros['fecha_fin'] ?? now()->toDateString();
        validator(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin], [
            'fecha_fin' => ['after_or_equal:fecha_inicio', 'before_or_equal:'.Carbon::parse($fechaInicio)->addDays(365)->toDateString()],
        ])->validate();
        $sucursalId = $filtros['sucursal_id'] ?? null;
        $ventas = Factura::whereNull('estado')
            ->whereBetween('fecha', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])
            ->when($sucursalId, fn ($query) => $query->where('sucursal_id', $sucursalId));
        $totalVentas = (float) (clone $ventas)->sum('total');
        $cantidadVentas = (clone $ventas)->count();
        $ventasDiarias = (clone $ventas)->selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->groupByRaw('DATE(fecha)')->pluck('total', 'dia');
        $dias = collect(CarbonPeriod::create($fechaInicio, $fechaFin))->map(fn ($dia) => [
            'fecha' => $dia->format('d/m'),
            'total' => (float) ($ventasDiarias[$dia->toDateString()] ?? 0),
        ]);
        $sucursales = Sucursal::orderBy('nombre')->get();
        $ventasSucursal = (clone $ventas)->selectRaw('sucursal_id, SUM(total) as total')
            ->groupBy('sucursal_id')->orderByDesc('total')->get()
            ->map(fn ($venta) => [
                'nombre' => $sucursales->firstWhere('id', $venta->sucursal_id)?->nombre ?? 'Sin sucursal',
                'total' => (float) $venta->total,
            ]);
        $productosVendidos = Detalle::whereIn('factura_id', (clone $ventas)->select('id'))
            ->selectRaw('producto_id, nombre_producto, SUM(cantidad) as cantidad')
            ->groupBy('producto_id', 'nombre_producto')->orderByDesc('cantidad')->limit(8)->get();

        return view('home.inicio', compact(
            'fechaInicio', 'fechaFin', 'sucursalId', 'sucursales', 'totalVentas', 'cantidadVentas',
            'dias', 'ventasSucursal', 'productosVendidos'
        ));
    }
}
