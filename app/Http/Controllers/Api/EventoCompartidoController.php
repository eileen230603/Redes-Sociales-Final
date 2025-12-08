<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoCompartido;
use Illuminate\Http\Request;

class EventoCompartidoController extends Controller
{
    /**
     * Registrar un compartido (público o autenticado)
     */
    public function compartir(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
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
            EventoCompartido::create([
                'evento_id' => $eventoId,
                'externo_id' => $externoId,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'email' => $email,
                'metodo' => $metodo,
            ]);

            $totalCompartidos = EventoCompartido::where('evento_id', $eventoId)->count();

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
}


