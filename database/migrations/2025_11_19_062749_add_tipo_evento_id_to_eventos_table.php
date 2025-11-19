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
        Schema::table('eventos', function (Blueprint $table) {
            // Agregar columna tipo_evento_id (nullable para migración gradual)
            $table->foreignId('tipo_evento_id')->nullable()->after('tipo_evento')->constrained('tipos_evento')->onDelete('set null');
            
            // Mantener tipo_evento por compatibilidad durante migración
            // Se puede eliminar después de migrar todos los datos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropForeign(['tipo_evento_id']);
            $table->dropColumn('tipo_evento_id');
        });
    }
};
