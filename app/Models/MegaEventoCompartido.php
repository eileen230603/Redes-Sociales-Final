<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaEventoCompartido extends Model
{
    use HasFactory;

    protected $table = 'mega_evento_compartidos';

    protected $fillable = [
        'mega_evento_id',
        'externo_id',
        'nombres',
        'apellidos',
        'email',
        'metodo',
    ];

    public $timestamps = true;

    // Relación con MegaEvento
    public function megaEvento()
    {
        return $this->belongsTo(MegaEvento::class, 'mega_evento_id', 'mega_evento_id');
    }

    // Relación con Usuario (externo) - nullable
    public function externo()
    {
        return $this->belongsTo(User::class, 'externo_id', 'id_usuario');
    }
}
