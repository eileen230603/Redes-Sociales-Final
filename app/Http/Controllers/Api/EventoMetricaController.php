<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoEmpresaParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\EventoParticipanteNoRegistrado;
use App\Models\Ong;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class EventoMetricaController extends Controller
{
    /**
     * Obtener métricas completas de un evento específico
     * Incluye KPIs, métricas de participación, engagement, etc.
     */
    public function metricasEvento(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos (ONG propietaria)
            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver estas métricas'
                ], 403);
            }

            // Calcular todas las métricas dentro de una transacción para consistencia
            $metricas = DB::transaction(function () use ($evento, $eventoId) {
                return $this->calcularMetricasCompletas($evento, $eventoId);
            });

            return response()->json([
                'success' => true,
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'estado' => $evento->estado,
                    'fecha_inicio' => $evento->fecha_inicio,
                    'fecha_fin' => $evento->fecha_fin,
                ],
                'metricas' => $metricas
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al obtener métricas del evento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al calcular métricas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métricas agregadas de todos los eventos de una ONG
     */
    public function metricasOng(Request $request)
    {
        try {
            $ongId = $request->user()->id_usuario;

            $metricas = DB::transaction(function () use ($ongId) {
                return $this->calcularMetricasOng($ongId);
            });

            return response()->json([
                'success' => true,
                'ong_id' => $ongId,
                'metricas' => $metricas
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al obtener métricas de la ONG: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al calcular métricas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener ciclo de vida completo de un evento
     * Desde concepción hasta finalización
     */
    public function cicloVidaEvento(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este ciclo de vida'
                ], 403);
            }

            $cicloVida = DB::transaction(function () use ($evento) {
                return $this->calcularCicloVida($evento);
            });

            return response()->json([
                'success' => true,
                'evento_id' => $eventoId,
                'ciclo_vida' => $cicloVida
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al obtener ciclo de vida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al calcular ciclo de vida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar reporte completo en PDF
     */
    public function generarReportePdf(Request $request, $eventoId)
    {
        try {
            $evento = Evento::find($eventoId);
            
            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            $ongId = $request->user()->id_usuario;
            if ($evento->ong_id != $ongId) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para generar este reporte'
                ], 403);
            }

            // Calcular métricas dentro de transacción
            $datos = DB::transaction(function () use ($evento, $eventoId) {
                return [
                    'evento' => $evento,
                    'metricas' => $this->calcularMetricasCompletas($evento, $eventoId),
                    'ciclo_vida' => $this->calcularCicloVida($evento),
                    'kpis' => $this->calcularKPIs($evento, $eventoId),
                ];
            });

            $pdf = Pdf::loadView('ong.reportes.evento-pdf', $datos);
            return $pdf->download("reporte-evento-{$eventoId}.pdf");

        } catch (\Throwable $e) {
            \Log::error('Error al generar reporte PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular métricas completas de un evento
     */
    private function calcularMetricasCompletas($evento, $eventoId)
    {
        // === PARTICIPACIÓN ===
        $participantesRegistrados = EventoParticipacion::where('evento_id', $eventoId)->count();
        $participantesNoRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)->count();
        $totalParticipantes = $participantesRegistrados + $participantesNoRegistrados;
        
        $participantesAprobados = EventoParticipacion::where('evento_id', $eventoId)
            ->where('estado', 'aprobada')
            ->count() + EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
            ->where('estado', 'aprobada')
            ->count();
        
        $participantesPendientes = EventoParticipacion::where('evento_id', $eventoId)
            ->where('estado', 'pendiente')
            ->count() + EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
            ->where('estado', 'pendiente')
            ->count();
        
        $participantesAsistieron = EventoParticipacion::where('evento_id', $eventoId)
            ->where('asistio', true)
            ->count();

        // === ENGAGEMENT ===
        $totalReacciones = EventoReaccion::where('evento_id', $eventoId)->count();
        $totalCompartidos = EventoCompartido::where('evento_id', $eventoId)->count();
        
        // === EMPRESAS ===
        $totalEmpresas = EventoEmpresaParticipacion::where('evento_id', $eventoId)
            ->where('activo', true)
            ->count();
        
        $empresasConfirmadas = EventoEmpresaParticipacion::where('evento_id', $eventoId)
            ->where('estado', 'confirmada')
            ->where('activo', true)
            ->count();

        // === CAPACIDAD ===
        $capacidadMaxima = $evento->capacidad_maxima;
        $porcentajeCapacidad = $capacidadMaxima > 0 
            ? round(($totalParticipantes / $capacidadMaxima) * 100, 2) 
            : null;

        // === TASAS Y RATIOS ===
        $tasaAprobacion = $totalParticipantes > 0 
            ? round(($participantesAprobados / $totalParticipantes) * 100, 2) 
            : 0;
        
        $tasaAsistencia = $participantesAprobados > 0 
            ? round(($participantesAsistieron / $participantesAprobados) * 100, 2) 
            : 0;
        
        $engagementRate = $totalParticipantes > 0 
            ? round((($totalReacciones + $totalCompartidos) / $totalParticipantes) * 100, 2) 
            : 0;

        // === TIEMPO ===
        $diasDesdeCreacion = $evento->created_at->diffInDays(now());
        $diasHastaInicio = $evento->fecha_inicio ? now()->diffInDays($evento->fecha_inicio, false) : null;
        $duracionEvento = $evento->fecha_fin && $evento->fecha_inicio 
            ? Carbon::parse($evento->fecha_inicio)->diffInDays($evento->fecha_fin) 
            : null;

        // === TENDENCIAS ===
        $inscripcionesPorDia = $this->calcularInscripcionesPorDia($eventoId);
        $reaccionesPorDia = $this->calcularReaccionesPorDia($eventoId);
        $compartidosPorDia = $this->calcularCompartidosPorDia($eventoId);

        return [
            'participacion' => [
                'total_participantes' => $totalParticipantes,
                'participantes_registrados' => $participantesRegistrados,
                'participantes_no_registrados' => $participantesNoRegistrados,
                'participantes_aprobados' => $participantesAprobados,
                'participantes_pendientes' => $participantesPendientes,
                'participantes_asistieron' => $participantesAsistieron,
                'capacidad_maxima' => $capacidadMaxima,
                'porcentaje_capacidad' => $porcentajeCapacidad,
            ],
            'engagement' => [
                'total_reacciones' => $totalReacciones,
                'total_compartidos' => $totalCompartidos,
                'engagement_rate' => $engagementRate,
            ],
            'empresas' => [
                'total_empresas' => $totalEmpresas,
                'empresas_confirmadas' => $empresasConfirmadas,
                'tasa_confirmacion_empresas' => $totalEmpresas > 0 
                    ? round(($empresasConfirmadas / $totalEmpresas) * 100, 2) 
                    : 0,
            ],
            'tasas' => [
                'tasa_aprobacion' => $tasaAprobacion,
                'tasa_asistencia' => $tasaAsistencia,
                'engagement_rate' => $engagementRate,
            ],
            'tiempo' => [
                'dias_desde_creacion' => $diasDesdeCreacion,
                'dias_hasta_inicio' => $diasHastaInicio,
                'duracion_evento_dias' => $duracionEvento,
                'fecha_creacion' => $evento->created_at->format('Y-m-d H:i:s'),
                'fecha_inicio' => $evento->fecha_inicio ? $evento->fecha_inicio->format('Y-m-d H:i:s') : null,
                'fecha_fin' => $evento->fecha_fin ? $evento->fecha_fin->format('Y-m-d H:i:s') : null,
            ],
            'tendencias' => [
                'inscripciones_por_dia' => $inscripcionesPorDia,
                'reacciones_por_dia' => $reaccionesPorDia,
                'compartidos_por_dia' => $compartidosPorDia,
            ],
        ];
    }

    /**
     * Calcular KPIs principales
     */
    private function calcularKPIs($evento, $eventoId)
    {
        $metricas = $this->calcularMetricasCompletas($evento, $eventoId);
        
        return [
            'kpi_participacion' => [
                'valor' => $metricas['participacion']['total_participantes'],
                'objetivo' => $evento->capacidad_maxima ?? 100,
                'porcentaje_cumplimiento' => $metricas['participacion']['porcentaje_capacidad'] ?? 0,
                'estado' => $this->evaluarKPI($metricas['participacion']['porcentaje_capacidad'] ?? 0, 80, 50),
            ],
            'kpi_engagement' => [
                'valor' => $metricas['engagement']['engagement_rate'],
                'objetivo' => 50, // 50% de engagement
                'porcentaje_cumplimiento' => min(($metricas['engagement']['engagement_rate'] / 50) * 100, 100),
                'estado' => $this->evaluarKPI($metricas['engagement']['engagement_rate'], 50, 25),
            ],
            'kpi_asistencia' => [
                'valor' => $metricas['tasas']['tasa_asistencia'],
                'objetivo' => 80, // 80% de asistencia
                'porcentaje_cumplimiento' => min(($metricas['tasas']['tasa_asistencia'] / 80) * 100, 100),
                'estado' => $this->evaluarKPI($metricas['tasas']['tasa_asistencia'], 80, 60),
            ],
            'kpi_empresas' => [
                'valor' => $metricas['empresas']['empresas_confirmadas'],
                'objetivo' => $metricas['empresas']['total_empresas'],
                'porcentaje_cumplimiento' => $metricas['empresas']['tasa_confirmacion_empresas'],
                'estado' => $this->evaluarKPI($metricas['empresas']['tasa_confirmacion_empresas'], 80, 50),
            ],
        ];
    }

    /**
     * Evaluar estado de un KPI
     */
    private function evaluarKPI($valor, $objetivo, $minimo)
    {
        if ($valor >= $objetivo) return 'excelente';
        if ($valor >= $minimo) return 'bueno';
        return 'mejorable';
    }

    /**
     * Calcular ciclo de vida completo del evento
     */
    private function calcularCicloVida($evento)
    {
        $ahora = now();
        $fechaCreacion = $evento->created_at;
        $fechaInicio = $evento->fecha_inicio;
        $fechaFin = $evento->fecha_fin;
        $fechaFinalizacion = $evento->fecha_finalizacion;

        // Etapas del ciclo de vida
        $etapas = [];

        // 1. CONCEPCIÓN (creación del evento)
        $etapas[] = [
            'etapa' => 'concepcion',
            'nombre' => 'Concepción',
            'fecha_inicio' => $fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_fin' => $evento->estado == 'borrador' ? null : ($fechaInicio ? $fechaInicio->format('Y-m-d H:i:s') : null),
            'duracion_dias' => $fechaInicio ? $fechaCreacion->diffInDays($fechaInicio) : $fechaCreacion->diffInDays($ahora),
            'estado' => $evento->estado == 'borrador' ? 'activa' : 'completada',
            'descripcion' => 'Evento creado en borrador',
        ];

        // 2. PLANIFICACIÓN (desde creación hasta publicación)
        if ($evento->estado != 'borrador') {
            $fechaPublicacion = $evento->updated_at; // Aproximación
            $etapas[] = [
                'etapa' => 'planificacion',
                'nombre' => 'Planificación',
                'fecha_inicio' => $fechaCreacion->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaPublicacion->format('Y-m-d H:i:s'),
                'duracion_dias' => $fechaCreacion->diffInDays($fechaPublicacion),
                'estado' => 'completada',
                'descripcion' => 'Evento planificado y publicado',
            ];
        }

        // 3. PROMOCIÓN (desde publicación hasta inicio)
        if ($evento->estado == 'publicado' && $fechaInicio) {
            $etapas[] = [
                'etapa' => 'promocion',
                'nombre' => 'Promoción',
                'fecha_inicio' => $evento->updated_at->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaInicio->isFuture() ? null : $fechaInicio->format('Y-m-d H:i:s'),
                'duracion_dias' => $fechaInicio->isFuture() 
                    ? $ahora->diffInDays($fechaInicio, false) 
                    : $evento->updated_at->diffInDays($fechaInicio),
                'estado' => $fechaInicio->isFuture() ? 'activa' : 'completada',
                'descripcion' => 'Período de promoción e inscripciones',
            ];
        }

        // 4. EJECUCIÓN (durante el evento)
        if ($fechaInicio && $fechaFin) {
            $estaActivo = $fechaInicio->isPast() && $fechaFin->isFuture();
            $etapas[] = [
                'etapa' => 'ejecucion',
                'nombre' => 'Ejecución',
                'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFin->isPast() ? $fechaFin->format('Y-m-d H:i:s') : null,
                'duracion_dias' => Carbon::parse($fechaInicio)->diffInDays($fechaFin),
                'estado' => $estaActivo ? 'activa' : ($fechaFin->isPast() ? 'completada' : 'pendiente'),
                'descripcion' => 'Evento en ejecución',
            ];
        }

        // 5. FINALIZACIÓN (después del evento)
        if ($fechaFin && $fechaFin->isPast()) {
            $etapas[] = [
                'etapa' => 'finalizacion',
                'nombre' => 'Finalización',
                'fecha_inicio' => $fechaFin->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFinalizacion ? $fechaFinalizacion->format('Y-m-d H:i:s') : null,
                'duracion_dias' => $fechaFinalizacion 
                    ? Carbon::parse($fechaFin)->diffInDays($fechaFinalizacion)
                    : Carbon::parse($fechaFin)->diffInDays($ahora),
                'estado' => $fechaFinalizacion ? 'completada' : 'activa',
                'descripcion' => 'Evento finalizado, generando reportes y métricas',
            ];
        }

        // Calcular estadísticas del ciclo
        $etapasCompletadas = collect($etapas)->where('estado', 'completada')->count();
        $etapasActivas = collect($etapas)->where('estado', 'activa')->count();
        $etapaActual = collect($etapas)->where('estado', 'activa')->first() 
            ?? collect($etapas)->where('estado', 'pendiente')->first()
            ?? collect($etapas)->last();

        return [
            'etapas' => $etapas,
            'resumen' => [
                'total_etapas' => count($etapas),
                'etapas_completadas' => $etapasCompletadas,
                'etapas_activas' => $etapasActivas,
                'etapa_actual' => $etapaActual,
                'progreso_porcentaje' => count($etapas) > 0 
                    ? round(($etapasCompletadas / count($etapas)) * 100, 2) 
                    : 0,
            ],
        ];
    }

    /**
     * Calcular métricas agregadas de la ONG
     */
    private function calcularMetricasOng($ongId)
    {
        $eventos = Evento::where('ong_id', $ongId)->get();
        $eventosIds = $eventos->pluck('id');

        // Totales
        $totalEventos = $eventos->count();
        $eventosPublicados = $eventos->where('estado', 'publicado')->count();
        $eventosFinalizados = $eventos->where('estado', 'finalizado')->count();
        $eventosActivos = $eventos->filter(function($e) {
            return $e->estaActivo();
        })->count();

        // Participación total
        $totalParticipantes = EventoParticipacion::whereIn('evento_id', $eventosIds)->count();
        $totalVoluntariosUnicos = EventoParticipacion::whereIn('evento_id', $eventosIds)
            ->distinct('externo_id')
            ->count('externo_id');

        // Engagement total
        $totalReacciones = EventoReaccion::whereIn('evento_id', $eventosIds)->count();
        $totalCompartidos = EventoCompartido::whereIn('evento_id', $eventosIds)->count();

        // Promedios
        $promedioParticipantes = $totalEventos > 0 
            ? round($totalParticipantes / $totalEventos, 2) 
            : 0;
        
        $promedioEngagement = $totalEventos > 0 
            ? round(($totalReacciones + $totalCompartidos) / $totalEventos, 2) 
            : 0;

        return [
            'eventos' => [
                'total' => $totalEventos,
                'publicados' => $eventosPublicados,
                'finalizados' => $eventosFinalizados,
                'activos' => $eventosActivos,
            ],
            'participacion' => [
                'total_participantes' => $totalParticipantes,
                'voluntarios_unicos' => $totalVoluntariosUnicos,
                'promedio_por_evento' => $promedioParticipantes,
            ],
            'engagement' => [
                'total_reacciones' => $totalReacciones,
                'total_compartidos' => $totalCompartidos,
                'promedio_por_evento' => $promedioEngagement,
            ],
        ];
    }

    /**
     * Calcular inscripciones por día
     */
    private function calcularInscripcionesPorDia($eventoId)
    {
        $registrados = EventoParticipacion::where('evento_id', $eventoId)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        $noRegistrados = EventoParticipanteNoRegistrado::where('evento_id', $eventoId)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();

        $resultado = [];
        $todasLasFechas = array_unique(array_merge(array_keys($registrados), array_keys($noRegistrados)));
        
        foreach ($todasLasFechas as $fecha) {
            $resultado[$fecha] = ($registrados[$fecha] ?? 0) + ($noRegistrados[$fecha] ?? 0);
        }

        return $resultado;
    }

    /**
     * Calcular reacciones por día
     */
    private function calcularReaccionesPorDia($eventoId)
    {
        return EventoReaccion::where('evento_id', $eventoId)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();
    }

    /**
     * Calcular compartidos por día
     */
    private function calcularCompartidosPorDia($eventoId)
    {
        return EventoCompartido::where('evento_id', $eventoId)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray();
    }
}

