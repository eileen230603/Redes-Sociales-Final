<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoUsuario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_usuario';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'permisos_default',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'permisos_default' => 'array',
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'tipo_usuario_id');
    }

    /**
     * Scope para tipos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
