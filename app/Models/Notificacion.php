<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'ong_id',
        'evento_id',
        'externo_id',
        'empresa_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    public $timestamps = true;

    // Relación con ONG
    public function ong()
    {
        return $this->belongsTo(User::class, 'ong_id', 'id_usuario');
    }

    // Relación con Evento
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    // Relación con Usuario Externo
    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}
