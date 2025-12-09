<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    /**
     * Constructor - Sin middleware de autenticación
     */
    public function __construct()
    {
        // No aplicar middleware de autenticación para acceso público
    }
    
    /**
     * Servir archivos de storage con CORS habilitado
     * Sin autenticación requerida para acceso público
     */
    public function serve($path)
    {
        try {
            // Guardar path original para logging
            $originalPath = $path;
            
            // Normalizar path para prevenir directory traversal
            $path = str_replace('..', '', $path);
            $path = ltrim($path, '/');
            
            // Decodificar URL encoding si existe
            $path = urldecode($path);
            
            // Si el path viene como URL completa, extraer solo la parte del path
            if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
                $parsed = parse_url($path);
                if (isset($parsed['path'])) {
                    $path = ltrim($parsed['path'], '/');
                    // Quitar "storage/" si está presente en el path
                    if (strpos($path, 'storage/') === 0) {
                        $path = substr($path, 8);
                    }
                }
            }
            
            // Si el path empieza con "storage/", quitarlo
            if (strpos($path, 'storage/') === 0) {
                $path = substr($path, 8);
            }
            
            // Limpiar query strings si existen (como ?_t=timestamp)
            if (($pos = strpos($path, '?')) !== false) {
                $path = substr($path, 0, $pos);
            }
            
            Log::info("StorageController: Intentando servir archivo (path procesado): $path");
            
            // Filtrar rutas que son claramente URLs externas mal formateadas
            $rutasExternasInvalidas = ['resizer/', '/resizer/', 'wp-content/', '/wp-content/', 
                                       'templates/', '/templates/', 'yootheme/', '/yootheme/'];
            foreach ($rutasExternasInvalidas as $rutaInvalida) {
                if (stripos($path, $rutaInvalida) !== false) {
                    Log::warning("StorageController: Ruta externa inválida detectada, rechazando: $path");
                    return response('Invalid path: external URL path detected', 404)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                        ->header('Content-Type', 'text/plain');
                }
            }
            
            // Intentar encontrar el archivo en diferentes ubicaciones
            $filePath = null;
            
            // El path ya está normalizado arriba, usar directamente
            $normalizedPath = $path;
            
            // Si el path contiene solo el nombre del archivo (sin directorio), buscar en subdirectorios comunes
            $searchPaths = [$normalizedPath];
            $isOnlyFilename = (strpos($normalizedPath, '/') === false);
            
            if ($isOnlyFilename) {
                // Es solo un nombre de archivo, buscar en subdirectorios comunes
                $commonDirs = ['eventos', 'mega_eventos', 'perfiles', 'empresas', 'ongs'];
                foreach ($commonDirs as $dir) {
                    $searchPaths[] = $dir . '/' . $normalizedPath;
                }
                
                // Buscar específicamente en subdirectorios de eventos (eventos/{id}/archivo)
                $eventosDir = storage_path('app/public/eventos');
                if (is_dir($eventosDir)) {
                    try {
                        $subdirs = glob($eventosDir . '/*', GLOB_ONLYDIR);
                        if ($subdirs) {
                            foreach ($subdirs as $subdir) {
                                $subdirName = basename($subdir);
                                $searchPaths[] = "eventos/{$subdirName}/{$normalizedPath}";
                            }
                            Log::info("StorageController: Buscando en " . count($subdirs) . " subdirectorios de eventos");
                        }
                    } catch (\Exception $e) {
                        Log::warning("StorageController: Error buscando subdirectorios de eventos: " . $e->getMessage());
                    }
                }
            }
            
            $locations = [];
            foreach ($searchPaths as $searchPath) {
                // Priorizar storage/app/public sobre public/storage (más confiable)
                $locations[] = storage_path('app/public/' . $searchPath);
                $locations[] = public_path('storage/' . $searchPath);
                $locations[] = storage_path('app/private/public/' . $searchPath);
            }
            
            // También intentar con el path original si es diferente
            if ($path !== $normalizedPath) {
                $locations[] = storage_path('app/public/' . $path);
                $locations[] = public_path('storage/' . $path);
            }
            
            foreach ($locations as $location) {
                if (file_exists($location) && is_file($location)) {
                    // Verificar permisos
                    if (is_readable($location)) {
                        $filePath = $location;
                        Log::info("StorageController: Archivo encontrado en: $filePath");
                        break;
                    } else {
                        // Intentar cambiar permisos temporalmente
                        @chmod($location, 0644);
                        if (is_readable($location)) {
                            $filePath = $location;
                            Log::info("StorageController: Archivo encontrado (permisos corregidos) en: $filePath");
                            break;
                        } else {
                            // Intentar con permisos más permisivos
                            @chmod($location, 0666);
                            if (is_readable($location)) {
                                $filePath = $location;
                                Log::info("StorageController: Archivo encontrado (permisos 0666) en: $filePath");
                                break;
                            }
                        }
                    }
                } else {
                    // Log para debug - solo si no es el último intento
                    if ($location === end($locations)) {
                        Log::debug("StorageController: Archivo no existe en: $location");
                    }
                }
            }
            
            if (!$filePath) {
                // Intentar usar Storage facade como último recurso
                try {
                    // Probar con diferentes paths
                    $storagePaths = array_merge([$normalizedPath, $path], $searchPaths);
                    foreach ($storagePaths as $storagePath) {
                        if (Storage::disk('public')->exists($storagePath)) {
                            $filePath = Storage::disk('public')->path($storagePath);
                            Log::info("StorageController: Archivo encontrado usando Storage facade: $filePath");
                            break;
                        }
                    }
                    
                    // Si aún no se encuentra y es solo un nombre de archivo, buscar recursivamente
                    if (!$filePath && $isOnlyFilename) {
                        $basePaths = [
                            storage_path('app/public'),
                            public_path('storage')
                        ];
                        
                        foreach ($basePaths as $basePath) {
                            if (!is_dir($basePath)) {
                                Log::info("StorageController: Directorio no existe: $basePath");
                                continue;
                            }
                            
                            try {
                                // Buscar recursivamente pero limitar la profundidad para mejor rendimiento
                                $iterator = new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                                    \RecursiveIteratorIterator::SELF_FIRST,
                                    \RecursiveIteratorIterator::CATCH_GET_CHILD
                                );
                                
                                $found = false;
                                foreach ($iterator as $file) {
                                    if ($file->isFile()) {
                                        // Comparar nombre de archivo (case-insensitive para Windows)
                                        if (strcasecmp($file->getFilename(), $normalizedPath) === 0) {
                                            $filePath = $file->getRealPath();
                                            Log::info("StorageController: Archivo encontrado recursivamente en: $filePath");
                                            $found = true;
                                            break;
                                        }
                                    }
                                }
                                
                                if ($found) {
                                    break; // Salir del loop de basePaths
                                }
                            } catch (\Exception $iterEx) {
                                Log::warning("StorageController: Error en búsqueda recursiva en $basePath: " . $iterEx->getMessage());
                            }
                        }
                    }
                } catch (\Exception $storageEx) {
                    Log::warning("StorageController: Error usando Storage facade: " . $storageEx->getMessage());
                }
                
                if (!$filePath) {
                    Log::warning("StorageController: Archivo no encontrado en ninguna ubicación: $path");
                    Log::warning("StorageController: Path normalizado: $normalizedPath");
                    Log::warning("StorageController: Paths de búsqueda: " . implode(', ', $searchPaths));
                }
            }
            
            if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
                // Intentar buscar el archivo por nombre en todas las carpetas de mega_eventos
                if (strpos($normalizedPath, 'mega_eventos/') !== false || strpos($normalizedPath, 'mega_eventos') !== false) {
                    $megaEventosDir = storage_path('app/public/mega_eventos');
                    if (is_dir($megaEventosDir)) {
                        $fileName = basename($normalizedPath);
                        $iterator = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator($megaEventosDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                            \RecursiveIteratorIterator::SELF_FIRST
                        );
                        
                        foreach ($iterator as $file) {
                            if ($file->isFile() && $file->getFilename() === $fileName) {
                                $filePath = $file->getRealPath();
                                Log::info("StorageController: Archivo encontrado por búsqueda recursiva: $filePath");
                                break;
                            }
                        }
                    }
                }
                
                if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
                    Log::error("StorageController: Archivo no encontrado después de todas las búsquedas");
                    Log::error("StorageController: Path recibido: $path");
                    Log::error("StorageController: Path normalizado: $normalizedPath");
                    Log::error("StorageController: Total paths de búsqueda probados: " . count($searchPaths));
                    Log::error("StorageController: Total ubicaciones probadas: " . count($locations));
                    
                    // Listar algunos archivos existentes para debug
                    $sampleDir = storage_path('app/public/mega_eventos');
                    if (is_dir($sampleDir)) {
                        try {
                            $sampleFiles = glob($sampleDir . '/*/*.{jpeg,jpg,png}', GLOB_BRACE);
                            if ($sampleFiles && count($sampleFiles) > 0) {
                                $sampleNames = array_slice(array_map('basename', $sampleFiles), 0, 5);
                                Log::info("StorageController: Archivos de ejemplo encontrados en mega_eventos: " . implode(', ', $sampleNames));
                            } else {
                                Log::info("StorageController: No se encontraron archivos de ejemplo en mega_eventos/");
                            }
                        } catch (\Exception $e) {
                            Log::warning("StorageController: Error listando archivos de ejemplo: " . $e->getMessage());
                        }
                    }
                    
                    return response('File not found: ' . $normalizedPath, 404)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                        ->header('Content-Type', 'text/plain');
                }
            }
            
            // Intentar leer el archivo directamente (funciona mejor en Windows que is_readable)
            $fileContent = @file_get_contents($filePath);
            
            if ($fileContent === false) {
                // Si no se pudo leer, intentar cambiar permisos
                $filePerms = fileperms($filePath);
                $currentPerms = substr(sprintf('%o', $filePerms), -4);
                
                Log::warning("StorageController: No se pudo leer el archivo: $filePath");
                Log::warning("StorageController: Permisos actuales: $currentPerms");
                
                // Intentar cambiar permisos (lectura para todos)
                $newPerms = 0644; // rw-r--r--
                if (@chmod($filePath, $newPerms)) {
                    Log::info("StorageController: Permisos cambiados a 0644 para: $filePath");
                    $fileContent = @file_get_contents($filePath);
                }
                
                if ($fileContent === false) {
                    // Intentar con permisos más permisivos
                    $newPerms = 0666; // rw-rw-rw-
                    if (@chmod($filePath, $newPerms)) {
                        Log::info("StorageController: Permisos cambiados a 0666 para: $filePath");
                        $fileContent = @file_get_contents($filePath);
                    }
                }
                
                if ($fileContent === false) {
                    Log::error("StorageController: No se pudo leer el archivo después de cambiar permisos: $filePath");
                    return response('Permission denied', 403)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                        ->header('Content-Type', 'text/plain');
                }
            }
            
            // Determinar MIME type
            $mimeType = 'application/octet-stream';
            if (function_exists('mime_content_type')) {
                $mimeType = mime_content_type($filePath) ?: $mimeType;
            } elseif (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filePath) ?: $mimeType;
                finfo_close($finfo);
            } else {
                // Fallback basado en extensión
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf',
                ];
                $mimeType = $mimeTypes[$extension] ?? $mimeType;
            }
            
            // Usar el contenido ya leído
            $file = $fileContent;
            
            Log::info("StorageController: Archivo servido exitosamente: $filePath (MIME: $mimeType, Tamaño: " . strlen($file) . " bytes)");
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('Content-Length', strlen($file));
                
        } catch (\Exception $e) {
            Log::error("StorageController: Error al servir archivo: " . $e->getMessage());
            Log::error("StorageController: Stack trace: " . $e->getTraceAsString());
            return response('Server error: ' . $e->getMessage(), 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Content-Type', 'text/plain');
        }
    }
    
    /**
     * Manejar preflight OPTIONS requests
     */
    public function options()
    {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->header('Access-Control-Max-Age', '86400');
    }
}

