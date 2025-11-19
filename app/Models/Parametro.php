<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parametro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parametros';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'tipo',
        'valor',
        'valor_defecto',
        'opciones',
        'grupo',
        'orden',
        'editable',
        'visible',
        'requerido',
        'validacion',
        'ayuda',
    ];

    protected $casts = [
        'opciones' => 'array',
        'editable' => 'boolean',
        'visible' => 'boolean',
        'requerido' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Obtener el valor del parámetro con conversión de tipo
     */
    public function getValorFormateadoAttribute()
    {
        $valor = $this->valor ?? $this->valor_defecto;

        switch ($this->tipo) {
            case 'numero':
                return is_numeric($valor) ? (float)$valor : 0;
            case 'booleano':
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return is_string($valor) ? json_decode($valor, true) : $valor;
            case 'fecha':
                return $valor ? date('Y-m-d', strtotime($valor)) : null;
            default:
                return $valor;
        }
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopePorGrupo($query, $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    /**
     * Scope para parámetros visibles
     */
    public function scopeVisibles($query)
    {
        return $query->where('visible', true);
    }

    /**
     * Scope para parámetros editables
     */
    public function scopeEditables($query)
    {
        return $query->where('editable', true);
    }

    /**
     * Obtener parámetro por código (helper estático)
     */
    public static function obtener($codigo, $default = null)
    {
        $parametro = self::where('codigo', $codigo)->first();
        
        if (!$parametro) {
            return $default;
        }

        return $parametro->valor_formateado ?? $default;
    }

    /**
     * Establecer valor de parámetro por código
     */
    public static function establecer($codigo, $valor)
    {
        $parametro = self::where('codigo', $codigo)->first();
        
        if (!$parametro) {
            return false;
        }

        if (!$parametro->editable) {
            return false;
        }

        $parametro->valor = $valor;
        return $parametro->save();
    }
}
