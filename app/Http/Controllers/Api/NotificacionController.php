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
                ->with(['evento:id,titulo', 'externo.integranteExterno'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    $externo = $notificacion->externo;
                    $nombreExterno = null;
                    $fotoExterno = null;
                    $tituloExterno = 'Usuario';
                    
                    if ($externo) {
                        // Obtener nombre del integrante externo si existe
                        if ($externo->integranteExterno) {
                            $nombres = trim($externo->integranteExterno->nombres ?? '');
                            $apellidos = trim($externo->integranteExterno->apellidos ?? '');
                            $nombreExterno = trim("{$nombres} {$apellidos}") ?: $externo->nombre_usuario;
                            $fotoExterno = $externo->integranteExterno->foto_perfil_url ?? $externo->foto_perfil_url ?? null;
                            $tituloExterno = 'Participante';
                        } else {
                            $nombreExterno = $externo->nombre_usuario;
                            $fotoExterno = $externo->foto_perfil_url ?? null;
                            $tituloExterno = 'Usuario';
                        }
                    }
                    
                    return [
                        'id' => $notificacion->id,
                        'tipo' => $notificacion->tipo,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'leida' => $notificacion->leida,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'externo_id' => $notificacion->externo_id,
                        'externo' => $externo ? [
                            'nombre_usuario' => $nombreExterno,
                            'foto_perfil_url' => $fotoExterno,
                            'titulo' => $tituloExterno
                        ] : null,
                        'fecha' => $notificacion->created_at,
                    ];
                });

            // Obtener notificaciones de eventos próximos (para alertas)
            $alertasEventosProximos = Notificacion::where('ong_id', $ongId)
                ->where('tipo', 'evento_proximo')
                ->where('leida', false)
                ->with(['evento:id,titulo,fecha_inicio'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    return [
                        'id' => $notificacion->id,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'fecha_inicio' => $notificacion->evento ? $notificacion->evento->fecha_inicio : null,
                    ];
                });

            $noLeidas = Notificacion::where('ong_id', $ongId)
                ->where('leida', false)
                ->count();

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'no_leidas' => $noLeidas,
                'alertas_eventos_proximos' => $alertasEventosProximos
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

    // ============================================
    // MÉTODOS PARA EMPRESAS
    // ============================================

    /**
     * Obtener todas las notificaciones de la empresa
     */
    public function indexEmpresa(Request $request)
    {
        try {
            $empresaId = $request->user()->id_usuario;

            // Notificaciones para empresas: ong_id es null y externo_id es el user_id de la empresa
            $notificaciones = Notificacion::where('externo_id', $empresaId)
                ->whereNull('ong_id') // Solo notificaciones para empresas
                ->whereIn('tipo', ['empresa_asignada', 'empresa_confirmada', 'evento_proximo']) // Incluir eventos próximos
                ->with(['evento:id,titulo,fecha_inicio'])
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
                        'fecha' => $notificacion->created_at,
                    ];
                });

            $noLeidas = Notificacion::where('externo_id', $empresaId)
                ->whereNull('ong_id')
                ->whereIn('tipo', ['empresa_asignada', 'empresa_confirmada', 'evento_proximo'])
                ->where('leida', false)
                ->count();

            // Obtener alertas de eventos próximos para empresas
            $alertasEventosProximos = Notificacion::where('externo_id', $empresaId)
                ->whereNull('ong_id')
                ->where('tipo', 'evento_proximo')
                ->where('leida', false)
                ->with(['evento:id,titulo,fecha_inicio'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notificacion) {
                    return [
                        'id' => $notificacion->id,
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'evento_id' => $notificacion->evento_id,
                        'evento_titulo' => $notificacion->evento ? $notificacion->evento->titulo : null,
                        'fecha_inicio' => $notificacion->evento ? $notificacion->evento->fecha_inicio : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'no_leidas' => $noLeidas,
                'alertas_eventos_proximos' => $alertasEventosProximos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener solo el contador de notificaciones no leídas para empresa
     */
    public function contadorEmpresa(Request $request)
    {
        try {
            $empresaId = $request->user()->id_usuario;

            $noLeidas = Notificacion::where('externo_id', $empresaId)
                ->whereNull('ong_id')
                ->whereIn('tipo', ['empresa_asignada', 'empresa_confirmada'])
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
     * Marcar notificación como leída (empresa)
     */
    public function marcarLeidaEmpresa(Request $request, $id)
    {
        try {
            $empresaId = $request->user()->id_usuario;

            $notificacion = Notificacion::where('id', $id)
                ->where('externo_id', $empresaId)
                ->whereNull('ong_id')
                ->whereIn('tipo', ['empresa_asignada', 'empresa_confirmada'])
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
     * Marcar todas las notificaciones como leídas (empresa)
     */
    public function marcarTodasLeidasEmpresa(Request $request)
    {
        try {
            $empresaId = $request->user()->id_usuario;

            Notificacion::where('externo_id', $empresaId)
                ->whereNull('ong_id')
                ->whereIn('tipo', ['empresa_asignada', 'empresa_confirmada'])
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
