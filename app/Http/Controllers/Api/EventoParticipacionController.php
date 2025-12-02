<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\IntegranteExterno;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventoParticipacionController extends Controller
{
    // Inscripción
    public function inscribir(Request $request)
    {
        $externoId = $request->user()->id_usuario;
        $eventoId = $request->evento_id;

        $evento = Evento::find($eventoId);

        if (!$evento) {
            return response()->json(["success" => false, "error" => "Evento no encontrado"], 404);
        }

        if (!$evento->inscripcion_abierta) {
            return response()->json(["success" => false, "error" => "Inscripciones cerradas"], 400);
        }

        $inscritos = EventoParticipacion::where('evento_id', $eventoId)->count();
        if ($evento->capacidad_maxima && $inscritos >= $evento->capacidad_maxima) {
            return response()->json(["success" => false, "error" => "Cupo agotado"], 400);
        }

        if (EventoParticipacion::where('evento_id', $eventoId)->where('externo_id', $externoId)->exists()) {
            return response()->json(["success" => false, "error" => "Ya estás inscrito"], 400);
        }

        // TRANSACCIÓN: Crear participación + notificación
        $data = DB::transaction(function () use ($eventoId, $externoId, $evento) {
            // 1. Crear participación
            $participacion = EventoParticipacion::create([
            "evento_id" => $eventoId,
            "externo_id" => $externoId,
            "estado" => "aprobada", // Aprobación automática
            "asistio" => false,
            "puntos" => 0
        ]);

            // 2. Crear notificación para la ONG
        $this->crearNotificacionParticipacion($evento, $externoId);

            return $participacion;
        });

        return response()->json(["success" => true, "message" => "Inscripción exitosa y aprobada automáticamente", "data" => $data]);
    }

    // Cancelar inscripción
    public function cancelar(Request $request)
    {
        $externoId = $request->user()->id_usuario;
        $eventoId = $request->evento_id;

        $registro = EventoParticipacion::where('evento_id', $eventoId)
            ->where('externo_id', $externoId)
            ->first();

        if (!$registro) {
            return response()->json(["success" => false, "error" => "No estás inscrito"], 404);
        }

        // TRANSACCIÓN: Eliminar participación + limpiar datos relacionados
        DB::transaction(function () use ($registro, $eventoId, $externoId) {
            // 1. Eliminar participación
        $registro->delete();
            
            // 2. Eliminar notificaciones relacionadas (opcional, para mantener consistencia)
            // Las notificaciones de participación se pueden mantener para historial
            // o eliminarse si se requiere limpieza completa
            // Notificacion::where('evento_id', $eventoId)
            //     ->where('externo_id', $externoId)
            //     ->where('tipo', 'participacion')
            //     ->delete();
        });

        return response()->json(["success" => true, "message" => "Inscripción cancelada"]);
    }

    // Ver mis eventos
    public function misEventos(Request $request)
    {
        $externoId = $request->user()->id_usuario;

        $registros = EventoParticipacion::with('evento')
            ->where('externo_id', $externoId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($participacion) {
                return [
                    'id' => $participacion->id,
                    'evento_id' => $participacion->evento_id,
                    'estado' => $participacion->estado ?? 'pendiente',
                    'asistio' => $participacion->asistio ?? false,
                    'puntos' => $participacion->puntos ?? 0,
                    'created_at' => $participacion->created_at,
                    'evento' => $participacion->evento
                ];
            });

        return response()->json(["success" => true, "eventos" => $registros]);
    }

    // Obtener participantes de un evento (para ONG)
    public function participantesEvento(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para ver los participantes de este evento"
                ], 403);
            }

            // Participantes registrados
            $participantesRegistrados = EventoParticipacion::where('evento_id', $eventoId)
                ->with(['externo' => function($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }])
                ->get()
                ->map(function($participacion) {
                    $user = $participacion->externo;
                    $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'registrado',
                        'externo_id' => $participacion->externo_id,
                        'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'correo' => $externo ? $externo->email : $user->correo_electronico,
                        'telefono' => $externo ? $externo->phone_number : 'No disponible',
                        'fecha_inscripcion' => $participacion->created_at,
                        'estado' => $participacion->estado ?? 'pendiente',
                        'asistio' => $participacion->asistio ?? false,
                        'puntos' => $participacion->puntos ?? 0,
                        'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                    ];
                });

            // Participantes no registrados
            $participantesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('estado', '!=', 'rechazada')
                ->get()
                ->map(function($participacion) {
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'no_registrado',
                        'nombre' => trim($participacion->nombres . ' ' . $participacion->apellidos),
                        'correo' => $participacion->email ?? 'No disponible',
                        'telefono' => $participacion->telefono ?? 'No disponible',
                        'fecha_inscripcion' => $participacion->created_at,
                        'estado' => $participacion->estado ?? 'pendiente',
                        'asistio' => $participacion->asistio ?? false,
                        'foto_perfil' => null
                    ];
                });

            // Combinar ambos tipos de participantes
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados);

            return response()->json([
                "success" => true,
                "participantes" => $participantes,
                "count" => $participantes->count()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al obtener participantes: " . $e->getMessage()
            ], 500);
        }
    }

    // Aprobar participación
    public function aprobar(Request $request, $participacionId)
    {
        try {
            $participacion = EventoParticipacion::find($participacionId);
            
            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $evento = Evento::find($participacion->evento_id);
            $ongId = $request->user()->id_usuario;

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para aprobar participantes de este evento"
                ], 403);
            }

            $participacion->estado = 'aprobada';
            $participacion->save();

            return response()->json([
                "success" => true,
                "message" => "Participación aprobada correctamente",
                "participacion" => $participacion
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al aprobar participación: " . $e->getMessage()
            ], 500);
        }
    }

    // Rechazar participación
    public function rechazar(Request $request, $participacionId)
    {
        try {
            $participacion = EventoParticipacion::find($participacionId);
            
            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $evento = Evento::find($participacion->evento_id);
            $ongId = $request->user()->id_usuario;

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para rechazar participantes de este evento"
                ], 403);
            }

            $participacion->estado = 'rechazada';
            $participacion->save();

            return response()->json([
                "success" => true,
                "message" => "Participación rechazada correctamente",
                "participacion" => $participacion
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al rechazar participación: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la ONG cuando alguien se inscribe
     */
    private function crearNotificacionParticipacion(Evento $evento, $externoId)
    {
        try {
            $externo = User::find($externoId);
            if (!$externo) return;

            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            $nombreUsuario = $integranteExterno 
                ? trim($integranteExterno->nombres . ' ' . ($integranteExterno->apellidos ?? ''))
                : $externo->nombre_usuario;

            Notificacion::create([
                'ong_id' => $evento->ong_id,
                'evento_id' => $evento->id,
                'externo_id' => $externoId,
                'tipo' => 'participacion',
                'titulo' => 'Nueva inscripción en tu evento',
                'mensaje' => "{$nombreUsuario} se inscribió al evento \"{$evento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            // Log error pero no fallar la inscripción
            \Log::error('Error creando notificación de participación: ' . $e->getMessage());
        }
    }

    /**
     * Participación pública (usuarios no registrados mediante QR)
     */
    public function participarPublico(Request $request, $eventoId)
    {
        try {
            // Validar datos
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'email' => 'nullable|email|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Verificar que el evento existe
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que las inscripciones estén abiertas
            if (!$evento->inscripcion_abierta) {
                return response()->json([
                    'success' => false,
                    'error' => 'Las inscripciones están cerradas para este evento'
                ], 400);
            }

            // Verificar capacidad
            $inscritosRegistrados = EventoParticipacion::where('evento_id', $eventoId)->count();
            $inscritosNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('estado', '!=', 'rechazada')
                ->count();
            $totalInscritos = $inscritosRegistrados + $inscritosNoRegistrados;

            if ($evento->capacidad_maxima && $totalInscritos >= $evento->capacidad_maxima) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupo agotado para este evento'
                ], 400);
            }

            // Verificar si ya está inscrito (por nombre y apellido)
            $yaInscrito = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('nombres', $request->nombres)
                ->where('apellidos', $request->apellidos)
                ->where('estado', '!=', 'rechazada')
                ->exists();

            if ($yaInscrito) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ya estás inscrito en este evento'
                ], 400);
            }

            // Crear participación (APROBADA automáticamente para usuarios no registrados)
            $participacion = EventoParticipanteNoRegistrado::create([
                'evento_id' => $eventoId,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'estado' => 'aprobada', // Aprobado automáticamente
                'asistio' => false,
            ]);

            // Crear notificación para la ONG
            try {
                $this->crearNotificacionParticipacionPublica($evento, $participacion);
            } catch (\Throwable $e) {
                \Log::error('Error creando notificación de participación pública: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => '¡Tu participación ha sido registrada y aprobada!',
                'data' => $participacion
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en participarPublico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar tu participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la ONG sobre participación pública
     */
    private function crearNotificacionParticipacionPublica(Evento $evento, EventoParticipanteNoRegistrado $participacion)
    {
        try {
            $ongId = $evento->ong_id ?? null;
            if (!$ongId) return;

            Notificacion::create([
                'usuario_id' => $ongId,
                'tipo' => 'participacion_publica',
                'titulo' => 'Nueva participación (Usuario no registrado)',
                'mensaje' => "{$participacion->nombres} {$participacion->apellidos} quiere participar en el evento: {$evento->titulo}",
                'evento_id' => $evento->id,
                'leida' => false,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de participación pública: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si un usuario no registrado ya participó en el evento
     */
    public function verificarParticipacionPublica(Request $request, $eventoId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $yaInscrito = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('nombres', $request->nombres)
                ->where('apellidos', $request->apellidos)
                ->where('estado', '!=', 'rechazada')
                ->first();

            return response()->json([
                'success' => true,
                'ya_participa' => $yaInscrito !== null,
                'participacion' => $yaInscrito ? [
                    'estado' => $yaInscrito->estado,
                    'fecha_inscripcion' => $yaInscrito->created_at
                ] : null
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en verificarParticipacionPublica: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprobar participación de usuario no registrado
     */
    public function aprobarNoRegistrado(Request $request, $participacionId)
    {
        try {
            $participacion = EventoParticipanteNoRegistrado::find($participacionId);
            
            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $participacion->estado = 'aprobada';
            $participacion->save();

            return response()->json([
                "success" => true,
                "message" => "Participación aprobada correctamente"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al aprobar participación: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar participación de usuario no registrado
     */
    public function rechazarNoRegistrado(Request $request, $participacionId)
    {
        try {
            $participacion = EventoParticipanteNoRegistrado::find($participacionId);
            
            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $participacion->estado = 'rechazada';
            $participacion->save();

            return response()->json([
                "success" => true,
                "message" => "Participación rechazada correctamente"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al rechazar participación: " . $e->getMessage()
            ], 500);
        }
    }
}
