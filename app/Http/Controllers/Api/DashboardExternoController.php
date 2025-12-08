<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\IntegranteExterno;
use App\Models\MegaEventoParticipanteExterno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardExternoController extends Controller
{
    /**
     * Obtener estadísticas generales para el home del usuario externo
     */
    public function estadisticasGenerales(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $externoId = $user->id_usuario;
            
            // Obtener integrante externo
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado'
                ], 404);
            }

            // Obtener TODAS las participaciones del usuario
            $participaciones = EventoParticipacion::where('externo_id', $externoId)
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'tipo_evento', 'fecha_inicio', 'fecha_fin', 'estado', 'ciudad', 'lat', 'lng');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Obtener participaciones en mega eventos
            $participacionesMega = DB::table('mega_evento_participantes_externos')
                ->where('integrante_externo_id', $externoId)
                ->where('activo', true)
                ->count();

            // 1. Historial de Participación (eventos inscritos vs asistidos por mes)
            $historialParticipacion = [];
            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            
            // Últimos 12 meses
            for ($i = 11; $i >= 0; $i--) {
                $fecha = now()->subMonths($i);
                $mesNumero = (int)$fecha->format('n');
                $mesKey = $meses[$mesNumero - 1] . ' ' . $fecha->format('Y');
                
                $inscritos = $participaciones->filter(function($p) use ($fecha) {
                    if (!$p->created_at) return false;
                    return $p->created_at->format('Y-m') === $fecha->format('Y-m');
                })->count();
                
                $asistidos = $participaciones->filter(function($p) use ($fecha) {
                    if (!$p->created_at || !$p->asistio) return false;
                    return $p->created_at->format('Y-m') === $fecha->format('Y-m');
                })->count();
                
                $historialParticipacion[$mesKey] = [
                    'inscritos' => $inscritos,
                    'asistidos' => $asistidos
                ];
            }

            // 2. Estado Actual de Participaciones
            $estadoParticipaciones = [
                'activos' => 0,
                'finalizados' => 0,
                'pendientes' => 0,
                'cancelados' => 0
            ];

            foreach ($participaciones as $p) {
                if ($p->evento) {
                    $fechaFin = $p->evento->fecha_fin ? new \DateTime($p->evento->fecha_fin) : null;
                    $estado = $p->evento->estado;
                    
                    if ($estado === 'cancelado') {
                        $estadoParticipaciones['cancelados']++;
                    } elseif ($estado === 'finalizado' || ($fechaFin && $fechaFin < now())) {
                        $estadoParticipaciones['finalizados']++;
                    } elseif ($p->estado === 'pendiente') {
                        $estadoParticipaciones['pendientes']++;
                    } else {
                        $estadoParticipaciones['activos']++;
                    }
                }
            }

            // 3. Tipo de Eventos Participados
            $tipoEventos = [];
            foreach ($participaciones as $p) {
                if ($p->evento && $p->evento->tipo_evento) {
                    $tipo = $p->evento->tipo_evento;
                    $tipoEventos[$tipo] = ($tipoEventos[$tipo] ?? 0) + 1;
                }
            }

            // 4. Eventos Favoritos o Más Interactuados
            $eventosInteracciones = [];
            foreach ($participaciones as $p) {
                if ($p->evento) {
                    $eventoId = $p->evento->id;
                    $reacciones = EventoReaccion::where('evento_id', $eventoId)->count();
                    $compartidos = EventoCompartido::where('evento_id', $eventoId)->count();
                    $totalInteracciones = $reacciones + $compartidos;
                    
                    if (!isset($eventosInteracciones[$eventoId])) {
                        $eventosInteracciones[$eventoId] = [
                            'titulo' => $p->evento->titulo,
                            'reacciones' => 0,
                            'compartidos' => 0,
                            'total' => 0
                        ];
                    }
                    $eventosInteracciones[$eventoId]['reacciones'] = $reacciones;
                    $eventosInteracciones[$eventoId]['compartidos'] = $compartidos;
                    $eventosInteracciones[$eventoId]['total'] = $totalInteracciones;
                }
            }
            
            // Ordenar por total de interacciones y tomar top 5
            usort($eventosInteracciones, function($a, $b) {
                return $b['total'] - $a['total'];
            });
            $eventosInteracciones = array_slice($eventosInteracciones, 0, 5);

            // 5. Reacciones Realizadas (por mes)
            $reaccionesPorMes = [];
            $reaccionesUsuario = EventoReaccion::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            for ($i = 11; $i >= 0; $i--) {
                $fecha = now()->subMonths($i);
                $mesNumero = (int)$fecha->format('n');
                $mesKey = $meses[$mesNumero - 1] . ' ' . $fecha->format('Y');
                
                $reaccionesPorMes[$mesKey] = $reaccionesUsuario->filter(function($r) use ($fecha) {
                    if (!$r->created_at) return false;
                    return $r->created_at->format('Y-m') === $fecha->format('Y-m');
                })->count();
            }

            // 6. Resumen de Impacto Personal
            $totalEventosInscritos = $participaciones->count();
            $totalEventosAsistidos = $participaciones->where('asistio', true)->count();
            $totalMegaEventosInscritos = $participacionesMega;
            $totalReacciones = $reaccionesUsuario->count();
            
            $totalCompartidos = EventoCompartido::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->count();
            
            $horasAcumuladas = $totalEventosAsistidos * 2;

            // 7. Ubicación de Participación
            $ubicaciones = [];
            foreach ($participaciones as $p) {
                if ($p->evento && $p->evento->ciudad) {
                    $ciudad = $p->evento->ciudad;
                    if (!isset($ubicaciones[$ciudad])) {
                        $ubicaciones[$ciudad] = [
                            'ciudad' => $ciudad,
                            'lat' => $p->evento->lat,
                            'lng' => $p->evento->lng,
                            'cantidad' => 0
                        ];
                    }
                    $ubicaciones[$ciudad]['cantidad']++;
                }
            }
            $ubicaciones = array_values($ubicaciones);

            // Obtener información del usuario
            $nombreUsuario = $integranteExterno 
                ? trim($integranteExterno->nombres . ' ' . ($integranteExterno->apellidos ?? '')) 
                : ($user->nombre_usuario ?? 'Usuario');
            
            $fotoPerfil = $integranteExterno 
                ? ($integranteExterno->foto_perfil_url ?? null) 
                : ($user->foto_perfil_url ?? null);
            
            return response()->json([
                'success' => true,
                'usuario' => [
                    'nombre' => $nombreUsuario,
                    'foto_perfil' => $fotoPerfil,
                ],
                'estadisticas' => [
                    'total_eventos_inscritos' => $totalEventosInscritos,
                    'total_eventos_asistidos' => $totalEventosAsistidos,
                    'total_mega_eventos_inscritos' => $totalMegaEventosInscritos,
                    'total_reacciones' => $totalReacciones,
                    'total_compartidos' => $totalCompartidos,
                    'horas_acumuladas' => $horasAcumuladas,
                ],
                'graficas' => [
                    'historial_participacion' => $historialParticipacion,
                    'estado_participaciones' => $estadoParticipaciones,
                    'tipo_eventos' => $tipoEventos,
                    'eventos_interacciones' => $eventosInteracciones,
                    'reacciones_por_mes' => $reaccionesPorMes,
                    'ubicaciones' => $ubicaciones,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos detallados para las tablas y cards
     */
    public function datosDetallados(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $externoId = $user->id_usuario;
            
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado'
                ], 404);
            }

            // 1. Eventos Inscritos
            $eventosInscritos = EventoParticipacion::where('externo_id', $externoId)
                ->with(['evento' => function($query) {
                    $query->with(['ong' => function($q) {
                        $q->select('user_id', 'nombre_ong', 'foto_perfil');
                    }]);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($participacion) {
                    $estado = $participacion->asistio ? 'asistido' : 'inscrito';
                    
                    if (!$participacion->evento) {
                        return [
                            'id' => $participacion->id,
                            'evento_id' => $participacion->evento_id,
                            'titulo' => 'Evento eliminado',
                            'tipo_evento' => null,
                            'fecha_inicio' => null,
                            'fecha_fin' => null,
                            'estado' => $estado,
                            'asistio' => $participacion->asistio ?? false,
                            'ciudad' => null,
                            'ubicacion' => null,
                            'organizador' => 'No disponible',
                            'imagen' => null,
                            'fecha_inscripcion' => $participacion->created_at,
                            'puntos' => $participacion->puntos ?? 0,
                        ];
                    }
                    
                    return [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'titulo' => $participacion->evento->titulo ?? 'Evento eliminado',
                        'tipo_evento' => $participacion->evento->tipo_evento ?? null,
                        'fecha_inicio' => $participacion->evento->fecha_inicio ?? null,
                        'fecha_fin' => $participacion->evento->fecha_fin ?? null,
                        'estado' => $estado,
                        'asistio' => $participacion->asistio ?? false,
                        'ciudad' => $participacion->evento->ciudad ?? null,
                        'ubicacion' => $participacion->evento->ubicacion ?? null,
                        'organizador' => ($participacion->evento->ong && $participacion->evento->ong->nombre_ong) 
                            ? $participacion->evento->ong->nombre_ong 
                            : 'No disponible',
                        'imagen' => (!empty($participacion->evento->imagenes) && is_array($participacion->evento->imagenes)) 
                            ? ($participacion->evento->imagenes[0] ?? null) 
                            : null,
                        'fecha_inscripcion' => $participacion->created_at,
                        'puntos' => $participacion->puntos ?? 0,
                    ];
                })
                ->filter();

            // 2. Eventos Asistidos
            $eventosAsistidos = EventoParticipacion::where('externo_id', $externoId)
                ->where('asistio', true)
                ->with(['evento' => function($query) {
                    $query->with(['ong' => function($q) {
                        $q->select('user_id', 'nombre_ong', 'foto_perfil');
                    }]);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($participacion) {
                    if (!$participacion->evento) {
                        return null;
                    }
                    
                    return [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'titulo' => $participacion->evento->titulo,
                        'tipo_evento' => $participacion->evento->tipo_evento,
                        'fecha_inicio' => $participacion->evento->fecha_inicio,
                        'fecha_fin' => $participacion->evento->fecha_fin,
                        'ciudad' => $participacion->evento->ciudad,
                        'ubicacion' => $participacion->evento->ubicacion,
                        'organizador' => ($participacion->evento->ong && $participacion->evento->ong->nombre_ong) 
                            ? $participacion->evento->ong->nombre_ong 
                            : 'No disponible',
                        'imagen' => (!empty($participacion->evento->imagenes) && is_array($participacion->evento->imagenes)) 
                            ? ($participacion->evento->imagenes[0] ?? null) 
                            : null,
                        'fecha_inscripcion' => $participacion->created_at,
                        'puntos' => $participacion->puntos ?? 0,
                    ];
                })
                ->filter();

            // 3. Reacciones
            $reacciones = EventoReaccion::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'tipo_evento', 'fecha_inicio');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($reaccion) {
                    return [
                        'id' => $reaccion->id,
                        'evento_id' => $reaccion->evento_id,
                        'evento_titulo' => $reaccion->evento ? $reaccion->evento->titulo : 'Evento eliminado',
                        'fecha_reaccion' => $reaccion->created_at,
                    ];
                });

            // 4. Compartidos
            $compartidos = EventoCompartido::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'tipo_evento');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($compartido) {
                    return [
                        'id' => $compartido->id,
                        'evento_id' => $compartido->evento_id,
                        'evento_titulo' => $compartido->evento ? $compartido->evento->titulo : 'Evento eliminado',
                        'metodo' => $compartido->metodo,
                        'fecha_compartido' => $compartido->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'eventos_inscritos' => $eventosInscritos,
                'eventos_asistidos' => $eventosAsistidos,
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener datos detallados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener eventos disponibles para participar
     */
    public function eventosDisponibles(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $externoId = $user->id_usuario;
            
            // Obtener IDs de eventos en los que el usuario ya está participando
            $eventosParticipando = EventoParticipacion::where('externo_id', $externoId)
                ->pluck('evento_id')
                ->toArray();
            
            // Obtener eventos disponibles (publicados y no participando)
            $eventos = Evento::where('estado', 'publicado')
                ->whereNotIn('id', $eventosParticipando)
                ->with(['ong' => function($q) {
                    $q->select('user_id', 'nombre_ong', 'foto_perfil');
                }])
                ->orderBy('fecha_inicio', 'asc')
                ->get()
                ->map(function($evento) {
                    return [
                        'id' => $evento->id,
                        'titulo' => $evento->titulo,
                        'descripcion' => $evento->descripcion,
                        'tipo_evento' => $evento->tipo_evento,
                        'fecha_inicio' => $evento->fecha_inicio,
                        'fecha_fin' => $evento->fecha_fin,
                        'ciudad' => $evento->ciudad,
                        'ubicacion' => $evento->ubicacion,
                        'imagen' => (!empty($evento->imagenes) && is_array($evento->imagenes)) 
                            ? ($evento->imagenes[0] ?? null) 
                            : null,
                        'organizador' => $evento->ong->nombre_ong ?? 'No disponible',
                        'estado' => $evento->estado,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'eventos' => $eventos,
                'count' => $eventos->count(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener eventos disponibles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar PDF completo del dashboard
     */
    public function descargarPdfCompleto(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $externoId = $user->id_usuario;
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado'
                ], 404);
            }

            // Obtener estadísticas
            $estadisticasResult = $this->estadisticasGenerales($request);
            $estadisticasData = json_decode($estadisticasResult->getContent(), true);
            
            if (!$estadisticasData['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al obtener estadísticas'
                ], 500);
            }

            // Obtener datos detallados
            $detallesResult = $this->datosDetallados($request);
            $detallesData = json_decode($detallesResult->getContent(), true);
            
            if (!$detallesData['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al obtener datos detallados'
                ], 500);
            }

            // Para mobile, retornar los datos en JSON para que la app genere el PDF
            // O si DomPDF está disponible, generar el PDF
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.dashboard-externo', [
                    'usuario' => $estadisticasData['usuario'],
                    'estadisticas' => $estadisticasData['estadisticas'],
                    'graficas' => $estadisticasData['graficas'],
                    'eventos_inscritos' => $detallesData['eventos_inscritos'] ?? [],
                    'eventos_asistidos' => $detallesData['eventos_asistidos'] ?? [],
                    'reacciones' => $detallesData['reacciones'] ?? [],
                    'compartidos' => $detallesData['compartidos'] ?? [],
                ]);
                
                return $pdf->download('dashboard-externo-' . $externoId . '.pdf');
            } else {
                // Retornar datos para que la app genere el PDF
                return response()->json([
                    'success' => true,
                    'data' => [
                        'usuario' => $estadisticasData['usuario'],
                        'estadisticas' => $estadisticasData['estadisticas'],
                        'graficas' => $estadisticasData['graficas'],
                        'eventos_inscritos' => $detallesData['eventos_inscritos'] ?? [],
                        'eventos_asistidos' => $detallesData['eventos_asistidos'] ?? [],
                        'reacciones' => $detallesData['reacciones'] ?? [],
                        'compartidos' => $detallesData['compartidos'] ?? [],
                    ],
                    'message' => 'Datos listos para generar PDF en la aplicación'
                ]);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}

