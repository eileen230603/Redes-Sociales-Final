<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoCompartido extends Model
{
    use HasFactory;

    protected $table = 'evento_compartidos';

    protected $fillable = [
        'evento_id',
        'externo_id',
        'nombres',
        'apellidos',
        'email',
        'metodo',
    ];

    public $timestamps = true;

    // Relación con Evento
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    // Relación con Usuario (externo) - nullable
    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}

