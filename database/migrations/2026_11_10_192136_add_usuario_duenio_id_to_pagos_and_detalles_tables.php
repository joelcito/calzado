<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['pagos', 'detalles'] as $nombre) {
            Schema::table($nombre, function (Blueprint $table) {
                $table->foreignId('usuario_duenio_id')->nullable()->constrained('users');
            });
        }
        DB::table('detalles')->update([
            'usuario_duenio_id' => DB::raw('(SELECT productos.usuario_duenio_id FROM productos WHERE productos.id = detalles.producto_id)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['pagos', 'detalles'] as $nombre) {
            Schema::table($nombre, function (Blueprint $table) {
                $table->dropConstrainedForeignId('usuario_duenio_id');
            });
        }
    }
};
