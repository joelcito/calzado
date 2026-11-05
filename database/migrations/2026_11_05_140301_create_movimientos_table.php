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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('producto_id')->references('id')->on('productos');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('detalle_id')->references('id')->on('detalles');
            $table->unsignedBigInteger('detalle_id')->nullable();
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();

            $table->decimal('precio_compra', 12, 2)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->decimal('ingreso', 12, 2)->nullable();
            $table->decimal('salida', 12, 2)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->text('descripcion')->nullable();

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
        Schema::dropIfExists('movimientos');
    }
};
