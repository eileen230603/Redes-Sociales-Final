<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoParticipanteNoRegistrado extends Model
{
    protected $table = 'evento_participantes_no_registrados';

    protected $fillable = [
        'evento_id',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'estado',
        'asistio',
    ];

    protected $casts = [
        'asistio' => 'boolean',
    ];

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
