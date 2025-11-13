<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use Illuminate\Http\Request;

class EventoParticipacionController extends Controller
{
    // Inscripción
    public function inscribir(Request $request)
    {
        $externoId = $request->user()->id;
        $eventoId = $request->evento_id;

        $evento = Evento::find($eventoId);

        if (!$evento) {
            return response()->json(["success" => false, "error" => "Evento no encontrado"], 404);
        }

        // ¿Inscripciones abiertas?
        if (!$evento->inscripcion_abierta) {
            return response()->json(["success" => false, "error" => "Inscripciones cerradas"], 400);
        }

        // ¿Cupo lleno?
        $inscritos = EventoParticipacion::where('evento_id', $eventoId)->count();
        if ($evento->capacidad_maxima && $inscritos >= $evento->capacidad_maxima) {
            return response()->json(["success" => false, "error" => "Cupo agotado"], 400);
        }

        // ¿Ya está inscrito?
        if (EventoParticipacion::where('evento_id', $eventoId)->where('externo_id', $externoId)->exists()) {
            return response()->json(["success" => false, "error" => "Ya estás inscrito"], 400);
        }

        // Registrar
        $data = EventoParticipacion::create([
            "evento_id" => $eventoId,
            "externo_id" => $externoId,
            "asistio" => false,
            "puntos" => 0
        ]);

        return response()->json(["success" => true, "message" => "Inscripción exitosa", "data" => $data]);
    }

    // Cancelar inscripción
    public function cancelar(Request $request)
    {
        $externoId = $request->user()->id;
        $eventoId = $request->evento_id;

        $registro = EventoParticipacion::where('evento_id', $eventoId)
            ->where('externo_id', $externoId)
            ->first();

        if (!$registro) {
            return response()->json(["success" => false, "error" => "No estás inscrito"], 404);
        }

        $registro->delete();

        return response()->json(["success" => true, "message" => "Inscripción cancelada"]);
    }

    // Ver mis eventos
    public function misEventos(Request $request)
    {
        $externoId = $request->user()->id;

        $registros = EventoParticipacion::with('evento')
            ->where('externo_id', $externoId)
            ->get();

        return response()->json(["success" => true, "eventos" => $registros]);
    }
}
