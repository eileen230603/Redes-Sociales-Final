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

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    /**
     * Accessor para obtener la URL completa de la foto de perfil
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

        // Si es una ruta relativa, construir la URL completa
        if (str_starts_with($this->foto_perfil, '/storage/')) {
            return url($this->foto_perfil);
        }

        return url('storage/' . $this->foto_perfil);
    }
}
