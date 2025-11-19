<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\IntegranteExterno;
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
}
