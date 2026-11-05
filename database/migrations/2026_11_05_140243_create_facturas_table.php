<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->unsignedBigInteger('cliente_id')->nullable();

            $table->dateTime('fecha')->nullable();
            $table->string('nit')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('numero_factura')->nullable();
            $table->string('numero_recibo')->nullable();
            $table->string('numero_cafc')->nullable();
            $table->string('facturado')->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->decimal('monto_total_subjeto_iva', 12, 2)->nullable();
            $table->decimal('descuento_adicional', 12, 2)->nullable();
            $table->string('cuf')->nullable();
            $table->text('productos_xml')->nullable();
            $table->string('codigo_descripcion')->nullable();
            $table->string('codigo_recepcion')->nullable();
            $table->string('codigo_transaccion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tipo_factura')->nullable();
            $table->string('uso_cafc')->nullable();
            $table->decimal('monto_gift_card', 12, 2)->nullable();
            $table->string('estado_pago', 10)->nullable();
            $table->string('tipo_atencion')->nullable();
            $table->string('hora_llegada')->nullable();
            $table->string('hora_entrega')->nullable();

            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
