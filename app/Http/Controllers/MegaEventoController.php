<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MegaEvento;

class MegaEventoController extends Controller
{
    public function index()
    {
        $items = MegaEvento::orderByDesc('MegaEventoID')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'ubicacion' => 'nullable|string|max:500',
            'categoria' => 'nullable|string|max:50',
            'estado' => 'nullable|string|max:20',
            'ong_organizadora_principal' => 'required|integer|exists:ongs,id_usuario',
            'capacidad_maxima' => 'nullable|integer',
            'es_publico' => 'boolean'
        ]);

        $mega = MegaEvento::create($data);
        return response()->json(['success' => true, 'mega_evento' => $mega], 201);
    }

    public function show($id)
    {
        $mega = MegaEvento::findOrFail($id);
        return response()->json($mega);
    }

    public function update(Request $request, $id)
    {
        $mega = MegaEvento::findOrFail($id);
        $mega->update($request->all());
        return response()->json(['success' => true, 'mega_evento' => $mega]);
    }

    public function destroy($id)
    {
        MegaEvento::destroy($id);
        return response()->json(['success' => true]);
    }
}
