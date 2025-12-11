<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exports\MegaEventosResumenExport;
use App\Exports\AnalisisTemporalExport;
use App\Exports\ParticipacionColaboracionExport;
use App\Exports\AnalisisGeograficoExport;
use App\Exports\RendimientoOngExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Controlador para generar reportes avanzados de ONGs
 * 
 * Este controlador maneja la generación de reportes operativos, tácticos y estratégicos
 * para apoyar la toma de decisiones de las ONGs. Incluye:
 * - Reportes ejecutivos con KPIs
 * - Análisis temporales y tendencias
 * - Análisis de participación y colaboración
 * - Análisis geográfico
 * - Rendimiento por ONG
 */
class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        // Removido middleware auth porque el sistema usa tokens en localStorage
        // La validación se hace en el frontend como en otras vistas de ONG
    }

    /**
     * Dashboard principal de reportes
     * Muestra el menú de navegación y KPIs destacados
     * 
     * Nota: La autenticación se maneja en el frontend con tokens de localStorage
     * El backend solo renderiza la vista, el JavaScript valida el acceso
     */
    public function index()
    {
        // No validamos autenticación aquí, se hace en el frontend
        // Pasamos un array vacío de KPIs, el JavaScript los cargará desde la API
        $kpis = [
            'total_eventos_general' => 0,
            'total_finalizados_general' => 0,
            'total_participantes' => 0,
            'total_patrocinadores' => 0,
            'tasa_finalizacion' => 0,
            'tasa_cancelacion' => 0,
            'detalle_tasa_finalizacion' => [
                'eventos_regulares' => ['total' => 0, 'finalizados' => 0, 'tasa' => 0, 'porcentaje' => 0],
                'mega_eventos' => ['total' => 0, 'finalizados' => 0, 'tasa' => 0, 'porcentaje' => 0],
                'consolidado' => ['total' => 0, 'finalizados' => 0, 'tasa' => 0],
            ],
        ];

        return view('ong.reportes.dashboard', compact('kpis'));
    }

    /**
     * Reporte 1: Resumen Ejecutivo de Mega Eventos
     * Muestra totales generales, KPIs principales y gráfico de torta por categorías
     * 
     * Nota: La autenticación se valida en el frontend, aquí solo renderizamos la vista
     */
    public function resumenEjecutivo(Request $request)
    {
        // Validar y sanitizar filtros
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'totales' => ['total_eventos' => 0, 'por_categoria' => [], 'por_estado' => []],
            'kpis' => ['eventos_finalizados' => 0, 'eventos_activos' => 0, 'tasa_finalizacion' => 0],
            'grafico_torta' => []
        ];

        return view('ong.reportes.resumen-ejecutivo', compact('datos', 'filtros'));
    }

    /**
     * Exportar Reporte 1 en PDF
     * 
     * Nota: Usa autenticación con Sanctum desde la API
     */
    /**
     * Obtener datos de la ONG para el PDF
     */
    private function getOngData($userId)
    {
        try {
            $ong = \App\Models\Ong::where('user_id', $userId)->first();
            $user = \App\Models\User::find($userId);
            
            $logoUrl = null;
            if ($ong && $ong->foto_perfil) {
                // Si es una URL completa, mantenerla
                if (filter_var($ong->foto_perfil, FILTER_VALIDATE_URL)) {
                    $logoUrl = $ong->foto_perfil;
                } else {
                    // Es una ruta relativa, construir la ruta completa
                    $fotoPath = ltrim($ong->foto_perfil, '/');
                    
                    // Intentar encontrar el archivo en storage
                    $storagePath = storage_path('app/public/' . $fotoPath);
                    if (file_exists($storagePath)) {
                        $logoUrl = $storagePath;
                    } else {
                        // Intentar en public/storage
                        $publicPath = public_path('storage/' . $fotoPath);
                        if (file_exists($publicPath)) {
                            $logoUrl = $publicPath;
                        } else {
                            // Si no existe, construir URL para intentar más tarde
                            $baseUrl = request()->getSchemeAndHttpHost() ?? env('APP_URL', 'http://10.26.5.12:8000');
                            $logoUrl = rtrim($baseUrl, '/') . '/storage/' . $fotoPath;
                        }
                    }
                }
            }
            
            return [
                'nombre' => $ong->nombre_ong ?? $user->nombre_usuario ?? 'ONG',
                'email' => $user->email ?? '',
                'telefono' => $ong->telefono ?? '',
                'direccion' => $ong->direccion ?? '',
                'logo_url' => $logoUrl
            ];
        } catch (\Exception $e) {
            Log::warning('Error obteniendo datos de ONG para PDF: ' . $e->getMessage());
            return [
                'nombre' => 'ONG',
                'email' => '',
                'telefono' => '',
                'direccion' => '',
                'logo_url' => null
            ];
        }
    }

    public function exportarResumenEjecutivoPDF(Request $request)
    {
        try {
            // Obtener usuario autenticado - intentar desde sesión primero (rutas web)
            $user = auth()->user();
            
            // Si no hay usuario desde sesión, intentar desde token (rutas API)
            if (!$user && $request->hasHeader('Authorization')) {
                $token = str_replace('Bearer ', '', $request->header('Authorization'));
                try {
                    $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
                } catch (\Exception $e) {
                    // Ignorar error
                }
            }
            
            // Si aún no hay usuario, intentar desde request->user() (middleware auth)
            if (!$user) {
                $user = $request->user();
            }
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                // Si es una petición AJAX o espera JSON, devolver JSON
                if ($request->expectsJson() || $request->hasHeader('X-Requested-With')) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Solo usuarios tipo ONG pueden acceder a los reportes'
                    ], 403);
                }
                // Si es una petición normal, redirigir al login
                return redirect()->route('login')->with('error', 'Debes iniciar sesión como ONG para acceder a los reportes');
            }

            $filtros = $this->validarFiltros($request);
            $datos = $this->reportService->getResumenEjecutivo($user->id_usuario, $filtros);

            // Capturar la hora exacta de exportación ANTES de generar el PDF
            $fechaExportacion = \Carbon\Carbon::now();
            $fechaFormateada = $fechaExportacion->format('d/m/Y');
            $horaFormateada = $fechaExportacion->format('H:i:s');
            
            // Construir estructura $pdfData
            $pdfData = [
                'titulo' => 'RESUMEN EJECUTIVO',
                'subtitulo' => 'Análisis Consolidado de Eventos',
                'ong' => $this->getOngData($user->id_usuario),
                'fecha_generacion' => $fechaFormateada,
                'hora_generacion' => $horaFormateada,
                'filtros_aplicados' => $filtros
            ];
            
            // Validar y convertir la URL del logo a ruta local si es necesario
            if (isset($pdfData['ong']['logo_url']) && $pdfData['ong']['logo_url']) {
                $logoUrl = $pdfData['ong']['logo_url'];
                // Si es una URL externa, intentar convertirla a base64 o usar ruta local
                if (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                    // Intentar obtener la ruta local si es una URL del mismo dominio
                    $baseUrl = request()->getSchemeAndHttpHost() ?? env('APP_URL', 'http://10.26.5.12:8000');
                    if (strpos($logoUrl, $baseUrl) === 0) {
                        // Es una URL del mismo dominio, convertir a ruta local
                        $path = str_replace($baseUrl . '/storage/', '', $logoUrl);
                        $localPath = storage_path('app/public/' . $path);
                        if (file_exists($localPath)) {
                            $pdfData['ong']['logo_url'] = $localPath;
                        } else {
                            // Si no existe, intentar con public_path
                            $publicPath = public_path('storage/' . $path);
                            if (file_exists($publicPath)) {
                                $pdfData['ong']['logo_url'] = $publicPath;
                            } else {
                                // Si no se encuentra, dejar null para evitar errores
                                $pdfData['ong']['logo_url'] = null;
                            }
                        }
                    } else {
                        // URL externa, dejar null para evitar problemas
                        $pdfData['ong']['logo_url'] = null;
                    }
                } else {
                    // Es una ruta relativa, convertir a absoluta
                    if (strpos($logoUrl, '/storage/') === 0) {
                        $path = str_replace('/storage/', '', $logoUrl);
                        $localPath = storage_path('app/public/' . $path);
                        if (file_exists($localPath)) {
                            $pdfData['ong']['logo_url'] = $localPath;
                        } else {
                            $publicPath = public_path('storage/' . $path);
                            if (file_exists($publicPath)) {
                                $pdfData['ong']['logo_url'] = $publicPath;
                            } else {
                                $pdfData['ong']['logo_url'] = null;
                            }
                        }
                    }
                }
            }
            
            $pdf = Pdf::loadView('ong.reportes.exports.resumen-ejecutivo-pdf', [
                'datos' => $datos,
                'pdfData' => $pdfData,
                'fechaExportacion' => $fechaExportacion,
                'fechaFormateada' => $fechaFormateada,
                'horaFormateada' => $horaFormateada
            ]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('isRemoteEnabled', false); // Deshabilitar URLs remotas para evitar errores
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('defaultFont', 'Helvetica');
            $pdf->setOption('isPhpEnabled', true);
            
            // Nombre del archivo con fecha y hora (sin caracteres especiales)
            $filename = 'reporte-resumen-ejecutivo-' . $fechaExportacion->format('Y-m-d_H-i-s') . '.pdf';
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            
            try {
                $pdfContent = $pdf->output();
                
                // Validar que el contenido del PDF no esté vacío
                if (empty($pdfContent) || strlen($pdfContent) < 100) {
                    throw new \Exception('El PDF generado está vacío o corrupto');
                }
                
                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Content-Length', strlen($pdfContent))
                    ->header('Cache-Control', 'no-cache, must-revalidate')
                    ->header('Pragma', 'no-cache');
            } catch (\Exception $pdfError) {
                Log::error('Error al generar contenido del PDF:', [
                    'error' => $pdfError->getMessage(),
                    'trace' => $pdfError->getTraceAsString()
                ]);
                throw $pdfError;
            }
        } catch (\Throwable $e) {
            Log::error('Error generando PDF de resumen ejecutivo:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar Reporte 1 en Excel
     * 
     * Nota: Usa autenticación con Sanctum desde la API
     */
    public function exportarResumenEjecutivoExcel(Request $request)
    {
        try {
            // Obtener usuario autenticado desde el token
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios tipo ONG pueden acceder a los reportes'
                ], 403);
            }

            $filtros = $this->validarFiltros($request);
            $filename = 'reporte-resumen-ejecutivo-' . date('Y-m-d') . '.xlsx';
            
            // Verificar que la clase de export existe
            if (!class_exists(MegaEventosResumenExport::class)) {
                throw new \Exception('La clase de exportación no existe');
            }
            
            $export = new MegaEventosResumenExport($user->id_usuario, $filtros);
            
            return Excel::download($export, $filename);
            
        } catch (\Throwable $e) {
            Log::error('Error generando Excel de resumen ejecutivo:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Si es una petición de API, devolver JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al generar el Excel: ' . $e->getMessage(),
                    'details' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }
            
            // Si es una petición web, devolver error HTML
            abort(500, 'Error al generar el Excel: ' . $e->getMessage());
        }
    }

    /**
     * Reporte 2: Análisis Temporal de Eventos
     * Gráfico de líneas de eventos creados por mes con comparativa año anterior
     */
    public function analisisTemporal(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'tendencias' => [],
            'meses' => [],
            'promedio_mensual' => 0,
            'total_actual' => 0,
            'total_anterior' => 0,
            'crecimiento_total' => 0
        ];

        return view('ong.reportes.analisis-temporal', compact('datos', 'filtros'));
    }

    /**
     * Reporte de Eventos Regulares
     * Muestra métricas detalladas de eventos regulares
     */
    public function eventosReport(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'total_eventos' => 0,
            'total_participantes' => 0,
            'eventos_activos' => 0,
            'promedio_participantes' => 0,
            'tasa_ocupacion_promedio' => 0,
            'distribucion_categoria' => [],
            'distribucion_estado' => [],
            'top_eventos' => [],
            'tendencias_mensuales' => [],
            'distribucion_geografica' => [],
            'total_patrocinios' => 0,
            'duracion_promedio' => 0,
            'distribucion_publico_privado' => [],
        ];

        return view('ong.reportes.eventos', compact('datos', 'filtros'));
    }

    /**
     * Reporte de Mega Eventos (ya existe pero mejorado)
     */
    public function megaEventosReport(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'total_mega_eventos' => 0,
            'total_participantes' => 0,
            'mega_eventos_activos' => 0,
            'promedio_participantes' => 0,
            'tasa_ocupacion_promedio' => 0,
            'distribucion_categoria' => [],
            'distribucion_estado' => [],
        ];

        return view('ong.reportes.mega-eventos', compact('datos', 'filtros'));
    }

    /**
     * Reporte Consolidado (Eventos + Mega Eventos)
     */
    public function consolidadoReport(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'total_participantes_general' => 0,
            'total_eventos_general' => 0,
            'comparativa' => [
                'eventos' => [
                    'total' => 0,
                    'participantes' => 0,
                    'activos' => 0,
                    'promedio_participantes' => 0,
                    'tasa_ocupacion' => 0,
                ],
                'mega_eventos' => [
                    'total' => 0,
                    'participantes' => 0,
                    'activos' => 0,
                    'promedio_participantes' => 0,
                    'tasa_ocupacion' => 0,
                ],
            ],
            'mejor_rendimiento' => 'empate',
            'distribucion_porcentual' => [
                'eventos' => 0,
                'mega_eventos' => 0,
            ],
            'metricas_eventos' => [],
            'metricas_mega_eventos' => [],
        ];

        return view('ong.reportes.consolidado', compact('datos', 'filtros'));
    }

    /**
     * Exportar Reporte 2 en PDF
     */
    public function exportarAnalisisTemporalPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getAnalisisTemporal($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.exports.analisis-temporal-pdf', compact('datos', 'filtros'));
        return $pdf->download('reporte-analisis-temporal-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar Reporte 2 en Excel
     */
    public function exportarAnalisisTemporalExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new AnalisisTemporalExport($user->id_usuario, $filtros),
            'reporte-analisis-temporal-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Exportar Reporte 2 en CSV
     */
    public function exportarAnalisisTemporalCSV(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getAnalisisTemporal($user->id_usuario, $filtros);

        $filename = 'reporte-analisis-temporal-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Mes', 'Año Actual', 'Año Anterior', 'Crecimiento %']);
            
            // Datos
            foreach ($datos['tendencias'] as $tendencia) {
                fputcsv($file, [
                    $tendencia['mes'],
                    $tendencia['cantidad_actual'],
                    $tendencia['cantidad_anterior'],
                    $tendencia['crecimiento_porcentual'] . '%'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reporte 3: Participación y Colaboración
     * Top empresas patrocinadoras, voluntarios más activos, eventos con más colaboradores
     */
    public function participacionColaboracion(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'top_empresas' => [],
            'top_voluntarios' => [],
            'eventos_colaboracion' => []
        ];

        return view('ong.reportes.participacion-colaboracion', compact('datos', 'filtros'));
    }

    /**
     * Exportar Reporte 3 en PDF
     */
    public function exportarParticipacionColaboracionPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getParticipacionColaboracion($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.exports.participacion-colaboracion-pdf', compact('datos', 'filtros'));
        return $pdf->download('reporte-participacion-colaboracion-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar Reporte 3 en Excel
     */
    public function exportarParticipacionColaboracionExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new ParticipacionColaboracionExport($user->id_usuario, $filtros),
            'reporte-participacion-colaboracion-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Reporte 4: Análisis Geográfico
     * Mapa de calor o tabla de ciudades con más eventos, distribución por departamentos
     */
    public function analisisGeografico(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'ciudades' => [],
            'departamentos' => [],
            'total_eventos' => 0
        ];

        return view('ong.reportes.analisis-geografico', compact('datos', 'filtros'));
    }

    /**
     * Exportar Reporte 4 en PDF
     */
    public function exportarAnalisisGeograficoPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getAnalisisGeografico($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.exports.analisis-geografico-pdf', compact('datos', 'filtros'));
        return $pdf->download('reporte-analisis-geografico-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar Reporte 4 en Excel
     */
    public function exportarAnalisisGeograficoExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new AnalisisGeograficoExport($user->id_usuario, $filtros),
            'reporte-analisis-geografico-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Reporte 5: Rendimiento por ONG
     * Ranking de ONGs por eventos creados, tasas de finalización, promedio de asistentes
     */
    public function rendimientoOng(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        
        // Datos iniciales vacíos, el JavaScript los cargará desde la API
        $datos = [
            'ong_actual' => [
                'ong_id' => 0,
                'nombre' => '',
                'total_eventos' => 0,
                'eventos_finalizados' => 0,
                'tasa_finalizacion' => 0,
                'promedio_asistentes' => 0,
                'posicion_ranking' => null
            ],
            'ranking_ongs' => []
        ];

        return view('ong.reportes.rendimiento-ong', compact('datos', 'filtros'));
    }

    /**
     * Exportar Reporte 5 en PDF
     */
    public function exportarRendimientoOngPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getRendimientoOng($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.exports.rendimiento-ong-pdf', compact('datos', 'filtros'));
        return $pdf->download('reporte-rendimiento-ong-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar Reporte 5 en Excel
     */
    public function exportarRendimientoOngExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new RendimientoOngExport($user->id_usuario, $filtros),
            'reporte-rendimiento-ong-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Exportar Reporte 5 en JSON
     */
    public function exportarRendimientoOngJSON(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getRendimientoOng($user->id_usuario, $filtros);

        return response()->json([
            'success' => true,
            'data' => $datos,
            'filtros' => $filtros,
            'generado_en' => now()->toIso8601String()
        ])->header('Content-Disposition', 'attachment; filename="reporte-rendimiento-ong-' . date('Y-m-d') . '.json"');
    }

    /**
     * Validar y sanitizar filtros del request
     * Previene SQL injection y valida rangos lógicos
     */
    private function validarFiltros(Request $request): array
    {
        $filtros = [];

        // Rango de fechas
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            
            // Validar formato de fecha
            if (strtotime($fechaInicio) && strtotime($fechaFin)) {
                // Validar que fecha_inicio < fecha_fin
                if (strtotime($fechaInicio) > strtotime($fechaFin)) {
                    abort(400, 'La fecha de inicio debe ser anterior a la fecha de fin');
                }
                
                $filtros['fecha_inicio'] = date('Y-m-d', strtotime($fechaInicio));
                $filtros['fecha_fin'] = date('Y-m-d', strtotime($fechaFin));
            }
        }

        // Categoría (sanitizar)
        if ($request->has('categoria')) {
            $categoriasValidas = ['social', 'educativo', 'ambiental', 'salud', 'cultural', 'deportivo', 'benefico', 'otro'];
            $categoria = $request->input('categoria');
            if (in_array($categoria, $categoriasValidas)) {
                $filtros['categoria'] = $categoria;
            }
        }

        // Estado (sanitizar)
        if ($request->has('estado')) {
            $estadosValidos = ['planificacion', 'activo', 'en_curso', 'finalizado', 'cancelado'];
            $estado = $request->input('estado');
            if (in_array($estado, $estadosValidos)) {
                $filtros['estado'] = $estado;
            }
        }

        // Ubicación (sanitizar string)
        if ($request->has('ubicacion')) {
            $filtros['ubicacion'] = filter_var($request->input('ubicacion'), FILTER_SANITIZE_STRING);
        }

        // ONG organizadora (validar que sea numérico)
        if ($request->has('ong_id')) {
            $ongId = filter_var($request->input('ong_id'), FILTER_VALIDATE_INT);
            if ($ongId !== false && $ongId > 0) {
                $filtros['ong_id'] = $ongId;
            }
        }

        // Rango de capacidad (validar números)
        if ($request->has('capacidad_min')) {
            $capacidadMin = filter_var($request->input('capacidad_min'), FILTER_VALIDATE_INT);
            if ($capacidadMin !== false && $capacidadMin >= 0) {
                $filtros['capacidad_min'] = $capacidadMin;
            }
        }

        if ($request->has('capacidad_max')) {
            $capacidadMax = filter_var($request->input('capacidad_max'), FILTER_VALIDATE_INT);
            if ($capacidadMax !== false && $capacidadMax > 0) {
                $filtros['capacidad_max'] = $capacidadMax;
            }
        }

        // Validar que capacidad_min < capacidad_max si ambos existen
        if (isset($filtros['capacidad_min']) && isset($filtros['capacidad_max'])) {
            if ($filtros['capacidad_min'] > $filtros['capacidad_max']) {
                abort(400, 'La capacidad mínima debe ser menor que la capacidad máxima');
            }
        }

        return $filtros;
    }

    /**
     * API: Obtener KPIs destacados para el dashboard
     * Endpoint para que el frontend cargue los KPIs desde la API
     */
    public function apiKPIsDestacados(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios tipo ONG pueden acceder a los reportes'
                ], 403);
            }

            $kpis = Cache::remember("reportes_kpis_ong_{$user->id_usuario}", 300, function () use ($user) {
                return $this->reportService->getKPIsDestacados($user->id_usuario);
            });

            return response()->json([
                'success' => true,
                'kpis' => $kpis
            ]);
        } catch (\Throwable $e) {
            Log::error('Error obteniendo KPIs destacados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener KPIs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos del Resumen Ejecutivo
     */
    public function apiResumenEjecutivo(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_resumen_ejecutivo_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 300, function () use ($user, $filtros) {
                return $this->reportService->getResumenEjecutivo($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos del Análisis Temporal
     */
    public function apiAnalisisTemporal(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_analisis_temporal_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 300, function () use ($user, $filtros) {
                return $this->reportService->getAnalisisTemporal($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos de Participación y Colaboración
     */
    public function apiParticipacionColaboracion(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_participacion_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 300, function () use ($user, $filtros) {
                return $this->reportService->getParticipacionColaboracion($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos del Análisis Geográfico
     */
    public function apiAnalisisGeografico(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_geografico_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 300, function () use ($user, $filtros) {
                return $this->reportService->getAnalisisGeografico($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos de Rendimiento por ONG
     */
    public function apiRendimientoOng(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_rendimiento_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 300, function () use ($user, $filtros) {
                return $this->reportService->getRendimientoOng($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener métricas de eventos regulares
     */
    public function apiEventosMetrics(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_eventos_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 3600, function () use ($user, $filtros) {
                return $this->reportService->getEventosMetrics($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener métricas de mega eventos
     */
    public function apiMegaEventosMetrics(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_mega_eventos_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 3600, function () use ($user, $filtros) {
                return $this->reportService->getMegaEventosMetrics($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener métricas consolidadas
     */
    public function apiConsolidadoMetrics(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json(['success' => false, 'error' => 'Acceso denegado'], 403);
            }

            $filtros = $this->validarFiltros($request);
            $cacheKey = "reporte_consolidado_{$user->id_usuario}_" . md5(json_encode($filtros));
            $datos = Cache::remember($cacheKey, 3600, function () use ($user, $filtros) {
                return $this->reportService->getConsolidadoMetrics($user->id_usuario, $filtros);
            });

            return response()->json([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar reporte de eventos en PDF
     */
    public function exportEventosPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getEventosMetrics($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.pdf.eventos-pdf', compact('datos', 'filtros'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('reporte-eventos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar reporte de eventos en Excel
     */
    public function exportEventosExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new \App\Exports\EventosExport($user->id_usuario, $filtros),
            'reporte-eventos-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Exportar reporte de mega eventos en PDF
     */
    public function exportMegaEventosPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getMegaEventosMetrics($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.pdf.mega-eventos-pdf', compact('datos', 'filtros'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('reporte-mega-eventos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar reporte de mega eventos en Excel
     */
    public function exportMegaEventosExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new \App\Exports\MegaEventosExport($user->id_usuario, $filtros),
            'reporte-mega-eventos-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Exportar reporte consolidado en PDF
     */
    public function exportConsolidadoPDF(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        $datos = $this->reportService->getConsolidadoMetrics($user->id_usuario, $filtros);

        $pdf = Pdf::loadView('ong.reportes.pdf.consolidado-pdf', compact('datos', 'filtros'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('reporte-consolidado-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar reporte consolidado en Excel
     */
    public function exportConsolidadoExcel(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->tipo_usuario !== 'ONG') abort(403);

        $filtros = $this->validarFiltros($request);
        
        return Excel::download(
            new \App\Exports\ConsolidadoExport($user->id_usuario, $filtros),
            'reporte-consolidado-' . date('Y-m-d') . '.xlsx'
        );
    }
}

