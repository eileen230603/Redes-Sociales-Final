<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MegaEvento;
use App\Models\MegaEventoCompartido;
use Illuminate\Http\Request;

class MegaEventoCompartidoController extends Controller
{
    /**
     * Registrar un compartido (público o autenticado)
     */
    public function compartir(Request $request, $megaEventoId)
    {
        try {
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            $metodo = $request->input('metodo', 'link');
            $externoId = null;
            $nombres = null;
            $apellidos = null;
            $email = null;

            // Si hay usuario autenticado, usar su información
            if ($request->user()) {
                $externoId = $request->user()->id_usuario;
            } else {
                // Usuario no registrado
                $nombres = $request->input('nombres');
                $apellidos = $request->input('apellidos');
                $email = $request->input('email');
            }

            // Crear registro de compartido
            MegaEventoCompartido::create([
                'mega_evento_id' => $megaEventoId,
                'externo_id' => $externoId,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'email' => $email,
                'metodo' => $metodo,
            ]);

            $totalCompartidos = MegaEventoCompartido::where('mega_evento_id', $megaEventoId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Compartido registrado',
                'total_compartidos' => $totalCompartidos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar compartido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener total de compartidos de un mega evento (público)
     */
    public function totalCompartidos($megaEventoId)
    {
        try {
            $megaEvento = MegaEvento::find($megaEventoId);
            if (!$megaEvento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Mega evento no encontrado'
                ], 404);
            }

            $totalCompartidos = MegaEventoCompartido::where('mega_evento_id', $megaEventoId)->count();

            return response()->json([
                'success' => true,
                'total_compartidos' => $totalCompartidos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener total de compartidos: ' . $e->getMessage()
            ], 500);
        }
    }
}
