<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MegaEventoParticipanteExterno extends Model
{
    protected $table = 'mega_evento_participantes_externos';
    
    // Clave primaria compuesta
    protected $primaryKey = ['mega_evento_id', 'integrante_externo_id'];
    public $incrementing = false;
    
    protected $fillable = [
        'mega_evento_id',
        'integrante_externo_id',
        'tipo_participacion',
        'habilidades_ofrecidas',
        'disponibilidad',
        'estado_participacion',
        'fecha_registro',
        'comentarios',
        'activo',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relación con MegaEvento
    public function megaEvento()
    {
        return $this->belongsTo(MegaEvento::class, 'mega_evento_id', 'mega_evento_id');
    }

    // Relación con IntegranteExterno
    public function integranteExterno()
    {
        return $this->belongsTo(IntegranteExterno::class, 'integrante_externo_id', 'user_id');
    }
}

