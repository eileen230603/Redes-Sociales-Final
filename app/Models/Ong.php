<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ong extends Model
{
    use HasFactory;

    protected $table = 'ongs';
    protected $primaryKey = 'user_id'; // ✔ ESTA ES LA PK REAL
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nombre_ong',
        'NIT',
        'telefono',
        'direccion',
        'sitio_web',
        'descripcion',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }
}
