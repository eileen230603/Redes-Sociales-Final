<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MegaEvento;
use App\Models\Notificacion;
use Illuminate\Support\Str;

class MegaEventoController extends Controller
{
    /**
     * Helper para procesar arrays de forma segura
     */
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
     * Procesar y guardar imágenes
     * Retorna rutas relativas (ej: /storage/mega_eventos/1/uuid.jpg) para guardar en BD
     * El accessor del modelo se encargará de convertirlas a URLs completas
     */
    private function processImages($request, $megaEventoId = null)
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
                try {
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
                        $filename = 'mega_eventos/' . ($megaEventoId ?? 'temp') . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                        
                        // Guardar usando el disco 'public' explícitamente
                        $path = Storage::disk('public')->putFileAs(
                            dirname($filename),
                            $file,
                            basename($filename)
                        );
                        
                        // Verificar que el archivo se guardó
                        $fullPath = storage_path('app/public/' . $path);
                        if (!file_exists($fullPath)) {
                            \Log::error("No se pudo guardar la imagen: $fullPath");
                            continue;
                        }
                        
                        // Copiar también a public/storage/ para que el servidor de PHP pueda servirlo directamente
                        $publicPath = public_path('storage/' . $path);
                        $publicDir = dirname($publicPath);
                        if (!file_exists($publicDir)) {
                            mkdir($publicDir, 0755, true);
                        }
                        if (file_exists($fullPath)) {
                            copy($fullPath, $publicPath);
                        }
                        
                        // Obtener la URL pública (ruta relativa)
                        $url = Storage::disk('public')->url($path);
                        $imagenes[] = $url;
                        
                        \Log::info("Imagen guardada: $url -> $fullPath (también copiada a $publicPath)");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error al guardar imagen: " . $e->getMessage());
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

    /**
     * Listar todos los mega eventos
     */
    public function index(Request $request)
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

            // Filtrar solo los mega eventos de la ONG autenticada
            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            
            $query = MegaEvento::with('ongPrincipal')
                ->where('ong_organizadora_principal', $ongIdAutenticada);
            
            // Filtro por categoría
            if ($request->has('categoria') && $request->categoria !== '' && $request->categoria !== 'todos') {
                $query->where('categoria', $request->categoria);
            }
            
            // Filtro por estado
            if ($request->has('estado') && $request->estado !== '' && $request->estado !== 'todos') {
                $query->where('estado', $request->estado);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }
            
            $megaEventos = $query->orderByDesc('mega_evento_id')->get();
            
            // Procesar imágenes para cada mega evento (el accessor del modelo ya genera URLs completas)
            foreach ($megaEventos as $mega) {
                // El accessor del modelo ya convierte las imágenes a URLs completas
                $mega->makeVisible('imagenes');
            }
            
            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventos,
                'count' => $megaEventos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega eventos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo mega evento
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
            'ubicacion' => 'nullable|string|max:500',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
            'categoria' => 'nullable|string|max:50',
                'estado' => 'nullable|string|max:20|in:planificacion,activo,en_curso,finalizado,cancelado',
            'ong_organizadora_principal' => 'required|integer|exists:ongs,user_id',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'es_publico' => 'nullable|boolean',
                'activo' => 'nullable|boolean',
                'imagenes' => 'nullable|array',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB por imagen
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            
            // Procesar fechas
            $data['fecha_creacion'] = now();
            $data['fecha_actualizacion'] = now();
            
            // Crear el mega evento primero (sin imágenes)
            $megaEvento = MegaEvento::create([
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'ubicacion' => $data['ubicacion'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
                'categoria' => $data['categoria'] ?? 'social',
                'estado' => $data['estado'] ?? 'planificacion',
                'ong_organizadora_principal' => $data['ong_organizadora_principal'],
                'capacidad_maxima' => $data['capacidad_maxima'] ?? null,
                'es_publico' => $data['es_publico'] ?? false,
                'activo' => $data['activo'] ?? true,
                'imagenes' => [], // Inicializar vacío
            ]);

            // Procesar y guardar imágenes (archivos y URLs)
            $imagenes = $this->processImages($request, $megaEvento->mega_evento_id);
            
            // Actualizar con las imágenes
            if (!empty($imagenes)) {
                $megaEvento->imagenes = $imagenes;
                $megaEvento->fecha_actualizacion = now();
                $megaEvento->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Mega evento creado correctamente',
                'mega_evento' => $megaEvento->fresh()
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear mega evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un mega evento específico
     */
    public function show($id)
    {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de mega evento inválido'
                ], 400);
            }

            $megaEvento = MegaEvento::with('ongPrincipal')->findOrFail($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // El accessor del modelo ya genera URLs completas
            $megaEvento->makeVisible('imagenes');

            // Asegurar que la relación ongPrincipal incluya el accessor foto_perfil_url
            if ($megaEvento->ongPrincipal) {
                // Forzar que se incluya el accessor en la serialización
                $megaEvento->ongPrincipal->makeVisible(['foto_perfil_url']);
                
                // Obtener el foto_perfil_url del accessor (ya normalizado)
                $fotoPerfilUrl = $megaEvento->ongPrincipal->foto_perfil_url;
                
                // También agregar manualmente para asegurar que esté disponible
                $megaEvento->ong_principal = [
                    'user_id' => $megaEvento->ongPrincipal->user_id,
                    'nombre_ong' => $megaEvento->ongPrincipal->nombre_ong,
                    'foto_perfil_url' => $fotoPerfilUrl,
                    'foto_perfil' => $megaEvento->ongPrincipal->foto_perfil,
                ];
                
                // También asegurar que la relación ongPrincipal tenga el accessor en su serialización
                $megaEvento->ongPrincipal->setAttribute('foto_perfil_url', $fotoPerfilUrl);
            }

            // Agregar información adicional útil
            $megaEvento->total_participantes = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->count();

            $megaEvento->participantes_aprobados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('estado_participacion', 'aprobada')
                ->where('activo', true)
                ->count();

            return response()->json([
                'success' => true,
                'mega_evento' => $megaEvento
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Mega evento no encontrado'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un mega evento
     */
    public function update(Request $request, $id)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:200',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after:fecha_inicio',
                'ubicacion' => 'nullable|string|max:500',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'categoria' => 'nullable|string|max:50',
                'estado' => 'nullable|string|max:20|in:planificacion,activo,en_curso,finalizado,cancelado',
                'ong_organizadora_principal' => 'sometimes|required|integer|exists:ongs,user_id',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'es_publico' => 'nullable|boolean',
                'activo' => 'nullable|boolean',
                'imagenes' => 'nullable|array',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['fecha_actualizacion'] = now();

            // Detectar si se está finalizando el mega evento (para notificación)
            $estadoAnterior = $megaEvento->estado;
            $seEstaFinalizando = isset($data['estado'])
                && $data['estado'] === 'finalizado'
                && $estadoAnterior !== 'finalizado';

            // Procesar imágenes: obtener las existentes primero
            $imagenesActuales = $this->safeArray($megaEvento->getRawOriginal('imagenes') ?? []);
            
            // Si vienen imágenes como JSON (para mantener las existentes o actualizar)
            if ($request->has('imagenes_json')) {
                $imagenesJsonInput = $request->input('imagenes_json');
                
                // Si es string JSON, decodificarlo
                if (is_string($imagenesJsonInput)) {
                    $decoded = json_decode($imagenesJsonInput, true);
                    $imagenesJson = is_array($decoded) ? $decoded : [];
                } else {
                    $imagenesJson = $this->safeArray($imagenesJsonInput);
                }
                
                // Normalizar a rutas relativas (para imágenes locales) o mantener URLs de internet
                $imagenesJson = array_map(function($img) {
                    if (empty($img)) return null;
                    // Si es URL completa de internet, mantenerla
                    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                        // Verificar si es URL de internet o URL local
                        $parsed = parse_url($img);
                        $host = $parsed['host'] ?? '';
                        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                        
                        // Si el host no es localhost ni el dominio actual, es URL de internet
                        if (!empty($host) && 
                            !in_array($host, ['localhost', '127.0.0.1', '192.168.0.6']) && 
                            $host !== $appHost &&
                            strpos($host, $appHost) === false &&
                            strpos($appHost, $host) === false) {
                            return $img; // Mantener URL de internet completa
                        }
                        // Si es URL local, extraer la ruta
                        return $parsed['path'] ?? null;
                    }
                    // Si ya es ruta relativa, retornarla
                    return $img;
                }, $imagenesJson);
                $imagenesActuales = array_values(array_filter($imagenesJson));
            }
            
            // Procesar nuevas imágenes (archivos y URLs de internet)
            // Esto incluye archivos subidos y URLs nuevas enviadas en imagenes_urls
            $nuevasImagenes = $this->processImages($request, $megaEvento->mega_evento_id);
            
            // Log para depuración (remover en producción)
            \Log::info('Mega Evento Update - Imágenes', [
                'existentes' => $imagenesActuales,
                'nuevas' => $nuevasImagenes,
                'imagenes_urls_input' => $request->input('imagenes_urls'),
                'imagenes_json_input' => $request->input('imagenes_json')
            ]);
            
            // TRANSACCIÓN: Procesar imágenes + actualizar mega evento + notificación si se finaliza
            DB::transaction(function () use ($megaEvento, $data, $imagenesActuales, $nuevasImagenes, $seEstaFinalizando) {
                // 1. Combinar imágenes existentes con nuevas
            $imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
            
                // 2. Eliminar duplicados y valores nulos, reindexar array
            $data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));
            
                // 3. Asegurar que lat y lng se guarden correctamente
            if (isset($data['lat']) && $data['lat'] === '') {
                $data['lat'] = null;
            }
            if (isset($data['lng']) && $data['lng'] === '') {
                $data['lng'] = null;
            }

                // 4. Actualizar mega evento
            $megaEvento->update($data);

                // 5. Si se está finalizando el mega evento, crear notificación para la ONG
            if ($seEstaFinalizando && $megaEvento->ong_organizadora_principal) {
                try {
                    Notificacion::create([
                        'ong_id' => $megaEvento->ong_organizadora_principal,
                        'evento_id' => null, // Para mega eventos no usamos evento_id
                        'externo_id' => null,
                        'tipo' => 'mega_evento_finalizado',
                        'titulo' => 'Mega evento finalizado',
                        'mensaje' => "Tu mega evento '{$megaEvento->titulo}' ha sido marcado como finalizado. Ya no está disponible para nuevas participaciones o interacciones.",
                        'leida' => false,
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Error al crear notificación de mega evento finalizado: ' . $e->getMessage());
                }
            }
            });
            
            // Forzar refresh para obtener las imágenes procesadas
            $megaEvento->refresh();
            $megaEvento->makeVisible('imagenes');

            return response()->json([
                'success' => true,
                'message' => 'Mega evento actualizado correctamente',
                'mega_evento' => $megaEvento
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar mega evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un mega evento
     */
    public function destroy($id)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Eliminar imágenes asociadas
            $imagenes = $this->safeArray($megaEvento->imagenes);
            foreach ($imagenes as $imagen) {
                // Extraer la ruta del storage
                $path = str_replace('/storage/', '', parse_url($imagen, PHP_URL_PATH));
                if ($path && Storage::exists('public/' . $path)) {
                    Storage::delete('public/' . $path);
                }
            }

            $megaEvento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mega evento eliminado correctamente'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar mega evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una imagen específica de un mega evento
     */
    public function deleteImage(Request $request, $id)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'imagen_url' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'URL de imagen requerida'
                ], 422);
            }

            $imagenUrl = $request->input('imagen_url');
            $imagenes = $this->safeArray($megaEvento->imagenes);

            // Remover la imagen del array
            $imagenes = array_filter($imagenes, function($img) use ($imagenUrl) {
                return $img !== $imagenUrl;
            });

            // Eliminar el archivo físico
            $path = str_replace('/storage/', '', parse_url($imagenUrl, PHP_URL_PATH));
            if ($path && Storage::exists('public/' . $path)) {
                Storage::delete('public/' . $path);
            }

            // Actualizar el mega evento
            $megaEvento->imagenes = array_values($imagenes); // Reindexar array
            $megaEvento->fecha_actualizacion = now();
            $megaEvento->save();

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente',
                'mega_evento' => $megaEvento->fresh()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Participar en un mega evento (para usuarios externos/voluntarios)
     */
    public function participar(Request $request, $megaEventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;
            
            // Verificar que el usuario es externo o voluntario
            $user = \App\Models\User::find($externoId);
            if (!$user || ($user->tipo_usuario !== 'Integrante externo' && $user->tipo_usuario !== 'Voluntario')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios externos y voluntarios pueden participar en mega eventos'
                ], 403);
            }

            $megaEvento = MegaEvento::find($megaEventoId);
            
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            if (!$megaEvento->es_publico) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este mega evento no es público'
                ], 403);
            }

            if (!$megaEvento->activo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este mega evento no está activo'
                ], 400);
            }

            // Verificar capacidad
            $participantes = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('activo', true)
                ->count();
            
            if ($megaEvento->capacidad_maxima && $participantes >= $megaEvento->capacidad_maxima) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupo agotado'
                ], 400);
            }

            // Obtener integrante externo
            $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario externo no encontrado'
                ], 404);
            }

            // Verificar si ya está participando
            $yaParticipa = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('integrante_externo_id', $integranteExterno->user_id)
                ->exists();

            if ($yaParticipa) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ya estás participando en este mega evento'
                ], 400);
            }

            // TRANSACCIÓN: Insertar participación + crear notificación
            DB::transaction(function () use ($megaEventoId, $integranteExterno, $megaEvento, $externoId) {
                // 1. Crear participación
            DB::table('mega_evento_participantes_externos')->insert([
                'mega_evento_id' => $megaEventoId,
                'integrante_externo_id' => $integranteExterno->user_id,
                'estado_participacion' => 'aprobada', // Aprobación automática
                'fecha_registro' => now(),
                'activo' => true
            ]);

                // 2. Crear notificación para la ONG
            $this->crearNotificacionMegaEvento($megaEvento, $externoId);
            });

            return response()->json([
                'success' => true,
                'message' => 'Participación registrada y aprobada automáticamente'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al participar en mega evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si el usuario está participando en un mega evento
     */
    public function verificarParticipacion(Request $request, $megaEventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;
            $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => true,
                    'participando' => false
                ]);
            }

            $participando = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('integrante_externo_id', $integranteExterno->user_id)
                ->where('activo', true)
                ->exists();

            return response()->json([
                'success' => true,
                'participando' => $participando
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Participación pública (usuarios no registrados mediante QR)
     */
    public function participarPublico(Request $request, $megaEventoId)
    {
        try {
            // Validar datos
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

            // Verificar que el mega evento existe
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            if (!$megaEvento->es_publico) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este mega evento no es público'
                ], 403);
            }

            if (!$megaEvento->activo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este mega evento no está activo'
                ], 400);
            }

            // Verificar capacidad
            $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('activo', true)
                ->count();
            $participantesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $megaEventoId)
                ->where('estado', '!=', 'rechazada')
                ->count();
            $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;

            if ($megaEvento->capacidad_maxima && $totalParticipantes >= $megaEvento->capacidad_maxima) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupo agotado para este mega evento'
                ], 400);
            }

            // Verificar si ya está inscrito (por nombre y apellido)
            $yaInscrito = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $megaEventoId)
                ->where('nombres', $request->nombres)
                ->where('apellidos', $request->apellidos)
                ->where('estado', '!=', 'rechazada')
                ->exists();

            if ($yaInscrito) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ya estás inscrito en este mega evento'
                ], 400);
            }

            // Crear participación (APROBADA automáticamente para usuarios no registrados)
            $participacion = \App\Models\MegaEventoParticipanteNoRegistrado::create([
                'mega_evento_id' => $megaEventoId,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'estado' => 'aprobada', // Aprobado automáticamente
                'asistio' => false,
            ]);

            // Crear notificación para la ONG
            try {
                $this->crearNotificacionMegaEventoPublica($megaEvento, $participacion);
            } catch (\Throwable $e) {
                \Log::error('Error creando notificación de participación pública de mega evento: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => '¡Tu participación ha sido registrada y aprobada!',
                'data' => $participacion
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en participarPublico mega evento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar tu participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un usuario no registrado ya participó en el mega evento
     */
    public function verificarParticipacionPublica(Request $request, $megaEventoId)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $yaInscrito = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $megaEventoId)
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
            \Log::error('Error en verificarParticipacionPublica mega evento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar participación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la ONG sobre participación pública
     */
    private function crearNotificacionMegaEventoPublica(MegaEvento $megaEvento, \App\Models\MegaEventoParticipanteNoRegistrado $participacion)
    {
        try {
            $ongId = $megaEvento->ong_organizadora_principal ?? null;
            if (!$ongId) return;

            \App\Models\Notificacion::create([
                'usuario_id' => $ongId,
                'tipo' => 'participacion_publica',
                'titulo' => 'Nueva participación (Usuario no registrado)',
                'mensaje' => "{$participacion->nombres} {$participacion->apellidos} quiere participar en el mega evento: {$megaEvento->titulo}",
                'leida' => false,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de participación pública de mega evento: ' . $e->getMessage());
        }
    }

    /**
     * Obtener mega eventos en los que el usuario está participando
     */
    public function misParticipaciones(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;
            $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
            
            if (!$integranteExterno) {
                return response()->json([
                    'success' => true,
                    'mega_eventos' => []
                ]);
            }

            $participaciones = DB::table('mega_evento_participantes_externos as mep')
                ->join('mega_eventos as me', 'mep.mega_evento_id', '=', 'me.mega_evento_id')
                ->where('mep.integrante_externo_id', $integranteExterno->user_id)
                ->where('mep.activo', true)
                ->select('me.*', 'mep.estado_participacion', 'mep.fecha_registro', 'mep.tipo_participacion')
                ->orderByDesc('mep.fecha_registro')
                ->get();

            $megaEventos = [];
            foreach ($participaciones as $participacion) {
                $mega = MegaEvento::find($participacion->mega_evento_id);
                if ($mega) {
                    $mega->makeVisible('imagenes');
                    $megaEventos[] = [
                        'mega_evento_id' => $mega->mega_evento_id,
                        'titulo' => $mega->titulo,
                        'descripcion' => $mega->descripcion,
                        'fecha_inicio' => $mega->fecha_inicio,
                        'fecha_fin' => $mega->fecha_fin,
                        'ubicacion' => $mega->ubicacion,
                        'categoria' => $mega->categoria,
                        'imagenes' => $mega->imagenes,
                        'estado_participacion' => $participacion->estado_participacion,
                        'fecha_registro' => $participacion->fecha_registro,
                        'tipo_participacion' => $participacion->tipo_participacion,
                        'ong' => $mega->ongPrincipal ? [
                            'nombre' => $mega->ongPrincipal->nombre_ong,
                            'foto_perfil' => $mega->ongPrincipal->foto_perfil_url ?? null
                        ] : null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener participaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener mega eventos públicos (para usuarios externos/voluntarios)
     */
    public function publicos(Request $request)
    {
        try {
            $query = MegaEvento::with('ongPrincipal')
                ->where('es_publico', true)
                ->where('activo', true);
            
            // Filtro por categoría
            if ($request->has('categoria') && $request->categoria !== '' && $request->categoria !== 'todos') {
                $query->where('categoria', $request->categoria);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }
            
            $megaEventos = $query->orderByDesc('fecha_inicio')->get();
            
            foreach ($megaEventos as $mega) {
                $mega->makeVisible('imagenes');
            }
            
            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventos,
                'count' => $megaEventos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega eventos públicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la ONG cuando alguien participa en un mega evento
     */
    private function crearNotificacionMegaEvento(MegaEvento $megaEvento, $externoId)
    {
        try {
            $externo = \App\Models\User::find($externoId);
            if (!$externo) return;

            $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
            $nombreUsuario = $integranteExterno 
                ? trim($integranteExterno->nombres . ' ' . ($integranteExterno->apellidos ?? ''))
                : $externo->nombre_usuario;

            \App\Models\Notificacion::create([
                'ong_id' => $megaEvento->ong_organizadora_principal,
                'evento_id' => null, // Los mega eventos no tienen evento_id
                'externo_id' => $externoId,
                'tipo' => 'participacion',
                'titulo' => 'Nueva participación en tu mega evento',
                'mensaje' => "{$nombreUsuario} se inscribió al mega evento \"{$megaEvento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de mega evento: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de seguimiento de un mega evento
     * Incluye métricas completas, seguimiento por tipo de actor, alertas y estadísticas de interacción
     */
    public function seguimiento($id)
    {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de mega evento inválido'
                ], 400);
            }

            $megaEvento = MegaEvento::with('ongPrincipal')->findOrFail($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG organizadora
            $user = auth()->user();
            if (!$user || $user->id_usuario != $megaEvento->ong_organizadora_principal) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos para ver el seguimiento de este mega evento'
                ], 403);
            }

            // ========== ESTADÍSTICAS DE PARTICIPANTES ==========
            $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->count();

            $participantesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
                ->where('estado', '!=', 'rechazada')
                ->count();

            $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;

            $participantesAprobados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('estado_participacion', 'aprobada')
                ->where('activo', true)
                ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
                ->where('estado', 'aprobada')
                ->count();

            $participantesPendientes = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('estado_participacion', '!=', 'aprobada')
                ->where('activo', true)
                ->count();

            $participantesCancelados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('estado_participacion', 'cancelada')
                ->where('activo', true)
                ->count();

            // ========== ESTADÍSTICAS DE INSCRIPCIONES POR DÍA ==========
            $inscripcionesRegistrados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->where('fecha_registro', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(fecha_registro) as fecha'), DB::raw('COUNT(*) as cantidad'))
                ->groupBy('fecha')
                ->get();

            $inscripcionesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as cantidad'))
                ->groupBy('fecha')
                ->get();

            // Combinar y sumar por fecha
            $inscripcionesPorDia = collect();
            $todasFechas = $inscripcionesRegistrados->pluck('fecha')->merge($inscripcionesNoRegistrados->pluck('fecha'))->unique();
            
            foreach ($todasFechas as $fecha) {
                $cantidadRegistrados = $inscripcionesRegistrados->where('fecha', $fecha)->sum('cantidad');
                $cantidadNoRegistrados = $inscripcionesNoRegistrados->where('fecha', $fecha)->sum('cantidad');
                $inscripcionesPorDia->push([
                    'fecha' => $fecha,
                    'cantidad' => $cantidadRegistrados + $cantidadNoRegistrados
                ]);
            }
            
            $inscripcionesPorDia = $inscripcionesPorDia->sortBy('fecha')->values();

            // ========== ESTADÍSTICAS DE REACCIONES POR DÍA ==========
            $reaccionesPorDia = collect();
            try {
                $reaccionesData = \App\Models\MegaEventoReaccion::where('mega_evento_id', $id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('fecha')
                    ->get();
                
                // Crear array con todos los días de los últimos 30 días
                $todasFechas = [];
                for ($i = 29; $i >= 0; $i--) {
                    $fecha = now()->subDays($i)->format('Y-m-d');
                    $todasFechas[] = $fecha;
                }
                
                // Mapear reacciones por fecha
                $reaccionesMap = $reaccionesData->pluck('cantidad', 'fecha')->toArray();
                
                // Crear array completo con todos los días
                foreach ($todasFechas as $fecha) {
                    $reaccionesPorDia->push([
                        'fecha' => $fecha,
                        'cantidad' => isset($reaccionesMap[$fecha]) ? (int)$reaccionesMap[$fecha] : 0
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error en reacciones por día: ' . $e->getMessage());
                // Si hay error, crear array vacío con los últimos 30 días
                for ($i = 29; $i >= 0; $i--) {
                    $fecha = now()->subDays($i)->format('Y-m-d');
                    $reaccionesPorDia->push([
                        'fecha' => $fecha,
                        'cantidad' => 0
                    ]);
                }
            }

            // ========== ESTADÍSTICAS DE CAPACIDAD ==========
            $porcentajeCapacidad = $megaEvento->capacidad_maxima 
                ? round(($totalParticipantes / $megaEvento->capacidad_maxima) * 100, 2)
                : null;

            // ========== ESTADÍSTICAS DE EMPRESAS PATROCINADORAS ==========
            $totalEmpresasPatrocinadoras = DB::table('mega_evento_patrocinadores')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->count();

            $totalContribucionEconomica = DB::table('mega_evento_patrocinadores')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->where('tipo_contribucion', 'economica')
                ->sum('monto_contribucion');

            $empresasConfirmadas = DB::table('mega_evento_patrocinadores')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->where('estado_compromiso', 'confirmado')
                ->count();

            // ========== ESTADÍSTICAS DE NOTIFICACIONES ==========
            $tituloMegaEvento = $megaEvento->titulo;
            $totalNotificaciones = DB::table('notificaciones')
                ->where('ong_id', $megaEvento->ong_organizadora_principal)
                ->where(function($q) use ($id, $tituloMegaEvento) {
                    $q->where('tipo', 'participacion')
                      ->whereRaw("mensaje LIKE ?", ["%mega evento%{$tituloMegaEvento}%"]);
                })
                ->count();

            $notificacionesNoLeidas = DB::table('notificaciones')
                ->where('ong_id', $megaEvento->ong_organizadora_principal)
                ->where('leida', false)
                ->where(function($q) use ($id, $tituloMegaEvento) {
                    $q->where('tipo', 'participacion')
                      ->whereRaw("mensaje LIKE ?", ["%mega evento%{$tituloMegaEvento}%"]);
                })
                ->count();

            // ========== TASA DE CONFIRMACIÓN Y CANCELACIÓN ==========
            $tasaConfirmacion = $totalParticipantes > 0 
                ? round(($participantesAprobados / $totalParticipantes) * 100, 2)
                : 0;

            $tasaCancelacion = $totalParticipantes > 0 
                ? round(($participantesCancelados / $totalParticipantes) * 100, 2)
                : 0;

            // ========== SEGUIMIENTO POR TIPO DE ACTOR ==========
            // ONG Organizadora
            $tareasCumplidasOng = [
                'evento_publicado' => $megaEvento->es_publico ? 1 : 0,
                'imagenes_cargadas' => !empty($megaEvento->imagenes) && count($megaEvento->imagenes) > 0 ? 1 : 0,
                'fechas_definidas' => !empty($megaEvento->fecha_inicio) && !empty($megaEvento->fecha_fin) ? 1 : 0,
                'ubicacion_definida' => !empty($megaEvento->ubicacion) ? 1 : 0,
            ];
            $totalTareasOng = count($tareasCumplidasOng);
            $tareasCompletadasOng = array_sum($tareasCumplidasOng);
            $porcentajeCumplimientoOng = $totalTareasOng > 0 
                ? round(($tareasCompletadasOng / $totalTareasOng) * 100, 2)
                : 0;

            // Empresas Patrocinadoras
            $empresasConAportes = DB::table('mega_evento_patrocinadores')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->whereNotNull('monto_contribucion')
                ->where('monto_contribucion', '>', 0)
                ->count();

            // Voluntarios (Participantes Externos)
            $voluntariosActivos = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->where('activo', true)
                ->where('estado_participacion', 'aprobada')
                ->count();

            // Usuarios Externos - Estadísticas de participación
            $participantesPorTipo = DB::table('mega_evento_participantes_externos as mep')
                ->join('integrantes_externos as ie', 'mep.integrante_externo_id', '=', 'ie.user_id')
                ->where('mep.mega_evento_id', $id)
                ->where('mep.activo', true)
                ->select(
                    DB::raw('COUNT(DISTINCT mep.integrante_externo_id) as total_unicos'),
                    DB::raw('COUNT(CASE WHEN mep.estado_participacion = \'aprobada\' THEN 1 END) as aprobados'),
                    DB::raw('COUNT(CASE WHEN mep.estado_participacion = \'pendiente\' THEN 1 END) as pendientes')
                )
                ->first();

            // ========== ALERTAS AUTOMÁTICAS ==========
            $alertas = [];

            // Alerta: 80% de cupo alcanzado
            if ($porcentajeCapacidad !== null && $porcentajeCapacidad >= 80) {
                $alertas[] = [
                    'tipo' => 'capacidad',
                    'nivel' => $porcentajeCapacidad >= 95 ? 'critica' : 'advertencia',
                    'mensaje' => $porcentajeCapacidad >= 95 
                        ? "¡Capacidad casi completa! ({$porcentajeCapacidad}% ocupado)"
                        : "Capacidad al {$porcentajeCapacidad}%",
                    'fecha' => now()->toDateTimeString()
                ];
            }

            // Alerta: Alta tasa de cancelación (>20%)
            if ($tasaCancelacion > 20) {
                $alertas[] = [
                    'tipo' => 'cancelacion',
                    'nivel' => 'advertencia',
                    'mensaje' => "Tasa de cancelación alta: {$tasaCancelacion}%",
                    'fecha' => now()->toDateTimeString()
                ];
            }

            // Alerta: Baja interacción (pocos participantes después de X días)
            $diasDesdeCreacion = now()->diffInDays($megaEvento->fecha_creacion);
            if ($diasDesdeCreacion >= 7 && $totalParticipantes < 10) {
                $alertas[] = [
                    'tipo' => 'interaccion',
                    'nivel' => 'info',
                    'mensaje' => "Baja participación después de {$diasDesdeCreacion} días ({$totalParticipantes} participantes)",
                    'fecha' => now()->toDateTimeString()
                ];
            }

            // Alerta: Tareas pendientes de ONG
            if ($porcentajeCumplimientoOng < 100) {
                $alertas[] = [
                    'tipo' => 'tareas',
                    'nivel' => 'info',
                    'mensaje' => "Cumplimiento de tareas: {$porcentajeCumplimientoOng}%",
                    'fecha' => now()->toDateTimeString()
                ];
            }

            // ========== ESTADÍSTICAS DE INTERACCIÓN SOCIAL ==========
            // Nota: Por ahora estas métricas están preparadas para futuras implementaciones
            // cuando se agreguen tablas específicas para mega eventos (mega_evento_reacciones, etc.)
            // ========== ESTADÍSTICAS DE REACCIONES ==========
            $totalReacciones = \App\Models\MegaEventoReaccion::where('mega_evento_id', $id)->count();

            // ========== ESTADÍSTICAS DE COMPARTIDOS ==========
            $totalCompartidos = \App\Models\MegaEventoCompartido::where('mega_evento_id', $id)->count();

            $estadisticasInteraccion = [
                'total_reacciones' => $totalReacciones,
                'total_compartidos' => $totalCompartidos,
                'total_comentarios' => 0, // Futuro: mega_evento_comentarios
                'tasa_conversion_visualizacion_inscripcion' => 0, // Cálculo futuro
            ];

            // ========== RESUMEN GENERAL ==========
            $resumen = [
                'nombre' => $megaEvento->titulo,
                'estado' => $megaEvento->estado,
                'fecha_creacion' => $megaEvento->fecha_creacion,
                'fecha_inicio' => $megaEvento->fecha_inicio,
                'fecha_fin' => $megaEvento->fecha_fin,
                'categoria' => $megaEvento->categoria,
                'es_publico' => $megaEvento->es_publico,
            ];

            return response()->json([
                'success' => true,
                'mega_evento' => $megaEvento,
                'resumen' => $resumen,
                'estadisticas' => [
                    // Participantes
                    'total_participantes' => $totalParticipantes,
                    'participantes_registrados' => $participantesRegistrados,
                    'participantes_no_registrados' => $participantesNoRegistrados,
                    'participantes_aprobados' => $participantesAprobados,
                    'participantes_pendientes' => $participantesPendientes,
                    'participantes_cancelados' => $participantesCancelados,
                    'voluntarios_activos' => $voluntariosActivos,
                    
                    // Capacidad
                    'capacidad_maxima' => $megaEvento->capacidad_maxima,
                    'porcentaje_capacidad' => $porcentajeCapacidad,
                    
                    // Tasas
                    'tasa_confirmacion' => $tasaConfirmacion,
                    'tasa_cancelacion' => $tasaCancelacion,
                    
                    // Empresas
                    'total_empresas_patrocinadoras' => $totalEmpresasPatrocinadoras,
                    'empresas_confirmadas' => $empresasConfirmadas,
                    'total_contribucion_economica' => $totalContribucionEconomica ?? 0,
                    'empresas_con_aportes' => $empresasConAportes,
                    
                    // Notificaciones
                    'total_notificaciones' => $totalNotificaciones,
                    'notificaciones_no_leidas' => $notificacionesNoLeidas,
                    
                    // ONG Organizadora
                    'tareas_cumplidas_ong' => $tareasCumplidasOng,
                    'porcentaje_cumplimiento_ong' => $porcentajeCumplimientoOng,
                    
                    // Usuarios Externos
                    'participantes_unicos' => $participantesPorTipo->total_unicos ?? 0,
                    
                    // Interacción Social
                    'interaccion_social' => $estadisticasInteraccion,
                ],
                'inscripciones_por_dia' => $inscripcionesPorDia,
                'reacciones_por_dia' => $reaccionesPorDia,
                'alertas' => $alertas,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener seguimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas agregadas de todos los mega eventos de la ONG
     */
    public function seguimientoGeneral()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $ongId = $user->id_usuario;

            // Obtener todos los mega eventos de la ONG
            $megaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)->get();

            if ($megaEventos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'estadisticas_agregadas' => [
                        'total_mega_eventos' => 0,
                        'total_participantes' => 0,
                        'total_reacciones' => 0,
                        'total_compartidos' => 0,
                        'total_participaciones' => 0,
                        'mega_eventos_activos' => 0,
                        'mega_eventos_finalizados' => 0,
                        'promedio_participantes_por_evento' => 0,
                        'promedio_reacciones_por_evento' => 0,
                        'promedio_compartidos_por_evento' => 0
                    ],
                    'mega_eventos_detalle' => []
                ]);
            }

            $totalParticipantes = 0;
            $totalReacciones = 0;
            $totalCompartidos = 0;
            $totalParticipaciones = 0;
            $megaEventosActivos = 0;
            $megaEventosFinalizados = 0;
            $megaEventosDetalle = [];

            foreach ($megaEventos as $megaEvento) {
                // Participantes
                $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('activo', true)
                    ->count();
                
                $participantesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('estado', '!=', 'rechazada')
                    ->count();
                
                $participantes = $participantesRegistrados + $participantesNoRegistrados;
                $totalParticipantes += $participantes;

                // Reacciones
                $reacciones = \App\Models\MegaEventoReaccion::where('mega_evento_id', $megaEvento->mega_evento_id)->count();
                $totalReacciones += $reacciones;

                // Compartidos
                $compartidos = \App\Models\MegaEventoCompartido::where('mega_evento_id', $megaEvento->mega_evento_id)->count();
                $totalCompartidos += $compartidos;

                // Participaciones (igual a participantes)
                $totalParticipaciones += $participantes;

                // Estados
                if ($megaEvento->estado === 'activo' || $megaEvento->estado === 'en_curso') {
                    $megaEventosActivos++;
                } elseif ($megaEvento->estado === 'finalizado') {
                    $megaEventosFinalizados++;
                }

                // Detalle por mega evento
                $megaEventosDetalle[] = [
                    'id' => $megaEvento->mega_evento_id,
                    'titulo' => $megaEvento->titulo,
                    'estado' => $megaEvento->estado,
                    'fecha_inicio' => $megaEvento->fecha_inicio,
                    'fecha_fin' => $megaEvento->fecha_fin,
                    'total_participantes' => $participantes,
                    'total_reacciones' => $reacciones,
                    'total_compartidos' => $compartidos,
                    'total_participaciones' => $participantes
                ];
            }

            $totalMegaEventos = $megaEventos->count();

            return response()->json([
                'success' => true,
                'estadisticas_agregadas' => [
                    'total_mega_eventos' => $totalMegaEventos,
                    'total_participantes' => $totalParticipantes,
                    'total_reacciones' => $totalReacciones,
                    'total_compartidos' => $totalCompartidos,
                    'total_participaciones' => $totalParticipaciones,
                    'mega_eventos_activos' => $megaEventosActivos,
                    'mega_eventos_finalizados' => $megaEventosFinalizados,
                    'promedio_participantes_por_evento' => $totalMegaEventos > 0 ? round($totalParticipantes / $totalMegaEventos, 2) : 0,
                    'promedio_reacciones_por_evento' => $totalMegaEventos > 0 ? round($totalReacciones / $totalMegaEventos, 2) : 0,
                    'promedio_compartidos_por_evento' => $totalMegaEventos > 0 ? round($totalCompartidos / $totalMegaEventos, 2) : 0
                ],
                'mega_eventos_detalle' => $megaEventosDetalle
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener seguimiento general: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de participantes con detalles
     */
    public function participantes($id, Request $request)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Verificar permisos
            $user = auth()->user();
            if (!$user || $user->id_usuario != $megaEvento->ong_organizadora_principal) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos'
                ], 403);
            }

            $participantes = collect();

            // Obtener participantes registrados
            $queryRegistrados = DB::table('mega_evento_participantes_externos as mep')
                ->join('integrantes_externos as ie', 'mep.integrante_externo_id', '=', 'ie.user_id')
                ->join('usuarios as u', 'ie.user_id', '=', 'u.id_usuario')
                ->where('mep.mega_evento_id', $id)
                ->where('mep.activo', true)
                ->select(
                    DB::raw("(mep.mega_evento_id::text || '-' || mep.integrante_externo_id::text) as id"),
                    'mep.integrante_externo_id',
                    'mep.fecha_registro',
                    'mep.estado_participacion as estado',
                    'ie.nombres',
                    'ie.apellidos',
                    'ie.email',
                    'ie.phone_number as telefono',
                    'u.nombre_usuario',
                    'u.foto_perfil',
                    DB::raw("'registrado' as tipo")
                );

            // Filtros para registrados
            if ($request->has('estado') && $request->estado !== '' && $request->estado !== 'todos') {
                $queryRegistrados->where('mep.estado_participacion', $request->estado);
            }

            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $queryRegistrados->where(function($q) use ($buscar) {
                    $q->where('ie.nombres', 'ilike', "%{$buscar}%")
                      ->orWhere('ie.apellidos', 'ilike', "%{$buscar}%")
                      ->orWhere('ie.email', 'ilike', "%{$buscar}%")
                      ->orWhere('u.nombre_usuario', 'ilike', "%{$buscar}%");
                });
            }

            $participantesRegistrados = $queryRegistrados->get();

            // Obtener participantes no registrados
            $queryNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
                ->select(
                    'id',
                    DB::raw('NULL as integrante_externo_id'),
                    'created_at as fecha_registro',
                    'estado',
                    'nombres',
                    'apellidos',
                    'email',
                    'telefono',
                    DB::raw('NULL as nombre_usuario'),
                    DB::raw('NULL as foto_perfil'),
                    DB::raw("'no_registrado' as tipo")
                );

            // Filtros para no registrados
            if ($request->has('estado') && $request->estado !== '' && $request->estado !== 'todos') {
                $queryNoRegistrados->where('estado', $request->estado);
            }

            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $queryNoRegistrados->where(function($q) use ($buscar) {
                    $q->where('nombres', 'ilike', "%{$buscar}%")
                      ->orWhere('apellidos', 'ilike', "%{$buscar}%")
                      ->orWhere('email', 'ilike', "%{$buscar}%");
                });
            }

            $participantesNoRegistrados = $queryNoRegistrados->get();

            // Combinar ambos tipos de participantes
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados)
                ->sortByDesc('fecha_registro')
                ->values();

            return response()->json([
                'success' => true,
                'participantes' => $participantes,
                'count' => $participantes->count()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener participantes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de cambios del mega evento
     * Incluye cronología completa de modificaciones, publicaciones y actividades
     */
    public function historial($id)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Verificar permisos
            $user = auth()->user();
            if (!$user || $user->id_usuario != $megaEvento->ong_organizadora_principal) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos'
                ], 403);
            }

            $historial = [];

            // Creación del mega evento
            $historial[] = [
                'fecha' => $megaEvento->fecha_creacion,
                'accion' => 'Creación del mega evento',
                'detalle' => "Mega evento '{$megaEvento->titulo}' creado",
                'usuario' => $user->nombre_usuario ?? 'Sistema',
                'tipo' => 'creacion',
                'icono' => 'fa-check'
            ];

            // Cambio de estado
            if ($megaEvento->estado) {
                $estadoTexto = match($megaEvento->estado) {
                    'planificacion' => 'En planificación',
                    'publicado' => 'Publicado',
                    'en_curso' => 'En curso',
                    'finalizado' => 'Finalizado',
                    'cancelado' => 'Cancelado',
                    default => $megaEvento->estado
                };
                $historial[] = [
                    'fecha' => $megaEvento->fecha_actualizacion,
                    'accion' => 'Estado actualizado',
                    'detalle' => "Estado: {$estadoTexto}",
                    'usuario' => $user->nombre_usuario ?? 'Sistema',
                    'tipo' => 'estado',
                    'icono' => 'fa-clock'
                ];
            }

            // Publicación del evento
            if ($megaEvento->es_publico) {
                $historial[] = [
                    'fecha' => $megaEvento->fecha_actualizacion,
                    'accion' => 'Evento publicado',
                    'detalle' => 'El evento fue marcado como público',
                    'usuario' => $user->nombre_usuario ?? 'Sistema',
                    'tipo' => 'publicacion',
                    'icono' => 'fa-calendar'
                ];
            }

            // Carga de imágenes
            if (!empty($megaEvento->imagenes) && is_array($megaEvento->imagenes) && count($megaEvento->imagenes) > 0) {
                $historial[] = [
                    'fecha' => $megaEvento->fecha_actualizacion,
                    'accion' => 'Imágenes cargadas',
                    'detalle' => count($megaEvento->imagenes) . ' imagen(es) agregada(s)',
                    'usuario' => $user->nombre_usuario ?? 'Sistema',
                    'tipo' => 'imagenes',
                    'icono' => 'fa-image'
                ];
            }

            // Última actualización
            if ($megaEvento->fecha_actualizacion != $megaEvento->fecha_creacion) {
                $historial[] = [
                    'fecha' => $megaEvento->fecha_actualizacion,
                    'accion' => 'Última actualización',
                    'detalle' => 'Información del evento actualizada',
                    'usuario' => $user->nombre_usuario ?? 'Sistema',
                    'tipo' => 'actualizacion',
                    'icono' => 'fa-edit'
                ];
            }

            // Actividades de participantes (últimas 10)
            $actividadesParticipantes = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $id)
                ->orderBy('fecha_registro', 'desc')
                ->limit(10)
                ->get()
                ->map(function($participante) {
                    return [
                        'fecha' => $participante->fecha_registro,
                        'accion' => 'Nueva inscripción',
                        'detalle' => "Participante inscrito (Estado: {$participante->estado_participacion})",
                        'usuario' => 'Participante',
                        'tipo' => 'participacion',
                        'icono' => 'fa-user-plus'
                    ];
                });

            $historial = array_merge($historial, $actividadesParticipantes->toArray());

            // Ordenar por fecha descendente
            usort($historial, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });

            return response()->json([
                'success' => true,
                'historial' => $historial,
                'total_registros' => count($historial)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar seguimiento de mega evento a Excel
     */
    public function exportarExcel($id)
    {
        try {
            $megaEvento = MegaEvento::with('ongPrincipal')->findOrFail($id);

            // Verificar permisos
            $user = auth()->user();
            if (!$user || $user->id_usuario != $megaEvento->ong_organizadora_principal) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos para exportar este reporte'
                ], 403);
            }

            // Obtener todos los datos del seguimiento
            $seguimientoData = $this->obtenerDatosParaExcel($id, $megaEvento);

            // Generar Excel con formato HTML (Excel puede abrir HTML con formato)
            $filename = 'seguimiento-mega-evento-' . $megaEvento->mega_evento_id . '-' . now()->format('Y-m-d') . '.xls';
            
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            // Generar HTML con formato Excel
            $html = $this->generarExcelHTML($seguimientoData);
            
            // Agregar BOM UTF-8 para que Excel interprete correctamente el HTML
            $html = "\xEF\xBB\xBF" . $html;

            return response($html, 200, $headers);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al exportar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los datos necesarios para el Excel
     */
    private function obtenerDatosParaExcel($id, $megaEvento)
    {
        // Obtener estadísticas (igual que en seguimiento)
        $participantesRegistrados = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('activo', true)
            ->count();

        $participantesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', '!=', 'rechazada')
            ->count();

        $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;

        $participantesAprobados = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('estado_participacion', 'aprobada')
            ->where('activo', true)
            ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', 'aprobada')
            ->count();

        $participantesPendientes = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('estado_participacion', 'pendiente')
            ->where('activo', true)
            ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', 'pendiente')
            ->count();

        $participantesCancelados = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('estado_participacion', 'cancelada')
            ->where('activo', true)
            ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', 'cancelada')
            ->count();

        $tasaConfirmacion = $totalParticipantes > 0 ? round(($participantesAprobados / $totalParticipantes) * 100, 1) : 0;
        $porcentajeCapacidad = $megaEvento->capacidad_maxima 
            ? round(($totalParticipantes / $megaEvento->capacidad_maxima) * 100, 2)
            : null;

        $totalReacciones = \App\Models\MegaEventoReaccion::where('mega_evento_id', $id)->count();
        $totalCompartidos = \App\Models\MegaEventoCompartido::where('mega_evento_id', $id)->count();

        // Obtener reacciones por día (últimos 30 días)
        $reaccionesPorDia = collect();
        try {
            $reaccionesData = \App\Models\MegaEventoReaccion::where('mega_evento_id', $id)
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('fecha')
                ->get();
            
            $todasFechas = [];
            for ($i = 29; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $todasFechas[] = $fecha;
            }
            
            $reaccionesMap = $reaccionesData->pluck('cantidad', 'fecha')->toArray();
            
            foreach ($todasFechas as $fecha) {
                $reaccionesPorDia->push([
                    'fecha' => $fecha,
                    'cantidad' => isset($reaccionesMap[$fecha]) ? (int)$reaccionesMap[$fecha] : 0
                ]);
            }
        } catch (\Exception $e) {
            for ($i = 29; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $reaccionesPorDia->push([
                    'fecha' => $fecha,
                    'cantidad' => 0
                ]);
            }
        }

        // Obtener participantes detallados
        $participantes = DB::table('mega_evento_participantes_externos as mep')
            ->join('integrantes_externos as ie', 'mep.integrante_externo_id', '=', 'ie.user_id')
            ->join('usuarios as u', 'ie.user_id', '=', 'u.id_usuario')
            ->where('mep.mega_evento_id', $id)
            ->where('mep.activo', true)
            ->select(
                'ie.nombres',
                'ie.apellidos',
                'ie.email',
                'ie.phone_number as telefono',
                'mep.estado_participacion as estado',
                'mep.fecha_registro',
                DB::raw("'Registrado' as tipo")
            )
            ->get();

        $participantesNoReg = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->select(
                'nombres',
                'apellidos',
                'email',
                'telefono',
                'estado',
                'created_at as fecha_registro',
                DB::raw("'No registrado' as tipo")
            )
            ->get();

        $todosParticipantes = $participantes->merge($participantesNoReg);

        // Obtener reacciones
        $reacciones = \App\Models\MegaEventoReaccion::where('mega_evento_id', $id)
            ->leftJoin('integrantes_externos as ie', function($join) {
                $join->on('mega_evento_reacciones.externo_id', '=', 'ie.user_id');
            })
            ->select(
                DB::raw("CASE 
                    WHEN ie.nombres IS NOT NULL THEN (ie.nombres || ' ' || COALESCE(ie.apellidos, ''))
                    WHEN mega_evento_reacciones.nombres IS NOT NULL THEN (mega_evento_reacciones.nombres || ' ' || COALESCE(mega_evento_reacciones.apellidos, ''))
                    ELSE 'Usuario anónimo'
                END as nombre"),
                DB::raw("COALESCE(ie.email, mega_evento_reacciones.email, 'N/A') as email"),
                'mega_evento_reacciones.created_at as fecha_reaccion'
            )
            ->get();

        // Obtener historial (similar a historial())
        $historial = [];
        $user = auth()->user();
        
        // Creación del mega evento
        $historial[] = [
            'fecha' => $megaEvento->fecha_creacion,
            'accion' => 'Creación del mega evento',
            'detalle' => 'El mega evento fue creado',
            'usuario' => $user->nombre_usuario ?? 'Sistema',
            'tipo' => 'creacion',
            'icono' => 'fa-check'
        ];

        // Estado actualizado
        if ($megaEvento->estado) {
            $historial[] = [
                'fecha' => $megaEvento->fecha_actualizacion,
                'accion' => 'Estado actualizado',
                'detalle' => 'Estado: ' . ucfirst(str_replace('_', ' ', $megaEvento->estado)),
                'usuario' => $user->nombre_usuario ?? 'Sistema',
                'tipo' => 'estado',
                'icono' => 'fa-clock'
            ];
        }

        // Publicación del evento
        if ($megaEvento->es_publico) {
            $historial[] = [
                'fecha' => $megaEvento->fecha_actualizacion,
                'accion' => 'Evento publicado',
                'detalle' => 'El evento fue marcado como público',
                'usuario' => $user->nombre_usuario ?? 'Sistema',
                'tipo' => 'publicacion',
                'icono' => 'fa-calendar'
            ];
        }

        // Ordenar por fecha descendente
        usort($historial, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        // Calcular estadísticas adicionales
        $participantesAprobados = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('estado_participacion', 'aprobada')
            ->where('activo', true)
            ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', 'aprobada')
            ->count();

        $participantesPendientes = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $id)
            ->where('estado_participacion', 'pendiente')
            ->where('activo', true)
            ->count() + \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
            ->where('estado', 'pendiente')
            ->count();

        $tasaConfirmacion = $totalParticipantes > 0 ? round(($participantesAprobados / $totalParticipantes) * 100, 1) : 0;
        $porcentajeCapacidad = $megaEvento->capacidad_maxima 
            ? round(($totalParticipantes / $megaEvento->capacidad_maxima) * 100, 2)
            : null;

        return [
            'mega_evento' => $megaEvento,
            'estadisticas' => [
                'total_participantes' => $totalParticipantes,
                'participantes_registrados' => $participantesRegistrados,
                'participantes_no_registrados' => $participantesNoRegistrados,
                'participantes_aprobados' => $participantesAprobados,
                'participantes_pendientes' => $participantesPendientes,
                'tasa_confirmacion' => $tasaConfirmacion,
                'porcentaje_capacidad' => $porcentajeCapacidad,
                'total_reacciones' => $totalReacciones,
                'total_compartidos' => $totalCompartidos,
            ],
            'participantes' => $todosParticipantes,
            'reacciones' => $reacciones,
            'reacciones_por_dia' => $reaccionesPorDia,
            'historial' => $historial
        ];
    }

    /**
     * Generar Excel con formato HTML profesional
     */
    private function generarExcelHTML($data)
    {
        $megaEvento = $data['mega_evento'];
        $estadisticas = $data['estadisticas'];
        $participantes = $data['participantes'];
        $reacciones = $data['reacciones'];
        $reaccionesPorDia = $data['reacciones_por_dia'] ?? collect();
        $historial = $data['historial'] ?? [];
        
        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
<x:ExcelWorkbook>
<x:ExcelWorksheets>
<x:ExcelWorksheet>
<x:Name>Dashboard Seguimiento</x:Name>
<x:WorksheetOptions>
<x:DefaultRowHeight>300</x:DefaultRowHeight>
<x:PrintGridlines/>
<x:GridlineColor>#D0D0D0</x:GridlineColor>
</x:WorksheetOptions>
</x:ExcelWorksheet>
</x:ExcelWorksheets>
</x:ExcelWorkbook>
</xml>
<![endif]-->
<style type="text/css">
body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; padding: 10px; }
.header-main { background-color: #00A36C; color: white; font-weight: bold; padding: 15px; text-align: center; font-size: 20px; }
.section-title { background-color: #0C2B44; color: white; font-weight: bold; padding: 12px; font-size: 14px; text-align: left; }
.info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
.info-table td { padding: 8px; border: 1px solid #E0E0E0; background-color: #FAFAFA; }
.info-label { font-weight: bold; width: 180px; background-color: #F5F5F5 !important; }
.metric-card { background-color: #00A36C; color: white; padding: 20px; text-align: center; border: 2px solid #008A5C; }
.metric-value { font-size: 36px; font-weight: bold; margin: 10px 0; }
.metric-label { font-size: 12px; opacity: 0.95; text-transform: uppercase; }
.data-table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11px; }
.data-table th { background-color: #00A36C; color: white; padding: 10px 8px; text-align: left; font-weight: bold; border: 1px solid #008A5C; }
.data-table td { padding: 8px; border: 1px solid #E0E0E0; background-color: white; }
.data-table tr:nth-child(even) td { background-color: #F9F9F9; }
.data-table tr:hover td { background-color: #F0F8F5; }
.number-cell { text-align: right; font-weight: bold; color: #00A36C; }
.status-aprobada { background-color: #E8F5E9; color: #2E7D32; font-weight: bold; padding: 4px 8px; border-radius: 3px; }
.status-pendiente { background-color: #FFF3E0; color: #F57C00; font-weight: bold; padding: 4px 8px; border-radius: 3px; }
.status-rechazada { background-color: #FFEBEE; color: #C62828; font-weight: bold; padding: 4px 8px; border-radius: 3px; }
.footer { background-color: #F5F5F5; padding: 10px; text-align: center; color: #666; font-size: 10px; border-top: 2px solid #00A36C; margin-top: 30px; }
</style>
</head>
<body>';

        // Encabezado principal
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
<tr>
<td class="header-main" colspan="4">DASHBOARD DE SEGUIMIENTO - MEGA EVENTO</td>
</tr>
</table>';

        // Información del Mega Evento
        $html .= '<table class="info-table">
<tr>
<td class="section-title" colspan="4">INFORMACIÓN DEL MEGA EVENTO</td>
</tr>
<tr>
<td class="info-label">Título:</td>
<td>' . htmlspecialchars($megaEvento->titulo) . '</td>
<td class="info-label">Estado:</td>
<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $megaEvento->estado))) . '</td>
</tr>
<tr>
<td class="info-label">Fecha de Creación:</td>
<td>' . date('d/m/Y H:i:s', strtotime($megaEvento->fecha_creacion)) . '</td>
<td class="info-label">Categoría:</td>
<td>' . htmlspecialchars(ucfirst($megaEvento->categoria)) . '</td>
</tr>
<tr>
<td class="info-label">Fecha de Inicio:</td>
<td>' . ($megaEvento->fecha_inicio ? date('d/m/Y H:i', strtotime($megaEvento->fecha_inicio)) : 'N/A') . '</td>
<td class="info-label">Fecha de Fin:</td>
<td>' . ($megaEvento->fecha_fin ? date('d/m/Y H:i', strtotime($megaEvento->fecha_fin)) : 'N/A') . '</td>
</tr>
</table>';

        // Métricas principales (igual que en la página de seguimiento)
        $html .= '<table class="data-table" style="margin: 20px 0;">
<tr>
<td class="section-title" colspan="4">MÉTRICAS PRINCIPALES</td>
</tr>
<tr>
<td style="background-color: #00A36C; color: white; text-align: center; padding: 20px; width: 25%;">
<div class="metric-value" style="font-size: 36px;">' . $estadisticas['total_participantes'] . '</div>
<div class="metric-label">TOTAL PARTICIPANTES</div>
</td>
<td style="background-color: #28a745; color: white; text-align: center; padding: 20px; width: 25%;">
<div class="metric-value" style="font-size: 36px;">' . ($estadisticas['participantes_aprobados'] ?? 0) . '</div>
<div class="metric-label">APROBADOS</div>
</td>
<td style="background-color: #17a2b8; color: white; text-align: center; padding: 20px; width: 25%;">
<div class="metric-value" style="font-size: 36px;">' . ($estadisticas['tasa_confirmacion'] ?? 0) . '%</div>
<div class="metric-label">TASA CONFIRMACIÓN</div>
</td>
<td style="background-color: #ffc107; color: white; text-align: center; padding: 20px; width: 25%;">
<div class="metric-value" style="font-size: 36px;">' . ($estadisticas['porcentaje_capacidad'] !== null ? $estadisticas['porcentaje_capacidad'] . '%' : 'Sin límite') . '</div>
<div class="metric-label">CAPACIDAD</div>
</td>
</tr>
</table>';

        // Métricas de Interacción
        $html .= '<table class="data-table" style="margin: 20px 0;">
<tr>
<td class="section-title" colspan="3">MÉTRICAS DE INTERACCIÓN</td>
</tr>
<tr>
<td style="background-color: #e91e63; color: white; text-align: center; padding: 20px; width: 33.33%; border-left: 4px solid #e91e63;">
<div class="metric-value" style="font-size: 32px;">' . $estadisticas['total_reacciones'] . '</div>
<div class="metric-label">TOTAL REACCIONES</div>
<small style="opacity: 0.9;">Me gusta recibidos</small>
</td>
<td style="background-color: #ff9800; color: white; text-align: center; padding: 20px; width: 33.33%; border-left: 4px solid #ff9800;">
<div class="metric-value" style="font-size: 32px;">' . $estadisticas['total_compartidos'] . '</div>
<div class="metric-label">TOTAL COMPARTIDOS</div>
<small style="opacity: 0.9;">Veces compartido</small>
</td>
<td style="background-color: #4caf50; color: white; text-align: center; padding: 20px; width: 33.33%; border-left: 4px solid #4caf50;">
<div class="metric-value" style="font-size: 32px;">' . $estadisticas['total_participantes'] . '</div>
<div class="metric-label">TOTAL PARTICIPACIONES</div>
<small style="opacity: 0.9;">' . $estadisticas['participantes_registrados'] . ' registrados, ' . $estadisticas['participantes_no_registrados'] . ' no registrados</small>
</td>
</tr>
</table>';

        // Tabla de estadísticas detalladas
        $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="4">ESTADÍSTICAS DETALLADAS</td>
</tr>
<tr>
<th style="width: 40%;">Métrica</th>
<th style="width: 20%;">Valor</th>
<th style="width: 20%;">Porcentaje</th>
<th style="width: 20%;">Promedio</th>
</tr>
<tr>
<td>Participantes Registrados</td>
<td class="number-cell">' . $estadisticas['participantes_registrados'] . '</td>
<td class="number-cell">' . ($estadisticas['total_participantes'] > 0 ? round(($estadisticas['participantes_registrados'] / $estadisticas['total_participantes']) * 100, 1) : 0) . '%</td>
<td>-</td>
</tr>
<tr>
<td>Participantes No Registrados</td>
<td class="number-cell">' . $estadisticas['participantes_no_registrados'] . '</td>
<td class="number-cell">' . ($estadisticas['total_participantes'] > 0 ? round(($estadisticas['participantes_no_registrados'] / $estadisticas['total_participantes']) * 100, 1) : 0) . '%</td>
<td>-</td>
</tr>
<tr>
<td>Reacciones (Me gusta)</td>
<td class="number-cell">' . $estadisticas['total_reacciones'] . '</td>
<td>-</td>
<td class="number-cell">' . ($estadisticas['total_participantes'] > 0 ? round($estadisticas['total_reacciones'] / $estadisticas['total_participantes'], 2) : 0) . '</td>
</tr>
<tr>
<td>Compartidos en Redes</td>
<td class="number-cell">' . $estadisticas['total_compartidos'] . '</td>
<td>-</td>
<td class="number-cell">' . ($estadisticas['total_participantes'] > 0 ? round($estadisticas['total_compartidos'] / $estadisticas['total_participantes'], 2) : 0) . '</td>
</tr>
</table>';

        // Tabla de Reacciones por Día
        if ($reaccionesPorDia->count() > 0) {
            $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="3">REACCIONES POR DÍA (ÚLTIMOS 30 DÍAS)</td>
</tr>
<tr>
<th style="width: 40%;">Fecha</th>
<th style="width: 30%;">Cantidad</th>
<th style="width: 30%;">Visualización</th>
</tr>';
            
            foreach ($reaccionesPorDia as $rpd) {
                $fechaFormateada = date('d/m/Y', strtotime($rpd['fecha']));
                $cantidad = $rpd['cantidad'];
                $maxReacciones = $reaccionesPorDia->max('cantidad');
                $anchoBarra = $maxReacciones > 0 ? round(($cantidad / $maxReacciones) * 100) : 0;
                $barra = str_repeat('█', min(50, round($anchoBarra / 2)));
                
                $html .= '<tr>
<td>' . $fechaFormateada . '</td>
<td class="number-cell">' . $cantidad . '</td>
<td style="font-family: monospace; color: #00A36C;">' . $barra . '</td>
</tr>';
            }
            
            $html .= '</table>';
        }

        // Tabla de Participantes
        if ($participantes->count() > 0) {
            $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="7">PARTICIPANTES (' . $participantes->count() . ')</td>
</tr>
<tr>
<th>Nombres</th>
<th>Apellidos</th>
<th>Email</th>
<th>Teléfono</th>
<th>Estado</th>
<th>Fecha Registro</th>
<th>Tipo</th>
</tr>';
            
            foreach ($participantes as $p) {
                $estadoClass = 'status-pendiente';
                $estadoTexto = ucfirst($p->estado ?? 'Pendiente');
                if (($p->estado ?? '') === 'aprobada') {
                    $estadoClass = 'status-aprobada';
                } elseif (($p->estado ?? '') === 'rechazada') {
                    $estadoClass = 'status-rechazada';
                }
                
                $html .= '<tr>
<td>' . htmlspecialchars($p->nombres ?? '') . '</td>
<td>' . htmlspecialchars($p->apellidos ?? '') . '</td>
<td>' . htmlspecialchars($p->email ?? '') . '</td>
<td>' . htmlspecialchars($p->telefono ?? '') . '</td>
<td class="' . $estadoClass . '">' . $estadoTexto . '</td>
<td>' . ($p->fecha_registro ? date('d/m/Y H:i', strtotime($p->fecha_registro)) : '') . '</td>
<td>' . htmlspecialchars($p->tipo ?? '') . '</td>
</tr>';
            }
            
            $html .= '</table>';
        }

        // Tabla de Reacciones
        if ($reacciones->count() > 0) {
            $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="3">REACCIONES (' . $reacciones->count() . ')</td>
</tr>
<tr>
<th style="width: 40%;">Nombre</th>
<th style="width: 35%;">Email</th>
<th style="width: 25%;">Fecha Reacción</th>
</tr>';
            
            foreach ($reacciones as $r) {
                $html .= '<tr>
<td>' . htmlspecialchars($r->nombre ?? 'Usuario anónimo') . '</td>
<td>' . htmlspecialchars($r->email ?? 'N/A') . '</td>
<td>' . ($r->fecha_reaccion ? date('d/m/Y H:i', strtotime($r->fecha_reaccion)) : '') . '</td>
</tr>';
            }
            
            $html .= '</table>';
        }

        // Distribución de participantes por estado
        if ($participantes->count() > 0) {
            $aprobados = $participantes->where('estado', 'aprobada')->count();
            $pendientes = $participantes->where('estado', 'pendiente')->count();
            $rechazados = $participantes->where('estado', 'rechazada')->count();
            
            $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="4">DISTRIBUCIÓN DE PARTICIPANTES POR ESTADO</td>
</tr>
<tr>
<th style="width: 30%;">Estado</th>
<th style="width: 20%;">Cantidad</th>
<th style="width: 20%;">Porcentaje</th>
<th style="width: 30%;">Visualización</th>
</tr>';
            
            if ($aprobados > 0) {
                $porcentaje = round(($aprobados / $participantes->count()) * 100, 1);
                $barra = str_repeat('█', min(50, round($porcentaje / 2)));
                $html .= '<tr>
<td class="status-aprobada">Aprobados</td>
<td class="number-cell">' . $aprobados . '</td>
<td class="number-cell">' . $porcentaje . '%</td>
<td style="font-family: monospace; color: #2E7D32;">' . $barra . '</td>
</tr>';
            }
            
            if ($pendientes > 0) {
                $porcentaje = round(($pendientes / $participantes->count()) * 100, 1);
                $barra = str_repeat('█', min(50, round($porcentaje / 2)));
                $html .= '<tr>
<td class="status-pendiente">Pendientes</td>
<td class="number-cell">' . $pendientes . '</td>
<td class="number-cell">' . $porcentaje . '%</td>
<td style="font-family: monospace; color: #F57C00;">' . $barra . '</td>
</tr>';
            }
            
            if ($rechazados > 0) {
                $porcentaje = round(($rechazados / $participantes->count()) * 100, 1);
                $barra = str_repeat('█', min(50, round($porcentaje / 2)));
                $html .= '<tr>
<td class="status-rechazada">Rechazados</td>
<td class="number-cell">' . $rechazados . '</td>
<td class="number-cell">' . $porcentaje . '%</td>
<td style="font-family: monospace; color: #C62828;">' . $barra . '</td>
</tr>';
            }
            
            $html .= '</table>';
        }

        // Bitácora de Cambios
        if (count($historial) > 0) {
            $html .= '<table class="data-table">
<tr>
<td class="section-title" colspan="4">BITÁCORA DE CAMBIOS</td>
</tr>
<tr>
<th style="width: 20%;">Fecha</th>
<th style="width: 25%;">Acción</th>
<th style="width: 40%;">Detalle</th>
<th style="width: 15%;">Usuario</th>
</tr>';
            
            foreach ($historial as $h) {
                $fecha = date('d/m/Y H:i', strtotime($h['fecha']));
                $icono = $h['icono'] ?? 'fa-clock';
                $color = '#00A36C';
                
                // Colores según tipo
                if (isset($h['tipo'])) {
                    $coloresPorTipo = [
                        'creacion' => '#00A36C',
                        'estado' => '#17a2b8',
                        'publicacion' => '#6f42c1',
                        'imagenes' => '#ffc107',
                        'actualizacion' => '#0C2B44',
                        'participacion' => '#00A36C'
                    ];
                    $color = $coloresPorTipo[$h['tipo']] ?? '#00A36C';
                }
                
                $html .= '<tr>
<td>' . $fecha . '</td>
<td style="color: ' . $color . '; font-weight: bold;">' . htmlspecialchars($h['accion']) . '</td>
<td>' . htmlspecialchars($h['detalle']) . '</td>
<td>' . htmlspecialchars($h['usuario']) . '</td>
</tr>';
            }
            
            $html .= '</table>';
        }

        // Resumen final
        $html .= '<table class="footer" width="100%">
<tr>
<td>
Reporte generado el ' . date('d/m/Y H:i:s') . ' | Mega Evento ID: ' . $megaEvento->mega_evento_id . ' | Total registros: ' . ($participantes->count() + $reacciones->count()) . '
</td>
</tr>
</table>';

        $html .= '</body></html>';
        
        return $html;
    }
}
