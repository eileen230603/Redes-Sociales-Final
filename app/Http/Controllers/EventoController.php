<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with('ong')->orderByDesc('id')->get();
        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ong_id' => 'required|integer|exists:ongs,user_id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_evento' => 'nullable|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'fecha_limite_inscripcion' => 'nullable|date',
            'capacidad_maxima' => 'nullable|integer',
            'inscripcion_abierta' => 'boolean',
            'estado' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string',
        ]);

        $evento = Evento::create($data);
        return response()->json(['success' => true, 'evento' => $evento], 201);
    }

    public function show($id)
    {
        $evento = Evento::findOrFail($id);
        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $evento->update($request->all());

        return response()->json(['success' => true, 'evento' => $evento]);
    }

    public function destroy($id)
    {
        Evento::destroy($id);
        return response()->json(['success' => true]);
    }
}
