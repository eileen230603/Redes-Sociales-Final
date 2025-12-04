<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoParticipacion extends Model
{
    protected $table = 'evento_participaciones';

    protected $fillable = [
        'evento_id',
        'externo_id',
        'estado',
        'asistio',
        'puntos',
        'ticket_codigo',
        'checkin_at',
        'checkout_at',
        'modo_asistencia',
        'observaciones',
        'registrado_por',
        'estado_asistencia',
        'estado_participacion_id',
    ];

    protected $casts = [
        'asistio' => 'boolean',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    protected $attributes = [
        'asistio' => false,
        'puntos' => 0,
        'estado' => 'pendiente',
        'estado_asistencia' => 'no_asistido',
    ];

    /**
     * Boot del modelo para establecer valores por defecto
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participacion) {
            // Establecer estado_asistencia por defecto si no está definido
            if (is_null($participacion->estado_asistencia)) {
                $participacion->estado_asistencia = 'no_asistido';
            }
            
            // Establecer estado por defecto si no está definido
            if (is_null($participacion->estado)) {
                $participacion->estado = 'pendiente';
            }
        });
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}
