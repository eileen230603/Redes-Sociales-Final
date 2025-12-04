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

        // Obtener la URL base desde PUBLIC_APP_URL o APP_URL
        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL', 'http://192.168.0.6:8000'));
        
        // Generar URLs completas para cada imagen
        return array_map(function($imagen) use ($baseUrl) {
            if (empty($imagen) || !is_string($imagen)) {
                return null;
            }

            // Si ya es una URL completa, retornarla
            if (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0) {
                return $imagen;
            }

            // Si empieza con /storage/, agregar el dominio base
            if (strpos($imagen, '/storage/') === 0) {
                return rtrim($baseUrl, '/') . $imagen;
            }

            // Si empieza con storage/, agregar /storage/
            if (strpos($imagen, 'storage/') === 0) {
                return rtrim($baseUrl, '/') . '/storage/' . $imagen;
            }

            // Por defecto, asumir que es relativa a storage
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($imagen, '/');
        }, array_filter($value, function($img) {
            return !empty($img) && is_string($img);
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
