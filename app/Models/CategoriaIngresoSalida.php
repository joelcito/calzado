<?php

namespace App\Models;

use App\Utils\Respuesta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaIngresoSalida extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'categoria_ingreso_salidas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'tipo',
        'nombre',
        'estado',
        'deleted_at'
    ];
}
