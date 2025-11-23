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

            // Agrupar y formatear los datos - Mostrar cada participación por separado
            $voluntarios = $participaciones->map(function($participacion) {
                $user = $participacion->externo;
                
                if (!$user) {
                    return null;
                }
                
                $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();
                
                // Determinar tipo de usuario
                $tipoUsuario = 'Externo';
                if ($user->tipo_usuario === 'Voluntario' || $user->tipo_usuario === 'voluntario') {
                    $tipoUsuario = 'Voluntario';
                }
                
                $nombre = $user->nombre_usuario;
                if ($externo) {
                    $nombreCompleto = trim($externo->nombres . ' ' . ($externo->apellidos ?? ''));
                    if (!empty($nombreCompleto)) {
                        $nombre = $nombreCompleto;
                    }
                }
                
                // Estado de participación - Solo mostrar aprobadas (las participaciones ahora son automáticas)
                $estado = $participacion->estado ?? 'aprobada';
                // Filtrar solo participaciones aprobadas
                if ($estado !== 'aprobada') {
                    return null;
                }
                $estadoLabels = [
                    'aprobada' => 'Aprobada'
                ];
                $estadoLabel = $estadoLabels[$estado] ?? 'Aprobada';
                
                return [
                    'id' => $participacion->id,
                    'user_id' => $user->id_usuario,
                    'nombre' => $nombre,
                    'email' => $externo ? ($externo->email ?? $user->correo_electronico) : $user->correo_electronico,
                    'telefono' => $externo ? ($externo->phone_number ?? 'No disponible') : 'No disponible',
                    'tipo_usuario' => $tipoUsuario,
                    'evento_id' => $participacion->evento_id,
                    'evento_titulo' => $participacion->evento->titulo ?? 'N/A',
                    'estado' => $estado,
                    'estado_label' => $estadoLabel,
                    'asistio' => (bool) $participacion->asistio,
                    'puntos' => (int) $participacion->puntos,
                    'fecha_inscripcion' => $participacion->created_at ? $participacion->created_at->format('Y-m-d H:i:s') : null,
                    'fecha_inscripcion_formateada' => $participacion->created_at ? $participacion->created_at->format('d/m/Y H:i') : null,
                    'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                ];
            })->filter()->values(); // Eliminar nulls y reindexar

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

