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

    // Reacciones (favoritos)
    public function reacciones()
    {
        return $this->hasMany(EventoReaccion::class, 'evento_id');
    }

    // Usuarios que reaccionaron
    public function usuariosQueReaccionaron()
    {
        return $this->belongsToMany(
            User::class,
            'evento_reacciones',
            'evento_id',
            'externo_id',
            'id',
            'id_usuario'
        );
    }

    /**
     * Accessor para convertir rutas relativas de imágenes a URLs completas
     */
    public function getImagenesAttribute($value)
    {
        // Si $value es null o no es array, retornar array vacío
        if (!is_array($value)) {
            // Si es string, intentar decodificar JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = is_array($decoded) ? $decoded : [];
            } else {
                $value = [];
            }
        }

        // Generar URLs completas para cada imagen
        return array_map(function($imagen) {
            if (empty($imagen) || !is_string($imagen)) {
                return null;
            }

            // Si ya es una URL completa, retornarla
            if (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0) {
                return $imagen;
            }

            // Si empieza con /storage/, agregar el dominio (para Laravel web)
            if (strpos($imagen, '/storage/') === 0) {
                return url($imagen);
            }

            // Si empieza con storage/, agregar /storage/
            if (strpos($imagen, 'storage/') === 0) {
                return url('/storage/' . $imagen);
            }

            // Por defecto, asumir que es relativa a storage
            return url('/storage/' . ltrim($imagen, '/'));
        }, array_filter($value, function($img) {
            return !empty($img) && is_string($img);
        }));
    }
}
