<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaEvento extends Model
{
    use HasFactory;

    protected $table = 'mega_eventos';
    protected $primaryKey = 'mega_evento_id';
    public $timestamps = false; // Usamos fecha_creacion y fecha_actualizacion manualmente

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'lat',
        'lng',
        'fecha_creacion',
        'fecha_actualizacion',
        'categoria',
        'estado',
        'ong_organizadora_principal',
        'capacidad_maxima',
        'es_publico',
        'activo',
        'imagenes'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'activo' => 'boolean',
        'es_publico' => 'boolean',
        'imagenes' => 'array',
    ];

    /**
     * Accessor para convertir rutas relativas de imágenes a URLs completas
     */
    public function getImagenesAttribute($value)
    {
        // Si $value es null o no es array, retornar array vacío
        if (!is_array($value)) {
            // Si es string, intentar decodificar JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = is_array($decoded) ? $decoded : [];
            } else {
                $value = [];
            }
        }

        // Obtener la URL base desde PUBLIC_APP_URL, APP_URL o del request actual
        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL'));
        
        // Si no hay baseUrl configurada, intentar obtenerla del request actual
        if (empty($baseUrl) && app()->runningInConsole() === false) {
            try {
                $request = request();
                if ($request) {
                    // Priorizar el header Origin si existe
                    $origin = $request->header('Origin');
                    if ($origin) {
                        $baseUrl = $origin;
                    } else {
                        $baseUrl = $request->getSchemeAndHttpHost();
                    }
                }
            } catch (\Exception $e) {
                // Si falla, usar el valor por defecto
            }
        }
        
        // Si aún no hay baseUrl, usar un valor por defecto
        if (empty($baseUrl)) {
            $baseUrl = 'http://10.26.0.215:8000';
        }
        
        // Generar URLs completas para cada imagen
        $resultado = array_map(function($imagen) use ($baseUrl) {
            if (empty($imagen) || !is_string($imagen)) {
                return null;
            }

            // Filtrar rutas inválidas (templates, cache, yootheme, resizer, wp-content) - solo para rutas locales
            $esUrlExterna = (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0);
            if (!$esUrlExterna) {
                if (strpos($imagen, '/templates/') !== false || 
                    strpos($imagen, '/cache/') !== false || 
                    strpos($imagen, '/yootheme/') !== false ||
                    strpos($imagen, '/resizer/') !== false ||
                    strpos($imagen, '/wp-content/') !== false ||
                    strpos($imagen, 'templates/') !== false || 
                    strpos($imagen, 'cache/') !== false || 
                    strpos($imagen, 'yootheme/') !== false ||
                    strpos($imagen, 'resizer/') !== false ||
                    strpos($imagen, 'wp-content/') !== false) {
                    return null;
                }
            }

            // Si ya es una URL completa, verificar si necesita actualización del host
            if ($esUrlExterna) {
                // Reemplazar IPs antiguas explícitamente
                $imagen = str_replace('http://127.0.0.1:8000', $baseUrl, $imagen);
                $imagen = str_replace('https://127.0.0.1:8000', $baseUrl, $imagen);
                $imagen = str_replace('http://192.168.0.6:8000', $baseUrl, $imagen);
                $imagen = str_replace('https://192.168.0.6:8000', $baseUrl, $imagen);
                $imagen = str_replace('http://10.26.15.110:8000', $baseUrl, $imagen);
                $imagen = str_replace('https://10.26.15.110:8000', $baseUrl, $imagen);
                
                $parsedUrl = parse_url($imagen);
                $currentHost = parse_url($baseUrl, PHP_URL_HOST);
                
                // Si el host de la imagen es diferente al origen actual, actualizarlo
                if (isset($parsedUrl['host']) && $parsedUrl['host'] !== $currentHost) {
                    // Si es una URL externa de internet, mantenerla (no es del mismo dominio)
                    if ($parsedUrl['host'] !== 'localhost' && 
                        $parsedUrl['host'] !== '127.0.0.1' && 
                        !str_starts_with($parsedUrl['host'], '192.168.') &&
                        strpos($parsedUrl['host'], '192.168.') !== 0 &&
                        strpos($parsedUrl['host'], '10.26.') !== 0) {
                        // Es una URL externa de internet, retornarla tal cual
                        return $imagen;
                    }
                    
                    // Es una IP local antigua, actualizarla
                    $parsedUrl['scheme'] = parse_url($baseUrl, PHP_URL_SCHEME) ?? 'http';
                    $parsedUrl['host'] = $currentHost;
                    $parsedUrl['port'] = parse_url($baseUrl, PHP_URL_PORT);
                    
                    $imagen = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] 
                        . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') 
                        . ($parsedUrl['path'] ?? '');
                }
                return $imagen;
            }

            // Si empieza con /storage/, agregar el dominio base
            if (strpos($imagen, '/storage/') === 0) {
                return rtrim($baseUrl, '/') . $imagen;
            }

            // Si empieza con storage/, agregar /storage/
            if (strpos($imagen, 'storage/') === 0) {
                return rtrim($baseUrl, '/') . '/' . $imagen;
            }

            // Por defecto, asumir que es relativa a storage
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($imagen, '/');
        }, array_filter($value, function($img) {
            return !empty($img) && is_string($img);
        }));
        
        // Filtrar valores null después del mapeo
        return array_values(array_filter($resultado, function($img) {
            return $img !== null && !empty($img);
        }));
    }

    public function ongPrincipal()
    {
        return $this->belongsTo(Ong::class, 'ong_organizadora_principal', 'user_id');
    }

    /**
     * Boot del modelo para actualizar fecha_actualizacion automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($megaEvento) {
            if (empty($megaEvento->fecha_creacion)) {
                $megaEvento->fecha_creacion = now();
            }
            if (empty($megaEvento->fecha_actualizacion)) {
                $megaEvento->fecha_actualizacion = now();
            }
        });

        static::updating(function ($megaEvento) {
            $megaEvento->fecha_actualizacion = now();
        });
    }
}
