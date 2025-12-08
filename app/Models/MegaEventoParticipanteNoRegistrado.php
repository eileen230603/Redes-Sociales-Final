<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaEventoParticipanteNoRegistrado extends Model
{
    use HasFactory;

    protected $table = 'mega_evento_participantes_no_registrados';

    protected $fillable = [
        'mega_evento_id',
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

    public $timestamps = true;

    // Relación con MegaEvento
    public function megaEvento()
    {
        return $this->belongsTo(MegaEvento::class, 'mega_evento_id', 'mega_evento_id');
    }
}

