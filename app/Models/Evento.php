<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    // Usamos guarded para permitir todos los campos
    protected $guarded = [];

    protected $casts = [
        'fecha_inicio'             => 'datetime',
        'fecha_fin'                => 'datetime',
        'fecha_limite_inscripcion' => 'datetime',
        'imagenes'                 => 'array',
        'patrocinadores'           => 'array',
        'auspiciadores'            => 'array',
        'invitados'                => 'array',
        'inscripcion_abierta'      => 'boolean',
    ];

    // ONG propietaria del evento
    public function ong()
    {
        return $this->belongsTo(Ong::class, 'ong_id', 'user_id');
    }

    // Participantes inscritos
    public function participantes()
    {
        return $this->hasMany(EventoParticipacion::class, 'evento_id');
    }

    // Usuarios externos
    public function externos()
    {
        return $this->belongsToMany(
            User::class,
            'evento_participaciones',
            'evento_id',     // FK en pivot
            'externo_id',    // FK en pivot
            'id',            // PK de eventos
            'id_usuario'     // PK de usuarios
        );
    }
}
