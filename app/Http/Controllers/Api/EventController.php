<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\User;
use App\Models\Empresa;
use App\Models\IntegranteExterno;

class EventController extends Controller
{
    private function safeArray($value)
    {
        if (is_array($value)) return $value;
        if ($value === null) return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // ======================================================
    //  LISTAR EVENTOS DE UNA ONG
    // ======================================================
    public function indexByOng($ongId)
    {
        try {
            // Convertir a entero para asegurar el tipo correcto
            $ongId = (int) $ongId;
            
            \Log::info("Buscando eventos para ONG ID: {$ongId}");
            
            $eventos = Evento::where('ong_id', $ongId)
                ->orderBy('id', 'desc')
                ->get();

            \Log::info("Eventos encontrados: " . $eventos->count());

            return response()->json([
                'success' => true,
                'eventos' => $eventos,
                'ong_id' => $ongId,
                'count' => $eventos->count()
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error al obtener eventos: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'ong_id' => $ongId
            ], 500);
        }
    }

    // ======================================================
    //  LISTAR EVENTOS PUBLICADOS PARA EXTERNOS
    // ======================================================
    public function indexAll()
    {
        try {
            \Log::info("Buscando eventos publicados para externos");
            
            $eventos = Evento::where('estado', 'publicado')
                ->orderBy('fecha_inicio', 'asc')
                ->get();

            \Log::info("Eventos publicados encontrados: " . $eventos->count());

            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->safeArray($e->patrocinadores);
                $e->invitados = $this->safeArray($e->invitados);
                $e->imagenes = $this->safeArray($e->imagenes);
                return $e;
            });

            return response()->json([
                'success' => true,
                'eventos' => $eventos,
                'count' => $eventos->count()
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error al obtener eventos publicados: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "error"   => $e->getMessage(),
                "file"    => $e->getFile(),
                "line"    => $e->getLine(),
            ], 500);
        }
    }

    // ======================================================
    //  CREAR EVENTO
    // ======================================================
    public function store(Request $request)
    {
        try {
            // Convertir campos JSON string a arrays si vienen como string
            $data = $request->all();
            
            // Procesar patrocinadores
            if (!isset($data['patrocinadores']) || empty($data['patrocinadores'])) {
                $data['patrocinadores'] = [];
            } elseif (is_string($data['patrocinadores'])) {
                $decoded = json_decode($data['patrocinadores'], true);
                $data['patrocinadores'] = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($data['patrocinadores'])) {
                $data['patrocinadores'] = [];
            }
            
            // Procesar invitados
            if (!isset($data['invitados']) || empty($data['invitados'])) {
                $data['invitados'] = [];
            } elseif (is_string($data['invitados'])) {
                $decoded = json_decode($data['invitados'], true);
                $data['invitados'] = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($data['invitados'])) {
                $data['invitados'] = [];
            }
            
            // Procesar auspiciadores
            if (!isset($data['auspiciadores']) || empty($data['auspiciadores'])) {
                $data['auspiciadores'] = [];
            } elseif (is_string($data['auspiciadores'])) {
                $decoded = json_decode($data['auspiciadores'], true);
                $data['auspiciadores'] = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($data['auspiciadores'])) {
                $data['auspiciadores'] = [];
            }
            
            // Procesar imágenes (puede venir como array de archivos o JSON string)
            if (!isset($data['imagenes']) || empty($data['imagenes'])) {
                $data['imagenes'] = [];
            } elseif (is_string($data['imagenes'])) {
                $decoded = json_decode($data['imagenes'], true);
                $data['imagenes'] = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($data['imagenes'])) {
                $data['imagenes'] = [];
            }
            
            $validator = Validator::make($data, [
                'ong_id' => 'required|exists:ongs,user_id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'tipo_evento' => 'required|string|max:100',
                'fecha_inicio' => 'required|date|after:now',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'fecha_limite_inscripcion' => 'nullable|date|before:fecha_inicio',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'estado' => 'required|in:borrador,publicado,cancelado',
                'ciudad' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'inscripcion_abierta' => 'nullable|boolean',
                'patrocinadores' => 'nullable|array',
                'invitados' => 'nullable|array',
                'imagenes' => 'nullable|array',
                'auspiciadores' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $evento = Evento::create([
                "ong_id" => $data['ong_id'],
                "titulo" => $data['titulo'],
                "descripcion" => $data['descripcion'] ?? null,
                "tipo_evento" => $data['tipo_evento'],
                "fecha_inicio" => $data['fecha_inicio'],
                "fecha_fin" => $data['fecha_fin'] ?? null,
                "fecha_limite_inscripcion" => $data['fecha_limite_inscripcion'] ?? null,
                "capacidad_maxima" => $data['capacidad_maxima'] ?? null,
                "estado" => $data['estado'],
                "ciudad" => $data['ciudad'] ?? null,
                "direccion" => $data['direccion'] ?? null,
                "lat" => $data['lat'] ?? null,
                "lng" => $data['lng'] ?? null,
                "inscripcion_abierta" => $data['inscripcion_abierta'] ?? true,
                "patrocinadores" => $this->safeArray($data['patrocinadores'] ?? []),
                "invitados" => $this->safeArray($data['invitados'] ?? []),
                "imagenes" => $this->safeArray($data['imagenes'] ?? []),
                "auspiciadores" => $this->safeArray($data['auspiciadores'] ?? []),
            ]);

            return response()->json([
                "success" => true,
                "message" => "Evento creado correctamente",
                "evento"  => $evento
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // ======================================================
    //  MOSTRAR UN EVENTO
    // ======================================================
    public function show($id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "message" => "Evento no encontrado"
                ], 404);
            }

            $evento->patrocinadores = $this->safeArray($evento->patrocinadores);
            $evento->invitados = $this->safeArray($evento->invitados);
            $evento->imagenes = $this->safeArray($evento->imagenes);

            return response()->json([
                "success" => true,
                "evento" => $evento
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500);
        }
    }

    // ======================================================
    //  ACTUALIZAR EVENTO
    // ======================================================
    public function update(Request $request, $id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento)
                return response()->json(["success" => false, "message" => "Evento no encontrado"], 404);

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'tipo_evento' => 'sometimes|required|string|max:100',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'fecha_limite_inscripcion' => 'nullable|date|before:fecha_inicio',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'estado' => 'sometimes|required|in:borrador,publicado,cancelado',
                'ciudad' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'inscripcion_abierta' => 'nullable|boolean',
                'patrocinadores' => 'nullable|array',
                'invitados' => 'nullable|array',
                'imagenes' => 'nullable|array',
                'auspiciadores' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $evento->update([
                "titulo" => $request->titulo ?? $evento->titulo,
                "descripcion" => $request->descripcion ?? $evento->descripcion,
                "tipo_evento" => $request->tipo_evento ?? $evento->tipo_evento,
                "fecha_inicio" => $request->fecha_inicio ?? $evento->fecha_inicio,
                "fecha_fin" => $request->fecha_fin ?? $evento->fecha_fin,
                "fecha_limite_inscripcion" => $request->fecha_limite_inscripcion ?? $evento->fecha_limite_inscripcion,
                "capacidad_maxima" => $request->capacidad_maxima ?? $evento->capacidad_maxima,
                "estado" => $request->estado ?? $evento->estado,
                "ciudad" => $request->ciudad ?? $evento->ciudad,
                "direccion" => $request->direccion ?? $evento->direccion,
                "lat" => $request->lat ?? $evento->lat,
                "lng" => $request->lng ?? $evento->lng,
                "inscripcion_abierta" => $request->has('inscripcion_abierta') ? $request->inscripcion_abierta : $evento->inscripcion_abierta,
                "patrocinadores" => $request->has('patrocinadores') ? $this->safeArray($request->patrocinadores) : $evento->patrocinadores,
                "invitados" => $request->has('invitados') ? $this->safeArray($request->invitados) : $evento->invitados,
                "imagenes" => $request->has('imagenes') ? $this->safeArray($request->imagenes) : $evento->imagenes,
                "auspiciadores" => $request->has('auspiciadores') ? $this->safeArray($request->auspiciadores) : $evento->auspiciadores,
            ]);

            return response()->json([
                "success" => true,
                "message" => "Evento actualizado correctamente",
                "evento"  => $evento->fresh()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500);
        }
    }

    // ======================================================
    //  AGREGAR PATROCINADOR A EVENTO
    // ======================================================
    public function agregarPatrocinador(Request $request, $id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento)
                return response()->json(["success" => false, "message" => "Evento no encontrado"], 404);

            $validator = Validator::make($request->all(), [
                'empresa_id' => 'required|integer|exists:empresas,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $empresaId = $request->empresa_id;
            $patrocinadores = $this->safeArray($evento->patrocinadores ?? []);
            
            // Convertir todos a string para consistencia
            $patrocinadores = array_map(function($p) {
                return (string) $p;
            }, $patrocinadores);
            
            $empresaIdStr = (string) $empresaId;

            // Verificar si ya es patrocinador
            if (in_array($empresaIdStr, $patrocinadores)) {
                return response()->json([
                    "success" => false,
                    "message" => "La empresa ya es patrocinadora de este evento"
                ], 400);
            }

            // Agregar la empresa a los patrocinadores
            $patrocinadores[] = $empresaIdStr;
            
            $evento->update([
                "patrocinadores" => $patrocinadores
            ]);

            return response()->json([
                "success" => true,
                "message" => "Empresa agregada como patrocinadora correctamente",
                "evento" => $evento->fresh()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500);
        }
    }

    // ======================================================
    //  ELIMINAR EVENTO
    // ======================================================
    public function destroy($id)
    {
        $evento = Evento::find($id);

        if (!$evento)
            return response()->json(["success" => false, "message" => "No encontrado"], 404);

        $evento->delete();

        return response()->json([
            "success" => true,
            "message" => "Evento eliminado"
        ]);
    }

    // ======================================================
    //  EMPRESAS DISPONIBLES PARA PATROCINAR
    // ======================================================
    public function empresasDisponibles()
    {
        try {
            $empresas = Empresa::with('usuario')
                ->whereHas('usuario', function($query) {
                    $query->where('activo', true);
                })
                ->get()
                ->map(function($empresa) {
                    return [
                        'id' => $empresa->user_id,
                        'nombre' => $empresa->nombre_empresa,
                        'NIT' => $empresa->NIT,
                    ];
                });

            return response()->json([
                'success' => true,
                'empresas' => $empresas
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================================================
    //  INVITADOS DISPONIBLES
    // ======================================================
    public function invitadosDisponibles()
    {
        try {
            // Los invitados pueden ser integrantes externos activos
            $invitados = IntegranteExterno::with('usuario')
                ->whereHas('usuario', function($query) {
                    $query->where('activo', true);
                })
                ->get()
                ->map(function($externo) {
                    return [
                        'id' => $externo->user_id,
                        'nombre' => trim($externo->nombres . ' ' . ($externo->apellidos ?? '')),
                    ];
                });

            return response()->json([
                'success' => true,
                'invitados' => $invitados
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
