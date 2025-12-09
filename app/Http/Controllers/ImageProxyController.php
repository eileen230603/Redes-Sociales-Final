<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ImageProxyController extends Controller
{
    /**
     * Proxy para servir imágenes externas y evitar problemas de CORS
     */
    public function proxy(Request $request)
    {
        try {
            // Obtener la URL de la imagen desde el parámetro
            $imageUrl = $request->query('url');
            
            if (empty($imageUrl)) {
                return response()->json(['error' => 'URL no proporcionada'], 400);
            }
            
            // Validar que sea una URL válida
            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                return response()->json(['error' => 'URL inválida'], 400);
            }
            
            // Validar que sea http o https
            $parsedUrl = parse_url($imageUrl);
            if (!in_array($parsedUrl['scheme'] ?? '', ['http', 'https'])) {
                return response()->json(['error' => 'Solo se permiten URLs HTTP/HTTPS'], 400);
            }
            
            // Cache key basado en la URL
            $cacheKey = 'image_proxy_' . md5($imageUrl);
            
            // Intentar obtener de cache (cache por 1 hora)
            $cachedImage = Cache::get($cacheKey);
            if ($cachedImage) {
                return response($cachedImage['content'], 200)
                    ->header('Content-Type', $cachedImage['content_type'])
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            // Descargar la imagen
            $response = Http::timeout(10)->get($imageUrl);
            
            if (!$response->successful()) {
                Log::warning('Error al descargar imagen desde proxy', [
                    'url' => $imageUrl,
                    'status' => $response->status()
                ]);
                return response()->json(['error' => 'No se pudo descargar la imagen'], 404);
            }
            
            $imageContent = $response->body();
            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            
            // Validar que sea realmente una imagen
            if (strpos($contentType, 'image/') !== 0) {
                // Intentar detectar el tipo por el contenido
                $imageInfo = @getimagesizefromstring($imageContent);
                if ($imageInfo === false) {
                    return response()->json(['error' => 'El archivo no es una imagen válida'], 400);
                }
                $contentType = 'image/' . str_replace('jpeg', 'jpg', $imageInfo['mime'] ?? 'jpeg');
            }
            
            // Guardar en cache por 1 hora
            Cache::put($cacheKey, [
                'content' => $imageContent,
                'content_type' => $contentType
            ], now()->addHour());
            
            // Retornar la imagen con headers CORS
            return response($imageContent, 200)
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type')
                ->header('Cache-Control', 'public, max-age=3600');
                
        } catch (\Exception $e) {
            Log::error('Error en proxy de imágenes', [
                'url' => $imageUrl ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Error al procesar la imagen'], 500);
        }
    }
    
    /**
     * Manejar preflight OPTIONS request
     */
    public function options()
    {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}

