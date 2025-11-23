<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoEvento;
use App\Models\CategoriaMegaEvento;
use App\Models\Ciudad;
use App\Models\Lugar;
use App\Models\EstadoParticipacion;
use App\Models\TipoNotificacion;
use App\Models\EstadoEvento;
use App\Models\TipoUsuario;

class ParametrizacionController extends Controller
{
    /**
     * Obtener todos los tipos de evento
     */
    public function tiposEvento(Request $request)
    {
        try {
            $query = TipoEvento::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos(); // Por defecto solo activos
            }

            $tipos = $query->ordenados()->get();

            return response()->json([
                'success' => true,
                'data' => $tipos,
                'count' => $tipos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener tipos de evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear tipo de evento
     */
    public function crearTipoEvento(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:tipos_evento,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo = TipoEvento::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de evento creado correctamente',
                'data' => $tipo
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear tipo de evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar tipo de evento
     */
    public function actualizarTipoEvento(Request $request, $id)
    {
        try {
            $tipo = TipoEvento::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de evento no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:tipos_evento,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de evento actualizado correctamente',
                'data' => $tipo
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar tipo de evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de evento
     */
    public function eliminarTipoEvento($id)
    {
        try {
            $tipo = TipoEvento::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de evento no encontrado'
                ], 404);
            }

            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de evento eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar tipo de evento: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // CATEGORÍAS DE MEGA EVENTOS
    // ============================================

    /**
     * Obtener todas las categorías de mega eventos
     */
    public function categoriasMegaEvento(Request $request)
    {
        try {
            $query = CategoriaMegaEvento::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activas();
            }

            $categorias = $query->ordenadas()->get();

            return response()->json([
                'success' => true,
                'data' => $categorias,
                'count' => $categorias->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear categoría de mega evento
     */
    public function crearCategoriaMegaEvento(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:categorias_mega_eventos,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $categoria = CategoriaMegaEvento::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente',
                'data' => $categoria
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar categoría de mega evento
     */
    public function actualizarCategoriaMegaEvento(Request $request, $id)
    {
        try {
            $categoria = CategoriaMegaEvento::find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'error' => 'Categoría no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:categorias_mega_eventos,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $categoria->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente',
                'data' => $categoria
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar categoría de mega evento
     */
    public function eliminarCategoriaMegaEvento($id)
    {
        try {
            $categoria = CategoriaMegaEvento::find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'error' => 'Categoría no encontrada'
                ], 404);
            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // CIUDADES
    // ============================================

    /**
     * Obtener todas las ciudades
     */
    public function ciudades(Request $request)
    {
        try {
            $query = Ciudad::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activas();
            }

            if ($request->has('buscar')) {
                $query->buscar($request->buscar);
            }

            if ($request->has('departamento')) {
                $query->where('departamento', $request->departamento);
            }

            if ($request->has('pais')) {
                $query->where('pais', $request->pais);
            }

            $ciudades = $query->orderBy('departamento')->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $ciudades,
                'count' => $ciudades->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener ciudades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear ciudad
     */
    public function crearCiudad(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'codigo_postal' => 'nullable|string|max:20',
                'departamento' => 'nullable|string|max:100',
                'pais' => 'nullable|string|max:100',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $ciudad = Ciudad::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ciudad creada correctamente',
                'data' => $ciudad
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear ciudad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar ciudad
     */
    public function actualizarCiudad(Request $request, $id)
    {
        try {
            $ciudad = Ciudad::find($id);

            if (!$ciudad) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ciudad no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:100',
                'codigo_postal' => 'nullable|string|max:20',
                'departamento' => 'nullable|string|max:100',
                'pais' => 'nullable|string|max:100',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $ciudad->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ciudad actualizada correctamente',
                'data' => $ciudad
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar ciudad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar ciudad
     */
    public function eliminarCiudad($id)
    {
        try {
            $ciudad = Ciudad::find($id);

            if (!$ciudad) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ciudad no encontrada'
                ], 404);
            }

            $ciudad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ciudad eliminada correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar ciudad: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // LUGARES
    // ============================================

    /**
     * Obtener todos los lugares
     */
    public function lugares(Request $request)
    {
        try {
            $query = Lugar::with('ciudad');

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos();
            }

            if ($request->has('buscar')) {
                $query->buscar($request->buscar);
            }

            if ($request->has('ciudad_id')) {
                $query->where('ciudad_id', $request->ciudad_id);
            }

            $lugares = $query->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $lugares,
                'count' => $lugares->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener lugares: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear lugar
     */
    public function crearLugar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:200',
                'direccion' => 'nullable|string',
                'ciudad_id' => 'nullable|exists:ciudades,id',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'capacidad' => 'nullable|integer|min:1',
                'descripcion' => 'nullable|string',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'sitio_web' => 'nullable|url|max:255',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $lugar = Lugar::create($request->all());
            $lugar->load('ciudad');

            return response()->json([
                'success' => true,
                'message' => 'Lugar creado correctamente',
                'data' => $lugar
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear lugar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar lugar
     */
    public function actualizarLugar(Request $request, $id)
    {
        try {
            $lugar = Lugar::find($id);

            if (!$lugar) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lugar no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:200',
                'direccion' => 'nullable|string',
                'ciudad_id' => 'nullable|exists:ciudades,id',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'capacidad' => 'nullable|integer|min:1',
                'descripcion' => 'nullable|string',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'sitio_web' => 'nullable|url|max:255',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $lugar->update($request->all());
            $lugar->load('ciudad');

            return response()->json([
                'success' => true,
                'message' => 'Lugar actualizado correctamente',
                'data' => $lugar
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar lugar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar lugar
     */
    public function eliminarLugar($id)
    {
        try {
            $lugar = Lugar::find($id);

            if (!$lugar) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lugar no encontrado'
                ], 404);
            }

            $lugar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lugar eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar lugar: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // ESTADOS DE PARTICIPACIÓN
    // ============================================

    /**
     * Obtener todos los estados de participación
     */
    public function estadosParticipacion(Request $request)
    {
        try {
            $query = EstadoParticipacion::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos();
            }

            $estados = $query->ordenados()->get();

            return response()->json([
                'success' => true,
                'data' => $estados,
                'count' => $estados->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear estado de participación
     */
    public function crearEstadoParticipacion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:estados_participacion,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'icono' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $estado = EstadoParticipacion::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Estado creado correctamente',
                'data' => $estado
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de participación
     */
    public function actualizarEstadoParticipacion(Request $request, $id)
    {
        try {
            $estado = EstadoParticipacion::find($id);

            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Estado no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:estados_participacion,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'icono' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $estado->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'data' => $estado
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar estado de participación
     */
    public function eliminarEstadoParticipacion($id)
    {
        try {
            $estado = EstadoParticipacion::find($id);

            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Estado no encontrado'
                ], 404);
            }

            $estado->delete();

            return response()->json([
                'success' => true,
                'message' => 'Estado eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // TIPOS DE NOTIFICACIÓN
    // ============================================

    /**
     * Obtener todos los tipos de notificación
     */
    public function tiposNotificacion(Request $request)
    {
        try {
            $query = TipoNotificacion::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos();
            }

            $tipos = $query->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $tipos,
                'count' => $tipos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener tipos de notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear tipo de notificación
     */
    public function crearTipoNotificacion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:tipos_notificacion,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'plantilla_mensaje' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo = TipoNotificacion::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de notificación creado correctamente',
                'data' => $tipo
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear tipo de notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar tipo de notificación
     */
    public function actualizarTipoNotificacion(Request $request, $id)
    {
        try {
            $tipo = TipoNotificacion::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de notificación no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:tipos_notificacion,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'plantilla_mensaje' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de notificación actualizado correctamente',
                'data' => $tipo
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar tipo de notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de notificación
     */
    public function eliminarTipoNotificacion($id)
    {
        try {
            $tipo = TipoNotificacion::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de notificación no encontrado'
                ], 404);
            }

            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de notificación eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar tipo de notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // ESTADOS DE EVENTO
    // ============================================

    /**
     * Obtener todos los estados de evento
     */
    public function estadosEvento(Request $request)
    {
        try {
            $query = EstadoEvento::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos();
            }

            if ($request->has('tipo')) {
                $query->porTipo($request->tipo);
            }

            $estados = $query->ordenados()->get();

            return response()->json([
                'success' => true,
                'data' => $estados,
                'count' => $estados->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear estado de evento
     */
    public function crearEstadoEvento(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:estados_evento,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'tipo' => 'required|in:evento,mega_evento,ambos',
                'color' => 'nullable|string|max:20',
                'icono' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $estado = EstadoEvento::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Estado creado correctamente',
                'data' => $estado
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de evento
     */
    public function actualizarEstadoEvento(Request $request, $id)
    {
        try {
            $estado = EstadoEvento::find($id);

            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Estado no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:estados_evento,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'tipo' => 'sometimes|in:evento,mega_evento,ambos',
                'color' => 'nullable|string|max:20',
                'icono' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $estado->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'data' => $estado
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar estado de evento
     */
    public function eliminarEstadoEvento($id)
    {
        try {
            $estado = EstadoEvento::find($id);

            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Estado no encontrado'
                ], 404);
            }

            $estado->delete();

            return response()->json([
                'success' => true,
                'message' => 'Estado eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // TIPOS DE USUARIO
    // ============================================

    /**
     * Obtener todos los tipos de usuario
     */
    public function tiposUsuario(Request $request)
    {
        try {
            $query = TipoUsuario::query();

            if ($request->has('activo')) {
                $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
            } else {
                $query->activos();
            }

            $tipos = $query->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $tipos,
                'count' => $tipos->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener tipos de usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear tipo de usuario
     */
    public function crearTipoUsuario(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:50|unique:tipos_usuario,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'permisos_default' => 'nullable|array',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo = TipoUsuario::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de usuario creado correctamente',
                'data' => $tipo
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear tipo de usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar tipo de usuario
     */
    public function actualizarTipoUsuario(Request $request, $id)
    {
        try {
            $tipo = TipoUsuario::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de usuario no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:50|unique:tipos_usuario,codigo,' . $id,
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'permisos_default' => 'nullable|array',
                'activo' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de usuario actualizado correctamente',
                'data' => $tipo
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar tipo de usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de usuario
     */
    public function eliminarTipoUsuario($id)
    {
        try {
            $tipo = TipoUsuario::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de usuario no encontrado'
                ], 404);
            }

            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de usuario eliminado correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar tipo de usuario: ' . $e->getMessage()
            ], 500);
        }
    }
}
