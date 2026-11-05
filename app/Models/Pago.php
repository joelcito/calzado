<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pagos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'factura_id',
        'categoria_ingreso_salida_id',
        'caja_id',
        'tipo_pago',
        'monto',
        'cambio',
        'fecha',
        'descripcion',
        'apertura_caja',
        'estado',
        'deleted_at'
    ];

    public function factura()
    {
        return $this->belongsTo('App\Models\Factura', 'factura_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User', 'usuario_creador_id');
    }

    public static function pagosEfectuados($sucursal_id, $fechaIni, $fechaFin)
    {
        return static::select('pagos.*')
            ->join('sucursales', 'sucursales.id', '=', 'pagos.sucursal_id')
            ->where('sucursales.id', $sucursal_id)
            ->whereBetween('pagos.fecha', [$fechaIni, $fechaFin])
            ->get()
        ;
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaIngresoSalida', 'categoria_ingreso_salida_id');
    }
}
