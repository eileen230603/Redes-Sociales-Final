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
use Illuminate\Support\Str;

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
            "puntos" => 0,
            // Generar código de ticket único para control de asistencia
            "ticket_codigo" => Str::uuid()->toString(),
            // Estado de asistencia por defecto
            "estado_asistencia" => "no_asistido",
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
                    'ticket_codigo' => $participacion->ticket_codigo,
                    'checkin_at' => $participacion->checkin_at,
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
                        'tipo_usuario' => 'Externo', // Los registrados son "Externos"
                        'externo_id' => $participacion->externo_id,
                        'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'correo' => $externo ? $externo->email : $user->correo_electronico,
                        'telefono' => $externo ? $externo->phone_number : 'No disponible',
                        'fecha_inscripcion' => $participacion->created_at,
                        'estado' => $participacion->estado ?? 'pendiente',
                        'asistio' => $participacion->asistio ?? false,
                        'puntos' => $participacion->puntos ?? 0,
                        'ticket_codigo' => $participacion->ticket_codigo ?? null,
                        'checkin_at' => $participacion->checkin_at ?? null,
                        'modo_asistencia' => $participacion->modo_asistencia ?? null,
                        'observaciones' => $participacion->observaciones ?? null,
                        'estado_asistencia' => $participacion->estado_asistencia ?? 'no_asistido',
                        'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                    ];
                });

            // Participantes no registrados - Estos son los "Voluntarios"
            $participantesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('estado', '!=', 'rechazada')
                ->get()
                ->map(function($participacion) {
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'no_registrado',
                        'tipo_usuario' => 'Voluntario', // Los no registrados son "Voluntarios"
                        'nombre' => trim($participacion->nombres . ' ' . $participacion->apellidos),
                        'correo' => $participacion->email ?? 'No disponible',
                        'telefono' => $participacion->telefono ?? 'No disponible',
                        'fecha_inscripcion' => $participacion->created_at,
                        'estado' => $participacion->estado ?? 'pendiente',
                        'asistio' => $participacion->asistio ?? false,
                        'ticket_codigo' => null, // Los no registrados no tienen ticket
                        'checkin_at' => null,
                        'modo_asistencia' => null,
                        'observaciones' => null,
                        'estado_asistencia' => $participacion->asistio ? 'asistido' : 'no_asistido',
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

    /**
     * Registrar asistencia usando el ticket del participante.
     */
    public function registrarAsistencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'evento_id' => 'required|integer',
            'ticket_codigo' => 'nullable|string', // Puede ser null para registro manual
            'modo_asistencia' => 'nullable|string|in:QR,Manual,Online,Confirmacion',
            'observaciones' => 'nullable|string|max:500',
            'participacion_id' => 'nullable|integer', // Para registro manual sin ticket
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => "Datos inválidos",
                "details" => $validator->errors(),
            ], 422);
        }

        try {
            $eventoId = (int) $request->input('evento_id');
            $ticketCodigo = $request->input('ticket_codigo');
            $modoAsistencia = $request->input('modo_asistencia', 'Manual');
            $observaciones = $request->input('observaciones');
            $participacionId = $request->input('participacion_id');
            $ongId = $request->user()->id_usuario;

            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado",
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para registrar asistencia en este evento",
                ], 403);
            }

            // Validar que el ticket solo sea válido durante el evento
            $ahora = now();
            $fechaInicio = $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio) : null;
            $fechaFin = $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin) : null;
            
            // Margen de tiempo permitido: 30 minutos antes del inicio y 2 horas después del fin
            $margenAntes = 30; // minutos antes del inicio
            $margenDespues = 120; // minutos después del fin (2 horas)
            
            if ($fechaInicio && $fechaFin) {
                $fechaInicioConMargen = $fechaInicio->copy()->subMinutes($margenAntes);
                $fechaFinConMargen = $fechaFin->copy()->addMinutes($margenDespues);
                
                // Verificar si estamos antes del evento (con margen)
                if ($ahora->lt($fechaInicioConMargen)) {
                    $tiempoRestante = $ahora->diffForHumans($fechaInicioConMargen, true);
                    return response()->json([
                        "success" => false,
                        "error" => "El evento aún no ha comenzado. El registro de asistencia estará disponible {$tiempoRestante}.",
                        "fecha_inicio_evento" => $fechaInicio->format('d/m/Y H:i'),
                    ], 400);
                }
                
                // Verificar si estamos después del evento (con margen)
                if ($ahora->gt($fechaFinConMargen)) {
                    $tiempoTranscurrido = $ahora->diffForHumans($fechaFinConMargen, true);
                    return response()->json([
                        "success" => false,
                        "error" => "El evento ya finalizó. El registro de asistencia cerró {$tiempoTranscurrido}. Contacte al organizador para correcciones.",
                        "fecha_fin_evento" => $fechaFin->format('d/m/Y H:i'),
                    ], 400);
                }
                
                // Si es registro por QR y estamos antes del inicio (pero dentro del margen), solo permitir manual
                if ($modoAsistencia === 'QR' && $ahora->lt($fechaInicio)) {
                    return response()->json([
                        "success" => false,
                        "error" => "El evento aún no ha comenzado. Use registro manual para casos especiales.",
                        "fecha_inicio_evento" => $fechaInicio->format('d/m/Y H:i'),
                    ], 400);
                }
            } elseif ($fechaInicio) {
                // Si solo hay fecha_inicio, validar que no sea muy temprano
                $fechaInicioConMargen = $fechaInicio->copy()->subMinutes($margenAntes);
                if ($ahora->lt($fechaInicioConMargen)) {
                    $tiempoRestante = $ahora->diffForHumans($fechaInicioConMargen, true);
                    return response()->json([
                        "success" => false,
                        "error" => "El evento aún no ha comenzado. El registro de asistencia estará disponible {$tiempoRestante}.",
                        "fecha_inicio_evento" => $fechaInicio->format('d/m/Y H:i'),
                    ], 400);
                }
            }

            // Buscar participación (puede ser registrada o no registrada)
            $participacion = null;
            $esNoRegistrado = false;
            
            if ($participacionId) {
                // Primero buscar en participantes registrados
                $participacion = EventoParticipacion::where('id', $participacionId)
                    ->where('evento_id', $eventoId)
                    ->first();
                
                // Si no se encuentra, buscar en participantes no registrados (voluntarios)
                if (!$participacion) {
                    $participanteNoRegistrado = EventoParticipanteNoRegistrado::where('id', $participacionId)
                        ->where('evento_id', $eventoId)
                        ->first();
                    
                    if ($participanteNoRegistrado) {
                        $esNoRegistrado = true;
                        $participacion = $participanteNoRegistrado;
                    }
                }
            } elseif ($ticketCodigo) {
                // Búsqueda por ticket (solo para participantes registrados)
                $ticketCodigo = trim($ticketCodigo);
                $participacion = EventoParticipacion::where('evento_id', $eventoId)
                    ->where('ticket_codigo', $ticketCodigo)
                    ->first();
                    
                // Si no se encuentra, intentar búsqueda case-insensitive
                if (!$participacion) {
                    $participacion = EventoParticipacion::where('evento_id', $eventoId)
                        ->whereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                        ->first();
                }
            } else {
                return response()->json([
                    "success" => false,
                    "error" => "Debe proporcionar ticket_codigo o participacion_id",
                ], 422);
            }

            if (!$participacion) {
                // Log para debugging
                \Log::warning('Participación no encontrada', [
                    'evento_id' => $eventoId,
                    'ticket_codigo' => $ticketCodigo ? substr($ticketCodigo, 0, 20) . '...' : null,
                    'participacion_id' => $participacionId,
                    'ong_id' => $ongId
                ]);
                
                // Verificar si existe el ticket en otro evento
                $ticketEnOtroEvento = $ticketCodigo ? EventoParticipacion::where('ticket_codigo', $ticketCodigo)
                    ->where('evento_id', '!=', $eventoId)
                    ->exists() : false;
                
                $mensajeError = "Participación no encontrada para este evento.";
                if ($ticketEnOtroEvento) {
                    $mensajeError .= " El ticket pertenece a otro evento.";
                } else {
                    $mensajeError .= " Verifique que el código del ticket o ID de participación sea correcto.";
                }
                
                return response()->json([
                    "success" => false,
                    "error" => $mensajeError,
                ], 404);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "La participación debe estar aprobada para registrar asistencia",
                ], 400);
            }

            // Manejar según el tipo de participación
            if ($esNoRegistrado) {
                // Participante no registrado (Voluntario) - solo tiene campo asistio
                if ($participacion->asistio && empty($observaciones)) {
                    return response()->json([
                        "success" => false,
                        "error" => "Este voluntario ya tiene asistencia registrada",
                    ], 409);
                }

                $participacion->asistio = true;
                $participacion->save();

                return response()->json([
                    "success" => true,
                    "message" => "Asistencia del voluntario registrada correctamente",
                    "participacion" => [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'nombre' => trim($participacion->nombres . ' ' . ($participacion->apellidos ?? '')),
                        'asistio' => $participacion->asistio,
                        'tipo' => 'no_registrado',
                        'tipo_usuario' => 'Voluntario',
                    ],
                ]);
            } else {
                // Participante registrado - tiene todos los campos de asistencia
                $yaTieneCheckin = !is_null($participacion->checkin_at);
                
                // Normalizar observaciones: tratar "sin comentario" como vacío
                $observacionesNormalizadas = $observaciones;
                if ($observaciones && strtolower(trim($observaciones)) === 'sin comentario') {
                    $observacionesNormalizadas = null;
                }
                
                // Si es registro manual, siempre permitir (útil para correcciones o re-registros)
                // Si es registro QR y ya tiene check-in, rechazar para evitar duplicados accidentales
                if ($yaTieneCheckin && $modoAsistencia === 'QR') {
                    return response()->json([
                        "success" => false,
                        "error" => "Este ticket ya fue utilizado para registrar asistencia. Use registro manual para actualizar.",
                        "participacion" => $participacion,
                    ], 409);
                }

                // Actualizar asistencia
                // Si es registro manual y ya tiene check-in, actualizar la hora de check-in
                $updateData = [
                    'asistio' => true,
                    'checkin_at' => $yaTieneCheckin && $modoAsistencia === 'Manual' ? now() : ($participacion->checkin_at ?? now()),
                    'modo_asistencia' => $modoAsistencia,
                    'registrado_por' => $ongId,
                    'estado_asistencia' => 'asistido',
                ];

                // Si se proporcionan observaciones válidas, actualizarlas
                if ($observacionesNormalizadas && trim($observacionesNormalizadas) !== '') {
                    $updateData['observaciones'] = trim($observacionesNormalizadas);
                }
                // Si no hay observaciones nuevas y ya tiene check-in, mantener las existentes (no sobrescribir)

                $participacion->update($updateData);

                // Refrescar el modelo
                $participacion->refresh();

                return response()->json([
                    "success" => true,
                    "message" => "Asistencia registrada correctamente",
                    "participacion" => [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'externo_id' => $participacion->externo_id,
                        'ticket_codigo' => $participacion->ticket_codigo,
                        'asistio' => $participacion->asistio,
                        'checkin_at' => $participacion->checkin_at,
                        'modo_asistencia' => $participacion->modo_asistencia,
                        'observaciones' => $participacion->observaciones,
                        'estado' => $participacion->estado,
                        'tipo' => 'registrado',
                        'tipo_usuario' => 'Externo',
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al registrar asistencia: " . $e->getMessage(),
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
            
            // Generar ticket si no existe
            if (empty($participacion->ticket_codigo)) {
                $participacion->ticket_codigo = Str::uuid()->toString();
            }
            
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
