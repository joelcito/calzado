<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'facturas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'cufd_id',
        'sucursal_id',
        'cliente_id',
        'mascota_id',
        'chofer_id',
        'fecha',
        'nit',
        'razon_social',
        'numero_factura',
        'numero_recibo',
        'numero_cafc',
        'facturado',
        'total',
        'tipo_atencion',
        'hora_llegada',
        'monto_total_subjeto_iva',
        'descuento_adicional',
        'cuf',
        'producto_xml',
        'codigo_descripcion',
        'codigo_recepcion',
        'codigo_transaccion',
        'descripcion',
        'estado_pago',
        'tipo_factura',
        'uso_cafc',
        'monto_gift_card',
        'estado',
        'deleted_at'
    ];

    public function usuarioCreador()
    {
        return $this->belongsTo('App\Models\User', 'usuario_creador_id');
    }

    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function chofer()
    {
        return $this->belongsTo(User::class, 'chofer_id');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'factura_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }
}
