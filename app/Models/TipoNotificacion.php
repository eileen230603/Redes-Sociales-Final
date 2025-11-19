<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoNotificacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_notificacion';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plantilla_mensaje',
        'icono',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con notificaciones
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'tipo_notificacion_id');
    }

    /**
     * Scope para tipos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Procesar plantilla de mensaje con variables
     */
    public function procesarPlantilla($variables = [])
    {
        $mensaje = $this->plantilla_mensaje ?? $this->nombre;
        
        foreach ($variables as $key => $value) {
            $mensaje = str_replace('{' . $key . '}', $value, $mensaje);
        }
        
        return $mensaje;
    }
}
