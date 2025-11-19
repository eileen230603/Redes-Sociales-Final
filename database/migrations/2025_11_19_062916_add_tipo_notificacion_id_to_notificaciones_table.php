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
        Schema::table('notificaciones', function (Blueprint $table) {
            // Agregar tipo_notificacion_id (nullable para migración gradual)
            $table->foreignId('tipo_notificacion_id')->nullable()->after('tipo')->constrained('tipos_notificacion')->onDelete('set null');
            
            // Mantener tipo por compatibilidad durante migración
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['tipo_notificacion_id']);
            $table->dropColumn('tipo_notificacion_id');
        });
    }
};
