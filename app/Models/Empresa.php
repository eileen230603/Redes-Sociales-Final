<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nombre_empresa',
        'NIT',
        'telefono',
        'direccion',
        'sitio_web',
        'descripcion',
        'foto_perfil',
    ];

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

        // Si ya es una URL completa, retornarla
        if (str_starts_with($this->foto_perfil, 'http://') || str_starts_with($this->foto_perfil, 'https://')) {
            return $this->foto_perfil;
        }

        // Obtener la URL base desde PUBLIC_APP_URL, APP_URL o del request actual
        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL'));
        
        // Si no hay baseUrl configurada, intentar obtenerla del request actual
        if (empty($baseUrl) && app()->runningInConsole() === false) {
            try {
                $request = request();
                if ($request) {
                    $baseUrl = $request->getSchemeAndHttpHost();
                }
            } catch (\Exception $e) {
                // Si falla, usar asset()
            }
        }
        
        // Si aún no hay baseUrl, usar asset() como fallback
        if (empty($baseUrl)) {
        return asset('storage/' . ltrim($this->foto_perfil, '/'));
        }

        // Normalizar la ruta
        if (str_starts_with($this->foto_perfil, '/storage/')) {
            return rtrim($baseUrl, '/') . $this->foto_perfil;
        } elseif (str_starts_with($this->foto_perfil, 'storage/')) {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, 'storage/');
        } else {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_perfil, '/');
        }
    }
}
