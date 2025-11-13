<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaEvento extends Model
{
    use HasFactory;

    protected $table = 'mega_eventos';
    protected $primaryKey = 'MegaEventoID';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'categoria',
        'estado',
        'ong_organizadora_principal',
        'capacidad_maxima',
        'es_publico',
        'activo'
    ];

    public function ongPrincipal()
    {
        return $this->belongsTo(Ong::class, 'ong_organizadora_principal', 'id_usuario');
    }
}
