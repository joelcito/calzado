<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'movimientos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'producto_id',
        'detalle_id',
        'sucursal_id',
        'precio_compra',
        'precio_venta',
        'ingreso',
        'salida',
        'fecha',
        'descripcion',
        'estado',
        'deleted_at'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function cantidaDisponileACiertaFecha($sucursal_id, $servicio_id, $fecha_ini, $fecha_fin)
    {
        $ingresos = Movimiento::where('sucursal_id', $sucursal_id)
            ->where('producto_id', $servicio_id)
            ->where('ingreso', '>', 0)
            ->where('fecha', '<=', $fecha_ini)
            ->sum('ingreso');

        $salidas = Movimiento::where('sucursal_id', $sucursal_id)
            ->where('producto_id', $servicio_id)
            ->where('salida', '>', 0)
            ->where('fecha', '<=', $fecha_fin)
            ->sum('salida');

        return $ingresos - $salidas;
    }

    public function cantidaDisponile($sucursal_id, $servicio_id)
    {
        $ingresos = Movimiento::where('sucursal_id', $sucursal_id)
            ->where('producto_id', $servicio_id)
            ->where('ingreso', '>', 0)
            ->sum('ingreso');

        $salidas = Movimiento::where('sucursal_id', $sucursal_id)
            ->where('producto_id', $servicio_id)
            ->where('salida', '>', 0)
            ->sum('salida');

        return $ingresos - $salidas;
    }
}
