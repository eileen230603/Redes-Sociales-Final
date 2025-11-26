<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use App\Models\EventoEmpresaParticipacion;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class MigrarPatrocinadoresEventos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:migrar-patrocinadores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra patrocinadores de eventos desde el campo JSON a la tabla evento_empresas_participantes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando migración de patrocinadores...');

        $eventos = Evento::all();
        $totalMigrados = 0;
        $totalErrores = 0;

        foreach ($eventos as $evento) {
            $patrocinadores = $evento->patrocinadores;
            
            if (empty($patrocinadores)) {
                continue;
            }

            // Convertir a array si es string
            if (is_string($patrocinadores)) {
                $decoded = json_decode($patrocinadores, true);
                $patrocinadores = is_array($decoded) ? $decoded : [];
            }

            if (!is_array($patrocinadores) || empty($patrocinadores)) {
                continue;
            }

            foreach ($patrocinadores as $empresaId) {
                try {
                    // Verificar que la empresa existe
                    $empresa = Empresa::where('user_id', $empresaId)->first();
                    if (!$empresa) {
                        $this->warn("Empresa con user_id {$empresaId} no encontrada para evento {$evento->id}");
                        $totalErrores++;
                        continue;
                    }

                    // Verificar si ya existe una participación
                    $existe = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                        ->where('empresa_id', $empresaId)
                        ->exists();

                    if (!$existe) {
                        EventoEmpresaParticipacion::create([
                            'evento_id' => $evento->id,
                            'empresa_id' => $empresaId,
                            'estado' => 'asignada',
                            'activo' => true,
                            'tipo_colaboracion' => 'Patrocinador',
                        ]);
                        $totalMigrados++;
                        $this->info("✓ Patrocinador {$empresaId} migrado para evento {$evento->id} - {$evento->titulo}");
                    } else {
                        // Actualizar el tipo si no es patrocinador
                        $participacion = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                            ->where('empresa_id', $empresaId)
                            ->first();
                        if ($participacion && $participacion->tipo_colaboracion !== 'Patrocinador') {
                            $participacion->tipo_colaboracion = 'Patrocinador';
                            $participacion->save();
                            $this->info("✓ Tipo actualizado para patrocinador {$empresaId} en evento {$evento->id}");
                        }
                    }
                } catch (\Throwable $e) {
                    $this->error("Error al migrar patrocinador {$empresaId} para evento {$evento->id}: " . $e->getMessage());
                    $totalErrores++;
                }
            }
        }

        $this->info("\nMigración completada:");
        $this->info("  - Total migrados: {$totalMigrados}");
        $this->info("  - Total errores: {$totalErrores}");

        return 0;
    }
}
