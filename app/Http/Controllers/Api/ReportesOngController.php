<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\Evento;
use App\Models\MegaEvento;
use App\Models\Empresa;
use App\Models\User;
use App\Models\IntegranteExterno;
use App\Models\EventoParticipacion;
use App\Models\EventoEmpresaParticipacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controlador API para reportes de ONGs
 * 
 * Endpoints protegidos con auth:sanctum para generar
 * reportes de participación y colaboración
 */
class ReportesOngController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Obtener datos de participación y colaboración
     * 
     * GET /api/reportes-ong/participacion-colaboracion
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function participacionColaboracion(Request $request)
    {
        try {
            // Validar autenticación
            $user = $request->user();
            if (!$user) {
                Log::warning('API participacionColaboracion: Usuario no autenticado');
                return response()->json([
                    'success' => false,
                    'error' => 'No autenticado'
                ], 401);
            }

            // Validar que sea tipo ONG
            if ($user->tipo_usuario !== 'ONG') {
                Log::warning('API participacionColaboracion: Usuario no es ONG', [
                    'user_id' => $user->id_usuario,
                    'tipo_usuario' => $user->tipo_usuario
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Acceso denegado. Solo ONGs pueden acceder a este endpoint.'
                ], 403);
            }

            $ongId = $user->id_usuario;
            Log::info('API participacionColaboracion: Iniciando para ONG', ['ong_id' => $ongId]);

            // Validar y obtener filtros
            $filtros = $this->validarFiltros($request);
            Log::info('API participacionColaboracion: Filtros aplicados', ['filtros' => $filtros]);

            // ========== OBTENER TOP EMPRESAS PATROCINADORAS ==========
            $topEmpresas = $this->obtenerTopEmpresas($ongId, $filtros);
            Log::info('API participacionColaboracion: Top empresas obtenidas', ['count' => count($topEmpresas)]);

            // ========== OBTENER TOP VOLUNTARIOS MÁS ACTIVOS ==========
            $topVoluntarios = $this->obtenerTopVoluntarios($ongId, $filtros);
            Log::info('API participacionColaboracion: Top voluntarios obtenidos', ['count' => count($topVoluntarios)]);

            // ========== OBTENER EVENTOS CON MÁS COLABORADORES ==========
            $eventosColaboracion = $this->obtenerEventosColaboracion($ongId, $filtros);
            Log::info('API participacionColaboracion: Eventos con colaboración obtenidos', ['count' => count($eventosColaboracion)]);

            // Formatear respuesta según especificación
            $response = [
                'success' => true,
                'datos' => [
                    'top_empresas' => $topEmpresas,
                    'top_voluntarios' => $topVoluntarios,
                    'eventos_colaboracion' => $eventosColaboracion
                ]
            ];

            Log::info('API participacionColaboracion: Respuesta preparada', [
                'empresas_count' => count($topEmpresas),
                'voluntarios_count' => count($topVoluntarios),
                'eventos_count' => count($eventosColaboracion)
            ]);

            return response()->json($response);

        } catch (\Throwable $e) {
            Log::error('Error en participacionColaboracion API: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener datos: ' . $e->getMessage(),
                'datos' => [
                    'top_empresas' => [],
                    'top_voluntarios' => [],
                    'eventos_colaboracion' => []
                ]
            ], 500);
        }
    }

    /**
     * Obtener Top 10 Empresas Patrocinadoras
     */
    private function obtenerTopEmpresas(int $ongId, array $filtros): array
    {
        try {
            Log::info('Top Empresas: Iniciando obtención', ['ong_id' => $ongId, 'filtros' => $filtros]);
            
            // PRIMERO: Obtener TODOS los eventos regulares de la ONG SIN filtros para debugging
            $queryEventos = Evento::where('ong_id', $ongId);
            
            // Aplicar filtros SOLO si se proporcionaron explícitamente
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                Log::info('Top Empresas: Aplicando filtro fecha_inicio', ['fecha' => $filtros['fecha_inicio']]);
                $queryEventos->where('fecha_inicio', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                Log::info('Top Empresas: Aplicando filtro fecha_fin', ['fecha' => $filtros['fecha_fin']]);
                $queryEventos->where('fecha_inicio', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                Log::info('Top Empresas: Aplicando filtro categoria', ['categoria' => $filtros['categoria']]);
                $queryEventos->where('tipo_evento', $filtros['categoria']);
            }
            
            $eventosIds = $queryEventos->pluck('id')->toArray();
            Log::info('Top Empresas: Eventos regulares encontrados', [
                'count' => count($eventosIds),
                'ids' => $eventosIds,
                'sql' => $queryEventos->toSql(),
                'bindings' => $queryEventos->getBindings()
            ]);

            // Obtener TODOS los mega eventos de la ONG
            $queryMega = MegaEvento::where('ong_organizadora_principal', $ongId);
            
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                $queryMega->where('fecha_creacion', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                $queryMega->where('fecha_creacion', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $queryMega->where('categoria', $filtros['categoria']);
            }
            
            $megaEventosIds = $queryMega->pluck('mega_evento_id')->toArray();
            Log::info('Top Empresas: Mega eventos encontrados', [
                'count' => count($megaEventosIds),
                'ids' => $megaEventosIds
            ]);

            // Empresas de eventos regulares
            $empresasEventos = collect();
            if (!empty($eventosIds)) {
                // OPCIÓN 1: Intentar con JOIN directo primero para debugging
                $isPostgreSQL = DB::getDriverName() === 'pgsql';
                
                try {
                    // Query directa con JOIN para verificar si hay datos
                    if ($isPostgreSQL) {
                        $empresasDirectas = DB::table('evento_empresas_participantes as eep')
                            ->whereIn('eep.evento_id', $eventosIds)
                            ->where(function($q) {
                                $q->where('eep.activo', true)->orWhereNull('eep.activo');
                            })
                            ->join('empresas as e', 'eep.empresa_id', '=', 'e.user_id')
                            ->select(
                                'e.user_id as empresa_id',
                                'e.nombre_empresa as nombre',
                                DB::raw('COUNT(DISTINCT eep.evento_id) as eventos_count')
                            )
                            ->groupBy('e.user_id', 'e.nombre_empresa')
                            ->get();
                    } else {
                        $empresasDirectas = DB::table('evento_empresas_participantes as eep')
                            ->whereIn('eep.evento_id', $eventosIds)
                            ->where(function($q) {
                                $q->where('eep.activo', true)->orWhereNull('eep.activo');
                            })
                            ->join('empresas as e', 'eep.empresa_id', '=', 'e.user_id')
                            ->select(
                                'e.user_id as empresa_id',
                                'e.nombre_empresa as nombre',
                                DB::raw('COUNT(DISTINCT eep.evento_id) as eventos_count')
                            )
                            ->groupBy('e.user_id', 'e.nombre_empresa')
                            ->get();
                    }
                    
                    Log::info('Top Empresas: Query directa con JOIN ejecutada', [
                        'count' => $empresasDirectas->count(),
                        'empresas' => $empresasDirectas->toArray()
                    ]);
                    
                    $empresasEventos = $empresasDirectas->map(function ($item) {
                        return [
                            'empresa_id' => $item->empresa_id,
                            'nombre' => $item->nombre ?? 'Empresa #' . $item->empresa_id,
                            'eventos_count' => (int) $item->eventos_count,
                            'monto_total' => 0
                        ];
                    });
                    
                    Log::info('Top Empresas: Empresas de eventos regulares (método JOIN)', [
                        'count' => $empresasEventos->count(),
                        'empresas' => $empresasEventos->toArray()
                    ]);
                    
                } catch (\Throwable $e) {
                    Log::error('Top Empresas: Error en query directa con JOIN', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Fallback: Intentar con Eloquent y relación
                    try {
                        $participaciones = EventoEmpresaParticipacion::whereIn('evento_id', $eventosIds)
                            ->where(function($q) {
                                $q->where('activo', true)->orWhereNull('activo');
                            })
                            ->get();
                        
                        Log::info('Top Empresas: Participaciones obtenidas (fallback Eloquent)', [
                            'count' => $participaciones->count(),
                            'empresa_ids' => $participaciones->pluck('empresa_id')->unique()->toArray()
                        ]);
                        
                        // Cargar empresas manualmente si la relación no funciona
                        $empresaIds = $participaciones->pluck('empresa_id')->unique()->filter();
                        $empresas = Empresa::whereIn('user_id', $empresaIds)->get()->keyBy('user_id');
                        
                        Log::info('Top Empresas: Empresas cargadas manualmente', [
                            'count' => $empresas->count(),
                            'empresa_ids' => $empresas->keys()->toArray()
                        ]);
                        
                        $empresasEventos = $participaciones
                            ->groupBy('empresa_id')
                            ->map(function ($participacionesGrupo) use ($empresas) {
                                $empresaId = $participacionesGrupo->first()->empresa_id;
                                $empresa = $empresas->get($empresaId);
                                
                                if (!$empresa) {
                                    Log::warning('Top Empresas: Empresa no encontrada', ['empresa_id' => $empresaId]);
                                    return null;
                                }
                                
                                return [
                                    'empresa_id' => $empresa->user_id,
                                    'nombre' => $empresa->nombre_empresa ?? 'Empresa #' . $empresa->user_id,
                                    'eventos_count' => $participacionesGrupo->unique('evento_id')->count(),
                                    'monto_total' => 0
                                ];
                            })
                            ->filter()
                            ->values();
                            
                        Log::info('Top Empresas: Empresas procesadas (fallback)', [
                            'count' => $empresasEventos->count(),
                            'empresas' => $empresasEventos->toArray()
                        ]);
                    } catch (\Throwable $e2) {
                        Log::error('Top Empresas: Error en fallback Eloquent', [
                            'error' => $e2->getMessage()
                        ]);
                        $empresasEventos = collect();
                    }
                }
            } else {
                Log::warning('Top Empresas: No hay eventos regulares para esta ONG', ['ong_id' => $ongId]);
            }

            // Empresas de mega eventos
            $empresasMega = collect();
            if (!empty($megaEventosIds)) {
                $isPostgreSQL = DB::getDriverName() === 'pgsql';
                
                // Cambiar condición de activo para incluir NULL
                $queryMegaEmpresas = DB::table('mega_evento_patrocinadores')
                    ->whereIn('mega_evento_id', $megaEventosIds)
                    ->where(function($q) {
                        $q->where('activo', true)->orWhereNull('activo');
                    })
                    ->join('empresas', 'mega_evento_patrocinadores.empresa_id', '=', 'empresas.user_id');
                
                if ($isPostgreSQL) {
                    $empresasMega = $queryMegaEmpresas
                        ->select(
                            'empresas.user_id as empresa_id',
                            'empresas.nombre_empresa as nombre',
                            DB::raw('COUNT(DISTINCT mega_evento_patrocinadores.mega_evento_id) as eventos_count'),
                            DB::raw('COALESCE(SUM(mega_evento_patrocinadores.monto_contribucion), 0) as monto_total')
                        )
                        ->groupBy('empresas.user_id', 'empresas.nombre_empresa')
                        ->get();
                } else {
                    $empresasMega = $queryMegaEmpresas
                        ->select(
                            'empresas.user_id as empresa_id',
                            'empresas.nombre_empresa as nombre',
                            DB::raw('COUNT(DISTINCT mega_evento_patrocinadores.mega_evento_id) as eventos_count'),
                            DB::raw('COALESCE(SUM(mega_evento_patrocinadores.monto_contribucion), 0) as monto_total')
                        )
                        ->groupBy('empresas.user_id', 'empresas.nombre_empresa')
                        ->get();
                }
                
                Log::info('Top Empresas: Query mega eventos ejecutada', ['count' => $empresasMega->count()]);
                
                $empresasMega = $empresasMega->map(function ($item) {
                    return [
                        'empresa_id' => $item->empresa_id,
                        'nombre' => $item->nombre ?? 'Empresa #' . $item->empresa_id,
                        'eventos_count' => (int) $item->eventos_count,
                        'monto_total' => (float) $item->monto_total
                    ];
                });
                    
                Log::info('Top Empresas: Empresas de mega eventos procesadas', [
                    'count' => $empresasMega->count(),
                    'empresas' => $empresasMega->toArray()
                ]);
            } else {
                Log::info('Top Empresas: No hay mega eventos para esta ONG', ['ong_id' => $ongId]);
            }

            // Consolidar empresas
            $empresasConsolidadas = collect();
            
            foreach ($empresasEventos as $emp) {
                $empresasConsolidadas->put($emp['empresa_id'], $emp);
            }
            
            foreach ($empresasMega as $emp) {
                if ($empresasConsolidadas->has($emp['empresa_id'])) {
                    // Obtener el array actual, modificarlo y volver a ponerlo
                    $empActual = $empresasConsolidadas->get($emp['empresa_id']);
                    $empActual['eventos_count'] += $emp['eventos_count'];
                    $empActual['monto_total'] += $emp['monto_total'];
                    $empresasConsolidadas->put($emp['empresa_id'], $empActual);
                } else {
                    $empresasConsolidadas->put($emp['empresa_id'], $emp);
                }
            }

            // Ordenar y tomar top 10
            $topEmpresas = $empresasConsolidadas
                ->sortByDesc('eventos_count')
                ->take(10)
                ->values()
                ->map(function ($emp) {
                    return [
                        'nombre' => $emp['nombre'],
                        'eventos_count' => (int) $emp['eventos_count'],
                        'monto_total' => (float) $emp['monto_total']
                    ];
                })
                ->toArray();

            Log::info('Top Empresas: Resultado final', [
                'count' => count($topEmpresas),
                'primera_empresa' => $topEmpresas[0] ?? null
            ]);
            
            return $topEmpresas;

        } catch (\Throwable $e) {
            Log::error('Error en obtenerTopEmpresas: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ong_id' => $ongId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Obtener Top 10 Voluntarios Más Activos
     */
    private function obtenerTopVoluntarios(int $ongId, array $filtros): array
    {
        try {
            // Obtener TODOS los eventos regulares de la ONG (los filtros son opcionales)
            $queryEventos = Evento::where('ong_id', $ongId);
            
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                $queryEventos->where('fecha_inicio', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                $queryEventos->where('fecha_inicio', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $queryEventos->where('tipo_evento', $filtros['categoria']);
            }
            
            $eventosIds = $queryEventos->pluck('id')->toArray();
            Log::info('Top Voluntarios: Eventos regulares encontrados', [
                'count' => count($eventosIds),
                'filtros_aplicados' => $filtros
            ]);

            // Obtener TODOS los mega eventos de la ONG (los filtros son opcionales)
            $queryMega = MegaEvento::where('ong_organizadora_principal', $ongId);
            
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                $queryMega->where('fecha_creacion', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                $queryMega->where('fecha_creacion', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $queryMega->where('categoria', $filtros['categoria']);
            }
            
            $megaEventosIds = $queryMega->pluck('mega_evento_id')->toArray();
            Log::info('Top Voluntarios: Mega eventos encontrados', [
                'count' => count($megaEventosIds),
                'filtros_aplicados' => $filtros
            ]);

            // Voluntarios de eventos regulares
            $voluntariosEventos = collect();
            if (!empty($eventosIds)) {
                $participaciones = EventoParticipacion::whereIn('evento_id', $eventosIds)
                    ->whereNotNull('externo_id')
                    ->with(['externo.integranteExterno'])
                    ->get();
                    
                Log::info('Top Voluntarios: Participaciones en eventos regulares', [
                    'count' => $participaciones->count(),
                    'eventos_ids' => $eventosIds,
                    'sample_ids' => $participaciones->take(5)->pluck('id')->toArray()
                ]);
                
                // Verificar relaciones cargadas
                $participacionesConUsuario = $participaciones->filter(function($p) {
                    return $p->externo !== null;
                });
                Log::info('Top Voluntarios: Participaciones con usuario cargado', ['count' => $participacionesConUsuario->count()]);
                
                $voluntariosEventos = $participaciones
                    ->groupBy('externo_id')
                    ->map(function ($participacionesGrupo) {
                        $primeraParticipacion = $participacionesGrupo->first();
                        $user = $primeraParticipacion->externo;
                        
                        if (!$user) {
                            Log::warning('Top Voluntarios: Participación sin usuario', [
                                'participacion_id' => $primeraParticipacion->id,
                                'externo_id' => $primeraParticipacion->externo_id
                            ]);
                            return null;
                        }
                        
                        $externo = $user->integranteExterno;
                        $nombre = $user->nombre_usuario ?? 'Usuario';
                        if ($externo) {
                            $nombreCompleto = trim(($externo->nombres ?? '') . ' ' . ($externo->apellidos ?? ''));
                            if (!empty($nombreCompleto)) {
                                $nombre = $nombreCompleto;
                            }
                        }
                        
                        return [
                            'user_id' => $user->id_usuario,
                            'nombre' => $nombre,
                            'eventos_count' => $participacionesGrupo->unique('evento_id')->count(),
                            'participaciones_count' => $participacionesGrupo->count(),
                            'horas_contribuidas' => $participacionesGrupo->count() * 2 // Estimación: 2 horas por participación
                        ];
                    })
                    ->filter()
                    ->values();
                    
                Log::info('Top Voluntarios: Voluntarios de eventos regulares agrupados', [
                    'count' => $voluntariosEventos->count(),
                    'voluntarios' => $voluntariosEventos->toArray()
                ]);
            } else {
                Log::warning('Top Voluntarios: No hay eventos regulares para esta ONG', ['ong_id' => $ongId]);
            }

            // Voluntarios de mega eventos
            $voluntariosMega = collect();
            if (!empty($megaEventosIds)) {
                $isPostgreSQL = DB::getDriverName() === 'pgsql';
                
                // Cambiar condición de activo para incluir NULL
                $queryMegaVoluntarios = DB::table('mega_evento_participantes_externos as mepe')
                    ->whereIn('mepe.mega_evento_id', $megaEventosIds)
                    ->where(function($q) {
                        $q->where('mepe.activo', true)->orWhereNull('mepe.activo');
                    })
                    ->join('integrantes_externos as ie', 'mepe.integrante_externo_id', '=', 'ie.user_id')
                    ->join('usuarios as u', 'ie.user_id', '=', 'u.id_usuario');
                
                if ($isPostgreSQL) {
                    $voluntariosMega = $queryMegaVoluntarios
                        ->select(
                            'u.id_usuario',
                            DB::raw("COALESCE(NULLIF(TRIM(COALESCE(ie.nombres, '') || ' ' || COALESCE(ie.apellidos, '')), ''), u.nombre_usuario, 'Usuario') as nombre"),
                            DB::raw('COUNT(DISTINCT mepe.mega_evento_id) as eventos_count'),
                            DB::raw('COUNT(*) as participaciones_count')
                        )
                        ->groupBy('u.id_usuario', 'u.nombre_usuario', 'ie.nombres', 'ie.apellidos')
                        ->get();
                } else {
                    $voluntariosMega = $queryMegaVoluntarios
                        ->select(
                            'u.id_usuario',
                            DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(ie.nombres, ''), ' ', COALESCE(ie.apellidos, ''))), ''), u.nombre_usuario, 'Usuario') as nombre"),
                            DB::raw('COUNT(DISTINCT mepe.mega_evento_id) as eventos_count'),
                            DB::raw('COUNT(*) as participaciones_count')
                        )
                        ->groupBy('u.id_usuario', 'u.nombre_usuario', 'ie.nombres', 'ie.apellidos')
                        ->get();
                }
                
                Log::info('Top Voluntarios: Query de mega eventos ejecutada', ['count' => $voluntariosMega->count()]);
                
                $voluntariosMega = $voluntariosMega->map(function ($item) {
                    return [
                        'user_id' => $item->id_usuario,
                        'nombre' => $item->nombre ?? 'Usuario',
                        'eventos_count' => (int) $item->eventos_count,
                        'participaciones_count' => (int) $item->participaciones_count,
                        'horas_contribuidas' => (int) $item->participaciones_count * 2
                    ];
                });
                    
                Log::info('Top Voluntarios: Voluntarios de mega eventos procesados', ['count' => $voluntariosMega->count()]);
            }

            // Consolidar voluntarios
            $voluntariosConsolidados = collect();
            
            foreach ($voluntariosEventos as $vol) {
                $voluntariosConsolidados->put($vol['user_id'], $vol);
            }
            
            foreach ($voluntariosMega as $vol) {
                if ($voluntariosConsolidados->has($vol['user_id'])) {
                    // Obtener el array actual, modificarlo y volver a ponerlo
                    $volActual = $voluntariosConsolidados->get($vol['user_id']);
                    $volActual['eventos_count'] += $vol['eventos_count'];
                    $volActual['participaciones_count'] += $vol['participaciones_count'];
                    $volActual['horas_contribuidas'] = $volActual['participaciones_count'] * 2;
                    $voluntariosConsolidados->put($vol['user_id'], $volActual);
                } else {
                    $voluntariosConsolidados->put($vol['user_id'], $vol);
                }
            }

            // Ordenar y tomar top 10
            $topVoluntarios = $voluntariosConsolidados
                ->sortByDesc('eventos_count')
                ->take(10)
                ->values()
                ->map(function ($vol) {
                    return [
                        'nombre' => $vol['nombre'],
                        'eventos_count' => (int) $vol['eventos_count'],
                        'horas_contribuidas' => (int) $vol['horas_contribuidas']
                    ];
                })
                ->toArray();

            Log::info('Top Voluntarios: Resultado final', [
                'count' => count($topVoluntarios),
                'primer_voluntario' => $topVoluntarios[0] ?? null
            ]);
            
            return $topVoluntarios;

        } catch (\Throwable $e) {
            Log::error('Error en obtenerTopVoluntarios: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ong_id' => $ongId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Obtener Top 10 Eventos con Más Colaboradores
     */
    private function obtenerEventosColaboracion(int $ongId, array $filtros): array
    {
        try {
            $isPostgreSQL = DB::getDriverName() === 'pgsql';
            $eventosColaboracion = collect();

            // Obtener TODOS los eventos regulares de la ONG (los filtros son opcionales)
            $queryEventos = Evento::where('ong_id', $ongId);
            
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                $queryEventos->where('fecha_inicio', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                $queryEventos->where('fecha_inicio', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $queryEventos->where('tipo_evento', $filtros['categoria']);
            }
            
            $eventos = $queryEventos->select('id', 'titulo', 'fecha_inicio')->get();
            Log::info('Eventos Colaboración: Eventos regulares encontrados', [
                'count' => $eventos->count(),
                'filtros_aplicados' => $filtros
            ]);

            foreach ($eventos as $evento) {
                try {
                    // Contar voluntarios únicos
                    if ($isPostgreSQL) {
                        $voluntariosCount = DB::table('evento_participaciones')
                            ->where('evento_id', $evento->id)
                            ->whereNotNull('externo_id')
                            ->select(DB::raw('COUNT(DISTINCT externo_id) as total'))
                            ->first()
                            ->total ?? 0;
                        
                        $empresasCount = DB::table('evento_empresas_participantes')
                            ->where('evento_id', $evento->id)
                            ->where('activo', true)
                            ->select(DB::raw('COUNT(DISTINCT empresa_id) as total'))
                            ->first()
                            ->total ?? 0;
                    } else {
                        $voluntariosCount = DB::table('evento_participaciones')
                            ->where('evento_id', $evento->id)
                            ->whereNotNull('externo_id')
                            ->distinct('externo_id')
                            ->count('externo_id');
                        
                        $empresasCount = DB::table('evento_empresas_participantes')
                            ->where('evento_id', $evento->id)
                            ->where('activo', true)
                            ->distinct('empresa_id')
                            ->count('empresa_id');
                    }

                    $eventosColaboracion->push([
                        'titulo' => $evento->titulo,
                        'fecha_inicio' => $evento->fecha_inicio ? Carbon::parse($evento->fecha_inicio)->format('d/m/Y') : 'N/A',
                        'voluntarios_count' => (int) $voluntariosCount,
                        'empresas_count' => (int) $empresasCount,
                        'total_colaboradores' => (int) ($voluntariosCount + $empresasCount)
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Error procesando evento regular para colaboración', [
                        'evento_id' => $evento->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Obtener TODOS los mega eventos de la ONG (los filtros son opcionales)
            $queryMega = MegaEvento::where('ong_organizadora_principal', $ongId);
            
            if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
                $queryMega->where('fecha_creacion', '>=', $filtros['fecha_inicio']);
            }
            if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
                $queryMega->where('fecha_creacion', '<=', $filtros['fecha_fin']);
            }
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $queryMega->where('categoria', $filtros['categoria']);
            }
            
            $megaEventos = $queryMega->select('mega_evento_id', 'titulo', 'fecha_creacion')->get();
            Log::info('Eventos Colaboración: Mega eventos encontrados', [
                'count' => $megaEventos->count(),
                'filtros_aplicados' => $filtros
            ]);

            foreach ($megaEventos as $megaEvento) {
                try {
                    // Contar voluntarios únicos y empresas
                    if ($isPostgreSQL) {
                        $voluntariosCount = DB::table('mega_evento_participantes_externos')
                            ->where('mega_evento_id', $megaEvento->mega_evento_id)
                            ->where('activo', true)
                            ->select(DB::raw('COUNT(DISTINCT integrante_externo_id) as total'))
                            ->first()
                            ->total ?? 0;
                        
                        $empresasCount = DB::table('mega_evento_patrocinadores')
                            ->where('mega_evento_id', $megaEvento->mega_evento_id)
                            ->where('activo', true)
                            ->select(DB::raw('COUNT(DISTINCT empresa_id) as total'))
                            ->first()
                            ->total ?? 0;
                    } else {
                        $voluntariosCount = DB::table('mega_evento_participantes_externos')
                            ->where('mega_evento_id', $megaEvento->mega_evento_id)
                            ->where('activo', true)
                            ->distinct('integrante_externo_id')
                            ->count('integrante_externo_id');
                        
                        $empresasCount = DB::table('mega_evento_patrocinadores')
                            ->where('mega_evento_id', $megaEvento->mega_evento_id)
                            ->where('activo', true)
                            ->distinct('empresa_id')
                            ->count('empresa_id');
                    }

                    $eventosColaboracion->push([
                        'titulo' => $megaEvento->titulo,
                        'fecha_inicio' => $megaEvento->fecha_creacion ? Carbon::parse($megaEvento->fecha_creacion)->format('d/m/Y') : 'N/A',
                        'voluntarios_count' => (int) $voluntariosCount,
                        'empresas_count' => (int) $empresasCount,
                        'total_colaboradores' => (int) ($voluntariosCount + $empresasCount)
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Error procesando mega evento para colaboración', [
                        'mega_evento_id' => $megaEvento->mega_evento_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Ordenar por total_colaboradores y tomar top 10
            $topEventos = $eventosColaboracion
                ->sortByDesc('total_colaboradores')
                ->take(10)
                ->values()
                ->toArray();

            Log::info('Eventos Colaboración: Resultado final', [
                'count' => count($topEventos),
                'primer_evento' => $topEventos[0] ?? null
            ]);
            
            return $topEventos;

        } catch (\Throwable $e) {
            Log::error('Error en obtenerEventosColaboracion: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ong_id' => $ongId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Validar y procesar filtros del request
     * 
     * @param Request $request
     * @return array
     */
    private function validarFiltros(Request $request): array
    {
        $filtros = [];
        
        Log::info('Validando filtros del request', [
            'all_params' => $request->all(),
            'has_fecha_inicio' => $request->has('fecha_inicio'),
            'has_fecha_fin' => $request->has('fecha_fin'),
            'has_categoria' => $request->has('categoria'),
            'fecha_inicio_value' => $request->input('fecha_inicio'),
            'fecha_fin_value' => $request->input('fecha_fin'),
            'categoria_value' => $request->input('categoria')
        ]);

        // Fecha inicio (solo si se proporciona explícitamente)
        if ($request->has('fecha_inicio') && !empty($request->fecha_inicio)) {
            try {
                $filtros['fecha_inicio'] = Carbon::parse($request->fecha_inicio)->format('Y-m-d');
                Log::info('Filtro fecha_inicio aplicado', ['fecha' => $filtros['fecha_inicio']]);
            } catch (\Exception $e) {
                Log::warning('Fecha inicio inválida: ' . $request->fecha_inicio, ['error' => $e->getMessage()]);
            }
        }

        // Fecha fin (solo si se proporciona explícitamente)
        if ($request->has('fecha_fin') && !empty($request->fecha_fin)) {
            try {
                $filtros['fecha_fin'] = Carbon::parse($request->fecha_fin)->format('Y-m-d');
                Log::info('Filtro fecha_fin aplicado', ['fecha' => $filtros['fecha_fin']]);
            } catch (\Exception $e) {
                Log::warning('Fecha fin inválida: ' . $request->fecha_fin, ['error' => $e->getMessage()]);
            }
        }

        // Validar que fecha_inicio <= fecha_fin (solo si ambas están presentes)
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            if (Carbon::parse($filtros['fecha_inicio'])->gt(Carbon::parse($filtros['fecha_fin']))) {
                Log::warning('Fecha inicio mayor que fecha fin, invirtiendo valores');
                $temp = $filtros['fecha_inicio'];
                $filtros['fecha_inicio'] = $filtros['fecha_fin'];
                $filtros['fecha_fin'] = $temp;
            }
        }

        // Categoría (opcional)
        if ($request->has('categoria') && !empty($request->categoria)) {
            $categoriasValidas = ['social', 'educativo', 'ambiental', 'salud', 'cultural', 'deportivo'];
            if (in_array(strtolower($request->categoria), $categoriasValidas)) {
                $filtros['categoria'] = strtolower($request->categoria);
                Log::info('Filtro categoria aplicado', ['categoria' => $filtros['categoria']]);
            } else {
                Log::warning('Categoría inválida: ' . $request->categoria);
            }
        }

        Log::info('Filtros validados finales', ['filtros' => $filtros]);
        
        return $filtros;
    }
}
