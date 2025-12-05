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

            // Participantes registrados - Obtener TODOS los registros de evento_participaciones
            // Usar join directo para obtener todos los datos necesarios
            $participantesRegistrados = DB::table('evento_participaciones as ep')
                ->leftJoin('usuarios as u', 'ep.externo_id', '=', 'u.id_usuario')
                ->leftJoin('integrantes_externos as ie', 'u.id_usuario', '=', 'ie.user_id')
                ->where('ep.evento_id', $eventoId)
                ->select(
                    'ep.id',
                    'ep.externo_id',
                    'ep.estado',
                    'ep.asistio',
                    'ep.puntos',
                    'ep.ticket_codigo',
                    'ep.checkin_at',
                    'ep.modo_asistencia',
                    'ep.observaciones',
                    'ep.comentario_asistencia',
                    'ep.estado_asistencia',
                    'ep.created_at as fecha_inscripcion',
                    'u.nombre_usuario',
                    'u.correo_electronico',
                    'ie.nombres',
                    'ie.apellidos',
                    'ie.email',
                    'ie.phone_number',
                    'ie.foto_perfil'
                )
                ->get()
                ->map(function($row) {
                    // Construir nombre completo
                    $nombre = null;
                    if ($row->nombres || $row->apellidos) {
                        $nombre = trim(($row->nombres ?? '') . ' ' . ($row->apellidos ?? ''));
                    } else {
                        $nombre = $row->nombre_usuario ?? 'Usuario no encontrado';
                    }
                    
                    // Construir correo
                    $correo = $row->email ?? $row->correo_electronico ?? 'No disponible';
                    
                    // Construir teléfono
                    $telefono = $row->phone_number ?? 'No disponible';
                    
                    // Construir foto de perfil
                    $fotoPerfil = null;
                    if ($row->foto_perfil) {
                        $fotoPerfil = $row->foto_perfil;
                        if (!str_starts_with($fotoPerfil, 'http')) {
                            $fotoPerfil = asset('storage/' . $fotoPerfil);
                        }
                    }
                    
                    return [
                        'id' => $row->id,
                        'tipo' => 'registrado',
                        'tipo_usuario' => 'Externo',
                        'externo_id' => $row->externo_id,
                        'nombre' => $nombre,
                        'correo' => $correo,
                        'telefono' => $telefono,
                        'fecha_inscripcion' => $row->fecha_inscripcion,
                        'estado' => $row->estado ?? 'pendiente',
                        'asistio' => (bool) $row->asistio,
                        'puntos' => $row->puntos ?? 0,
                        'ticket_codigo' => $row->ticket_codigo,
                        'checkin_at' => $row->checkin_at,
                        'modo_asistencia' => $row->modo_asistencia,
                        'observaciones' => $row->observaciones,
                        'comentario' => null, // No existe columna comentario en evento_participaciones
                        'comentario_asistencia' => $row->comentario_asistencia,
                        'estado_asistencia' => $row->estado_asistencia ?? 'no_asistido',
                        'foto_perfil' => $fotoPerfil
                    ];
                });

            // Participantes no registrados - Obtener TODOS los registros de evento_participantes_no_registrados
            $participantesNoRegistrados = DB::table('evento_participantes_no_registrados')
                ->where('evento_id', $eventoId)
                ->select(
                    'id',
                    'evento_id',
                    'nombres',
                    'apellidos',
                    'email',
                    'telefono',
                    'estado',
                    'asistio',
                    'created_at as fecha_inscripcion'
                )
                ->get()
                ->map(function($row) {
                    return [
                        'id' => $row->id,
                        'tipo' => 'no_registrado',
                        'tipo_usuario' => 'Voluntario',
                        'nombre' => trim(($row->nombres ?? '') . ' ' . ($row->apellidos ?? '')),
                        'correo' => $row->email ?? 'No disponible',
                        'telefono' => $row->telefono ?? 'No disponible',
                        'fecha_inscripcion' => $row->fecha_inscripcion,
                        'estado' => $row->estado ?? 'pendiente',
                        'asistio' => (bool) $row->asistio,
                        'ticket_codigo' => null,
                        'checkin_at' => null,
                        'modo_asistencia' => null,
                        'observaciones' => null,
                        'comentario' => null, // No existe columna comentario en evento_participantes_no_registrados
                        'comentario_asistencia' => null,
                        'estado_asistencia' => $row->asistio ? 'asistido' : 'no_asistido',
                        'foto_perfil' => null
                    ];
                });

            // Combinar ambos tipos de participantes
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados);

            // Log para debug
            \Log::info('Participantes obtenidos', [
                'evento_id' => $eventoId,
                'registrados_count' => $participantesRegistrados->count(),
                'no_registrados_count' => $participantesNoRegistrados->count(),
                'total_count' => $participantes->count()
            ]);

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
                // Generar código de ticket único
                'ticket_codigo' => Str::uuid()->toString(),
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

    /**
     * Marcar asistencia por el usuario externo (Parte 1)
     * Solo disponible cuando el evento está "En curso" (activo)
     */
    public function marcarAsistenciaUsuario(Request $request, $eventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar que el evento esté "En curso" (activo)
            if ($evento->estado_dinamico !== 'activo') {
                return response()->json([
                    "success" => false,
                    "error" => "Solo puedes marcar asistencia cuando el evento está en curso"
                ], 400);
            }

            // Verificar que el usuario esté inscrito
            $participacion = EventoParticipacion::where('evento_id', $eventoId)
                ->where('externo_id', $externoId)
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "No estás inscrito en este evento"
                ], 404);
            }

            // Verificar que no haya marcado asistencia previamente
            if ($participacion->estado_asistencia === 'asistido') {
                return response()->json([
                    "success" => false,
                    "error" => "Ya marcaste tu asistencia para este evento"
                ], 409);
            }

            // Obtener IP del usuario
            $ipRegistro = $request->ip();
            
            // Intentar obtener ubicación aproximada (opcional, puede usar servicios externos)
            $ubicacionAproximada = null;
            // TODO: Integrar con servicio de geolocalización si se requiere

            // Actualizar participación
            $participacion->update([
                'estado_asistencia' => 'asistido',
                'asistio' => true,
                'checkin_at' => now(),
                'modo_asistencia' => 'Confirmacion',
                'ip_registro' => $ipRegistro,
                'ubicacion_aproximada' => $ubicacionAproximada,
                'fecha_modificacion' => now(),
                'usuario_modifico' => $externoId,
                'registrado_por' => $externoId, // El usuario se auto-registra
            ]);

            return response()->json([
                "success" => true,
                "message" => "¡Gracias por participar! Tu asistencia fue registrada correctamente.",
                "data" => [
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'fecha_registro' => $participacion->checkin_at,
                    'estado_asistencia' => $participacion->estado_asistencia,
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al marcar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener eventos activos del usuario externo para marcar asistencia (Parte 1)
     */
    public function eventosActivosParaMarcar(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;

            // Obtener eventos en los que está inscrito y que están "En curso"
            $participaciones = EventoParticipacion::where('externo_id', $externoId)
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'fecha_inicio', 'fecha_fin', 'direccion', 'ciudad');
                }])
                ->get()
                ->filter(function($participacion) {
                    // Filtrar solo eventos que están "activos" (en curso)
                    return $participacion->evento && $participacion->evento->estado_dinamico === 'activo';
                })
                ->filter(function($participacion) {
                    // Excluir los que ya marcaron asistencia
                    return $participacion->estado_asistencia !== 'asistido';
                })
                ->map(function($participacion) {
                    return [
                        'participacion_id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'evento_titulo' => $participacion->evento->titulo,
                        'fecha_inicio' => $participacion->evento->fecha_inicio,
                        'ubicacion' => $participacion->evento->direccion,
                        'ciudad' => $participacion->evento->ciudad,
                        'ya_marcado' => $participacion->estado_asistencia === 'asistido',
                    ];
                })
                ->values();

            return response()->json([
                "success" => true,
                "eventos" => $participaciones
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al obtener eventos: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener control de asistencia completo para ONG (Parte 2)
     */
    public function controlAsistencia(Request $request, $eventoId)
    {
        try {
            $ongId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para ver el control de asistencia de este evento"
                ], 403);
            }

            // Obtener participantes registrados
            $participantesRegistrados = EventoParticipacion::where('evento_id', $eventoId)
                ->with(['externo' => function($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }, 'registradoPor:id_usuario,nombre_usuario', 'usuarioModifico:id_usuario,nombre_usuario'])
                ->get()
                ->map(function($participacion) {
                    $user = $participacion->externo;
                    $externo = \App\Models\IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    // Determinar quién validó
                    $validadoPor = null;
                    if ($participacion->registrado_por === $participacion->externo_id) {
                        $validadoPor = 'Usuario externo';
                    } elseif ($participacion->registrado_por) {
                        $validadoPor = $participacion->registradoPor ? $participacion->registradoPor->nombre_usuario : 'ONG';
                    }

                    // Estado de asistencia formateado
                    $estadoAsistencia = '❌ No asistió';
                    if ($participacion->estado_asistencia === 'asistido') {
                        $estadoAsistencia = '✅ Asistió';
                    }

                    // Obtener avatar/foto de perfil
                    $fotoPerfil = null;
                    if ($externo && $externo->foto_perfil) {
                        $fotoPerfil = $externo->foto_perfil;
                        if (!str_starts_with($fotoPerfil, 'http')) {
                            $fotoPerfil = asset('storage/' . $fotoPerfil);
                        }
                    }
                    
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'registrado',
                        'participante' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'email' => $externo ? ($externo->email ?? $user->correo_electronico) : $user->correo_electronico,
                        'telefono' => $externo ? ($externo->phone_number ?? '—') : '—',
                        'fecha_inscripcion' => $participacion->created_at->format('d/m/Y - H:i'),
                        'estado_asistencia' => $estadoAsistencia,
                        'estado_asistencia_raw' => $participacion->estado_asistencia,
                        'validado_por' => $validadoPor,
                        'observaciones' => $participacion->observaciones ?? '-',
                        'comentario' => $participacion->comentario ?? '-', // Comentario de registro
                        'comentario_asistencia' => $participacion->comentario_asistencia ?? '-', // Comentario al marcar asistencia
                        'fecha_registro_asistencia' => $participacion->checkin_at ? $participacion->checkin_at->format('d/m/Y H:i') : null,
                        'fecha_modificacion' => $participacion->fecha_modificacion ? $participacion->fecha_modificacion->format('d/m/Y H:i') : null,
                        'usuario_modifico' => $participacion->usuarioModifico ? $participacion->usuarioModifico->nombre_usuario : null,
                        'ip_registro' => $participacion->ip_registro,
                        'modo_asistencia' => $participacion->modo_asistencia,
                        'asistio' => $participacion->asistio ?? false,
                        'foto_perfil' => $fotoPerfil
                    ];
                });

            // Obtener participantes no registrados (voluntarios)
            $participantesNoRegistrados = \App\Models\EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('estado', '!=', 'rechazada')
                ->get()
                ->map(function($participacion) {
                    $estadoAsistencia = $participacion->asistio ? '✅ Asistió' : '❌ No asistió';
                    
                    // Determinar quién validó (si fue validado desde welcome, mostrar "Validación por ticket")
                    $validadoPor = '—';
                    $fechaRegistroAsistencia = null;
                    if ($participacion->asistio) {
                        // Si tiene ticket_codigo y asistio es true, probablemente fue validado desde welcome
                        if ($participacion->ticket_codigo) {
                            $validadoPor = 'Validación por ticket';
                        } else {
                            $validadoPor = 'ONG';
                        }
                        // Usar updated_at como fecha de validación si asistio es true
                        $fechaRegistroAsistencia = $participacion->updated_at ? $participacion->updated_at->format('d/m/Y H:i') : null;
                    }
                    
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'voluntario',
                        'tipo_usuario' => 'Voluntario',
                        'participante' => trim($participacion->nombres . ' ' . ($participacion->apellidos ?? '')),
                        'email' => $participacion->email ?? '—',
                        'telefono' => $participacion->telefono ?? '—',
                        'fecha_inscripcion' => $participacion->created_at->format('d/m/Y - H:i'),
                        'estado_asistencia' => $estadoAsistencia,
                        'estado_asistencia_raw' => $participacion->asistio ? 'asistido' : 'no_asistido',
                        'validado_por' => $validadoPor,
                        'observaciones' => '-',
                        'comentario' => $participacion->comentario ?? '-', // Comentario de registro del voluntario
                        'comentario_asistencia' => '-', // Los voluntarios no tienen comentario de asistencia separado
                        'fecha_registro_asistencia' => $fechaRegistroAsistencia,
                        'fecha_modificacion' => null,
                        'usuario_modifico' => null,
                        'ip_registro' => null,
                        'modo_asistencia' => $participacion->ticket_codigo ? 'Validación por ticket' : null,
                        'asistio' => $participacion->asistio ?? false,
                        'foto_perfil' => null
                    ];
                });

            // Combinar ambos tipos
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados);

            return response()->json([
                "success" => true,
                "evento" => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'estado' => $evento->estado_dinamico,
                ],
                "participantes" => $participantes,
                "total" => $participantes->count(),
                "asistieron" => $participantes->where('estado_asistencia_raw', 'asistido')->count(),
                "no_asistieron" => $participantes->where('estado_asistencia_raw', 'no_asistido')->count(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al obtener control de asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modificar estado de asistencia por ONG (Parte 2)
     */
    public function modificarAsistencia(Request $request, $participacionId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'estado_asistencia' => 'required|string|in:asistido,no_asistido',
                'observaciones' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Datos inválidos",
                    "details" => $validator->errors(),
                ], 422);
            }

            $ongId = $request->user()->id_usuario;
            
            // Buscar en participantes registrados primero
            $participacion = EventoParticipacion::find($participacionId);
            $esNoRegistrado = false;
            
            if (!$participacion) {
                // Buscar en participantes no registrados
                $participacion = \App\Models\EventoParticipanteNoRegistrado::find($participacionId);
                $esNoRegistrado = true;
            }

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Participación no encontrada"
                ], 404);
            }

            // Verificar que el evento pertenece a la ONG
            $eventoId = $participacion->evento_id;
            $evento = Evento::find($eventoId);
            
            if (!$evento || $evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para modificar esta asistencia"
                ], 403);
            }

            // Verificar que el evento esté en curso o finalizado
            if (!in_array($evento->estado_dinamico, ['activo', 'finalizado'])) {
                return response()->json([
                    "success" => false,
                    "error" => "Solo puedes modificar asistencia en eventos en curso o finalizados"
                ], 400);
            }

            $estadoAsistencia = $request->input('estado_asistencia');
            $observaciones = $request->input('observaciones');

            if ($esNoRegistrado) {
                // Participante no registrado
                $participacion->asistio = ($estadoAsistencia === 'asistido');
                $participacion->save();
            } else {
                // Participante registrado
                $updateData = [
                    'estado_asistencia' => $estadoAsistencia,
                    'asistio' => ($estadoAsistencia === 'asistido'),
                    'fecha_modificacion' => now(),
                    'usuario_modifico' => $ongId,
                    'registrado_por' => $ongId, // Si lo modifica la ONG, se registra como validado por ONG
                ];

                if ($estadoAsistencia === 'asistido' && !$participacion->checkin_at) {
                    $updateData['checkin_at'] = now();
                }

                if ($observaciones) {
                    $updateData['observaciones'] = $observaciones;
                }

                $participacion->update($updateData);
            }

            return response()->json([
                "success" => true,
                "message" => "Estado de asistencia actualizado correctamente",
                "data" => [
                    'participacion_id' => $participacion->id,
                    'estado_asistencia' => $estadoAsistencia,
                    'fecha_modificacion' => now()->format('d/m/Y H:i'),
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al modificar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar lista de asistencia a PDF (Parte 2)
     */
    public function exportarAsistenciaPDF(Request $request, $eventoId)
    {
        try {
            $ongId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento || $evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para exportar esta información"
                ], 403);
            }

            // Obtener datos de asistencia directamente
            $participantesRegistrados = EventoParticipacion::where('evento_id', $eventoId)
                ->with(['externo' => function($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }, 'registradoPor:id_usuario,nombre_usuario', 'usuarioModifico:id_usuario,nombre_usuario'])
                ->get()
                ->map(function($participacion) {
                    $user = $participacion->externo;
                    $externo = \App\Models\IntegranteExterno::where('user_id', $user->id_usuario)->first();
                    
                    $validadoPor = null;
                    if ($participacion->registrado_por === $participacion->externo_id) {
                        $validadoPor = 'Usuario externo';
                    } elseif ($participacion->registrado_por) {
                        $validadoPor = $participacion->registradoPor ? $participacion->registradoPor->nombre_usuario : 'ONG';
                    }

                    $estadoAsistencia = '❌ No asistió';
                    if ($participacion->estado_asistencia === 'asistido') {
                        $estadoAsistencia = '✅ Asistió';
                    }

                    return [
                        'participante' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'fecha_inscripcion' => $participacion->created_at->format('d/m/Y - H:i'),
                        'estado_asistencia' => $estadoAsistencia,
                        'validado_por' => $validadoPor ?? '—',
                        'observaciones' => $participacion->observaciones ?? '—',
                    ];
                });

            $participantesNoRegistrados = \App\Models\EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
                ->where('estado', '!=', 'rechazada')
                ->get()
                ->map(function($participacion) {
                    $estadoAsistencia = $participacion->asistio ? '✅ Asistió' : '❌ No asistió';
                    
                    return [
                        'participante' => trim($participacion->nombres . ' ' . ($participacion->apellidos ?? '')),
                        'fecha_inscripcion' => $participacion->created_at->format('d/m/Y - H:i'),
                        'estado_asistencia' => $estadoAsistencia,
                        'validado_por' => $participacion->asistio ? 'ONG' : '—',
                        'observaciones' => '—',
                    ];
                });

            $participantes = $participantesRegistrados->concat($participantesNoRegistrados);
            $total = $participantes->count();
            $asistieron = $participantes->filter(fn($p) => strpos($p['estado_asistencia'], '✅') !== false)->count();
            $no_asistieron = $total - $asistieron;

            // Generar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.asistencia-pdf', [
                'evento' => $evento,
                'participantes' => $participantes,
                'total' => $total,
                'asistieron' => $asistieron,
                'no_asistieron' => $no_asistieron,
            ]);

            $filename = 'asistencia_' . $evento->id . '_' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Throwable $e) {
            \Log::error('Error exportando PDF de asistencia: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al exportar PDF: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar lista de asistencia a Excel (Parte 2)
     */
    public function exportarAsistenciaExcel(Request $request, $eventoId)
    {
        try {
            $ongId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento || $evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para exportar esta información"
                ], 403);
            }

            // Obtener datos de asistencia
            $controlResponse = $this->controlAsistencia($request, $eventoId);
            $controlData = json_decode($controlResponse->getContent(), true);
            
            if (!$controlData['success']) {
                return $controlResponse;
            }

            $participantes = $controlData['participantes'];

            // Generar Excel (requiere maatwebsite/excel)
            // Por ahora, retornamos JSON que puede convertirse a Excel en el frontend
            return response()->json([
                "success" => true,
                "evento" => $evento,
                "participantes" => $participantes,
                "total" => $controlData['total'],
                "asistieron" => $controlData['asistieron'],
                "no_asistieron" => $controlData['no_asistieron'],
                "formato" => "excel_ready"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al exportar Excel: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los participantes con información completa (para gestión ONG)
     */
    public function obtenerParticipantesCompleto(Request $request, $eventoId)
    {
        try {
            $ongId = $request->user()->id_usuario;
            
            // Verificar que el evento pertenece a la ONG
            $evento = Evento::find($eventoId);
            if (!$evento || $evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para ver esta información"
                ], 403);
            }

            // Obtener todas las participaciones con información del usuario externo
            $participaciones = EventoParticipacion::where('evento_id', $eventoId)
                ->with(['externo:id_usuario,nombre,apellidos,email,celular'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Verificar si el evento ya finalizó
            $eventoFinalizado = $evento->estado === 'finalizado' || 
                               ($evento->fecha_fin && now()->greaterThan($evento->fecha_fin));

            // Mapear datos para incluir información relevante
            $participantesData = $participaciones->map(function($p) use ($eventoFinalizado) {
                return [
                    'id' => $p->id,
                    'estado' => $p->estado,
                    'estado_asistencia' => $p->estado_asistencia,
                    'asistio' => $p->asistio || $p->estado_asistencia === 'asistido',
                    'checkin_at' => $p->checkin_at,
                    'comentario_asistencia' => $p->comentario_asistencia,
                    'ticket_codigo' => $p->ticket_codigo,
                    'created_at' => $p->created_at,
                    'evento_finalizado' => $eventoFinalizado,
                    'externo' => $p->externo ? [
                        'id' => $p->externo->id_usuario,
                        'nombre' => $p->externo->nombre,
                        'apellidos' => $p->externo->apellidos,
                        'email' => $p->externo->email,
                        'celular' => $p->externo->celular
                    ] : null
                ];
            });

            return response()->json([
                "success" => true,
                "participantes" => $participantesData,
                "total" => $participaciones->count(),
                "evento" => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'fecha_inicio' => $evento->fecha_inicio,
                    'fecha_fin' => $evento->fecha_fin,
                    'estado' => $evento->estado
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error obteniendo participantes completo: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al obtener los participantes: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar participantes completos a Excel
     */
    public function exportarParticipantesCompleto(Request $request, $eventoId)
    {
        try {
            $ongId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento || $evento->ong_id != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para exportar esta información"
                ], 403);
            }

            // Obtener datos completos
            $dataResponse = $this->obtenerParticipantesCompleto($request, $eventoId);
            $data = json_decode($dataResponse->getContent(), true);
            
            if (!$data['success']) {
                return $dataResponse;
            }

            return response()->json([
                "success" => true,
                "evento" => $data['evento'],
                "participantes" => $data['participantes'],
                "total" => $data['total'],
                "formato" => "excel_ready"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al exportar participantes: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener eventos en curso del usuario externo autenticado (para welcome)
     */
    public function eventosEnCursoUsuario(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;

            // Obtener eventos en los que está inscrito y que están "En curso" (activo)
            $participaciones = EventoParticipacion::where('externo_id', $externoId)
                ->where('estado', 'aprobada')
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'fecha_inicio', 'fecha_fin', 'direccion', 'ciudad');
                }])
                ->get()
                ->filter(function($participacion) {
                    // Filtrar solo eventos que están "activos" (en curso)
                    return $participacion->evento && $participacion->evento->estado_dinamico === 'activo';
                })
                ->filter(function($participacion) {
                    // Excluir los que ya marcaron asistencia
                    return $participacion->estado_asistencia !== 'asistido';
                })
                ->map(function($participacion) {
                    return [
                        'participacion_id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'evento_titulo' => $participacion->evento->titulo,
                        'fecha_inicio' => $participacion->evento->fecha_inicio,
                        'ubicacion' => $participacion->evento->direccion,
                        'ciudad' => $participacion->evento->ciudad,
                        'ticket_codigo' => $participacion->ticket_codigo,
                        'ya_marcado' => $participacion->estado_asistencia === 'asistido',
                    ];
                })
                ->values();

            return response()->json([
                "success" => true,
                "eventos" => $participaciones,
                "tiene_eventos" => $participaciones->count() > 0
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al obtener eventos: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar ticket y obtener información del evento (sin validar aún)
     */
    public function verificarTicketWelcome(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_codigo' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Debe proporcionar un código de ticket válido"
                ], 422);
            }

            $externoId = $request->user()->id_usuario;
            $ticketCodigo = trim($request->input('ticket_codigo'));

            // Buscar participación por código de ticket
            $participacion = EventoParticipacion::where('ticket_codigo', $ticketCodigo)
                ->orWhereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'direccion', 'ciudad', 'tipo_evento');
                }])
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Código de ticket inválido. Verifique que el código sea correcto."
                ], 404);
            }

            // Verificar que el ticket pertenece al usuario autenticado
            if ($participacion->externo_id != $externoId) {
                return response()->json([
                    "success" => false,
                    "error" => "Este código de ticket no está asociado a tu cuenta. Solo puedes validar tus propios tickets."
                ], 403);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este evento aún no ha sido aprobada."
                ], 400);
            }

            // Verificar que el evento esté en curso
            $evento = $participacion->evento;
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar si el evento permite registro de asistencia
            // Permitir si está activo O si terminó hace menos de 24 horas
            $ahora = now();
            $fechaFin = $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin) : null;
            $eventoTerminado = $fechaFin && $ahora->greaterThan($fechaFin);
            $horasDesdeFinalizacion = $eventoTerminado && $fechaFin ? $ahora->diffInHours($fechaFin) : 0;
            $dentroDe24Horas = $horasDesdeFinalizacion <= 24;
            
            $puedeRegistrarAsistencia = ($evento->estado_dinamico === 'activo') || ($eventoTerminado && $dentroDe24Horas);
            
            if (!$puedeRegistrarAsistencia) {
                $mensaje = $eventoTerminado && !$dentroDe24Horas 
                    ? "El plazo de 24 horas para registrar asistencia ha expirado. Este evento finalizó hace más de 24 horas."
                    : "Este evento aún no ha comenzado. Solo puedes validar asistencia durante el evento o hasta 24 horas después de finalizar.";
                    
                return response()->json([
                    "success" => false,
                    "error" => $mensaje
                ], 400);
            }

            // Verificar si ya fue usado
            $yaUsado = $participacion->estado_asistencia === 'asistido' && $participacion->checkin_at;

            // Formatear fecha
            $fechaInicio = $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio) : null;
            $fechaFormateada = $fechaInicio ? $fechaInicio->format('d/m/Y H:i') : 'No especificada';

            return response()->json([
                "success" => true,
                "data" => [
                    'participacion_id' => $participacion->id,
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'evento_descripcion' => $evento->descripcion,
                    'evento_tipo' => $evento->tipo_evento,
                    'fecha_inicio' => $fechaFormateada,
                    'ubicacion' => $evento->direccion,
                    'ciudad' => $evento->ciudad,
                    'ticket_codigo' => $participacion->ticket_codigo,
                    'ya_validado' => $yaUsado,
                    'fecha_validacion_anterior' => $yaUsado ? $participacion->checkin_at->format('d/m/Y H:i') : null,
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error verificando ticket desde welcome: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al verificar ticket: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar asistencia desde welcome.php mediante código de ticket o QR
     */
    public function validarAsistenciaWelcome(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_codigo' => 'required|string',
                'modo_validacion' => 'nullable|string|in:QR,Manual', // Para distinguir si viene de QR o manual
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Debe proporcionar un código de ticket válido"
                ], 422);
            }

            $externoId = $request->user()->id_usuario;
            $ticketCodigo = trim($request->input('ticket_codigo'));

            // Buscar participación por código de ticket
            $participacion = EventoParticipacion::where('ticket_codigo', $ticketCodigo)
                ->orWhereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Código de ticket inválido. Verifique que el código sea correcto."
                ], 404);
            }

            // Verificar que el ticket pertenece al usuario autenticado
            if ($participacion->externo_id != $externoId) {
                return response()->json([
                    "success" => false,
                    "error" => "Este código de ticket no está asociado a tu cuenta. Solo puedes validar tus propios tickets."
                ], 403);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este evento aún no ha sido aprobada."
                ], 400);
            }

            // Verificar que el evento esté en curso
            $evento = Evento::find($participacion->evento_id);
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar si el evento permite registro de asistencia (hasta 24 horas después de finalizar)
            $ahora = now();
            $fechaFin = $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin) : null;
            $eventoTerminado = $fechaFin && $ahora->greaterThan($fechaFin);
            $horasDesdeFinalizacion = $eventoTerminado && $fechaFin ? $ahora->diffInHours($fechaFin) : 0;
            $dentroDe24Horas = $horasDesdeFinalizacion <= 24;
            
            $puedeRegistrarAsistencia = ($evento->estado_dinamico === 'activo') || ($eventoTerminado && $dentroDe24Horas);
            
            if (!$puedeRegistrarAsistencia) {
                $mensaje = $eventoTerminado && !$dentroDe24Horas 
                    ? "El plazo de 24 horas para registrar asistencia ha expirado. Este evento finalizó hace más de 24 horas."
                    : "Este evento aún no ha comenzado. Solo puedes validar asistencia durante el evento o hasta 24 horas después de finalizar.";
                    
                return response()->json([
                    "success" => false,
                    "error" => $mensaje
                ], 400);
            }

            // Verificar que no haya sido usado previamente
            if ($participacion->estado_asistencia === 'asistido' && $participacion->checkin_at) {
                return response()->json([
                    "success" => false,
                    "error" => "Este código de ticket ya fue utilizado. No se puede reutilizar para validar asistencia.",
                    "fecha_uso_anterior" => $participacion->checkin_at->format('d/m/Y H:i')
                ], 409);
            }

            // Obtener IP del usuario
            $ipRegistro = $request->ip();
            $modoValidacion = $request->input('modo_validacion', 'Manual'); // Por defecto Manual si no se especifica
            $comentario = $request->input('comentario'); // Comentario al registrar asistencia

            // Registrar asistencia
            $dataUpdate = [
                'estado_asistencia' => 'asistido',
                'asistio' => true,
                'checkin_at' => now(),
                'modo_asistencia' => $modoValidacion === 'QR' ? 'QR' : 'Validación por ticket',
                'ip_registro' => $ipRegistro,
                'fecha_modificacion' => now(),
                'usuario_modifico' => $externoId,
                'registrado_por' => $externoId,
                'observaciones' => 'Validación desde welcome.php - Validación usuario',
            ];
            
            // Agregar comentario si se proporcionó
            if ($comentario && trim($comentario) !== '') {
                $dataUpdate['comentario_asistencia'] = trim($comentario);
            }
            
            $participacion->update($dataUpdate);

            return response()->json([
                "success" => true,
                "message" => "¡Asistencia validada correctamente!",
                "data" => [
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'fecha_registro' => $participacion->checkin_at->format('d/m/Y H:i'),
                    'estado_asistencia' => $participacion->estado_asistencia,
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error validando asistencia desde welcome: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al validar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar asistencia de usuario no registrado desde welcome.php
     * Requiere: nombre completo + código de ticket
     */
    public function validarAsistenciaNoRegistradoWelcome(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'ticket_codigo' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Debe proporcionar nombres, apellidos y código de ticket"
                ], 422);
            }

            $nombres = trim($request->input('nombres'));
            $apellidos = trim($request->input('apellidos'));
            $ticketCodigo = trim($request->input('ticket_codigo'));

            // Buscar participación no registrada por código de ticket y nombre
            $participacion = EventoParticipanteNoRegistrado::where('ticket_codigo', $ticketCodigo)
                ->orWhereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                ->where('nombres', $nombres)
                ->where('apellidos', $apellidos)
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "No se encontró una participación con esos datos. Verifica que el nombre y código de ticket sean correctos."
                ], 404);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este evento aún no ha sido aprobada."
                ], 400);
            }

            // Verificar que el evento esté en curso
            $evento = Evento::find($participacion->evento_id);
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar si el evento permite registro de asistencia (hasta 24 horas después de finalizar)
            $ahora = now();
            $fechaFin = $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin) : null;
            $eventoTerminado = $fechaFin && $ahora->greaterThan($fechaFin);
            $horasDesdeFinalizacion = $eventoTerminado && $fechaFin ? $ahora->diffInHours($fechaFin) : 0;
            $dentroDe24Horas = $horasDesdeFinalizacion <= 24;
            
            $puedeRegistrarAsistencia = ($evento->estado_dinamico === 'activo') || ($eventoTerminado && $dentroDe24Horas);
            
            if (!$puedeRegistrarAsistencia) {
                $mensaje = $eventoTerminado && !$dentroDe24Horas 
                    ? "El plazo de 24 horas para registrar asistencia ha expirado. Este evento finalizó hace más de 24 horas."
                    : "Este evento aún no ha comenzado. Solo puedes validar asistencia durante el evento o hasta 24 horas después de finalizar.";
                    
                return response()->json([
                    "success" => false,
                    "error" => $mensaje
                ], 400);
            }

            // Verificar que no haya sido usado previamente
            if ($participacion->asistio) {
                return response()->json([
                    "success" => false,
                    "error" => "Ya registraste tu asistencia para este evento. No se puede reutilizar el código de ticket."
                ], 409);
            }

            // Obtener IP del usuario
            $ipRegistro = $request->ip();

            // Registrar asistencia
            $participacion->update([
                'asistio' => true,
            ]);

            return response()->json([
                "success" => true,
                "message" => "¡Asistencia validada correctamente!",
                "data" => [
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'participante' => trim($nombres . ' ' . $apellidos),
                    'fecha_registro' => now()->format('d/m/Y H:i'),
                    'estado_asistencia' => 'asistido',
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error validando asistencia no registrado desde welcome: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al validar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar ticket de usuario no registrado (sin autenticación)
     */
    public function verificarTicketNoRegistradoWelcome(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'ticket_codigo' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Debe proporcionar nombres, apellidos y código de ticket"
                ], 422);
            }

            $nombres = trim($request->input('nombres'));
            $apellidos = trim($request->input('apellidos'));
            $ticketCodigo = trim($request->input('ticket_codigo'));

            // Buscar participación
            $participacion = EventoParticipanteNoRegistrado::where('ticket_codigo', $ticketCodigo)
                ->orWhereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                ->where('nombres', $nombres)
                ->where('apellidos', $apellidos)
                ->with(['evento' => function($query) {
                    $query->select('id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'direccion', 'ciudad', 'tipo_evento');
                }])
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "No se encontró una participación con esos datos. Verifica que el nombre y código de ticket sean correctos."
                ], 404);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este evento aún no ha sido aprobada."
                ], 400);
            }

            // Verificar que el evento esté en curso
            $evento = $participacion->evento;
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "error" => "Evento no encontrado"
                ], 404);
            }

            // Verificar si el evento permite registro de asistencia (hasta 24 horas después de finalizar)
            $ahora = now();
            $fechaFin = $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin) : null;
            $eventoTerminado = $fechaFin && $ahora->greaterThan($fechaFin);
            $horasDesdeFinalizacion = $eventoTerminado && $fechaFin ? $ahora->diffInHours($fechaFin) : 0;
            $dentroDe24Horas = $horasDesdeFinalizacion <= 24;
            
            $puedeRegistrarAsistencia = ($evento->estado_dinamico === 'activo') || ($eventoTerminado && $dentroDe24Horas);
            
            if (!$puedeRegistrarAsistencia) {
                $mensaje = $eventoTerminado && !$dentroDe24Horas 
                    ? "El plazo de 24 horas para registrar asistencia ha expirado. Este evento finalizó hace más de 24 horas."
                    : "Este evento aún no ha comenzado. Solo puedes validar asistencia durante el evento o hasta 24 horas después de finalizar.";
                    
                return response()->json([
                    "success" => false,
                    "error" => $mensaje
                ], 400);
            }

            // Verificar si ya fue usado
            $yaUsado = $participacion->asistio;

            // Formatear fecha
            $fechaInicio = $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio) : null;
            $fechaFormateada = $fechaInicio ? $fechaInicio->format('d/m/Y H:i') : 'No especificada';

            return response()->json([
                "success" => true,
                "data" => [
                    'participacion_id' => $participacion->id,
                    'evento_id' => $evento->id,
                    'evento_titulo' => $evento->titulo,
                    'evento_descripcion' => $evento->descripcion,
                    'evento_tipo' => $evento->tipo_evento,
                    'fecha_inicio' => $fechaFormateada,
                    'ubicacion' => $evento->direccion,
                    'ciudad' => $evento->ciudad,
                    'ticket_codigo' => $participacion->ticket_codigo,
                    'participante' => trim($nombres . ' ' . $apellidos),
                    'ya_validado' => $yaUsado,
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error verificando ticket no registrado desde welcome: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al verificar ticket: " . $e->getMessage()
            ], 500);
        }
    }
}
