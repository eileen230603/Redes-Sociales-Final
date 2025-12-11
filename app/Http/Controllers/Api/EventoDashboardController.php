<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\EventoParticipacion;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\Ong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class EventoDashboardController extends Controller
{
    /**
     * Obtener datos completos del dashboard del evento
     */
    public function dashboard(Request $request, $id)
    {
        try {
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden acceder al dashboard'
                ], 403);
            }

            $evento = Evento::with('ong')->find($id);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos
            if ($evento->ong_id != $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este dashboard'
                ], 403);
            }

            // Obtener filtros de fecha
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::parse($evento->fecha_inicio ?? $evento->created_at)->subDays(30);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            // Métricas principales
            $metricas = $this->obtenerMetricasPrincipales($id, $fechaInicio, $fechaFin);
            
            // Gráficos de tendencias temporales
            $tendencias = $this->obtenerTendenciasTemporales($id, $fechaInicio, $fechaFin);
            
            // Distribución por estados
            $distribucionEstados = $this->obtenerDistribucionEstados($id);
            
            // Actividad semanal
            $actividadSemanal = $this->obtenerActividadSemanal($id, $fechaInicio, $fechaFin);
            
            // Top participantes
            $topParticipantes = $this->obtenerTopParticipantes($id, 10);
            
            // Últimos 10 días de actividad
            $actividadReciente = $this->obtenerActividadReciente($id, 10);
            
            // Comparativas con período anterior
            $comparativas = $this->obtenerComparativas($id, $fechaInicio, $fechaFin);
            
            // Gráfico radar (métricas generales)
            $metricasRadar = $this->obtenerMetricasRadar($id, $fechaInicio, $fechaFin);

            // Preparar datos para JSON
            $datos = [
                'success' => true,
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'fecha_inicio' => $evento->fecha_inicio,
                    'fecha_fin' => $evento->fecha_fin,
                    'ubicacion' => $evento->ubicacion,
                    'categoria' => $evento->categoria,
                    'estado' => $evento->estado,
                ],
                'ong' => [
                    'nombre' => $evento->ong->nombre_ong ?? 'ONG',
                    'logo_url' => $evento->ong->logo_url ?? $this->obtenerLogoOng($evento->ong_id)
                ],
                'filtros' => [
                    'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                    'fecha_fin' => $fechaFin->format('Y-m-d')
                ],
                'metricas' => $metricas,
                'tendencias' => $tendencias,
                'distribucion_estados' => $distribucionEstados,
                'actividad_semanal' => $actividadSemanal,
                'top_participantes' => $topParticipantes,
                'actividad_reciente' => $actividadReciente,
                'comparativas' => $comparativas,
                'metricas_radar' => $metricasRadar
            ];
            
            // Limpiar output buffer antes de enviar respuesta
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json($datos, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            Log::error('Error en dashboard del evento:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Limpiar output buffer antes de enviar respuesta de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener dashboard: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Exportar dashboard en PDF
     */
    /**
     * Generar PDF profesional del dashboard del evento
     */
    public function exportarPdf(Request $request, $id)
    {
        // Optimización: límites mínimos necesarios
        ini_set('memory_limit', '128M');
        set_time_limit(30);
        
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden exportar reportes'
                ], 403);
            }

            $evento = Evento::with('ong')->find($id);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            if ($evento->ong_id != $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para exportar este reporte'
                ], 403);
            }

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::parse($evento->fecha_inicio ?? $evento->created_at)->subDays(30);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            // Cache key para los datos (30 minutos para PDF)
            $cacheKey = "pdf_dashboard_evento_{$id}_" . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            
            // Obtener datos completos con cache extendido
            $datos = Cache::remember($cacheKey, 1800, function() use ($id, $fechaInicio, $fechaFin, $evento) {
                return $this->obtenerDatosCompletosParaPdf($id, $fechaInicio, $fechaFin, $evento);
            });
            
            // Cache para URLs de gráficos (30 minutos)
            $graficosCacheKey = "pdf_graficos_evento_{$id}_" . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            $graficosUrls = Cache::remember($graficosCacheKey, 1800, function() use ($datos) {
                return $this->generarUrlsGraficosProfesionales($datos);
            });
            
            // Obtener logo de ONG (cache extendido)
            $logoOng = Cache::remember("logo_ong_{$evento->ong_id}", 7200, function() use ($evento) {
                return $this->obtenerLogoOng($evento->ong_id);
            });
            
            // Ruta del logo UNI2 (cachear existencia)
            $logoUni2Key = "logo_uni2_exists";
            $logoUni2 = Cache::remember($logoUni2Key, 3600, function() {
                $path = public_path('assets/img/UNI2 - copia.png');
                return file_exists($path) ? $path : null;
            });

            // Cache para métricas adicionales e insights (30 minutos)
            $metricasCacheKey = "pdf_metricas_evento_{$id}_" . md5($fechaInicio->format('Y-m-d') . $fechaFin->format('Y-m-d'));
            $metricasEInsights = Cache::remember($metricasCacheKey, 1800, function() use ($datos) {
                $metricasAdicionales = $this->calcularMetricasAdicionales($datos);
                $insights = $this->generarInsights($datos, $metricasAdicionales);
                return [
                    'metricas_adicionales' => $metricasAdicionales,
                    'insights' => $insights
                ];
            });
            
            $metricasAdicionales = $metricasEInsights['metricas_adicionales'];
            $insights = $metricasEInsights['insights'];
            
            // Preparar datos para la vista
            $datosVista = [
                'evento' => $evento,
                'datos' => $datos,
                'metricas_adicionales' => $metricasAdicionales,
                'insights' => $insights,
                'graficos_urls' => $graficosUrls,
                'logo_ong' => $logoOng,
                'logo_uni2' => $logoUni2,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'fecha_generacion' => now(),
                'ong' => $evento->ong
            ];

            // Generar PDF con configuración optimizada para velocidad
            $pdf = Pdf::loadView('ong.eventos.dashboard-pdf', $datosVista)
                ->setPaper('a4', 'portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('isRemoteEnabled', true) // Necesario para QuickChart
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'Arial')
                ->setOption('dpi', 96) // Reducir DPI para velocidad (96 es suficiente para PDF)
                ->setOption('fontDir', storage_path('fonts/'))
                ->setOption('fontCache', storage_path('fonts/'))
                ->setOption('enable-font-subsetting', false)
                ->setOption('enable-javascript', false) // Deshabilitar JS para velocidad
                ->setOption('enable-php', false); // Deshabilitar PHP embebido para velocidad

            // Generar nombre de archivo descriptivo
            $tituloSlug = \Str::slug($evento->titulo, '-');
            $fechaArchivo = now()->format('Ymd');
            $filename = "dashboard-evento-{$evento->id}-{$tituloSlug}-{$fechaArchivo}.pdf";
            
            // Log de auditoría
            Log::info('PDF generado exitosamente', [
                'evento_id' => $id,
                'usuario_id' => $user->id_usuario,
                'fecha_generacion' => now()->toDateTimeString(),
                'archivo' => $filename
            ]);
            
            return $pdf->download($filename);

        } catch (\Throwable $e) {
            Log::error('Error generando PDF del dashboard:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
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
    public function exportarExcel(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->tipo_usuario !== 'ONG') {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo usuarios ONG pueden exportar reportes'
                ], 403);
            }

            $evento = Evento::find($id);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            if ($evento->ong_id != $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para exportar este reporte'
                ], 403);
            }

            // Obtener filtros
            $fechaInicio = $request->input('fecha_inicio') 
                ? Carbon::parse($request->input('fecha_inicio')) 
                : Carbon::parse($evento->fecha_inicio ?? $evento->created_at)->subDays(30);
            
            $fechaFin = $request->input('fecha_fin') 
                ? Carbon::parse($request->input('fecha_fin')) 
                : Carbon::now();

            // Obtener datos
            $datos = $this->obtenerDatosCompletos($id, $fechaInicio, $fechaFin, $evento);

            $export = new \App\Exports\EventoDashboardExport($evento, $datos, $fechaInicio, $fechaFin);
            
            $filename = 'dashboard-evento-' . $evento->id . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download($export, $filename);

        } catch (\Throwable $e) {
            Log::error('Error generando Excel del dashboard:', [
                'evento_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métricas principales
     */
    private function obtenerMetricasPrincipales($eventoId, $fechaInicio, $fechaFin)
    {
        $baseQuery = function($table) use ($eventoId, $fechaInicio, $fechaFin) {
            return DB::table($table)
                ->where('evento_id', $eventoId)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        };

        // Reacciones
        $totalReacciones = $baseQuery('evento_reacciones')->count();
        
        // Compartidos
        $totalCompartidos = $baseQuery('evento_compartidos')->count();
        
        // Voluntarios únicos
        $totalVoluntarios = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereNotNull('externo_id')
            ->distinct('externo_id')
            ->count('externo_id');
        
        // Participantes totales
        $participantesRegistrados = $baseQuery('evento_participaciones')->count();
        $participantesNoRegistrados = $baseQuery('evento_participantes_no_registrados')->count();
        $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;
        
        // Participantes por estado
        $participantesPorEstado = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
        
        $participantesNoRegPorEstado = DB::table('evento_participantes_no_registrados')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
        
        foreach ($participantesNoRegPorEstado as $estado => $total) {
            $participantesPorEstado[$estado] = ($participantesPorEstado[$estado] ?? 0) + $total;
        }

        return [
            'reacciones' => $totalReacciones,
            'compartidos' => $totalCompartidos,
            'voluntarios' => $totalVoluntarios,
            'participantes_total' => $totalParticipantes,
            'participantes_por_estado' => $participantesPorEstado
        ];
    }

    /**
     * Obtener tendencias temporales
     */
    private function obtenerTendenciasTemporales($eventoId, $fechaInicio, $fechaFin)
    {
        // Reacciones por día
        $reaccionesPorDia = DB::table('evento_reacciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        // Compartidos por día
        $compartidosPorDia = DB::table('evento_compartidos')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        // Inscripciones por día
        $inscripcionesRegistrados = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        $inscripcionesNoRegistrados = DB::table('evento_participantes_no_registrados')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        $inscripcionesPorDia = [];
        foreach (array_merge(array_keys($inscripcionesRegistrados), array_keys($inscripcionesNoRegistrados)) as $fecha) {
            $inscripcionesPorDia[$fecha] = ($inscripcionesRegistrados[$fecha] ?? 0) + ($inscripcionesNoRegistrados[$fecha] ?? 0);
        }
        ksort($inscripcionesPorDia);

        return [
            'reacciones_por_dia' => $reaccionesPorDia,
            'compartidos_por_dia' => $compartidosPorDia,
            'inscripciones_por_dia' => $inscripcionesPorDia
        ];
    }

    /**
     * Obtener distribución por estados
     */
    private function obtenerDistribucionEstados($eventoId)
    {
        $registrados = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $noRegistrados = DB::table('evento_participantes_no_registrados')
            ->where('evento_id', $eventoId)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $distribucion = [];
        foreach (array_merge(array_keys($registrados), array_keys($noRegistrados)) as $estado) {
            $distribucion[$estado] = ($registrados[$estado] ?? 0) + ($noRegistrados[$estado] ?? 0);
        }

        return $distribucion;
    }

    /**
     * Obtener actividad semanal
     */
    private function obtenerActividadSemanal($eventoId, $fechaInicio, $fechaFin)
    {
        $actividad = [];
        
        // Combinar todas las actividades
        $reacciones = DB::table('evento_reacciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha')
            ->get()
            ->pluck('fecha')
            ->toArray();

        $compartidos = DB::table('evento_compartidos')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha')
            ->get()
            ->pluck('fecha')
            ->toArray();

        $participaciones = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
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
     * Obtener top participantes
     */
    private function obtenerTopParticipantes($eventoId, $limite = 10)
    {
        return DB::table('evento_participaciones as ep')
            ->join('integrantes_externos as ie', 'ep.externo_id', '=', 'ie.user_id')
            ->where('ep.evento_id', $eventoId)
            ->selectRaw('ie.nombres, ie.apellidos, COUNT(*) as total_actividades')
            ->groupBy('ie.user_id', 'ie.nombres', 'ie.apellidos')
            ->orderBy('total_actividades', 'desc')
            ->limit($limite)
            ->get()
            ->map(function($item) {
                return [
                    'nombre' => trim(($item->nombres ?? '') . ' ' . ($item->apellidos ?? '')),
                    'total_actividades' => $item->total_actividades
                ];
            })
            ->toArray();
    }

    /**
     * Obtener actividad reciente (últimos N días)
     * Optimizado: una sola consulta agrupada en lugar de múltiples consultas en loop
     */
    private function obtenerActividadReciente($eventoId, $dias = 10)
    {
        $fechaInicio = Carbon::now()->subDays($dias);
        $fechaFin = Carbon::now();
        
        // Inicializar todas las fechas con ceros
        $actividad = [];
        for ($i = 0; $i < $dias; $i++) {
            $fecha = $fechaInicio->copy()->addDays($i)->format('Y-m-d');
            $actividad[$fecha] = [
                'reacciones' => 0,
                'compartidos' => 0,
                'inscripciones' => 0,
                'total' => 0
            ];
        }

        // Una sola consulta para reacciones agrupadas por fecha
        $reacciones = DB::table('evento_reacciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        
        foreach ($reacciones as $r) {
            $fecha = Carbon::parse($r->fecha)->format('Y-m-d');
            if (isset($actividad[$fecha])) {
                $actividad[$fecha]['reacciones'] = $r->total;
                $actividad[$fecha]['total'] += $r->total;
            }
        }

        // Una sola consulta para compartidos agrupados por fecha
        $compartidos = DB::table('evento_compartidos')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        
        foreach ($compartidos as $c) {
            $fecha = Carbon::parse($c->fecha)->format('Y-m-d');
            if (isset($actividad[$fecha])) {
                $actividad[$fecha]['compartidos'] = $c->total;
                $actividad[$fecha]['total'] += $c->total;
            }
        }

        // Una sola consulta para inscripciones agrupadas por fecha
        $inscripciones = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        
        foreach ($inscripciones as $i) {
            $fecha = Carbon::parse($i->fecha)->format('Y-m-d');
            if (isset($actividad[$fecha])) {
                $actividad[$fecha]['inscripciones'] = $i->total;
                $actividad[$fecha]['total'] += $i->total;
            }
        }

        return $actividad;
    }

    /**
     * Obtener comparativas con período anterior
     */
    private function obtenerComparativas($eventoId, $fechaInicio, $fechaFin)
    {
        $duracion = $fechaInicio->diffInDays($fechaFin);
        $periodoAnteriorInicio = $fechaInicio->copy()->subDays($duracion);
        $periodoAnteriorFin = $fechaInicio->copy()->subDay();

        $actual = $this->obtenerMetricasPrincipales($eventoId, $fechaInicio, $fechaFin);
        $anterior = $this->obtenerMetricasPrincipales($eventoId, $periodoAnteriorInicio, $periodoAnteriorFin);

        $comparativas = [];
        foreach (['reacciones', 'compartidos', 'voluntarios', 'participantes_total'] as $metrica) {
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
    private function obtenerMetricasRadar($eventoId, $fechaInicio, $fechaFin)
    {
        $metricas = $this->obtenerMetricasPrincipales($eventoId, $fechaInicio, $fechaFin);
        
        // Normalizar valores (0-100)
        $maxValores = [
            'reacciones' => max($metricas['reacciones'], 100),
            'compartidos' => max($metricas['compartidos'], 100),
            'voluntarios' => max($metricas['voluntarios'], 100),
            'participantes_total' => max($metricas['participantes_total'], 100)
        ];

        return [
            'reacciones' => ($metricas['reacciones'] / $maxValores['reacciones']) * 100,
            'compartidos' => ($metricas['compartidos'] / $maxValores['compartidos']) * 100,
            'voluntarios' => ($metricas['voluntarios'] / $maxValores['voluntarios']) * 100,
            'participantes' => ($metricas['participantes_total'] / $maxValores['participantes_total']) * 100
        ];
    }

    /**
     * Obtener datos completos para exportación
     */
    private function obtenerDatosCompletos($eventoId, $fechaInicio, $fechaFin, $evento)
    {
        // Optimizado: no incluir datos detallados que no se usan en PDF
        return [
            'metricas' => $this->obtenerMetricasPrincipales($eventoId, $fechaInicio, $fechaFin),
            'tendencias' => $this->obtenerTendenciasTemporales($eventoId, $fechaInicio, $fechaFin),
            'distribucion_estados' => $this->obtenerDistribucionEstados($eventoId),
            'actividad_semanal' => $this->obtenerActividadSemanal($eventoId, $fechaInicio, $fechaFin),
            'top_participantes' => $this->obtenerTopParticipantes($eventoId, 10),
            'actividad_reciente' => $this->obtenerActividadReciente($eventoId, 10),
            'comparativas' => $this->obtenerComparativas($eventoId, $fechaInicio, $fechaFin),
            'metricas_radar' => $this->obtenerMetricasRadar($eventoId, $fechaInicio, $fechaFin),
        ];
    }

    /**
     * Obtener datos completos optimizados para PDF profesional
     * Optimizado para velocidad: consultas simplificadas y datos mínimos necesarios
     */
    private function obtenerDatosCompletosParaPdf($eventoId, $fechaInicio, $fechaFin, $evento)
    {
        // Ejecutar consultas en paralelo usando DB::select para optimizar
        // Usar solo los datos esenciales para el PDF
        
        return [
            'metricas' => $this->obtenerMetricasPrincipales($eventoId, $fechaInicio, $fechaFin),
            'tendencias' => $this->obtenerTendenciasTemporales($eventoId, $fechaInicio, $fechaFin),
            'distribucion_estados' => $this->obtenerDistribucionEstados($eventoId),
            'actividad_semanal' => $this->obtenerActividadSemanal($eventoId, $fechaInicio, $fechaFin),
            'actividad_por_dia_semana' => $this->obtenerActividadPorDiaSemana($eventoId, $fechaInicio, $fechaFin),
            'top_participantes' => $this->obtenerTopParticipantesCompleto($eventoId, 10),
            'actividad_reciente' => $this->obtenerActividadReciente($eventoId, 14),
            'comparativas' => $this->obtenerComparativas($eventoId, $fechaInicio, $fechaFin),
            'metricas_radar' => $this->obtenerMetricasRadar($eventoId, $fechaInicio, $fechaFin),
            'reacciones_por_dia' => $this->obtenerReaccionesPorDia($eventoId, $fechaInicio, $fechaFin, 14),
            'compartidos_por_dia' => $this->obtenerCompartidosPorDia($eventoId, $fechaInicio, $fechaFin, 14),
            'inscripciones_por_dia' => $this->obtenerInscripcionesPorDia($eventoId, $fechaInicio, $fechaFin, 14),
        ];
    }

    /**
     * Obtener reacciones detalladas
     */
    private function obtenerReaccionesDetalladas($eventoId, $fechaInicio, $fechaFin)
    {
        // Limitar a 100 registros para optimizar
        return DB::table('evento_reacciones as er')
            ->leftJoin('integrantes_externos as ie', 'er.externo_id', '=', 'ie.user_id')
            ->where('er.evento_id', $eventoId)
            ->whereBetween('er.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'er.id',
                'er.created_at',
                DB::raw("COALESCE(
                    CONCAT(ie.nombres, ' ', ie.apellidos),
                    CONCAT(er.nombres, ' ', er.apellidos),
                    'Usuario'
                ) as nombre")
            )
            ->orderBy('er.created_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
    }

    /**
     * Obtener compartidos detallados
     */
    private function obtenerCompartidosDetallados($eventoId, $fechaInicio, $fechaFin)
    {
        // Limitar a 100 registros para optimizar
        return DB::table('evento_compartidos as ec')
            ->leftJoin('integrantes_externos as ie', 'ec.externo_id', '=', 'ie.user_id')
            ->where('ec.evento_id', $eventoId)
            ->whereBetween('ec.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'ec.id',
                'ec.metodo as plataforma',
                'ec.created_at',
                DB::raw("COALESCE(
                    CONCAT(ie.nombres, ' ', ie.apellidos),
                    CONCAT(ec.nombres, ' ', ec.apellidos),
                    'Usuario'
                ) as nombre")
            )
            ->orderBy('ec.created_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
    }

    /**
     * Obtener inscripciones detalladas
     */
    private function obtenerInscripcionesDetalladas($eventoId, $fechaInicio, $fechaFin)
    {
        // Optimizado: limitar registros para PDF
        $registrados = DB::table('evento_participaciones as ep')
            ->leftJoin('integrantes_externos as ie', 'ep.externo_id', '=', 'ie.user_id')
            ->where('ep.evento_id', $eventoId)
            ->whereBetween('ep.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'ep.id',
                'ep.estado',
                'ep.created_at',
                DB::raw("COALESCE(CONCAT(ie.nombres, ' ', ie.apellidos), 'Participante') as nombre"),
                DB::raw("'registrado' as tipo")
            )
            ->limit(100)
            ->get()
            ->toArray();

        $noRegistrados = DB::table('evento_participantes_no_registrados as epr')
            ->where('epr.evento_id', $eventoId)
            ->whereBetween('epr.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'epr.id',
                'epr.estado',
                'epr.created_at',
                DB::raw("COALESCE(CONCAT(epr.nombres, ' ', epr.apellidos), 'Participante') as nombre"),
                DB::raw("'no_registrado' as tipo")
            )
            ->limit(100)
            ->get()
            ->toArray();

        return array_merge($registrados, $noRegistrados);
    }

    /**
     * Generar URLs de gráficos optimizado (sin esperar respuesta HTTP)
     */
    private function generarUrlsGraficosOptimizado($datos)
    {
        return $this->generarUrlsGraficos($datos);
    }

    /**
     * Generar URLs de gráficos usando QuickChart
     */
    private function generarUrlsGraficos($datos)
    {
        $baseUrl = 'https://quickchart.io/chart?c=';
        
        // Gráfico de líneas - Tendencias temporales
        $tendenciasData = $datos['tendencias'];
        $labels = array_keys($tendenciasData['reacciones_por_dia'] ?? []);
        $reaccionesData = array_values($tendenciasData['reacciones_por_dia'] ?? []);
        $compartidosData = array_values($tendenciasData['compartidos_por_dia'] ?? []);
        
        $chartLine = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Reacciones',
                        'data' => $reaccionesData,
                        'borderColor' => '#dc3545',
                        'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                        'fill' => true
                    ],
                    [
                        'label' => 'Compartidos',
                        'data' => $compartidosData,
                        'borderColor' => '#00A36C',
                        'backgroundColor' => 'rgba(0, 163, 108, 0.1)',
                        'fill' => true
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Tendencias Temporales']
                ]
            ]
        ];
        
        // Gráfico de dona - Distribución por estados
        $estadosData = $datos['distribucion_estados'];
        $chartDona = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($estadosData),
                'datasets' => [[
                    'data' => array_values($estadosData),
                    'backgroundColor' => ['#0C2B44', '#00A36C', '#dc3545', '#17a2b8', '#ffc107']
                ]]
            ]
        ];
        
        // Gráfico de barras - Comparativas
        $comparativas = $datos['comparativas'];
        $chartBarras = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Reacciones', 'Compartidos', 'Voluntarios', 'Participantes'],
                'datasets' => [
                    [
                        'label' => 'Período Actual',
                        'data' => [
                            $comparativas['reacciones']['actual'] ?? 0,
                            $comparativas['compartidos']['actual'] ?? 0,
                            $comparativas['voluntarios']['actual'] ?? 0,
                            $comparativas['participantes_total']['actual'] ?? 0
                        ],
                        'backgroundColor' => '#00A36C'
                    ],
                    [
                        'label' => 'Período Anterior',
                        'data' => [
                            $comparativas['reacciones']['anterior'] ?? 0,
                            $comparativas['compartidos']['anterior'] ?? 0,
                            $comparativas['voluntarios']['anterior'] ?? 0,
                            $comparativas['participantes_total']['anterior'] ?? 0
                        ],
                        'backgroundColor' => '#0C2B44'
                    ]
                ]
            ]
        ];
        
        // Gráfico de área - Actividad semanal
        $actividadSemanal = $datos['actividad_semanal'];
        $chartArea = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($actividadSemanal),
                'datasets' => [[
                    'label' => 'Actividad Semanal',
                    'data' => array_values($actividadSemanal),
                    'borderColor' => '#17a2b8',
                    'backgroundColor' => 'rgba(23, 162, 184, 0.3)',
                    'fill' => true
                ]]
            ]
        ];
        
        // Gráfico radar
        $metricasRadar = $datos['metricas_radar'];
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
                    'pointBackgroundColor' => '#00A36C'
                ]]
            ]
        ];

        return [
            'tendencias' => $baseUrl . urlencode(json_encode($chartLine)),
            'distribucion_estados' => $baseUrl . urlencode(json_encode($chartDona)),
            'comparativas' => $baseUrl . urlencode(json_encode($chartBarras)),
            'actividad_semanal' => $baseUrl . urlencode(json_encode($chartArea)),
            'radar' => $baseUrl . urlencode(json_encode($chartRadar))
        ];
    }

    /**
     * Obtener logo de ONG
     */
    private function obtenerLogoOng($ongId)
    {
        $ong = Ong::find($ongId);
        if (!$ong || !$ong->foto_perfil) {
            return null;
        }

        // Usar el accessor logo_url si está disponible
        if (method_exists($ong, 'getLogoUrlAttribute')) {
            return $ong->logo_url;
        }

        $baseUrl = request()->getSchemeAndHttpHost() ?? env('APP_URL', 'http://10.26.5.12:8000');
        
        if (filter_var($ong->foto_perfil, FILTER_VALIDATE_URL)) {
            return $ong->foto_perfil;
        }
        
        return rtrim($baseUrl, '/') . '/storage/' . ltrim($ong->foto_perfil, '/');
    }

    /**
     * Obtener actividad por día de la semana
     */
    private function obtenerActividadPorDiaSemana($eventoId, $fechaInicio, $fechaFin)
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $actividad = array_fill_keys($diasSemana, 0);

        // Reacciones por día de semana
        $reacciones = DB::table('evento_reacciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('EXTRACT(DOW FROM created_at) as dia_semana, COUNT(*) as total')
            ->groupBy('dia_semana')
            ->get();

        foreach ($reacciones as $r) {
            $dia = $diasSemana[$r->dia_semana == 0 ? 6 : $r->dia_semana - 1];
            $actividad[$dia] += $r->total;
        }

        // Compartidos por día de semana
        $compartidos = DB::table('evento_compartidos')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('EXTRACT(DOW FROM created_at) as dia_semana, COUNT(*) as total')
            ->groupBy('dia_semana')
            ->get();

        foreach ($compartidos as $c) {
            $dia = $diasSemana[$c->dia_semana == 0 ? 6 : $c->dia_semana - 1];
            $actividad[$dia] += $c->total;
        }

        return $actividad;
    }

    /**
     * Obtener top participantes completo con email
     */
    private function obtenerTopParticipantesCompleto($eventoId, $limite = 10)
    {
        return DB::table('evento_participaciones as ep')
            ->join('integrantes_externos as ie', 'ep.externo_id', '=', 'ie.user_id')
            ->where('ep.evento_id', $eventoId)
            ->selectRaw('ie.nombres, ie.apellidos, ie.email, COUNT(DISTINCT ep.id) as total_actividades, COUNT(DISTINCT ep.evento_id) as eventos_participados')
            ->groupBy('ie.user_id', 'ie.nombres', 'ie.apellidos', 'ie.email')
            ->orderBy('total_actividades', 'desc')
            ->limit($limite)
            ->get()
            ->map(function($item) {
                return [
                    'nombre' => trim(($item->nombres ?? '') . ' ' . ($item->apellidos ?? '')),
                    'email' => $item->email ?? 'N/A',
                    'total_actividades' => $item->total_actividades,
                    'eventos_participados' => $item->eventos_participados
                ];
            })
            ->toArray();
    }

    /**
     * Obtener reacciones por día (últimos N días)
     */
    private function obtenerReaccionesPorDia($eventoId, $fechaInicio, $fechaFin, $dias = 14)
    {
        $fechaInicioReciente = Carbon::now()->subDays($dias);
        $resultado = [];

        $reacciones = DB::table('evento_reacciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicioReciente, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get();

        $acumulado = 0;
        foreach ($reacciones as $r) {
            $acumulado += $r->cantidad;
            $resultado[$r->fecha] = [
                'cantidad' => $r->cantidad,
                'acumulado' => $acumulado
            ];
        }

        return $resultado;
    }

    /**
     * Obtener compartidos por día (últimos N días)
     */
    private function obtenerCompartidosPorDia($eventoId, $fechaInicio, $fechaFin, $dias = 14)
    {
        $fechaInicioReciente = Carbon::now()->subDays($dias);
        $resultado = [];

        $compartidos = DB::table('evento_compartidos')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicioReciente, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get();

        $acumulado = 0;
        foreach ($compartidos as $c) {
            $acumulado += $c->cantidad;
            $resultado[$c->fecha] = [
                'cantidad' => $c->cantidad,
                'acumulado' => $acumulado
            ];
        }

        return $resultado;
    }

    /**
     * Obtener inscripciones por día con estados (últimos N días)
     */
    private function obtenerInscripcionesPorDia($eventoId, $fechaInicio, $fechaFin, $dias = 14)
    {
        $fechaInicioReciente = Carbon::now()->subDays($dias);
        $resultado = [];

        // Registrados
        $registrados = DB::table('evento_participaciones')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicioReciente, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, estado, COUNT(*) as cantidad')
            ->groupBy(DB::raw('DATE(created_at)'), 'estado')
            ->get();

        // No registrados
        $noRegistrados = DB::table('evento_participantes_no_registrados')
            ->where('evento_id', $eventoId)
            ->whereBetween('created_at', [$fechaInicioReciente, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, estado, COUNT(*) as cantidad')
            ->groupBy(DB::raw('DATE(created_at)'), 'estado')
            ->get();

        // Combinar y procesar
        foreach ($registrados as $r) {
            if (!isset($resultado[$r->fecha])) {
                $resultado[$r->fecha] = [
                    'total' => 0,
                    'aprobadas' => 0,
                    'pendientes' => 0,
                    'rechazadas' => 0
                ];
            }
            $resultado[$r->fecha]['total'] += $r->cantidad;
            if ($r->estado === 'aprobada') {
                $resultado[$r->fecha]['aprobadas'] += $r->cantidad;
            } elseif ($r->estado === 'pendiente') {
                $resultado[$r->fecha]['pendientes'] += $r->cantidad;
            } elseif ($r->estado === 'rechazada') {
                $resultado[$r->fecha]['rechazadas'] += $r->cantidad;
            }
        }

        foreach ($noRegistrados as $nr) {
            if (!isset($resultado[$nr->fecha])) {
                $resultado[$nr->fecha] = [
                    'total' => 0,
                    'aprobadas' => 0,
                    'pendientes' => 0,
                    'rechazadas' => 0
                ];
            }
            $resultado[$nr->fecha]['total'] += $nr->cantidad;
            if ($nr->estado === 'aprobada') {
                $resultado[$nr->fecha]['aprobadas'] += $nr->cantidad;
            } elseif ($nr->estado === 'pendiente') {
                $resultado[$nr->fecha]['pendientes'] += $nr->cantidad;
            } elseif ($nr->estado === 'rechazada') {
                $resultado[$nr->fecha]['rechazadas'] += $nr->cantidad;
            }
        }

        ksort($resultado);
        return $resultado;
    }

    /**
     * Generar URLs de gráficos profesionales con QuickChart
     */
    private function generarUrlsGraficosProfesionales($datos)
    {
        $baseUrl = 'https://quickchart.io/chart?c=';
        $params = '&width=600&height=300&backgroundColor=white&devicePixelRatio=1.5'; // Optimizado para velocidad
        
        $urls = [];

        // Gráfico 1: Reacciones por Día (Line Chart)
        $reaccionesData = $datos['tendencias']['reacciones_por_dia'] ?? [];
        $labels1 = array_map(function($fecha) {
            return Carbon::parse($fecha)->format('d/m');
        }, array_keys($reaccionesData));
        
        $chart1 = [
            'type' => 'line',
            'data' => [
                'labels' => $labels1,
                'datasets' => [[
                    'label' => 'Reacciones',
                    'data' => array_values($reaccionesData),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#dc3545'
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Reacciones por Día', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        $urls['reacciones'] = $baseUrl . urlencode(json_encode($chart1)) . $params;

        // Gráfico 2: Compartidos por Día (Bar Chart)
        $compartidosData = $datos['tendencias']['compartidos_por_dia'] ?? [];
        $labels2 = array_map(function($fecha) {
            return Carbon::parse($fecha)->format('d/m');
        }, array_keys($compartidosData));
        
        $chart2 = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels2,
                'datasets' => [[
                    'label' => 'Compartidos',
                    'data' => array_values($compartidosData),
                    'backgroundColor' => '#00A36C',
                    'borderColor' => '#00A36C',
                    'borderRadius' => 6
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Compartidos por Día', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        $urls['compartidos'] = $baseUrl . urlencode(json_encode($chart2)) . $params;

        // Gráfico 3: Inscripciones por Día (Line Chart)
        $inscripcionesData = $datos['tendencias']['inscripciones_por_dia'] ?? [];
        $labels3 = array_map(function($fecha) {
            return Carbon::parse($fecha)->format('d/m');
        }, array_keys($inscripcionesData));
        
        $chart3 = [
            'type' => 'line',
            'data' => [
                'labels' => $labels3,
                'datasets' => [[
                    'label' => 'Inscripciones',
                    'data' => array_values($inscripcionesData),
                    'borderColor' => '#17a2b8',
                    'backgroundColor' => 'rgba(23, 162, 184, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Inscripciones por Día', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        $urls['inscripciones'] = $baseUrl . urlencode(json_encode($chart3)) . $params;

        // Gráfico 4: Comparativa Reacciones vs Compartidos (Bar Chart agrupado)
        $todasFechas = array_unique(array_merge(array_keys($reaccionesData), array_keys($compartidosData)));
        sort($todasFechas);
        $labels4 = array_map(function($fecha) {
            return Carbon::parse($fecha)->format('d/m');
        }, $todasFechas);
        
        $chart4 = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels4,
                'datasets' => [
                    [
                        'label' => 'Reacciones',
                        'data' => array_map(function($fecha) use ($reaccionesData) {
                            return $reaccionesData[$fecha] ?? 0;
                        }, $todasFechas),
                        'backgroundColor' => '#dc3545'
                    ],
                    [
                        'label' => 'Compartidos',
                        'data' => array_map(function($fecha) use ($compartidosData) {
                            return $compartidosData[$fecha] ?? 0;
                        }, $todasFechas),
                        'backgroundColor' => '#00A36C'
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Comparativa Reacciones vs Compartidos', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'top']
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        $urls['comparativa'] = $baseUrl . urlencode(json_encode($chart4)) . $params;

        // Gráfico 5: Participantes por Estado (Doughnut Chart)
        $estadosData = $datos['distribucion_estados'] ?? [];
        $totalParticipantes = array_sum($estadosData);
        
        $chart5 = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_map('ucfirst', array_keys($estadosData)),
                'datasets' => [[
                    'data' => array_values($estadosData),
                    'backgroundColor' => ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Distribución de Participantes por Estado', 'font' => ['size' => 16]],
                    'legend' => ['display' => true, 'position' => 'right']
                ]
            ]
        ];
        $urls['distribucion_estados'] = $baseUrl . urlencode(json_encode($chart5)) . '&width=500&height=350&devicePixelRatio=1.5';

        // Gráfico 6: Actividad por Día de la Semana (Horizontal Bar Chart)
        $actividadDiaSemana = $datos['actividad_por_dia_semana'] ?? [];
        $chart6 = [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($actividadDiaSemana),
                'datasets' => [[
                    'label' => 'Actividad',
                    'data' => array_values($actividadDiaSemana),
                    'backgroundColor' => [
                        'rgba(12, 43, 68, 0.8)',
                        'rgba(0, 163, 108, 0.8)',
                        'rgba(12, 43, 68, 0.8)',
                        'rgba(0, 163, 108, 0.8)',
                        'rgba(12, 43, 68, 0.8)',
                        'rgba(0, 163, 108, 0.8)',
                        'rgba(12, 43, 68, 0.8)'
                    ]
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Actividad por Día de la Semana', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'x' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                    'y' => ['grid' => ['display' => false]]
                ]
            ]
        ];
        $urls['actividad_semana'] = $baseUrl . urlencode(json_encode($chart6)) . '&width=600&height=350&devicePixelRatio=1.5';

        // Gráfico 7: Métricas Radar
        $metricasRadar = $datos['metricas_radar'] ?? [];
        $chart7 = [
            'type' => 'radar',
            'data' => [
                'labels' => ['Engagement', 'Participación', 'Alcance', 'Conversión'],
                'datasets' => [[
                    'label' => 'Métricas Generales',
                    'data' => [
                        $metricasRadar['reacciones'] ?? 0,
                        $metricasRadar['participantes'] ?? 0,
                        $metricasRadar['compartidos'] ?? 0,
                        ($metricasRadar['voluntarios'] ?? 0) * 0.8
                    ],
                    'backgroundColor' => 'rgba(0, 163, 108, 0.2)',
                    'borderColor' => '#00A36C',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => '#00A36C'
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Métricas Generales (Radar)', 'font' => ['size' => 16]],
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => ['display' => false]
                    ]
                ]
            ]
        ];
        $urls['radar'] = $baseUrl . urlencode(json_encode($chart7)) . '&width=500&height=400&devicePixelRatio=1.5';

        return $urls;
    }

    /**
     * Calcular métricas adicionales para el PDF
     */
    private function calcularMetricasAdicionales($datos)
    {
        $metricas = $datos['metricas'];
        $participantesPorEstado = $metricas['participantes_por_estado'] ?? [];
        
        $totalParticipantes = $metricas['participantes_total'] ?? 0;
        $participantesAprobados = $participantesPorEstado['aprobada'] ?? 0;
        $participantesPendientes = $participantesPorEstado['pendiente'] ?? 0;
        $participantesRechazados = $participantesPorEstado['rechazada'] ?? 0;
        
        $tasaAprobacion = $totalParticipantes > 0 
            ? round(($participantesAprobados / $totalParticipantes) * 100, 2) 
            : 0;

        // Calcular engagement rate
        $totalReacciones = $metricas['reacciones'] ?? 0;
        $totalCompartidos = $metricas['compartidos'] ?? 0;
        $engagementRate = $totalParticipantes > 0
            ? round((($totalReacciones + $totalCompartidos) / $totalParticipantes) * 100, 2)
            : 0;

        // Calcular promedio de reacciones por participante
        $promedioReacciones = $totalParticipantes > 0
            ? round($totalReacciones / $totalParticipantes, 2)
            : 0;

        // Calcular tasa de conversión (inscripciones / vistas estimadas)
        // Asumimos que las vistas son aproximadamente 3x las reacciones
        $vistasEstimadas = $totalReacciones * 3;
        $tasaConversion = $vistasEstimadas > 0
            ? round(($totalParticipantes / $vistasEstimadas) * 100, 2)
            : 0;

        // Calcular tasa de participación (asistentes / inscritos)
        // Asumimos que los aprobados son los que asistieron
        $tasaParticipacion = $totalParticipantes > 0
            ? round(($participantesAprobados / $totalParticipantes) * 100, 2)
            : 0;

        return [
            'participantes_aprobados' => $participantesAprobados,
            'participantes_pendientes' => $participantesPendientes,
            'participantes_rechazados' => $participantesRechazados,
            'tasa_aprobacion' => $tasaAprobacion,
            'engagement_rate' => $engagementRate,
            'promedio_reacciones_por_participante' => $promedioReacciones,
            'tasa_conversion' => $tasaConversion,
            'tasa_participacion' => $tasaParticipacion
        ];
    }

    /**
     * Generar insights automáticos basados en los datos
     * Optimizado: solo insights esenciales para velocidad
     */
    private function generarInsights($datos, $metricasAdicionales)
    {
        $insights = [];
        
        // Insight sobre tasa de aprobación (más importante)
        $tasaAprobacion = $metricasAdicionales['tasa_aprobacion'] ?? 0;
        if ($tasaAprobacion >= 70) {
            $insights[] = "La tasa de aprobación es del {$tasaAprobacion}%, lo cual es excelente.";
        } elseif ($tasaAprobacion >= 50) {
            $insights[] = "La tasa de aprobación es del {$tasaAprobacion}%, lo cual es bueno pero hay margen de mejora.";
        } else {
            $insights[] = "La tasa de aprobación es del {$tasaAprobacion}%, se recomienda revisar los criterios de selección.";
        }

        // Insight sobre engagement (simplificado)
        $engagementRate = $metricasAdicionales['engagement_rate'] ?? 0;
        if ($engagementRate >= 50) {
            $insights[] = "El engagement promedio es {$engagementRate}%, indicando alta participación.";
        } elseif ($engagementRate >= 25) {
            $insights[] = "El engagement promedio es {$engagementRate}%, mostrando participación moderada.";
        } else {
            $insights[] = "El engagement promedio es {$engagementRate}%, se recomienda aumentar la participación.";
        }

        // Solo un insight de comparativas si hay datos significativos
        $comparativas = $datos['comparativas'] ?? [];
        if (!empty($comparativas)) {
            $crecimientoParticipantes = $comparativas['participantes_total']['crecimiento'] ?? 0;
            if (abs($crecimientoParticipantes) > 10) { // Solo si el cambio es significativo
                if ($crecimientoParticipantes > 0) {
                    $insights[] = "Crecimiento del " . abs($crecimientoParticipantes) . "% en participantes comparado con el período anterior.";
                } elseif ($crecimientoParticipantes < 0) {
                    $insights[] = "Disminución del " . abs($crecimientoParticipantes) . "% en participantes. Se recomienda analizar las causas.";
                }
            }
        }

        return $insights;
    }
}

