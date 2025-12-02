<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoReaccion extends Model
{
    use HasFactory;

    protected $table = 'evento_reacciones';

    protected $fillable = [
        'evento_id',
        'externo_id',
        'nombres',
        'apellidos',
        'email',
    ];

    public $timestamps = true;

    // Relación con Evento
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    // Relación con Usuario (externo)
    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}
