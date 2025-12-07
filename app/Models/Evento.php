<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    // Usamos guarded para permitir todos los campos
    protected $guarded = [];

    protected $casts = [
        'fecha_inicio'             => 'datetime',
        'fecha_fin'                => 'datetime',
        'fecha_limite_inscripcion' => 'datetime',
        'fecha_finalizacion'       => 'datetime',
        'imagenes'                 => 'array',
        'patrocinadores'           => 'array',
        'auspiciadores'            => 'array',
        'invitados'                => 'array',
        'inscripcion_abierta'      => 'boolean',
    ];

    // ONG propietaria del evento
    public function ong()
    {
        return $this->belongsTo(Ong::class, 'ong_id', 'user_id');
    }

    // Participantes inscritos
    public function participantes()
    {
        return $this->hasMany(EventoParticipacion::class, 'evento_id');
    }

    // Usuarios externos
    public function externos()
    {
        return $this->belongsToMany(
            User::class,
            'evento_participaciones',
            'evento_id',     // FK en pivot
            'externo_id',    // FK en pivot
            'id',            // PK de eventos
            'id_usuario'     // PK de usuarios
        );
    }

    // Reacciones (favoritos)
    public function reacciones()
    {
        return $this->hasMany(EventoReaccion::class, 'evento_id');
    }

    // Usuarios que reaccionaron
    public function usuariosQueReaccionaron()
    {
        return $this->belongsToMany(
            User::class,
            'evento_reacciones',
            'evento_id',
            'externo_id',
            'id',
            'id_usuario'
        );
    }

    // Empresas participantes (colaboradoras)
    public function empresasParticipantes()
    {
        return $this->hasMany(EventoEmpresaParticipacion::class, 'evento_id');
    }

    // Empresas colaboradoras (relación many-to-many)
    public function empresas()
    {
        return $this->belongsToMany(
            Empresa::class,
            'evento_empresas_participantes',
            'evento_id',
            'empresa_id',
            'id',
            'user_id'
        )->withPivot(['estado', 'asistio', 'tipo_colaboracion', 'descripcion_colaboracion', 'activo'])
          ->wherePivot('activo', true);
    }

    /**
     * Calcular el estado dinámico del evento basado en fechas
     * Retorna: 'proximo', 'activo', 'finalizado' o el estado guardado si es 'borrador' o 'cancelado'
     */
    public function getEstadoDinamicoAttribute()
    {
        // Si el estado es borrador o cancelado, mantenerlo
        if (in_array($this->estado, ['borrador', 'cancelado'])) {
            return $this->estado;
        }

        // Si ya tiene fecha_finalizacion, está finalizado
        if ($this->fecha_finalizacion) {
            return 'finalizado';
        }

        $ahora = now();
        
        // Si no tiene fecha_fin, usar el estado guardado
        if (!$this->fecha_fin) {
            return $this->estado;
        }

        $fechaInicio = $this->fecha_inicio ? \Carbon\Carbon::parse($this->fecha_inicio) : null;
        $fechaFin = \Carbon\Carbon::parse($this->fecha_fin);

        // Si la fecha_fin ya pasó, está finalizado
        if ($fechaFin->isPast()) {
            return 'finalizado';
        }

        // Si la fecha_inicio aún no ha llegado, está próximo
        if ($fechaInicio && $fechaInicio->isFuture()) {
            return 'proximo';
        }

        // Si está entre fecha_inicio y fecha_fin, está activo
        if ($fechaInicio && $fechaInicio->isPast() && $fechaFin->isFuture()) {
            return 'activo';
        }

        // Por defecto, usar el estado guardado
        return $this->estado;
    }

    /**
     * Verificar si el evento está finalizado (basado en fecha_fin)
     */
    public function estaFinalizado()
    {
        if (!$this->fecha_fin) {
            return false;
        }
        
        return \Carbon\Carbon::parse($this->fecha_fin)->isPast();
    }

    /**
     * Verificar si el evento está próximo (fecha_inicio en el futuro)
     */
    public function estaProximo()
    {
        if (!$this->fecha_inicio) {
            return false;
        }
        
        return \Carbon\Carbon::parse($this->fecha_inicio)->isFuture();
    }

    /**
     * Verificar si el evento está activo (entre fecha_inicio y fecha_fin)
     */
    public function estaActivo()
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return false;
        }
        
        $inicio = \Carbon\Carbon::parse($this->fecha_inicio);
        $fin = \Carbon\Carbon::parse($this->fecha_fin);
        $ahora = now();
        
        return $inicio->isPast() && $fin->isFuture();
    }

    /**
     * Accessor para convertir rutas relativas de imágenes a URLs completas
     */
    public function getImagenesAttribute($value)
    {
        // Si $value es null o no es array, retornar array vacío
        if (!is_array($value)) {
            // Si es string, intentar decodificar JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = is_array($decoded) ? $decoded : [];
            } else {
                $value = [];
            }
        }

        // Obtener la URL base desde PUBLIC_APP_URL, APP_URL o del request actual
        $baseUrl = env('PUBLIC_APP_URL', env('APP_URL'));
        
        // Si no hay baseUrl configurada, intentar obtenerla del request actual
        if (empty($baseUrl) && app()->runningInConsole() === false) {
            try {
                $request = request();
                if ($request) {
                    $baseUrl = $request->getSchemeAndHttpHost();
                }
            } catch (\Exception $e) {
                // Si falla, usar el valor por defecto
            }
        }
        
        // Si aún no hay baseUrl, usar un valor por defecto
        if (empty($baseUrl)) {
            $baseUrl = 'http://127.0.0.1:8000';
        }
        
        // Generar URLs completas para cada imagen
        return array_map(function($imagen) use ($baseUrl) {
            if (empty($imagen) || !is_string($imagen)) {
                return null;
            }

            // Si ya es una URL completa, verificar si tiene IPs antiguas y reemplazarlas
            if (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0) {
                // Reemplazar IPs antiguas con la nueva si están presentes
                $imagen = str_replace('http://10.26.15.110:8000', $baseUrl, $imagen);
                $imagen = str_replace('https://10.26.15.110:8000', $baseUrl, $imagen);
                $imagen = str_replace('http://192.168.0.6:8000', $baseUrl, $imagen);
                $imagen = str_replace('https://192.168.0.6:8000', $baseUrl, $imagen);
                return $imagen;
            }

            // Si empieza con /storage/, agregar el dominio base
            if (strpos($imagen, '/storage/') === 0) {
                return rtrim($baseUrl, '/') . $imagen;
            }

            // Si empieza con storage/, agregar /storage/
            if (strpos($imagen, 'storage/') === 0) {
                return rtrim($baseUrl, '/') . '/storage/' . $imagen;
            }

            // Por defecto, asumir que es relativa a storage
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($imagen, '/');
        }, array_filter($value, function($img) {
            return !empty($img) && is_string($img);
        }));
    }
}
