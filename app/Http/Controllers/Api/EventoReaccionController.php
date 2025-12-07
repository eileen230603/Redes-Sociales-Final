<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoReaccion;
use App\Models\Notificacion;
use App\Models\IntegranteExterno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                // TRANSACCIÓN: Crear reacción + notificación
                DB::transaction(function () use ($eventoId, $externoId, $evento) {
                    // 1. Crear reacción
                    EventoReaccion::create([
                    'evento_id' => $eventoId,
                    'externo_id' => $externoId
                ]);

                    // 2. Crear notificación para la ONG
                $this->crearNotificacionReaccion($evento, $externoId);
                });

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
                    // Si es usuario registrado
                    if ($reaccion->externo_id) {
                        $user = $reaccion->externo;
                        if (!$user) {
                            return null;
                        }
                        $externo = IntegranteExterno::where('user_id', $user->id_usuario)->first();

                        return [
                            'id' => $reaccion->id,
                            'externo_id' => $reaccion->externo_id,
                            'tipo' => 'externo',
                            'tipo_usuario' => 'Externo',
                            'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                            'correo' => $externo ? $externo->email : $user->correo_electronico,
                            'telefono' => $externo ? ($externo->phone_number ?? null) : null,
                            'fecha_reaccion' => $reaccion->created_at,
                            'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                        ];
                    } else {
                        // Usuario no registrado
                        return [
                            'id' => $reaccion->id,
                            'externo_id' => null,
                            'tipo' => 'no_registrado',
                            'tipo_usuario' => 'No Registrado',
                            'nombre' => trim(($reaccion->nombres ?? '') . ' ' . ($reaccion->apellidos ?? '')),
                            'correo' => $reaccion->email,
                            'telefono' => $reaccion->telefono ?? null,
                            'fecha_reaccion' => $reaccion->created_at,
                            'foto_perfil' => null
                        ];
                    }
                })
                ->filter(); // Eliminar nulls

            return response()->json([
                'success' => true,
                'reacciones' => $reacciones->values(),
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
     * Reaccionar como usuario no registrado (público)
     */
    public function reaccionarPublico(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            $nombres = $request->input('nombres');
            $apellidos = $request->input('apellidos');
            $email = $request->input('email');

            if (!$nombres || !$apellidos) {
                return response()->json([
                    'success' => false,
                    'error' => 'Nombre y apellido son requeridos'
                ], 400);
            }

            // Verificar si ya existe la reacción (por email o nombre completo)
            $reaccionExistente = EventoReaccion::where('evento_id', $eventoId)
                ->whereNull('externo_id')
                ->where(function($query) use ($email, $nombres, $apellidos) {
                    if ($email) {
                        $query->where('email', $email);
                    } else {
                        $query->where('nombres', $nombres)
                              ->where('apellidos', $apellidos);
                    }
                })
                ->first();

            if ($reaccionExistente) {
                // Quitar reacción
                $reaccionExistente->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Reacción eliminada',
                    'reaccionado' => false,
                    'total_reacciones' => EventoReaccion::where('evento_id', $eventoId)->count()
                ]);
            } else {
                // Crear reacción
                $reaccion = EventoReaccion::create([
                    'evento_id' => $eventoId,
                    'externo_id' => null,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email,
                ]);

                // Contar TODAS las reacciones (registradas y no registradas)
                $totalReacciones = EventoReaccion::where('evento_id', $eventoId)->count();

                \Log::info('Reacción pública creada:', [
                    'evento_id' => $eventoId,
                    'reaccion_id' => $reaccion->id,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'total_reacciones' => $totalReacciones
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reacción agregada',
                    'reaccionado' => true,
                    'total_reacciones' => $totalReacciones
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

    /**
     * Obtener total de reacciones de un evento (público, sin autenticación)
     */
    public function totalReacciones($eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Contar TODAS las reacciones (registradas y no registradas)
            $totalReacciones = EventoReaccion::where('evento_id', $eventoId)->count();
            
            // Debug: contar por tipo
            $registradas = EventoReaccion::where('evento_id', $eventoId)
                ->whereNotNull('externo_id')
                ->count();
            $noRegistradas = EventoReaccion::where('evento_id', $eventoId)
                ->whereNull('externo_id')
                ->count();

            \Log::info('Total reacciones consultado:', [
                'evento_id' => $eventoId,
                'total' => $totalReacciones,
                'registradas' => $registradas,
                'no_registradas' => $noRegistradas
            ]);

            return response()->json([
                'success' => true,
                'total_reacciones' => $totalReacciones
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en totalReacciones:', [
                'evento_id' => $eventoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener total de reacciones: ' . $e->getMessage()
            ], 500);
        }
    }
}
