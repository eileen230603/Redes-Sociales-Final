<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MegaEvento;
use App\Models\Notificacion;
use App\Models\Ong;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            // Obtener el usuario autenticado
            $usuarioAutenticado = $request->user();
            
            if (!$usuarioAutenticado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*')
                  ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                  ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            // Filtrar solo los mega eventos de la ONG autenticada
            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            
            \Log::info('Mega Evento Index - Iniciando', [
                'ong_id' => $ongIdAutenticada,
                'request_params' => $request->all()
            ]);
            
            // Construir query directamente con DB::table para evitar problemas con accessors
            $ahora = now();
            $query = DB::table('mega_eventos')
                ->where('ong_organizadora_principal', $ongIdAutenticada)
                // Excluir mega eventos finalizados por defecto (a menos que se pida explícitamente)
                ->where(function($q) use ($ahora, $request) {
                    // Si se solicita explícitamente ver finalizados, incluirlos
                    if ($request->has('estado') && $request->estado === 'finalizado') {
                        $q->whereNotNull('fecha_fin')
                          ->where('fecha_fin', '<', $ahora);
                    } else {
                        // Por defecto, excluir los que ya finalizaron
                        $q->where(function($subQ) use ($ahora) {
                            $subQ->whereNull('fecha_fin')
                                 ->orWhere('fecha_fin', '>=', $ahora);
                        });
                    }
                });
            
            // Filtro por categoría
            if ($request->has('categoria') && $request->categoria !== '' && $request->categoria !== 'todos') {
                $query->where('categoria', $request->categoria);
            }
            
            // Filtro por estado (solo si no es 'finalizado' que ya se maneja arriba)
            if ($request->has('estado') && $request->estado !== '' && $request->estado !== 'todos' && $request->estado !== 'finalizado') {
                $query->where('estado', $request->estado);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = trim($request->buscar);
                $buscarLower = '%' . strtolower($buscar) . '%';
                // Construir búsqueda con whereRaw para evitar problemas con closures en DB::table()
                $query->where(function($q) use ($buscarLower) {
                    $q->whereRaw('LOWER(COALESCE(titulo::text, \'\')) LIKE ?', [$buscarLower])
                      ->orWhereRaw('LOWER(COALESCE(descripcion::text, \'\')) LIKE ?', [$buscarLower]);
                });
            }
            
            // Obtener resultados
            try {
                $megaEventos = $query->orderByDesc('mega_evento_id')->get();
                \Log::info('Mega Evento Index - Consulta exitosa', [
                    'count' => $megaEventos->count()
                ]);
            } catch (\Exception $e) {
                \Log::error('Error en consulta DB: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            // Procesar cada mega evento usando map
            $megaEventos = $megaEventos->map(function($mega) use ($request) {
                try {
                    // Validar que $mega no sea null
                    if (!$mega || !isset($mega->mega_evento_id)) {
                        return null;
                    }
                    
                    // Obtener datos directamente de la base de datos
                    // Helper para formatear fechas de forma segura
                    $formatDate = function($date) {
                        if (!$date) return null;
                        try {
                            if (is_string($date)) {
                                return Carbon::parse($date)->format('Y-m-d H:i:s');
                            } elseif ($date instanceof \DateTime) {
                                return $date->format('Y-m-d H:i:s');
                            }
                            return $date;
                        } catch (\Exception $e) {
                            \Log::warning('Error formateando fecha: ' . $e->getMessage());
                            return is_string($date) ? $date : null;
                        }
                    };
                    
                    // Procesar imágenes - MEJORADO
                    $imagenesRaw = $mega->imagenes ?? null;
                    $imagenes = [];
                    
                // Si ya es array PHP (procesado por el cast del modelo)
                if (is_array($imagenesRaw)) {
                    // Mapear y limpiar cada elemento
                    $imagenes = array_map(function($img) {
                        if (!is_string($img)) return null;
                        $trimmed = trim($img);
                        return $trimmed !== '' ? $trimmed : null;
                    }, $imagenesRaw);
                    $imagenes = array_filter($imagenes, function($img) {
                        return $img !== null;
                    });
                }
                // Si es string, procesarlo
                elseif (is_string($imagenesRaw) && !empty($imagenesRaw)) {
                    $trimmed = trim($imagenesRaw);
                    
                    // Verificar si empieza con [ o { para identificar JSON
                    if (!empty($trimmed) && ($trimmed[0] === '[' || $trimmed[0] === '{')) {
                        $decoded = json_decode($trimmed, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            // Procesar igual que array
                            $imagenes = array_map(function($img) {
                                if (!is_string($img)) return null;
                                $trimmed = trim($img);
                                return $trimmed !== '' ? $trimmed : null;
                            }, $decoded);
                            $imagenes = array_filter($imagenes, function($img) {
                                return $img !== null;
                            });
                        } else {
                            // JSON inválido, tratar como URL única
                            $imagenes = [$trimmed];
                        }
                    } else {
                        // String simple, tratarlo como URL única
                        $imagenes = [$trimmed];
                        }
                }
                
                // Limpiar el array final
                $imagenes = array_values(array_unique(array_filter($imagenes, function($img) {
                    return !empty($img) && is_string($img);
                })));
                        
                        // Obtener base URL del request o configuración
                        $baseUrl = $request->getSchemeAndHttpHost() ?: env('APP_URL', 'http://10.26.5.12:8000');
                        
                // Procesar cada imagen para construir URLs completas
                $imagenesProcesadas = [];
                foreach ($imagenes as $img) {
                            $img = trim($img);
                    if (empty($img)) continue;
                    
                            // Filtrar rutas inválidas (solo si no es URL completa)
                            $esUrlCompleta = strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0;
                            if (!$esUrlCompleta && (stripos($img, 'wp-content') !== false || 
                                stripos($img, 'resizer/') !== false || 
                                stripos($img, '/resizer/') !== false)) {
                                continue;
                            }
                            
                            // Si ya es URL completa, reemplazar IPs antiguas y usar directamente
                    if ($esUrlCompleta) {
                                // Reemplazar IPs antiguas explícitamente
                                $img = str_replace('http://127.0.0.1:8000', $baseUrl, $img);
                                $img = str_replace('https://127.0.0.1:8000', $baseUrl, $img);
                                $img = str_replace('http://192.168.0.6:8000', $baseUrl, $img);
                                $img = str_replace('https://192.168.0.6:8000', $baseUrl, $img);
                                $img = str_replace('http://10.26.15.110:8000', $baseUrl, $img);
                                $img = str_replace('https://10.26.15.110:8000', $baseUrl, $img);
                                $img = str_replace('http://10.26.5.12:8000', $baseUrl, $img);
                                $img = str_replace('https://10.26.5.12:8000', $baseUrl, $img);
                                
                                // Si es una URL externa de internet, mantenerla
                                $parsedUrl = parse_url($img);
                                $currentHost = parse_url($baseUrl, PHP_URL_HOST);
                                
                                if (isset($parsedUrl['host']) && $parsedUrl['host'] !== $currentHost) {
                                    // Si no es localhost ni IP local, es URL externa - mantenerla
                                    if ($parsedUrl['host'] !== 'localhost' && 
                                        $parsedUrl['host'] !== '127.0.0.1' &&
                                        $parsedUrl['host'] !== '10.26.5.12' && 
                                        strpos($parsedUrl['host'], '192.168.') !== 0 &&
                                        strpos($parsedUrl['host'], '10.26.') !== 0) {
                                $imagenesProcesadas[] = $img;
                                        continue;
                                    }
                                    
                                    // Es IP local antigua, actualizar host
                                    $parsedUrl['scheme'] = parse_url($baseUrl, PHP_URL_SCHEME) ?? 'http';
                                    $parsedUrl['host'] = $currentHost;
                                    $parsedUrl['port'] = parse_url($baseUrl, PHP_URL_PORT);
                                    
                                    $img = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] 
                                        . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') 
                                        . ($parsedUrl['path'] ?? '');
                                    }
                                
                        $imagenesProcesadas[] = $img;
                                continue;
                            }
                            
                            // Función helper para obtener la ruta de storage
                            $getStoragePath = function($image) {
                                // Si empieza con /storage/, extraer la parte después
                                if (strpos($image, '/storage/') === 0) {
                                    return ltrim(str_replace('/storage/', '', $image), '/');
                                }
                                // Si empieza con storage/, quitar el prefijo
                                if (strpos($image, 'storage/') === 0) {
                                    return ltrim(str_replace('storage/', '', $image), '/');
                                }
                                // Si es ruta relativa, retornarla
                                return ltrim($image, '/');
                            };
                            
                            $storagePath = $getStoragePath($img);
                            
                            // Verificar si el archivo existe en storage
                            $existe = Storage::disk('public')->exists($storagePath);
                            
                            // Construir URL completa para rutas de storage
                                $urlCompleta = rtrim($baseUrl, '/') . '/storage/' . $storagePath;
                                
                                // Solo agregar si el archivo existe O si parece ser una ruta válida de mega_eventos
                                if ($existe || (strpos($storagePath, 'mega_eventos/') === 0 && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $storagePath))) {
                        $imagenesProcesadas[] = $urlCompleta;
                                    
                                    if (!$existe) {
                                        \Log::warning('Imagen agregada sin verificación de existencia', [
                                            'storage_path' => $storagePath,
                                            'url' => $urlCompleta,
                                            'mega_evento_id' => $mega->mega_evento_id ?? 'N/A'
                                        ]);
                                    }
                                } else {
                                    \Log::debug('Imagen omitida - no existe en storage', [
                                        'storage_path' => $storagePath,
                                        'mega_evento_id' => $mega->mega_evento_id ?? 'N/A'
                                    ]);
                        }
                    }
                    
                    // Filtrar duplicados y valores nulos
                $imagenesProcesadas = array_values(array_unique(array_filter($imagenesProcesadas, function($img) {
                    return !empty($img) && is_string($img);
                })));
                    
                    // Log para depuración
                if (count($imagenesProcesadas) > 0) {
                        \Log::info('Imágenes procesadas para mega evento', [
                            'mega_evento_id' => $mega->mega_evento_id ?? 'N/A',
                        'total' => count($imagenesProcesadas),
                        'imagenes' => $imagenesProcesadas
                    ]);
                }
                    
                    // Construir objeto de retorno
                    $megaData = [
                        'mega_evento_id' => (int) ($mega->mega_evento_id ?? 0),
                        'titulo' => $mega->titulo ?? '',
                        'descripcion' => $mega->descripcion ?? null,
                        'fecha_inicio' => $formatDate($mega->fecha_inicio ?? null),
                        'fecha_fin' => $formatDate($mega->fecha_fin ?? null),
                        'ubicacion' => $mega->ubicacion ?? null,
                        'lat' => isset($mega->lat) && $mega->lat !== null ? (float) $mega->lat : null,
                        'lng' => isset($mega->lng) && $mega->lng !== null ? (float) $mega->lng : null,
                        'categoria' => $mega->categoria ?? 'social',
                        'estado' => $mega->estado ?? 'planificacion',
                        'es_publico' => (bool) ($mega->es_publico ?? false),
                        'activo' => (bool) ($mega->activo ?? true),
                        'fecha_creacion' => $formatDate($mega->fecha_creacion ?? null),
                        'fecha_actualizacion' => $formatDate($mega->fecha_actualizacion ?? null),
                        'ong_organizadora_principal' => isset($mega->ong_organizadora_principal) ? (int) $mega->ong_organizadora_principal : null,
                        'imagenes' => $imagenesProcesadas
                    ];
                    
                    // Procesar capacidad_maxima
                    $capacidadMaxima = $mega->capacidad_maxima ?? null;
                    if ($capacidadMaxima !== null && is_numeric($capacidadMaxima)) {
                        $megaData['capacidad_maxima'] = (int) $capacidadMaxima;
                    } else {
                        $megaData['capacidad_maxima'] = null;
                    }
                    
                    // Cargar ONG organizadora de forma segura usando DB::table para evitar problemas
                    try {
                        if ($mega->ong_organizadora_principal) {
                            $ongData = DB::table('ongs')
                                ->join('usuarios', 'ongs.user_id', '=', 'usuarios.id_usuario')
                                ->where('ongs.user_id', $mega->ong_organizadora_principal)
                                ->select(
                                    'ongs.user_id',
                                    'ongs.nombre_ong',
                                    'ongs.foto_perfil as ong_foto_perfil',
                                    'usuarios.nombre_usuario',
                                    'usuarios.foto_perfil as usuario_foto_perfil'
                                )
                                ->first();
                            
                            if ($ongData) {
                                // Obtener foto_perfil sin usar el accessor (priorizar foto de ONG, luego de usuario)
                                $fotoPerfil = $ongData->ong_foto_perfil ?? $ongData->usuario_foto_perfil ?? null;
                                $fotoPerfilUrl = null;
                                if ($fotoPerfil) {
                                    if (strpos($fotoPerfil, 'http://') === 0 || strpos($fotoPerfil, 'https://') === 0) {
                                        $fotoPerfilUrl = $fotoPerfil;
                                    } else {
                                        // Usar el origen de la petición
                                        $origin = $request->header('Origin') 
                                            ?? $request->getSchemeAndHttpHost() 
                                            ?? env('PUBLIC_APP_URL', env('APP_URL', 'http://10.26.5.12:8000'));
                                        $fotoPerfilUrl = rtrim($origin, '/') . '/storage/' . ltrim($fotoPerfil, '/');
                                    }
                                }
                                
                                $megaData['ong_principal'] = [
                                    'user_id' => $ongData->user_id,
                                    'nombre_ong' => $ongData->nombre_ong ?? '',
                                    'nombre_usuario' => $ongData->nombre_usuario ?? '',
                                    'nombre' => $ongData->nombre_ong ?? $ongData->nombre_usuario ?? 'ONG',
                                    'foto_perfil_url' => $fotoPerfilUrl,
                                    'avatar' => $fotoPerfilUrl // Alias para compatibilidad
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error al cargar ONG: ' . $e->getMessage());
                        // Continuar sin la ONG si hay error
                    }
                    
                    return $megaData;
                } catch (\Exception $e) {
                    \Log::error('Error al procesar mega evento: ' . $e->getMessage(), [
                        'mega_evento_id' => $mega->mega_evento_id ?? 'unknown',
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Retornar null si hay error, será filtrado después
                    return null;
                }
            })->filter(function($mega) {
                return $mega !== null;
            })->values();
            
            \Log::info('Mega Evento Index - Completado', [
                'count' => $megaEventos->count()
            ]);
            
            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'mega_eventos' => $megaEventos->toArray()
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            \Log::error('Mega Evento Index - Error completo', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'error_class' => get_class($e)
            ]);
            
            // Asegurar que siempre se devuelva una respuesta con headers CORS
            try {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al obtener mega eventos: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                        'class' => get_class($e)
                    ] : null
                ], 500)->header('Access-Control-Allow-Origin', '*')
                  ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                  ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            } catch (\Exception $responseError) {
                // Si falla la respuesta JSON, devolver respuesta simple
                \Log::error('Error al crear respuesta de error: ' . $responseError->getMessage());
                return response('Error interno del servidor', 500)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }
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
                'capacidad_maxima' => isset($data['capacidad_maxima']) && $data['capacidad_maxima'] !== '' && is_numeric($data['capacidad_maxima']) && (int)$data['capacidad_maxima'] > 0 
                    ? (int) $data['capacidad_maxima'] 
                    : null,
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

            // Procesar patrocinadores
            $patrocinadoresIds = [];
            if ($request->has('patrocinadores')) {
                $patrocinadoresRaw = $request->input('patrocinadores');
                if (is_string($patrocinadoresRaw)) {
                    $patrocinadoresIds = json_decode($patrocinadoresRaw, true) ?? [];
                } elseif (is_array($patrocinadoresRaw)) {
                    $patrocinadoresIds = $patrocinadoresRaw;
                }
            }

            // Guardar patrocinadores en la tabla mega_evento_patrocinadores
            if (!empty($patrocinadoresIds) && is_array($patrocinadoresIds)) {
                foreach ($patrocinadoresIds as $empresaId) {
                    try {
                        $empresaId = (int) $empresaId;
                        // Verificar que la empresa existe
                        $empresa = \App\Models\Empresa::where('user_id', $empresaId)->first();
                        if (!$empresa) {
                            \Log::warning("Empresa con user_id {$empresaId} no encontrada al crear patrocinador para mega evento {$megaEvento->mega_evento_id}");
                            continue;
                        }

                        // Verificar si ya existe
                        $existe = DB::table('mega_evento_patrocinadores')
                            ->where('mega_evento_id', $megaEvento->mega_evento_id)
                            ->where('empresa_id', $empresaId)
                            ->exists();

                        if (!$existe) {
                            DB::table('mega_evento_patrocinadores')->insert([
                                'mega_evento_id' => $megaEvento->mega_evento_id,
                                'empresa_id' => $empresaId,
                                'tipo_patrocinio' => null,
                                'monto_contribucion' => null,
                                'tipo_contribucion' => null,
                                'descripcion_contribucion' => null,
                                'fecha_compromiso' => now(),
                                'estado_compromiso' => 'confirmado',
                                'activo' => true,
                            ]);
                            \Log::info("Patrocinador {$empresaId} agregado a mega evento {$megaEvento->mega_evento_id}");
                        }
                    } catch (\Throwable $e) {
                        \Log::error("Error agregando patrocinador {$empresaId} al mega evento {$megaEvento->mega_evento_id}: " . $e->getMessage());
                        // Continuar con los demás patrocinadores aunque uno falle
                    }
                }
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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
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

            // Obtener imágenes directamente desde PostgreSQL
            $imagenesRaw = $megaEvento->getRawOriginal('imagenes');
            
            \Log::info('Mega Evento Show - Imágenes desde PostgreSQL', [
                'mega_evento_id' => $id,
                'imagenes_raw_type' => gettype($imagenesRaw),
                'imagenes_raw' => $imagenesRaw
            ]);
            
            // Procesar imágenes desde PostgreSQL JSON
            $imagenesProcesadas = [];
            if ($imagenesRaw) {
                // Si es string JSON (como viene de PostgreSQL), decodificarlo
                if (is_string($imagenesRaw)) {
                    $imagenesDecoded = json_decode($imagenesRaw, true);
                    if (is_array($imagenesDecoded)) {
                        $imagenesProcesadas = $imagenesDecoded;
                    } elseif (json_last_error() === JSON_ERROR_NONE && $imagenesDecoded !== null) {
                        // Si es un solo valor, convertirlo a array
                        $imagenesProcesadas = [$imagenesDecoded];
                    }
                } elseif (is_array($imagenesRaw)) {
                    $imagenesProcesadas = $imagenesRaw;
                }
            }
            
            \Log::info('Mega Evento Show - Imágenes decodificadas', [
                'mega_evento_id' => $id,
                'imagenes_procesadas_count' => count($imagenesProcesadas),
                'imagenes_procesadas' => $imagenesProcesadas
            ]);
            
            // Procesar cada imagen para construir URLs completas
            $baseUrl = request()->getSchemeAndHttpHost() ?? env('PUBLIC_APP_URL', env('APP_URL', 'http://10.26.5.12:8000'));
            
            $imagenesFinales = [];
            foreach ($imagenesProcesadas as $img) {
                if (empty($img) || !is_string($img)) continue;
                
                // Verificar si es URL completa antes de filtrar
                $esUrlCompleta = strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0;
                
                // Filtrar rutas inválidas (solo si no es URL completa)
                if (!$esUrlCompleta) {
                if (strpos($img, '/templates/') !== false || 
                    strpos($img, '/cache/') !== false || 
                    strpos($img, '/yootheme/') !== false ||
                        strpos($img, '/resizer/') !== false ||
                        strpos($img, '/wp-content/') !== false ||
                    strpos($img, 'templates/') !== false || 
                    strpos($img, 'cache/') !== false || 
                        strpos($img, 'yootheme/') !== false ||
                        strpos($img, 'resizer/') !== false ||
                        strpos($img, 'wp-content/') !== false) {
                    continue;
                    }
                }
                
                // Si ya es URL completa, mantenerla (pero actualizar host si es necesario)
                if ($esUrlCompleta) {
                    // Reemplazar IPs antiguas explícitamente
                    $img = str_replace('http://127.0.0.1:8000', $baseUrl, $img);
                    $img = str_replace('https://127.0.0.1:8000', $baseUrl, $img);
                    $img = str_replace('http://192.168.0.6:8000', $baseUrl, $img);
                    $img = str_replace('https://192.168.0.6:8000', $baseUrl, $img);
                    $img = str_replace('http://10.26.15.110:8000', $baseUrl, $img);
                    $img = str_replace('https://10.26.15.110:8000', $baseUrl, $img);
                    $img = str_replace('http://10.26.5.12:8000', $baseUrl, $img);
                    $img = str_replace('https://10.26.5.12:8000', $baseUrl, $img);
                    
                    // Actualizar host si es diferente (solo para IPs locales antiguas)
                    $parsedUrl = parse_url($img);
                    $currentHost = parse_url($baseUrl, PHP_URL_HOST);
                    
                    if (isset($parsedUrl['host']) && $parsedUrl['host'] !== $currentHost) {
                        // Solo actualizar si es una IP local antigua
                        $hostsAntiguos = ['127.0.0.1', '192.168.0.6', '10.26.15.110'];
                        if (in_array($parsedUrl['host'], $hostsAntiguos) || 
                            strpos($parsedUrl['host'], 'localhost') !== false) {
                            $parsedUrl['scheme'] = parse_url($baseUrl, PHP_URL_SCHEME) ?? 'http';
                            $parsedUrl['host'] = $currentHost;
                            $parsedUrl['port'] = parse_url($baseUrl, PHP_URL_PORT);
                            
                            $img = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] 
                                . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') 
                                . ($parsedUrl['path'] ?? '');
                        }
                    }
                    $imagenesFinales[] = $img;
                } elseif (str_starts_with($img, '/storage/')) {
                    // Verificar si es una ruta externa mal formateada
                    $rutasExternas = ['/storage/resizer/', '/storage/wp-content/', '/storage/templates/', 
                                     '/storage/cache/', '/storage/yootheme/'];
                    $esRutaExterna = false;
                    foreach ($rutasExternas as $ruta) {
                        if (stripos($img, $ruta) === 0) {
                            $esRutaExterna = true;
                            break;
                        }
                    }
                    
                    if ($esRutaExterna) {
                        // Es una ruta externa mal formateada, omitirla
                        \Log::warning('Ruta externa mal formateada detectada en mega evento', [
                            'mega_evento_id' => $id,
                            'ruta' => $img
                        ]);
                        continue;
                    }
                    
                    // Ruta relativa con /storage/
                    $imagenesFinales[] = rtrim($baseUrl, '/') . $img;
                } elseif (str_starts_with($img, 'storage/')) {
                    // Verificar si es una ruta externa mal formateada
                    $rutasExternas = ['storage/resizer/', 'storage/wp-content/', 'storage/templates/', 
                                     'storage/cache/', 'storage/yootheme/'];
                    $esRutaExterna = false;
                    foreach ($rutasExternas as $ruta) {
                        if (stripos($img, $ruta) === 0) {
                            $esRutaExterna = true;
                            break;
                        }
                    }
                    
                    if ($esRutaExterna) {
                        // Es una ruta externa mal formateada, omitirla
                        \Log::warning('Ruta externa mal formateada detectada en mega evento', [
                            'mega_evento_id' => $id,
                            'ruta' => $img
                        ]);
                        continue;
                    }
                    
                    // Ruta relativa con storage/
                    $imagenesFinales[] = rtrim($baseUrl, '/') . '/' . $img;
                } else {
                    // Verificar si es una ruta externa sin prefijo
                    $rutasExternas = ['resizer/', 'wp-content/', 'templates/', 'cache/', 'yootheme/'];
                    $esRutaExterna = false;
                    foreach ($rutasExternas as $ruta) {
                        if (stripos($img, $ruta) !== false) {
                            $esRutaExterna = true;
                            break;
                        }
                    }
                    
                    if ($esRutaExterna) {
                        // Es una ruta externa, omitirla
                        \Log::warning('Ruta externa detectada en mega evento (sin URL completa)', [
                            'mega_evento_id' => $id,
                            'ruta' => $img
                        ]);
                        continue;
                    }
                    
                    // Ruta relativa sin prefijo
                    $imagenesFinales[] = rtrim($baseUrl, '/') . '/storage/' . ltrim($img, '/');
                }
            }
            
            \Log::info('Mega Evento Show - Imágenes finales procesadas', [
                'mega_evento_id' => $id,
                'imagenes_finales_count' => count($imagenesFinales),
                'imagenes_finales' => $imagenesFinales
            ]);
            
            // Asignar las imágenes procesadas directamente como array
            // Esto asegura que se serialicen como array en JSON, no como string
            $megaEvento->setAttribute('imagenes', $imagenesFinales);
            $megaEvento->makeVisible('imagenes');
            
            // Asegurar que capacidad_maxima esté visible y se devuelva correctamente
            $megaEvento->makeVisible('capacidad_maxima');
            
            // Obtener el valor crudo de capacidad_maxima para asegurar que se devuelva correctamente
            $capacidadMaxima = $megaEvento->getRawOriginal('capacidad_maxima');
            // Si es null, mantenerlo como null; si es numérico, convertirlo a int
            if ($capacidadMaxima !== null && is_numeric($capacidadMaxima)) {
                $megaEvento->capacidad_maxima = (int) $capacidadMaxima;
            } else {
                $megaEvento->capacidad_maxima = null;
            }

            // Obtener patrocinadores (empresas que patrocinan este mega evento)
            $patrocinadores = DB::table('mega_evento_patrocinadores as mep')
                ->join('empresas as e', 'mep.empresa_id', '=', 'e.user_id')
                ->join('usuarios as u', 'e.user_id', '=', 'u.id_usuario')
                ->where('mep.mega_evento_id', $id)
                ->where('mep.activo', true)
                ->select(
                    'e.user_id as id',
                    'e.nombre_empresa as nombre',
                    'e.NIT',
                    'e.descripcion',
                    'u.foto_perfil',
                    'mep.tipo_patrocinio',
                    'mep.monto_contribucion',
                    'mep.tipo_contribucion',
                    'mep.estado_compromiso'
                )
                ->get()
                ->map(function($pat) use ($id) {
                    $fotoPerfil = null;
                    if ($pat->foto_perfil) {
                        $fotoPerfil = $pat->foto_perfil;
                        
                        // Filtrar rutas inválidas
                        if (strpos($fotoPerfil, '/templates/') !== false || 
                            strpos($fotoPerfil, '/cache/') !== false || 
                            strpos($fotoPerfil, '/yootheme/') !== false) {
                            $fotoPerfil = null;
                        } else {
                            // Construir URL completa si no lo es
                            if (!str_starts_with($fotoPerfil, 'http://') && !str_starts_with($fotoPerfil, 'https://')) {
                                $baseUrl = request()->getSchemeAndHttpHost() ?? env('PUBLIC_APP_URL', env('APP_URL', 'http://10.26.5.12:8000'));
                                
                                // Si ya empieza con /storage/, solo agregar el dominio
                                if (str_starts_with($fotoPerfil, '/storage/')) {
                                    $fotoPerfil = rtrim($baseUrl, '/') . $fotoPerfil;
                                } elseif (str_starts_with($fotoPerfil, 'storage/')) {
                                    $fotoPerfil = rtrim($baseUrl, '/') . '/' . $fotoPerfil;
                                } else {
                                    // Si es una ruta relativa, agregar /storage/
                                    $fotoPerfil = rtrim($baseUrl, '/') . '/storage/' . ltrim($fotoPerfil, '/');
                                }
                            }
                        }
                    }
                    
                    return [
                        'id' => $pat->id,
                        'nombre' => $pat->nombre,
                        'NIT' => $pat->NIT,
                        'descripcion' => $pat->descripcion,
                        'foto_perfil' => $fotoPerfil,
                        'tipo_patrocinio' => $pat->tipo_patrocinio,
                        'monto_contribucion' => $pat->monto_contribucion,
                        'tipo_contribucion' => $pat->tipo_contribucion,
                        'estado_compromiso' => $pat->estado_compromiso,
                    ];
                });
            
            $megaEvento->patrocinadores = $patrocinadores;

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

            // Preparar el mega evento para la respuesta JSON
            // Convertir a array y asegurar que las imágenes sean un array
            $megaEventoArray = $megaEvento->toArray();
            
            // Asegurar que las imágenes sean un array, no un string JSON
            // Forzar que las imágenes procesadas se asignen directamente como array
            $megaEventoArray['imagenes'] = $imagenesFinales;
            
            \Log::info('Mega Evento Show - Respuesta JSON final', [
                'mega_evento_id' => $id,
                'imagenes_count' => count($imagenesFinales),
                'imagenes_type' => gettype($megaEventoArray['imagenes']),
                'imagenes_is_array' => is_array($megaEventoArray['imagenes']),
                'imagenes_sample' => count($imagenesFinales) > 0 ? $imagenesFinales[0] : null
            ]);
            
            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'mega_evento' => $megaEventoArray
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            return response()->json([
                'success' => false,
                'error' => 'Mega evento no encontrado'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega evento: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Actualizar un mega evento
     */
    public function update(Request $request, $id)
    {
        try {
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Si no viene ong_organizadora_principal, usar el que ya tiene el mega evento
            if (!$request->has('ong_organizadora_principal') && $megaEvento->ong_organizadora_principal) {
                $request->merge(['ong_organizadora_principal' => $megaEvento->ong_organizadora_principal]);
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
                'ong_organizadora_principal' => 'sometimes|integer|exists:ongs,user_id',
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

            // Obtener todos los campos del request
            // FormData siempre envía los campos, aunque estén vacíos
            $requestData = $request->all();
            
            // Extraer solo los campos que necesitamos
            // IMPORTANTE: Siempre incluir los campos que vienen del request, incluso si están vacíos
            $data = [];
            $camposPermitidos = [
                'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin',
                'ubicacion', 'lat', 'lng', 'categoria', 'estado',
                'es_publico', 'activo', 'ong_organizadora_principal', 'capacidad_maxima'
            ];
            
            foreach ($camposPermitidos as $campo) {
                // FormData siempre envía los campos, así que siempre los incluimos si están presentes
                if ($request->has($campo)) {
                    $value = $request->input($campo);
                    // Incluir el campo incluso si está vacío (para poder establecer null)
                    $data[$campo] = $value;
                }
            }
            
            // Asegurar que fecha_actualizacion siempre se actualice
            $data['fecha_actualizacion'] = now();
            
            // Log para depuración - mostrar TODOS los datos recibidos
            \Log::info('Mega Evento Update - Request recibido', [
                'mega_evento_id' => $id,
                'campos_recibidos' => array_keys($data),
                'data_values' => $data,
                'request_all_keys' => array_keys($requestData),
                'request_all_values' => $requestData,
                'request_method' => $request->method(),
                'request_content_type' => $request->header('Content-Type'),
                'has_titulo' => $request->has('titulo'),
                'titulo_value' => $request->input('titulo'),
                'has_descripcion' => $request->has('descripcion'),
                'descripcion_value' => $request->input('descripcion'),
                'has_capacidad_maxima' => $request->has('capacidad_maxima'),
                'capacidad_maxima_value' => $request->input('capacidad_maxima')
            ]);

            // Normalizar valores booleanos para PostgreSQL
            if (isset($data['es_publico'])) {
                $esPublicoValue = $data['es_publico'];
                if (is_string($esPublicoValue)) {
                    $data['es_publico'] = in_array(strtolower($esPublicoValue), ['1', 'true', 'yes', 'on']);
                } else {
                    $data['es_publico'] = filter_var($esPublicoValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
                }
            }
            if (isset($data['activo'])) {
                $activoValue = $data['activo'];
                if (is_string($activoValue)) {
                    $data['activo'] = in_array(strtolower($activoValue), ['1', 'true', 'yes', 'on']);
                } else {
                    $data['activo'] = filter_var($activoValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
                }
            }
            
            // Normalizar coordenadas
            if (isset($data['lat']) && $data['lat'] !== '' && $data['lat'] !== null) {
                $data['lat'] = is_numeric($data['lat']) ? (float) $data['lat'] : null;
            }
            if (isset($data['lng']) && $data['lng'] !== '' && $data['lng'] !== null) {
                $data['lng'] = is_numeric($data['lng']) ? (float) $data['lng'] : null;
            }

            // Normalizar valores numéricos - Capacidad máxima
            // Verificar si el campo existe en el array de datos (FormData siempre envía el campo, aunque esté vacío)
            if (array_key_exists('capacidad_maxima', $data)) {
                $capacidadValue = $data['capacidad_maxima'];
                if ($capacidadValue === '' || $capacidadValue === null || $capacidadValue === 'null' || (is_string($capacidadValue) && trim($capacidadValue) === '')) {
                    $data['capacidad_maxima'] = null;
                } elseif (is_numeric($capacidadValue) && (int)$capacidadValue > 0) {
                    $data['capacidad_maxima'] = (int) $capacidadValue;
                } else {
                    $data['capacidad_maxima'] = null;
                }
            } else {
                // Si no viene el campo, mantener el valor existente
                $data['capacidad_maxima'] = $megaEvento->capacidad_maxima;
            }
            
            // Normalizar coordenadas - asegurar que se guarden correctamente
            if (isset($data['lat'])) {
                if ($data['lat'] === '' || $data['lat'] === null) {
                    $data['lat'] = null;
                } elseif (is_numeric($data['lat'])) {
                    $data['lat'] = (float) $data['lat'];
                } else {
                    $data['lat'] = null;
                }
            }
            
            if (isset($data['lng'])) {
                if ($data['lng'] === '' || $data['lng'] === null) {
                    $data['lng'] = null;
                } elseif (is_numeric($data['lng'])) {
                    $data['lng'] = (float) $data['lng'];
                } else {
                    $data['lng'] = null;
                }
            }
            
            // Asegurar que ubicación se guarde correctamente
            if (isset($data['ubicacion']) && is_string($data['ubicacion']) && trim($data['ubicacion']) === '') {
                $data['ubicacion'] = null;
            }
            
            // Asegurar que descripción se guarde correctamente
            if (isset($data['descripcion']) && is_string($data['descripcion']) && trim($data['descripcion']) === '') {
                $data['descripcion'] = null;
            }
            
            // Asegurar que categoría se guarde correctamente
            if (isset($data['categoria']) && is_string($data['categoria']) && trim($data['categoria']) === '') {
                $data['categoria'] = null;
            }

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
                            !in_array($host, ['localhost', '127.0.0.1', '192.168.0.6', '10.26.15.110', '10.26.5.12']) && 
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
            DB::transaction(function () use ($megaEvento, &$data, $imagenesActuales, $nuevasImagenes, $seEstaFinalizando, $id) {
                // Recargar el mega evento dentro de la transacción para asegurar datos frescos
                $megaEvento = MegaEvento::findOrFail($id);
                // 1. Combinar imágenes existentes con nuevas
                $imagenesCombinadas = array_merge($imagenesActuales, $nuevasImagenes);
                
                // 2. Eliminar duplicados y valores nulos, reindexar array
                $imagenesFinales = array_values(array_unique(array_filter($imagenesCombinadas, function($img) {
                    return !empty($img) && is_string($img);
                })));
                
                // 3. Preparar array de datos para actualizar - SIEMPRE incluir todos los campos
                $updateData = [];
                
                // IMPORTANTE: FormData siempre envía los campos, aunque estén vacíos
                // Por lo tanto, siempre debemos usar los valores del request si están presentes
                
                // TÍTULO - SIEMPRE actualizar si viene del request
                if (isset($data['titulo'])) {
                    $updateData['titulo'] = trim($data['titulo']) !== '' ? trim($data['titulo']) : $megaEvento->titulo;
                } else {
                    $updateData['titulo'] = $megaEvento->titulo;
                }
                
                // DESCRIPCIÓN
                if (isset($data['descripcion'])) {
                    $descripcionValue = trim($data['descripcion']);
                    $updateData['descripcion'] = $descripcionValue === '' ? null : $descripcionValue;
                } else {
                    $updateData['descripcion'] = $megaEvento->descripcion;
                }
                
                // FECHAS - convertir strings a formato de base de datos
                if (isset($data['fecha_inicio']) && $data['fecha_inicio'] !== null && $data['fecha_inicio'] !== '') {
                    try {
                        if (is_string($data['fecha_inicio'])) {
                            $updateData['fecha_inicio'] = Carbon::parse($data['fecha_inicio'])->format('Y-m-d H:i:s');
                        } elseif ($data['fecha_inicio'] instanceof Carbon || $data['fecha_inicio'] instanceof \DateTime) {
                            $updateData['fecha_inicio'] = $data['fecha_inicio']->format('Y-m-d H:i:s');
                        } else {
                            $updateData['fecha_inicio'] = $data['fecha_inicio'];
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error parseando fecha_inicio: ' . $e->getMessage());
                        $updateData['fecha_inicio'] = $megaEvento->fecha_inicio instanceof Carbon 
                            ? $megaEvento->fecha_inicio->format('Y-m-d H:i:s')
                            : $megaEvento->fecha_inicio;
                    }
                } else {
                    $updateData['fecha_inicio'] = $megaEvento->fecha_inicio instanceof Carbon 
                        ? $megaEvento->fecha_inicio->format('Y-m-d H:i:s')
                        : $megaEvento->fecha_inicio;
                }
                
                if (isset($data['fecha_fin']) && $data['fecha_fin'] !== null && $data['fecha_fin'] !== '') {
                    try {
                        if (is_string($data['fecha_fin'])) {
                            $updateData['fecha_fin'] = Carbon::parse($data['fecha_fin'])->format('Y-m-d H:i:s');
                        } elseif ($data['fecha_fin'] instanceof Carbon || $data['fecha_fin'] instanceof \DateTime) {
                            $updateData['fecha_fin'] = $data['fecha_fin']->format('Y-m-d H:i:s');
                        } else {
                            $updateData['fecha_fin'] = $data['fecha_fin'];
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error parseando fecha_fin: ' . $e->getMessage());
                        $updateData['fecha_fin'] = $megaEvento->fecha_fin instanceof Carbon 
                            ? $megaEvento->fecha_fin->format('Y-m-d H:i:s')
                            : $megaEvento->fecha_fin;
                    }
                } else {
                    $updateData['fecha_fin'] = $megaEvento->fecha_fin instanceof Carbon 
                        ? $megaEvento->fecha_fin->format('Y-m-d H:i:s')
                        : $megaEvento->fecha_fin;
                }
                
                // UBICACIÓN
                if (isset($data['ubicacion'])) {
                    $updateData['ubicacion'] = trim($data['ubicacion']) === '' ? null : trim($data['ubicacion']);
                } else {
                    $updateData['ubicacion'] = $megaEvento->ubicacion;
                }
                
                // Coordenadas - convertir a decimal si es necesario
                if (isset($data['lat'])) {
                    if ($data['lat'] === '' || $data['lat'] === null) {
                        $updateData['lat'] = null;
                    } else {
                        $updateData['lat'] = is_numeric($data['lat']) ? (float) $data['lat'] : null;
                    }
                } else {
                    $updateData['lat'] = $megaEvento->lat;
                }
                
                if (isset($data['lng'])) {
                    if ($data['lng'] === '' || $data['lng'] === null) {
                        $updateData['lng'] = null;
                    } else {
                        $updateData['lng'] = is_numeric($data['lng']) ? (float) $data['lng'] : null;
                    }
                } else {
                    $updateData['lng'] = $megaEvento->lng;
                }
                
                // CATEGORÍA
                if (isset($data['categoria'])) {
                    $updateData['categoria'] = trim($data['categoria']) === '' ? null : trim($data['categoria']);
                } else {
                    $updateData['categoria'] = $megaEvento->categoria;
                }
                
                // ESTADO
                if (isset($data['estado']) && $data['estado'] !== null && $data['estado'] !== '') {
                    $updateData['estado'] = $data['estado'];
                } else {
                    $updateData['estado'] = $megaEvento->estado;
                }
                
                // CAPACIDAD MÁXIMA - usar el valor normalizado del request
                if (isset($data['capacidad_maxima'])) {
                    $updateData['capacidad_maxima'] = $data['capacidad_maxima']; // Ya está normalizado (null o int)
                } else {
                    $updateData['capacidad_maxima'] = $megaEvento->capacidad_maxima;
                }
                
                // ES PÚBLICO
                if (isset($data['es_publico'])) {
                    $updateData['es_publico'] = $data['es_publico']; // Ya está normalizado (bool)
                } else {
                    $updateData['es_publico'] = $megaEvento->es_publico;
                }
                
                // ACTIVO
                if (isset($data['activo'])) {
                    $updateData['activo'] = $data['activo']; // Ya está normalizado (bool)
                } else {
                    $updateData['activo'] = $megaEvento->activo;
                }
                
                // Imágenes - siempre actualizar
                $updateData['imagenes'] = json_encode($imagenesFinales);
                
                // Fecha de actualización - siempre actualizar
                $updateData['fecha_actualizacion'] = now()->format('Y-m-d H:i:s');
                
                // ONG organizadora principal (si viene, actualizarla)
                if (isset($data['ong_organizadora_principal']) && !empty($data['ong_organizadora_principal'])) {
                    $updateData['ong_organizadora_principal'] = $data['ong_organizadora_principal'];
                }

                // 4. Log de datos que se van a guardar (para depuración)
                \Log::info('Mega Evento Update - Datos a guardar en DB', [
                    'mega_evento_id' => $megaEvento->mega_evento_id,
                    'campos' => array_keys($updateData),
                    'updateData' => $updateData,
                    'data_original' => $data
                ]);

                // 5. Preparar datos finales para la base de datos
                // Asegurar que los valores estén en el formato correcto para PostgreSQL
                $dbUpdateData = [];
                foreach ($updateData as $key => $value) {
                    // Convertir booleanos a true/false explícitos
                    if (is_bool($value)) {
                        $dbUpdateData[$key] = $value;
                    } 
                    // Mantener null como null
                    elseif ($value === null) {
                        $dbUpdateData[$key] = null;
                    }
                    // Convertir strings 'true'/'false'/'1'/'0' a booleanos para campos booleanos
                    elseif (in_array($key, ['es_publico', 'activo']) && is_string($value)) {
                        $dbUpdateData[$key] = in_array(strtolower($value), ['1', 'true', 'yes', 'on']);
                    }
                    // Mantener el resto de valores como están (ya están formateados)
                    else {
                        $dbUpdateData[$key] = $value;
                    }
                }
                
                \Log::info('Mega Evento Update - Datos finales para DB', [
                    'mega_evento_id' => $megaEvento->mega_evento_id,
                    'db_update_data' => $dbUpdateData,
                    'keys_count' => count($dbUpdateData),
                    'titulo' => $dbUpdateData['titulo'] ?? 'not_set',
                    'estado' => $dbUpdateData['estado'] ?? 'not_set',
                    'capacidad_maxima' => $dbUpdateData['capacidad_maxima'] ?? 'not_set'
                ]);
                
                // 6. Actualizar usando Eloquent directamente (más confiable)
                // Recargar el modelo primero para asegurar que tenemos los datos más recientes
                $megaEvento->refresh();
                
                // Actualizar campo por campo usando Eloquent
                foreach ($dbUpdateData as $key => $value) {
                    $megaEvento->$key = $value;
                }
                
                $saved = $megaEvento->save();
                
                \Log::info('Mega Evento Update - Resultado de Eloquent save', [
                    'saved' => $saved,
                    'wasChanged' => $megaEvento->wasChanged(),
                    'getChanges' => $megaEvento->getChanges(),
                    'mega_evento_id' => $megaEvento->mega_evento_id,
                    'update_data_count' => count($dbUpdateData)
                ]);
                
                if (!$saved) {
                    \Log::error('Mega Evento Update - ERROR CRÍTICO: No se pudo guardar con Eloquent', [
                        'mega_evento_id' => $megaEvento->mega_evento_id,
                        'update_data' => $dbUpdateData,
                        'model_errors' => $megaEvento->getErrors() ?? 'none'
                    ]);
                    
                    // Intentar con DB::table como fallback
                    $affectedRows = DB::table('mega_eventos')
                        ->where('mega_evento_id', $megaEvento->mega_evento_id)
                        ->update($dbUpdateData);
                    
                    if ($affectedRows === 0) {
                        throw new \Exception('No se pudieron actualizar los datos del mega evento en la base de datos');
                    }
                    
                    \Log::info('Mega Evento Update - ✅ Actualización exitosa con DB::table (fallback)', [
                        'affected_rows' => $affectedRows
                    ]);
                } else {
                    \Log::info('Mega Evento Update - ✅ Actualización exitosa con Eloquent', [
                        'changes' => $megaEvento->getChanges()
                    ]);
                }
                
                // 6. Verificar que se guardó correctamente consultando directamente la BD
                $megaEventoVerificado = DB::table('mega_eventos')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->first();
                
                if (!$megaEventoVerificado) {
                    \Log::error('Mega Evento Update - ERROR: No se encontró el mega evento después de actualizar');
                    throw new \Exception('Error al verificar la actualización del mega evento');
                }
                
                \Log::info('Mega Evento Update - Verificación desde BD', [
                    'mega_evento_id' => $megaEvento->mega_evento_id,
                    'titulo_bd' => $megaEventoVerificado->titulo ?? 'not_found',
                    'estado_bd' => $megaEventoVerificado->estado ?? 'not_found',
                    'capacidad_maxima_bd' => $megaEventoVerificado->capacidad_maxima ?? 'not_found',
                    'descripcion_bd' => $megaEventoVerificado->descripcion ?? 'not_found',
                    'categoria_bd' => $megaEventoVerificado->categoria ?? 'not_found',
                    'ubicacion_bd' => $megaEventoVerificado->ubicacion ?? 'not_found',
                    'fecha_actualizacion_bd' => $megaEventoVerificado->fecha_actualizacion ?? 'not_found'
                ]);

                // 7. Si se está finalizando el mega evento, crear notificación para la ONG
                if ($seEstaFinalizando && $megaEvento->ong_organizadora_principal) {
                    try {
                        Notificacion::create([
                            'ong_id' => $megaEvento->ong_organizadora_principal,
                            'evento_id' => null, // Para mega eventos no usamos evento_id
                            'externo_id' => null,
                            'tipo' => 'mega_evento_finalizado',
                            'titulo' => 'Mega evento finalizado',
                            'mensaje' => "Tu mega evento '{$updateData['titulo']}' ha sido marcado como finalizado. Ya no está disponible para nuevas participaciones o interacciones.",
                            'leida' => false,
                        ]);
                    } catch (\Throwable $e) {
                        \Log::error('Error al crear notificación de mega evento finalizado: ' . $e->getMessage());
                    }
                }
            }, 5); // Reintentar hasta 5 veces en caso de deadlock
            
            // Procesar patrocinadores si vienen en el request
            $patrocinadoresIds = [];
            if ($request->has('patrocinadores')) {
                $patrocinadoresRaw = $request->input('patrocinadores');
                if (is_string($patrocinadoresRaw)) {
                    $patrocinadoresIds = json_decode($patrocinadoresRaw, true) ?? [];
                } elseif (is_array($patrocinadoresRaw)) {
                    $patrocinadoresIds = $patrocinadoresRaw;
                }
            }

            // Si vienen patrocinadores, actualizar la tabla mega_evento_patrocinadores
            if (!empty($patrocinadoresIds) && is_array($patrocinadoresIds)) {
                // Eliminar patrocinadores existentes que no están en la nueva lista
                DB::table('mega_evento_patrocinadores')
                    ->where('mega_evento_id', $id)
                    ->whereNotIn('empresa_id', $patrocinadoresIds)
                    ->update(['activo' => false]);

                // Agregar nuevos patrocinadores o reactivar los existentes
                foreach ($patrocinadoresIds as $empresaId) {
                    try {
                        $empresaId = (int) $empresaId;
                        // Verificar que la empresa existe
                        $empresa = \App\Models\Empresa::where('user_id', $empresaId)->first();
                        if (!$empresa) {
                            \Log::warning("Empresa con user_id {$empresaId} no encontrada al actualizar patrocinador para mega evento {$id}");
                            continue;
                        }

                        // Verificar si ya existe
                        $existe = DB::table('mega_evento_patrocinadores')
                            ->where('mega_evento_id', $id)
                            ->where('empresa_id', $empresaId)
                            ->exists();

                        if ($existe) {
                            // Reactivar si estaba desactivado
                            DB::table('mega_evento_patrocinadores')
                                ->where('mega_evento_id', $id)
                                ->where('empresa_id', $empresaId)
                                ->update(['activo' => true]);
                        } else {
                            // Crear nuevo registro
                            DB::table('mega_evento_patrocinadores')->insert([
                                'mega_evento_id' => $id,
                                'empresa_id' => $empresaId,
                                'tipo_patrocinio' => null,
                                'monto_contribucion' => null,
                                'tipo_contribucion' => null,
                                'descripcion_contribucion' => null,
                                'fecha_compromiso' => now(),
                                'estado_compromiso' => 'confirmado',
                                'activo' => true,
                            ]);
                        }
                        \Log::info("Patrocinador {$empresaId} actualizado/agregado a mega evento {$id}");
                    } catch (\Throwable $e) {
                        \Log::error("Error actualizando patrocinador {$empresaId} al mega evento {$id}: " . $e->getMessage());
                        // Continuar con los demás patrocinadores aunque uno falle
                    }
                }
            }
            
            // Forzar refresh desde la base de datos para obtener los datos más recientes
            $megaEvento = MegaEvento::with('ongPrincipal')->findOrFail($id);
            $megaEvento->makeVisible('imagenes');
            $megaEvento->makeVisible('capacidad_maxima');
            
            // Asegurar que capacidad_maxima se devuelva correctamente
            $capacidadMaxima = $megaEvento->getRawOriginal('capacidad_maxima');
            if ($capacidadMaxima !== null && is_numeric($capacidadMaxima)) {
                $megaEvento->capacidad_maxima = (int) $capacidadMaxima;
            } else {
                $megaEvento->capacidad_maxima = null;
            }
            
            \Log::info('Mega Evento Update - Datos finales devueltos', [
                'mega_evento_id' => $megaEvento->mega_evento_id,
                'titulo' => $megaEvento->titulo,
                'estado' => $megaEvento->estado,
                'capacidad_maxima' => $megaEvento->capacidad_maxima,
                'descripcion' => $megaEvento->descripcion,
                'categoria' => $megaEvento->categoria
            ]);

            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'message' => 'Mega evento actualizado correctamente',
                'mega_evento' => $megaEvento
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            \Log::error('Mega Evento Update - Error completo', [
                'mega_evento_id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar mega evento: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
                // 1. Crear participación con ticket único
            DB::table('mega_evento_participantes_externos')->insert([
                'mega_evento_id' => $megaEventoId,
                'integrante_externo_id' => $integranteExterno->user_id,
                'estado_participacion' => 'aprobada', // Aprobación automática
                'fecha_registro' => now(),
                'activo' => true,
                // Generar código de ticket único para control de asistencia
                'ticket_codigo' => \Illuminate\Support\Str::uuid()->toString(),
                // Estado de asistencia por defecto
                'estado_asistencia' => 'no_asistido',
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
     * Cancelar participación en un mega evento (Usuario Externo)
     */
    public function cancelarParticipacion(Request $request, $megaEventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;
            
            // Buscar participación del usuario en este mega evento
            $participacion = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('externo_id', $externoId)
                ->first();
            
            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "No estás inscrito en este mega evento"
                ], 404);
            }
            
            // Eliminar participación
            DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('externo_id', $externoId)
                ->delete();
            
            return response()->json([
                "success" => true,
                "message" => "Participación cancelada exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al cancelar participación: " . $e->getMessage()
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

            $nombreCompleto = trim($participacion->nombres . ' ' . ($participacion->apellidos ?? ''));

            \App\Models\Notificacion::create([
                'ong_id' => $ongId,
                'evento_id' => null, // Los mega eventos no tienen evento_id
                'externo_id' => null, // Usuario no registrado
                'tipo' => 'participacion_mega_evento_publica',
                'titulo' => 'Nueva participación en tu mega evento',
                'mensaje' => "{$nombreCompleto} (usuario no registrado) se inscribió al mega evento \"{$megaEvento->titulo}\"",
                'leida' => false
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
                ->select('me.*', 'mep.estado_participacion', 'mep.fecha_registro', 'mep.tipo_participacion', 'mep.ticket_codigo', 'mep.estado_asistencia', 'mep.asistio', 'mep.checkin_at')
                ->orderByDesc('mep.fecha_registro')
                ->get();

            $megaEventos = [];
            foreach ($participaciones as $participacion) {
                $mega = MegaEvento::find($participacion->mega_evento_id);
                if ($mega) {
                    $mega->makeVisible('imagenes');
                    
                    // Si no tiene ticket_codigo, generarlo automáticamente (para usuarios que se inscribieron antes)
                    $ticketCodigo = $participacion->ticket_codigo;
                    if (!$ticketCodigo && $participacion->estado_participacion === 'aprobada') {
                        try {
                            $nuevoTicket = \Illuminate\Support\Str::uuid()->toString();
                            DB::table('mega_evento_participantes_externos')
                                ->where('mega_evento_id', $participacion->mega_evento_id)
                                ->where('integrante_externo_id', $integranteExterno->user_id)
                                ->update(['ticket_codigo' => $nuevoTicket]);
                            $ticketCodigo = $nuevoTicket;
                        } catch (\Exception $e) {
                            \Log::warning('Error generando ticket para mega evento: ' . $e->getMessage());
                        }
                    }
                    
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
                        'ticket_codigo' => $ticketCodigo,
                        'estado_asistencia' => $participacion->estado_asistencia ?? 'no_asistido',
                        'asistio' => $participacion->asistio ?? false,
                        'checkin_at' => $participacion->checkin_at,
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
            // Headers CORS para todas las respuestas
            $corsHeaders = [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin',
                'Access-Control-Max-Age' => '86400',
            ];
            
            $ahora = now();
            
            $query = MegaEvento::with('ongPrincipal')
                ->where('es_publico', true)
                ->where('activo', true)
                // Excluir mega eventos que ya finalizaron (fecha_fin < ahora)
                ->where(function($q) use ($ahora) {
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', $ahora);
                });
            
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
            
            // Obtener el origen de la petición para generar URLs correctas
            $origin = $request->header('Origin') 
                ?? $request->getSchemeAndHttpHost() 
                ?? env('PUBLIC_APP_URL', env('APP_URL', 'http://10.26.5.12:8000'));
            
            // Procesar imágenes para usar URLs completas
            foreach ($megaEventos as $mega) {
                $mega->makeVisible('imagenes');
                
                // Procesar imágenes para asegurar URLs completas
                if ($mega->imagenes && is_array($mega->imagenes)) {
                    $imagenesProcesadas = [];
                    foreach ($mega->imagenes as $imagen) {
                        if (empty($imagen) || !is_string($imagen)) continue;
                        
                        if (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0) {
                            // Si ya es una URL completa, verificar si necesita actualización
                            $parsedUrl = parse_url($imagen);
                            if (isset($parsedUrl['host']) && $parsedUrl['host'] !== parse_url($origin, PHP_URL_HOST)) {
                                // Reemplazar el host si es diferente
                                $parsedUrl['scheme'] = parse_url($origin, PHP_URL_SCHEME) ?? 'http';
                                $parsedUrl['host'] = parse_url($origin, PHP_URL_HOST);
                                $parsedUrl['port'] = parse_url($origin, PHP_URL_PORT);
                                $imagen = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] 
                                    . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') 
                                    . ($parsedUrl['path'] ?? '');
                            }
                            $imagenesProcesadas[] = $imagen;
                        } elseif (strpos($imagen, '/storage/') === 0) {
                            // Ruta relativa - construir URL con el origen actual
                            $imagenesProcesadas[] = rtrim($origin, '/') . $imagen;
                        } else {
                            // Ruta sin prefijo - agregar /storage/
                            $imagenesProcesadas[] = rtrim($origin, '/') . '/storage/' . ltrim($imagen, '/');
                        }
                    }
                    $mega->imagenes = $imagenesProcesadas;
                }
            }
            
            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventos,
                'count' => $megaEventos->count()
            ], 200, $corsHeaders);
        } catch (\Throwable $e) {
            \Log::error('Error en publicos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega eventos públicos: ' . $e->getMessage()
            ], 500, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin',
                'Access-Control-Max-Age' => '86400',
            ]);
        }
    }

    /**
     * Obtener mega eventos en curso (para ONG)
     */
    public function enCurso(Request $request)
    {
        try {
            $usuarioAutenticado = $request->user();
            
            if (!$usuarioAutenticado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            $ahora = now();
            
            $query = DB::table('mega_eventos')
                ->where('ong_organizadora_principal', $ongIdAutenticada)
                ->where('activo', true)
                // Mega eventos que están por iniciar o en curso
                ->where(function($q) use ($ahora) {
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', $ahora);
                })
                // Que ya hayan iniciado o estén por iniciar
                ->where(function($q) use ($ahora) {
                    $q->whereNull('fecha_inicio')
                      ->orWhere('fecha_inicio', '<=', $ahora->copy()->addHours(24)); // Incluir los que inician en las próximas 24 horas
                });
            
            // Filtro por categoría
            if ($request->has('categoria') && $request->categoria !== '' && $request->categoria !== 'todos') {
                $query->where('categoria', $request->categoria);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = trim($request->buscar);
                $buscarLower = '%' . strtolower($buscar) . '%';
                $query->where(function($q) use ($buscarLower) {
                    $q->whereRaw('LOWER(COALESCE(titulo::text, \'\')) LIKE ?', [$buscarLower])
                      ->orWhereRaw('LOWER(COALESCE(descripcion::text, \'\')) LIKE ?', [$buscarLower]);
                });
            }
            
            $megaEventos = $query->orderBy('fecha_inicio', 'asc')->get();
            
            // Procesar resultados
            $megaEventosArray = [];
            foreach ($megaEventos as $mega) {
                $megaData = [
                    'mega_evento_id' => (int) ($mega->mega_evento_id ?? 0),
                    'titulo' => $mega->titulo ?? '',
                    'descripcion' => $mega->descripcion ?? null,
                    'fecha_inicio' => $mega->fecha_inicio ? Carbon::parse($mega->fecha_inicio)->format('Y-m-d H:i:s') : null,
                    'fecha_fin' => $mega->fecha_fin ? Carbon::parse($mega->fecha_fin)->format('Y-m-d H:i:s') : null,
                    'categoria' => $mega->categoria ?? 'social',
                    'estado' => $mega->estado ?? 'planificacion',
                    'imagenes' => $mega->imagenes ? (is_string($mega->imagenes) ? json_decode($mega->imagenes, true) : $mega->imagenes) : []
                ];
                $megaEventosArray[] = $megaData;
            }
            
            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventosArray,
                'count' => count($megaEventosArray)
            ]);
            
        } catch (\Throwable $e) {
            \Log::error('Error en enCurso mega eventos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega eventos en curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener mega eventos finalizados (para ONG)
     */
    public function finalizados(Request $request)
    {
        try {
            $usuarioAutenticado = $request->user();
            
            if (!$usuarioAutenticado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $ongIdAutenticada = (int) $usuarioAutenticado->id_usuario;
            $ahora = now();
            
            $query = DB::table('mega_eventos')
                ->where('ong_organizadora_principal', $ongIdAutenticada)
                // Mega eventos que ya finalizaron
                ->where(function($q) use ($ahora) {
                    $q->whereNotNull('fecha_fin')
                      ->where('fecha_fin', '<', $ahora);
                });
            
            // Filtro por categoría
            if ($request->has('categoria') && $request->categoria !== '' && $request->categoria !== 'todos') {
                $query->where('categoria', $request->categoria);
            }
            
            // Búsqueda por título o descripción
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = trim($request->buscar);
                $buscarLower = '%' . strtolower($buscar) . '%';
                $query->where(function($q) use ($buscarLower) {
                    $q->whereRaw('LOWER(COALESCE(titulo::text, \'\')) LIKE ?', [$buscarLower])
                      ->orWhereRaw('LOWER(COALESCE(descripcion::text, \'\')) LIKE ?', [$buscarLower]);
                });
            }
            
            $megaEventos = $query->orderBy('fecha_fin', 'desc')->get();
            
            // Procesar resultados
            $megaEventosArray = [];
            foreach ($megaEventos as $mega) {
                $megaData = [
                    'mega_evento_id' => (int) ($mega->mega_evento_id ?? 0),
                    'titulo' => $mega->titulo ?? '',
                    'descripcion' => $mega->descripcion ?? null,
                    'fecha_inicio' => $mega->fecha_inicio ? Carbon::parse($mega->fecha_inicio)->format('Y-m-d H:i:s') : null,
                    'fecha_fin' => $mega->fecha_fin ? Carbon::parse($mega->fecha_fin)->format('Y-m-d H:i:s') : null,
                    'categoria' => $mega->categoria ?? 'social',
                    'estado' => $mega->estado ?? 'finalizado',
                    'imagenes' => $mega->imagenes ? (is_string($mega->imagenes) ? json_decode($mega->imagenes, true) : $mega->imagenes) : []
                ];
                $megaEventosArray[] = $megaData;
            }
            
            return response()->json([
                'success' => true,
                'mega_eventos' => $megaEventosArray,
                'count' => count($megaEventosArray)
            ]);
            
        } catch (\Throwable $e) {
            \Log::error('Error en finalizados mega eventos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mega eventos finalizados: ' . $e->getMessage()
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
                'tipo' => 'participacion_mega_evento',
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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
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

            // Preparar datos para JSON
            $datos = [
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
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
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

            // Preparar datos para JSON
            $datos = [
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
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
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

            $participantesRegistrados = $queryRegistrados->get()
                ->map(function($participante) {
                    // Construir nombre completo desde nombres y apellidos
                    $nombres = trim($participante->nombres ?? '');
                    $apellidos = trim($participante->apellidos ?? '');
                    $nombreCompleto = trim($nombres . ' ' . $apellidos);
                    
                    // Si no hay nombre completo, usar nombre_usuario como fallback
                    $nombreFinal = !empty($nombreCompleto) ? $nombreCompleto : ($participante->nombre_usuario ?? 'Usuario');
                    
                    // Si el nombre_usuario es muy corto (como "U"), intentar usar nombres o email
                    if (strlen($nombreFinal) <= 2 && !empty($nombres)) {
                        $nombreFinal = $nombres;
                    }
                    
                    // Obtener avatar/foto de perfil
                    $fotoPerfil = null;
                    if ($participante->foto_perfil) {
                        $fotoPerfil = $participante->foto_perfil;
                        if (!str_starts_with($fotoPerfil, 'http')) {
                            $fotoPerfil = asset('storage/' . $fotoPerfil);
                        }
                    }
                    
                    return [
                        'id' => $participante->id,
                        'integrante_externo_id' => $participante->integrante_externo_id,
                        'fecha_registro' => $participante->fecha_registro,
                        'estado' => $participante->estado,
                        'estado_participacion' => $participante->estado,
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'nombre_completo' => $nombreFinal,
                        'nombre_usuario' => $participante->nombre_usuario ?? 'Usuario',
                        'email' => $participante->email ?? '—',
                        'telefono' => $participante->telefono ?? '—',
                        'foto_perfil' => $fotoPerfil,
                        'avatar' => $fotoPerfil, // Alias para compatibilidad
                        'tipo' => 'registrado'
                    ];
                });

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

            $participantesNoRegistrados = $queryNoRegistrados->get()
                ->map(function($participante) {
                    // Construir nombre completo desde nombres y apellidos
                    $nombres = trim($participante->nombres ?? '');
                    $apellidos = trim($participante->apellidos ?? '');
                    $nombreCompleto = trim($nombres . ' ' . $apellidos);
                    
                    // Si no hay nombre completo, usar "Usuario" como fallback
                    $nombreFinal = !empty($nombreCompleto) ? $nombreCompleto : 'Usuario';
                    
                    return [
                        'id' => $participante->id,
                        'integrante_externo_id' => null,
                        'fecha_registro' => $participante->fecha_registro,
                        'estado' => $participante->estado,
                        'estado_participacion' => $participante->estado,
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'nombre_completo' => $nombreFinal,
                        'nombre_usuario' => null,
                        'email' => $participante->email ?? '—',
                        'telefono' => $participante->telefono ?? '—',
                        'foto_perfil' => null,
                        'avatar' => null,
                        'tipo' => 'no_registrado'
                    ];
                });

            // Combinar ambos tipos de participantes
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados)
                ->sortByDesc('fecha_registro')
                ->values();

            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'participantes' => $participantes,
                'count' => $participantes->count()
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
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

            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'historial' => $historial,
                'total_registros' => count($historial)
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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

    /**
     * Obtener control de asistencia completo para ONG
     */
    public function controlAsistencia(Request $request, $id)
    {
        try {
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            $ongId = $request->user()->id_usuario;
            
            $megaEvento = MegaEvento::find($id);
            if (!$megaEvento) {
                return response()->json([
                    "success" => false,
                    "error" => "Mega evento no encontrado"
                ], 404);
            }

            // Verificar que el usuario autenticado es la ONG propietaria
            if ($megaEvento->ong_organizadora_principal != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para ver el control de asistencia de este mega evento"
                ], 403);
            }

            // Verificar qué columnas existen en la tabla
            $columnas = [];
            try {
                $columnasExistentes = DB::select("
                    SELECT column_name 
                    FROM information_schema.columns 
                    WHERE table_name = 'mega_evento_participantes_externos'
                ");
                $columnas = array_map(function($col) { return $col->column_name; }, $columnasExistentes);
            } catch (\Exception $e) {
                // Si falla la consulta, asumir que las columnas no existen
                \Log::warning('Error verificando columnas de mega_evento_participantes_externos: ' . $e->getMessage());
            }
            
            $tieneAsistio = in_array('asistio', $columnas);
            $tieneEstadoAsistencia = in_array('estado_asistencia', $columnas);
            $tieneModoAsistencia = in_array('modo_asistencia', $columnas);
            $tieneCheckinAt = in_array('checkin_at', $columnas);
            $tieneObservaciones = in_array('observaciones', $columnas);
            $tieneComentarioAsistencia = in_array('comentario_asistencia', $columnas);
            $tieneIpRegistro = in_array('ip_registro', $columnas);
            $tieneTicketCodigo = in_array('ticket_codigo', $columnas);
            $tieneRegistradoPor = in_array('registrado_por', $columnas);

            // Construir SELECT dinámicamente
            $selects = [
                DB::raw("(mep.mega_evento_id::text || '-' || mep.integrante_externo_id::text) as id"),
                'mep.integrante_externo_id',
                    'ie.nombres',
                    'ie.apellidos',
                    'ie.email',
                    'ie.phone_number as telefono',
                'u.nombre_usuario',
                'u.foto_perfil',
                'mep.fecha_registro',
            ];
            
            if ($tieneAsistio) {
                $selects[] = 'mep.asistio';
                    } else {
                $selects[] = DB::raw("false as asistio");
                    }
                    
            if ($tieneEstadoAsistencia) {
                $selects[] = 'mep.estado_asistencia';
            } else {
                $selects[] = DB::raw("'no_asistido' as estado_asistencia");
            }
            
            if ($tieneModoAsistencia) {
                $selects[] = 'mep.modo_asistencia';
            } else {
                $selects[] = DB::raw("NULL as modo_asistencia");
                        }
            
            if ($tieneCheckinAt) {
                $selects[] = 'mep.checkin_at';
            } else {
                $selects[] = DB::raw("NULL as checkin_at");
            }
            
            if ($tieneObservaciones) {
                $selects[] = 'mep.observaciones';
            } else {
                $selects[] = DB::raw("NULL as observaciones");
            }
            
            if ($tieneComentarioAsistencia) {
                $selects[] = 'mep.comentario_asistencia';
            } else {
                $selects[] = DB::raw("NULL as comentario_asistencia");
            }
            
            if ($tieneIpRegistro) {
                $selects[] = 'mep.ip_registro';
                        } else {
                $selects[] = DB::raw("NULL as ip_registro");
            }
            
            if ($tieneTicketCodigo) {
                $selects[] = 'mep.ticket_codigo';
            } else {
                $selects[] = DB::raw("NULL as ticket_codigo");
            }
            
            if ($tieneRegistradoPor) {
                $selects[] = 'rp.nombre_usuario as registrado_por_nombre';
            } else {
                $selects[] = DB::raw("NULL as registrado_por_nombre");
            }
            
            $selects[] = DB::raw("'registrado' as tipo");

            // Obtener participantes registrados
            $query = DB::table('mega_evento_participantes_externos as mep')
                ->join('integrantes_externos as ie', 'mep.integrante_externo_id', '=', 'ie.user_id')
                ->join('usuarios as u', 'ie.user_id', '=', 'u.id_usuario');
            
            if ($tieneRegistradoPor) {
                $query->leftJoin('usuarios as rp', 'mep.registrado_por', '=', 'rp.id_usuario');
            }
            
            $participantesRegistrados = $query
                ->where('mep.mega_evento_id', $id)
                ->where('mep.activo', true)
                ->where('mep.estado_participacion', 'aprobada')
                ->select($selects)
                ->get()
                ->map(function($participacion) {
                    // Construir nombre completo desde nombres y apellidos
                    $nombres = trim($participacion->nombres ?? '');
                    $apellidos = trim($participacion->apellidos ?? '');
                    $nombreCompleto = trim($nombres . ' ' . $apellidos);
                    
                    // Si no hay nombre completo, usar nombre_usuario como fallback
                    $nombreFinal = !empty($nombreCompleto) ? $nombreCompleto : ($participacion->nombre_usuario ?? 'Usuario');
                    
                    // Si el nombre_usuario es muy corto (como "U"), intentar usar nombres o email
                    if (strlen($nombreFinal) <= 2 && !empty($nombres)) {
                        $nombreFinal = $nombres;
                    }
                    
                    // Estado de asistencia formateado
                    $asistio = isset($participacion->asistio) ? (bool)$participacion->asistio : false;
                    $estadoAsistenciaRaw = $participacion->estado_asistencia ?? ($asistio ? 'asistido' : 'no_asistido');
                    $estadoAsistencia = ($estadoAsistenciaRaw === 'asistido' || $asistio) ? '✅ Asistió' : '❌ No asistió';

                    // Obtener avatar/foto de perfil
                    $fotoPerfil = null;
                    if ($participacion->foto_perfil) {
                        $fotoPerfil = $participacion->foto_perfil;
                        if (!str_starts_with($fotoPerfil, 'http')) {
                            $fotoPerfil = asset('storage/' . $fotoPerfil);
                        }
                    }
                    
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'registrado',
                        'participante' => $nombreFinal,
                        'nombre_completo' => $nombreFinal,
                        'nombre_usuario' => $participacion->nombre_usuario ?? 'Usuario',
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'email' => $participacion->email ?? '—',
                        'telefono' => $participacion->telefono ?? '—',
                        'fecha_inscripcion' => $participacion->fecha_registro ? \Carbon\Carbon::parse($participacion->fecha_registro)->setTimezone(config('app.timezone'))->format('d/m/Y - H:i') : '—',
                        'estado_asistencia' => $estadoAsistencia,
                        'estado_asistencia_raw' => $estadoAsistenciaRaw,
                        'validado_por' => $participacion->registrado_por_nombre ?? '—',
                        'observaciones' => $participacion->observaciones ?? '-',
                        'comentario_asistencia' => $participacion->comentario_asistencia ?? '-',
                        'fecha_registro_asistencia' => ($participacion->checkin_at) ? \Carbon\Carbon::parse($participacion->checkin_at)->setTimezone(config('app.timezone'))->format('d/m/Y H:i') : null,
                        'modo_asistencia' => $participacion->modo_asistencia ?? null,
                        'asistio' => $asistio,
                        'ticket_codigo' => $participacion->ticket_codigo ?? null,
                        'foto_perfil' => $fotoPerfil,
                        'avatar' => $fotoPerfil // Alias para compatibilidad
                    ];
                });

            // Obtener participantes no registrados
            $participantesNoRegistrados = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $id)
                ->where('estado', '!=', 'rechazada')
                ->get()
                ->map(function($participacion) {
                    // Manejar campos que pueden no existir
                    $asistio = isset($participacion->asistio) ? (bool)$participacion->asistio : false;
                    $estadoAsistenciaRaw = $participacion->estado_asistencia ?? ($asistio ? 'asistido' : 'no_asistido');
                    $estadoAsistencia = ($asistio || $estadoAsistenciaRaw === 'asistido') ? '✅ Asistió' : '❌ No asistió';
                    
                    return [
                        'id' => $participacion->id,
                        'tipo' => 'voluntario',
                        'tipo_usuario' => 'Voluntario',
                        'participante' => trim($participacion->nombres . ' ' . ($participacion->apellidos ?? '')),
                        'email' => $participacion->email ?? '—',
                        'telefono' => $participacion->telefono ?? '—',
                        'fecha_inscripcion' => $participacion->created_at ? \Carbon\Carbon::parse($participacion->created_at)->setTimezone(config('app.timezone'))->format('d/m/Y - H:i') : '—',
                        'estado_asistencia' => $estadoAsistencia,
                        'estado_asistencia_raw' => $estadoAsistenciaRaw,
                        'validado_por' => '—',
                        'observaciones' => (isset($participacion->observaciones) && $participacion->observaciones) ? $participacion->observaciones : '-',
                        'comentario_asistencia' => (isset($participacion->comentario_asistencia) && $participacion->comentario_asistencia) ? $participacion->comentario_asistencia : '-',
                        'fecha_registro_asistencia' => (isset($participacion->checkin_at) && $participacion->checkin_at) ? \Carbon\Carbon::parse($participacion->checkin_at)->setTimezone(config('app.timezone'))->format('d/m/Y H:i') : null,
                        'modo_asistencia' => isset($participacion->modo_asistencia) ? $participacion->modo_asistencia : null,
                        'asistio' => $asistio,
                        'ticket_codigo' => isset($participacion->ticket_codigo) ? $participacion->ticket_codigo : null,
                        'foto_perfil' => null
                    ];
                });

            // Combinar ambos tipos
            $participantes = $participantesRegistrados->concat($participantesNoRegistrados);

            // Preparar datos para JSON
            $datos = [
                "success" => true,
                "mega_evento" => [
                    'id' => $megaEvento->mega_evento_id,
                    'titulo' => $megaEvento->titulo,
                    'estado' => $megaEvento->estado,
                ],
                "participantes" => $participantes,
                "total" => $participantes->count(),
                "asistieron" => $participantes->where('estado_asistencia_raw', 'asistido')->count(),
                "no_asistieron" => $participantes->where('estado_asistencia_raw', 'no_asistido')->count(),
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al obtener control de asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modificar estado de asistencia por ONG
     */
    public function modificarAsistencia(Request $request, $participacionId, $tipo = 'registrado')
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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
            
            // Buscar según el tipo
            if ($tipo === 'registrado') {
                // Parsear el ID compuesto (mega_evento_id-integrante_externo_id)
                $ids = explode('-', $participacionId);
                if (count($ids) !== 2) {
                    return response()->json([
                        "success" => false,
                        "error" => "ID de participación inválido"
                    ], 400);
                }
                
                $megaEventoId = $ids[0];
                $integranteExternoId = $ids[1];
                
                $participacion = DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEventoId)
                    ->where('integrante_externo_id', $integranteExternoId)
                    ->first();
                
                if (!$participacion) {
                    return response()->json([
                        "success" => false,
                        "error" => "Participación no encontrada"
                    ], 404);
                }
                
                // Verificar que el mega evento pertenece a la ONG
                $megaEvento = MegaEvento::find($megaEventoId);
                if (!$megaEvento || $megaEvento->ong_organizadora_principal != $ongId) {
                    return response()->json([
                        "success" => false,
                        "error" => "No tienes permiso para modificar esta asistencia"
                    ], 403);
                }
                
                $estadoAsistencia = $request->input('estado_asistencia');
                $observaciones = $request->input('observaciones');
                
                $updateData = [
                    'estado_asistencia' => $estadoAsistencia,
                    'asistio' => ($estadoAsistencia === 'asistido'),
                    'observaciones' => $observaciones,
                    'registrado_por' => $ongId,
                ];
                
                if ($estadoAsistencia === 'asistido' && !$participacion->checkin_at) {
                    $updateData['checkin_at'] = now();
                } elseif ($estadoAsistencia === 'no_asistido') {
                    $updateData['checkin_at'] = null;
                }
                
                DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEventoId)
                    ->where('integrante_externo_id', $integranteExternoId)
                    ->update($updateData);
                    
                        } else {
                // Participante no registrado
                $participacion = \App\Models\MegaEventoParticipanteNoRegistrado::find($participacionId);
                
                if (!$participacion) {
                    return response()->json([
                        "success" => false,
                        "error" => "Participación no encontrada"
                    ], 404);
                }
                
                // Verificar que el mega evento pertenece a la ONG
                $megaEvento = MegaEvento::find($participacion->mega_evento_id);
                if (!$megaEvento || $megaEvento->ong_organizadora_principal != $ongId) {
                    return response()->json([
                        "success" => false,
                        "error" => "No tienes permiso para modificar esta asistencia"
                    ], 403);
                }
                
                $estadoAsistencia = $request->input('estado_asistencia');
                $observaciones = $request->input('observaciones');
                
                $participacion->estado_asistencia = $estadoAsistencia;
                $participacion->asistio = ($estadoAsistencia === 'asistido');
                $participacion->observaciones = $observaciones;
                $participacion->registrado_por = $ongId;
                
                if ($estadoAsistencia === 'asistido' && !$participacion->checkin_at) {
                    $participacion->checkin_at = now();
                } elseif ($estadoAsistencia === 'no_asistido') {
                    $participacion->checkin_at = null;
                            }
                
                $participacion->save();
            }

            return response()->json([
                "success" => true,
                "message" => "Asistencia modificada correctamente"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al modificar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar asistencia por ticket o manualmente
     */
    public function registrarAsistencia(Request $request, $megaEventoId)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'ticket_codigo' => 'nullable|string|max:100',
                'participacion_id' => 'nullable|integer',
                'tipo' => 'required|string|in:registrado,no_registrado',
                'observaciones' => 'nullable|string|max:500',
                'modo_asistencia' => 'nullable|string|in:QR,Manual,Online,Confirmacion',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "error" => "Datos inválidos",
                    "details" => $validator->errors(),
                ], 422);
            }

            $ongId = $request->user()->id_usuario;
            
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    "success" => false,
                    "error" => "Mega evento no encontrado"
                ], 404);
            }

            // Verificar permisos
            if ($megaEvento->ong_organizadora_principal != $ongId) {
                return response()->json([
                    "success" => false,
                    "error" => "No tienes permiso para registrar asistencia en este mega evento"
                ], 403);
            }

            $ticketCodigo = $request->input('ticket_codigo');
            $participacionId = $request->input('participacion_id');
            $tipo = $request->input('tipo');
            $observaciones = $request->input('observaciones');
            $modoAsistencia = $request->input('modo_asistencia', 'Manual');

            if ($tipo === 'registrado') {
                // Buscar por ticket o por ID
                if ($ticketCodigo) {
                    $participacion = DB::table('mega_evento_participantes_externos')
                        ->where('mega_evento_id', $megaEventoId)
                        ->where('ticket_codigo', $ticketCodigo)
                        ->first();
                } elseif ($participacionId) {
                    $ids = explode('-', $participacionId);
                    if (count($ids) === 2) {
                        $participacion = DB::table('mega_evento_participantes_externos')
                            ->where('mega_evento_id', $ids[0])
                            ->where('integrante_externo_id', $ids[1])
                            ->first();
                    }
                }

                if (!$participacion) {
                    return response()->json([
                        "success" => false,
                        "error" => "Participación no encontrada"
                    ], 404);
                }

                // Verificar que esté aprobada
                if ($participacion->estado_participacion !== 'aprobada') {
                    return response()->json([
                        "success" => false,
                        "error" => "La participación debe estar aprobada para registrar asistencia"
                    ], 400);
                }

                // Verificar que no haya marcado asistencia previamente
                if ($participacion->asistio && empty($observaciones)) {
                    return response()->json([
                        "success" => false,
                        "error" => "Este participante ya tiene asistencia registrada"
                    ], 409);
                }

                $updateData = [
                    'asistio' => true,
                    'estado_asistencia' => 'asistido',
                    'modo_asistencia' => $modoAsistencia,
                    'observaciones' => $observaciones,
                    'registrado_por' => $ongId,
                    'checkin_at' => now(),
                    'ip_registro' => $request->ip(),
                ];

                if (!$participacion->ticket_codigo && $ticketCodigo) {
                    $updateData['ticket_codigo'] = $ticketCodigo;
                }

                DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $participacion->mega_evento_id)
                    ->where('integrante_externo_id', $participacion->integrante_externo_id)
                    ->update($updateData);

            } else {
                // Participante no registrado
                if ($ticketCodigo) {
                    $participacion = \App\Models\MegaEventoParticipanteNoRegistrado::where('mega_evento_id', $megaEventoId)
                        ->where('ticket_codigo', $ticketCodigo)
                        ->first();
                } elseif ($participacionId) {
                    $participacion = \App\Models\MegaEventoParticipanteNoRegistrado::find($participacionId);
                }

                if (!$participacion) {
            return response()->json([
                        "success" => false,
                        "error" => "Participación no encontrada"
                    ], 404);
                }

                // Verificar que esté aprobada
                if ($participacion->estado !== 'aprobada') {
                    return response()->json([
                        "success" => false,
                        "error" => "La participación debe estar aprobada para registrar asistencia"
                    ], 400);
                }

                // Verificar que no haya marcado asistencia previamente
                if ($participacion->asistio && empty($observaciones)) {
                    return response()->json([
                        "success" => false,
                        "error" => "Este participante ya tiene asistencia registrada"
                    ], 409);
                }

                $participacion->asistio = true;
                $participacion->estado_asistencia = 'asistido';
                $participacion->modo_asistencia = $modoAsistencia;
                $participacion->observaciones = $observaciones;
                $participacion->registrado_por = $ongId;
                $participacion->checkin_at = now();
                $participacion->ip_registro = $request->ip();

                if (!$participacion->ticket_codigo && $ticketCodigo) {
                    $participacion->ticket_codigo = $ticketCodigo;
                }

                $participacion->save();
            }

            return response()->json([
                "success" => true,
                "message" => "Asistencia registrada correctamente"
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => "Error al registrar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Usuario externo: marcar su propia asistencia en mega evento
     */
    public function marcarAsistenciaUsuario(Request $request, $megaEventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;
            
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    "success" => false,
                    "error" => "Mega evento no encontrado"
                ], 404);
            }

            // Validar que el mega evento esté en curso (fecha_inicio <= ahora <= fecha_fin)
            $ahora = now();
            $fechaInicio = $megaEvento->fecha_inicio ? \Carbon\Carbon::parse($megaEvento->fecha_inicio) : null;
            $fechaFin = $megaEvento->fecha_fin ? \Carbon\Carbon::parse($megaEvento->fecha_fin) : null;
            
            // El evento debe haber iniciado
            if (!$fechaInicio || $ahora->lessThan($fechaInicio)) {
                return response()->json([
                    "success" => false,
                    "error" => "Este mega evento aún no ha comenzado. Solo puedes registrar asistencia cuando el evento esté en curso."
                ], 400);
            }
            
            // El evento no debe haber finalizado (si tiene fecha_fin)
            if ($fechaFin && $ahora->greaterThan($fechaFin)) {
                return response()->json([
                    "success" => false,
                    "error" => "Este mega evento ya finalizó. Solo puedes registrar asistencia durante el evento."
                ], 400);
            }

            // CRÍTICO: El usuario externo solo puede registrar su propia asistencia
            // Buscar la participación del usuario autenticado (no por ticket, solo por su ID)
            // El integrante_externo_id en la tabla mega_evento_participantes_externos
            // hace referencia a user_id de integrantes_externos, que es igual a id_usuario de usuarios
            
            // Primero intentar buscar directamente por externoId (que es id_usuario)
            $participacion = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('integrante_externo_id', $externoId) // SOLO busca por el ID del usuario autenticado
                ->where('activo', true)
                ->first();
            
            // Si no se encuentra, verificar si existe IntegranteExterno y buscar
            if (!$participacion) {
                $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
                if ($integranteExterno) {
                    // Buscar usando el user_id del IntegranteExterno (que debería ser igual a externoId)
                    $participacion = DB::table('mega_evento_participantes_externos')
                        ->where('mega_evento_id', $megaEventoId)
                        ->where('integrante_externo_id', $integranteExterno->user_id) // SOLO busca por el ID del usuario autenticado
                        ->where('activo', true)
                        ->first();
                }
            }

            if (!$participacion) {
                // Log detallado para debug
                $todasLasParticipaciones = DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEventoId)
                    ->get(['integrante_externo_id', 'activo', 'estado_participacion']);
                
                \Log::warning('Usuario no encontrado en mega evento', [
                    'externo_id' => $externoId,
                    'mega_evento_id' => $megaEventoId,
                    'tiene_integrante_externo' => \App\Models\IntegranteExterno::where('user_id', $externoId)->exists(),
                    'participaciones_existentes' => $todasLasParticipaciones->toArray(),
                    'total_participaciones' => $todasLasParticipaciones->count()
                ]);
                
                return response()->json([
                    "success" => false,
                    "error" => "No estás inscrito en este mega evento. Por favor, verifica que tu inscripción haya sido aprobada."
                ], 404);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado_participacion !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este mega evento aún no ha sido aprobada. Solo puedes registrar asistencia si tu inscripción fue aprobada previamente."
                ], 400);
            }

            // Verificar que no haya marcado asistencia previamente
            if ($participacion->asistio && $participacion->estado_asistencia === 'asistido') {
                return response()->json([
                    "success" => false,
                    "error" => "Ya marcaste tu asistencia para este mega evento"
                ], 409);
            }
            
            // VALIDACIÓN CRÍTICA: Asegurar que el usuario solo puede registrar su propia asistencia
            // No se permite usar tickets/QR de otros usuarios - solo se busca por integrante_externo_id del usuario autenticado
            // Esta validación ya está garantizada porque solo buscamos por externoId, no por ticket_codigo

            // Obtener IP del usuario
            $ipRegistro = $request->ip();
            
            // Obtener el integrante_externo_id de la participación encontrada
            $integranteExternoId = is_object($participacion) 
                ? ($participacion->integrante_externo_id ?? $externoId)
                : (is_array($participacion) ? ($participacion['integrante_externo_id'] ?? $externoId) : $externoId);
            
            // La tabla tiene clave primaria compuesta, así que usamos ambos campos para actualizar
            DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEventoId)
                ->where('integrante_externo_id', $integranteExternoId)
                ->update([
                    'asistio' => true,
                    'estado_asistencia' => 'asistido',
                    'modo_asistencia' => 'Confirmacion',
                    'checkin_at' => now(),
                    'ip_registro' => $ipRegistro,
                    'registrado_por' => $externoId, // El usuario se auto-registra
                ]);

            return response()->json([
                "success" => true,
                "message" => "¡Gracias por participar! Tu asistencia fue registrada correctamente.",
                "data" => [
                    'mega_evento_id' => $megaEvento->id,
                    'mega_evento_titulo' => $megaEvento->titulo,
                    'fecha_registro' => now()->setTimezone(config('app.timezone'))->format('d/m/Y H:i'),
                    'estado_asistencia' => 'asistido',
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error marcando asistencia usuario en mega evento: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al marcar asistencia: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar notificaciones automáticas 5 minutos antes del inicio del mega evento
     * Este método debe ser llamado por un comando programado (Laravel Scheduler)
     */
    public function enviarNotificaciones5Minutos()
    {
        try {
            $ahora = now();
            $en5Minutos = $ahora->copy()->addMinutes(5);
            $en6Minutos = $ahora->copy()->addMinutes(6);
            
            // Obtener mega eventos que inician entre 5 y 6 minutos desde ahora
            $megaEventos = DB::table('mega_eventos')
                ->where('activo', true)
                ->whereNotNull('fecha_inicio')
                ->whereBetween('fecha_inicio', [$en5Minutos->format('Y-m-d H:i:s'), $en6Minutos->format('Y-m-d H:i:s')])
                ->get();
            
            $notificacionesEnviadas = 0;
            
            foreach ($megaEventos as $megaEvento) {
                try {
                    $megaEventoModel = MegaEvento::find($megaEvento->mega_evento_id);
                    if (!$megaEventoModel) continue;
                    
                    // Obtener todos los participantes aprobados
                    $participantes = DB::table('mega_evento_participantes_externos')
                        ->where('mega_evento_id', $megaEvento->mega_evento_id)
                        ->where('estado_participacion', 'aprobada')
                        ->where('activo', true)
                        ->get();
                    
                    // Enviar notificación a cada participante
                    foreach ($participantes as $participante) {
                        // Verificar si ya se envió la notificación (evitar duplicados)
                        $notificacionExistente = DB::table('notificaciones')
                            ->where('externo_id', $participante->integrante_externo_id)
                            ->where('tipo', 'alerta_mega_evento_5min')
                            ->whereRaw("mensaje LIKE ?", ["%mega evento \"{$megaEvento->titulo}\"%"])
                            ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                            ->exists();
                        
                        if (!$notificacionExistente) {
                            Notificacion::create([
                                'ong_id' => $megaEvento->ong_organizadora_principal,
                                'evento_id' => null,
                                'externo_id' => $participante->integrante_externo_id,
                                'tipo' => 'alerta_mega_evento_5min',
                                'titulo' => '¡Mega Evento por comenzar!',
                                'mensaje' => "El mega evento \"{$megaEvento->titulo}\" iniciará en 5 minutos. ¡Prepárate!",
                                'leida' => false
                            ]);
                            $notificacionesEnviadas++;
                        }
                    }
                    
                    // Obtener patrocinadores (empresas que patrocinan este mega evento)
                    $patrocinadores = DB::table('mega_evento_patrocinadores')
                        ->where('mega_evento_id', $megaEvento->mega_evento_id)
                        ->where('activo', true)
                        ->get();
                    
                    // Enviar notificación a cada patrocinador
                    foreach ($patrocinadores as $patrocinador) {
                        $empresa = \App\Models\Empresa::find($patrocinador->empresa_id);
                        if ($empresa && $empresa->user_id) {
                            // Verificar si ya se envió la notificación
                            $notificacionExistente = DB::table('notificaciones')
                                ->where('empresa_id', $empresa->empresa_id)
                                ->where('tipo', 'alerta_mega_evento_5min')
                                ->whereRaw("mensaje LIKE ?", ["%mega evento \"{$megaEvento->titulo}\"%"])
                                ->where('created_at', '>=', $ahora->copy()->subMinutes(10))
                                ->exists();
                            
                            if (!$notificacionExistente) {
                                Notificacion::create([
                                    'ong_id' => $megaEvento->ong_organizadora_principal,
                                    'evento_id' => null,
                                    'empresa_id' => $empresa->empresa_id,
                                    'tipo' => 'alerta_mega_evento_5min',
                                    'titulo' => '¡Mega Evento por comenzar!',
                                    'mensaje' => "El mega evento \"{$megaEvento->titulo}\" que patrocinas iniciará en 5 minutos.",
                                    'leida' => false
                                ]);
                                $notificacionesEnviadas++;
                            }
                        }
                    }
                    
                } catch (\Throwable $e) {
                    \Log::error("Error enviando notificaciones para mega evento {$megaEvento->mega_evento_id}: " . $e->getMessage());
                }
            }
            
            \Log::info("Notificaciones de mega eventos enviadas", [
                'mega_eventos_procesados' => $megaEventos->count(),
                'notificaciones_enviadas' => $notificacionesEnviadas
            ]);
            
            return $notificacionesEnviadas;
            
        } catch (\Throwable $e) {
            \Log::error('Error en enviarNotificaciones5Minutos: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener mega eventos que inician en 5 minutos (para alertas)
     */
    public function alertas5Minutos(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;
            $ahora = now();
            $en5Minutos = $ahora->copy()->addMinutes(5);
            $en6Minutos = $ahora->copy()->addMinutes(6);

            // Obtener integrante externo
            $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
            if (!$integranteExterno) {
                return response()->json([
                    "success" => true,
                    "mega_eventos" => []
                ]);
            }

            // Obtener mega eventos en los que está inscrito y que inician entre ahora y 6 minutos
            $participaciones = DB::table('mega_evento_participantes_externos as mep')
                ->join('mega_eventos as me', 'mep.mega_evento_id', '=', 'me.mega_evento_id')
                ->where('mep.integrante_externo_id', $integranteExterno->user_id)
                ->where('mep.estado_participacion', 'aprobada')
                ->where('mep.activo', true)
                ->where(function($query) {
                    $query->whereNull('mep.asistio')
                          ->orWhere('mep.asistio', false)
                          ->orWhere('mep.estado_asistencia', '!=', 'asistido');
                })
                ->select('me.mega_evento_id', 'me.titulo', 'me.fecha_inicio', 'me.fecha_fin')
                ->get()
                ->filter(function($participacion) use ($ahora, $en5Minutos, $en6Minutos) {
                    if (!$participacion->fecha_inicio) {
                        return false;
                    }
                    
                    $fechaInicio = \Carbon\Carbon::parse($participacion->fecha_inicio);
                    
                    // El mega evento debe iniciar entre ahora y 6 minutos
                    return $fechaInicio->greaterThanOrEqualTo($ahora) && 
                           $fechaInicio->lessThanOrEqualTo($en6Minutos);
                })
                ->map(function($participacion) {
                    $fechaInicio = \Carbon\Carbon::parse($participacion->fecha_inicio);
                    $ahora = now();
                    $minutosRestantes = $ahora->diffInMinutes($fechaInicio, false);
                    
                    return [
                        'mega_evento_id' => $participacion->mega_evento_id,
                        'titulo' => $participacion->titulo,
                        'fecha_inicio' => $participacion->fecha_inicio,
                        'minutos_restantes' => max(0, $minutosRestantes),
                    ];
                })
                ->filter(function($megaEvento) {
                    // Solo mega eventos que inician en exactamente 5 minutos o menos
                    return $megaEvento['minutos_restantes'] <= 5 && $megaEvento['minutos_restantes'] >= 0;
                })
                ->values();

            return response()->json([
                "success" => true,
                "mega_eventos" => $participaciones
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error obteniendo alertas de 5 minutos para mega eventos: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al obtener alertas: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar descarga del QR del ticket de mega evento (solo una vez por ticket)
     */
    public function registrarDescargaQR(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

            // Buscar participación por código de ticket en mega eventos
            $participacion = DB::table('mega_evento_participantes_externos')
                ->where('ticket_codigo', $ticketCodigo)
                ->orWhereRaw('LOWER(ticket_codigo) = LOWER(?)', [$ticketCodigo])
                ->first();

            if (!$participacion) {
                return response()->json([
                    "success" => false,
                    "error" => "Código de ticket inválido. Verifique que el código sea correcto."
                ], 404);
            }

            // Verificar que el ticket pertenece al usuario autenticado
            if ($participacion->integrante_externo_id != $externoId) {
                return response()->json([
                    "success" => false,
                    "error" => "Este código de ticket no está asociado a tu cuenta. Solo puedes descargar tus propios tickets."
                ], 403);
            }

            // Verificar que la participación esté aprobada
            if ($participacion->estado_participacion !== 'aprobada') {
                return response()->json([
                    "success" => false,
                    "error" => "Tu participación en este mega evento aún no ha sido aprobada."
                ], 400);
            }

            // Verificar si ya se descargó el QR anteriormente
            if ($participacion->qr_descargado_at) {
                return response()->json([
                    "success" => false,
                    "error" => "El QR de este ticket ya fue descargado anteriormente. Solo se permite una descarga por ticket.",
                    "fecha_descarga_anterior" => \Carbon\Carbon::parse($participacion->qr_descargado_at)->format('d/m/Y H:i:s'),
                    "ya_descargado" => true
                ], 409);
            }

            // Registrar la descarga del QR
            try {
                DB::table('mega_evento_participantes_externos')
                    ->where('ticket_codigo', $ticketCodigo)
                    ->update([
                        'qr_descargado_at' => now(),
                    ]);
            } catch (\Exception $e) {
                // Si la columna no existe, intentar agregarla directamente
                if (strpos($e->getMessage(), 'qr_descargado_at') !== false || strpos($e->getMessage(), 'no existe la columna') !== false) {
                    \Log::warning('Columna qr_descargado_at no existe en mega eventos, intentando agregarla...');
                    try {
                        DB::statement('ALTER TABLE mega_evento_participantes_externos ADD COLUMN IF NOT EXISTS qr_descargado_at TIMESTAMP NULL');
                        // Reintentar la actualización
                        DB::table('mega_evento_participantes_externos')
                            ->where('ticket_codigo', $ticketCodigo)
                            ->update([
                                'qr_descargado_at' => now(),
                            ]);
                    } catch (\Exception $e2) {
                        \Log::error('Error agregando columna qr_descargado_at a mega eventos: ' . $e2->getMessage());
                        return response()->json([
                            "success" => false,
                            "error" => "Error al registrar descarga. Por favor, contacte al administrador.",
                            "detalle" => "La columna qr_descargado_at no existe en la base de datos. Ejecute la migración: php artisan migrate"
                        ], 500);
                    }
                } else {
                    throw $e;
                }
            }

            // Obtener la participación actualizada
            $participacionActualizada = DB::table('mega_evento_participantes_externos')
                ->where('ticket_codigo', $ticketCodigo)
                ->first();

            return response()->json([
                "success" => true,
                "message" => "Descarga de QR autorizada",
                "data" => [
                    'ticket_codigo' => $participacionActualizada->ticket_codigo,
                    'fecha_descarga' => $participacionActualizada->qr_descargado_at ? \Carbon\Carbon::parse($participacionActualizada->qr_descargado_at)->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error registrando descarga de QR de mega evento: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al registrar descarga: " . $e->getMessage()
            ], 500);
        }
    }

}
