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
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre_usuario',
        'correo_electronico',
        'contrasena',
        'tipo_usuario',
        'activo',
        'fecha_registro',
        'foto_perfil',
    ];

    protected $hidden = ['contrasena'];

    // Relaciones
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

    // Métodos de rol
    public function esOng()               { return $this->tipo_usuario === 'ONG'; }
    public function esEmpresa()           { return $this->tipo_usuario === 'Empresa'; }
    public function esIntegranteExterno() { return $this->tipo_usuario === 'Integrante externo'; }
    public function esSuperAdmin()        { return $this->tipo_usuario === 'Super admin'; }

    // Contraseña personalizada
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'id_usuario';
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
