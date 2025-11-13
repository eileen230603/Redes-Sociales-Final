<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\User;

class EventController extends Controller
{
    // ======================================================
    //  Conversión segura JSON → ARRAY
    // ======================================================
    private function safeArray($value)
    {
        if (is_array($value)) return $value;
        if ($value === null) return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // ======================================================
    //  LISTAR EVENTOS PARA LA ONG
    // ======================================================
    public function indexByOng($ongId)
    {
        $eventos = Evento::where('ong_id', $ongId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos
        ]);
    }

    // ======================================================
    //  LISTAR EVENTOS PARA EL USUARIO EXTERNO
    //  SOLO ESTADO = PUBLICADO
    // ======================================================
    public function indexAll()
    {
        try {
            $eventos = Evento::where('estado', 'publicado')
                ->orderBy('fecha_inicio', 'asc')
                ->get();

            // Convertir arrays para evitar errores
            $eventos->transform(function ($e) {
                $e->patrocinadores = $this->safeArray($e->patrocinadores);
                $e->invitados = $this->safeArray($e->invitados);
                $e->imagenes = $this->safeArray($e->imagenes);
                return $e;
            });

            // IMPORTANTE: la vista espera directamente un array
            return response()->json($eventos);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error"   => $e->getMessage(),
                "file"    => $e->getFile(),
                "line"    => $e->getLine(),
            ], 500);
        }
    }

    // ======================================================
    //  CREAR EVENTO
    // ======================================================
    public function store(Request $request)
    {
        try {
            $evento = Evento::create([
                "ong_id" => $request->ong_id,
                "titulo" => $request->titulo,
                "descripcion" => $request->descripcion,
                "tipo_evento" => $request->tipo_evento,

                "fecha_inicio" => $request->fecha_inicio,
                "fecha_fin" => $request->fecha_fin,
                "fecha_limite_inscripcion" => $request->fecha_limite_inscripcion,

                "capacidad_maxima" => $request->capacidad_maxima,
                "estado" => $request->estado,
                "ciudad" => $request->ciudad,
                "direccion" => $request->direccion,
                "lat" => $request->lat,
                "lng" => $request->lng,

                "patrocinadores" => $this->safeArray($request->patrocinadores),
                "invitados" => $this->safeArray($request->invitados),
                "imagenes" => $this->safeArray($request->imagenes),
            ]);

            return response()->json([
                "success" => true,
                "message" => "Evento creado correctamente",
                "evento"  => $evento
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // ======================================================
    //  DETALLE DEL EVENTO
    // ======================================================
    public function show($id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    "success" => false,
                    "message" => "Evento no encontrado"
                ], 404);
            }

            $evento->patrocinadores = $this->safeArray($evento->patrocinadores);
            $evento->invitados = $this->safeArray($evento->invitados);
            $evento->imagenes = $this->safeArray($evento->imagenes);

            return response()->json([
                "success" => true,
                "evento" => $evento
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500);
        }
    }

    // ======================================================
    //  ACTUALIZAR EVENTO
    // ======================================================
    public function update(Request $request, $id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento)
                return response()->json(["success" => false, "message" => "No encontrado"], 404);

            $evento->update([
                "titulo" => $request->titulo,
                "descripcion" => $request->descripcion,
                "tipo_evento" => $request->tipo_evento,
                "fecha_inicio" => $request->fecha_inicio,
                "fecha_fin" => $request->fecha_fin,
                "fecha_limite_inscripcion" => $request->fecha_limite_inscripcion,
                "capacidad_maxima" => $request->capacidad_maxima,
                "estado" => $request->estado,
                "ciudad" => $request->ciudad,
                "direccion" => $request->direccion,
                "lat" => $request->lat,
                "lng" => $request->lng,
                "patrocinadores" => $this->safeArray($request->patrocinadores),
                "invitados" => $this->safeArray($request->invitados),
                "imagenes" => $this->safeArray($request->imagenes),
            ]);

            return response()->json([
                "success" => true,
                "message" => "Evento actualizado",
                "evento"  => $evento->fresh()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ], 500);
        }
    }

    // ======================================================
    //  ELIMINAR
    // ======================================================
    public function destroy($id)
    {
        $evento = Evento::find($id);

        if (!$evento)
            return response()->json(["success" => false, "message" => "No encontrado"], 404);

        $evento->delete();

        return response()->json([
            "success" => true,
            "message" => "Evento eliminado"
        ]);
    }

    // ======================================================
    //  PARTICIPAR EN EVENTO
    // ======================================================
    public function participar($id)
    {
        $userId = auth()->id();

        $ya = EventoParticipacion::where("evento_id", $id)
            ->where("externo_id", $userId)
            ->first();

        if ($ya) {
            return response()->json(["success" => false, "message" => "Ya estás inscrito"]);
        }

        EventoParticipacion::create([
            "evento_id" => $id,
            "externo_id" => $userId
        ]);

        return response()->json(["success" => true, "message" => "Inscripción realizada"]);
    }

    // ======================================================
    //  CANCELAR INSCRIPCIÓN
    // ======================================================
    public function cancelar($id)
    {
        $userId = auth()->id();

        EventoParticipacion::where("evento_id", $id)
            ->where("externo_id", $userId)
            ->delete();

        return response()->json(["success" => true, "message" => "Inscripción cancelada"]);
    }

    // ======================================================
    //  MIS EVENTOS
    // ======================================================
    public function misEventos()
    {
        $userId = auth()->id();

        $eventos = EventoParticipacion::with("evento")
            ->where("externo_id", $userId)
            ->get();

        return response()->json([
            "success" => true,
            "eventos" => $eventos
        ]);
    }

    // ======================================================
    //  EMPRESAS
    // ======================================================
    public function empresasDisponibles()
    {
        return response()->json([
            "success" => true,
            "empresas" => [
                ["id" => 1, "nombre" => "Coca-Cola"],
                ["id" => 2, "nombre" => "Samsung"],
                ["id" => 3, "nombre" => "Toyota"],
            ]
        ]);
    }

    // ======================================================
    //  INVITADOS
    // ======================================================
    public function invitados()
    {
        return response()->json([
            "success" => true,
            "invitados" => [
                ["id" => 1, "nombre" => "Juan Pérez"],
                ["id" => 2, "nombre" => "María Gómez"],
                ["id" => 3, "nombre" => "Carlos López"]
            ]
        ]);
    }
}
