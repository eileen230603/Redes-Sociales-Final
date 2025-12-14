<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ong extends Model
{
    use HasFactory;

    protected $table = 'ongs';
    protected $primaryKey = 'user_id'; // ← importante
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nombre_ong',
        'NIT',
        'telefono',
        'direccion',
        'sitio_web',
        'descripcion',
        'foto_perfil',
    ];

    /**
     * Los accessors que se incluirán automáticamente en la serialización JSON
     */
    protected $appends = ['foto_perfil_url'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    /**
     * Accessor para convertir rutas relativas de foto de perfil a URLs completas
     */
    public function getFotoPerfilUrlAttribute()
    {
        if (!$this->foto_perfil) return null;

        // Si ya es una URL completa, normalizarla
        if (str_starts_with($this->foto_perfil, 'http://') || str_starts_with($this->foto_perfil, 'https://')) {
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
                $baseUrl = 'http://192.168.0.7:8000';
            }
            
            // Reemplazar IPs antiguas con la URL actual
            $fotoPerfil = str_replace('http://192.168.0.6:8000', $baseUrl, $this->foto_perfil);
            $fotoPerfil = str_replace('https://192.168.0.6:8000', $baseUrl, $fotoPerfil);
            $fotoPerfil = str_replace('http://127.0.0.1:8000', $baseUrl, $fotoPerfil);
            $fotoPerfil = str_replace('https://127.0.0.1:8000', $baseUrl, $fotoPerfil);
            $fotoPerfil = str_replace('http://192.168.0.7:8000', $baseUrl, $fotoPerfil);
            $fotoPerfil = str_replace('https://192.168.0.7:8000', $baseUrl, $fotoPerfil);
            
            return $fotoPerfil;
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
            $baseUrl = 'http://192.168.0.7:8000';
        }

        // Normalizar la ruta
        if (str_starts_with($this->foto_perfil, '/storage/')) {
            return rtrim($baseUrl, '/') . $this->foto_perfil;
        } elseif (str_starts_with($this->foto_perfil, 'storage/')) {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, 'storage/');
        }

        return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, '/');
    }

    /**
     * Accessor para obtener la URL del logo de la ONG
     * Compatible con DomPDF para exportación de PDFs
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->foto_perfil) {
            return null;
        }

        // Si ya es una URL completa válida, retornarla
        if (filter_var($this->foto_perfil, FILTER_VALIDATE_URL)) {
            return $this->foto_perfil;
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
            $baseUrl = 'http://192.168.0.7:8000';
        }

        // Normalizar la ruta
        if (str_starts_with($this->foto_perfil, '/storage/')) {
            return rtrim($baseUrl, '/') . $this->foto_perfil;
        } elseif (str_starts_with($this->foto_perfil, 'storage/')) {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, 'storage/');
        }

        return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, '/');
    }
}
