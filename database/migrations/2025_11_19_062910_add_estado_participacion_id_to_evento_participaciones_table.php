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
        Schema::table('evento_participaciones', function (Blueprint $table) {
            // Agregar estado_participacion_id (nullable para migración gradual)
            $table->foreignId('estado_participacion_id')->nullable()->after('estado')->constrained('estados_participacion')->onDelete('set null');
            
            // Mantener estado por compatibilidad durante migración
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participaciones', function (Blueprint $table) {
            $table->dropForeign(['estado_participacion_id']);
            $table->dropColumn('estado_participacion_id');
        });
    }
};
