<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_sales_without_inventory_and_excludes_cancelled_sales(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 10)->setTime(12, 0));
        $sucursal = DB::table('sucursales')->insertGetId(['nombre' => 'Central']);
        $otraSucursal = DB::table('sucursales')->insertGetId(['nombre' => 'Otra']);
        DB::table('roles')->insert(['id' => 1, 'nombre' => 'Administrador']);
        $usuario = User::factory()->create(['sucursal_id' => $sucursal, 'rol_id' => 1]);
        $producto = DB::table('productos')->insertGetId(['nombre' => 'Producto de prueba', 'codigo' => 'Z1', 'control_stock' => 0]);
        foreach ([[100, null, $sucursal], [900, 'ANULADO', $sucursal], [600, null, $otraSucursal]] as [$total, $estado, $sucursalVenta]) {
            $factura = DB::table('facturas')->insertGetId(['sucursal_id' => $sucursalVenta, 'total' => $total, 'estado' => $estado, 'fecha' => '2026-11-10 09:00:00']);
            DB::table('detalles')->insert(['factura_id' => $factura, 'producto_id' => $producto, 'nombre_producto' => 'Producto de prueba', 'cantidad' => 3, 'estado' => 'Finalizado']);
        }
        $this->actingAs($usuario)->get('/home?fecha_inicio=2026-11-10&fecha_fin=2026-11-10&sucursal_id='.$sucursal)
            ->assertOk()->assertSee('Ventas por día')->assertSee('Producto de prueba')
            ->assertViewHas('totalVentas', 100.0)->assertViewHas('cantidadVentas', 1)
            ->assertDontSee('Unidades en stock')->assertDontSee('Productos por reponer')->assertDontSee('Inventario actual')
            ->assertViewHas('productosVendidos', fn ($productos) => (float) $productos->first()->cantidad === 3.0)
            ->assertViewHas('dias', fn ($dias) => $dias->first()['total'] === 100.0);
    }

    public function test_empty_dashboard_and_invalid_date_range(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario)->get('/home')->assertOk()
            ->assertSee('No hay ventas en el período seleccionado.')
            ->assertViewHas('totalVentas', 0.0);
        $this->get('/home?fecha_inicio=2026-11-10&fecha_fin=2026-11-01')->assertSessionHasErrors('fecha_fin');
    }
}
