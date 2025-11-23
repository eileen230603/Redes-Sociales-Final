<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoReaccion;
use App\Models\Notificacion;
use App\Models\IntegranteExterno;
use App\Models\User;
use Illuminate\Http\Request;

class EventoReaccionController extends Controller
{
    /**
     * Agregar o quitar reacción (toggle)
     */
    public function toggle(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;
            $eventoId = $request->evento_id;

            if (!$eventoId) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de evento requerido'
                ], 400);
            }

            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar si ya existe la reacción
            $reaccion = EventoReaccion::where('evento_id', $eventoId)
                ->where('externo_id', $externoId)
                ->first();

            if ($reaccion) {
                // Quitar reacción
                $reaccion->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Reacción eliminada',
                    'reaccionado' => false,
                    'total_reacciones' => EventoReaccion::where('evento_id', $eventoId)->count()
                ]);
            } else {
                // Agregar reacción
                $nuevaReaccion = EventoReaccion::create([
                    'evento_id' => $eventoId,
                    'externo_id' => $externoId
                ]);

                // Crear notificación para la ONG
                $this->crearNotificacionReaccion($evento, $externoId);

                return response()->json([
                    'success' => true,
                    'message' => 'Reacción agregada',
                    'reaccionado' => true,
                    'total_reacciones' => EventoReaccion::where('evento_id', $eventoId)->count()
                ]);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar reacción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si el usuario actual reaccionó al evento
     */
    public function verificar(Request $request, $eventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;

            $reaccionado = EventoReaccion::where('evento_id', $eventoId)
                ->where('externo_id', $externoId)
                ->exists();

            $totalReacciones = EventoReaccion::where('evento_id', $eventoId)->count();

            return response()->json([
                'success' => true,
                'reaccionado' => $reaccionado,
                'total_reacciones' => $totalReacciones
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar reacción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de usuarios que reaccionaron a un evento (para ONG)
     */
    public function usuariosQueReaccionaron(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario es la ONG dueña del evento
            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver esta información'
                ], 403);
            }

            $reacciones = EventoReaccion::where('evento_id', $eventoId)
                ->with(['externo' => function($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($reaccion) {
                    $user = $reaccion->externo;
                    $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();

                    return [
                        'id' => $reaccion->id,
                        'externo_id' => $reaccion->externo_id,
                        'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                        'correo' => $externo ? $externo->email : $user->correo_electronico,
                        'fecha_reaccion' => $reaccion->created_at,
                        'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                    ];
                });

            return response()->json([
                'success' => true,
                'reacciones' => $reacciones,
                'total' => $reacciones->count()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener reacciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación para la ONG cuando alguien reacciona
     */
    private function crearNotificacionReaccion(Evento $evento, $externoId)
    {
        try {
            $externo = User::find($externoId);
            if (!$externo) return;

            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            $nombreUsuario = $integranteExterno 
                ? trim($integranteExterno->nombres . ' ' . ($integranteExterno->apellidos ?? ''))
                : $externo->nombre_usuario;

            Notificacion::create([
                'ong_id' => $evento->ong_id,
                'evento_id' => $evento->id,
                'externo_id' => $externoId,
                'tipo' => 'reaccion',
                'titulo' => 'Nueva reacción en tu evento',
                'mensaje' => "{$nombreUsuario} reaccionó con un corazón al evento \"{$evento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            // Log error pero no fallar la reacción
            \Log::error('Error creando notificación de reacción: ' . $e->getMessage());
        }
    }
}
