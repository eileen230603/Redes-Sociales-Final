<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Services\DashboardPDFService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Exportar dashboard del evento en PDF
     */
    public function exportarDashboardPDF(Request $request, $id)
    {
        try {
            Log::info('Iniciando generación PDF dashboard evento', ['evento_id' => $id]);

            // Validar ID
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID inválido'
                ], 400)->header('Content-Type', 'application/json');
            }

            // Verificar que el evento existe
            if (!Evento::where('id', $id)->exists()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404)->header('Content-Type', 'application/json');
            }

            // Verificar autenticación
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'No autenticado'
                ], 401)->header('Content-Type', 'application/json');
            }

            // Obtener evento
            $evento = Evento::find($id);
            
            // Verificar que el usuario tenga acceso al evento
            if ($user->tipo_usuario !== 'ONG' || $evento->ong_id != $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sin permisos para acceder a este evento'
                ], 403)->header('Content-Type', 'application/json');
            }

            // Generar PDF usando el servicio compartido
            $pdf = DashboardPDFService::generarPDFEvento($id);

            // Generar nombre de archivo
            $fecha = now()->format('Y-m-d');
            $filename = "dashboard-evento-{$id}-{$fecha}.pdf";

            Log::info('PDF generado exitosamente', [
                'evento_id' => $id,
                'filename' => $filename
            ]);

            // Retornar PDF con headers correctos
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (ModelNotFoundException $e) {
            Log::error('Evento no encontrado', [
                'evento_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Evento no encontrado'
            ], 404)->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            Log::error('Error generando PDF dashboard evento', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF'
            ], 500)->header('Content-Type', 'application/json');
        }
    }
}
