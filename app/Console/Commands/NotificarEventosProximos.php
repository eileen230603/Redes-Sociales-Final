<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoEmpresaParticipacion;
use App\Models\Notificacion;
use Carbon\Carbon;

class NotificarEventosProximos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:notificar-proximos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a participantes, patrocinadores y creador cuando un evento está por empezar (1 hora antes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Iniciando proceso de notificación de eventos próximos...');

        $ahora = Carbon::now();
        // Notificar eventos que inician en 1 hora (entre 55 y 65 minutos)
        $tiempoNotificacion = $ahora->copy()->addHour();
        $margenMinutos = 5; // Margen de 5 minutos antes y después
        
        $eventosProximos = Evento::with('ong')
            ->where('estado', 'publicado')
            ->whereNotNull('fecha_inicio')
            ->whereBetween('fecha_inicio', [
                $tiempoNotificacion->copy()->subMinutes($margenMinutos),
                $tiempoNotificacion->copy()->addMinutes($margenMinutos)
            ])
            ->get();

        $contadorNotificaciones = 0;

        foreach ($eventosProximos as $evento) {
            $this->line("📅 Procesando evento: '{$evento->titulo}' (ID: {$evento->id})");
            
            // Verificar si ya se notificó (evitar notificaciones duplicadas)
            $yaNotificado = Notificacion::where('evento_id', $evento->id)
                ->where('tipo', 'evento_proximo')
                ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                ->exists();
            
            if ($yaNotificado) {
                $this->line("  ⏭️  Ya se notificó recientemente, omitiendo...");
                continue;
            }

            $fechaInicio = Carbon::parse($evento->fecha_inicio);
            $tiempoRestante = $ahora->diffInMinutes($fechaInicio);
            $mensajeTiempo = $tiempoRestante >= 60 
                ? "dentro de " . round($tiempoRestante / 60) . " hora(s)"
                : "en " . $tiempoRestante . " minuto(s)";

            // 1. Notificar al creador del evento (ONG)
            if ($evento->ong_id) {
                try {
                    Notificacion::create([
                        'ong_id' => $evento->ong_id,
                        'evento_id' => $evento->id,
                        'externo_id' => null,
                        'tipo' => 'evento_proximo',
                        'titulo' => 'Evento por Iniciar',
                        'mensaje' => "Tu evento '{$evento->titulo}' comenzará {$mensajeTiempo}. ¡Prepárate!",
                        'leida' => false
                    ]);
                    $contadorNotificaciones++;
                    $this->line("  ✅ Notificación creada para la ONG creadora (ID: {$evento->ong_id})");
                } catch (\Throwable $e) {
                    $this->warn("  ⚠️  Error al notificar a la ONG: " . $e->getMessage());
                }
            }

            // 2. Notificar a participantes aprobados
            try {
                $participaciones = EventoParticipacion::where('evento_id', $evento->id)
                    ->where('estado', 'aprobada')
                    ->whereNotNull('externo_id')
                    ->get();

                foreach ($participaciones as $participacion) {
                    try {
                        Notificacion::create([
                            'ong_id' => null,
                            'evento_id' => $evento->id,
                            'externo_id' => $participacion->externo_id,
                            'tipo' => 'evento_proximo',
                            'titulo' => 'Evento por Iniciar',
                            'mensaje' => "El evento '{$evento->titulo}' en el que participas comenzará {$mensajeTiempo}. ¡No olvides asistir!",
                            'leida' => false
                        ]);
                        $contadorNotificaciones++;
                    } catch (\Throwable $e) {
                        $this->warn("  ⚠️  Error al notificar participante (ID: {$participacion->externo_id}): " . $e->getMessage());
                    }
                }
                if ($participaciones->count() > 0) {
                    $this->line("  ✅ Notificaciones creadas para {$participaciones->count()} participante(s)");
                }
            } catch (\Throwable $e) {
                $this->warn("  ⚠️  Error al procesar participantes: " . $e->getMessage());
            }

            // 3. Notificar a patrocinadores (empresas)
            try {
                $patrocinadores = $this->safeArray($evento->patrocinadores);
                foreach ($patrocinadores as $patrocinadorId) {
                    if (is_numeric($patrocinadorId)) {
                        try {
                            // Buscar empresa por user_id
                            $empresa = \App\Models\Empresa::where('user_id', $patrocinadorId)->first();
                            if ($empresa) {
                                Notificacion::create([
                                    'ong_id' => null,
                                    'evento_id' => $evento->id,
                                    'externo_id' => $patrocinadorId, // user_id de la empresa
                                    'tipo' => 'evento_proximo',
                                    'titulo' => 'Evento por Iniciar',
                                    'mensaje' => "El evento '{$evento->titulo}' que patrocinas comenzará {$mensajeTiempo}. ¡Prepárate!",
                                    'leida' => false
                                ]);
                                $contadorNotificaciones++;
                            }
                        } catch (\Throwable $e) {
                            $this->warn("  ⚠️  Error al notificar patrocinador (ID: {$patrocinadorId}): " . $e->getMessage());
                        }
                    }
                }
                if (count($patrocinadores) > 0) {
                    $this->line("  ✅ Notificaciones creadas para " . count($patrocinadores) . " patrocinador(es)");
                }
            } catch (\Throwable $e) {
                $this->warn("  ⚠️  Error al procesar patrocinadores: " . $e->getMessage());
            }

            // 4. Notificar a empresas colaboradoras
            try {
                $empresasColaboradoras = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                    ->where('activo', true)
                    ->get();

                foreach ($empresasColaboradoras as $colaboracion) {
                    try {
                        $empresa = \App\Models\Empresa::where('user_id', $colaboracion->empresa_id)->first();
                        if ($empresa) {
                            Notificacion::create([
                                'ong_id' => null,
                                'evento_id' => $evento->id,
                                'externo_id' => $colaboracion->empresa_id, // user_id de la empresa
                                'tipo' => 'evento_proximo',
                                'titulo' => 'Evento por Iniciar',
                                'mensaje' => "El evento '{$evento->titulo}' en el que colaboras comenzará {$mensajeTiempo}. ¡Prepárate!",
                                'leida' => false
                            ]);
                            $contadorNotificaciones++;
                        }
                    } catch (\Throwable $e) {
                        $this->warn("  ⚠️  Error al notificar empresa colaboradora (ID: {$colaboracion->empresa_id}): " . $e->getMessage());
                    }
                }
                if ($empresasColaboradoras->count() > 0) {
                    $this->line("  ✅ Notificaciones creadas para {$empresasColaboradoras->count()} empresa(s) colaboradora(s)");
                }
            } catch (\Throwable $e) {
                $this->warn("  ⚠️  Error al procesar empresas colaboradoras: " . $e->getMessage());
            }
        }

        if ($contadorNotificaciones > 0) {
            $this->info("✅ Se crearon {$contadorNotificaciones} notificación(es) para eventos próximos.");
        } else {
            $this->info("ℹ️  No hay eventos próximos a notificar en este momento.");
        }

        return Command::SUCCESS;
    }

    private function safeArray($value)
    {
        if (is_array($value)) return $value;
        if ($value === null) return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
