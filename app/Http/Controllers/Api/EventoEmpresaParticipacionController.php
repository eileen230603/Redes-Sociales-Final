<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoEmpresaParticipacion;
use App\Models\Empresa;
use App\Models\Ong;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventoEmpresaParticipacionController extends Controller
{
    /**
     * Asignar empresas colaboradoras a un evento (por ONG)
     */
    public function asignarEmpresas(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para asignar empresas a este evento'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'empresas' => 'required|array',
                'empresas.*' => 'required|integer|exists:empresas,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $empresasIds = $request->empresas;

            // TRANSACCIÓN: Asignar empresas + crear notificaciones + actualizar campo patrocinadores
            $empresasAsignadas = DB::transaction(function () use ($eventoId, $empresasIds, $evento) {
                $asignadas = [];

                \Log::info("Iniciando asignación de empresas al evento {$eventoId}. Empresas: " . implode(', ', $empresasIds));

                foreach ($empresasIds as $empresaId) {
                    try {
                        // Verificar si ya está asignada
                        $existe = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                            ->where('empresa_id', $empresaId)
                            ->exists();

                        if (!$existe) {
                            // Crear participación
                            $participacion = EventoEmpresaParticipacion::create([
                                'evento_id' => $eventoId,
                                'empresa_id' => $empresaId,
                                'estado' => 'asignada',
                                'activo' => true,
                            ]);

                            \Log::info("Participación creada para empresa {$empresaId} en evento {$eventoId}");

                            // Crear notificación para la empresa
                            $notificacion = $this->crearNotificacionEmpresa($evento, $empresaId);
                            
                            if ($notificacion) {
                                \Log::info("Notificación creada exitosamente para empresa {$empresaId}");
                            } else {
                                \Log::warning("No se pudo crear notificación para empresa {$empresaId}");
                            }

                            $asignadas[] = $participacion;
                        } else {
                            \Log::info("La empresa {$empresaId} ya está asignada al evento {$eventoId}");
                        }
                    } catch (\Throwable $e) {
                        \Log::error("Error asignando empresa {$empresaId} al evento {$eventoId}: " . $e->getMessage());
                        // Continuar con las demás empresas aunque una falle
                    }
                }

                // Actualizar el campo patrocinadores del evento con los IDs de las empresas colaboradoras
                if (count($asignadas) > 0) {
                    try {
                        // Obtener todos los IDs de empresas colaboradoras del evento (incluyendo las ya existentes)
                        $todasLasEmpresasIds = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                            ->where('activo', true)
                            ->pluck('empresa_id')
                            ->toArray();

                        // Actualizar el campo patrocinadores con los IDs de las empresas
                        $evento->patrocinadores = $todasLasEmpresasIds;
                        $evento->save();

                        \Log::info("Campo patrocinadores actualizado para evento {$eventoId} con " . count($todasLasEmpresasIds) . " empresa(s)");
                    } catch (\Throwable $e) {
                        \Log::error("Error actualizando campo patrocinadores del evento {$eventoId}: " . $e->getMessage());
                        // No fallar la transacción si solo falla la actualización del campo
                    }
                }

                \Log::info("Total de empresas asignadas: " . count($asignadas));
                return $asignadas;
            });

            return response()->json([
                'success' => true,
                'message' => count($empresasAsignadas) . ' empresa(s) asignada(s) correctamente',
                'empresas_asignadas' => count($empresasAsignadas),
                'data' => $empresasAsignadas
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al asignar empresas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al asignar empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover empresas colaboradoras de un evento (por ONG)
     */
    public function removerEmpresas(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria del evento
            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para remover empresas de este evento'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'empresas' => 'required|array',
                'empresas.*' => 'required|integer|exists:empresas,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $empresasIds = $request->empresas;

            // TRANSACCIÓN: Remover empresas + actualizar campo patrocinadores
            $removidas = DB::transaction(function () use ($eventoId, $empresasIds, $evento) {
                $count = 0;

                foreach ($empresasIds as $empresaId) {
                    $deleted = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                        ->where('empresa_id', $empresaId)
                        ->delete();
                    
                    if ($deleted) {
                        $count++;
                    }
                }

                // Actualizar el campo patrocinadores del evento después de remover empresas
                if ($count > 0) {
                    try {
                        // Obtener todos los IDs de empresas colaboradoras restantes del evento
                        $empresasRestantesIds = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                            ->where('activo', true)
                            ->pluck('empresa_id')
                            ->toArray();

                        // Actualizar el campo patrocinadores con los IDs restantes
                        $evento->patrocinadores = $empresasRestantesIds;
                        $evento->save();

                        \Log::info("Campo patrocinadores actualizado para evento {$eventoId} después de remover empresas. Empresas restantes: " . count($empresasRestantesIds));
                    } catch (\Throwable $e) {
                        \Log::error("Error actualizando campo patrocinadores del evento {$eventoId} después de remover: " . $e->getMessage());
                    }
                }

                return $count;
            });

            return response()->json([
                'success' => true,
                'message' => $removidas . ' empresa(s) removida(s) correctamente',
                'empresas_removidas' => $removidas
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al remover empresas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al remover empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar participación de empresa (por la empresa misma)
     */
    public function confirmarParticipacion(Request $request, $eventoId)
    {
        try {
            $empresaId = $request->user()->id_usuario;
            
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            $participacion = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$participacion) {
                return response()->json([
                    'success' => false,
                    'error' => 'No estás asignada como empresa colaboradora de este evento'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'tipo_colaboracion' => 'nullable|string|max:255',
                'descripcion_colaboracion' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $participacion->estado = 'confirmada';
            if ($request->has('tipo_colaboracion')) {
                $participacion->tipo_colaboracion = $request->tipo_colaboracion;
            }
            if ($request->has('descripcion_colaboracion')) {
                $participacion->descripcion_colaboracion = $request->descripcion_colaboracion;
            }
            $participacion->save();

            // Crear notificación para la ONG
            $this->crearNotificacionConfirmacion($evento, $empresaId);

            return response()->json([
                'success' => true,
                'message' => 'Participación confirmada correctamente',
                'participacion' => $participacion
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al confirmar participación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al confirmar participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver empresas participantes de un evento
     */
    public function empresasParticipantes(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos: ONG propietaria o empresa participante
            $usuarioId = $request->user()->id_usuario;
            $esOngPropietaria = $evento->ong_id == $usuarioId;
            $esEmpresaParticipante = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('empresa_id', $usuarioId)
                ->exists();

            if (!$esOngPropietaria && !$esEmpresaParticipante) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver las empresas participantes'
                ], 403);
            }

            $participaciones = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('activo', true)
                ->with(['empresa.usuario'])
                ->get()
                ->map(function($participacion) {
                    $empresa = $participacion->empresa;
                    return [
                        'id' => $participacion->id,
                        'empresa_id' => $participacion->empresa_id,
                        'nombre_empresa' => $empresa->nombre_empresa ?? 'N/A',
                        'NIT' => $empresa->NIT ?? null,
                        'telefono' => $empresa->telefono ?? null,
                        'sitio_web' => $empresa->sitio_web ?? null,
                        'foto_perfil' => $empresa->foto_perfil_url ?? null,
                        'estado' => $participacion->estado,
                        'asistio' => $participacion->asistio,
                        'tipo_colaboracion' => $participacion->tipo_colaboracion,
                        'descripcion_colaboracion' => $participacion->descripcion_colaboracion,
                        'fecha_asignacion' => $participacion->created_at,
                        'fecha_confirmacion' => $participacion->estado === 'confirmada' ? $participacion->updated_at : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'empresas' => $participaciones,
                'count' => $participaciones->count()
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al obtener empresas participantes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener empresas participantes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver eventos en los que participa una empresa (como colaboradora o patrocinadora)
     */
    public function misEventos(Request $request)
    {
        try {
            $empresaId = $request->user()->id_usuario;
            
            \Log::info("Buscando eventos para empresa con user_id: {$empresaId}");

            // 1. Obtener eventos donde la empresa participa (colaboradora o patrocinadora) desde tabla de participaciones
            $participaciones = EventoEmpresaParticipacion::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->with(['evento.ong'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info("Participaciones encontradas en tabla: " . $participaciones->count());
            
            $participaciones = $participaciones->filter(function($participacion) {
                    return $participacion->evento !== null;
                })
                ->map(function($participacion) {
                    // Determinar el tipo de relación basado en tipo_colaboracion
                    $tipoRelacion = 'colaboradora';
                    if ($participacion->tipo_colaboracion === 'Patrocinador' || 
                        stripos($participacion->tipo_colaboracion ?? '', 'patrocinador') !== false) {
                        $tipoRelacion = 'patrocinadora';
                    }
                    $evento = $participacion->evento;
                    
                    // Procesar imágenes del evento
                    $imagenes = [];
                    if ($evento->imagenes) {
                        if (is_array($evento->imagenes)) {
                            $imagenes = $evento->imagenes;
                        } elseif (is_string($evento->imagenes)) {
                            $decoded = json_decode($evento->imagenes, true);
                            $imagenes = is_array($decoded) ? $decoded : [];
                        }
                    }

                    // Procesar patrocinadores
                    $patrocinadores = [];
                    if ($evento->patrocinadores) {
                        if (is_array($evento->patrocinadores)) {
                            $patrocinadores = $evento->patrocinadores;
                        } elseif (is_string($evento->patrocinadores)) {
                            $decoded = json_decode($evento->patrocinadores, true);
                            $patrocinadores = is_array($decoded) ? $decoded : [];
                        }
                    }

                    return [
                        'id' => $participacion->id,
                        'evento_id' => $participacion->evento_id,
                        'tipo_relacion' => $tipoRelacion, // Determinar según tipo_colaboracion
                        'estado_participacion' => $participacion->estado,
                        'asistio' => $participacion->asistio,
                        'tipo_colaboracion' => $participacion->tipo_colaboracion,
                        'descripcion_colaboracion' => $participacion->descripcion_colaboracion,
                        'fecha_asignacion' => $participacion->created_at ? $participacion->created_at->toISOString() : null,
                        'evento' => [
                            'id' => $evento->id,
                            'titulo' => $evento->titulo,
                            'descripcion' => $evento->descripcion,
                            'tipo_evento' => $evento->tipo_evento,
                            'fecha_inicio' => $evento->fecha_inicio ? $evento->fecha_inicio->toISOString() : null,
                            'fecha_fin' => $evento->fecha_fin ? $evento->fecha_fin->toISOString() : null,
                            'fecha_limite_inscripcion' => $evento->fecha_limite_inscripcion ? $evento->fecha_limite_inscripcion->toISOString() : null,
                            'fecha_finalizacion' => $evento->fecha_finalizacion ? $evento->fecha_finalizacion->toISOString() : null,
                            'capacidad_maxima' => $evento->capacidad_maxima,
                            'inscripcion_abierta' => $evento->inscripcion_abierta,
                            'estado' => $evento->estado,
                            'lat' => $evento->lat,
                            'lng' => $evento->lng,
                            'direccion' => $evento->direccion,
                            'ciudad' => $evento->ciudad,
                            'imagenes' => $imagenes,
                            'patrocinadores' => $patrocinadores,
                            'ong_id' => $evento->ong_id,
                            'ong' => $evento->ong ? [
                                'user_id' => $evento->ong->user_id,
                                'nombre_ong' => $evento->ong->nombre_ong,
                            ] : null,
                        ]
                    ];
                })
                ->values();

            // 2. Obtener eventos donde la empresa es patrocinadora (desde campo JSON patrocinadores)
            // Esto es para eventos antiguos que no tienen registro en la tabla pero sí en el campo JSON
            // También migrar automáticamente estos eventos a la tabla
            $todosEventos = Evento::with(['ong'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info("Total de eventos en sistema: " . $todosEventos->count());
            
            $eventosPatrocinador = $todosEventos->filter(function($evento) use ($empresaId) {
                    // Verificar que el evento tenga patrocinadores y que contenga el ID de la empresa
                    $patrocinadores = $evento->patrocinadores;
                    
                    if (empty($patrocinadores)) {
                        return false;
                    }
                    
                    // Convertir a array si es string
                    if (is_string($patrocinadores)) {
                        $decoded = json_decode($patrocinadores, true);
                        $patrocinadores = is_array($decoded) ? $decoded : [];
                    }
                    
                    if (!is_array($patrocinadores) || empty($patrocinadores)) {
                        return false;
                    }
                    
                    // Verificar si el ID de la empresa está en el array (como número o string)
                    $esPatrocinador = in_array($empresaId, $patrocinadores, true) || 
                           in_array((string)$empresaId, $patrocinadores, true) ||
                           in_array((int)$empresaId, array_map('intval', $patrocinadores), true);
                    
                    // Si es patrocinador pero no tiene registro en la tabla, crearlo automáticamente
                    if ($esPatrocinador) {
                        $existeEnTabla = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                            ->where('empresa_id', $empresaId)
                            ->exists();
                        
                        if (!$existeEnTabla) {
                            try {
                                EventoEmpresaParticipacion::create([
                                    'evento_id' => $evento->id,
                                    'empresa_id' => $empresaId,
                                    'estado' => 'asignada',
                                    'activo' => true,
                                    'tipo_colaboracion' => 'Patrocinador',
                                ]);
                                \Log::info("Migrado automáticamente: Patrocinador {$empresaId} agregado a evento {$evento->id} desde campo JSON");
                            } catch (\Throwable $e) {
                                \Log::error("Error al migrar patrocinador {$empresaId} para evento {$evento->id}: " . $e->getMessage());
                            }
                        }
                    }
                    
                    return $esPatrocinador;
                })
                ->map(function($evento) use ($empresaId) {
                    // Procesar imágenes del evento
                    $imagenes = [];
                    if ($evento->imagenes) {
                        if (is_array($evento->imagenes)) {
                            $imagenes = $evento->imagenes;
                        } elseif (is_string($evento->imagenes)) {
                            $decoded = json_decode($evento->imagenes, true);
                            $imagenes = is_array($decoded) ? $decoded : [];
                        }
                    }

                    // Procesar patrocinadores
                    $patrocinadores = [];
                    if ($evento->patrocinadores) {
                        if (is_array($evento->patrocinadores)) {
                            $patrocinadores = $evento->patrocinadores;
                        } elseif (is_string($evento->patrocinadores)) {
                            $decoded = json_decode($evento->patrocinadores, true);
                            $patrocinadores = is_array($decoded) ? $decoded : [];
                        }
                    }

                    return [
                        'id' => null, // No hay participación, solo es patrocinador
                        'evento_id' => $evento->id,
                        'tipo_relacion' => 'patrocinadora', // Marcar como patrocinadora
                        'estado_participacion' => null, // No aplica para patrocinadores
                        'asistio' => false,
                        'tipo_colaboracion' => null,
                        'descripcion_colaboracion' => null,
                        'fecha_asignacion' => $evento->created_at ? $evento->created_at->toISOString() : null,
                        'evento' => [
                            'id' => $evento->id,
                            'titulo' => $evento->titulo,
                            'descripcion' => $evento->descripcion,
                            'tipo_evento' => $evento->tipo_evento,
                            'fecha_inicio' => $evento->fecha_inicio ? $evento->fecha_inicio->toISOString() : null,
                            'fecha_fin' => $evento->fecha_fin ? $evento->fecha_fin->toISOString() : null,
                            'fecha_limite_inscripcion' => $evento->fecha_limite_inscripcion ? $evento->fecha_limite_inscripcion->toISOString() : null,
                            'fecha_finalizacion' => $evento->fecha_finalizacion ? $evento->fecha_finalizacion->toISOString() : null,
                            'capacidad_maxima' => $evento->capacidad_maxima,
                            'inscripcion_abierta' => $evento->inscripcion_abierta,
                            'estado' => $evento->estado,
                            'lat' => $evento->lat,
                            'lng' => $evento->lng,
                            'direccion' => $evento->direccion,
                            'ciudad' => $evento->ciudad,
                            'imagenes' => $imagenes,
                            'patrocinadores' => $patrocinadores,
                            'ong_id' => $evento->ong_id,
                            'ong' => $evento->ong ? [
                                'user_id' => $evento->ong->user_id,
                                'nombre_ong' => $evento->ong->nombre_ong,
                            ] : null,
                        ]
                    ];
                })
                ->values();

            // 3. Combinar ambos tipos de eventos y eliminar duplicados
            // Si un evento aparece como colaborador y patrocinador, mantener solo como colaborador (prioridad)
            $eventosCombinados = collect($participaciones)
                ->concat($eventosPatrocinador)
                ->groupBy('evento_id')
                ->map(function($grupo) {
                    // Si hay múltiples entradas para el mismo evento, priorizar la de tipo "colaboradora"
                    if ($grupo->count() > 1) {
                        $colaboradora = $grupo->firstWhere('tipo_relacion', 'colaboradora');
                        return $colaboradora ?: $grupo->first();
                    }
                    return $grupo->first();
                })
                ->values()
                ->sortByDesc(function($item) {
                    return $item['fecha_asignacion'] ?? $item['evento']['fecha_inicio'] ?? '';
                })
                ->values();

            \Log::info("Eventos combinados encontrados: " . $eventosCombinados->count());
            \Log::info("Colaboradores: " . $participaciones->count() . ", Patrocinadores: " . $eventosPatrocinador->count());

            return response()->json([
                'success' => true,
                'eventos' => $eventosCombinados,
                'count' => $eventosCombinados->count(),
                'colaboradores' => $participaciones->count(),
                'patrocinadores' => $eventosPatrocinador->count(),
                'debug' => [
                    'empresa_id' => $empresaId,
                    'participaciones_tabla' => $participaciones->count(),
                    'patrocinadores_json' => $eventosPatrocinador->count(),
                    'total_combinados' => $eventosCombinados->count()
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al obtener mis eventos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener eventos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si una empresa está asignada a un evento
     */
    public function verificarParticipacion(Request $request, $eventoId)
    {
        try {
            $empresaId = $request->user()->id_usuario;

            $participacion = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('empresa_id', $empresaId)
                ->where('activo', true)
                ->first();

            return response()->json([
                'success' => true,
                'participando' => $participacion !== null,
                'participacion' => $participacion ? [
                    'id' => $participacion->id,
                    'estado' => $participacion->estado,
                    'asistio' => $participacion->asistio,
                    'tipo_colaboracion' => $participacion->tipo_colaboracion,
                ] : null
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la empresa cuando es asignada
     */
    private function crearNotificacionEmpresa(Evento $evento, $empresaId)
    {
        try {
            $empresa = Empresa::where('user_id', $empresaId)->first();
            if (!$empresa) {
                \Log::warning("No se encontró empresa con user_id: {$empresaId}");
                return;
            }

            // Obtener información de la ONG que asignó el evento
            $ong = Ong::where('user_id', $evento->ong_id)->first();
            $nombreOng = $ong ? $ong->nombre_ong : 'una ONG';

            // Formatear fecha del evento si existe
            $fechaEvento = '';
            if ($evento->fecha_inicio) {
                try {
                    $fecha = new \DateTime($evento->fecha_inicio);
                    $fechaEvento = ' el ' . $fecha->format('d/m/Y');
                    if ($evento->fecha_inicio && strpos($evento->fecha_inicio, ' ') !== false) {
                        $fechaEvento .= ' a las ' . $fecha->format('H:i');
                    }
                } catch (\Exception $e) {
                    // Si hay error al parsear la fecha, simplemente no la incluimos
                }
            }

            // Crear la notificación para la empresa
            $notificacion = Notificacion::create([
                'ong_id' => null, // No es notificación para ONG, es para empresa
                'evento_id' => $evento->id,
                'externo_id' => $empresaId, // user_id de la empresa (se usa externo_id para empresas)
                'tipo' => 'empresa_asignada',
                'titulo' => 'Nuevo evento asignado',
                'mensaje' => "{$nombreOng} te ha asignado como empresa colaboradora del evento \"{$evento->titulo}\"{$fechaEvento}. El evento ya está disponible en tu lista de eventos colaboradores. Puedes ver los detalles y confirmar tu participación.",
                'leida' => false
            ]);

            \Log::info("Notificación creada para empresa {$empresa->nombre_empresa} (user_id: {$empresaId}) sobre evento {$evento->id}");

            return $notificacion;

        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de empresa: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Crear notificación para la ONG cuando una empresa confirma participación
     */
    private function crearNotificacionConfirmacion(Evento $evento, $empresaId)
    {
        try {
            $empresa = Empresa::where('user_id', $empresaId)->first();
            if (!$empresa) return;

            Notificacion::create([
                'ong_id' => $evento->ong_id,
                'evento_id' => $evento->id,
                'externo_id' => $empresaId,
                'tipo' => 'empresa_confirmada',
                'titulo' => 'Empresa confirmó participación',
                'mensaje' => "{$empresa->nombre_empresa} confirmó su participación en el evento \"{$evento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de confirmación: ' . $e->getMessage());
        }
    }
}

