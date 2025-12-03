<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\User;
use App\Models\IntegranteExterno;
use Illuminate\Support\Facades\DB;

class VoluntarioController extends Controller
{
    // ======================================================
    //  LISTAR VOLUNTARIOS DE UNA ONG
    // ======================================================
    public function indexByOng($ongId)
    {
        try {
            // Obtener todos los eventos de la ONG
            $eventos = Evento::where('ong_id', $ongId)->pluck('id');

            if ($eventos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'voluntarios' => []
                ]);
            }

            $voluntarios = collect();

            // 1. Obtener participaciones registradas (aprobadas)
            $participaciones = EventoParticipacion::whereIn('evento_id', $eventos)
                ->where('estado', 'aprobada')
                ->with(['evento:id,titulo', 'externo'])
                ->get();

            // Formatear participaciones registradas
            $voluntariosRegistrados = $participaciones->map(function($participacion) {
                $user = $participacion->externo;
                
                if (!$user) {
                    return null;
                }
                
                $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                
                // Los usuarios registrados siempre son "Externos" (los voluntarios son los no registrados)
                $tipoUsuario = 'Externo';
                
                $nombre = $user->nombre_usuario;
                if ($externo) {
                    $nombreCompleto = trim($externo->nombres . ' ' . ($externo->apellidos ?? ''));
                    if (!empty($nombreCompleto)) {
                        $nombre = $nombreCompleto;
                    }
                }
                
                return [
                    'id' => $participacion->id,
                    'participacion_id' => $participacion->id,
                    'user_id' => $user->id_usuario,
                    'nombre' => $nombre,
                    'email' => $externo ? ($externo->email ?? $user->correo_electronico) : $user->correo_electronico,
                    'telefono' => $externo ? ($externo->phone_number ?? 'No disponible') : 'No disponible',
                    'tipo_usuario' => $tipoUsuario,
                    'tipo_participacion' => 'registrado',
                    'evento_id' => $participacion->evento_id,
                    'evento_titulo' => $participacion->evento->titulo ?? 'N/A',
                    'estado' => $participacion->estado ?? 'aprobada',
                    'estado_label' => 'Aprobada',
                    'asistio' => (bool) $participacion->asistio,
                    'puntos' => (int) ($participacion->puntos ?? 0),
                    'fecha_inscripcion' => $participacion->created_at ? $participacion->created_at->format('Y-m-d H:i:s') : null,
                    'fecha_inscripcion_formateada' => $participacion->created_at ? $participacion->created_at->format('d/m/Y H:i') : null,
                    'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                ];
            })->filter();

            // 2. Obtener participantes no registrados (aprobados)
            $participantesNoRegistrados = EventoParticipanteNoRegistrado::whereIn('evento_id', $eventos)
                ->where('estado', 'aprobada')
                ->with('evento:id,titulo')
                ->get();

            // Formatear participantes no registrados - Estos son los "Voluntarios"
            $voluntariosNoRegistrados = $participantesNoRegistrados->map(function($participante) {
                $nombreCompleto = trim($participante->nombres . ' ' . ($participante->apellidos ?? ''));
                
                return [
                    'id' => $participante->id,
                    'participacion_id' => $participante->id,
                    'user_id' => null,
                    'nombre' => $nombreCompleto ?: 'Usuario no registrado',
                    'email' => $participante->email ?? 'Sin email',
                    'telefono' => $participante->telefono ?? 'No disponible',
                    'tipo_usuario' => 'Voluntario', // Los no registrados son voluntarios
                    'tipo_participacion' => 'no_registrado',
                    'evento_id' => $participante->evento_id,
                    'evento_titulo' => $participante->evento->titulo ?? 'N/A',
                    'estado' => $participante->estado ?? 'aprobada',
                    'estado_label' => 'Aprobada',
                    'asistio' => (bool) $participante->asistio,
                    'puntos' => 0, // Los no registrados no tienen puntos
                    'fecha_inscripcion' => $participante->created_at ? $participante->created_at->format('Y-m-d H:i:s') : null,
                    'fecha_inscripcion_formateada' => $participante->created_at ? $participante->created_at->format('d/m/Y H:i') : null,
                    'foto_perfil' => null
                ];
            });

            // 3. Combinar ambos tipos y ordenar por fecha de inscripción
            $voluntarios = $voluntariosRegistrados->concat($voluntariosNoRegistrados)
                ->sortByDesc('fecha_inscripcion')
                ->values();

            return response()->json([
                'success' => true,
                'voluntarios' => $voluntarios
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en VoluntarioController@indexByOng: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}

