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
        Schema::table('evento_participantes_no_registrados', function (Blueprint $table) {
            // Código único de ticket para controlar asistencia
            $table->string('ticket_codigo', 100)
                ->nullable()
                ->after('telefono')
                ->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participantes_no_registrados', function (Blueprint $table) {
            $table->dropUnique(['ticket_codigo']);
            $table->dropColumn('ticket_codigo');
        });
    }
};
