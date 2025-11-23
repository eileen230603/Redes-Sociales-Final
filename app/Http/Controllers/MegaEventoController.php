<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MegaEvento;
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
            $query = MegaEvento::with('ongPrincipal');
            
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
            $megaEvento = MegaEvento::with('ongPrincipal')->find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // El accessor del modelo ya genera URLs completas
            $megaEvento->makeVisible('imagenes');

            return response()->json([
                'success' => true,
                'mega_evento' => $megaEvento
            ]);
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
                            !in_array($host, ['localhost', '127.0.0.1']) && 
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
            
            // Combinar imágenes existentes con nuevas
            $imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
            
            // Eliminar duplicados y valores nulos, reindexar array
            $data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));
            
            // Log final antes de guardar
            \Log::info('Mega Evento Update - Imágenes finales a guardar', [
                'imagenes' => $data['imagenes']
            ]);

            // Asegurar que lat y lng se guarden correctamente
            if (isset($data['lat']) && $data['lat'] === '') {
                $data['lat'] = null;
            }
            if (isset($data['lng']) && $data['lng'] === '') {
                $data['lng'] = null;
            }

            $megaEvento->update($data);
            
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

            // Crear participación automáticamente aprobada
            DB::table('mega_evento_participantes_externos')->insert([
                'mega_evento_id' => $megaEventoId,
                'integrante_externo_id' => $integranteExterno->user_id,
                'estado_participacion' => 'aprobada', // Aprobación automática
                'fecha_registro' => now(),
                'activo' => true
            ]);

            // Crear notificación para la ONG
            $this->crearNotificacionMegaEvento($megaEvento, $externoId);

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
}
