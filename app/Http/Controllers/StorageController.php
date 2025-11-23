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
            Log::info("StorageController: Intentando servir archivo: $path");
            
            // Normalizar path para prevenir directory traversal
            $path = str_replace('..', '', $path);
            $path = ltrim($path, '/');
            
            // Intentar encontrar el archivo en diferentes ubicaciones
            $filePath = null;
            
            // 1. Primero intentar en public/storage/ (Laravel web)
            $filePath = public_path('storage/' . $path);
            if (!file_exists($filePath) || !is_file($filePath) || !is_readable($filePath)) {
                // 2. Intentar en storage/app/public/
                $filePath = storage_path('app/public/' . $path);
                if (!file_exists($filePath) || !is_file($filePath) || !is_readable($filePath)) {
                    // 3. Intentar en storage/app/private/public/ (compatibilidad)
                    $filePath = storage_path('app/private/public/' . $path);
                }
            }
            
            if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
                Log::warning("StorageController: Archivo no encontrado: $path");
                return response()->json(['error' => 'File not found'], 404)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type');
            }
            
            if (!is_readable($filePath)) {
                Log::error("StorageController: Archivo no tiene permisos de lectura: $filePath");
                return response()->json(['error' => 'Permission denied'], 403)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type');
            }
            
            Log::info("StorageController: Archivo encontrado y accesible: $filePath");
            
            $file = file_get_contents($filePath);
            if ($file === false) {
                Log::error("StorageController: No se pudo leer el archivo: $filePath");
                return response()->json(['error' => 'Could not read file'], 500)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type');
            }
            
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('X-Content-Type-Options', 'nosniff');
                
        } catch (\Exception $e) {
            Log::error("StorageController: Error al servir archivo: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type');
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

