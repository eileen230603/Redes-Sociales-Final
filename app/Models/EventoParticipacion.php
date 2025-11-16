<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoParticipacion extends Model
{
    protected $table = 'evento_participaciones';

    protected $fillable = [
        'evento_id',
        'externo_id',
        'asistio',
        'puntos',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}
