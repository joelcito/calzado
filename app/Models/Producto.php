<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'productos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'categoria_producto_id',
        'usuario_duenio_id',
        'nombre',
        'codigo',
        'minimo_stock',
        'precio_compra',
        'precio_venta',
        'control_stock',
        'tipo',
        'estado',
        'deleted_at'
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function duenio()
    {
        return $this->belongsTo(User::class, 'usuario_duenio_id');
    }
}
