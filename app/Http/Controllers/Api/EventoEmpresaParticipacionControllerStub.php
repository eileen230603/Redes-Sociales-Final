<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventoEmpresaParticipacion;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use Illuminate\Http\Request;

class EventoEmpresaParticipacionController extends Controller
{
    public function eventosPatrocinados(Request $request)
    {
        try {
            $empresaId = $request->user()->id_usuario;
            $participaciones = EventoEmpresaParticipacion::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->with(['evento'])
                ->get()
                ->filter(function ($participacion) {
                    return $participacion->evento !== null;
                });

            $eventosPatrocinados = $participaciones->map(function ($participacion) {
                $evento = $participacion->evento;
                $totalParticipantes = EventoParticipacion::where('evento_id', $evento->id)->count();
                $totalReacciones = EventoReaccion::where('evento_id', $evento->id)->count();
                $totalCompartidos = EventoCompartido::where('evento_id', $evento->id)->count();

                return [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'estado' => $evento->estado,
                    'categoria' => $evento->tipo_evento ?? 'Sin categoría',
                    'total_participantes' => $totalParticipantes,
                    'total_reacciones' => $totalReacciones,
                    'total_compartidos' => $totalCompartidos,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'eventos_patrocinados' => $eventosPatrocinados,
                'count' => $eventosPatrocinados->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener eventos: ' . $e->getMessage(),
            ], 500);
        }
    }
}
