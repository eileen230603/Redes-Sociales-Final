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
            // Código único de ticket para controlar asistencia
            $table->string('ticket_codigo', 100)
                ->nullable()
                ->after('estado_participacion_id')
                ->unique();

            // Control de asistencia
            $table->timestamp('checkin_at')->nullable()->after('asistio');
            $table->timestamp('checkout_at')->nullable()->after('checkin_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participaciones', function (Blueprint $table) {
            $table->dropUnique(['ticket_codigo']);
            $table->dropColumn(['ticket_codigo', 'checkin_at', 'checkout_at']);
        });
    }
};


