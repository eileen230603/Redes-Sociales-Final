<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\User;
use App\Models\IntegranteExterno;

class VoluntarioController extends Controller
{
    // ======================================================
    //  LISTAR VOLUNTARIOS DE UNA ONG
    // ======================================================
    public function indexByOng($ongId)
    {
        try {
            // Obtener todos los eventos de la ONG
            $eventos = Evento::where('ong_id', $ongId)->pluck('id');

            if ($eventos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'voluntarios' => []
                ]);
            }

            // Obtener todas las participaciones de los eventos de la ONG
            $participaciones = EventoParticipacion::whereIn('evento_id', $eventos)
                ->with(['evento:id,titulo', 'externo'])
                ->get();

            // Agrupar y formatear los datos
            $voluntarios = $participaciones->map(function($participacion) {
                $user = $participacion->externo;
                
                if (!$user) {
                    return null;
                }
                
                $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                
                $nombre = $user->nombre_usuario;
                if ($externo) {
                    $nombreCompleto = trim($externo->nombres . ' ' . ($externo->apellidos ?? ''));
                    if (!empty($nombreCompleto)) {
                        $nombre = $nombreCompleto;
                    }
                }
                
                return [
                    'id' => $participacion->id,
                    'user_id' => $user->id_usuario,
                    'nombre' => $nombre,
                    'email' => $user->correo_electronico,
                    'evento_id' => $participacion->evento_id,
                    'evento_titulo' => $participacion->evento->titulo ?? 'N/A',
                    'asistio' => (bool) $participacion->asistio,
                    'puntos' => (int) $participacion->puntos,
                    'fecha_inscripcion' => $participacion->created_at ? $participacion->created_at->format('Y-m-d H:i:s') : null,
                ];
            })->filter(); // Eliminar nulls

            return response()->json([
                'success' => true,
                'voluntarios' => $voluntarios
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

