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

        if (str_starts_with($this->foto_perfil, 'http')) {
            return $this->foto_perfil;
        }

        return asset('storage/' . ltrim($this->foto_perfil, '/'));
    }
}
