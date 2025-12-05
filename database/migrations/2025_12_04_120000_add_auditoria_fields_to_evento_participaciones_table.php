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
            // IP desde donde se registró la asistencia
            $table->string('ip_registro', 45)->nullable()->after('estado_asistencia');
            
            // Ubicación aproximada (puede obtenerse de servicios de geolocalización)
            $table->string('ubicacion_aproximada', 255)->nullable()->after('ip_registro');
            
            // Fecha de última modificación del estado de asistencia
            $table->timestamp('fecha_modificacion')->nullable()->after('ubicacion_aproximada');
            
            // Usuario que modificó el estado de asistencia (puede ser ONG o el mismo usuario)
            $table->unsignedBigInteger('usuario_modifico')->nullable()->after('fecha_modificacion');
            $table->foreign('usuario_modifico')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_modifico']);
            $table->dropColumn(['ip_registro', 'ubicacion_aproximada', 'fecha_modificacion', 'usuario_modifico']);
        });
    }
};
