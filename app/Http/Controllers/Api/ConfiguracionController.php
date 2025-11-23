<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    /**
     * Listar todos los parámetros
     */
    public function index(Request $request)
    {
        try {
            $query = Parametro::query();

            // Filtros
            if ($request->has('categoria') && $request->categoria) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->has('grupo') && $request->grupo) {
                $query->where('grupo', $request->grupo);
            }

            if ($request->has('visible') && $request->visible !== null) {
                $query->where('visible', filter_var($request->visible, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->has('editable') && $request->editable !== null) {
                $query->where('editable', filter_var($request->editable, FILTER_VALIDATE_BOOLEAN));
            }

            // Búsqueda
            if ($request->has('buscar') && $request->buscar) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('codigo', 'ilike', "%{$buscar}%")
                      ->orWhere('nombre', 'ilike', "%{$buscar}%")
                      ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            }

            $parametros = $query->orderBy('categoria')
                                ->orderBy('grupo')
                                ->orderBy('orden')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $parametros,
                'count' => $parametros->count()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener parámetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un parámetro por ID
     */
    public function show($id)
    {
        try {
            $parametro = Parametro::find($id);

            if (!$parametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'Parámetro no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $parametro
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener parámetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener parámetro por código
     */
    public function porCodigo($codigo)
    {
        try {
            $parametro = Parametro::where('codigo', $codigo)->first();

            if (!$parametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'Parámetro no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $parametro,
                'valor_formateado' => $parametro->valor_formateado
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener parámetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo parámetro
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:100|unique:parametros,codigo',
                'nombre' => 'required|string|max:200',
                'descripcion' => 'nullable|string',
                'categoria' => 'required|string|max:50',
                'tipo' => 'required|in:texto,numero,booleano,json,fecha,select',
                'valor' => 'nullable',
                'valor_defecto' => 'nullable',
                'opciones' => 'nullable|array',
                'grupo' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'editable' => 'nullable|boolean',
                'visible' => 'nullable|boolean',
                'requerido' => 'nullable|boolean',
                'validacion' => 'nullable|string|max:500',
                'ayuda' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Procesar valor según tipo
            $valor = $request->valor;
            if ($request->tipo === 'json' && is_array($valor)) {
                $valor = json_encode($valor);
            } elseif ($request->tipo === 'booleano') {
                $valor = filter_var($valor, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            $parametro = Parametro::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria' => $request->categoria,
                'tipo' => $request->tipo,
                'valor' => $valor,
                'valor_defecto' => $request->valor_defecto,
                'opciones' => $request->opciones,
                'grupo' => $request->grupo,
                'orden' => $request->orden ?? 0,
                'editable' => $request->editable ?? true,
                'visible' => $request->visible ?? true,
                'requerido' => $request->requerido ?? false,
                'validacion' => $request->validacion,
                'ayuda' => $request->ayuda,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parámetro creado correctamente',
                'data' => $parametro
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear parámetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar parámetro
     */
    public function update(Request $request, $id)
    {
        try {
            $parametro = Parametro::find($id);

            if (!$parametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'Parámetro no encontrado'
                ], 404);
            }

            if (!$parametro->editable) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este parámetro no es editable'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'codigo' => 'sometimes|string|max:100|unique:parametros,codigo,' . $id,
                'nombre' => 'sometimes|string|max:200',
                'descripcion' => 'nullable|string',
                'categoria' => 'sometimes|string|max:50',
                'tipo' => 'sometimes|in:texto,numero,booleano,json,fecha,select',
                'valor' => 'nullable',
                'valor_defecto' => 'nullable',
                'opciones' => 'nullable|array',
                'grupo' => 'nullable|string|max:50',
                'orden' => 'nullable|integer|min:0',
                'editable' => 'nullable|boolean',
                'visible' => 'nullable|boolean',
                'requerido' => 'nullable|boolean',
                'validacion' => 'nullable|string|max:500',
                'ayuda' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Procesar valor según tipo
            if ($request->has('valor')) {
                $valor = $request->valor;
                $tipo = $request->tipo ?? $parametro->tipo;
                
                if ($tipo === 'json' && is_array($valor)) {
                    $valor = json_encode($valor);
                } elseif ($tipo === 'booleano') {
                    $valor = filter_var($valor, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                }
                
                $parametro->valor = $valor;
            }

            // Actualizar otros campos
            $parametro->fill($request->only([
                'codigo', 'nombre', 'descripcion', 'categoria', 'tipo',
                'valor_defecto', 'opciones', 'grupo', 'orden',
                'editable', 'visible', 'requerido', 'validacion', 'ayuda'
            ]));

            $parametro->save();

            return response()->json([
                'success' => true,
                'message' => 'Parámetro actualizado correctamente',
                'data' => $parametro
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar parámetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar solo el valor de un parámetro
     */
    public function actualizarValor(Request $request, $id)
    {
        try {
            $parametro = Parametro::find($id);

            if (!$parametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'Parámetro no encontrado'
                ], 404);
            }

            if (!$parametro->editable) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este parámetro no es editable'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'valor' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Procesar valor según tipo
            $valor = $request->valor;
            if ($parametro->tipo === 'json' && is_array($valor)) {
                $valor = json_encode($valor);
            } elseif ($parametro->tipo === 'booleano') {
                $valor = filter_var($valor, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            $parametro->valor = $valor;
            $parametro->save();

            return response()->json([
                'success' => true,
                'message' => 'Valor actualizado correctamente',
                'data' => $parametro
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar parámetro
     */
    public function destroy($id)
    {
        try {
            $parametro = Parametro::find($id);

            if (!$parametro) {
                return response()->json([
                    'success' => false,
                    'error' => 'Parámetro no encontrado'
                ], 404);
            }

            $parametro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Parámetro eliminado correctamente'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar parámetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categorías disponibles
     */
    public function categorias()
    {
        try {
            $categorias = Parametro::select('categoria')
                ->distinct()
                ->orderBy('categoria')
                ->pluck('categoria');

            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener grupos disponibles
     */
    public function grupos()
    {
        try {
            $grupos = Parametro::select('grupo')
                ->whereNotNull('grupo')
                ->distinct()
                ->orderBy('grupo')
                ->pluck('grupo');

            return response()->json([
                'success' => true,
                'data' => $grupos
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener grupos: ' . $e->getMessage()
            ], 500);
        }
    }
}
