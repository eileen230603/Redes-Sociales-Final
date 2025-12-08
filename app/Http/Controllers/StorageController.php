<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    /**
     * Constructor - sin middleware de autenticación
     */
    public function __construct()
    {
        // Acceso público
    }

    /**
     * Respuesta OPTIONS para CORS
     */
    public function options($path)
    {
        return response('')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Servir archivos de storage con CORS habilitado
     */
    public function serve($path)
    {
        try {
            Log::info("StorageController (mobile): Intentando servir archivo: $path");

            $path = str_replace('..', '', $path);
            $path = ltrim($path, '/');

            $filePath = null;

            // 1. public/storage
            $filePath = public_path('storage/' . $path);
            if (!file_exists($filePath) || !is_file($filePath) || !is_readable($filePath)) {
                // 2. storage/app/public
                $filePath = storage_path('app/public/' . $path);
                if (!file_exists($filePath) || !is_file($filePath) || !is_readable($filePath)) {
                    // 3. storage/app/private/public (compatibilidad)
                    $filePath = storage_path('app/private/public/' . $path);
                }
            }

            if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
                Log::warning("StorageController (mobile): Archivo no encontrado: $path");
                return response()->json(['error' => 'File not found'], 404)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            if (!is_readable($filePath)) {
                Log::error("StorageController (mobile): Archivo sin permisos de lectura: $filePath");
                return response()->json(['error' => 'Permission denied'], 403)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            $file = file_get_contents($filePath);
            if ($file === false) {
                Log::error("StorageController (mobile): No se pudo leer el archivo: $filePath");
                return response()->json(['error' => 'Could not read file'], 500)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Cache-Control', 'public, max-age=31536000');

        } catch (\Throwable $e) {
            Log::error("StorageController (mobile): Error al servir archivo: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }
    }
}


