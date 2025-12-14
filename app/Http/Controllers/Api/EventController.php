<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoEmpresaParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\Notificacion;
use App\Models\User;
use App\Models\Empresa;
use App\Models\IntegranteExterno;
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
     * Normalizar URL del avatar de una empresa
     */
    private function normalizarAvatarUrl($avatar)
    {
        if (!$avatar) return null;
        
        // Si ya es una URL completa, verificar si tiene IPs antiguas y reemplazarlas
        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            // Obtener la URL base actual
            $baseUrl = env('PUBLIC_APP_URL', env('APP_URL'));
            if (empty($baseUrl) && app()->runningInConsole() === false) {
                try {
                    $request = request();
                    if ($request) {
                        $baseUrl = $request->getSchemeAndHttpHost();
                    }
                } catch (\Exception $e) {
                    // Si falla, usar el valor por defecto
                }
            }
            if (empty($baseUrl)) {
                $baseUrl = 'http://10.26.5.12:8000';
            }
            
            // Reemplazar IPs antiguas con la URL actual
            $avatar = str_replace('http://192.168.0.6:8000', $baseUrl, $avatar);
            $avatar = str_replace('https://192.168.0.6:8000', $baseUrl, $avatar);
            $avatar = str_replace('http://10.26.15.110:8000', $baseUrl, $avatar);
            $avatar = str_replace('https://10.26.15.110:8000', $baseUrl, $avatar);
            $avatar = str_replace('http://10.26.5.12:8000', $baseUrl, $avatar);
            $avatar = str_replace('https://10.26.5.12:8000', $baseUrl, $avatar);
            
            return $avatar;
        }
        
        // Obtener la URL base
        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL'));
        if (empty($baseUrl) && app()->runningInConsole() === false) {
            try {
                $request = request();
                if ($request) {
                    $baseUrl = $request->getSchemeAndHttpHost();
                }
            } catch (\Exception $e) {
                // Si falla, usar el valor por defecto
            }
        }
        if (empty($baseUrl)) {
            $baseUrl = 'http://10.26.5.12:8000';
        }
        
        // Normalizar la ruta
        if (str_starts_with($avatar, '/storage/')) {
            return rtrim($baseUrl, '/') . $avatar;
        } elseif (str_starts_with($avatar, 'storage/')) {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($avatar, 'storage/');
        } else {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($avatar, '/');
        }
    }

    /**
     * Enriquecer patrocinadores con información completa (avatar y nombre)
     */
    private function enriquecerPatrocinadores($patrocinadores, $eventoId = null)
    {
        $enriquecidos = [];
        
        // 1. Procesar patrocinadores del campo JSON
        if (is_array($patrocinadores) && !empty($patrocinadores)) {
        foreach ($patrocinadores as $pat) {
                $empresaId = null;
                
                // Extraer ID de empresa
                if (is_array($pat) && isset($pat['id'])) {
                    $empresaId = $pat['id'];
                } elseif (is_numeric($pat)) {
                    $empresaId = $pat;
                } elseif (is_string($pat) && is_numeric($pat)) {
                    $empresaId = (int)$pat;
                }
                
                // Si tenemos un ID, buscar la empresa
                if ($empresaId) {
                    $empresa = Empresa::where('user_id', $empresaId)->first();
                    if ($empresa) {
                        // Usar el accessor foto_perfil_url igual que con el creador
                        $fotoPerfil = $empresa->foto_perfil_url ?? null;
                        
                        $enriquecidos[] = [
                            'id' => $empresaId,
                            'nombre' => $empresa->nombre_empresa,
                            'foto_perfil' => $fotoPerfil,
                            'tipo' => 'empresa'
                        ];
                    }
                } elseif (is_string($pat) && !is_numeric($pat)) {
                    // Si es solo texto, mantenerlo pero sin avatar
                    $enriquecidos[] = [
                        'id' => null,
                        'nombre' => $pat,
                        'foto_perfil' => null,
                        'tipo' => 'texto'
                    ];
                }
            }
        }
        
        // 2. Si se proporciona eventoId, también obtener patrocinadores desde la tabla
        if ($eventoId) {
            $patrocinadoresTabla = EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('tipo_colaboracion', 'Patrocinador')
                ->where('activo', true)
                ->with('empresa')
                ->get();
            
            foreach ($patrocinadoresTabla as $participacion) {
                if ($participacion->empresa) {
                    $empresa = $participacion->empresa;
                    $empresaId = $empresa->user_id;
                    // Verificar si ya está en la lista
                    $existe = collect($enriquecidos)->contains('id', $empresaId);
                    if (!$existe) {
                        // Usar el accessor foto_perfil_url igual que con el creador
                        $fotoPerfil = $empresa->foto_perfil_url ?? null;
                        
                        $enriquecidos[] = [
                            'id' => $empresaId,
                            'nombre' => $empresa->nombre_empresa,
                            'foto_perfil' => $fotoPerfil,
                            'tipo' => 'empresa'
                        ];
                    }
                }
            }
        }
        
        return $enriquecidos;
    }

    /**
     * Enriquecer auspiciadores con información completa (avatar y nombre)
     */
    private function enriquecerAuspiciadores($auspiciadores)
    {
        if (!is_array($auspiciadores) || empty($auspiciadores)) {
            return [];
        }

        $enriquecidos = [];
        foreach ($auspiciadores as $aus) {
            // Si es un ID numérico, buscar la empresa
            if (is_numeric($aus)) {
                $empresa = Empresa::where('user_id', $aus)->first();
                if ($empresa) {
                    $enriquecidos[] = [
                        'id' => $aus,
                        'nombre' => $empresa->nombre_empresa,
                        'avatar' => $empresa->foto_perfil_url ?? null,
                        'tipo' => 'empresa'
                    ];
                }
            } elseif (is_string($aus)) {
                // Si es un string, puede ser un nombre o un ID como string
                if (is_numeric($aus)) {
                    $empresa = Empresa::where('user_id', (int)$aus)->first();
                    if ($empresa) {
                        $enriquecidos[] = [
                            'id' => (int)$aus,
                            'nombre' => $empresa->nombre_empresa,
                            'avatar' => $empresa->foto_perfil_url ?? null,
                            'tipo' => 'empresa'
                        ];
                    }
                } else {
                    // Si es solo texto, mantenerlo pero sin avatar
                    $enriquecidos[] = [
                        'id' => null,
                        'nombre' => $aus,
                        'avatar' => null,
                        'tipo' => 'texto'
                    ];
                }
            }
        }
        return $enriquecidos;
    }

    /**
     * Enriquecer empresas colaboradoras con información completa desde la tabla de participaciones
     */
    private function enriquecerEmpresasColaboradoras($eventoId)
    {
        try {
            $participaciones = \App\Models\EventoEmpresaParticipacion::where('evento_id', $eventoId)
                ->where('activo', true)
                ->with(['empresa'])
                ->get();

            $empresas = [];
            foreach ($participaciones as $participacion) {
                if ($participacion->empresa) {
                    $empresas[] = [
                        'id' => $participacion->empresa_id,
                        'nombre' => $participacion->empresa->nombre_empresa ?? 'N/A',
                        'avatar' => $participacion->empresa->foto_perfil_url ?? null,
                        'tipo' => 'empresa_colaboradora',
                        'estado' => $participacion->estado,
                        'tipo_colaboracion' => $participacion->tipo_colaboracion,
                        'descripcion_colaboracion' => $participacion->descripcion_colaboracion,
                    ];
                }
            }
            return $empresas;
        } catch (\Throwable $e) {
            \Log::error('Error enriqueciendo empresas colaboradoras: ' . $e->getMessage());
            return [];
        }
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
                        $filename = 'eventos/' . ($eventoId ?? 'temp') . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                        
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
                        
                        // Guardar SOLO el path relativo en BD (sin dominio)
                        // El accessor del modelo construirá la URL completa al servir la API
                        $imagenes[] = $path;

                        \Log::info("Imagen guardada (path relativo): $path -> $fullPath (también copiada a $publicPath)");
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
            
            // Filtro por tipo de evento (case-insensitive)
            if ($request->has('tipo_evento') && $request->tipo_evento !== '' && $request->tipo_evento !== 'todos') {
                $tipoEventoFiltro = strtolower(trim($request->tipo_evento));
                $query->whereRaw('LOWER(tipo_evento) = ?', [$tipoEventoFiltro]);
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
            
            // Por defecto, excluir eventos finalizados de la lista principal (a menos que se solicite explícitamente)
            $excluirFinalizados = $request->get('excluir_finalizados', true);
            
            if ($excluirFinalizados && ($estadoFiltro === 'todos' || $estadoFiltro === '')) {
                $todosEventos = $todosEventos->filter(function($e) {
                    return $e->estado_dinamico !== 'finalizado';
                })->values();
            }
            
            if ($estadoFiltro !== 'todos' && $estadoFiltro !== '') {
                $todosEventos = $todosEventos->filter(function($e) use ($estadoFiltro) {
                    $estadoDinamico = $e->estado_dinamico;
                    
                    // Mapear valores del frontend a estados dinámicos
                    if ($estadoFiltro === 'finalizado' || $estadoFiltro === 'finalizados') {
                        return $estadoDinamico === 'finalizado';
                    } elseif ($estadoFiltro === 'activos' || $estadoFiltro === 'activo') {
                        return $estadoDinamico === 'activo';
                    } elseif ($estadoFiltro === 'proximos' || $estadoFiltro === 'proximo') {
                        return $estadoDinamico === 'proximo';
                    } elseif ($estadoFiltro === 'en_curso') {
                        return $estadoDinamico === 'activo';
                    } elseif ($estadoFiltro === 'publicado') {
                        // Para publicado, puede ser activo, próximo o finalizado según fechas
                        return in_array($estadoDinamico, ['activo', 'proximo', 'finalizado']) || $e->estado === 'publicado';
                    } else {
                        // Para borrador, cancelado, etc., usar el estado guardado directamente
                        return $e->estado === $estadoFiltro;
                    }
                })->values();
            }
            
            $eventos = $todosEventos;

            \Log::info("Eventos encontrados: " . $eventos->count());

            // Enriquecer patrocinadores, auspiciadores, invitados y empresas colaboradoras con información completa
            // Y calcular estado dinámico basado en fechas
            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores), $e->id);
                $e->auspiciadores = $this->enriquecerAuspiciadores($this->safeArray($e->auspiciadores ?? []));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                $e->empresas_colaboradoras = $this->enriquecerEmpresasColaboradoras($e->id);
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
            
            // Si el usuario está autenticado, excluir eventos en los que ya participa
            if ($request->user()) {
                $userId = $request->user()->id_usuario;
                $user = $request->user();
                
                // Si es usuario externo, excluir eventos donde ya participa
                if ($user->esIntegranteExterno()) {
                $eventosParticipando = EventoParticipacion::where('externo_id', $userId)
                    ->pluck('evento_id')
                    ->toArray();
                
                if (!empty($eventosParticipando)) {
                    $query->whereNotIn('id', $eventosParticipando);
                    }
                }
                
                // Si es empresa, excluir eventos donde ya es patrocinadora o colaboradora
                if ($user->esEmpresa()) {
                    // Obtener eventos donde la empresa ya participa (como patrocinadora o colaboradora)
                    $eventosPatrocinando = EventoEmpresaParticipacion::where('empresa_id', $userId)
                        ->where('activo', true)
                        ->pluck('evento_id')
                        ->toArray();
                    
                    \Log::info("Eventos donde empresa {$userId} participa (tabla): " . count($eventosPatrocinando));
                    
                    // También verificar eventos donde la empresa está en el campo JSON patrocinadores
                    // Obtener todos los eventos publicados y verificar manualmente el JSON
                    try {
                        $eventosConPatrocinadorJSON = Evento::where('estado', 'publicado')
                            ->whereNotNull('patrocinadores')
                            ->get()
                            ->filter(function($evento) use ($userId) {
                                // Usar getRawOriginal para obtener el valor crudo antes de que Laravel lo procese
                                $patrocinadoresRaw = $evento->getRawOriginal('patrocinadores');
                                
                                if (empty($patrocinadoresRaw)) {
                                    return false;
                                }
                                
                                // Convertir a array si es string JSON
                                $patrocinadores = $this->safeArray($patrocinadoresRaw);
                                
                                if (empty($patrocinadores) || !is_array($patrocinadores)) {
                                    return false;
                                }
                                
                                // Verificar si el ID está en el array (como número o string)
                                $encontrado = false;
                                foreach ($patrocinadores as $p) {
                                    if (is_numeric($p)) {
                                        if ((int)$p === (int)$userId) {
                                            $encontrado = true;
                                            break;
                                        }
                                    } elseif (is_string($p)) {
                                        if (trim($p) === (string)$userId || (int)trim($p) === (int)$userId) {
                                            $encontrado = true;
                                            break;
                                        }
                                    }
                                }
                                
                                return $encontrado;
                            })
                            ->pluck('id')
                            ->toArray();
                        
                        \Log::info("Eventos donde empresa {$userId} patrocina (JSON): " . count($eventosConPatrocinadorJSON));
                    } catch (\Throwable $e) {
                        \Log::error("Error verificando patrocinadores en JSON: " . $e->getMessage());
                        $eventosConPatrocinadorJSON = [];
                    }
                    
                    // Combinar ambos arrays y eliminar duplicados
                    $eventosExcluir = array_unique(array_merge($eventosPatrocinando, $eventosConPatrocinadorJSON));
                    
                    \Log::info("Total eventos a excluir para empresa {$userId}: " . count($eventosExcluir));
                    
                    if (!empty($eventosExcluir)) {
                        $query->whereNotIn('id', $eventosExcluir);
                    }
                }
            }
            
            // Filtro por tipo de evento (case-insensitive)
            if ($request->has('tipo_evento') && $request->tipo_evento !== '' && $request->tipo_evento !== 'todos') {
                $tipoEventoFiltro = strtolower(trim($request->tipo_evento));
                $query->whereRaw('LOWER(tipo_evento) = ?', [$tipoEventoFiltro]);
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

            \Log::info("Eventos publicados encontrados antes de filtrar finalizados: " . $eventos->count());

            // Transformar eventos y calcular estado dinámico
            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores), $e->id);
                $e->auspiciadores = $this->enriquecerAuspiciadores($this->safeArray($e->auspiciadores ?? []));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                $e->empresas_colaboradoras = $this->enriquecerEmpresasColaboradoras($e->id);
                // El accessor del modelo ya genera URLs completas
                $e->makeVisible(['imagenes', 'fecha_finalizacion']);
                // Agregar estado dinámico calculado
                $e->estado_dinamico = $e->estado_dinamico;
                return $e;
            });
            
            // Filtrar eventos finalizados basándose en estado_dinamico
            $eventos = $eventos->filter(function($e) {
                return $e->estado_dinamico !== 'finalizado';
            })->values();

            \Log::info("Eventos publicados encontrados después de filtrar finalizados: " . $eventos->count());

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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            // Obtener el evento sin caché para asegurar datos actualizados
            // Usar fresh() para forzar una nueva consulta a la base de datos
            $evento = Evento::with('ong')->find($id);
            
            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "message" => "Evento no encontrado"
                ], 404);
            }
            
            // Forzar refresh para obtener los datos más recientes
            $evento = $evento->fresh(['ong']);

            // Obtener patrocinadores (desde JSON y tabla, sin duplicados)
            $evento->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($evento->patrocinadores), $evento->id);
            $evento->auspiciadores = $this->enriquecerAuspiciadores($this->safeArray($evento->auspiciadores ?? []));
            $evento->invitados = $this->enriquecerInvitados($this->safeArray($evento->invitados));
            
            // Agregar empresas colaboradoras desde la tabla de participaciones
            $evento->empresas_colaboradoras = $this->enriquecerEmpresasColaboradoras($evento->id);
            
            // Agregar información del creador (ONG)
            if ($evento->ong) {
                $evento->creador = [
                    'tipo' => 'ONG',
                    'nombre' => $evento->ong->nombre_ong ?? 'ONG',
                    'foto_perfil' => $evento->ong->foto_perfil_url ?? null,
                    'id' => $evento->ong->user_id ?? null
                ];
            } else {
                $evento->creador = null;
            }
            
            // El accessor del modelo ya genera URLs completas
            $evento->makeVisible(['imagenes', 'fecha_finalizacion']);
            // Agregar estado dinámico calculado
            $evento->estado_dinamico = $evento->estado_dinamico;
            
            // Convertir el modelo a array para poder modificar las fechas
            $eventoArray = $evento->toArray();
            
            // Formatear fechas en formato PostgreSQL (YYYY-MM-DD HH:MM:SS) sin zona horaria
            // PostgreSQL almacena fechas como 'timestamp without time zone'
            // Laravel las interpreta según la timezone de la app (UTC por defecto)
            // Para PostgreSQL, necesitamos obtener el valor raw de la BD sin conversión
            // Usamos getRawOriginal() para obtener el valor tal como está en PostgreSQL
            if ($evento->fecha_inicio) {
                // Obtener el valor raw de PostgreSQL (sin conversión de Laravel)
                $rawValue = $evento->getRawOriginal('fecha_inicio');
                // Si es string, usarlo directamente; si es Carbon, formatearlo
                $eventoArray['fecha_inicio'] = is_string($rawValue) ? $rawValue : $evento->fecha_inicio->format('Y-m-d H:i:s');
            }
            if ($evento->fecha_fin) {
                $rawValue = $evento->getRawOriginal('fecha_fin');
                $eventoArray['fecha_fin'] = is_string($rawValue) ? $rawValue : $evento->fecha_fin->format('Y-m-d H:i:s');
            }
            if ($evento->fecha_limite_inscripcion) {
                $rawValue = $evento->getRawOriginal('fecha_limite_inscripcion');
                $eventoArray['fecha_limite_inscripcion'] = is_string($rawValue) ? $rawValue : $evento->fecha_limite_inscripcion->format('Y-m-d H:i:s');
            }
            if ($evento->fecha_finalizacion) {
                $rawValue = $evento->getRawOriginal('fecha_finalizacion');
                $eventoArray['fecha_finalizacion'] = is_string($rawValue) ? $rawValue : ($evento->fecha_finalizacion ? $evento->fecha_finalizacion->format('Y-m-d H:i:s') : null);
            }

            // Preparar datos para JSON
            $datos = [
                "success" => true,
                "evento" => $eventoArray
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
            
            // Convertir strings JSON vacíos a arrays vacíos para patrocinadores e invitados
            if (isset($requestData['patrocinadores'])) {
                if (is_string($requestData['patrocinadores']) && $requestData['patrocinadores'] === '[]') {
                $requestData['patrocinadores'] = [];
                } elseif (!is_array($requestData['patrocinadores'])) {
                    // Si viene como string pero no es '[]', intentar decodificarlo
                    $decoded = json_decode($requestData['patrocinadores'], true);
                    $requestData['patrocinadores'] = is_array($decoded) ? $decoded : [];
            }
            } else {
                $requestData['patrocinadores'] = [];
            }
            
            if (isset($requestData['invitados']) && is_string($requestData['invitados']) && $requestData['invitados'] === '[]') {
                $requestData['invitados'] = [];
            }
            
            // Procesar auspiciadores si vienen
            if (isset($requestData['auspiciadores'])) {
                if (is_string($requestData['auspiciadores']) && $requestData['auspiciadores'] === '[]') {
                    $requestData['auspiciadores'] = [];
                } elseif (!is_array($requestData['auspiciadores'])) {
                    $decoded = json_decode($requestData['auspiciadores'], true);
                    $requestData['auspiciadores'] = is_array($decoded) ? $decoded : [];
                }
            } else {
                $requestData['auspiciadores'] = [];
            }
            
            $validator = Validator::make($requestData, [
                'ong_id' => 'required|integer|exists:ongs,user_id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string|min:10',
                'tipo_evento' => 'required|string|max:100',
                'fecha_inicio' => 'required|date|after:now',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'fecha_limite_inscripcion' => 'nullable|date|before:fecha_inicio',
                'capacidad_maxima' => 'nullable|integer|min:1',
                'estado' => 'required|in:borrador,publicado,finalizado,cancelado',
                'ciudad' => 'nullable|string|max:255',
                'direccion' => 'required|string|max:255',
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
                'inscripcion_abierta' => 'nullable|boolean',
                'patrocinadores' => 'nullable|array',
                'patrocinadores.*' => 'integer',
                'invitados' => 'nullable|array',
                'invitados.*' => 'integer',
                'imagenes' => 'nullable|array',
                'imagenes_urls' => 'nullable|string',
                'auspiciadores' => 'nullable|array',
            ], [
                'titulo.required' => 'El título del evento es obligatorio.',
                'descripcion.required' => 'La descripción del evento es obligatoria.',
                'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
                'tipo_evento.required' => 'Debes seleccionar un tipo de evento.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
                'fecha_inicio.after' => 'La fecha de inicio debe ser una fecha futura.',
                'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
                'fecha_fin.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
                'estado.required' => 'Debes seleccionar un estado para el evento.',
                'direccion.required' => 'La ubicación/dirección del evento es obligatoria.',
                'lat.required' => 'Debes seleccionar una ubicación en el mapa.',
                'lng.required' => 'Debes seleccionar una ubicación en el mapa.',
            ]);

            // Validar que haya al menos una imagen (archivo o URL)
            $tieneImagenes = false;
            if ($request->hasFile('imagenes') && count($request->file('imagenes')) > 0) {
                $tieneImagenes = true;
            }
            if ($request->has('imagenes_urls')) {
                $urls = json_decode($request->input('imagenes_urls'), true);
                if (is_array($urls) && count($urls) > 0) {
                    $tieneImagenes = true;
                }
            }

            if ($validator->fails()) {
                if (!$tieneImagenes) {
                    $validator->errors()->add('imagenes', 'Debes agregar al menos una imagen promocional (archivo o URL).');
                }
                return response()->json([
                    "success" => false,
                    "error" => "Por favor, completa todos los campos obligatorios antes de crear el evento.",
                    "errors" => $validator->errors()
                ], 422);
            }
            
            // Validación adicional: debe haber al menos una imagen
            if (!$tieneImagenes) {
                return response()->json([
                    "success" => false,
                    "error" => "Debes agregar al menos una imagen promocional (archivo o URL) antes de crear el evento.",
                    "errors" => ['imagenes' => ['Debes agregar al menos una imagen promocional.']]
                ], 422);
            }

            $data = $validator->validated();

            // Procesar patrocinadores e invitados correctamente
            // Ya están validados como arrays, solo asegurar que sean enteros
            $patrocinadores = [];
            if (isset($data['patrocinadores']) && is_array($data['patrocinadores'])) {
                $patrocinadores = array_map('intval', array_filter($data['patrocinadores'], 'is_numeric'));
                \Log::info("Patrocinadores recibidos al crear evento: " . json_encode($patrocinadores));
            } else {
                \Log::info("No se recibieron patrocinadores o no es un array. Valor recibido: " . json_encode($data['patrocinadores'] ?? 'no definido'));
            }

            $invitados = [];
            if (isset($data['invitados']) && is_array($data['invitados'])) {
                $invitados = array_map('intval', array_filter($data['invitados'], 'is_numeric'));
            }

            // Procesar auspiciadores
            $auspiciadores = [];
            if (isset($data['auspiciadores']) && is_array($data['auspiciadores'])) {
                $auspiciadores = array_map('intval', array_filter($data['auspiciadores'], 'is_numeric'));
            }

            // TRANSACCIÓN: Crear evento + procesar imágenes + crear patrocinadores
            $evento = DB::transaction(function () use ($data, $request, $patrocinadores, $invitados, $auspiciadores) {
                // 1. Crear evento
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
                    "auspiciadores" => $auspiciadores,
            ]);

                // 2. Procesar imágenes después de crear el evento
            $imagenes = $this->processImages($request, $evento->id);
            if (!empty($imagenes)) {
                $evento->update(['imagenes' => $imagenes]);
            }
                
                // 3. Crear registros en evento_empresas_participantes para los patrocinadores
                if (!empty($patrocinadores)) {
                    foreach ($patrocinadores as $empresaId) {
                        // Verificar que la empresa existe
                        $empresa = Empresa::where('user_id', $empresaId)->first();
                        if (!$empresa) {
                            \Log::warning("Empresa con user_id {$empresaId} no encontrada al crear patrocinador para evento {$evento->id}");
                            continue;
                        }

                        // Verificar si ya existe una participación
                        $existe = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                            ->where('empresa_id', $empresaId)
                            ->exists();

                        if (!$existe) {
                            EventoEmpresaParticipacion::create([
                                'evento_id' => $evento->id,
                                'empresa_id' => $empresaId,
                                'estado' => 'asignada', // Patrocinadores se asignan automáticamente
                                'activo' => true,
                                'tipo_colaboracion' => 'Patrocinador', // Marcar como patrocinador
                            ]);
                            \Log::info("Patrocinador {$empresaId} agregado a evento {$evento->id} en tabla de participaciones");
                        }
                    }
                }
                
                return $evento;
            });
            
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
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            \Log::info("=== INICIO ACTUALIZACIÓN EVENTO ID: {$id} ===");
            \Log::info("Datos recibidos:", $request->all());
            
            $evento = Evento::find($id);

            if (!$evento) {
                \Log::error("Evento no encontrado: {$id}");
                return response()->json(["success" => false, "message" => "Evento no encontrado"], 404);
            }

            \Log::info("Evento encontrado. Estado actual: {$evento->estado}, Título: {$evento->titulo}");

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
                \Log::error("Errores de validación:", $validator->errors()->toArray());
                return response()->json([
                    "success" => false,
                    "errors" => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            \Log::info("Datos validados:", $data);

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

            // Verificar si se está finalizando el evento (para crear notificación)
            $estadoAnterior = $evento->estado;
            $seEstaFinalizando = isset($data['estado']) && 
                                 $data['estado'] === 'finalizado' && 
                                 $estadoAnterior !== 'finalizado';
            
            // TRANSACCIÓN: Actualizar evento + sincronizar patrocinadores
            DB::transaction(function () use ($evento, $data, $request, $seEstaFinalizando) {
                \Log::info("Iniciando transacción de actualización");
                \Log::info("Datos a actualizar:", $data);
                
                // 1. Actualizar evento
                $resultado = $evento->update($data);
                \Log::info("Resultado de update(): " . ($resultado ? 'true' : 'false'));
                
                // Refrescar el modelo para obtener los datos actualizados
                $evento->refresh();
                \Log::info("Evento refrescado. Nuevo estado: {$evento->estado}, Nuevo título: {$evento->titulo}");
                
                // 2. Si se está finalizando el evento, crear notificación para la ONG
                if ($seEstaFinalizando && $evento->ong_id) {
                    try {
                        // Si no tiene fecha_finalizacion, establecerla ahora
                        if (!$evento->fecha_finalizacion) {
                            $evento->fecha_finalizacion = now();
                            $evento->save();
                        }
                        
                        Notificacion::create([
                            'ong_id' => $evento->ong_id,
                            'evento_id' => $evento->id,
                            'externo_id' => null,
                            'tipo' => 'evento_finalizado',
                            'titulo' => 'Evento Finalizado',
                            'mensaje' => "Tu evento '{$evento->titulo}' ha sido finalizado. El evento ya no está disponible para nuevas participaciones o interacciones.",
                            'leida' => false
                        ]);
                    } catch (\Throwable $e) {
                        \Log::error("Error al crear notificación de evento finalizado: " . $e->getMessage());
                    }
                }
                
                // 2. Sincronizar patrocinadores en la tabla evento_empresas_participantes
                if (isset($data['patrocinadores']) && is_array($data['patrocinadores'])) {
                    $nuevosPatrocinadores = array_map('intval', array_filter($data['patrocinadores'], 'is_numeric'));
                    // Obtener patrocinadores actuales en la tabla (donde tipo_colaboracion = 'Patrocinador')
                    $patrocinadoresActuales = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                        ->where('tipo_colaboracion', 'Patrocinador')
                        ->pluck('empresa_id')
                        ->toArray();
                    
                    // Eliminar patrocinadores que ya no están en la lista
                    $patrocinadoresAEliminar = array_diff($patrocinadoresActuales, $nuevosPatrocinadores);
                    if (!empty($patrocinadoresAEliminar)) {
                        EventoEmpresaParticipacion::where('evento_id', $evento->id)
                            ->whereIn('empresa_id', $patrocinadoresAEliminar)
                            ->where('tipo_colaboracion', 'Patrocinador')
                            ->delete();
                        \Log::info("Patrocinadores eliminados del evento {$evento->id}: " . implode(', ', $patrocinadoresAEliminar));
                    }
                    
                    // Agregar nuevos patrocinadores
                    foreach ($nuevosPatrocinadores as $empresaId) {
                        // Verificar que la empresa existe
                        $empresa = Empresa::where('user_id', $empresaId)->first();
                        if (!$empresa) {
                            \Log::warning("Empresa con user_id {$empresaId} no encontrada al actualizar patrocinador para evento {$evento->id}");
                            continue;
                        }

                        // Verificar si ya existe una participación (como patrocinador o colaborador)
                        $existe = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                            ->where('empresa_id', $empresaId)
                            ->exists();

                        if (!$existe) {
                            EventoEmpresaParticipacion::create([
                                'evento_id' => $evento->id,
                                'empresa_id' => $empresaId,
                                'estado' => 'asignada',
                                'activo' => true,
                                'tipo_colaboracion' => 'Patrocinador',
                            ]);
                            \Log::info("Patrocinador {$empresaId} agregado a evento {$evento->id} en tabla de participaciones");
                        } else {
                            // Si ya existe pero no es patrocinador, actualizar el tipo
                            $participacion = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                                ->where('empresa_id', $empresaId)
                                ->first();
                            if ($participacion && $participacion->tipo_colaboracion !== 'Patrocinador') {
                                // No cambiar si ya es colaborador, solo actualizar si no tiene tipo
                                if (empty($participacion->tipo_colaboracion)) {
                                    $participacion->tipo_colaboracion = 'Patrocinador';
                                    $participacion->save();
                                }
                            }
                        }
                    }
                }
            });
            
            // Forzar refresh para obtener las imágenes procesadas
            $evento->refresh();
            $evento->makeVisible('imagenes');
            
            // Obtener el evento actualizado de la base de datos directamente
            $eventoActualizado = Evento::find($id);
            if ($eventoActualizado) {
                \Log::info("Evento final después de actualización:");
                \Log::info("- ID: {$eventoActualizado->id}");
                \Log::info("- Título: {$eventoActualizado->titulo}");
                \Log::info("- Estado: {$eventoActualizado->estado}");
                \Log::info("- Lat: " . ($eventoActualizado->lat ?? 'null'));
                \Log::info("- Lng: " . ($eventoActualizado->lng ?? 'null'));
                \Log::info("- Ciudad: " . ($eventoActualizado->ciudad ?? 'null'));
                \Log::info("- Dirección: " . ($eventoActualizado->direccion ?? 'null'));
            }
            \Log::info("=== FIN ACTUALIZACIÓN EVENTO ID: {$id} ===");

            // Preparar datos para JSON
            $datos = [
                "success" => true,
                "message" => "Evento actualizado correctamente",
                "evento" => $eventoActualizado ? $eventoActualizado->fresh() : $evento->fresh()
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    // ======================================================
    //  DASHBOARD DE EVENTOS POR ESTADO (ONG)
    // ======================================================
    public function dashboardPorEstado($ongId, Request $request)
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
                $e->patrocinadores = $this->enriquecerPatrocinadores($this->safeArray($e->patrocinadores), $e->id);
                $e->auspiciadores = $this->enriquecerAuspiciadores($this->safeArray($e->auspiciadores ?? []));
                $e->invitados = $this->enriquecerInvitados($this->safeArray($e->invitados));
                $e->empresas_colaboradoras = $this->enriquecerEmpresasColaboradoras($e->id);
                $e->imagenes = $this->safeArray($e->imagenes);
                $e->makeVisible(['fecha_finalizacion']);
                // Agregar estado dinámico calculado
                $e->estado_dinamico = $e->estado_dinamico;
                return $e;
            });
            
            // Obtener IDs de todos los eventos
            $eventosIds = $todosEventos->pluck('id');
            
            // Calcular estadísticas basadas en estados dinámicos
            $estadisticas = [
                'total' => $todosEventos->count(),
                'finalizados' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'finalizado')->count(),
                'activos' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'activo')->count(),
                'proximos' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'proximo')->count(),
                'en_curso' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'activo')->count(),
                'cancelados' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'cancelado')->count(),
                'borradores' => $todosEventos->filter(fn($e) => $e->estado_dinamico === 'borrador')->count(),
            ];
            
            // === MÉTRICAS AGREGADAS ===
            $totalParticipantes = EventoParticipacion::whereIn('evento_id', $eventosIds)->count() 
                + EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)->count();
            
            $totalParticipantesAprobados = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->where('estado', 'aprobada')
                ->count() + EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)
                ->where('estado', 'aprobada')
                ->count();
            
            $totalParticipantesAsistieron = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->where('asistio', true)
                ->count();
            
            $totalVoluntariosUnicos = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->whereNotNull('externo_id')
                ->distinct('externo_id')
                ->count('externo_id');
            
            // Calcular participantes únicos no registrados (por email único)
            $participantesNoRegistradosUnicos = EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)
                ->whereNotNull('email')
                ->distinct('email')
                ->count('email');
            
            // Total de participantes únicos (registrados + no registrados únicos)
            $totalParticipantesUnicos = $totalVoluntariosUnicos + $participantesNoRegistradosUnicos;
            
            $totalReacciones = EventoReaccion::whereIn('evento_id', $eventosIds)->count();
            $totalCompartidos = EventoCompartido::whereIn('evento_id', $eventosIds)->count();
            
            // === KPIs Y MÉTRICAS DE RENDIMIENTO ===
            $promedioParticipantes = $estadisticas['total'] > 0 
                ? round($totalParticipantes / $estadisticas['total'], 2) 
                : 0;
            
            $tasaAsistencia = $totalParticipantesAprobados > 0 
                ? round(($totalParticipantesAsistieron / $totalParticipantesAprobados) * 100, 2) 
                : 0;
            
            // Engagement Rate: usar participantes únicos para evitar valores > 100%
            // Si no hay participantes únicos, usar totalParticipantes como fallback
            $denominadorEngagement = $totalParticipantesUnicos > 0 ? $totalParticipantesUnicos : $totalParticipantes;
            $engagementRate = $denominadorEngagement > 0 
                ? round((($totalReacciones + $totalCompartidos) / $denominadorEngagement) * 100, 2) 
                : 0;
            
            // Limitar el Engagement Rate al 100% máximo (por si acaso)
            if ($engagementRate > 100) {
                $engagementRate = 100;
            }
            
            $tasaFinalizacion = $estadisticas['total'] > 0 
                ? round(($estadisticas['finalizados'] / $estadisticas['total']) * 100, 2) 
                : 0;
            
            // === EVENTOS DESTACADOS ===
            $eventosConMetricas = $todosEventos->map(function($e) {
                $participantes = EventoParticipacion::where('evento_id', $e->id)->count() 
                    + EventoParticipanteNoRegistrado::where('evento_id', $e->id)->count();
                $reacciones = EventoReaccion::where('evento_id', $e->id)->count();
                $compartidos = EventoCompartido::where('evento_id', $e->id)->count();
                $engagement = $reacciones + $compartidos;
                
                return [
                    'id' => $e->id,
                    'titulo' => $e->titulo,
                    'participantes' => $participantes,
                    'reacciones' => $reacciones,
                    'compartidos' => $compartidos,
                    'engagement' => $engagement,
                    'fecha_inicio' => $e->fecha_inicio,
                    'estado_dinamico' => $e->estado_dinamico,
                ];
            });
            
            $eventoMasParticipantes = $eventosConMetricas->sortByDesc('participantes')->first();
            $eventoMasEngagement = $eventosConMetricas->sortByDesc('engagement')->first();
            $proximoEventoImportante = $todosEventos->filter(fn($e) => $e->estado_dinamico === 'proximo')
                ->sortBy('fecha_inicio')
                ->first();
            $eventoMasReciente = $todosEventos->sortByDesc('created_at')->first();
            
            // === GRÁFICOS ADICIONALES ===
            // Eventos creados por mes
            $eventosPorMes = $todosEventos->groupBy(function($e) {
                return \Carbon\Carbon::parse($e->created_at)->format('Y-m');
            })->map->count();
            
            // Top 5 eventos por participación
            $top5Participacion = $eventosConMetricas->sortByDesc('participantes')->take(5)->values();
            
            // Top 5 eventos por engagement
            $top5Engagement = $eventosConMetricas->sortByDesc('engagement')->take(5)->values();
            
            // === MÉTRICAS DE TIEMPO ===
            $eventosConFechas = $todosEventos->filter(fn($e) => $e->fecha_inicio);
            $diasPromedioHastaInicio = $eventosConFechas->map(function($e) {
                return \Carbon\Carbon::parse($e->created_at)->diffInDays($e->fecha_inicio);
            })->avg();
            
            $duracionPromedio = $todosEventos->filter(fn($e) => $e->fecha_inicio && $e->fecha_fin)
                ->map(function($e) {
                    return \Carbon\Carbon::parse($e->fecha_inicio)->diffInDays($e->fecha_fin);
                })->avg();
            
            // === TABLA RESUMEN DE EVENTOS ===
            $tablaResumen = $eventos->map(function($e) {
                $participantes = EventoParticipacion::where('evento_id', $e->id)->count() 
                    + EventoParticipanteNoRegistrado::where('evento_id', $e->id)->count();
                $participantesAprobados = EventoParticipacion::where('evento_id', $e->id)
                    ->where('estado', 'aprobada')
                    ->count() + EventoParticipanteNoRegistrado::where('evento_id', $e->id)
                    ->where('estado', 'aprobada')
                    ->count();
                $asistieron = EventoParticipacion::where('evento_id', $e->id)
                    ->where('asistio', true)
                    ->count();
                $reacciones = EventoReaccion::where('evento_id', $e->id)->count();
                $compartidos = EventoCompartido::where('evento_id', $e->id)->count();
                
                $tasaAsistenciaEvento = $participantesAprobados > 0 
                    ? round(($asistieron / $participantesAprobados) * 100, 2) 
                    : 0;
                
                return [
                    'id' => $e->id,
                    'titulo' => $e->titulo,
                    'estado' => $e->estado_dinamico,
                    'participantes' => $participantes,
                    'reacciones' => $reacciones,
                    'compartidos' => $compartidos,
                    'tasa_asistencia' => $tasaAsistenciaEvento,
                    'fecha_inicio' => $e->fecha_inicio,
                ];
            })->values();
            
            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'eventos' => $eventos,
                'estadisticas' => $estadisticas,
                'metricas_agregadas' => [
                    'total_participantes' => $totalParticipantes,
                    'total_participantes_aprobados' => $totalParticipantesAprobados,
                    'total_participantes_asistieron' => $totalParticipantesAsistieron,
                    'total_voluntarios_unicos' => $totalVoluntariosUnicos,
                    'total_reacciones' => $totalReacciones,
                    'total_compartidos' => $totalCompartidos,
                    'promedio_participantes' => $promedioParticipantes,
                    'tasa_asistencia' => $tasaAsistencia,
                    'engagement_rate' => $engagementRate,
                    'tasa_finalizacion' => $tasaFinalizacion,
                ],
                'eventos_destacados' => [
                    'mas_participantes' => $eventoMasParticipantes,
                    'mas_engagement' => $eventoMasEngagement,
                    'proximo_importante' => $proximoEventoImportante ? [
                        'id' => $proximoEventoImportante->id,
                        'titulo' => $proximoEventoImportante->titulo,
                        'fecha_inicio' => $proximoEventoImportante->fecha_inicio,
                    ] : null,
                    'mas_reciente' => $eventoMasReciente ? [
                        'id' => $eventoMasReciente->id,
                        'titulo' => $eventoMasReciente->titulo,
                        'fecha_creacion' => $eventoMasReciente->created_at,
                    ] : null,
                ],
                'graficos_adicionales' => [
                    'eventos_por_mes' => $eventosPorMes,
                    'top5_participacion' => $top5Participacion,
                    'top5_engagement' => $top5Engagement,
                ],
                'metricas_tiempo' => [
                    'dias_promedio_hasta_inicio' => round($diasPromedioHastaInicio ?? 0, 1),
                    'duracion_promedio_dias' => round($duracionPromedio ?? 0, 1),
                ],
                'tabla_resumen' => $tablaResumen,
                'filtro_estado' => $estadoFiltro,
                'count' => $eventos->count()
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
        try {
        $evento = Evento::find($id);

            if (!$evento) {
            return response()->json(["success" => false, "message" => "No encontrado"], 404);
            }

            // TRANSACCIÓN: Eliminar evento + datos relacionados
            DB::transaction(function () use ($evento) {
                // 1. Eliminar participaciones relacionadas
                EventoParticipacion::where('evento_id', $evento->id)->delete();

                // 2. Eliminar empresas participantes relacionadas
                EventoEmpresaParticipacion::where('evento_id', $evento->id)->delete();

                // 3. Eliminar reacciones relacionadas
                EventoReaccion::where('evento_id', $evento->id)->delete();

                // 4. Eliminar notificaciones relacionadas
                Notificacion::where('evento_id', $evento->id)->delete();

                // 5. Eliminar el evento
        $evento->delete();
            });

        return response()->json([
            "success" => true,
                "message" => "Evento eliminado correctamente"
        ]);

        } catch (\Throwable $e) {
            \Log::error('Error al eliminar evento: ' . $e->getMessage());
            return response()->json([
                "success" => false,
                "error" => "Error al eliminar el evento"
            ], 500);
        }
    }

    // ======================================================
    //  EMPRESAS DISPONIBLES PARA PATROCINAR
    // ======================================================
    public function empresasDisponibles()
    {
        try {
            $empresas = Empresa::select('user_id', 'nombre_empresa', 'descripcion', 'foto_perfil', 'NIT')
                ->get()
                ->map(function ($empresa) {
                    // Construir URL completa de la foto de perfil
                    $fotoPerfil = null;
                    if ($empresa->foto_perfil) {
                        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL', 'http://10.26.5.12:8000'));
                        if (strpos($empresa->foto_perfil, 'http://') === 0 || strpos($empresa->foto_perfil, 'https://') === 0) {
                            $fotoPerfil = $empresa->foto_perfil;
                        } else {
                            $fotoPerfil = rtrim($baseUrl, '/') . '/storage/' . ltrim($empresa->foto_perfil, '/');
                        }
                    }
                    
                    return [
                        'id' => $empresa->user_id,
                        'nombre' => $empresa->nombre_empresa,
                        'descripcion' => $empresa->descripcion ?? '',
                        'foto_perfil' => $fotoPerfil,
                        'NIT' => $empresa->NIT ?? null
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
            
            // Usar transacción para asegurar consistencia
            DB::transaction(function () use ($evento, $patrocinadores, $empresaId) {
                // 1. Actualizar campo JSON de patrocinadores
            $evento->update([
                "patrocinadores" => $patrocinadores
            ]);

                // 2. Crear registro en la tabla de participaciones si no existe
                $existeParticipacion = EventoEmpresaParticipacion::where('evento_id', $evento->id)
                    ->where('empresa_id', $empresaId)
                    ->exists();

                if (!$existeParticipacion) {
                    try {
                        EventoEmpresaParticipacion::create([
                            'evento_id' => $evento->id,
                            'empresa_id' => $empresaId,
                            'estado' => 'asignada', // Estado inicial cuando se auto-asigna como patrocinador
                            'activo' => true,
                            'tipo_colaboracion' => 'Patrocinador',
                        ]);
                        \Log::info("Registro de patrocinador creado en tabla para empresa {$empresaId} en evento {$evento->id}");
                    } catch (\Throwable $e) {
                        \Log::error("Error al crear registro de patrocinador en tabla: " . $e->getMessage());
                        // No fallar la transacción si esto falla, el campo JSON ya está actualizado
                    }
                }
            });

            // Obtener información de la empresa
            $empresa = Empresa::where('user_id', $empresaId)->first();
            $nombreEmpresa = $empresa ? $empresa->nombre_empresa : 'Una empresa';

            // Crear notificación para la ONG del evento
            if ($evento->ong_id) {
                try {
                    Notificacion::create([
                        'ong_id' => $evento->ong_id,
                        'evento_id' => $evento->id,
                        'externo_id' => null,
                        'tipo' => 'nuevo_patrocinador',
                        'titulo' => 'Nuevo Patrocinador',
                        'mensaje' => "{$nombreEmpresa} ha decidido patrocinar tu evento \"{$evento->titulo}\". ¡Gracias por el apoyo!",
                        'leida' => false
                    ]);
                    \Log::info("Notificación de patrocinador creada para ONG {$evento->ong_id} sobre evento {$evento->id}");
                } catch (\Throwable $e) {
                    \Log::error("Error al crear notificación de patrocinador: " . $e->getMessage());
                    // No fallar la operación si la notificación falla
                }
            }

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
                abort(401, 'No autenticado');
            }

            $evento = Evento::find($id);
            
            if (!$evento) {
                abort(404, 'Evento no encontrado');
            }

            // Verificar que el usuario es el dueño del evento
            $ongId = auth()->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                abort(403, 'No tienes permiso para ver este dashboard');
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

            // Obtener datos para gráficas
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
            } catch (\Exception $e) {
                \Log::error('Error obteniendo participantes por estado: ' . $e->getMessage());
            }

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
                \Log::error('Error obteniendo inscripciones por día: ' . $e->getMessage());
            }

            // Verificar que dompdf esté disponible
            if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                abort(500, 'La librería de PDF no está instalada');
            }

            // Obtener nombres de quienes reaccionaron
            $reaccionesConNombres = [];
            try {
                $reacciones = EventoReaccion::where('evento_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                foreach ($reacciones as $reaccion) {
                    if ($reaccion->externo_id) {
                        // Usuario registrado
                        $user = User::find($reaccion->externo_id);
                        if ($user) {
                            $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                            $nombre = $externo 
                                ? trim($externo->nombres . ' ' . ($externo->apellidos ?? ''))
                                : ($user->nombre_usuario ?? 'Usuario');
                            $email = $externo ? $externo->email : ($user->correo_electronico ?? '');
                        } else {
                            $nombre = 'Usuario no encontrado';
                            $email = '';
                        }
                    } else {
                        // Usuario no registrado
                        $nombre = trim(($reaccion->nombres ?? '') . ' ' . ($reaccion->apellidos ?? ''));
                        $email = $reaccion->email ?? '';
                    }
                    
                    if (!empty($nombre)) {
                        $reaccionesConNombres[] = [
                            'nombre' => $nombre,
                            'email' => $email,
                            'fecha' => $reaccion->created_at->format('d/m/Y H:i')
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error obteniendo reacciones con nombres: ' . $e->getMessage());
            }

            // Obtener nombres de participantes
            $participantesConNombres = [];
            try {
                // Participantes registrados
                $participaciones = EventoParticipacion::where('evento_id', $id)
                    ->with('externo')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                foreach ($participaciones as $participacion) {
                    if ($participacion->externo_id) {
                        // Usuario registrado
                        $user = $participacion->externo;
                        if ($user) {
                            $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                            $nombre = $externo 
                                ? trim($externo->nombres . ' ' . ($externo->apellidos ?? ''))
                                : ($user->nombre_usuario ?? 'Usuario');
                            $email = $externo ? $externo->email : ($user->correo_electronico ?? '');
                        } else {
                            $nombre = 'Usuario no encontrado';
                            $email = '';
                        }
                    } else {
                        continue; // Saltar si no tiene externo_id
                    }
                    
                    if (!empty($nombre)) {
                        $participantesConNombres[] = [
                            'nombre' => $nombre,
                            'email' => $email,
                            'estado' => $participacion->estado ?? 'pendiente',
                            'fecha_inscripcion' => $participacion->created_at->format('d/m/Y H:i'),
                            'asistio' => $participacion->asistio ?? false
                        ];
                    }
                }

                // Participantes no registrados
                $participantesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                foreach ($participantesNoRegistrados as $participante) {
                    $nombre = trim(($participante->nombres ?? '') . ' ' . ($participante->apellidos ?? ''));
                    if (!empty($nombre)) {
                        $participantesConNombres[] = [
                            'nombre' => $nombre,
                            'email' => $participante->email ?? '',
                            'estado' => $participante->estado ?? 'pendiente',
                            'fecha_inscripcion' => $participante->created_at->format('d/m/Y H:i'),
                            'asistio' => $participante->asistio ?? false
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error obteniendo participantes con nombres: ' . $e->getMessage());
            }

            // Ruta de la imagen hoja.png
            $hojaPath = public_path('assets/img/hoja.png');
            $hojaExists = file_exists($hojaPath);

            // Generar PDF usando el facade
            $pdf = Pdf::loadView('ong.eventos.dashboard-pdf', [
                'evento' => $evento,
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
                'reacciones_con_nombres' => $reaccionesConNombres,
                'participantes_con_nombres' => $participantesConNombres,
                'hoja_path' => $hojaPath,
                'hoja_exists' => $hojaExists,
                'fecha_generacion' => now()->format('d/m/Y H:i:s')
            ])->setPaper('a4', 'portrait')
              ->setOption('enable-local-file-access', true)
              ->setOption('isRemoteEnabled', true);

            return $pdf->download('dashboard-evento-' . $evento->id . '-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Throwable $e) {
            \Log::error('Error generando PDF del dashboard:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
