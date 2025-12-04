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
            // Modo de asistencia: QR, Manual, Online, Confirmacion
            $table->string('modo_asistencia', 50)->nullable()->after('checkout_at');
            
            // Observaciones del organizador
            $table->text('observaciones')->nullable()->after('modo_asistencia');
            
            // Usuario que registró la asistencia (ONG)
            $table->unsignedBigInteger('registrado_por')->nullable()->after('observaciones');
            $table->foreign('registrado_por')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('set null');
            
            // Estado de asistencia: asistido, no_asistido, en_revision
            $table->string('estado_asistencia', 50)->default('no_asistido')->after('registrado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participaciones', function (Blueprint $table) {
            $table->dropForeign(['registrado_por']);
            $table->dropColumn(['modo_asistencia', 'observaciones', 'registrado_por', 'estado_asistencia']);
        });
    }
};
