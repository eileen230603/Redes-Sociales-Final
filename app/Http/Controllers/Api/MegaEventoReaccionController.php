<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MegaEvento;
use App\Models\MegaEventoReaccion;
use App\Models\IntegranteExterno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MegaEventoReaccionController extends Controller
{
    /**
     * Obtener total de reacciones de un mega evento (público)
     */
    public function totalReacciones($megaEventoId)
    {
        try {
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            $totalReacciones = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count();

            return response()->json([
                'success' => true,
                'total_reacciones' => $totalReacciones
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
    public function reaccionarPublico(Request $request, $megaEventoId)
    {
        try {
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
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
            $reaccionExistente = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)
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
                    'total_reacciones' => MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count()
                ]);
            } else {
                // Crear reacción
                DB::transaction(function () use ($megaEventoId, $nombres, $apellidos, $email, $megaEvento) {
                $reaccion = MegaEventoReaccion::create([
                    'mega_evento_id' => $megaEventoId,
                    'externo_id' => null,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email,
                ]);
                    
                    // Crear notificación para la ONG
                    $this->crearNotificacionReaccionMegaEventoPublica($megaEvento, $nombres, $apellidos);
                });

                // Contar TODAS las reacciones (registradas y no registradas)
                $totalReacciones = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count();

                \Log::info('Reacción pública de mega evento creada:', [
                    'mega_evento_id' => $megaEventoId,
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
     * Agregar o quitar reacción (toggle) para usuarios registrados
     */
    public function toggle(Request $request)
    {
        try {
            $externoId = $request->user()->id_usuario;
            $megaEventoId = $request->mega_evento_id;

            if (!$megaEventoId) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de mega evento requerido'
                ], 400);
            }

            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Verificar si ya existe la reacción
            $reaccion = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)
                ->where('externo_id', $externoId)
                ->first();

            if ($reaccion) {
                // Quitar reacción
                $reaccion->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Reacción eliminada',
                    'reaccionado' => false,
                    'total_reacciones' => MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count()
                ]);
            } else {
                // Crear reacción
                DB::transaction(function () use ($megaEventoId, $externoId, $megaEvento) {
                    MegaEventoReaccion::create([
                        'mega_evento_id' => $megaEventoId,
                        'externo_id' => $externoId
                    ]);
                    
                    // Crear notificación para la ONG
                    $this->crearNotificacionReaccionMegaEvento($megaEvento, $externoId);
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Reacción agregada',
                    'reaccionado' => true,
                    'total_reacciones' => MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count()
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
     * Verificar si el usuario actual reaccionó al mega evento
     */
    public function verificar(Request $request, $megaEventoId)
    {
        try {
            $externoId = $request->user()->id_usuario;

            $reaccionado = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)
                ->where('externo_id', $externoId)
                ->exists();

            $totalReacciones = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count();

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
     * Obtener lista de usuarios que reaccionaron a un mega evento (para ONG)
     */
    public function usuariosQueReaccionaron(Request $request, $megaEventoId)
    {
        try {
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            // Verificar que el usuario es la ONG dueña del mega evento
            $ongId = $request->user()->id_usuario;
            if ($megaEvento->ong_organizadora_principal != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver esta información'
                ], 403);
            }

            $reacciones = MegaEventoReaccion::where('mega_evento_id', $megaEventoId)
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
                            'tipo' => 'registrado',
                            'nombre' => $externo ? trim($externo->nombres . ' ' . ($externo->apellidos ?? '')) : $user->nombre_usuario,
                            'correo' => $externo ? $externo->email : $user->correo_electronico,
                            'fecha_reaccion' => $reaccion->created_at,
                            'foto_perfil' => $externo ? ($externo->foto_perfil_url ?? null) : ($user->foto_perfil_url ?? null)
                        ];
                    } else {
                        // Usuario no registrado
                        return [
                            'id' => $reaccion->id,
                            'externo_id' => null,
                            'tipo' => 'no_registrado',
                            'nombre' => trim(($reaccion->nombres ?? '') . ' ' . ($reaccion->apellidos ?? '')),
                            'correo' => $reaccion->email ?? null,
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
     * Crear notificación para la ONG cuando un usuario registrado reacciona
     */
    private function crearNotificacionReaccionMegaEvento(MegaEvento $megaEvento, $externoId)
    {
        try {
            $externo = User::find($externoId);
            if (!$externo) return;

            $integranteExterno = IntegranteExterno::where('user_id', $externoId)->first();
            $nombreUsuario = $integranteExterno 
                ? trim($integranteExterno->nombres . ' ' . ($integranteExterno->apellidos ?? ''))
                : $externo->nombre_usuario;

            \App\Models\Notificacion::create([
                'ong_id' => $megaEvento->ong_organizadora_principal,
                'evento_id' => null, // Los mega eventos no tienen evento_id
                'externo_id' => $externoId,
                'tipo' => 'reaccion_mega_evento',
                'titulo' => 'Nueva reacción en tu mega evento',
                'mensaje' => "{$nombreUsuario} reaccionó con un corazón al mega evento \"{$megaEvento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            // Log error pero no fallar la reacción
            \Log::error('Error creando notificación de reacción de mega evento: ' . $e->getMessage());
        }
    }

    /**
     * Crear notificación para la ONG cuando un usuario no registrado reacciona
     */
    private function crearNotificacionReaccionMegaEventoPublica(MegaEvento $megaEvento, $nombres, $apellidos)
    {
        try {
            $nombreCompleto = trim($nombres . ' ' . ($apellidos ?? ''));

            \App\Models\Notificacion::create([
                'ong_id' => $megaEvento->ong_organizadora_principal,
                'evento_id' => null, // Los mega eventos no tienen evento_id
                'externo_id' => null, // Usuario no registrado
                'tipo' => 'reaccion_mega_evento_publica',
                'titulo' => 'Nueva reacción en tu mega evento',
                'mensaje' => "{$nombreCompleto} (usuario no registrado) reaccionó con un corazón al mega evento \"{$megaEvento->titulo}\"",
                'leida' => false
            ]);

        } catch (\Throwable $e) {
            // Log error pero no fallar la reacción
            \Log::error('Error creando notificación de reacción pública de mega evento: ' . $e->getMessage());
        }
    }
}
