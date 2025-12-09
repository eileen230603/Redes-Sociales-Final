<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\IntegranteExterno;
use App\Models\MegaEvento;
use App\Models\Ong;
use Illuminate\Http\Request;

class DashboardOngController extends Controller
{
    /**
     * Obtener estadísticas de participantes por evento
     */
    public function estadisticasParticipantes(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            // Obtener todos los eventos de la ONG
            $eventos = Evento::where('ong_id', $ongId)
                ->select('id', 'titulo', 'fecha_inicio')
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $estadisticas = $eventos->map(function($evento) {
                $participantes = EventoParticipacion::where('evento_id', $evento->id)->get();
                
                return [
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'total' => $participantes->count(),
                    'aprobados' => $participantes->where('estado', 'aprobada')->count(),
                    'pendientes' => $participantes->where('estado', 'pendiente')->count(),
                    'rechazados' => $participantes->where('estado', 'rechazada')->count(),
                ];
            });

            // Estadísticas generales
            $totalParticipantes = EventoParticipacion::whereIn('evento_id', $eventos->pluck('id'))->count();
            $totalAprobados = EventoParticipacion::whereIn('evento_id', $eventos->pluck('id'))
                ->where('estado', 'aprobada')->count();
            $totalPendientes = EventoParticipacion::whereIn('evento_id', $eventos->pluck('id'))
                ->where('estado', 'pendiente')->count();
            $totalRechazados = EventoParticipacion::whereIn('evento_id', $eventos->pluck('id'))
                ->where('estado', 'rechazada')->count();

            return response()->json([
                'success' => true,
                'estadisticas_por_evento' => $estadisticas,
                'totales' => [
                    'total' => $totalParticipantes,
                    'aprobados' => $totalAprobados,
                    'pendientes' => $totalPendientes,
                    'rechazados' => $totalRechazados,
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista detallada de participantes
     */
    public function listaParticipantes(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;
            $eventoId = $request->input('evento_id'); // Opcional: filtrar por evento

            // Obtener IDs de eventos de la ONG
            $eventosIds = Evento::where('ong_id', $ongId)->pluck('id');
            
            $query = EventoParticipacion::whereIn('evento_id', $eventosIds);

            if ($eventoId) {
                $query->where('evento_id', $eventoId);
            }

            $participantes = $query->with(['evento:id,titulo', 'externo'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($participacion) {
                    $user = $participacion->externo;
                    $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    return [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'evento_titulo' => $participacion->evento->titulo,
                        'externo_id' => $participacion->externo_id,
                        'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'correo' => $externo ? $externo->email : $user->correo_electronico,
                        'telefono' => $externo ? $externo->phone_number : 'No disponible',
                        'fecha_inscripcion' => $participacion->created_at,
                        'estado' => $participacion->estado ?? 'pendiente',
                        'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null),
                    ];
                });

            return response()->json([
                'success' => true,
                'participantes' => $participantes
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener participantes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de reacciones por evento
     */
    public function estadisticasReacciones(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            // Obtener todos los eventos de la ONG
            $eventos = Evento::where('ong_id', $ongId)
                ->select('id', 'titulo', 'fecha_inicio')
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $estadisticas = $eventos->map(function($evento) {
                $totalReacciones = EventoReaccion::where('evento_id', $evento->id)->count();
                
                return [
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'total_reacciones' => $totalReacciones,
                ];
            });

            $totalReacciones = EventoReaccion::whereIn('evento_id', $eventos->pluck('id'))->count();

            return response()->json([
                'success' => true,
                'estadisticas_por_evento' => $estadisticas,
                'total_reacciones' => $totalReacciones
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estadísticas de reacciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista detallada de reacciones
     */
    public function listaReacciones(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;
            $eventoId = $request->input('evento_id'); // Opcional: filtrar por evento

            // Obtener IDs de eventos de la ONG
            $eventosIds = Evento::where('ong_id', $ongId)->pluck('id');
            
            $query = EventoReaccion::whereIn('evento_id', $eventosIds);

            if ($eventoId) {
                $query->where('evento_id', $eventoId);
            }

            $reacciones = $query->with(['evento:id,titulo', 'externo'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($reaccion) {
                    $user = $reaccion->externo;
                    $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    return [
                        'id' => $reaccion->id,
                        'evento_id' => $reaccion->evento_id,
                        'evento_titulo' => $reaccion->evento->titulo,
                        'externo_id' => $reaccion->externo_id,
                        'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'correo' => $externo ? $externo->email : $user->correo_electronico,
                        'fecha_reaccion' => $reaccion->created_at,
                        'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null),
                    ];
                });

            return response()->json([
                'success' => true,
                'reacciones' => $reacciones
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener reacciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas generales para el home de la ONG
     */
    public function estadisticasGenerales(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            // Obtener información de la ONG
            $ong = Ong::where('user_id', $ongId)->first();
            
            // Obtener IDs de eventos de la ONG
            $eventosIds = Evento::where('ong_id', $ongId)->pluck('id');
            
            // Estadísticas de eventos
            $totalEventos = Evento::where('ong_id', $ongId)->count();
            $eventosActivos = Evento::where('ong_id', $ongId)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_fin', '>=', now())
                ->count();
            $eventosProximos = Evento::where('ong_id', $ongId)
                ->where('fecha_inicio', '>', now())
                ->count();
            $eventosFinalizados = Evento::where('ong_id', $ongId)
                ->where(function($query) {
                    $query->where('fecha_fin', '<', now())
                          ->orWhere('estado', 'finalizado');
                })
                ->count();

            // Estadísticas de mega eventos
            $totalMegaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)->count();
            $megaEventosActivos = MegaEvento::where('ong_organizadora_principal', $ongId)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_fin', '>=', now())
                ->count();

            // Estadísticas de participantes/voluntarios
            $totalVoluntarios = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->distinct('externo_id')
                ->count('externo_id');
            $totalParticipantes = EventoParticipacion::whereIn('evento_id', $eventosIds)->count();
            $participantesAprobados = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->where('estado', 'aprobada')
                ->count();

            // Estadísticas de reacciones
            $totalReacciones = EventoReaccion::whereIn('evento_id', $eventosIds)->count();
            $eventosConReacciones = EventoReaccion::whereIn('evento_id', $eventosIds)
                ->distinct('evento_id')
                ->count('evento_id');

            // Distribución de eventos por tipo
            $eventosPorTipo = Evento::where('ong_id', $ongId)
                ->selectRaw('tipo_evento, COUNT(*) as total')
                ->groupBy('tipo_evento')
                ->get()
                ->pluck('total', 'tipo_evento');

            // Distribución de participantes por estado
            $participantesPorEstado = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get()
                ->pluck('total', 'estado');

            // Eventos por mes (últimos 12 meses)
            $eventosPorMes = Evento::where('ong_id', $ongId)
                ->where('fecha_inicio', '>=', now()->subMonths(12))
                ->get()
                ->groupBy(function($evento) {
                    return $evento->fecha_inicio->format('M');
                })
                ->map(function($group) {
                    return $group->count();
                });

            // Voluntarios por mes (últimos 5 meses)
            $voluntariosPorMes = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->where('created_at', '>=', now()->subMonths(5))
                ->get()
                ->groupBy(function($participacion) {
                    return $participacion->created_at->format('M');
                })
                ->map(function($group) {
                    return $group->pluck('externo_id')->unique()->count();
                });

            // Mega eventos por mes (últimos 12 meses)
            $megaEventosPorMes = MegaEvento::where('ong_organizadora_principal', $ongId)
                ->where('fecha_inicio', '>=', now()->subMonths(12))
                ->get()
                ->groupBy(function($megaEvento) {
                    return $megaEvento->fecha_inicio->format('M');
                })
                ->map(function($group) {
                    return $group->count();
                });

            // Reacciones por mes (últimos 12 meses)
            $reaccionesPorMes = EventoReaccion::whereIn('evento_id', $eventosIds)
                ->where('created_at', '>=', now()->subMonths(12))
                ->get()
                ->groupBy(function($reaccion) {
                    return $reaccion->created_at->format('M');
                })
                ->map(function($group) {
                    return $group->count();
                });

            // Obtener información del usuario
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'ong' => [
                    'nombre' => $ong ? $ong->nombre_ong : ($user->nombre_usuario ?? 'ONG'),
                    'descripcion' => $ong ? $ong->descripcion : null,
                    'foto_perfil' => $ong ? ($ong->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null),
                ],
                'usuario' => [
                    'nombre_usuario' => $user->nombre_usuario ?? null,
                    'foto_perfil' => $user->foto_perfil_url ?? null,
                ],
                'estadisticas' => [
                    'eventos' => [
                        'total' => $totalEventos,
                        'activos' => $eventosActivos,
                        'proximos' => $eventosProximos,
                        'finalizados' => $eventosFinalizados,
                    ],
                    'mega_eventos' => [
                        'total' => $totalMegaEventos,
                        'activos' => $megaEventosActivos,
                    ],
                    'voluntarios' => [
                        'total_unicos' => $totalVoluntarios,
                        'total_inscripciones' => $totalParticipantes,
                        'aprobados' => $participantesAprobados,
                    ],
                    'reacciones' => [
                        'total' => $totalReacciones,
                        'eventos_con_reacciones' => $eventosConReacciones,
                    ],
                ],
                'distribuciones' => [
                    'eventos_por_tipo' => $eventosPorTipo,
                    'participantes_por_estado' => $participantesPorEstado,
                ],
                'graficas' => [
                    'eventos_por_mes' => $eventosPorMes,
                    'voluntarios_por_mes' => $voluntariosPorMes,
                    'mega_eventos_por_mes' => $megaEventosPorMes,
                    'reacciones_por_mes' => $reaccionesPorMes,
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
