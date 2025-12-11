<?php

namespace App\Services;

use App\Models\MegaEvento;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Servicio para generar reportes avanzados de ONGs
 * 
 * Contiene toda la lógica de negocio para calcular métricas, agregaciones
 * y análisis complejos. Optimizado con eager loading y queries eficientes.
 */
class ReportService
{
    /**
     * Obtener KPIs destacados para el dashboard
     * Métricas principales que se muestran en cards con análisis comparativo
     * 
     * Incluye métricas operativas, tácticas y estratégicas:
     * - Totales y tasas de conversión
     * - Comparativas período actual vs anterior
     * - Tendencias y crecimiento
     * - Datos consolidados de eventos regulares y mega eventos
     */
    public function getKPIsDestacados(int $ongId): array
    {
        // ========== MEGA EVENTOS ==========
        // Query optimizado con select específico
        $megaEventos = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'estado', 'categoria', 'fecha_creacion', 'fecha_fin', 'capacidad_maxima')
            ->get();

        $totalMegaEventos = $megaEventos->count();
        $megaEventosFinalizados = $megaEventos->where('estado', 'finalizado')->count();
        $megaEventosActivos = $megaEventos->whereIn('estado', ['activo', 'en_curso'])->count();
        $megaEventosCancelados = $megaEventos->where('estado', 'cancelado')->count();

        // Total de participantes de mega eventos (query optimizado con join)
        $totalParticipantesMega = DB::table('mega_evento_participantes_externos')
            ->join('mega_eventos', 'mega_evento_participantes_externos.mega_evento_id', '=', 'mega_eventos.mega_evento_id')
            ->where('mega_eventos.ong_organizadora_principal', $ongId)
            ->where('mega_evento_participantes_externos.activo', true)
            ->count() + 
            DB::table('mega_evento_participantes_no_registrados')
            ->join('mega_eventos', 'mega_evento_participantes_no_registrados.mega_evento_id', '=', 'mega_eventos.mega_evento_id')
            ->where('mega_eventos.ong_organizadora_principal', $ongId)
            ->where('mega_evento_participantes_no_registrados.estado', '!=', 'rechazada')
            ->count();

        // Total de empresas patrocinadoras de mega eventos (distinct para evitar duplicados)
        $totalPatrocinadoresMega = DB::table('mega_evento_patrocinadores')
            ->join('mega_eventos', 'mega_evento_patrocinadores.mega_evento_id', '=', 'mega_eventos.mega_evento_id')
            ->where('mega_eventos.ong_organizadora_principal', $ongId)
            ->distinct('mega_evento_patrocinadores.empresa_id')
            ->count('mega_evento_patrocinadores.empresa_id');

        // ========== EVENTOS REGULARES ==========
        // Query optimizado para eventos regulares
        $eventos = Evento::where('ong_id', $ongId)
            ->select('id', 'estado', 'fecha_inicio', 'fecha_fin', 'fecha_finalizacion', 'tipo_evento', 'capacidad_maxima')
            ->get();

        $totalEventos = $eventos->count();
        
        // Eventos finalizados: estado = 'finalizado' O fecha_fin < ahora O tiene fecha_finalizacion
        $ahora = now();
        $eventosFinalizados = $eventos->filter(function($evento) use ($ahora) {
            if ($evento->estado === 'finalizado') {
                return true;
            }
            if ($evento->fecha_finalizacion) {
                return true;
            }
            if ($evento->fecha_fin) {
                return \Carbon\Carbon::parse($evento->fecha_fin)->isPast();
            }
            return false;
        })->count();

        $eventosActivos = $eventos->filter(function($evento) use ($ahora) {
            if ($evento->estado === 'cancelado' || $evento->estado === 'borrador') {
                return false;
            }
            if ($evento->fecha_inicio && $evento->fecha_fin) {
                $inicio = \Carbon\Carbon::parse($evento->fecha_inicio);
                $fin = \Carbon\Carbon::parse($evento->fecha_fin);
                return $inicio->isPast() && $fin->isFuture();
            }
            return $evento->estado === 'publicado';
        })->count();

        $eventosCancelados = $eventos->where('estado', 'cancelado')->count();

        // Total de participantes de eventos regulares (aprobados)
        $totalParticipantesEventos = DB::table('evento_participaciones')
            ->join('eventos', 'evento_participaciones.evento_id', '=', 'eventos.id')
            ->where('eventos.ong_id', $ongId)
            ->where('evento_participaciones.estado', 'aprobada')
            ->count();

        // Total de empresas patrocinadoras de eventos regulares
        $totalPatrocinadoresEventos = DB::table('evento_empresas_participantes')
            ->join('eventos', 'evento_empresas_participantes.evento_id', '=', 'eventos.id')
            ->where('eventos.ong_id', $ongId)
            ->where('evento_empresas_participantes.activo', true)
            ->distinct('evento_empresas_participantes.empresa_id')
            ->count('evento_empresas_participantes.empresa_id');

        // ========== TOTALES CONSOLIDADOS ==========
        $totalEventosGeneral = $totalEventos + $totalMegaEventos;
        $totalFinalizadosGeneral = $eventosFinalizados + $megaEventosFinalizados;
        $totalParticipantesGeneral = $totalParticipantesEventos + $totalParticipantesMega;
        $totalPatrocinadoresGeneral = $totalPatrocinadoresEventos + $totalPatrocinadoresMega;

        // ========== TASAS DE FINALIZACIÓN ==========
        // Tasa de finalización de eventos regulares
        $tasaFinalizacionEventos = $totalEventos > 0 
            ? round(($eventosFinalizados / $totalEventos) * 100, 2) 
            : 0;

        // Tasa de finalización de mega eventos
        $tasaFinalizacionMega = $totalMegaEventos > 0 
            ? round(($megaEventosFinalizados / $totalMegaEventos) * 100, 2) 
            : 0;

        // Tasa de finalización consolidada
        $tasaFinalizacionGeneral = $totalEventosGeneral > 0 
            ? round(($totalFinalizadosGeneral / $totalEventosGeneral) * 100, 2) 
            : 0;

        // Tasa de cancelación consolidada
        $totalCanceladosGeneral = $eventosCancelados + $megaEventosCancelados;
        $tasaCancelacionGeneral = $totalEventosGeneral > 0 
            ? round(($totalCanceladosGeneral / $totalEventosGeneral) * 100, 2) 
            : 0;

        // ANÁLISIS COMPARATIVO: Período actual vs anterior (últimos 6 meses vs anteriores 6 meses)
        $hace6Meses = $ahora->copy()->subMonths(6);
        $hace12Meses = $ahora->copy()->subMonths(12);

        // Mega eventos últimos 6 meses
        $megaEventosUltimos6Meses = $megaEventos->filter(function ($evento) use ($hace6Meses) {
            return Carbon::parse($evento->fecha_creacion)->gte($hace6Meses);
        })->count();

        // Mega eventos 6 meses anteriores
        $megaEventos6MesesAnteriores = $megaEventos->filter(function ($evento) use ($hace6Meses, $hace12Meses) {
            $fecha = Carbon::parse($evento->fecha_creacion);
            return $fecha->gte($hace12Meses) && $fecha->lt($hace6Meses);
        })->count();

        // Eventos regulares últimos 6 meses
        $eventosUltimos6Meses = $eventos->filter(function ($evento) use ($hace6Meses) {
            return $evento->fecha_inicio && Carbon::parse($evento->fecha_inicio)->gte($hace6Meses);
        })->count();

        // Eventos regulares 6 meses anteriores
        $eventos6MesesAnteriores = $eventos->filter(function ($evento) use ($hace6Meses, $hace12Meses) {
            if (!$evento->fecha_inicio) return false;
            $fecha = Carbon::parse($evento->fecha_inicio);
            return $fecha->gte($hace12Meses) && $fecha->lt($hace6Meses);
        })->count();

        // Totales consolidados para comparativa
        $totalUltimos6Meses = $megaEventosUltimos6Meses + $eventosUltimos6Meses;
        $total6MesesAnteriores = $megaEventos6MesesAnteriores + $eventos6MesesAnteriores;

        // Calcular crecimiento porcentual
        $crecimientoEventos = $total6MesesAnteriores > 0 
            ? round((($totalUltimos6Meses - $total6MesesAnteriores) / $total6MesesAnteriores) * 100, 2)
            : ($totalUltimos6Meses > 0 ? 100 : 0);

        // Capacidad total y promedio (mega eventos)
        $capacidadTotalMega = $megaEventos->sum('capacidad_maxima') ?? 0;
        $promedioCapacidadMega = $totalMegaEventos > 0 
            ? round($capacidadTotalMega / $totalMegaEventos, 2)
            : 0;

        // Tasa de utilización mega eventos
        $tasaUtilizacionMega = $capacidadTotalMega > 0 
            ? round(($totalParticipantesMega / $capacidadTotalMega) * 100, 2)
            : 0;

        // Distribución por categoría (top 3) - mega eventos
        $distribucionCategoriaMega = $megaEventos->groupBy('categoria')
            ->map->count()
            ->sortDesc()
            ->take(3)
            ->toArray();

        return [
            // ========== MEGA EVENTOS ==========
            'total_mega_eventos' => $totalMegaEventos,
            'mega_eventos_finalizados' => $megaEventosFinalizados,
            'mega_eventos_activos' => $megaEventosActivos,
            'mega_eventos_cancelados' => $megaEventosCancelados,
            'total_participantes_mega' => $totalParticipantesMega,
            'total_patrocinadores_mega' => $totalPatrocinadoresMega,
            'tasa_finalizacion_mega' => $tasaFinalizacionMega,
            'tasa_utilizacion_mega' => $tasaUtilizacionMega,
            
            // ========== EVENTOS REGULARES ==========
            'total_eventos' => $totalEventos,
            'eventos_finalizados' => $eventosFinalizados,
            'eventos_activos' => $eventosActivos,
            'eventos_cancelados' => $eventosCancelados,
            'total_participantes_eventos' => $totalParticipantesEventos,
            'total_patrocinadores_eventos' => $totalPatrocinadoresEventos,
            'tasa_finalizacion_eventos' => $tasaFinalizacionEventos,
            
            // ========== TOTALES CONSOLIDADOS ==========
            'total_eventos_general' => $totalEventosGeneral,
            'total_finalizados_general' => $totalFinalizadosGeneral,
            'total_participantes' => $totalParticipantesGeneral,
            'total_patrocinadores' => $totalPatrocinadoresGeneral,
            'tasa_finalizacion' => $tasaFinalizacionGeneral,
            'tasa_cancelacion' => $tasaCancelacionGeneral,
            
            // ========== DETALLES DE TASA DE FINALIZACIÓN ==========
            'detalle_tasa_finalizacion' => [
                'eventos_regulares' => [
                    'total' => $totalEventos,
                    'finalizados' => $eventosFinalizados,
                    'tasa' => $tasaFinalizacionEventos,
                    'porcentaje' => $totalEventosGeneral > 0 
                        ? round(($totalEventos / $totalEventosGeneral) * 100, 2) 
                        : 0,
                ],
                'mega_eventos' => [
                    'total' => $totalMegaEventos,
                    'finalizados' => $megaEventosFinalizados,
                    'tasa' => $tasaFinalizacionMega,
                    'porcentaje' => $totalEventosGeneral > 0 
                        ? round(($totalMegaEventos / $totalEventosGeneral) * 100, 2) 
                        : 0,
                ],
                'consolidado' => [
                    'total' => $totalEventosGeneral,
                    'finalizados' => $totalFinalizadosGeneral,
                    'tasa' => $tasaFinalizacionGeneral,
                ],
            ],
            
            // Análisis comparativo
            'eventos_ultimos_6_meses' => $totalUltimos6Meses,
            'eventos_6_meses_anteriores' => $total6MesesAnteriores,
            'crecimiento_porcentual' => $crecimientoEventos,
            
            // Métricas de capacidad
            'capacidad_total' => $capacidadTotalMega,
            'promedio_capacidad' => $promedioCapacidadMega,
            
            // Distribución
            'distribucion_categoria' => $distribucionCategoriaMega,
        ];
    }

    /**
     * Reporte 1: Resumen Ejecutivo Consolidado (Eventos Regulares + Mega Eventos)
     * Totales generales, KPIs principales, gráfico de torta por categorías
     * 
     * Consultas SQL optimizadas con eager loading y agregaciones eficientes
     */
    public function getResumenEjecutivo(int $ongId, array $filtros = []): array
    {
        // ========== MEGA EVENTOS ==========
        $queryMega = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'titulo', 'categoria', 'estado', 'fecha_creacion', 'fecha_fin', 'capacidad_maxima');

        // Aplicar filtros
        $queryMega = $this->aplicarFiltros($queryMega, $filtros);
        $megaEventos = $queryMega->get();

        // ========== EVENTOS REGULARES ==========
        $queryEventos = Evento::where('ong_id', $ongId)
            ->select('id', 'titulo', 'tipo_evento as categoria', 'estado', 'fecha_inicio as fecha_creacion', 'fecha_fin', 'capacidad_maxima');

        // Aplicar filtros a eventos regulares
        if (isset($filtros['fecha_inicio'])) {
            $queryEventos->where('fecha_inicio', '>=', $filtros['fecha_inicio']);
        }
        if (isset($filtros['fecha_fin'])) {
            $queryEventos->where('fecha_fin', '<=', $filtros['fecha_fin']);
        }
        if (isset($filtros['categoria']) && $filtros['categoria']) {
            $queryEventos->where('tipo_evento', $filtros['categoria']);
        }
        if (isset($filtros['estado']) && $filtros['estado']) {
            $queryEventos->where('estado', $filtros['estado']);
        }

        $eventos = $queryEventos->get();

        // ========== CONSOLIDACIÓN DE DATOS ==========
        // Combinar eventos regulares y mega eventos para análisis
        $todosEventos = collect();
        
        // Agregar eventos regulares con tipo identificador
        foreach ($eventos as $evento) {
            $todosEventos->push((object)[
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'categoria' => $evento->categoria,
                'estado' => $evento->estado,
                'fecha_creacion' => $evento->fecha_creacion,
                'fecha_fin' => $evento->fecha_fin,
                'tipo' => 'regular'
            ]);
        }
        
        // Agregar mega eventos con tipo identificador
        foreach ($megaEventos as $megaEvento) {
            $todosEventos->push((object)[
                'id' => $megaEvento->mega_evento_id,
                'titulo' => $megaEvento->titulo,
                'categoria' => $megaEvento->categoria,
                'estado' => $megaEvento->estado,
                'fecha_creacion' => $megaEvento->fecha_creacion,
                'fecha_fin' => $megaEvento->fecha_fin,
                'tipo' => 'mega'
            ]);
        }

        // Totales generales consolidados
        $totales = [
            'total_eventos' => $todosEventos->count(),
            'total_eventos_regulares' => $eventos->count(),
            'total_mega_eventos' => $megaEventos->count(),
            'por_categoria' => $todosEventos->groupBy('categoria')->map->count(),
            'por_estado' => $todosEventos->groupBy('estado')->map->count(),
            'por_tipo' => $todosEventos->groupBy('tipo')->map->count(),
        ];

        // KPIs principales consolidados
        $ahora = now();
        $eventosFinalizados = $todosEventos->filter(function($evento) use ($ahora) {
            if ($evento->estado === 'finalizado') return true;
            if ($evento->fecha_fin) {
                return Carbon::parse($evento->fecha_fin)->isPast();
            }
            return false;
        })->count();

        $eventosActivos = $todosEventos->filter(function($evento) use ($ahora) {
            if (in_array($evento->estado, ['activo', 'en_curso', 'publicado'])) return true;
            if ($evento->fecha_creacion && $evento->fecha_fin) {
                $inicio = Carbon::parse($evento->fecha_creacion);
                $fin = Carbon::parse($evento->fecha_fin);
                return $inicio->isPast() && $fin->isFuture();
            }
            return false;
        })->count();

        $kpis = [
            'eventos_finalizados' => $eventosFinalizados,
            'eventos_activos' => $eventosActivos,
            'eventos_cancelados' => $todosEventos->where('estado', 'cancelado')->count(),
            'tasa_finalizacion' => $todosEventos->count() > 0 
                ? round(($eventosFinalizados / $todosEventos->count()) * 100, 2)
                : 0,
            'tasa_cancelacion' => $todosEventos->count() > 0
                ? round(($todosEventos->where('estado', 'cancelado')->count() / $todosEventos->count()) * 100, 2)
                : 0,
        ];

        // Total de participantes (query optimizada con JOIN)
        $totalParticipantesEventos = DB::table('evento_participaciones')
            ->join('eventos', 'evento_participaciones.evento_id', '=', 'eventos.id')
            ->where('eventos.ong_id', $ongId)
            ->where('evento_participaciones.estado', 'aprobada')
            ->count();

        $totalParticipantesMega = DB::table('mega_evento_participantes_externos')
            ->join('mega_eventos', 'mega_evento_participantes_externos.mega_evento_id', '=', 'mega_eventos.mega_evento_id')
            ->where('mega_eventos.ong_organizadora_principal', $ongId)
            ->where('mega_evento_participantes_externos.activo', true)
            ->count() + 
            DB::table('mega_evento_participantes_no_registrados')
            ->join('mega_eventos', 'mega_evento_participantes_no_registrados.mega_evento_id', '=', 'mega_eventos.mega_evento_id')
            ->where('mega_eventos.ong_organizadora_principal', $ongId)
            ->where('mega_evento_participantes_no_registrados.estado', '!=', 'rechazada')
            ->count();

        $kpis['total_participantes'] = $totalParticipantesEventos + $totalParticipantesMega;
        $kpis['total_participantes_eventos'] = $totalParticipantesEventos;
        $kpis['total_participantes_mega'] = $totalParticipantesMega;

        // Datos para gráfico de torta por categorías (consolidado)
        $datosGraficoTorta = $totales['por_categoria']->map(function ($cantidad, $categoria) {
            return [
                'categoria' => ucfirst($categoria),
                'cantidad' => $cantidad,
                'porcentaje' => 0
            ];
        })->values();

        // Calcular porcentajes
        $total = $datosGraficoTorta->sum('cantidad');
        $datosGraficoTorta = $datosGraficoTorta->map(function ($item) use ($total) {
            $item['porcentaje'] = $total > 0 ? round(($item['cantidad'] / $total) * 100, 2) : 0;
            return $item;
        });

        return [
            'totales' => $totales,
            'kpis' => $kpis,
            'grafico_torta' => $datosGraficoTorta,
            'filtros_aplicados' => $filtros,
        ];
    }

    /**
     * Reporte 2: Análisis Temporal de Eventos
     * Gráfico de líneas de eventos creados por mes con comparativa año anterior
     */
    public function getAnalisisTemporal(int $ongId, array $filtros = []): array
    {
        $query = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'fecha_creacion', 'estado', 'categoria');

        // Aplicar filtros excepto fechas (las usamos para el análisis)
        $filtrosSinFechas = $filtros;
        unset($filtrosSinFechas['fecha_inicio'], $filtrosSinFechas['fecha_fin']);
        $query = $this->aplicarFiltros($query, $filtrosSinFechas);

        $megaEventos = $query->get();

        // Determinar rango de fechas
        $fechaInicio = isset($filtros['fecha_inicio']) 
            ? Carbon::parse($filtros['fecha_inicio']) 
            : ($megaEventos->min('fecha_creacion') ? Carbon::parse($megaEventos->min('fecha_creacion')) : Carbon::now()->subYear());
        
        $fechaFin = isset($filtros['fecha_fin']) 
            ? Carbon::parse($filtros['fecha_fin']) 
            : Carbon::now();

        // Agrupar por mes del año actual
        $eventosPorMes = $megaEventos->filter(function ($evento) use ($fechaInicio, $fechaFin) {
            $fechaEvento = Carbon::parse($evento->fecha_creacion);
            return $fechaEvento->between($fechaInicio, $fechaFin);
        })->groupBy(function ($evento) {
            return Carbon::parse($evento->fecha_creacion)->format('Y-m');
        })->map->count();

        // Obtener datos del año anterior para comparación
        $fechaInicioAnterior = $fechaInicio->copy()->subYear();
        $fechaFinAnterior = $fechaFin->copy()->subYear();

        $eventosAnioAnterior = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->whereBetween('fecha_creacion', [$fechaInicioAnterior, $fechaFinAnterior])
            ->get()
            ->groupBy(function ($evento) {
                return Carbon::parse($evento->fecha_creacion)->format('Y-m');
            })->map->count();

        // Construir array de tendencias con comparativa
        $tendencias = [];
        $meses = [];
        $actual = $fechaInicio->copy()->startOfMonth();
        
        while ($actual->lte($fechaFin)) {
            $mesKey = $actual->format('Y-m');
            $mesAnteriorKey = $actual->copy()->subYear()->format('Y-m');
            
            $cantidadActual = $eventosPorMes->get($mesKey, 0);
            $cantidadAnterior = $eventosAnioAnterior->get($mesAnteriorKey, 0);
            
            $crecimiento = $cantidadAnterior > 0 
                ? round((($cantidadActual - $cantidadAnterior) / $cantidadAnterior) * 100, 2)
                : ($cantidadActual > 0 ? 100 : 0);

            $tendencias[] = [
                'mes' => $actual->format('M Y'),
                'mes_key' => $mesKey,
                'cantidad_actual' => $cantidadActual,
                'cantidad_anterior' => $cantidadAnterior,
                'crecimiento_porcentual' => $crecimiento,
            ];

            $meses[] = $actual->format('M Y');
            $actual->addMonth();
        }

        // Calcular promedios y totales
        $promedioMensual = count($tendencias) > 0 
            ? round(array_sum(array_column($tendencias, 'cantidad_actual')) / count($tendencias), 2)
            : 0;

        $totalActual = array_sum(array_column($tendencias, 'cantidad_actual'));
        $totalAnterior = array_sum(array_column($tendencias, 'cantidad_anterior'));

        $crecimientoTotal = $totalAnterior > 0 
            ? round((($totalActual - $totalAnterior) / $totalAnterior) * 100, 2)
            : ($totalActual > 0 ? 100 : 0);

        return [
            'tendencias' => $tendencias,
            'meses' => $meses,
            'promedio_mensual' => $promedioMensual,
            'total_actual' => $totalActual,
            'total_anterior' => $totalAnterior,
            'crecimiento_total' => $crecimientoTotal,
            'filtros_aplicados' => $filtros,
        ];
    }

    /**
     * Reporte 3: Participación y Colaboración
     * Top empresas patrocinadoras, voluntarios más activos, eventos con más colaboradores
     */
    public function getParticipacionColaboracion(int $ongId, array $filtros = []): array
    {
        // Query base para mega eventos
        $query = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'titulo', 'fecha_creacion');

        $query = $this->aplicarFiltros($query, $filtros);
        $megaEventosIds = $query->pluck('mega_evento_id');

        // Top empresas patrocinadoras (query optimizado con join)
        $topEmpresas = DB::table('mega_evento_patrocinadores')
            ->join('empresas', 'mega_evento_patrocinadores.empresa_id', '=', 'empresas.user_id')
            ->whereIn('mega_evento_patrocinadores.mega_evento_id', $megaEventosIds)
            ->select(
                'empresas.user_id',
                'empresas.nombre',
                DB::raw('COUNT(DISTINCT mega_evento_patrocinadores.mega_evento_id) as total_eventos'),
                DB::raw('COUNT(mega_evento_patrocinadores.empresa_id) as total_patrocinios')
            )
            ->groupBy('empresas.user_id', 'empresas.nombre')
            ->orderByDesc('total_eventos')
            ->limit(10)
            ->get();

        // Voluntarios más activos (participaciones)
        $topVoluntarios = DB::table('mega_evento_participantes_externos')
            ->join('users', 'mega_evento_participantes_externos.user_id', '=', 'users.id_usuario')
            ->whereIn('mega_evento_participantes_externos.mega_evento_id', $megaEventosIds)
            ->select(
                'users.id_usuario',
                'users.nombre',
                'users.email',
                DB::raw('COUNT(DISTINCT mega_evento_participantes_externos.mega_evento_id) as total_eventos'),
                DB::raw('COUNT(mega_evento_participantes_externos.user_id) as total_participaciones')
            )
            ->groupBy('users.id_usuario', 'users.nombre', 'users.email')
            ->orderByDesc('total_eventos')
            ->limit(10)
            ->get();

        // Eventos con más colaboradores (patrocinadores + participantes)
        $eventosColaboracion = DB::table('mega_eventos')
            ->leftJoin('mega_evento_patrocinadores', 'mega_eventos.mega_evento_id', '=', 'mega_evento_patrocinadores.mega_evento_id')
            ->leftJoin('mega_evento_participantes_externos', 'mega_eventos.mega_evento_id', '=', 'mega_evento_participantes_externos.mega_evento_id')
            ->whereIn('mega_eventos.mega_evento_id', $megaEventosIds)
            ->select(
                'mega_eventos.mega_evento_id',
                'mega_eventos.titulo',
                DB::raw('COUNT(DISTINCT mega_evento_patrocinadores.empresa_id) as total_patrocinadores'),
                DB::raw('COUNT(DISTINCT mega_evento_participantes_externos.user_id) as total_participantes'),
                DB::raw('(COUNT(DISTINCT mega_evento_patrocinadores.empresa_id) + COUNT(DISTINCT mega_evento_participantes_externos.user_id)) as total_colaboradores')
            )
            ->groupBy('mega_eventos.mega_evento_id', 'mega_eventos.titulo')
            ->orderByDesc('total_colaboradores')
            ->limit(10)
            ->get();

        return [
            'top_empresas' => $topEmpresas,
            'top_voluntarios' => $topVoluntarios,
            'eventos_colaboracion' => $eventosColaboracion,
            'filtros_aplicados' => $filtros,
        ];
    }

    /**
     * Reporte 4: Análisis Geográfico
     * Tabla de ciudades con más eventos, distribución por departamentos
     */
    public function getAnalisisGeografico(int $ongId, array $filtros = []): array
    {
        $query = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'ubicacion', 'lat', 'lng');

        $query = $this->aplicarFiltros($query, $filtros);
        $megaEventos = $query->get();

        // Extraer ciudades de ubicaciones (lógica simplificada)
        $ciudades = [];
        foreach ($megaEventos as $evento) {
            if ($evento->ubicacion) {
                // Intentar extraer ciudad de la ubicación
                $ubicacionParts = explode(',', $evento->ubicacion);
                $ciudad = trim($ubicacionParts[0] ?? 'Sin especificar');
                
                if (!isset($ciudades[$ciudad])) {
                    $ciudades[$ciudad] = 0;
                }
                $ciudades[$ciudad]++;
            }
        }

        // Ordenar por cantidad y tomar top 20
        arsort($ciudades);
        $topCiudades = array_slice($ciudades, 0, 20, true);

        // Preparar datos para tabla
        $datosCiudades = [];
        foreach ($topCiudades as $ciudad => $cantidad) {
            $datosCiudades[] = [
                'ciudad' => $ciudad,
                'cantidad_eventos' => $cantidad,
                'porcentaje' => count($megaEventos) > 0 
                    ? round(($cantidad / count($megaEventos)) * 100, 2)
                    : 0,
            ];
        }

        // Distribución por departamentos (extraer de ubicación)
        $departamentos = [];
        foreach ($megaEventos as $evento) {
            if ($evento->ubicacion) {
                // Intentar identificar departamento (última parte de la ubicación)
                $ubicacionParts = explode(',', $evento->ubicacion);
                $departamento = trim(end($ubicacionParts) ?? 'Sin especificar');
                
                if (!isset($departamentos[$departamento])) {
                    $departamentos[$departamento] = 0;
                }
                $departamentos[$departamento]++;
            }
        }

        arsort($departamentos);
        $datosDepartamentos = [];
        foreach ($departamentos as $departamento => $cantidad) {
            $datosDepartamentos[] = [
                'departamento' => $departamento,
                'cantidad_eventos' => $cantidad,
                'porcentaje' => count($megaEventos) > 0 
                    ? round(($cantidad / count($megaEventos)) * 100, 2)
                    : 0,
            ];
        }

        return [
            'ciudades' => $datosCiudades,
            'departamentos' => $datosDepartamentos,
            'total_eventos' => count($megaEventos),
            'filtros_aplicados' => $filtros,
        ];
    }

    /**
     * Reporte 5: Rendimiento por ONG
     * Ranking de ONGs por eventos creados, tasas de finalización, promedio de asistentes
     */
    public function getRendimientoOng(int $ongId, array $filtros = []): array
    {
        // Para este reporte, mostramos datos de la ONG actual y comparativa con otras
        $query = MegaEvento::where('ong_organizadora_principal', $ongId)
            ->select('mega_evento_id', 'titulo', 'estado', 'fecha_creacion', 'fecha_fin', 'capacidad_maxima');

        $query = $this->aplicarFiltros($query, $filtros);
        $megaEventos = $query->get();

        // Obtener nombre de la ONG actual
        $ongActual = DB::table('ongs')->where('user_id', $ongId)->first();
        $nombreOng = $ongActual->nombre ?? 'ONG Actual';

        // Métricas de la ONG actual
        $totalEventos = $megaEventos->count();
        $eventosFinalizados = $megaEventos->where('estado', 'finalizado')->count();
        $tasaFinalizacion = $totalEventos > 0 
            ? round(($eventosFinalizados / $totalEventos) * 100, 2)
            : 0;

        // Calcular promedio de asistentes (usando participantes)
        $totalAsistentes = DB::table('mega_evento_participantes_externos')
            ->whereIn('mega_evento_id', $megaEventos->pluck('mega_evento_id'))
            ->count();

        $promedioAsistentes = $totalEventos > 0 
            ? round($totalAsistentes / $totalEventos, 2)
            : 0;

        // Obtener datos de otras ONGs para comparación (top 10)
        $rankingOngs = DB::table('mega_eventos')
            ->join('ongs', 'mega_eventos.ong_organizadora_principal', '=', 'ongs.user_id')
            ->select(
                'ongs.user_id',
                'ongs.nombre',
                DB::raw('COUNT(mega_eventos.mega_evento_id) as total_eventos'),
                DB::raw('SUM(CASE WHEN mega_eventos.estado = \'finalizado\' THEN 1 ELSE 0 END) as eventos_finalizados')
            )
            ->groupBy('ongs.user_id', 'ongs.nombre')
            ->orderByDesc('total_eventos')
            ->limit(10)
            ->get()
            ->map(function ($ong) {
                $tasaFinalizacionOng = $ong->total_eventos > 0 
                    ? round(($ong->eventos_finalizados / $ong->total_eventos) * 100, 2)
                    : 0;

                return [
                    'ong_id' => $ong->user_id,
                    'nombre' => $ong->nombre,
                    'total_eventos' => $ong->total_eventos,
                    'eventos_finalizados' => $ong->eventos_finalizados,
                    'tasa_finalizacion' => $tasaFinalizacionOng,
                ];
            });

        // Encontrar posición de la ONG actual en el ranking
        $posicion = $rankingOngs->search(function ($ong) use ($ongId) {
            return $ong['ong_id'] == $ongId;
        });

        return [
            'ong_actual' => [
                'ong_id' => $ongId,
                'nombre' => $nombreOng,
                'total_eventos' => $totalEventos,
                'eventos_finalizados' => $eventosFinalizados,
                'tasa_finalizacion' => $tasaFinalizacion,
                'promedio_asistentes' => $promedioAsistentes,
                'posicion_ranking' => $posicion !== false ? $posicion + 1 : null,
            ],
            'ranking_ongs' => $rankingOngs,
            'filtros_aplicados' => $filtros,
        ];
    }

    /**
     * Aplicar filtros a una query usando query scopes
     * Reutilizable para todos los reportes
     */
    private function aplicarFiltros($query, array $filtros)
    {
        // Filtro por rango de fechas
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query->whereBetween('fecha_creacion', [
                $filtros['fecha_inicio'],
                $filtros['fecha_fin']
            ]);
        } elseif (isset($filtros['fecha_inicio'])) {
            $query->where('fecha_creacion', '>=', $filtros['fecha_inicio']);
        } elseif (isset($filtros['fecha_fin'])) {
            $query->where('fecha_creacion', '<=', $filtros['fecha_fin']);
        }

        // Filtro por categoría
        if (isset($filtros['categoria'])) {
            $query->where('categoria', $filtros['categoria']);
        }

        // Filtro por estado
        if (isset($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // Filtro por ubicación (búsqueda parcial)
        if (isset($filtros['ubicacion'])) {
            $query->where('ubicacion', 'LIKE', '%' . $filtros['ubicacion'] . '%');
        }

        // Filtro por ONG organizadora
        if (isset($filtros['ong_id'])) {
            $query->where('ong_organizadora_principal', $filtros['ong_id']);
        }

        // Filtro por rango de capacidad
        if (isset($filtros['capacidad_min'])) {
            $query->where('capacidad_maxima', '>=', $filtros['capacidad_min']);
        }

        if (isset($filtros['capacidad_max'])) {
            $query->where('capacidad_maxima', '<=', $filtros['capacidad_max']);
        }

        return $query;
    }

    /**
     * Obtener métricas completas de eventos regulares
     * Incluye totales, distribución por categoría/estado, participantes, tasas, etc.
     */
    public function getEventosMetrics(int $ongId, array $filtros = []): array
    {
        $query = Evento::where('ong_id', $ongId);
        $query = $this->aplicarFiltrosEventos($query, $filtros);

        // Obtener eventos con eager loading para optimizar
        $eventos = $query->with(['participantes'])->get();

        // Calcular métricas básicas
        $totalEventos = $eventos->count();
        $totalParticipantes = $eventos->sum(function($evento) {
            return $evento->participantes->where('estado', 'aprobada')->count();
        });

        // Distribución por categoría (tipo_evento)
        $distribucionCategoria = $eventos->groupBy('tipo_evento')
            ->map->count()
            ->toArray();

        // Distribución por estado
        $distribucionEstado = $eventos->groupBy('estado')
            ->map->count()
            ->toArray();

        // Eventos activos (en_curso basado en fechas)
        $eventosActivos = $eventos->filter(function($evento) {
            $now = now();
            return $evento->fecha_inicio && $evento->fecha_fin &&
                   $evento->fecha_inicio <= $now && $evento->fecha_fin >= $now;
        })->count();

        // Promedio de participantes por evento
        $promedioParticipantes = $totalEventos > 0 
            ? round($totalParticipantes / $totalEventos, 2) 
            : 0;

        // Tasa de ocupación promedio
        $tasasOcupacion = $eventos->map(function($evento) {
            if (!$evento->capacidad_maxima || $evento->capacidad_maxima == 0) {
                return null;
            }
            $participantes = $evento->participantes->where('estado', 'aprobada')->count();
            return round(($participantes / $evento->capacidad_maxima) * 100, 2);
        })->filter()->values();

        $tasaOcupacionPromedio = $tasasOcupacion->count() > 0
            ? round($tasasOcupacion->avg(), 2)
            : 0;

        // Top 5 eventos por participantes
        $topEventos = $eventos->map(function($evento) {
            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'participantes' => $evento->participantes->where('estado', 'aprobada')->count(),
                'capacidad' => $evento->capacidad_maxima ?? 0,
                'tasa_ocupacion' => $evento->capacidad_maxima && $evento->capacidad_maxima > 0
                    ? round(($evento->participantes->where('estado', 'aprobada')->count() / $evento->capacidad_maxima) * 100, 2)
                    : 0,
                'fecha_inicio' => $evento->fecha_inicio?->format('d/m/Y'),
            ];
        })->sortByDesc('participantes')->take(5)->values();

        // Tendencias temporales por mes
        $tendenciasMensuales = $eventos->groupBy(function($evento) {
            return $evento->fecha_inicio?->format('Y-m') ?? 'sin-fecha';
        })->map->count()->toArray();

        // Análisis geográfico (ciudades)
        $distribucionGeografica = $eventos->whereNotNull('ciudad')
            ->groupBy('ciudad')
            ->map->count()
            ->sortDesc()
            ->take(20)
            ->toArray();

        // Participación de empresas patrocinadoras
        $totalPatrocinios = $eventos->sum(function($evento) {
            return $evento->empresasParticipantes()->where('activo', true)->count();
        });

        // Duración promedio de eventos
        $duraciones = $eventos->filter(function($evento) {
            return $evento->fecha_inicio && $evento->fecha_fin;
        })->map(function($evento) {
            return $evento->fecha_inicio->diffInDays($evento->fecha_fin);
        });

        $duracionPromedio = $duraciones->count() > 0
            ? round($duraciones->avg(), 1)
            : 0;

        // Eventos públicos vs privados (basado en inscripcion_abierta)
        $eventosPublicos = $eventos->where('inscripcion_abierta', true)->count();
        $eventosPrivados = $eventos->where('inscripcion_abierta', false)->count();
        $totalParaDistribucion = $eventosPublicos + $eventosPrivados;
        
        $distribucionPublicoPrivado = [
            'publicos' => $eventosPublicos,
            'privados' => $eventosPrivados,
            'porcentaje_publicos' => $totalParaDistribucion > 0 
                ? round(($eventosPublicos / $totalParaDistribucion) * 100, 2) 
                : 0,
            'porcentaje_privados' => $totalParaDistribucion > 0 
                ? round(($eventosPrivados / $totalParaDistribucion) * 100, 2) 
                : 0,
        ];

        return [
            'total_eventos' => $totalEventos,
            'total_participantes' => $totalParticipantes,
            'eventos_activos' => $eventosActivos,
            'promedio_participantes' => $promedioParticipantes,
            'tasa_ocupacion_promedio' => $tasaOcupacionPromedio,
            'distribucion_categoria' => $distribucionCategoria,
            'distribucion_estado' => $distribucionEstado,
            'top_eventos' => $topEventos,
            'tendencias_mensuales' => $tendenciasMensuales,
            'distribucion_geografica' => $distribucionGeografica,
            'total_patrocinios' => $totalPatrocinios,
            'duracion_promedio' => $duracionPromedio,
            'distribucion_publico_privado' => $distribucionPublicoPrivado,
        ];
    }

    /**
     * Aplicar filtros a query de eventos regulares
     */
    private function aplicarFiltrosEventos($query, array $filtros)
    {
        // Filtro por rango de fechas
        if (isset($filtros['fecha_inicio'])) {
            $query->where('fecha_inicio', '>=', $filtros['fecha_inicio']);
        }
        if (isset($filtros['fecha_fin'])) {
            $query->where('fecha_inicio', '<=', $filtros['fecha_fin']);
        }

        // Filtro por categoría (tipo_evento)
        if (isset($filtros['categoria']) && $filtros['categoria'] !== '') {
            $query->where('tipo_evento', $filtros['categoria']);
        }

        // Filtro por estado
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $query->where('estado', $filtros['estado']);
        }

        // Filtro por ubicación/ciudad
        if (isset($filtros['ubicacion']) && $filtros['ubicacion'] !== '') {
            $query->where(function($q) use ($filtros) {
                $q->where('ciudad', 'LIKE', '%' . $filtros['ubicacion'] . '%')
                  ->orWhere('direccion', 'LIKE', '%' . $filtros['ubicacion'] . '%');
            });
        }

        // Filtro por ONG (solo para admins)
        if (isset($filtros['ong_id']) && $filtros['ong_id'] !== '') {
            $query->where('ong_id', $filtros['ong_id']);
        }

        // Filtro por rango de participantes (usando subquery)
        if (isset($filtros['participantes_min']) || isset($filtros['participantes_max'])) {
            $query->withCount(['participantes' => function($q) {
                $q->where('estado', 'aprobada');
            }]);

            if (isset($filtros['participantes_min'])) {
                $query->having('participantes_count', '>=', $filtros['participantes_min']);
            }
            if (isset($filtros['participantes_max'])) {
                $query->having('participantes_count', '<=', $filtros['participantes_max']);
            }
        }

        return $query;
    }

    /**
     * Obtener métricas consolidadas combinando eventos regulares y mega eventos
     */
    public function getConsolidadoMetrics(int $ongId, array $filtros = []): array
    {
        // Obtener métricas de eventos regulares
        $metricasEventos = $this->getEventosMetrics($ongId, $filtros);

        // Obtener métricas de mega eventos
        $metricasMegaEventos = $this->getMegaEventosMetrics($ongId, $filtros);

        // Calcular totales combinados
        $totalParticipantesGeneral = $metricasEventos['total_participantes'] + 
                                    ($metricasMegaEventos['total_participantes'] ?? 0);
        
        $totalEventosGeneral = $metricasEventos['total_eventos'] + 
                              ($metricasMegaEventos['total_mega_eventos'] ?? 0);

        // Comparativa lado a lado
        $comparativa = [
            'eventos' => [
                'total' => $metricasEventos['total_eventos'],
                'participantes' => $metricasEventos['total_participantes'],
                'activos' => $metricasEventos['eventos_activos'],
                'promedio_participantes' => $metricasEventos['promedio_participantes'],
                'tasa_ocupacion' => $metricasEventos['tasa_ocupacion_promedio'],
            ],
            'mega_eventos' => [
                'total' => $metricasMegaEventos['total_mega_eventos'] ?? 0,
                'participantes' => $metricasMegaEventos['total_participantes'] ?? 0,
                'activos' => $metricasMegaEventos['mega_eventos_activos'] ?? 0,
                'promedio_participantes' => $metricasMegaEventos['promedio_participantes'] ?? 0,
                'tasa_ocupacion' => $metricasMegaEventos['tasa_ocupacion_promedio'] ?? 0,
            ],
        ];

        // Determinar qué tipo tiene mejor rendimiento
        $mejorRendimiento = 'empate';
        if ($comparativa['eventos']['promedio_participantes'] > $comparativa['mega_eventos']['promedio_participantes']) {
            $mejorRendimiento = 'eventos';
        } elseif ($comparativa['mega_eventos']['promedio_participantes'] > $comparativa['eventos']['promedio_participantes']) {
            $mejorRendimiento = 'mega_eventos';
        }

        // Distribución porcentual
        $distribucionPorcentual = [
            'eventos' => $totalEventosGeneral > 0 
                ? round(($metricasEventos['total_eventos'] / $totalEventosGeneral) * 100, 2) 
                : 0,
            'mega_eventos' => $totalEventosGeneral > 0 
                ? round((($metricasMegaEventos['total_mega_eventos'] ?? 0) / $totalEventosGeneral) * 100, 2) 
                : 0,
        ];

        return [
            'total_participantes_general' => $totalParticipantesGeneral,
            'total_eventos_general' => $totalEventosGeneral,
            'comparativa' => $comparativa,
            'mejor_rendimiento' => $mejorRendimiento,
            'distribucion_porcentual' => $distribucionPorcentual,
            'metricas_eventos' => $metricasEventos,
            'metricas_mega_eventos' => $metricasMegaEventos,
        ];
    }

    /**
     * Obtener participantes agrupados por rango de fechas (mes o año)
     */
    public function getParticipantesByDateRange(int $ongId, string $tipo = 'eventos', string $agrupacion = 'mes', array $filtros = []): array
    {
        if ($tipo === 'eventos') {
            $query = Evento::where('ong_id', $ongId)
                ->withCount(['participantes' => function($q) {
                    $q->where('estado', 'aprobada');
                }]);
            
            $query = $this->aplicarFiltrosEventos($query, $filtros);
            $eventos = $query->get();

            $agrupado = $eventos->groupBy(function($evento) use ($agrupacion) {
                if ($agrupacion === 'mes') {
                    return $evento->fecha_inicio?->format('Y-m') ?? 'sin-fecha';
                } else {
                    return $evento->fecha_inicio?->format('Y') ?? 'sin-fecha';
                }
            })->map(function($grupo) {
                return $grupo->sum('participantes_count');
            })->toArray();
        } else {
            // Mega eventos
            $query = MegaEvento::where('ong_organizadora_principal', $ongId);
            $query = $this->aplicarFiltros($query, $filtros);
            $megaEventos = $query->get();

            // Contar participantes de mega eventos
            $agrupado = [];
            foreach ($megaEventos as $megaEvento) {
                $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('activo', true)
                    ->count();
                
                $participantesNoRegistrados = DB::table('mega_evento_participantes_no_registrados')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('estado', '!=', 'rechazada')
                    ->count();
                
                $total = $participantesRegistrados + $participantesNoRegistrados;
                
                $key = $agrupacion === 'mes' 
                    ? ($megaEvento->fecha_creacion?->format('Y-m') ?? 'sin-fecha')
                    : ($megaEvento->fecha_creacion?->format('Y') ?? 'sin-fecha');
                
                $agrupado[$key] = ($agrupado[$key] ?? 0) + $total;
            }
        }

        return $agrupado;
    }

    /**
     * Obtener top eventos por participantes
     */
    public function getTopEventosByParticipantes(int $ongId, int $limite = 5, array $filtros = []): array
    {
        $query = Evento::where('ong_id', $ongId)
            ->withCount(['participantes' => function($q) {
                $q->where('estado', 'aprobada');
            }]);
        
        $query = $this->aplicarFiltrosEventos($query, $filtros);
        
        $eventos = $query->orderByDesc('participantes_count')
            ->limit($limite)
            ->get();

        return $eventos->map(function($evento) {
            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'participantes' => $evento->participantes_count,
                'capacidad' => $evento->capacidad_maxima ?? 0,
                'tasa_ocupacion' => $evento->capacidad_maxima && $evento->capacidad_maxima > 0
                    ? round(($evento->participantes_count / $evento->capacidad_maxima) * 100, 2)
                    : 0,
                'fecha_inicio' => $evento->fecha_inicio?->format('d/m/Y'),
                'categoria' => $evento->tipo_evento,
            ];
        })->toArray();
    }

    /**
     * Obtener distribución por categoría
     */
    public function getCategoriaDistribution(int $ongId, string $tipo = 'eventos', array $filtros = []): array
    {
        if ($tipo === 'eventos') {
            $query = Evento::where('ong_id', $ongId);
            $query = $this->aplicarFiltrosEventos($query, $filtros);
            $eventos = $query->get();
            
            return $eventos->groupBy('tipo_evento')
                ->map->count()
                ->toArray();
        } else {
            $query = MegaEvento::where('ong_organizadora_principal', $ongId);
            $query = $this->aplicarFiltros($query, $filtros);
            $megaEventos = $query->get();
            
            return $megaEventos->groupBy('categoria')
                ->map->count()
                ->toArray();
        }
    }

    /**
     * Métodos de utilidad para formateo
     */
    public function formatNumber($number, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', '.');
    }

    public function formatCurrency($amount, string $currency = 'PYG'): string
    {
        return $currency . ' ' . number_format($amount, 0, ',', '.');
    }

    public function formatPercentage($value, int $decimals = 2): string
    {
        return number_format($value, $decimals, ',', '.') . '%';
    }

    /**
     * Obtener métricas de mega eventos (método mejorado)
     */
    public function getMegaEventosMetrics(int $ongId, array $filtros = []): array
    {
        $query = MegaEvento::where('ong_organizadora_principal', $ongId);
        $query = $this->aplicarFiltros($query, $filtros);
        $megaEventos = $query->get();

        $totalMegaEventos = $megaEventos->count();
        $totalParticipantes = 0;
        $megaEventosActivos = 0;

        // Calcular participantes y estados
        foreach ($megaEventos as $megaEvento) {
            // Participantes registrados
            $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                ->where('mega_evento_id', $megaEvento->mega_evento_id)
                ->where('activo', true)
                ->count();
            
            // Participantes no registrados
            $participantesNoRegistrados = DB::table('mega_evento_participantes_no_registrados')
                ->where('mega_evento_id', $megaEvento->mega_evento_id)
                ->where('estado', '!=', 'rechazada')
                ->count();
            
            $totalParticipantes += ($participantesRegistrados + $participantesNoRegistrados);

            // Contar activos
            if ($megaEvento->estado === 'activo' || $megaEvento->estado === 'en_curso') {
                $megaEventosActivos++;
            }
        }

        // Distribución por categoría
        $distribucionCategoria = $megaEventos->groupBy('categoria')
            ->map->count()
            ->toArray();

        // Distribución por estado
        $distribucionEstado = $megaEventos->groupBy('estado')
            ->map->count()
            ->toArray();

        // Promedio de participantes
        $promedioParticipantes = $totalMegaEventos > 0 
            ? round($totalParticipantes / $totalMegaEventos, 2) 
            : 0;

        // Tasa de ocupación promedio
        $tasasOcupacion = [];
        foreach ($megaEventos as $megaEvento) {
            if ($megaEvento->capacidad_maxima && $megaEvento->capacidad_maxima > 0) {
                $participantesRegistrados = DB::table('mega_evento_participantes_externos')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('activo', true)
                    ->count();
                
                $participantesNoRegistrados = DB::table('mega_evento_participantes_no_registrados')
                    ->where('mega_evento_id', $megaEvento->mega_evento_id)
                    ->where('estado', '!=', 'rechazada')
                    ->count();
                
                $total = $participantesRegistrados + $participantesNoRegistrados;
                $tasasOcupacion[] = round(($total / $megaEvento->capacidad_maxima) * 100, 2);
            }
        }

        $tasaOcupacionPromedio = count($tasasOcupacion) > 0
            ? round(array_sum($tasasOcupacion) / count($tasasOcupacion), 2)
            : 0;

        return [
            'total_mega_eventos' => $totalMegaEventos,
            'total_participantes' => $totalParticipantes,
            'mega_eventos_activos' => $megaEventosActivos,
            'promedio_participantes' => $promedioParticipantes,
            'tasa_ocupacion_promedio' => $tasaOcupacionPromedio,
            'distribucion_categoria' => $distribucionCategoria,
            'distribucion_estado' => $distribucionEstado,
        ];
    }
}

