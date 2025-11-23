<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoEvento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estados_evento';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
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
     * Relación con eventos
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'estado_evento_id');
    }

    /**
     * Relación con mega eventos
     */
    public function megaEventos()
    {
        return $this->hasMany(MegaEvento::class, 'estado_evento_id');
    }

    /**
     * Scope para estados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where(function($q) use ($tipo) {
            $q->where('tipo', $tipo)
              ->orWhere('tipo', 'ambos');
        });
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
