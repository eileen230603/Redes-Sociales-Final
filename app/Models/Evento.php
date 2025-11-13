<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'ong_id',
        'titulo',
        'descripcion',
        'tipo_evento',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_inscripcion',
        'capacidad_maxima',
        'inscripcion_abierta',
        'estado',
        'lat',
        'lng',
        'direccion',
        'ciudad',

        // JSON
        'imagenes',
        'patrocinadores',
        'auspiciadores',
        'invitados',
    ];

    protected $guarded = [];

    protected $casts = [
        // FECHAS
        'fecha_inicio'             => 'datetime',
        'fecha_fin'                => 'datetime',
        'fecha_limite_inscripcion' => 'datetime',

        // JSON ARRAYS
        'imagenes'        => 'array',
        'patrocinadores'  => 'array',
        'auspiciadores'   => 'array',
        'invitados'       => 'array',

        // BOOLEANOS
        'inscripcion_abierta' => 'boolean',
    ];

    // ===================================================
    // 🔥 MUTADORES – GARANTIZAN SIEMPRE JSON CORRECTO
    // ===================================================

    public function setPatrocinadoresAttribute($value)
    {
        $this->attributes['patrocinadores'] =
            is_string($value) ? $value : json_encode($value ?? []);
    }

    public function setAuspiciadoresAttribute($value)
    {
        $this->attributes['auspiciadores'] =
            is_string($value) ? $value : json_encode($value ?? []);
    }

    public function setInvitadosAttribute($value)
    {
        $this->attributes['invitados'] =
            is_string($value) ? $value : json_encode($value ?? []);
    }

    public function setImagenesAttribute($value)
    {
        $this->attributes['imagenes'] =
            is_string($value) ? $value : json_encode($value ?? []);
    }

    // ===================================================
    // 🔥 RELACIONES
    // ===================================================

    // ONG dueña del evento
    public function ong()
    {
        return $this->belongsTo(Ong::class, 'ong_id');
    }

    // Participaciones de usuarios externos
    public function participantes()
    {
        return $this->hasMany(EventoParticipacion::class, 'evento_id');
    }

    // Usuarios externos que participan en el evento
    public function externos()
    {
        return $this->belongsToMany(
            User::class,
            'evento_participaciones',
            'evento_id',
            'externo_id'
        );
    }
}
