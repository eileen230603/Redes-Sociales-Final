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
            }

            // Crear reacción
            DB::transaction(function () use ($megaEventoId, $externoId) {
                MegaEventoReaccion::create([
                    'mega_evento_id' => $megaEventoId,
                    'externo_id' => $externoId
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reacción agregada',
                'reaccionado' => true,
                'total_reacciones' => MegaEventoReaccion::where('mega_evento_id', $megaEventoId)->count()
            ]);

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
                ->with(['externo' => function ($query) {
                    $query->select('id_usuario', 'nombre_usuario', 'correo_electronico');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($reaccion) {
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
                            'nombre' => $externo
                                ? trim($externo->nombres . ' ' . ($externo->apellidos ?? ''))
                                : ($user->nombre_usuario ?? 'Usuario'),
                            'email' => $externo
                                ? $externo->email
                                : ($user->correo_electronico ?? ''),
                            'fecha_reaccion' => $reaccion->created_at->format('d/m/Y H:i'),
                            'tipo' => 'registrado',
                        ];
                    }

                    // Usuario no registrado
                    $nombre = trim(($reaccion->nombres ?? '') . ' ' . ($reaccion->apellidos ?? ''));

                    return [
                        'id' => $reaccion->id,
                        'externo_id' => null,
                        'nombre' => $nombre ?: 'Usuario anónimo',
                        'email' => $reaccion->email ?? '',
                        'fecha_reaccion' => $reaccion->created_at->format('d/m/Y H:i'),
                        'tipo' => 'no_registrado',
                    ];
                })
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'usuarios' => $reacciones,
                'total' => $reacciones->count(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener usuarios que reaccionaron: ' . $e->getMessage()
            ], 500);
        }
    }
}


