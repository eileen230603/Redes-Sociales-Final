<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    public function serve($path)
    {
        Log::info("StorageController: Intentando servir archivo: $path");
        
        // Normalizar la ruta y prevenir directory traversal
        $path = str_replace('..', '', $path);
        
        // Buscar primero en public/storage/ (donde Laravel web los tiene)
        $filePath = public_path('storage/' . $path);
        Log::info("StorageController: Buscando en: $filePath");
        
        // Si no existe, buscar en storage/app/public/ (ubicación principal)
        if (!file_exists($filePath) || !is_file($filePath)) {
            $filePath = storage_path('app/public/' . $path);
            Log::info("StorageController: No encontrado, buscando en: $filePath");
        }
        
        // Si aún no existe, intentar en la ubicación antigua (private/public) por compatibilidad
        if (!file_exists($filePath) || !is_file($filePath)) {
            $filePath = storage_path('app/private/public/' . $path);
            Log::info("StorageController: No encontrado, buscando en: $filePath");
        }
        
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            Log::warning("StorageController: Archivo no encontrado: $path");
            return response()->json(['error' => 'File not found', 'path' => $path], 404)
                ->header('Access-Control-Allow-Origin', '*');
        }
        
        Log::info("StorageController: Archivo encontrado, sirviendo: $filePath");
        $file = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
    
    public function options()
    {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}

