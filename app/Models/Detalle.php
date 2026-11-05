<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalle extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'detalles';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'factura_id',
        'producto_id',
        'sucursal_id',
        'nombre_producto',
        'descripcion_adicional',
        'precio',
        'cantidad',
        'descuento',
        'total',
        'descripcion',
        'imagen',
        'importe',
        'fecha',
        'estado',
        'deleted_at'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
