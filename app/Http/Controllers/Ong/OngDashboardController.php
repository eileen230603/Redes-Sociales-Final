<?php

namespace App\Http\Controllers\Ong;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\EventoParticipacion;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\Ong;
use App\Models\IntegranteExterno;
use App\Models\MegaEvento;
use App\Models\MegaEventoReaccion;
use App\Models\MegaEventoCompartido;
use App\Models\MegaEventoParticipanteExterno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class OngDashboardController extends Controller
{
    /**
     * Mostrar vista del dashboard
     */
    public function index()
    {
        return view('ong.dashboard');
    }

    /**
     * Obtener datos completos del dashboard de la ONG
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden acceder al dashboard',
                    'message' => 'Acceso denegado'
                ], 403);
            }

            $ongId = $user->id_usuario;

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::now()->subMonths(6);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            $estadoEvento = $request->input('estado_evento');
            $tipoParticipacion = $request->input('tipo_participacion');
            $busquedaEvento = $request->input('busqueda_evento');

            // Cache key único por filtros
            $cacheKey = "ong_dashboard_{$ongId}_" . md5(json_encode([
                $fechaInicio->format('Y-m-d'),
                $fechaFin->format('Y-m-d'),
                $estadoEvento,
                $tipoParticipacion,
                $busquedaEvento
            ]));

            // Intentar obtener de cache (30 minutos para reducir carga del servidor)
            $datos = Cache::remember($cacheKey, 1800, function () use ($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento) {
                return $this->obtenerDatosDashboard($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento);
            });

            return response()->json([
                'success' => true,
                'data' => $datos,
                'message' => 'Dashboard cargado correctamente'
            ]);

        } catch (\Throwable $e) {
            Log::error('Error en dashboard de ONG:', [
                'ong_id' => $request->user()->id_usuario ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener dashboard: ' . $e->getMessage(),
                'message' => 'Error al cargar datos del dashboard'
            ], 500);
        }
    }

    /**
     * Exportar dashboard en PDF
     */
    public function exportarPdf(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden exportar reportes',
                    'message' => 'Acceso denegado'
                ], 403);
            }

            $ongId = $user->id_usuario;
            $ong = Ong::find($ongId);

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::now()->subMonths(6);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            $estadoEvento = $request->input('estado_evento');
            $tipoParticipacion = $request->input('tipo_participacion');
            $busquedaEvento = $request->input('busqueda_evento');

            // Aumentar límites para generación de PDF
            ini_set('memory_limit', '256M');
            set_time_limit(120);
            
            // Cache key para PDF (30 minutos)
            $cacheKey = 'pdf_dashboard_ong_' . $ongId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d') . ($estadoEvento ?? '') . ($tipoParticipacion ?? '') . ($busquedaEvento ?? ''));
            
            // Obtener datos con cache
            $datos = Cache::remember($cacheKey, 1800, function() use ($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento) {
                return $this->obtenerDatosDashboard($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento);
            });
            
            // Cache para URLs de gráficos (30 minutos)
            $graficosCacheKey = 'pdf_graficos_ong_' . $ongId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            $graficosUrls = Cache::remember($graficosCacheKey, 1800, function() use ($datos) {
                return $this->generarUrlsGraficos($datos);
            });
            
            // Cache para logos (1 hora)
            $logoOng = Cache::remember("logo_ong_{$ongId}", 3600, function() use ($ong) {
                return $ong->logo_url ?? null;
            });
            
            $logoUni2 = Cache::remember('logo_uni2_path', 3600, function() {
                $path = public_path('assets/img/UNI2 - copia.png');
                return file_exists($path) ? $path : null;
            });

            $pdf = Pdf::loadView('ong.dashboard-pdf', [
                'ong' => $ong,
                'datos' => $datos,
                'graficos_urls' => $graficosUrls,
                'logo_ong' => $logoOng,
                'logo_uni2' => $logoUni2,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'fecha_generacion' => now()->format('d/m/Y H:i:s')
            ])->setPaper('a4', 'portrait')
              ->setOption('enable-local-file-access', true)
              ->setOption('isRemoteEnabled', true)
              ->setOption('isHtml5ParserEnabled', true)
              ->setOption('defaultFont', 'Arial')
              ->setOption('dpi', 96);

            $filename = 'dashboard-ong-' . $ongId . '-' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            // Generar PDF una sola vez
            $pdfContent = $pdf->output();
            
            // Retornar PDF como respuesta binaria con headers correctos
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');

        } catch (\Throwable $e) {
            Log::error('Error generando PDF del dashboard ONG:', [
                'ong_id' => $request->user()->id_usuario ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage(),
                'message' => 'Error al generar el reporte PDF'
            ], 500);
        }
    }

    /**
     * Generar PDF del Dashboard General de la ONG
     * Ruta: /api/ong/dashboard/pdf
     */
    public function generarPDFDashboard(Request $request)
    {
        try {
            // 1) Verificar autenticación y obtener ONG del usuario
            if (!auth()->check()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $user = auth()->user();
            if ($user->tipo_usuario !== 'ONG') {
                return response()->json(['error' => 'Usuario no pertenece a ninguna ONG'], 403);
            }

            $ong = $user->ong;
            if (!$ong) {
                return response()->json(['error' => 'ONG no encontrada'], 403);
            }

            $ongId = $ong->user_id ?? $user->id_usuario;
            Log::info('Generando PDF dashboard para ONG: ' . $ongId);

            // Aumentar límites de memoria y tiempo
            ini_set('memory_limit', '512M');
            set_time_limit(120);

            // 2) Obtener TODAS las estadísticas necesarias con queries Eloquent optimizadas
            $totalEventos = Evento::where('ong_id', $ongId)->count();
            
            $eventosActivos = Evento::where('ong_id', $ongId)
                ->where('estado', 'Publicado')
                ->whereDate('fecha_fin', '>=', now())
                ->count();
            
            $eventosFinalizados = Evento::where('ong_id', $ongId)
                ->where('estado', 'Publicado')
                ->whereDate('fecha_fin', '<', now())
                ->count();
            
            $totalReacciones = EventoReaccion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->count();
            
            $totalCompartidos = EventoCompartido::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->count();
            
            // Contar voluntarios únicos (todos los participantes se consideran voluntarios en este contexto)
            $totalVoluntarios = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
              ->distinct('externo_id')
              ->count('externo_id');
            
            $totalParticipantes = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->count();
            
            $participantesAprobados = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->where('estado', 'aprobado')->count();
            
            $participantesPendientes = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->where('estado', 'pendiente')->count();

            // 3) Obtener datos para gráficos
            // Usar TO_CHAR para PostgreSQL en lugar de DATE_FORMAT de MySQL
            $reaccionesPorMes = EventoReaccion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
            ->select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mes"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('mes')
            ->limit(12)
            ->get()
            ->pluck('total', 'mes')
            ->toArray();

            $compartidosPorMes = EventoCompartido::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
            ->select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mes"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('mes')
            ->limit(12)
            ->get()
            ->pluck('total', 'mes')
            ->toArray();

            $inscripcionesPorMes = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
            ->select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mes"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('mes')
            ->limit(12)
            ->get()
            ->pluck('total', 'mes')
            ->toArray();

            $eventosPorEstado = Evento::where('ong_id', $ongId)
                ->select('estado', DB::raw('COUNT(*) as total'))
                ->groupBy('estado')
                ->get()
                ->pluck('total', 'estado')
                ->toArray();

            // Top eventos con conteos manuales
            $topEventos = Evento::where('ong_id', $ongId)
                ->get()
                ->map(function($evento) {
                    $evento->reacciones_count = EventoReaccion::where('evento_id', $evento->id)->count();
                    $evento->compartidos_count = EventoCompartido::where('evento_id', $evento->id)->count();
                    $evento->participantes_count = EventoParticipacion::where('evento_id', $evento->id)->count();
                    return $evento;
                })
                ->sortByDesc('reacciones_count')
                ->take(10)
                ->values();

            // Top voluntarios
            $topVoluntarios = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
            ->whereNotNull('externo_id')
            ->select('externo_id', DB::raw('COUNT(*) as participaciones_count'))
            ->groupBy('externo_id')
            ->orderByDesc('participaciones_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $user = \App\Models\User::find($item->externo_id);
                if ($user) {
                    $user->participaciones_count = $item->participaciones_count;
                    return $user;
                }
                return null;
            })
            ->filter();

            // 4) Generar URLs de gráficos con QuickChart API
            $chartReacciones = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($reaccionesPorMes),
                    'datasets' => [[
                        'label' => 'Reacciones',
                        'data' => array_values($reaccionesPorMes),
                        'borderColor' => '#dc3545',
                        'backgroundColor' => 'rgba(220,53,69,0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]]
                ],
                'options' => [
                    'responsive' => false,
                    'maintainAspectRatio' => false,
                    'scales' => ['y' => ['beginAtZero' => true]]
                ]
            ])) . '&width=700&height=300&backgroundColor=white&devicePixelRatio=2';

            $chartCompartidos = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($compartidosPorMes),
                    'datasets' => [[
                        'label' => 'Compartidos',
                        'data' => array_values($compartidosPorMes),
                        'borderColor' => '#00A36C',
                        'backgroundColor' => 'rgba(0,163,108,0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]]
                ],
                'options' => [
                    'responsive' => false,
                    'maintainAspectRatio' => false,
                    'scales' => ['y' => ['beginAtZero' => true]]
                ]
            ])) . '&width=700&height=300&backgroundColor=white&devicePixelRatio=2';

            $chartInscripciones = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($inscripcionesPorMes),
                    'datasets' => [[
                        'label' => 'Inscripciones',
                        'data' => array_values($inscripcionesPorMes),
                        'borderColor' => '#0C2B44',
                        'backgroundColor' => 'rgba(12,43,68,0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]]
                ],
                'options' => [
                    'responsive' => false,
                    'maintainAspectRatio' => false,
                    'scales' => ['y' => ['beginAtZero' => true]]
                ]
            ])) . '&width=700&height=300&backgroundColor=white&devicePixelRatio=2';

            // 5) Preparar array de datos
            $estadisticas = compact(
                'totalEventos', 'eventosActivos', 'eventosFinalizados',
                'totalReacciones', 'totalCompartidos', 'totalVoluntarios',
                'totalParticipantes', 'participantesAprobados', 'participantesPendientes'
            );

            $data = [
                'ong' => $ong,
                'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
                'estadisticas' => $estadisticas,
                'topEventos' => $topEventos,
                'topVoluntarios' => $topVoluntarios,
                'graficos' => compact('chartReacciones', 'chartCompartidos', 'chartInscripciones')
            ];

            Log::info('Estadísticas calculadas: ' . json_encode($estadisticas));

            // 6) Generar PDF con try-catch robusto
            try {
                $pdf = Pdf::loadView('ong.dashboard.dashboard-pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'enable-local-file-access' => true
                ]);
                
                $filename = 'dashboard-ong-' . $ongId . '-' . date('Ymd') . '.pdf';
                return $pdf->download($filename);
                
            } catch (\Exception $e) {
                Log::error('Error generando PDF dashboard ONG: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Error al generar PDF: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Throwable $e) {
            Log::error('Error en generarPDFDashboard: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar dashboard en Excel
     */
    public function exportarExcel(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden exportar reportes',
                    'message' => 'Acceso denegado'
                ], 403);
            }

            $ongId = $user->id_usuario;
            $ong = Ong::find($ongId);

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::now()->subMonths(6);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            $estadoEvento = $request->input('estado_evento');
            $tipoParticipacion = $request->input('tipo_participacion');
            $busquedaEvento = $request->input('busqueda_evento');

            // Cache key para Excel (30 minutos)
            $cacheKey = 'excel_dashboard_ong_' . $ongId . '_' . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d') . ($estadoEvento ?? '') . ($tipoParticipacion ?? '') . ($busquedaEvento ?? ''));
            
            // Obtener datos con cache
            $datos = Cache::remember($cacheKey, 1800, function() use ($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento) {
                return $this->obtenerDatosDashboard($ongId, $fechaInicio, $fechaFin, $estadoEvento, $tipoParticipacion, $busquedaEvento);
            });

            try {
                // Verificar que la clase existe
                if (!class_exists(\App\Exports\OngDashboardExport::class)) {
                    throw new \Exception('La clase de exportación no está disponible');
                }
                
                $export = new \App\Exports\OngDashboardExport($ong, $datos, $fechaInicio, $fechaFin);
                
                $filename = 'dashboard-ong-' . $ongId . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                
                // Forzar descarga automática
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            } catch (\Throwable $e) {
                Log::error('Error creando export Excel:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                throw new \Exception('Error al generar Excel: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('Error generando Excel del dashboard ONG:', [
                'ong_id' => $request->user()->id_usuario ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar Excel: ' . $e->getMessage(),
                'message' => 'Error al generar el reporte Excel'
            ], 500);
        }
    }

    /**
     * Obtener datos completos del dashboard
     */
    private function obtenerDatosDashboard($ongId, $fechaInicio, $fechaFin, $estadoEvento = null, $tipoParticipacion = null, $busquedaEvento = null)
    {
        // Query base de eventos regulares (optimizado: solo eventos creados en el rango)
        $queryEventos = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($estadoEvento) {
            // Normalizar estado para eventos regulares
            if ($estadoEvento === 'activo') {
                $queryEventos->where('estado', 'publicado')
                    ->where(function($q) {
                        // Excluir los que ya finalizaron por fecha
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', Carbon::now());
                    });
            } elseif ($estadoEvento === 'inactivo') {
                $queryEventos->where('estado', 'borrador')
                    ->where(function($q) {
                        // Excluir los que ya finalizaron por fecha
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', Carbon::now());
                    });
            } elseif ($estadoEvento === 'finalizado') {
                $queryEventos->where(function($q) {
                    $q->where('estado', 'finalizado')
                      ->orWhere(function($q2) {
                          // Eventos con fecha_fin ya pasada
                          $q2->whereNotNull('fecha_fin')
                             ->where('fecha_fin', '<=', Carbon::now());
                      });
                });
            } else {
                $queryEventos->where('estado', $estadoEvento);
            }
        }

        if ($busquedaEvento) {
            $queryEventos->where('titulo', 'like', "%{$busquedaEvento}%");
        }

        $eventos = $queryEventos->get();
        
        // Obtener mega eventos también (optimizado: solo eventos creados en el rango)
        $queryMegaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->whereBetween('fecha_creacion', [$fechaInicio, $fechaFin]);
        
        // Aplicar filtro de estado si existe
        if ($estadoEvento) {
            if ($estadoEvento === 'finalizado') {
                $queryMegaEventos->where(function($q) {
                    $q->where('estado', 'finalizado')
                      ->orWhere('estado', 'completado')
                      ->orWhere(function($q2) {
                          // Eventos con fecha_fin ya pasada
                          $q2->whereNotNull('fecha_fin')
                             ->where('fecha_fin', '<=', Carbon::now());
                      });
                });
            } elseif ($estadoEvento === 'activo') {
                $queryMegaEventos->where('activo', true)
                    ->where(function($q) {
                        $q->where('estado', 'activo')
                          ->orWhere('estado', 'publicado')
                          ->orWhere('estado', 'en_curso');
                    })
                    ->where(function($q) {
                        // Excluir los que ya finalizaron por fecha
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', Carbon::now());
                    });
            } elseif ($estadoEvento === 'inactivo') {
                $queryMegaEventos->where(function($q) {
                    $q->where('activo', false)
                      ->orWhere('estado', 'inactivo')
                      ->orWhere('estado', 'borrador');
                })
                ->where(function($q) {
                    // Excluir los que ya finalizaron por fecha
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>', Carbon::now());
                });
            }
        }
        
        // Aplicar búsqueda si existe
        if ($busquedaEvento) {
            $queryMegaEventos->where('titulo', 'like', "%{$busquedaEvento}%");
        }
        
        $megaEventos = $queryMegaEventos->get();

        // Métricas principales (pasar también los mega eventos obtenidos)
        $metricas = $this->obtenerMetricasPrincipales($ongId, $fechaInicio, $fechaFin, $eventos, $megaEventos);
        
        // Tendencias mensuales
        $tendenciasMensuales = $this->obtenerTendenciasMensuales($ongId, $fechaInicio, $fechaFin);
        
        // Distribución de estados (pasar también los mega eventos obtenidos)
        $distribucionEstados = $this->obtenerDistribucionEstados($ongId, $eventos, $megaEventos);
        
        // Actividad semanal
        $actividadSemanal = $this->obtenerActividadSemanal($ongId, $fechaInicio, $fechaFin);
        
        // Comparativa entre eventos
        $comparativaEventos = $this->obtenerComparativaEventos($ongId, $eventos);
        
        // Top eventos por engagement
        $topEventos = $this->obtenerTopEventos($ongId, $eventos, 10);
        
        // Top voluntarios
        $topVoluntarios = $this->obtenerTopVoluntarios($ongId, 10);
        
        // Distribución de participantes
        $distribucionParticipantes = $this->obtenerDistribucionParticipantes($ongId, $fechaInicio, $fechaFin, $tipoParticipacion);
        
        // Listado de eventos (pasar también los mega eventos obtenidos)
        $listadoEventos = $this->obtenerListadoEventos($ongId, $eventos, $megaEventos);
        
        // Actividad reciente (30 días)
        $actividadReciente = $this->obtenerActividadReciente($ongId, 30);
        
        // Comparativas con período anterior
        $comparativas = $this->obtenerComparativas($ongId, $fechaInicio, $fechaFin);
        
        // Métricas radar
        $metricasRadar = $this->obtenerMetricasRadar($metricas);
        
        // Alertas
        $alertas = $this->obtenerAlertas($ongId, $eventos);

        return [
            'metricas' => $metricas,
            'tendencias_mensuales' => $tendenciasMensuales,
            'distribucion_estados' => $distribucionEstados,
            'actividad_semanal' => $actividadSemanal,
            'comparativa_eventos' => $comparativaEventos,
            'top_eventos' => $topEventos,
            'top_voluntarios' => $topVoluntarios,
            'distribucion_participantes' => $distribucionParticipantes,
            'listado_eventos' => $listadoEventos,
            'actividad_reciente' => $actividadReciente,
            'comparativas' => $comparativas,
            'metricas_radar' => $metricasRadar,
            'alertas' => $alertas
        ];
    }

    /**
     * Obtener métricas principales
     */
    private function obtenerMetricasPrincipales($ongId, $fechaInicio, $fechaFin, $eventos, $megaEventos = null)
    {
        $eventosIds = $eventos->pluck('id')->toArray();

        // Total eventos por estado (estados: borrador, publicado, finalizado, cancelado)
        // Contar eventos activos (publicado y fecha_fin no pasada)
        $eventosActivos = $eventos->filter(function($evento) {
            return $evento->estado === 'publicado' && 
                   (!$evento->fecha_fin || Carbon::parse($evento->fecha_fin)->isFuture());
        })->count();
        
        // Contar eventos inactivos (borrador y fecha_fin no pasada)
        $eventosInactivos = $eventos->filter(function($evento) {
            return $evento->estado === 'borrador' && 
                   (!$evento->fecha_fin || Carbon::parse($evento->fecha_fin)->isFuture());
        })->count();
        
        // Contar eventos finalizados (estado finalizado O fecha_fin ya pasó)
        $eventosFinalizados = $eventos->filter(function($evento) {
            return $evento->estado === 'finalizado' || 
                   ($evento->fecha_fin && Carbon::parse($evento->fecha_fin)->isPast());
        })->count();
        
        // Obtener mega eventos si no se pasaron como parámetro (optimizado)
        if ($megaEventos === null) {
            $megaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)
                ->whereBetween('fecha_creacion', [$fechaInicio, $fechaFin])
                ->get();
        }
        
        // Agregar mega eventos por estado
        foreach ($megaEventos as $megaEvento) {
            $estado = $megaEvento->estado;
            // Verificar si está finalizado (por estado o por fecha)
            $estaFinalizado = false;
            if ($estado === 'finalizado' || $estado === 'completado') {
                $estaFinalizado = true;
            } elseif ($megaEvento->fecha_fin && Carbon::parse($megaEvento->fecha_fin)->isPast()) {
                // Si la fecha de fin ya pasó, considerarlo finalizado
                $estaFinalizado = true;
            }
            
            if ($estaFinalizado) {
                $eventosFinalizados++;
            } elseif ($megaEvento->activo && ($estado === 'activo' || $estado === 'publicado' || $estado === 'en_curso')) {
                $eventosActivos++;
            } elseif (!$megaEvento->activo || $estado === 'inactivo' || $estado === 'borrador') {
                $eventosInactivos++;
            }
        }

        // Obtener IDs de mega eventos para consultas
        $megaEventosIds = $megaEventos->pluck('mega_evento_id')->toArray();

        // Total reacciones acumuladas (eventos regulares + mega eventos)
        $totalReacciones = EventoReaccion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();
        
        // Agregar reacciones de mega eventos
        if (!empty($megaEventosIds)) {
            $totalReacciones += MegaEventoReaccion::whereIn('mega_evento_id', $megaEventosIds)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count();
        }

        // Total compartidos (eventos regulares + mega eventos)
        $totalCompartidos = EventoCompartido::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();
        
        if (!empty($megaEventosIds)) {
            $totalCompartidos += MegaEventoCompartido::whereIn('mega_evento_id', $megaEventosIds)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count();
        }

        // Total voluntarios únicos (eventos regulares + mega eventos)
        $totalVoluntarios = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereNotNull('externo_id')
            ->distinct('externo_id')
            ->count('externo_id');
        
        if (!empty($megaEventosIds)) {
            $totalVoluntarios += DB::table('mega_evento_participantes_externos')
                ->whereIn('mega_evento_id', $megaEventosIds)
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->whereNotNull('integrante_externo_id')
                ->distinct('integrante_externo_id')
                ->count('integrante_externo_id');
        }

        // Total participantes (incluyendo no registrados)
        $participantesRegistrados = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();
        
        $participantesNoRegistrados = EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();
        
        $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;
        
        if (!empty($megaEventosIds)) {
            $totalParticipantes += DB::table('mega_evento_participantes_externos')
                ->whereIn('mega_evento_id', $megaEventosIds)
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->count();
        }

        return [
            'eventos_activos' => $eventosActivos,
            'eventos_inactivos' => $eventosInactivos,
            'eventos_finalizados' => $eventosFinalizados,
            'total_reacciones' => $totalReacciones,
            'total_compartidos' => $totalCompartidos,
            'total_voluntarios' => $totalVoluntarios,
            'total_participantes' => $totalParticipantes
        ];
    }

    /**
     * Obtener tendencias mensuales
     */
    private function obtenerTendenciasMensuales($ongId, $fechaInicio, $fechaFin)
    {
        $eventosIds = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->pluck('id')
            ->toArray();

        $tendencias = [];

        $fechaActual = $fechaInicio->copy();
        while ($fechaActual <= $fechaFin) {
            $mes = $fechaActual->format('Y-m');
            $inicioMes = $fechaActual->copy()->startOfMonth();
            $finMes = $fechaActual->copy()->endOfMonth();

            $participantes = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->count();
            
            $participantesNoReg = EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->count();

            $tendencias[$mes] = $participantes + $participantesNoReg;

            $fechaActual->addMonth();
        }

        return $tendencias;
    }

    /**
     * Obtener distribución de estados
     */
    private function obtenerDistribucionEstados($ongId, $eventos, $megaEventos = null)
    {
        $distribucion = [
            'activo' => 0,
            'inactivo' => 0,
            'finalizado' => 0,
            'cancelado' => 0
        ];
        
        // Eventos regulares (estados: borrador, publicado, finalizado, cancelado)
        foreach ($eventos as $evento) {
            $estado = $evento->estado;
            
            // Verificar si está finalizado por fecha
            $estaFinalizadoPorFecha = false;
            if ($evento->fecha_fin && Carbon::parse($evento->fecha_fin)->isPast()) {
                $estaFinalizadoPorFecha = true;
            }
            
            // Normalizar estados: publicado -> activo, borrador -> inactivo
            if ($estaFinalizadoPorFecha || $estado === 'finalizado') {
                $distribucion['finalizado']++;
            } elseif ($estado === 'publicado' && !$estaFinalizadoPorFecha) {
                $distribucion['activo']++;
            } elseif ($estado === 'borrador' && !$estaFinalizadoPorFecha) {
                $distribucion['inactivo']++;
            } elseif ($estado === 'cancelado') {
                $distribucion['cancelado']++;
            }
        }
        
        // Mega eventos (usar los pasados como parámetro o obtener todos)
        if ($megaEventos === null) {
            $megaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)->get();
        }
        
        foreach ($megaEventos as $megaEvento) {
            $estado = $megaEvento->estado;
            $estaFinalizado = false;
            
            // Verificar si está finalizado (por estado o por fecha)
            if ($estado === 'finalizado' || $estado === 'completado') {
                $estaFinalizado = true;
            } elseif ($megaEvento->fecha_fin && Carbon::parse($megaEvento->fecha_fin)->isPast()) {
                // Si la fecha de fin ya pasó, considerarlo finalizado
                $estaFinalizado = true;
            }
            
            // Normalizar estados de mega eventos
            if ($estaFinalizado) {
                $distribucion['finalizado']++;
            } elseif ($megaEvento->activo && ($estado === 'activo' || $estado === 'publicado' || $estado === 'en_curso')) {
                $distribucion['activo']++;
            } elseif (!$megaEvento->activo || $estado === 'inactivo' || $estado === 'borrador') {
                $distribucion['inactivo']++;
            } elseif ($estado === 'cancelado') {
                $distribucion['cancelado']++;
            }
        }
        
        return $distribucion;
    }

    /**
     * Obtener actividad semanal
     */
    private function obtenerActividadSemanal($ongId, $fechaInicio, $fechaFin)
    {
        $eventosIds = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->pluck('id')
            ->toArray();

        $actividad = [];

        // Combinar todas las actividades
        $reacciones = EventoReaccion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha')
            ->get()
            ->pluck('fecha')
            ->toArray();

        $compartidos = EventoCompartido::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha')
            ->get()
            ->pluck('fecha')
            ->toArray();

        $participaciones = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha')
            ->get()
            ->pluck('fecha')
            ->toArray();

        $todasFechas = array_merge($reacciones, $compartidos, $participaciones);
        
        // Agrupar por semana
        foreach ($todasFechas as $fecha) {
            $semana = Carbon::parse($fecha)->format('Y-W');
            $actividad[$semana] = ($actividad[$semana] ?? 0) + 1;
        }

        ksort($actividad);
        return $actividad;
    }

    /**
     * Obtener comparativa entre eventos
     */
    private function obtenerComparativaEventos($ongId, $eventos)
    {
        $comparativa = [];

        foreach ($eventos as $evento) {
            $reacciones = EventoReaccion::where('evento_id', $evento->id)->count();
            $compartidos = EventoCompartido::where('evento_id', $evento->id)->count();
            $participantes = EventoParticipacion::where('evento_id', $evento->id)->count() +
                           EventoParticipanteNoRegistrado::where('evento_id', $evento->id)->count();

            $comparativa[] = [
                'evento_id' => $evento->id,
                'titulo' => $evento->titulo,
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
                'participantes' => $participantes
            ];
        }

        return $comparativa;
    }

    /**
     * Obtener top eventos por engagement
     */
    private function obtenerTopEventos($ongId, $eventos, $limite = 10)
    {
        $topEventos = [];

        foreach ($eventos as $evento) {
            $reacciones = EventoReaccion::where('evento_id', $evento->id)->count();
            $compartidos = EventoCompartido::where('evento_id', $evento->id)->count();
            $inscripciones = EventoParticipacion::where('evento_id', $evento->id)->count() +
                           EventoParticipanteNoRegistrado::where('evento_id', $evento->id)->count();

            $engagement = $reacciones + $compartidos + $inscripciones;

            $topEventos[] = [
                'evento_id' => $evento->id,
                'titulo' => $evento->titulo,
                'fecha_inicio' => $evento->fecha_inicio,
                'ubicacion' => $evento->ubicacion,
                'estado' => $evento->estado,
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
                'inscripciones' => $inscripciones,
                'engagement' => $engagement
            ];
        }

        // Ordenar por engagement descendente
        usort($topEventos, function($a, $b) {
            return $b['engagement'] <=> $a['engagement'];
        });

        return array_slice($topEventos, 0, $limite);
    }

    /**
     * Obtener top voluntarios
     */
    private function obtenerTopVoluntarios($ongId, $limite = 10)
    {
        $eventosIds = Evento::where('ong_id', $ongId)->pluck('id')->toArray();

        return DB::table('evento_participaciones as ep')
            ->join('integrantes_externos as ie', 'ep.externo_id', '=', 'ie.user_id')
            ->whereIn('ep.evento_id', $eventosIds)
            ->whereNotNull('ep.externo_id')
            ->select(
                'ie.user_id as externo_id',
                'ie.nombres',
                'ie.apellidos',
                'ie.email',
                DB::raw('COUNT(DISTINCT ep.evento_id) as eventos_participados'),
                DB::raw('COUNT(ep.id) as total_participaciones')
            )
            ->groupBy('ie.user_id', 'ie.nombres', 'ie.apellidos', 'ie.email')
            ->orderBy('eventos_participados', 'desc')
            ->orderBy('total_participaciones', 'desc')
            ->limit($limite)
            ->get()
            ->map(function($item) {
                return [
                    'externo_id' => $item->externo_id,
                    'nombre' => trim(($item->nombres ?? '') . ' ' . ($item->apellidos ?? '')),
                    'email' => $item->email ?? '',
                    'eventos_participados' => $item->eventos_participados,
                    'horas_contribuidas' => $item->total_participaciones * 2 // Estimación: 2 horas por participación
                ];
            })
            ->toArray();
    }

    /**
     * Obtener distribución de participantes
     */
    private function obtenerDistribucionParticipantes($ongId, $fechaInicio, $fechaFin, $tipoParticipacion = null)
    {
        $eventosIds = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->pluck('id')
            ->toArray();

        // Por estado de inscripción
        $porEstado = DB::table('evento_participaciones')
            ->whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $porEstadoNoReg = DB::table('evento_participantes_no_registrados')
            ->whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $distribucionEstado = [];
        foreach (array_merge(array_keys($porEstado), array_keys($porEstadoNoReg)) as $estado) {
            $distribucionEstado[$estado] = ($porEstado[$estado] ?? 0) + ($porEstadoNoReg[$estado] ?? 0);
        }

        // Por tipo (todos son participantes, pero podemos diferenciar por externo_id)
        $conExternoId = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereNotNull('externo_id')
            ->count();

        $sinExternoId = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereNull('externo_id')
            ->count();

        $noRegistrados = EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        return [
            'por_estado' => $distribucionEstado,
            'por_tipo' => [
                'voluntario' => $conExternoId,
                'asistente' => $sinExternoId,
                'colaborador' => $noRegistrados
            ]
        ];
    }

    /**
     * Obtener listado de eventos (incluyendo mega eventos categorizados)
     */
    private function obtenerListadoEventos($ongId, $eventos, $megaEventos = null)
    {
        $listado = [];
        
        // Eventos regulares
        foreach ($eventos as $evento) {
            $participantes = EventoParticipacion::where('evento_id', $evento->id)->count() +
                           EventoParticipanteNoRegistrado::where('evento_id', $evento->id)->count();

            // Construir ubicación desde direccion y ciudad
            $ubicacion = '';
            if ($evento->direccion) {
                $ubicacion = $evento->direccion;
            }
            if ($evento->ciudad) {
                $ubicacion .= ($ubicacion ? ', ' : '') . $evento->ciudad;
            }
            if (empty($ubicacion)) {
                $ubicacion = 'N/A';
            }
            
            // Normalizar estado para mostrar correctamente
            $estado = $evento->estado;
            
            // Verificar si está finalizado por fecha
            $estaFinalizadoPorFecha = false;
            if ($evento->fecha_fin && Carbon::parse($evento->fecha_fin)->isPast()) {
                $estaFinalizadoPorFecha = true;
            }
            
            if ($estaFinalizadoPorFecha) {
                $estado = 'finalizado';
            } elseif ($estado === 'publicado') {
                $estado = 'activo';
            } elseif ($estado === 'borrador') {
                $estado = 'inactivo';
            }
            
            $listado[] = [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'fecha_inicio' => $evento->fecha_inicio,
                'fecha_fin' => $evento->fecha_fin,
                'ubicacion' => $ubicacion,
                'estado' => $estado,
                'total_participantes' => $participantes,
                'tipo' => 'evento' // Tipo: evento regular
            ];
        }
        
        // Mega eventos (usar los pasados como parámetro o obtener todos)
        if ($megaEventos === null) {
            $megaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)->get();
        }
        
        foreach ($megaEventos as $megaEvento) {
            $participantes = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEvento->mega_evento_id)
                ->count();

            // Normalizar estado para mega eventos (verificar si está finalizado por fecha también)
            $estadoMega = $megaEvento->estado;
            $estaFinalizado = false;
            
            if ($estadoMega === 'finalizado' || $estadoMega === 'completado') {
                $estaFinalizado = true;
            } elseif ($megaEvento->fecha_fin && Carbon::parse($megaEvento->fecha_fin)->isPast()) {
                // Si la fecha de fin ya pasó, considerarlo finalizado
                $estaFinalizado = true;
            }
            
            if ($estaFinalizado) {
                $estadoMega = 'finalizado';
            } elseif ($megaEvento->activo && ($estadoMega === 'activo' || $estadoMega === 'publicado' || $estadoMega === 'en_curso')) {
                $estadoMega = 'activo';
            } elseif (!$megaEvento->activo || $estadoMega === 'inactivo' || $estadoMega === 'borrador') {
                $estadoMega = 'inactivo';
            }

            $listado[] = [
                'id' => $megaEvento->mega_evento_id,
                'titulo' => $megaEvento->titulo,
                'fecha_inicio' => $megaEvento->fecha_inicio,
                'fecha_fin' => $megaEvento->fecha_fin,
                'ubicacion' => $megaEvento->ubicacion ?? 'N/A',
                'estado' => $estadoMega,
                'total_participantes' => $participantes,
                'tipo' => 'mega_evento' // Tipo: mega evento
            ];
        }
        
        // Ordenar por total de participantes descendente
        usort($listado, function($a, $b) {
            return $b['total_participantes'] <=> $a['total_participantes'];
        });
        
        return $listado;
    }

    /**
     * Obtener actividad reciente (últimos N días)
     */
    private function obtenerActividadReciente($ongId, $dias = 30)
    {
        $fechaInicio = Carbon::now()->subDays($dias);
        $eventosIds = Evento::where('ong_id', $ongId)->pluck('id')->toArray();
        $actividad = [];

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $fechaInicio->copy()->addDays($i)->format('Y-m-d');
            
            $reacciones = EventoReaccion::whereIn('evento_id', $eventosIds)
                ->whereDate('created_at', $fecha)
                ->count();

            $compartidos = EventoCompartido::whereIn('evento_id', $eventosIds)
                ->whereDate('created_at', $fecha)
                ->count();

            $inscripciones = EventoParticipacion::whereIn('evento_id', $eventosIds)
                ->whereDate('created_at', $fecha)
                ->count();

            $actividad[$fecha] = [
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
                'inscripciones' => $inscripciones,
                'total' => $reacciones + $compartidos + $inscripciones
            ];
        }

        return $actividad;
    }

    /**
     * Obtener comparativas con período anterior
     */
    private function obtenerComparativas($ongId, $fechaInicio, $fechaFin)
    {
        $duracion = $fechaInicio->diffInDays($fechaFin);
        $periodoAnteriorInicio = $fechaInicio->copy()->subDays($duracion);
        $periodoAnteriorFin = $fechaInicio->copy()->subDay();

        $eventosActuales = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->pluck('id')
            ->toArray();

        $eventosAnteriores = Evento::where('ong_id', $ongId)
            ->whereBetween('created_at', [$periodoAnteriorInicio, $periodoAnteriorFin])
            ->pluck('id')
            ->toArray();

        $actual = [
            'reacciones' => EventoReaccion::whereIn('evento_id', $eventosActuales)->count(),
            'compartidos' => EventoCompartido::whereIn('evento_id', $eventosActuales)->count(),
            'voluntarios' => EventoParticipacion::whereIn('evento_id', $eventosActuales)->whereNotNull('externo_id')->distinct('externo_id')->count('externo_id'),
            'participantes' => EventoParticipacion::whereIn('evento_id', $eventosActuales)->count() + 
                             EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosActuales)->count()
        ];

        $anterior = [
            'reacciones' => EventoReaccion::whereIn('evento_id', $eventosAnteriores)->count(),
            'compartidos' => EventoCompartido::whereIn('evento_id', $eventosAnteriores)->count(),
            'voluntarios' => EventoParticipacion::whereIn('evento_id', $eventosAnteriores)->whereNotNull('externo_id')->distinct('externo_id')->count('externo_id'),
            'participantes' => EventoParticipacion::whereIn('evento_id', $eventosAnteriores)->count() + 
                             EventoParticipanteNoRegistrado::whereIn('evento_id', $eventosAnteriores)->count()
        ];

        $comparativas = [];
        foreach (['reacciones', 'compartidos', 'voluntarios', 'participantes'] as $metrica) {
            $valorActual = $actual[$metrica] ?? 0;
            $valorAnterior = $anterior[$metrica] ?? 0;
            $crecimiento = $valorAnterior > 0 
                ? (($valorActual - $valorAnterior) / $valorAnterior) * 100 
                : ($valorActual > 0 ? 100 : 0);
            
            $comparativas[$metrica] = [
                'actual' => $valorActual,
                'anterior' => $valorAnterior,
                'crecimiento' => round($crecimiento, 2),
                'tendencia' => $crecimiento > 0 ? 'up' : ($crecimiento < 0 ? 'down' : 'stable')
            ];
        }

        return $comparativas;
    }

    /**
     * Obtener métricas para gráfico radar
     */
    private function obtenerMetricasRadar($metricas)
    {
        $maxValores = [
            'reacciones' => max($metricas['total_reacciones'], 100),
            'compartidos' => max($metricas['total_compartidos'], 100),
            'voluntarios' => max($metricas['total_voluntarios'], 100),
            'participantes' => max($metricas['total_participantes'], 100)
        ];

        return [
            'reacciones' => ($metricas['total_reacciones'] / $maxValores['reacciones']) * 100,
            'compartidos' => ($metricas['total_compartidos'] / $maxValores['compartidos']) * 100,
            'voluntarios' => ($metricas['total_voluntarios'] / $maxValores['voluntarios']) * 100,
            'participantes' => ($metricas['total_participantes'] / $maxValores['participantes']) * 100
        ];
    }

    /**
     * Obtener alertas
     */
    private function obtenerAlertas($ongId, $eventos)
    {
        $alertas = [];

        foreach ($eventos as $evento) {
            $participantes = EventoParticipacion::where('evento_id', $evento->id)->count() +
                           EventoParticipanteNoRegistrado::where('evento_id', $evento->id)->count();

            // Alerta: baja participación
            if ($participantes < 10 && $evento->estado === 'activo') {
                $alertas[] = [
                    'tipo' => 'baja_participacion',
                    'severidad' => 'warning',
                    'mensaje' => "El evento '{$evento->titulo}' tiene menos de 10 participantes",
                    'evento_id' => $evento->id
                ];
            }

            // Alerta: evento próximo sin voluntarios
            if ($evento->fecha_inicio && Carbon::parse($evento->fecha_inicio)->isFuture() && 
                Carbon::parse($evento->fecha_inicio)->diffInDays(Carbon::now()) <= 7) {
                $voluntarios = EventoParticipacion::where('evento_id', $evento->id)
                    ->whereNotNull('externo_id')
                    ->count();
                
                if ($voluntarios < 5) {
                    $alertas[] = [
                        'tipo' => 'sin_voluntarios',
                        'severidad' => 'danger',
                        'mensaje' => "El evento '{$evento->titulo}' inicia pronto y tiene menos de 5 voluntarios",
                        'evento_id' => $evento->id
                    ];
                }
            }

            // Alerta: evento finalizado sin evaluación
            if ($evento->estado === 'finalizado' && $evento->fecha_fin && 
                Carbon::parse($evento->fecha_fin)->diffInDays(Carbon::now()) > 30) {
                $alertas[] = [
                    'tipo' => 'pendiente_evaluacion',
                    'severidad' => 'info',
                    'mensaje' => "El evento '{$evento->titulo}' finalizó hace más de 30 días y está pendiente de evaluación",
                    'evento_id' => $evento->id
                ];
            }
        }

        return $alertas;
    }

    /**
     * Generar URLs de gráficos usando QuickChart
     */
    private function generarUrlsGraficos($datos)
    {
        $baseUrl = 'https://quickchart.io/chart?c=';
        $params = '&width=700&height=350&backgroundColor=white&devicePixelRatio=1.5';
        
        // Gráfico de líneas - Tendencias mensuales
        $tendencias = $datos['tendencias_mensuales'] ?? [];
        if (empty($tendencias)) {
            $tendencias = ['Sin datos' => 0];
        }
        $chartLine = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($tendencias),
                'datasets' => [[
                    'label' => 'Participantes',
                    'data' => array_values($tendencias),
                    'borderColor' => '#00A36C',
                    'backgroundColor' => 'rgba(0, 163, 108, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Tendencias Mensuales de Participantes', 'font' => ['size' => 16]]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        
        // Gráfico de dona - Distribución de estados
        $estados = $datos['distribucion_estados'] ?? [];
        if (empty($estados)) {
            $estados = ['Sin datos' => 1];
        }
        $chartDona = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($estados),
                'datasets' => [[
                    'data' => array_values($estados),
                    'backgroundColor' => ['#00A36C', '#0C2B44', '#dc3545', '#17a2b8', '#ffc107']
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Distribución de Estados de Eventos', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'right']
                ]
            ]
        ];
        
        // Gráfico de barras - Comparativa eventos
        $comparativa = array_slice($datos['comparativa_eventos'] ?? [], 0, 10);
        if (empty($comparativa)) {
            $comparativa = [['titulo' => 'Sin datos', 'reacciones' => 0, 'compartidos' => 0]];
        }
        $chartBarras = [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($comparativa, 'titulo'),
                'datasets' => [
                    [
                        'label' => 'Reacciones',
                        'data' => array_column($comparativa, 'reacciones'),
                        'backgroundColor' => '#dc3545'
                    ],
                    [
                        'label' => 'Compartidos',
                        'data' => array_column($comparativa, 'compartidos'),
                        'backgroundColor' => '#00A36C'
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Comparativa de Rendimiento por Evento', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'top']
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        
        // Gráfico de área - Actividad semanal
        $actividad = $datos['actividad_semanal'] ?? [];
        if (empty($actividad)) {
            $actividad = ['Sin datos' => 0];
        }
        $chartArea = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($actividad),
                'datasets' => [[
                    'label' => 'Actividad Semanal',
                    'data' => array_values($actividad),
                    'borderColor' => '#17a2b8',
                    'backgroundColor' => 'rgba(23, 162, 184, 0.3)',
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Actividad Semanal Agregada', 'font' => ['size' => 16]]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        
        // Gráfico radar
        $metricasRadar = $datos['metricas_radar'] ?? [];
        $chartRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => ['Reacciones', 'Compartidos', 'Voluntarios', 'Participantes'],
                'datasets' => [[
                    'label' => 'Métricas',
                    'data' => [
                        $metricasRadar['reacciones'] ?? 0,
                        $metricasRadar['compartidos'] ?? 0,
                        $metricasRadar['voluntarios'] ?? 0,
                        $metricasRadar['participantes'] ?? 0
                    ],
                    'backgroundColor' => 'rgba(0, 163, 108, 0.2)',
                    'borderColor' => '#00A36C',
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Métricas Generales (Gráfico Radar)', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'r' => ['beginAtZero' => true, 'grid' => ['display' => true]]
                ]
            ]
        ];

        return [
            'tendencias_mensuales' => $baseUrl . urlencode(json_encode($chartLine)) . $params,
            'distribucion_estados' => $baseUrl . urlencode(json_encode($chartDona)) . $params,
            'comparativa_eventos' => $baseUrl . urlencode(json_encode($chartBarras)) . $params,
            'actividad_semanal' => $baseUrl . urlencode(json_encode($chartArea)) . $params,
            'radar' => $baseUrl . urlencode(json_encode($chartRadar)) . $params
        ];
    }
}

