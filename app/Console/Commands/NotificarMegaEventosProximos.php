<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MegaEvento;
use App\Models\Notificacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificarMegaEventosProximos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mega-eventos:notificar-5min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a participantes y patrocinadores cuando un mega evento está por empezar (5 minutos antes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Iniciando proceso de notificación de mega eventos próximos (5 minutos)...');

        $ahora = Carbon::now();
        // Notificar mega eventos que inician en 5 minutos (entre 4 y 6 minutos)
        $tiempoNotificacion = $ahora->copy()->addMinutes(5);
        $margenMinutos = 1; // Margen de 1 minuto antes y después
        
        $megaEventosProximos = DB::table('mega_eventos')
            ->where('activo', true)
            ->whereNotNull('fecha_inicio')
            ->whereBetween('fecha_inicio', [
                $tiempoNotificacion->copy()->subMinutes($margenMinutos)->format('Y-m-d H:i:s'),
                $tiempoNotificacion->copy()->addMinutes($margenMinutos)->format('Y-m-d H:i:s')
            ])
            ->get();

        $contadorNotificaciones = 0;

        foreach ($megaEventosProximos as $megaEvento) {
            $this->line("📅 Procesando mega evento: '{$megaEvento->titulo}' (ID: {$megaEvento->mega_evento_id})");
            
            try {
                $megaEventoModel = MegaEvento::find($megaEvento->mega_evento_id);
                if (!$megaEventoModel) {
                    $this->warn("  ⚠️  Mega evento no encontrado, omitiendo...");
                    continue;
                }

                $fechaInicio = Carbon::parse($megaEvento->fecha_inicio);
                $tiempoRestante = $ahora->diffInMinutes($fechaInicio);
                
                // Verificar si ya se notificó (evitar notificaciones duplicadas)
                $yaNotificado = Notificacion::where('tipo', 'alerta_mega_evento_5min')
                    ->whereRaw("mensaje LIKE ?", ["%mega evento \"{$megaEvento->titulo}\"%"])
                    ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                    ->exists();
                
                if ($yaNotificado) {
                    $this->line("  ⏭️  Ya se notificó recientemente, omitiendo...");
                    continue;
                }

                // 1. Notificar a participantes aprobados
                try {
                    $participantes = DB::table('mega_evento_participantes_externos')
                        ->where('mega_evento_id', $megaEvento->mega_evento_id)
                        ->where('estado_participacion', 'aprobada')
                        ->where('activo', true)
                        ->get();

                    foreach ($participantes as $participante) {
                        try {
                            // Verificar si ya se envió la notificación a este participante
                            $notificacionExistente = DB::table('notificaciones')
                                ->where('externo_id', $participante->integrante_externo_id)
                                ->where('tipo', 'alerta_mega_evento_5min')
                                ->whereRaw("mensaje LIKE ?", ["%mega evento \"{$megaEvento->titulo}\"%"])
                                ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                                ->exists();
                            
                            if (!$notificacionExistente) {
                                Notificacion::create([
                                    'ong_id' => $megaEvento->ong_organizadora_principal,
                                    'evento_id' => null,
                                    'externo_id' => $participante->integrante_externo_id,
                                    'tipo' => 'alerta_mega_evento_5min',
                                    'titulo' => '¡Mega Evento por comenzar!',
                                    'mensaje' => "El mega evento \"{$megaEvento->titulo}\" iniciará en 5 minutos. ¡Prepárate!",
                                    'leida' => false
                                ]);
                                $contadorNotificaciones++;
                            }
                        } catch (\Throwable $e) {
                            $this->warn("  ⚠️  Error al notificar participante (ID: {$participante->integrante_externo_id}): " . $e->getMessage());
                        }
                    }
                    if ($participantes->count() > 0) {
                        $this->line("  ✅ Notificaciones creadas para {$participantes->count()} participante(s)");
                    }
                } catch (\Throwable $e) {
                    $this->warn("  ⚠️  Error al procesar participantes: " . $e->getMessage());
                }

                // 2. Notificar a patrocinadores (empresas)
                try {
                    $patrocinadores = DB::table('mega_evento_patrocinadores')
                        ->where('mega_evento_id', $megaEvento->mega_evento_id)
                        ->where('activo', true)
                        ->get();

                    foreach ($patrocinadores as $patrocinador) {
                        try {
                            $empresa = \App\Models\Empresa::find($patrocinador->empresa_id);
                            if ($empresa && $empresa->user_id) {
                                // Verificar si ya se envió la notificación a este patrocinador
                                $notificacionExistente = DB::table('notificaciones')
                                    ->where('empresa_id', $empresa->empresa_id)
                                    ->where('tipo', 'alerta_mega_evento_5min')
                                    ->whereRaw("mensaje LIKE ?", ["%mega evento \"{$megaEvento->titulo}\"%"])
                                    ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                                    ->exists();
                                
                                if (!$notificacionExistente) {
                                    Notificacion::create([
                                        'ong_id' => $megaEvento->ong_organizadora_principal,
                                        'evento_id' => null,
                                        'empresa_id' => $empresa->empresa_id,
                                        'tipo' => 'alerta_mega_evento_5min',
                                        'titulo' => '¡Mega Evento por comenzar!',
                                        'mensaje' => "El mega evento \"{$megaEvento->titulo}\" que patrocinas iniciará en 5 minutos.",
                                        'leida' => false
                                    ]);
                                    $contadorNotificaciones++;
                                }
                            }
                        } catch (\Throwable $e) {
                            $this->warn("  ⚠️  Error al notificar patrocinador (ID: {$patrocinador->empresa_id}): " . $e->getMessage());
                        }
                    }
                    if ($patrocinadores->count() > 0) {
                        $this->line("  ✅ Notificaciones creadas para {$patrocinadores->count()} patrocinador(es)");
                    }
                } catch (\Throwable $e) {
                    $this->warn("  ⚠️  Error al procesar patrocinadores: " . $e->getMessage());
                }

            } catch (\Throwable $e) {
                $this->warn("  ⚠️  Error procesando mega evento {$megaEvento->mega_evento_id}: " . $e->getMessage());
            }
        }

        if ($contadorNotificaciones > 0) {
            $this->info("✅ Se crearon {$contadorNotificaciones} notificación(es) para mega eventos próximos.");
        } else {
            $this->info("ℹ️  No hay mega eventos próximos a notificar en este momento.");
        }

        return Command::SUCCESS;
    }
}

