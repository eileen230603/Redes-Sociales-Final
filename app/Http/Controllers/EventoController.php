<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with('ong')->orderByDesc('EventoID')->get();
        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Tittulo' => 'required|string|max:200',
            'Descripcion' => 'nullable|string',
            'F_Inicio' => 'required|date',
            'F_final' => 'nullable|date',
            'Locacion' => 'nullable|string|max:250',
            'ong_id' => 'required|integer|exists:ongs,id_usuario',
            'Tipo_evento' => 'nullable|string|max:100'
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
