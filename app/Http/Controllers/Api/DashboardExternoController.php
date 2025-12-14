<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\IntegranteExterno;
use App\Models\MegaEventoParticipanteExterno;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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

            // Obtener TODAS las participaciones del usuario (sin filtros adicionales)
            $participaciones = EventoParticipacion::where('externo_id', $externoId)
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'tipo_evento', 'fecha_inicio', 'fecha_fin', 'estado', 'ciudad', 'lat', 'lng');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info('Dashboard Externo - Participaciones encontradas:', [
                'externo_id' => $externoId,
                'total_participaciones' => $participaciones->count(),
                'participaciones' => $participaciones->map(function($p) {
                    return [
                        'id' => $p->id,
                        'evento_id' => $p->evento_id,
                        'created_at' => $p->created_at,
                        'asistio' => $p->asistio
                    ];
                })
            ]);

            // Obtener participaciones en mega eventos
            $participacionesMega = MegaEventoParticipanteExterno::where('integrante_externo_id', $externoId)
                ->where('activo', true)
                ->with(['megaEvento' => function($query) {
                    $query->select('mega_evento_id', 'titulo', 'categoria', 'fecha_inicio', 'fecha_fin', 'ubicacion');
                }])
                ->orderBy('fecha_registro', 'desc')
                ->get();

            // 1. Historial de Participación (eventos inscritos vs asistidos por mes)
            $historialParticipacion = [];
            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            
            // Últimos 12 meses
            for ($i = 11; $i >= 0; $i--) {
                $fecha = now()->subMonths($i);
                $mesNumero = (int)$fecha->format('n'); // n = mes sin ceros iniciales (1-12)
                $mesKey = $meses[$mesNumero - 1] . ' ' . $fecha->format('Y');
                
                // Filtrar por mes de creación de la participación
                $inscritos = $participaciones->filter(function($p) use ($fecha) {
                    if (!$p->created_at) return false;
                    return $p->created_at->format('Y-m') === $fecha->format('Y-m');
                })->count();
                
                // Filtrar por mes de creación Y que haya asistido
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

            // 4. Eventos Favoritos o Más Interactuados (top 5 eventos con más interacciones)
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
            
            \Log::info('Dashboard Externo - Reacciones encontradas:', [
                'externo_id' => $externoId,
                'total_reacciones' => $reaccionesUsuario->count()
            ]);
            
            for ($i = 11; $i >= 0; $i--) {
                $fecha = now()->subMonths($i);
                $mesNumero = (int)$fecha->format('n'); // n = mes sin ceros iniciales (1-12)
                $mesKey = $meses[$mesNumero - 1] . ' ' . $fecha->format('Y');
                
                $reaccionesPorMes[$mesKey] = $reaccionesUsuario->filter(function($r) use ($fecha) {
                    if (!$r->created_at) return false;
                    return $r->created_at->format('Y-m') === $fecha->format('Y-m');
                })->count();
            }

            // 6. Resumen de Impacto Personal
            // Total de eventos inscritos (TODOS los eventos en los que el usuario está inscrito)
            $totalEventosInscritos = $participaciones->count();
            
            // Total de eventos asistidos (TODOS los eventos a los que el usuario asistió)
            $totalEventosAsistidos = $participaciones->where('asistio', true)->count();
            
            // Total de participaciones en mega eventos
            $totalMegaEventosInscritos = $participacionesMega->count();
            
            // Total de reacciones (TODAS las reacciones que el usuario ha hecho)
            $totalReacciones = $reaccionesUsuario->count();
            
            \Log::info('Dashboard Externo - Totales calculados:', [
                'total_eventos_inscritos' => $totalEventosInscritos,
                'total_eventos_asistidos' => $totalEventosAsistidos,
                'total_mega_eventos_inscritos' => $totalMegaEventosInscritos,
                'total_reacciones' => $totalReacciones
            ]);
            
            // Total de compartidos
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
            
            // Calcular horas acumuladas (estimado: 2 horas por evento asistido)
            $horasAcumuladas = $totalEventosAsistidos * 2;

            // 7. Ubicación de Participación (ciudades únicas)
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
            
            $response = [
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
                'debug' => [
                    'externo_id' => $externoId,
                    'total_participaciones' => $participaciones->count(),
                    'total_participaciones_mega' => $participacionesMega->count(),
                    'total_reacciones_raw' => $reaccionesUsuario->count(),
                    'historial_participacion_keys' => array_keys($historialParticipacion),
                    'reacciones_por_mes_keys' => array_keys($reaccionesPorMes),
                    'historial_participacion_sample' => array_slice($historialParticipacion, -3, 3, true),
                    'reacciones_por_mes_sample' => array_slice($reaccionesPorMes, -3, 3, true),
                ]
            ];
            
            \Log::info('Dashboard Externo - Respuesta completa:', [
                'total_eventos_inscritos' => $totalEventosInscritos,
                'total_eventos_asistidos' => $totalEventosAsistidos,
                'total_reacciones' => $totalReacciones,
                'historial_participacion_count' => count($historialParticipacion),
                'reacciones_por_mes_count' => count($reaccionesPorMes)
            ]);
            
            return response()->json($response);

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
            
            // Obtener integrante externo
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado'
                ], 404);
            }

            // 1. Eventos Inscritos (todos los eventos en los que está inscrito)
            $eventosInscritos = EventoParticipacion::where('externo_id', $externoId)
                ->with(['evento' => function($query) {
                    $query->with(['ong' => function($q) {
                        $q->select('user_id', 'nombre_ong', 'foto_perfil');
                    }]);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($participacion) {
                    // Determinar estado basado en asistio
                    $estado = $participacion->asistio ? 'asistido' : 'inscrito';
                    
                    // Manejar caso cuando el evento fue eliminado
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
                ->filter(); // Eliminar nulls si hay algún problema

            // 2. Eventos Asistidos (solo los que asistió)
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
                    // Manejar caso cuando el evento fue eliminado
                    if (!$participacion->evento) {
                        return [
                            'id' => $participacion->id,
                            'evento_id' => $participacion->evento_id,
                            'titulo' => 'Evento eliminado',
                            'tipo_evento' => null,
                            'fecha_inicio' => null,
                            'fecha_fin' => null,
                            'organizador' => 'No disponible',
                            'ciudad' => null,
                            'ubicacion' => null,
                            'imagen' => null,
                            'fecha_asistencia' => $participacion->created_at,
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
                        'organizador' => ($participacion->evento->ong && $participacion->evento->ong->nombre_ong) 
                            ? $participacion->evento->ong->nombre_ong 
                            : 'No disponible',
                        'ciudad' => $participacion->evento->ciudad ?? null,
                        'ubicacion' => $participacion->evento->ubicacion ?? null,
                        'imagen' => (!empty($participacion->evento->imagenes) && is_array($participacion->evento->imagenes)) 
                            ? ($participacion->evento->imagenes[0] ?? null) 
                            : null,
                        'fecha_asistencia' => $participacion->created_at,
                        'puntos' => $participacion->puntos ?? 0,
                    ];
                })
                ->filter(); // Eliminar nulls si hay algún problema

            // 3. Reacciones (todas las reacciones del usuario)
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
                    $query->select('id', 'titulo', 'tipo_evento', 'fecha_inicio', 'fecha_fin', 'imagenes', 'descripcion');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($reaccion) {
                    return [
                        'id' => $reaccion->id,
                        'evento_id' => $reaccion->evento_id,
                        'titulo' => $reaccion->evento->titulo ?? 'Evento eliminado',
                        'tipo_evento' => $reaccion->evento->tipo_evento ?? null,
                        'imagen' => (!empty($reaccion->evento->imagenes) && is_array($reaccion->evento->imagenes)) 
                            ? ($reaccion->evento->imagenes[0] ?? null) 
                            : null,
                        'tipo_reaccion' => 'me_gusta', // Por defecto, se puede expandir después
                        'comentario' => null, // Si se agrega comentarios después
                        'fecha_reaccion' => $reaccion->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'eventos_inscritos' => $eventosInscritos,
                'eventos_asistidos' => $eventosAsistidos,
                'reacciones' => $reacciones,
                'contadores' => [
                    'total_inscritos' => $eventosInscritos->count(),
                    'total_asistidos' => $eventosAsistidos->count(),
                    'total_reacciones' => $reacciones->count(),
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en dashboard-externo/datos-detallados:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener datos detallados: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Obtener eventos disponibles excluyendo los que el usuario ya está participando
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
            
            // Estadísticas para gráficas
            $totalEventos = $eventos->count();
            $totalMegaEventos = \App\Models\MegaEvento::where('estado', 'publicado')->count();
            
            // Distribución por tipo de evento
            $tiposEventos = $eventos->groupBy('tipo_evento')->map->count();
            
            return response()->json([
                'success' => true,
                'eventos' => $eventos,
                'estadisticas' => [
                    'total_eventos' => $totalEventos,
                    'total_mega_eventos' => $totalMegaEventos,
                    'tipos_eventos' => $tiposEventos,
                ]
            ]);
            
        } catch (\Throwable $e) {
            \Log::error('Error en dashboard-externo/eventos-disponibles:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener eventos disponibles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF completo de reportes
     */
    public function descargarPdfCompleto(Request $request)
    {
        try {
            // Verificar que la clase PDF esté disponible
            if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                \Log::error('DomPDF no está instalado o no está disponible');
                return response()->json([
                    'success' => false,
                    'error' => 'La librería de PDF no está instalada. Por favor, ejecuta: composer require barryvdh/laravel-dompdf'
                ], 500);
            }

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

            // Obtener todos los datos necesarios
            $estadisticas = $this->obtenerEstadisticasParaPdf($externoId, $integranteExterno);
            $eventosInscritos = $this->obtenerEventosInscritosParaPdf($externoId);
            $eventosAsistidos = $this->obtenerEventosAsistidosParaPdf($externoId);
            $reacciones = $this->obtenerReaccionesParaPdf($externoId, $integranteExterno);

            // Datos para la portada
            $fechaActual = now();
            $datosPortada = [
                'nombre_usuario' => $integranteExterno->nombres . ' ' . $integranteExterno->apellidos,
                'fecha_generacion' => $fechaActual->format('d/m/Y'),
                'hora_generacion' => $fechaActual->format('H:i:s'),
                'periodo' => 'Enero - ' . $fechaActual->format('diciembre Y'),
            ];

            // Verificar si GD está disponible
            if (!extension_loaded('gd')) {
                \Log::error('Extensión GD no está instalada');
                return response()->json([
                    'success' => false,
                    'error' => 'La extensión GD de PHP no está instalada. Por favor, habilítala en tu php.ini descomentando la línea: extension=gd'
                ], 500);
            }

            // Ruta absoluta de la imagen para la marca de agua
            $logoPath = public_path('assets/img/UNI2.png');
            if (!file_exists($logoPath)) {
                \Log::warning('Logo no encontrado en: ' . $logoPath);
                $logoPath = null;
            }

            // Generar PDF
            $pdf = Pdf::loadView('externo.reportes.pdf-completo', [
                'portada' => $datosPortada,
                'estadisticas' => $estadisticas,
                'eventos_inscritos' => $eventosInscritos,
                'eventos_asistidos' => $eventosAsistidos,
                'reacciones' => $reacciones,
                'integrante' => $integranteExterno,
                'logo_path' => $logoPath,
            ])->setPaper('a4', 'portrait')
              ->setOption('enable-local-file-access', true)
              ->setOption('isRemoteEnabled', true);

            return $pdf->download('reporte-participacion-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Throwable $e) {
            \Log::error('Error generando PDF:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Obtener estadísticas para el PDF
     */
    private function obtenerEstadisticasParaPdf($externoId, $integranteExterno)
    {
        $totalEventosInscritos = EventoParticipacion::where('externo_id', $externoId)->count();
        $totalEventosAsistidos = EventoParticipacion::where('externo_id', $externoId)->where('asistio', true)->count();
        $totalMegaEventos = MegaEventoParticipanteExterno::where('integrante_externo_id', $externoId)->count();
        
        // Total de reacciones (incluyendo usuarios no registrados con mismo nombre/apellido)
        $totalReacciones = EventoReaccion::where(function($query) use ($externoId, $integranteExterno) {
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
        
        // Total de compartidos (incluyendo usuarios no registrados con mismo nombre/apellido)
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

        // Ciudades impactadas
        $ciudades = EventoParticipacion::where('externo_id', $externoId)
            ->join('eventos', 'evento_participaciones.evento_id', '=', 'eventos.id')
            ->whereNotNull('eventos.ciudad')
            ->distinct()
            ->pluck('eventos.ciudad')
            ->toArray();

        return [
            'total_eventos_inscritos' => $totalEventosInscritos,
            'total_eventos_asistidos' => $totalEventosAsistidos,
            'total_mega_eventos' => $totalMegaEventos,
            'total_reacciones' => $totalReacciones,
            'total_compartidos' => $totalCompartidos,
            'ciudades_impactadas' => count($ciudades),
            'ciudades' => $ciudades,
        ];
    }

    /**
     * Obtener eventos inscritos para el PDF
     */
    private function obtenerEventosInscritosParaPdf($externoId)
    {
        return EventoParticipacion::where('externo_id', $externoId)
            ->with(['evento.ong'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($participacion) {
                if (!$participacion->evento) {
                    return null;
                }
                return [
                    'titulo' => $participacion->evento->titulo,
                    'fecha_inicio' => $participacion->evento->fecha_inicio,
                    'fecha_fin' => $participacion->evento->fecha_fin,
                    'ciudad' => $participacion->evento->ciudad,
                    'ubicacion' => $participacion->evento->ubicacion,
                    'tipo_evento' => $participacion->evento->tipo_evento,
                    'organizador' => $participacion->evento->ong->nombre_ong ?? 'No disponible',
                    'fecha_inscripcion' => $participacion->created_at,
                    'estado' => $participacion->asistio ? 'asistido' : 'inscrito',
                    'asistio' => $participacion->asistio,
                    'imagenes' => $participacion->evento->imagenes ?? [],
                ];
            })
            ->filter();
    }

    /**
     * Obtener eventos asistidos para el PDF
     */
    private function obtenerEventosAsistidosParaPdf($externoId)
    {
        return EventoParticipacion::where('externo_id', $externoId)
            ->where('asistio', true)
            ->with(['evento.ong'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($participacion) {
                if (!$participacion->evento) {
                    return null;
                }
                return [
                    'titulo' => $participacion->evento->titulo,
                    'fecha_inicio' => $participacion->evento->fecha_inicio,
                    'fecha_fin' => $participacion->evento->fecha_fin,
                    'ciudad' => $participacion->evento->ciudad,
                    'organizador' => $participacion->evento->ong->nombre_ong ?? 'No disponible',
                    'fecha_asistencia' => $participacion->created_at,
                    'puntos' => $participacion->puntos ?? 0,
                    'imagenes' => $participacion->evento->imagenes ?? [],
                ];
            })
            ->filter();
    }

    /**
     * Obtener reacciones para el PDF
     */
    private function obtenerReaccionesParaPdf($externoId, $integranteExterno)
    {
        return EventoReaccion::where(function($query) use ($externoId, $integranteExterno) {
                $query->where('externo_id', $externoId);
                if ($integranteExterno) {
                    $query->orWhere(function($q) use ($integranteExterno) {
                        $q->whereNull('externo_id')
                          ->where('nombres', $integranteExterno->nombres)
                          ->where('apellidos', $integranteExterno->apellidos ?? '');
                    });
                }
            })
            ->with(['evento'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reaccion) {
                if (!$reaccion->evento) {
                    return null;
                }
                return [
                    'titulo' => $reaccion->evento->titulo,
                    'fecha_reaccion' => $reaccion->created_at,
                    'tipo_reaccion' => 'me_gusta',
                ];
            })
            ->filter();
    }

    /**
     * Obtener notificaciones del usuario externo (incluyendo alertas de eventos próximos)
     */
    public function notificaciones(Request $request)
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

            // Obtener todas las notificaciones del usuario externo
            $notificaciones = Notificacion::where('externo_id', $externoId)
                ->whereNull('ong_id') // Notificaciones para usuarios externos
                ->with(['evento:id,titulo,fecha_inicio'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    return [
                        'id' => $notificacion->id,
                        'tipo' => $notificacion->tipo,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'leida' => $notificacion->leida,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'fecha_inicio' => $notificacion->evento ? $notificacion->evento->fecha_inicio : null,
                        'fecha' => $notificacion->created_at,
                    ];
                });

            $noLeidas = Notificacion::where('externo_id', $externoId)
                ->whereNull('ong_id')
                ->where('leida', false)
                ->count();

            // Obtener alertas de eventos próximos
            $alertasEventosProximos = Notificacion::where('externo_id', $externoId)
                ->whereNull('ong_id')
                ->where('tipo', 'evento_proximo')
                ->where('leida', false)
                ->with(['evento:id,titulo,fecha_inicio'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    return [
                        'id' => $notificacion->id,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'fecha_inicio' => $notificacion->evento ? $notificacion->evento->fecha_inicio : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'no_leidas' => $noLeidas,
                'alertas_eventos_proximos' => $alertasEventosProximos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar dashboard en PDF
     */
    public function exportarPdf(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'EXTERNO') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios externos pueden exportar reportes',
                    'message' => 'Acceso denegado'
                ], 403);
            }

            $externoId = $user->id_usuario;
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();

            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::now()->subMonths(6);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            // Aumentar límites para generación de PDF
            ini_set('memory_limit', '256M');
            set_time_limit(120);
            
            // Cache key para PDF (30 minutos)
            $cacheKey = 'pdf_dashboard_externo_' . $externoId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            
            // Obtener datos con cache
            $datos = Cache::remember($cacheKey, 1800, function() use ($externoId, $integranteExterno, $fechaInicio, $fechaFin) {
                return $this->obtenerDatosDashboardExterno($externoId, $integranteExterno, $fechaInicio, $fechaFin);
            });
            
            // Cache para URLs de gráficos (30 minutos)
            $graficosCacheKey = 'pdf_graficos_externo_' . $externoId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            $graficosUrls = Cache::remember($graficosCacheKey, 1800, function() use ($datos) {
                return $this->generarUrlsGraficos($datos);
            });
            
            // Cache para logos (1 hora)
            $logoUni2 = Cache::remember('logo_uni2_path', 3600, function() {
                $path = public_path('assets/img/UNI2 - copia.png');
                return file_exists($path) ? $path : null;
            });

            $pdf = Pdf::loadView('pdf.dashboard-externo', [
                'integrante' => $integranteExterno,
                'datos' => $datos,
                'graficos_urls' => $graficosUrls,
                'logo_uni2' => $logoUni2,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'fecha_generacion' => now()->format('d/m/Y H:i:s')
            ])->setPaper('a4', 'portrait')
              ->setOption('enable-local-file-access', true)
              ->setOption('isRemoteEnabled', true)
              ->setOption('isHtml5ParserEnabled', true)
              ->setOption('defaultFont', 'Arial')
              ->setOption('dpi', 96);

            $filename = 'dashboard-externo-' . $externoId . '-' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            // Generar PDF una sola vez
            $pdfContent = $pdf->output();
            
            // Retornar PDF como respuesta binaria con headers correctos
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');

        } catch (\Throwable $e) {
            Log::error('Error generando PDF del dashboard Externo:', [
                'externo_id' => $request->user()->id_usuario ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage(),
                'message' => 'Error al generar el reporte PDF'
            ], 500);
        }
    }

    /**
     * Exportar dashboard en Excel
     */
    public function exportarExcel(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'EXTERNO') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios externos pueden exportar reportes',
                    'message' => 'Acceso denegado'
                ], 403);
            }

            $externoId = $user->id_usuario;
            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();

            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::now()->subMonths(6);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            // Cache key para Excel (30 minutos)
            $cacheKey = 'excel_dashboard_externo_' . $externoId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            
            // Obtener datos con cache
            $datos = Cache::remember($cacheKey, 1800, function() use ($externoId, $integranteExterno, $fechaInicio, $fechaFin) {
                return $this->obtenerDatosDashboardExterno($externoId, $integranteExterno, $fechaInicio, $fechaFin);
            });

            try {
                // Verificar que la clase existe
                if (!class_exists(\App\Exports\DashboardExternoExport::class)) {
                    throw new \Exception('La clase de exportación no está disponible');
                }
                
                $export = new \App\Exports\DashboardExternoExport($integranteExterno, $datos, $fechaInicio, $fechaFin);
                
                $filename = 'dashboard-externo-' . $externoId . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                
                // Forzar descarga automática
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            } catch (\Throwable $e) {
                Log::error('Error creando export Excel:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                throw new \Exception('Error al generar Excel: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('Error generando Excel del dashboard Externo:', [
                'externo_id' => $request->user()->id_usuario ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar Excel: ' . $e->getMessage(),
                'message' => 'Error al generar el reporte Excel'
            ], 500);
        }
    }

    /**
     * Obtener datos completos del dashboard externo
     */
    private function obtenerDatosDashboardExterno($externoId, $integranteExterno, $fechaInicio, $fechaFin)
    {
        // Obtener participaciones en el rango de fechas
        $participaciones = EventoParticipacion::where('externo_id', $externoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('evento')
            ->get();

        // Obtener participaciones en mega eventos
        $participacionesMega = MegaEventoParticipanteExterno::where('integrante_externo_id', $externoId)
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->where('activo', true)
            ->get();

        // Métricas principales
        $metricas = [
            'total_eventos_inscritos' => $participaciones->count(),
            'total_eventos_asistidos' => $participaciones->where('asistio', true)->count(),
            'total_mega_eventos_inscritos' => $participacionesMega->count(),
            'total_reacciones' => EventoReaccion::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count(),
            'total_compartidos' => EventoCompartido::where(function($query) use ($externoId, $integranteExterno) {
                    $query->where('externo_id', $externoId);
                    if ($integranteExterno) {
                        $query->orWhere(function($q) use ($integranteExterno) {
                            $q->whereNull('externo_id')
                              ->where('nombres', $integranteExterno->nombres)
                              ->where('apellidos', $integranteExterno->apellidos ?? '');
                        });
                    }
                })
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count(),
            'horas_acumuladas' => $participaciones->where('asistio', true)->count() * 2
        ];

        // Historial de participación por mes
        $historialParticipacion = [];
        $fechaActual = $fechaInicio->copy();
        while ($fechaActual <= $fechaFin) {
            $mes = $fechaActual->format('Y-m');
            $inicioMes = $fechaActual->copy()->startOfMonth();
            $finMes = $fechaActual->copy()->endOfMonth();
            
            $inscritos = $participaciones->filter(function($p) use ($inicioMes, $finMes) {
                return $p->created_at >= $inicioMes && $p->created_at <= $finMes;
            })->count();
            
            $asistidos = $participaciones->filter(function($p) use ($inicioMes, $finMes) {
                return $p->asistio && $p->created_at >= $inicioMes && $p->created_at <= $finMes;
            })->count();
            
            $historialParticipacion[$mes] = [
                'inscritos' => $inscritos,
                'asistidos' => $asistidos
            ];
            
            $fechaActual->addMonth();
        }

        // Estado de participaciones
        $estadoParticipaciones = [
            'activos' => 0,
            'finalizados' => 0,
            'pendientes' => 0,
            'cancelados' => 0
        ];

        foreach ($participaciones as $p) {
            if ($p->evento) {
                $fechaFin = $p->evento->fecha_fin ? Carbon::parse($p->evento->fecha_fin) : null;
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

        // Tipo de eventos participados
        $tipoEventos = [];
        foreach ($participaciones as $p) {
            if ($p->evento && $p->evento->tipo_evento) {
                $tipo = $p->evento->tipo_evento;
                $tipoEventos[$tipo] = ($tipoEventos[$tipo] ?? 0) + 1;
            }
        }

        // Top eventos por interacción
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
        
        usort($eventosInteracciones, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        $topEventos = array_slice($eventosInteracciones, 0, 10);

        // Listado de eventos inscritos
        $listadoEventos = $participaciones->map(function($p) {
            if (!$p->evento) return null;
            return [
                'id' => $p->evento_id,
                'titulo' => $p->evento->titulo,
                'fecha_inicio' => $p->evento->fecha_inicio,
                'fecha_fin' => $p->evento->fecha_fin,
                'ciudad' => $p->evento->ciudad,
                'tipo_evento' => $p->evento->tipo_evento,
                'estado' => $p->asistio ? 'asistido' : 'inscrito',
                'asistio' => $p->asistio,
                'fecha_inscripcion' => $p->created_at
            ];
        })->filter()->toArray();

        return [
            'metricas' => $metricas,
            'historial_participacion' => $historialParticipacion,
            'estado_participaciones' => $estadoParticipaciones,
            'tipo_eventos' => $tipoEventos,
            'top_eventos' => $topEventos,
            'listado_eventos' => $listadoEventos
        ];
    }

    /**
     * Generar URLs de gráficos usando QuickChart
     */
    private function generarUrlsGraficos($datos)
    {
        $baseUrl = 'https://quickchart.io/chart?c=';
        $params = '&width=700&height=350&backgroundColor=white&devicePixelRatio=1.5';
        
        // Gráfico de líneas - Historial de participación
        $historial = $datos['historial_participacion'] ?? [];
        if (empty($historial)) {
            $historial = ['Sin datos' => ['inscritos' => 0, 'asistidos' => 0]];
        }
        $chartHistorial = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($historial),
                'datasets' => [
                    [
                        'label' => 'Inscritos',
                        'data' => array_column($historial, 'inscritos'),
                        'borderColor' => '#00A36C',
                        'backgroundColor' => 'rgba(0, 163, 108, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Asistidos',
                        'data' => array_column($historial, 'asistidos'),
                        'borderColor' => '#0C2B44',
                        'backgroundColor' => 'rgba(12, 43, 68, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Historial de Participación', 'font' => ['size' => 16]]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        
        // Gráfico de dona - Estado de participaciones
        $estados = $datos['estado_participaciones'] ?? [];
        if (empty($estados)) {
            $estados = ['Sin datos' => 1];
        }
        $chartEstados = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($estados),
                'datasets' => [[
                    'data' => array_values($estados),
                    'backgroundColor' => ['#00A36C', '#0C2B44', '#dc3545', '#17a2b8']
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Estado de Participaciones', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'right']
                ]
            ]
        ];
        
        // Gráfico de barras - Top eventos
        $topEventos = array_slice($datos['top_eventos'] ?? [], 0, 10);
        if (empty($topEventos)) {
            $topEventos = [['titulo' => 'Sin datos', 'reacciones' => 0, 'compartidos' => 0]];
        }
        $chartTopEventos = [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($topEventos, 'titulo'),
                'datasets' => [
                    [
                        'label' => 'Reacciones',
                        'data' => array_column($topEventos, 'reacciones'),
                        'backgroundColor' => '#dc3545'
                    ],
                    [
                        'label' => 'Compartidos',
                        'data' => array_column($topEventos, 'compartidos'),
                        'backgroundColor' => '#00A36C'
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Top Eventos por Interacción', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'top']
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];

        return [
            'historial_participacion' => $baseUrl . urlencode(json_encode($chartHistorial)) . $params,
            'estado_participaciones' => $baseUrl . urlencode(json_encode($chartEstados)) . $params,
            'top_eventos' => $baseUrl . urlencode(json_encode($chartTopEventos)) . $params
        ];
    }
}

