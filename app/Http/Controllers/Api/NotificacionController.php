<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Obtener todas las notificaciones de la ONG
     */
    public function index(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            $notificaciones = Notificacion::where('ong_id', $ongId)
                ->with(['evento:id,titulo', 'externo'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    return [
                        'id' => $notificacion->id,
                        'tipo' => $notificacion->tipo,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'leida' => $notificacion->leida,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'externo_id' => $notificacion->externo_id,
                        'fecha' => $notificacion->created_at,
                    ];
                });

            $noLeidas = Notificacion::where('ong_id', $ongId)
                ->where('leida', false)
                ->count();

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'no_leidas' => $noLeidas
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener solo el contador de notificaciones no leídas (para actualización en tiempo real)
     */
    public function contador(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            $noLeidas = Notificacion::where('ong_id', $ongId)
                ->where('leida', false)
                ->count();

            return response()->json([
                'success' => true,
                'no_leidas' => $noLeidas
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener contador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida(Request $request, $id)
    {
        try {
            $ongId = $request->user()->id_usuario;

            $notificacion = Notificacion::where('id', $id)
                ->where('ong_id', $ongId)
                ->first();

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'error' => 'Notificación no encontrada'
                ], 404);
            }

            $notificacion->leida = true;
            $notificacion->save();

            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al marcar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasLeidas(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            Notificacion::where('ong_id', $ongId)
                ->where('leida', false)
                ->update(['leida' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Todas las notificaciones marcadas como leídas'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al marcar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }
}
