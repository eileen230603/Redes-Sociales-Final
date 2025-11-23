<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoParticipacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estados_participacion';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'color',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Relación con participaciones
     */
    public function participaciones()
    {
        return $this->hasMany(EventoParticipacion::class, 'estado_participacion_id');
    }

    /**
     * Scope para estados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
