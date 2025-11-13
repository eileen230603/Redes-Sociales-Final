<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_usuario',
        'correo_electronico',
        'contrasena',
        'tipo_usuario',
        'activo',
    ];

    protected $hidden = ['contrasena'];

    // Relación con ONG, Empresa e Integrante Externo
    public function ong()
    {
        return $this->hasOne(Ong::class, 'user_id', 'id_usuario');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'user_id', 'id_usuario');
    }

    public function integranteExterno()
    {
        return $this->hasOne(IntegranteExterno::class, 'user_id', 'id_usuario');
    }

    // Métodos de tipo de usuario
    public function esOng()               { return $this->tipo_usuario === 'ONG'; }
    public function esEmpresa()           { return $this->tipo_usuario === 'Empresa'; }
    public function esIntegranteExterno() { return $this->tipo_usuario === 'Integrante externo'; }
    public function esSuperAdmin()        { return $this->tipo_usuario === 'Super admin'; }

    // Laravel espera “password” como campo, lo adaptamos:
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}
