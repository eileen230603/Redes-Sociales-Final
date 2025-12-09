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
        Schema::table('mega_evento_participantes_externos', function (Blueprint $table) {
            // Campo asistio (ya existe en no_registrados, lo agregamos aquí también)
            $table->boolean('asistio')->default(false)->after('activo');
            
            // Modo de asistencia: QR, Manual, Online, Confirmacion
            $table->string('modo_asistencia', 50)->nullable()->after('asistio');
            
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
            
            // Fechas de check-in y check-out
            $table->dateTime('checkin_at')->nullable()->after('estado_asistencia');
            $table->dateTime('checkout_at')->nullable()->after('checkin_at');
            
            // Código de ticket único para control de asistencia
            $table->string('ticket_codigo', 100)->unique()->nullable()->after('checkout_at');
            
            // Comentario al marcar asistencia
            $table->text('comentario_asistencia')->nullable()->after('ticket_codigo');
            
            // IP y ubicación aproximada al registrar asistencia
            $table->string('ip_registro', 45)->nullable()->after('comentario_asistencia');
            $table->string('ubicacion_aproximada', 255)->nullable()->after('ip_registro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mega_evento_participantes_externos', function (Blueprint $table) {
            $table->dropForeign(['registrado_por']);
            $table->dropColumn([
                'asistio',
                'modo_asistencia',
                'observaciones',
                'registrado_por',
                'estado_asistencia',
                'checkin_at',
                'checkout_at',
                'ticket_codigo',
                'comentario_asistencia',
                'ip_registro',
                'ubicacion_aproximada'
            ]);
        });
    }
};
