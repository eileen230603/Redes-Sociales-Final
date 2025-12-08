<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\EventoCompartido;
use App\Models\User;
use App\Models\Empresa;
use App\Models\IntegranteExterno;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Enriquecer patrocinadores con información completa (avatar y nombre)
     */
    private function enriquecerPatrocinadores($patrocinadores)
    {
        if (!is_array($patrocinadores) || empty($patrocinadores)) {
            return [];
        }

        $enriquecidos = [];
        foreach ($patrocinadores as $pat) {
            // Si es un ID numérico, buscar la empresa
            if (is_numeric($pat)) {
                $empresa = Empresa::where('user_id', $pat)->first();
                if ($empresa) {
                    $enriquecidos[] = [
                        'id' => $pat,
                        'nombre' => $empresa->nombre_empresa,
                        'avatar' => $empresa->foto_perfil_url ?? null,
                        'tipo' => 'empresa'
                    ];
                }
            } elseif (is_string($pat)) {
                // Si es un string, puede ser un nombre o un ID como string
                if (is_numeric($pat)) {
                    $empresa = Empresa::where('user_id', (int)$pat)->first();
                    if ($empresa) {
                        $enriquecidos[] = [
                            'id' => (int)$pat,
                            'nombre' => $empresa->nombre_empresa,
                            'avatar' => $empresa->foto_perfil_url ?? null,
                            'tipo' => 'empresa'
                        ];
                    }
                } else {
                    // Si es solo texto, mantenerlo pero sin avatar
                    $enriquecidos[] = [
                        'id' => null,
                        'nombre' => $pat,
                        'avatar' => null,
                        'tipo' => 'texto'
                    ];
                }
            }
        }
        return $enriquecidos;
    }

    /**
     * Enriquecer invitados con información completa (avatar y nombre)
     */
    private function enriquecerInvitados($invitados)
    {
        if (!is_array($invitados) || empty($invitados)) {
            return [];
        }

        $enriquecidos = [];
        foreach ($invitados as $inv) {
            // Si es un ID numérico, buscar el externo
            if (is_numeric($inv)) {
                $externo = IntegranteExterno::where('user_id', $inv)->with('usuario')->first();
                if ($externo) {
                    $nombre = trim($externo->nombres . ' ' . ($externo->apellidos ?? ''));
                    $enriquecidos[] = [
                        'id' => $inv,
                        'nombre' => $nombre ?: ($externo->usuario->nombre_usuario ?? 'N/A'),
                        'avatar' => $externo->foto_perfil_url ?? null,
                        'tipo' => 'externo'
                    ];
                }
            } elseif (is_string($inv)) {
                // Si es un string, puede ser un nombre o un ID como string
                if (is_numeric($inv)) {
                    $externo = IntegranteExterno::where('user_id', (int)$inv)->with('usuario')->first();
                    if ($externo) {
                        $nombre = trim($externo->nombres . ' ' . ($externo->apellidos ?? ''));
                        $enriquecidos[] = [
                            'id' => (int)$inv,
                            'nombre' => $nombre ?: ($externo->usuario->nombre_usuario ?? 'N/A'),
                            'avatar' => $externo->foto_perfil_url ?? null,
                            'tipo' => 'externo'
                        ];
                    }
                } else {
                    // Si es solo texto, mantenerlo pero sin avatar
                    $enriquecidos[] = [
                        'id' => null,
                        'nombre' => $inv,
                        'avatar' => null,
                        'tipo' => 'texto'
                    ];
                }
            }
        }
        return $enriquecidos;
    }

    /**
     * Procesar y guardar imágenes
     */
    private function processImages($request, $eventoId = null)
    {
        $imagenes = [];
        
        // Si hay archivos de imagen en el request
        if ($request->hasFile('imagenes')) {
            $files = $request->file('imagenes');
            
            // Asegurar que sea un array
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file->isValid()) {
                    // Validar tipo y tamaño
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        continue;
                    }
                    
                    if ($file->getSize() > 5120 * 1024) { // 5MB
                        continue;
                    }
                    
                    // Generar nombre único para la imagen
                    $filename = 'eventos/' . ($eventoId ?? 'temp') . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    
                    // Guardar en storage/public
                    $path = $file->storeAs('public', $filename);
                    
                    // Obtener la ruta relativa (ej: /storage/eventos/1/uuid.jpg)
                    $url = Storage::url($filename);
                    $imagenes[] = $url; // Guardar como /storage/... (ruta relativa)
                }
            }
        }
        
        // Si también vienen imágenes como JSON string o array (rutas relativas existentes)
        if ($request->has('imagenes_json')) {
            $imagenesJson = $this->safeArray($request->input('imagenes_json'));
            // Normalizar a rutas relativas
            $imagenesJson = array_map(function($img) {
                if (empty($img)) return null;
                // Si es URL completa, extraer la ruta relativa
                if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                    $parsed = parse_url($img);
                    return $parsed['path'] ?? null;
                }
                // Si ya es ruta relativa, retornarla
                return $img;
            }, $imagenesJson);
            $imagenes = array_merge($imagenes, array_filter($imagenesJson));
        }

        // Si vienen imágenes por URL (URLs completas de internet)
        if ($request->has('imagenes_urls')) {
            $urlsInput = $request->input('imagenes_urls');
            
            // Si es string JSON, decodificarlo
            if (is_string($urlsInput)) {
                $decoded = json_decode($urlsInput, true);
                $urlsImagenes = is_array($decoded) ? $decoded : [];
            } else {
                $urlsImagenes = $this->safeArray($urlsInput);
            }
            
            // Validar y agregar URLs completas (se guardan como están, el accessor las manejará)
            $urlsValidas = array_filter($urlsImagenes, function($url) {
                if (empty($url) || !is_string($url)) return false;
                // Validar que sea una URL válida
                return filter_var($url, FILTER_VALIDATE_URL) !== false;
            });
            $imagenes = array_merge($imagenes, $urlsValidas);
        }
        
        // Si vienen imágenes como array directo (desde JSON)
        if ($request->has('imagenes') && !$request->hasFile('imagenes')) {
            $imagenesArray = $this->safeArray($request->input('imagenes'));
            $imagenes = array_merge($imagenes, $imagenesArray);
        }
        
        return array_values(array_unique(array_filter($imagenes))); // Eliminar duplicados y valores nulos
    }

    // ======================================================
    //  LISTAR EVENTOS DE UNA ONG
    // ======================================================
    public function indexByOng($ongId, Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuarioAutenticado = $request->user();
            
            if (!$usuarioAutenticado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }
            
            // Validar que el ID de la ONG en la URL coincida con el usuario autenticado
            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            $ongIdParametro = (int) $ongId;
            
            if ($ongIdAutenticada !== $ongIdParametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para acceder a estos eventos'
                ], 403);
            }
            
            \Log::info("Buscando eventos para ONG ID: {$ongIdAutenticada}");
            
            // Obtener todos los eventos de la ONG autenticada
            $query = Evento::where('ong_id', $ongIdAutenticada);
            
            // Filtro por tipo de evento
            if ($request->has('tipo_evento') && $request->tipo_evento !== '' && $request->tipo_evento !== 'todos') {
                $query->where('tipo_evento', $request->tipo_evento);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }
            
            // Obtener todos los eventos primero para calcular estados dinámicos
            $todosEventos = $query->orderBy('id', 'desc')->get();
            
            // Filtrar por estado dinámico si se especifica
            $estadoFiltro = $request->get('estado', 'todos');
            if ($estadoFiltro !== 'todos' && $estadoFiltro !== '') {
                $todosEventos = $todosEventos->filter(function($e) use ($estadoFiltro) {
                    $estadoDinamico = $e->estado_dinamico;
                    
                    if ($estadoFiltro === 'finalizados') {
                        return $estadoDinamico === 'finalizado';
                    } elseif ($estadoFiltro === 'activos') {
                        return $estadoDinamico === 'activo';
                    } elseif ($estadoFiltro === 'proximos') {
                        return $estadoDinamico === 'proximo';
                    } elseif ($estadoFiltro === 'en_curso') {
                        return $estadoDinamico === 'activo';
                    } else {
                        // Para borrador, cancelado, etc., usar el estado guardado
                        return $e->estado === $estadoFiltro;
                    }
                })->values();
            }
            
            $eventos = $todosEventos;

            \Log::info("Eventos encontrados: " . $eventos->count());

            // Enriquecer patrocinadores e invitados con información completa
            // Y calcular estado dinámico basado en fechas
            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                // El accessor del modelo ya genera URLs completas
                $e->makeVisible(['imagenes', 'fecha_finalizacion']);
                // Agregar estado dinámico calculado
                $e->estado_dinamico = $e->estado_dinamico;
                return $e;
            });

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
    public function indexAll(Request $request)
    {
        try {
            \Log::info("Buscando eventos publicados para externos");
            
            $query = Evento::where('estado', 'publicado');
            
            // Filtro por tipo de evento
            if ($request->has('tipo_evento') && $request->tipo_evento !== '' && $request->tipo_evento !== 'todos') {
                $query->where('tipo_evento', $request->tipo_evento);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }
            
            $eventos = $query->orderBy('fecha_inicio', 'asc')->get();

            \Log::info("Eventos publicados encontrados: " . $eventos->count());

            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                // El accessor del modelo ya genera URLs completas
                $e->makeVisible(['imagenes', 'fecha_finalizacion']);
                // Agregar estado dinámico calculado
                $e->estado_dinamico = $e->estado_dinamico;
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
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
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

            $evento->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($evento->patrocinadores));
            $evento->invitados = $this->enriquecerInvitados($this->safeArray($evento->invitados));
            // El accessor del modelo ya genera URLs completas
            $evento->makeVisible(['imagenes', 'fecha_finalizacion']);
            // Agregar estado dinámico calculado
            $evento->estado_dinamico = $evento->estado_dinamico;

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
    //  CREAR EVENTO
    // ======================================================
    public function store(Request $request)
    {
        try {
            // Preparar datos para validación
            $requestData = $request->all();
            
            // Convertir strings JSON vacíos a arrays vacíos para patrocinadores, invitados y auspiciadores
            if (isset($requestData['patrocinadores']) && is_string($requestData['patrocinadores']) && $requestData['patrocinadores'] === '[]') {
                $requestData['patrocinadores'] = [];
            }
            if (isset($requestData['invitados']) && is_string($requestData['invitados']) && $requestData['invitados'] === '[]') {
                $requestData['invitados'] = [];
            }
            if (isset($requestData['auspiciadores']) && is_string($requestData['auspiciadores']) && $requestData['auspiciadores'] === '[]') {
                $requestData['auspiciadores'] = [];
            }
            
            $validator = Validator::make($requestData, [
                'ong_id' => 'required|integer|exists:ongs,user_id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'tipo_evento' => 'required|string|max:100',
                'fecha_inicio' => 'required|date|after:now',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'fecha_limite_inscripcion' => 'nullable|date|before:fecha_inicio',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'estado' => 'required|in:borrador,publicado,finalizado,cancelado',
                'ciudad' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'inscripcion_abierta' => 'nullable|boolean',
                'patrocinadores' => 'nullable|array',
                'patrocinadores.*' => 'integer',
                'invitados' => 'nullable|array',
                'invitados.*' => 'integer',
                'imagenes' => 'nullable|array',
                'auspiciadores' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "errors" => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Procesar patrocinadores e invitados correctamente
            // Ya están validados como arrays, solo asegurar que sean enteros
            $patrocinadores = [];
            if (isset($data['patrocinadores']) && is_array($data['patrocinadores'])) {
                $patrocinadores = array_map('intval', array_filter($data['patrocinadores'], 'is_numeric'));
            }

            $invitados = [];
            if (isset($data['invitados']) && is_array($data['invitados'])) {
                $invitados = array_map('intval', array_filter($data['invitados'], 'is_numeric'));
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
                "patrocinadores" => $patrocinadores,
                "invitados" => $invitados,
                "imagenes" => [],
                "auspiciadores" => $this->safeArray($data['auspiciadores'] ?? []),
            ]);

            // Procesar imágenes después de crear el evento
            $imagenes = $this->processImages($request, $evento->id);
            if (!empty($imagenes)) {
                $evento->update(['imagenes' => $imagenes]);
            }
            
            // Forzar refresh para obtener las imágenes procesadas
            $evento->refresh();
            $evento->makeVisible('imagenes');

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
                'estado' => 'sometimes|required|in:borrador,publicado,finalizado,cancelado',
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
                    "success" => false,
                    "errors" => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Procesar imágenes: obtener las existentes primero
            $imagenesActuales = $this->safeArray($evento->getRawOriginal('imagenes') ?? []);
            
            // Si vienen imágenes como JSON (para mantener las existentes o actualizar)
            if ($request->has('imagenes_json')) {
                $imagenesJson = $this->safeArray($request->input('imagenes_json'));
                // Normalizar a rutas relativas o URLs completas según corresponda
                $imagenesJson = array_map(function($img) {
                    if (empty($img)) return null;
                    // Si es URL completa de internet, mantenerla
                    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                        // Verificar si es URL externa (no del servidor)
                        $parsed = parse_url($img);
                        $host = $parsed['host'] ?? '';
                        $serverHost = parse_url(url('/'), PHP_URL_HOST);
                        if ($host !== $serverHost && !empty($host)) {
                            return $img; // URL externa, mantenerla completa
                        }
                        // Si es URL del servidor, extraer ruta relativa
                        return $parsed['path'] ?? null;
                    }
                    // Si ya es ruta relativa, retornarla
                    return $img;
                }, $imagenesJson);
                $imagenesActuales = array_values(array_filter($imagenesJson));
            }
            
            // Procesar nuevas imágenes (archivos y URLs de internet)
            // Esto incluye archivos subidos y URLs nuevas enviadas en imagenes_urls
            $nuevasImagenes = $this->processImages($request, $evento->id);
            
            // Combinar imágenes existentes con nuevas
            $imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
            
            // Eliminar duplicados y valores nulos, reindexar array
            $data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));

            // Convertir arrays a JSON si vienen
            if (isset($data['patrocinadores'])) {
                $data['patrocinadores'] = $this->safeArray($data['patrocinadores']);
            }
            if (isset($data['invitados'])) {
                $data['invitados'] = $this->safeArray($data['invitados']);
            }
            if (isset($data['auspiciadores'])) {
                $data['auspiciadores'] = $this->safeArray($data['auspiciadores']);
            }

            $evento->update($data);
            
            // Forzar refresh para obtener las imágenes procesadas
            $evento->refresh();
            $evento->makeVisible('imagenes');

            return response()->json([
                "success" => true,
                "message" => "Evento actualizado correctamente",
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
    //  DASHBOARD DE EVENTOS POR ESTADO (ONG)
    // ======================================================
    public function dashboardPorEstado($ongId, Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuarioAutenticado = $request->user();
            
            if (!$usuarioAutenticado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }
            
            // Validar que el ID de la ONG en la URL coincida con el usuario autenticado
            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            $ongIdParametro = (int) $ongId;
            
            if ($ongIdAutenticada !== $ongIdParametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para acceder a estos eventos'
                ], 403);
            }
            
            \Log::info("Dashboard: Buscando eventos para ONG ID autenticada: {$ongIdAutenticada}");
            
            // Obtener todos los eventos de la ONG autenticada
            $query = Evento::where('ong_id', $ongIdAutenticada);
            
            // Búsqueda
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }
            
            // Ordenar por fecha de inicio descendente
            $todosEventos = $query->orderBy('fecha_inicio', 'desc')->get();
            
            // Calcular estados dinámicos y filtrar
            $estadoFiltro = $request->get('estado', 'todos');
            $eventos = $todosEventos->filter(function($e) use ($estadoFiltro) {
                $estadoDinamico = $e->estado_dinamico;
                
                if ($estadoFiltro === 'todos') {
                    return true;
                } elseif ($estadoFiltro === 'finalizados') {
                    return $estadoDinamico === 'finalizado';
                } elseif ($estadoFiltro === 'activos') {
                    return $estadoDinamico === 'activo';
                } elseif ($estadoFiltro === 'proximos') {
                    return $estadoDinamico === 'proximo';
                } elseif ($estadoFiltro === 'en_curso') {
                    return $estadoDinamico === 'activo';
                } elseif ($estadoFiltro === 'cancelados') {
                    return $estadoDinamico === 'cancelado';
                } elseif ($estadoFiltro === 'borradores') {
                    return $estadoDinamico === 'borrador';
                }
                return true;
            })->values();
            
            // Enriquecer datos y agregar estado dinámico
            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                $e->imagenes = $this->safeArray($e->imagenes);
                $e->makeVisible(['fecha_finalizacion']);
                // Agregar estado dinámico calculado
                $e->estado_dinamico = $e->estado_dinamico;
                return $e;
            });
            
            // Calcular estadísticas basadas en estados dinámicos (solo eventos)
            $estadisticas = [
                'total' => $todosEventos->count(),
                'finalizados' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'finalizado')->count(),
                'activos' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'activo')->count(),
                'proximos' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'proximo')->count(),
                'en_curso' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'activo')->count(),
                'cancelados' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'cancelado')->count(),
                'borradores' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'borrador')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'eventos' => $eventos,
                'estadisticas' => $estadisticas,
                'filtro_estado' => $estadoFiltro,
                'count' => $eventos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
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
            $empresas = Empresa::select('user_id', 'nombre_empresa', 'descripcion')
                ->get()
                ->map(function ($empresa) {
                    return [
                        'id' => $empresa->user_id,
                        'nombre' => $empresa->nombre_empresa,
                        'descripcion' => $empresa->descripcion ?? ''
                    ];
                });

            return response()->json([
                "success" => true,
                "empresas" => $empresas
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error en empresasDisponibles: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // ======================================================
    //  INVITADOS DISPONIBLES
    // ======================================================
    public function invitadosDisponibles()
    {
        try {
            $invitados = IntegranteExterno::with('usuario:id_usuario,nombre_usuario,correo_electronico')
                ->select('user_id', 'nombres', 'apellidos', 'descripcion')
                ->get()
                ->map(function ($inv) {
                    $nombreCompleto = trim(($inv->nombres ?? '') . ' ' . ($inv->apellidos ?? ''));
                    if (empty($nombreCompleto)) {
                        $nombreCompleto = $inv->usuario->nombre_usuario ?? 'Sin nombre';
                    }
                    return [
                        'id' => $inv->user_id,
                        'nombre' => $nombreCompleto,
                        'email' => $inv->usuario->correo_electronico ?? $inv->email ?? null
                    ];
                });

            return response()->json([
                "success" => true,
                "invitados" => $invitados
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error en invitadosDisponibles: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
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

            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "message" => "Evento no encontrado"
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'empresa_id' => 'required|integer|exists:empresas,user_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "errors" => $validator->errors()
                ], 422);
            }

            $empresaId = $request->empresa_id;
            $empresaIdStr = (string) $empresaId;
            
            $patrocinadores = $this->safeArray($evento->patrocinadores);

            // Verificar si ya es patrocinador
            if (in_array($empresaIdStr, $patrocinadores) || in_array($empresaId, $patrocinadores)) {
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
    //  DASHBOARD DEL EVENTO
    // ======================================================
    public function dashboard($id)
    {
        try {
            // Validar que el usuario esté autenticado
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No autenticado'
                ], 401);
            }

            $evento = Evento::find($id);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario es el dueño del evento
            $ongId = auth()->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este dashboard'
                ], 403);
            }

            // Estadísticas básicas
            $totalReacciones = EventoReaccion::where('evento_id', $id)->count();
            $totalCompartidos = EventoCompartido::where('evento_id', $id)->count();
            
            // Contar participantes registrados
            $participantesRegistrados = EventoParticipacion::where('evento_id', $id)->count();
            
            // Contar participantes no registrados
            $participantesNoRegistradosCount = EventoParticipanteNoRegistrado::where('evento_id', $id)->count();
            
            // Total de participantes (registrados + no registrados)
            $totalParticipantes = $participantesRegistrados + $participantesNoRegistradosCount;
            
            // Contar voluntarios únicos (solo registrados con externo_id)
            $totalVoluntarios = EventoParticipacion::where('evento_id', $id)
                ->whereNotNull('externo_id')
                ->distinct()
                ->count('externo_id');
            
            // Participantes aprobados (registrados + no registrados)
            $participantesAprobadosRegistrados = EventoParticipacion::where('evento_id', $id)
                ->where('estado', 'aprobada')
                ->count();
            $participantesAprobadosNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $id)
                ->where('estado', 'aprobada')
                ->count();
            $participantesAprobados = $participantesAprobadosRegistrados + $participantesAprobadosNoRegistrados;
            
            // Participantes pendientes (registrados + no registrados)
            $participantesPendientesRegistrados = EventoParticipacion::where('evento_id', $id)
                ->where('estado', 'pendiente')
                ->count();
            $participantesPendientesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $id)
                ->where('estado', 'pendiente')
                ->count();
            $participantesPendientes = $participantesPendientesRegistrados + $participantesPendientesNoRegistrados;

            // Gráficas: Reacciones por día
            $reaccionesPorDia = [];
            try {
                $reaccionesData = EventoReaccion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                
                foreach ($reaccionesData as $item) {
                    $reaccionesPorDia[$item->fecha] = (int)$item->total;
                }
            } catch (\Exception $e) {
                \Log::error('Error en reacciones por día: ' . $e->getMessage());
            }

            // Gráficas: Compartidos por día
            $compartidosPorDia = [];
            try {
                $compartidosData = EventoCompartido::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                
                foreach ($compartidosData as $item) {
                    $compartidosPorDia[$item->fecha] = (int)$item->total;
                }
            } catch (\Exception $e) {
                \Log::error('Error en compartidos por día: ' . $e->getMessage());
            }

            // Gráficas: Participantes por estado
            $participantesPorEstado = [];
            try {
                // Participantes registrados por estado
                $participantesData = EventoParticipacion::where('evento_id', $id)
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get();
                
                foreach ($participantesData as $item) {
                    $estado = $item->estado ?? 'pendiente';
                    if (!isset($participantesPorEstado[$estado])) {
                        $participantesPorEstado[$estado] = 0;
                    }
                    $participantesPorEstado[$estado] += (int)$item->total;
                }
                
                // Participantes no registrados por estado
                $participantesNoRegistradosData = EventoParticipanteNoRegistrado::where('evento_id', $id)
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get();
                
                foreach ($participantesNoRegistradosData as $item) {
                    $estado = $item->estado ?? 'pendiente';
                    if (!isset($participantesPorEstado[$estado])) {
                        $participantesPorEstado[$estado] = 0;
                    }
                    $participantesPorEstado[$estado] += (int)$item->total;
                }
            } catch (\Exception $e) {
                \Log::error('Error en participantes por estado: ' . $e->getMessage());
            }

            // Gráficas: Inscripciones por día
            $inscripcionesPorDia = [];
            try {
                // Inscripciones de participantes registrados
                $inscripcionesData = EventoParticipacion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                
                foreach ($inscripcionesData as $item) {
                    $fecha = $item->fecha;
                    if (!isset($inscripcionesPorDia[$fecha])) {
                        $inscripcionesPorDia[$fecha] = 0;
                    }
                    $inscripcionesPorDia[$fecha] += (int)$item->total;
                }
                
                // Inscripciones de participantes no registrados
                $inscripcionesNoRegistradosData = EventoParticipanteNoRegistrado::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                
                foreach ($inscripcionesNoRegistradosData as $item) {
                    $fecha = $item->fecha;
                    if (!isset($inscripcionesPorDia[$fecha])) {
                        $inscripcionesPorDia[$fecha] = 0;
                    }
                    $inscripcionesPorDia[$fecha] += (int)$item->total;
                }
            } catch (\Exception $e) {
                \Log::error('Error en inscripciones por día: ' . $e->getMessage());
            }

            // Gráficas: Actividad por semana (combinando todas las actividades)
            $actividadSemanal = [];
            try {
                // Obtener todas las fechas de actividad
                $fechasReacciones = EventoReaccion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha')
                    ->get()
                    ->pluck('fecha')
                    ->toArray();
                
                $fechasCompartidos = EventoCompartido::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha')
                    ->get()
                    ->pluck('fecha')
                    ->toArray();
                
                $fechasParticipaciones = EventoParticipacion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha')
                    ->get()
                    ->pluck('fecha')
                    ->toArray();
                
                // Combinar todas las fechas
                $todasFechas = array_merge($fechasReacciones, $fechasCompartidos, $fechasParticipaciones);
                
                // Agrupar por semana
                $actividadPorSemana = [];
                foreach ($todasFechas as $fecha) {
                    $semana = date('Y-W', strtotime($fecha));
                    if (!isset($actividadPorSemana[$semana])) {
                        $actividadPorSemana[$semana] = 0;
                    }
                    $actividadPorSemana[$semana]++;
                }
                
                // Convertir a formato de fecha
                foreach ($actividadPorSemana as $semana => $total) {
                    $fechaSemana = date('Y-m-d', strtotime(substr($semana, 0, 4) . 'W' . substr($semana, 5, 2) . '1'));
                    $actividadSemanal[$fechaSemana] = $total;
                }
                
                ksort($actividadSemanal);
            } catch (\Exception $e) {
                \Log::error('Error en actividad semanal: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo
                ],
                'estadisticas' => [
                    'reacciones' => $totalReacciones,
                    'compartidos' => $totalCompartidos,
                    'voluntarios' => $totalVoluntarios,
                    'participantes' => $totalParticipantes,
                    'participantes_aprobados' => $participantesAprobados,
                    'participantes_pendientes' => $participantesPendientes
                ],
                'graficas' => [
                    'reacciones_por_dia' => $reaccionesPorDia,
                    'compartidos_por_dia' => $compartidosPorDia,
                    'participantes_por_estado' => $participantesPorEstado,
                    'inscripciones_por_dia' => $inscripcionesPorDia,
                    'actividad_semanal' => $actividadSemanal
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en dashboard del evento:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener dashboard: ' . $e->getMessage(),
                'details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    // ======================================================
    //  GENERAR PDF DEL DASHBOARD DEL EVENTO
    // ======================================================
    public function dashboardPdf($id)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No autenticado'
                ], 401);
            }

            $evento = Evento::find($id);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario es el dueño del evento
            $ongId = auth()->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este dashboard'
                ], 403);
            }

            // Obtener todas las estadísticas (reutilizar lógica del método dashboard)
            $totalReacciones = EventoReaccion::where('evento_id', $id)->count();
            $totalCompartidos = EventoCompartido::where('evento_id', $id)->count();
            
            // Contar participantes registrados
            $participantesRegistrados = EventoParticipacion::where('evento_id', $id)->count();
            
            // Contar participantes no registrados
            $participantesNoRegistradosCount = EventoParticipanteNoRegistrado::where('evento_id', $id)->count();
            
            // Total de participantes (registrados + no registrados)
            $totalParticipantes = $participantesRegistrados + $participantesNoRegistradosCount;
            
            // Contar voluntarios únicos (solo registrados con externo_id)
            $totalVoluntarios = EventoParticipacion::where('evento_id', $id)
                ->whereNotNull('externo_id')
                ->distinct()
                ->count('externo_id');
            
            // Participantes aprobados (registrados + no registrados)
            $participantesAprobadosRegistrados = EventoParticipacion::where('evento_id', $id)
                ->where('estado', 'aprobada')
                ->count();
            $participantesAprobadosNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $id)
                ->where('estado', 'aprobada')
                ->count();
            $participantesAprobados = $participantesAprobadosRegistrados + $participantesAprobadosNoRegistrados;
            
            // Participantes pendientes (registrados + no registrados)
            $participantesPendientesRegistrados = EventoParticipacion::where('evento_id', $id)
                ->where('estado', 'pendiente')
                ->count();
            $participantesPendientesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $id)
                ->where('estado', 'pendiente')
                ->count();
            $participantesPendientes = $participantesPendientesRegistrados + $participantesPendientesNoRegistrados;

            // Obtener datos para gráficas (simplificado para PDF)
            $reaccionesPorDia = [];
            $compartidosPorDia = [];
            $participantesPorEstado = [];
            $inscripcionesPorDia = [];

            try {
                $reaccionesData = EventoReaccion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                foreach ($reaccionesData as $item) {
                    $reaccionesPorDia[$item->fecha] = (int)$item->total;
                }
            } catch (\Exception $e) {}

            try {
                $compartidosData = EventoCompartido::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                foreach ($compartidosData as $item) {
                    $compartidosPorDia[$item->fecha] = (int)$item->total;
                }
            } catch (\Exception $e) {}

            try {
                // Participantes registrados por estado
                $participantesData = EventoParticipacion::where('evento_id', $id)
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get();
                foreach ($participantesData as $item) {
                    $estado = $item->estado ?? 'pendiente';
                    if (!isset($participantesPorEstado[$estado])) {
                        $participantesPorEstado[$estado] = 0;
                    }
                    $participantesPorEstado[$estado] += (int)$item->total;
                }
                
                // Participantes no registrados por estado
                $participantesNoRegistradosData = EventoParticipanteNoRegistrado::where('evento_id', $id)
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get();
                foreach ($participantesNoRegistradosData as $item) {
                    $estado = $item->estado ?? 'pendiente';
                    if (!isset($participantesPorEstado[$estado])) {
                        $participantesPorEstado[$estado] = 0;
                    }
                    $participantesPorEstado[$estado] += (int)$item->total;
                }
            } catch (\Exception $e) {}

            try {
                // Inscripciones de participantes registrados
                $inscripcionesData = EventoParticipacion::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                foreach ($inscripcionesData as $item) {
                    $fecha = $item->fecha;
                    if (!isset($inscripcionesPorDia[$fecha])) {
                        $inscripcionesPorDia[$fecha] = 0;
                    }
                    $inscripcionesPorDia[$fecha] += (int)$item->total;
                }
                
                // Inscripciones de participantes no registrados
                $inscripcionesNoRegistradosData = EventoParticipanteNoRegistrado::where('evento_id', $id)
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                foreach ($inscripcionesNoRegistradosData as $item) {
                    $fecha = $item->fecha;
                    if (!isset($inscripcionesPorDia[$fecha])) {
                        $inscripcionesPorDia[$fecha] = 0;
                    }
                    $inscripcionesPorDia[$fecha] += (int)$item->total;
                }
            } catch (\Exception $e) {}

            // Para mobile, retornamos los datos en JSON para que Flutter genere el PDF
            // En lugar de generar el PDF en el servidor
            return response()->json([
                'success' => true,
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'fecha_inicio' => $evento->fecha_inicio,
                    'fecha_fin' => $evento->fecha_fin,
                    'ciudad' => $evento->ciudad,
                ],
                'estadisticas' => [
                    'reacciones' => $totalReacciones,
                    'compartidos' => $totalCompartidos,
                    'voluntarios' => $totalVoluntarios,
                    'participantes' => $totalParticipantes,
                    'participantes_aprobados' => $participantesAprobados,
                    'participantes_pendientes' => $participantesPendientes
                ],
                'graficas' => [
                    'reacciones_por_dia' => $reaccionesPorDia,
                    'compartidos_por_dia' => $compartidosPorDia,
                    'participantes_por_estado' => $participantesPorEstado,
                    'inscripciones_por_dia' => $inscripcionesPorDia
                ],
                'message' => 'Datos del dashboard listos para generar PDF en la aplicación móvil'
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en dashboard PDF del evento:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener datos para PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
