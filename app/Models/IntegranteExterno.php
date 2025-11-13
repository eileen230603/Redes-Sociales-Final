<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegranteExterno extends Model
{
    use HasFactory;

    protected $table = 'integrantes_externos';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'email',
        'phone_number',
        'descripcion',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }
}
