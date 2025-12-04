<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Corrige valores null en los campos de evento_participaciones
     */
    public function up(): void
    {
        // Actualizar estado_asistencia a 'no_asistido' si es null
        DB::table('evento_participaciones')
            ->whereNull('estado_asistencia')
            ->update(['estado_asistencia' => 'no_asistido']);

        // Actualizar estado_participacion_id basado en el campo estado
        // Solo si existe la tabla estados_participacion y hay estados configurados
        if (Schema::hasTable('estados_participacion')) {
            // Mapear estados a estado_participacion_id
            $estadosMap = [
                'pendiente' => DB::table('estados_participacion')->where('codigo', 'pendiente')->value('id'),
                'aprobada' => DB::table('estados_participacion')->where('codigo', 'aprobada')->value('id'),
                'rechazada' => DB::table('estados_participacion')->where('codigo', 'rechazada')->value('id'),
            ];

            foreach ($estadosMap as $estado => $estadoId) {
                if ($estadoId) {
                    DB::table('evento_participaciones')
                        ->whereNull('estado_participacion_id')
                        ->where('estado', $estado)
                        ->update(['estado_participacion_id' => $estadoId]);
                }
            }
        }

        // checkin_at y checkout_at deben permanecer null hasta que se registre asistencia/salida
        // Esto es correcto y no necesita corrección
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay necesidad de revertir estos cambios
        // Los valores null son válidos en estos campos
    }
};
