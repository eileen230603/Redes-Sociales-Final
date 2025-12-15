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

        $data = EventoParticipacion::create([
            "evento_id" => $eventoId,
            "externo_id" => $externoId,
            "estado" => "pendiente",
            "asistio" => false,
            "puntos" => 0
        ]);

        // Crear notificación para la ONG
        $this->crearNotificacionParticipacion($evento, $externoId);

        return response()->json(["success" => true, "message" => "Inscripción exitosa", "data" => $data]);
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

        $registro->delete();

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

            $participantes = EventoParticipacion::where('evento_id', $eventoId)
                ->with(['externo' => function($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }])
                ->get()
                ->map(function($participacion) {
                    $user = $participacion->externo;
                    $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    return [
                        'id' => $participacion->id,
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

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            $evento = Evento::find($participacion->evento_id);
            $ongId = $request->user()->id_usuario;

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

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            $evento = Evento::find($participacion->evento_id);
            $ongId = $request->user()->id_usuario;

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
                "message" => "Participación rechazada correctamente"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al rechazar participación: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar asistencia mediante código QR
     */
    public function registrarAsistencia(Request $request, $participacionId)
    {
        try {
            $request->validate([
                'codigo' => 'required|string',
            ]);

            $participacion = EventoParticipacion::find($participacionId);

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $evento = Evento::find($participacion->evento_id);

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
                    "error" => "No tienes permiso para registrar asistencias en este evento"
                ], 403);
            }

            // Verificar que la participación está aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "La participación debe estar aprobada para registrar asistencia"
                ], 400);
            }

            // Validar código QR (el código debería ser el ID de la participación + algún hash)
            // Por ahora validamos que el código no esté vacío
            $codigoValido = !empty($request->codigo);

            if (!$codigoValido) {
                return response()->json([
                    "success" => false,
                    "error" => "Código QR inválido"
                ], 400);
            }

            // Marcar asistencia
            $participacion->asistio = true;
            $participacion->save();

            return response()->json([
                "success" => true,
                "message" => "Asistencia registrada correctamente"
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "error" => "Código requerido"
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al registrar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar o desmarcar asistencia manualmente (sin QR)
     */
    public function marcarAsistencia(Request $request, $participacionId)
    {
        try {
            $request->validate([
                'asistio' => 'required|boolean',
            ]);

            $participacion = EventoParticipacion::find($participacionId);

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            $evento = Evento::find($participacion->evento_id);

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
                    "error" => "No tienes permiso para marcar asistencias en este evento"
                ], 403);
            }

            // Actualizar asistencia
            $participacion->asistio = $request->asistio;
            $participacion->save();

            $mensaje = $request->asistio
                ? 'Asistencia marcada correctamente'
                : 'Asistencia desmarcada correctamente';

            return response()->json([
                "success" => true,
                "message" => $mensaje,
                "participacion" => $participacion
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "error" => "El campo 'asistio' es requerido y debe ser booleano"
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al marcar asistencia: " . $e->getMessage()
            ], 500);
        }
    }
}
