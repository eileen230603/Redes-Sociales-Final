<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lugar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad_id',
        'lat',
        'lng',
        'capacidad',
        'descripcion',
        'telefono',
        'email',
        'sitio_web',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'capacidad' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    /**
     * Relación con ciudad
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    /**
     * Relación con eventos
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'lugar_id');
    }

    /**
     * Scope para lugares activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'ilike', "%{$termino}%")
            ->orWhere('direccion', 'ilike', "%{$termino}%");
    }
}
