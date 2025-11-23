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
        if (!$this->foto_perfil) {
            return null;
        }

        // Si ya es una URL completa, retornarla
        if (str_starts_with($this->foto_perfil, 'http://') || str_starts_with($this->foto_perfil, 'https://')) {
            return $this->foto_perfil;
        }

        // Si empieza con /storage/, agregar el dominio (para Laravel web)
        if (str_starts_with($this->foto_perfil, '/storage/')) {
            return url($this->foto_perfil);
        }

        // Si empieza con storage/, agregar /storage/
        if (str_starts_with($this->foto_perfil, 'storage/')) {
            return url('/storage/' . $this->foto_perfil);
        }

        // Por defecto, asumir que es relativa a storage
        return url('/storage/' . ltrim($this->foto_perfil, '/'));
    }
}
