<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciudad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'codigo_postal',
        'departamento',
        'pais',
        'lat',
        'lng',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    /**
     * Relación con lugares
     */
    public function lugares()
    {
        return $this->hasMany(Lugar::class, 'ciudad_id');
    }

    /**
     * Relación con eventos
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'ciudad_id');
    }

    /**
     * Scope para ciudades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'ilike', "%{$termino}%")
            ->orWhere('departamento', 'ilike', "%{$termino}%");
    }
}
