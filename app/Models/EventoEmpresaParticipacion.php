<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoEmpresaParticipacion extends Model
{
    protected $table = 'evento_empresas_participantes';

    protected $fillable = [
        'evento_id',
        'empresa_id',
        'estado',
        'asistio',
        'tipo_colaboracion',
        'descripcion_colaboracion',
        'activo',
    ];

    protected $casts = [
        'asistio' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    /**
     * Relación con la empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'user_id');
    }

    /**
     * Relación con el usuario de la empresa
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'empresa_id', 'id_usuario');
    }
}

