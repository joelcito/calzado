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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('factura_id')->references('id')->on('facturas');
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreign('caja_id')->references('id')->on('cajas');
            $table->unsignedBigInteger('caja_id')->nullable();
            $table->foreign('categoria_ingreso_salida_id')->references('id')->on('categoria_ingreso_salidas');
            $table->unsignedBigInteger('categoria_ingreso_salida_id')->nullable();

            $table->decimal('monto', 12, 2)->nullable();
            $table->decimal('cambio', 12, 2)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('apertura_caja')->nullable();
            $table->string('tipo_pago')->nullable();

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
        Schema::dropIfExists('pagos');
    }
};
