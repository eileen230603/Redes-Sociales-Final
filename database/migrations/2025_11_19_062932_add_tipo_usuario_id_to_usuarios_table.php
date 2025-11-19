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
        Schema::table('usuarios', function (Blueprint $table) {
            // Agregar tipo_usuario_id (nullable para migración gradual)
            $table->foreignId('tipo_usuario_id')->nullable()->after('tipo_usuario')->constrained('tipos_usuario')->onDelete('set null');
            
            // Mantener tipo_usuario por compatibilidad durante migración
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['tipo_usuario_id']);
            $table->dropColumn('tipo_usuario_id');
        });
    }
};
