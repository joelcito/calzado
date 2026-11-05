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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('categoria_producto_id')->references('id')->on('categoria_productos');
            $table->unsignedBigInteger('categoria_producto_id')->nullable();

            $table->string('nombre')->nullable();
            $table->string('codigo')->nullable();
            $table->decimal('minimo_stock', 12, 2)->nullable();
            $table->decimal('precio_compra', 12, 2)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->boolean('control_stock')->nullable();
            $table->string('tipo')->nullable();
            $table->string('imagen')->nullable();

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
        Schema::dropIfExists('productos');
    }
};
