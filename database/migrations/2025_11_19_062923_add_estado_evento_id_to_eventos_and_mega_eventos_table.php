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
        // Para eventos
        Schema::table('eventos', function (Blueprint $table) {
            $table->foreignId('estado_evento_id')->nullable()->after('estado')->constrained('estados_evento')->onDelete('set null');
            // Mantener estado enum por compatibilidad
        });

        // Para mega_eventos
        Schema::table('mega_eventos', function (Blueprint $table) {
            $table->foreignId('estado_evento_id')->nullable()->after('estado')->constrained('estados_evento')->onDelete('set null');
            // Mantener estado string por compatibilidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropForeign(['estado_evento_id']);
            $table->dropColumn('estado_evento_id');
        });

        Schema::table('mega_eventos', function (Blueprint $table) {
            $table->dropForeign(['estado_evento_id']);
            $table->dropColumn('estado_evento_id');
        });
    }
};
