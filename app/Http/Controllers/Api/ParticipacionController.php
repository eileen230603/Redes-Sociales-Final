<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use Illuminate\Http\Request;

class ParticipacionController extends Controller
{
    public function inscribirse(Request $request, $eventoId)
    {
        $externoId = auth()->user()->id_usuario;

        $evento = Evento::find($eventoId);
        if (!$evento) {
            return response()->json(['success' => false, 'message' => 'Evento no encontrado'], 404);
        }

        $yaInscrito = EventoParticipacion::where('evento_id', $eventoId)
                        ->where('externo_id', $externoId)
                        ->exists();

        if ($yaInscrito) {
            return response()->json(['success' => false, 'message' => 'Ya estás inscrito'], 400);
        }

        $inscritos = EventoParticipacion::where('evento_id', $eventoId)->count();
        if ($evento->capacidad_maxima && $inscritos >= $evento->capacidad_maxima) {
            return response()->json(['success' => false, 'message' => 'No hay cupos disponibles'], 400);
        }

        EventoParticipacion::create([
            'evento_id' => $eventoId,
            'externo_id' => $externoId
        ]);

        return response()->json(['success' => true, 'message' => 'Inscripción exitosa']);
    }

    public function cancelar(Request $request, $eventoId)
    {
        $externoId = auth()->user()->id_usuario;

        $registro = EventoParticipacion::where('evento_id', $eventoId)
                    ->where('externo_id', $externoId)
                    ->first();

        if (!$registro) {
            return response()->json(['success' => false, 'message' => 'No estás inscrito'], 400);
        }

        $registro->delete();

        return response()->json(['success' => true, 'message' => 'Participación cancelada']);
    }

    public function listado($eventoId)
    {
        $list = EventoParticipacion::where('evento_id', $eventoId)
            ->with('externo:id_usuario,nombre_usuario,correo_electronico')
            ->get();

        return response()->json([
            'success' => true,
            'participantes' => $list
        ]);
    }
}
