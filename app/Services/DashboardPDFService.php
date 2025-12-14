<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\EventoParticipacion;
use App\Models\EventoReaccion;
use App\Models\EventoCompartido;
use App\Models\Ong;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardPDFService
{
    /**
     * Generar PDF del dashboard de ONG
     */
    public static function generarPDFOng($ongId, $fechaInicio = null, $fechaFin = null)
    {
        Log::info('Iniciando generación PDF ONG', ['ong_id' => $ongId]);
        
        // Aumentar límites de memoria y tiempo
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        // Cargar ONG
        $ong = Ong::with(['usuario'])->find($ongId);
        if (!$ong) {
            $ong = Ong::where('user_id', $ongId)->with(['usuario'])->first();
        }
        
        if (!$ong) {
            throw new \Exception('ONG no encontrada');
        }

        // Fechas por defecto
        if (!$fechaInicio) {
            $fechaInicio = Carbon::now()->subMonths(6);
        }
        if (!$fechaFin) {
            $fechaFin = Carbon::now();
        }

        $fecha_generacion = Carbon::now()->setTimezone('America/La_Paz');
        $isPostgreSQL = DB::getDriverName() === 'pgsql';
        $dateFormat = $isPostgreSQL ? "TO_CHAR(created_at, 'YYYY-MM')" : "DATE_FORMAT(created_at, '%Y-%m')";

        // Obtener estadísticas de la ONG
        $datos = self::obtenerDatosOng($ongId, $fechaInicio, $fechaFin, $dateFormat, $isPostgreSQL);
        
        // Obtener folio
        $numeroExportacion = DB::table('ong_exportaciones_pdf')
            ->where('ong_id', $ongId)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;
        $folio = 'DASH-' . str_pad($numeroExportacion, 6, '0', STR_PAD_LEFT);

        // Registrar exportación
        try {
            DB::table('ong_exportaciones_pdf')->insert([
                'ong_id' => $ongId,
                'tipo' => 'pdf',
                'fecha_inicio' => $fechaInicio->toDateString(),
                'fecha_fin' => $fechaFin->toDateString(),
                'numero_exportacion' => $numeroExportacion,
                'folio' => $folio,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo registrar exportación PDF: ' . $e->getMessage());
        }

        // Generar gráficas
        $graficas = self::generarGraficasOng($datos, $ong);

        // Preparar variables para vista
        $variablesParaVista = [
            'ong' => $ong,
            'datos' => $datos,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'fecha_generacion' => $fecha_generacion,
            'folio' => $folio,
            'numero_exportacion' => $numeroExportacion,
            'grafica_tendencias' => $graficas['tendencias'],
            'grafica_distribucion' => $graficas['distribucion'],
            'grafica_comparativa' => $graficas['comparativa'],
            'grafica_actividad_semanal' => $graficas['actividad_semanal']
        ];

        Log::info('Datos obtenidos para PDF ONG', ['datos_keys' => array_keys($datos)]);
        
        return self::generarPDF($variablesParaVista, 'pdf.dashboard-ong');
    }

    /**
     * Generar PDF del dashboard de evento específico
     */
    public static function generarPDFEvento($eventoId)
    {
        Log::info('Iniciando generación PDF evento', ['evento_id' => $eventoId]);

        // Validar ID
        if (!is_numeric($eventoId)) {
            throw new \Exception('ID inválido', 400);
        }

        // Validar que el evento existe
        if (!Evento::where('id', $eventoId)->exists()) {
            throw new \Exception('Evento no encontrado', 404);
        }

        // Aumentar límites
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        // Obtener evento con ONG
        $evento = Evento::with('ong')->find($eventoId);
        if (!$evento) {
            throw new \Exception('Evento no encontrado', 404);
        }

        $ong = $evento->ong;
        if (!$ong) {
            $ong = Ong::where('user_id', $evento->ong_id)->with(['usuario'])->first();
        }
        
        if (!$ong) {
            throw new \Exception('ONG no encontrada para este evento', 404);
        }

        $fecha_generacion = Carbon::now()->setTimezone('America/La_Paz');
        $isPostgreSQL = DB::getDriverName() === 'pgsql';
        $dateFormat = $isPostgreSQL ? "TO_CHAR(created_at, 'YYYY-MM')" : "DATE_FORMAT(created_at, '%Y-%m')";

        // Obtener estadísticas del evento
        $datos = self::obtenerDatosEvento($eventoId, $evento, $dateFormat, $isPostgreSQL);

        // Obtener folio
        $fechaHoy = now()->toDateString();
        $numeroExportacion = 1;
        $folio = 'EVT-' . str_pad($eventoId, 4, '0', STR_PAD_LEFT) . '-' . now()->format('YmdHis');
        
        try {
            if (DB::getSchemaBuilder()->hasTable('ong_exportaciones_pdf')) {
                if ($isPostgreSQL) {
                    $numeroExportacion = DB::table('ong_exportaciones_pdf')
                        ->where('ong_id', $evento->ong_id)
                        ->whereRaw("DATE(created_at) = ?", [$fechaHoy])
                        ->count() + 1;
                } else {
                    $numeroExportacion = DB::table('ong_exportaciones_pdf')
                        ->where('ong_id', $evento->ong_id)
                        ->whereDate('created_at', $fechaHoy)
                        ->count() + 1;
                }
                $folio = 'EVT-' . str_pad($eventoId, 4, '0', STR_PAD_LEFT) . '-' . str_pad($numeroExportacion, 4, '0', STR_PAD_LEFT);

                try {
                    DB::table('ong_exportaciones_pdf')->insert([
                        'ong_id' => $evento->ong_id,
                        'tipo' => 'pdf',
                        'fecha_inicio' => $evento->fecha_inicio ? Carbon::parse($evento->fecha_inicio)->subDays(30)->toDateString() : now()->subDays(30)->toDateString(),
                        'fecha_fin' => now()->toDateString(),
                        'numero_exportacion' => $numeroExportacion,
                        'folio' => $folio,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo registrar exportación: ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Error al obtener número de exportación: ' . $e->getMessage());
        }

        // Generar gráficas
        $graficas = self::generarGraficasEvento($datos, $evento);

        // Preparar variables para vista
        $variablesParaVista = [
            'ong' => $ong,
            'evento' => $evento,
            'datos' => $datos,
            'fecha_inicio' => $evento->fecha_inicio ? Carbon::parse($evento->fecha_inicio)->subDays(30) : Carbon::now()->subDays(30),
            'fecha_fin' => Carbon::now(),
            'fecha_generacion' => $fecha_generacion,
            'folio' => $folio,
            'numero_exportacion' => $numeroExportacion,
            'grafica_tendencias' => $graficas['tendencias'],
            'grafica_distribucion' => $graficas['distribucion'],
            'grafica_comparativa' => $graficas['comparativa'],
            'grafica_actividad_semanal' => $graficas['actividad_semanal']
        ];

        Log::info('Datos obtenidos para PDF Evento', ['datos_keys' => array_keys($datos)]);
        
        return self::generarPDF($variablesParaVista, 'pdf.dashboard-ong');
    }

    /**
     * Método privado compartido para generar PDF
     */
    private static function generarPDF($variablesParaVista, $vista)
    {
        Log::info('Generando PDF con vista', ['vista' => $vista]);

        $pdf = Pdf::loadView($vista, $variablesParaVista);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'enable-local-file-access' => true,
            'defaultFont' => 'Arial'
        ]);

        Log::info('PDF generado exitosamente');
        
        return $pdf;
    }

    /**
     * Obtener datos de ONG
     */
    private static function obtenerDatosOng($ongId, $fechaInicio, $fechaFin, $dateFormat, $isPostgreSQL)
    {
        // Eventos activos
        $eventosActivos = Evento::where('ong_id', $ongId)
            ->where(function($q) {
                $q->where('estado', 'activo')
                  ->orWhere('estado', 'Publicado')
                  ->orWhere('estado', 'publicado');
            })
            ->count();

        // Total reacciones
        $totalReacciones = DB::table('evento_reacciones')
            ->join('eventos', 'evento_reacciones.evento_id', '=', 'eventos.id')
            ->where('eventos.ong_id', $ongId)
            ->count();

        // Total compartidos
        $totalCompartidos = DB::table('evento_compartidos')
            ->join('eventos', 'evento_compartidos.evento_id', '=', 'eventos.id')
            ->where('eventos.ong_id', $ongId)
            ->count();

        // Total voluntarios
        $totalVoluntarios = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
            $q->where('ong_id', $ongId);
        })
        ->whereNotNull('externo_id')
        ->distinct('externo_id')
        ->count('externo_id');

        // Total participantes
        $totalParticipantes = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
            $q->where('ong_id', $ongId);
        })->count();

        // Eventos finalizados
        $eventosFinalizados = Evento::where('ong_id', $ongId)
            ->where('estado', 'finalizado')
            ->count();

        // Tendencias mensuales
        $tendenciasMensuales = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
            $q->where('ong_id', $ongId);
        })
        ->select(DB::raw("{$dateFormat} as mes"), DB::raw('COUNT(*) as total'))
        ->groupBy(DB::raw($dateFormat))
        ->orderBy('mes', 'desc')
        ->limit(12)
        ->get()
        ->pluck('total', 'mes')
        ->toArray();

        // Distribución de estados
        $eventosPorEstado = Evento::where('ong_id', $ongId)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        // Top 8 eventos
        $top8Eventos = Evento::where('ong_id', $ongId)
            ->withCount(['reacciones', 'compartidos', 'participantes'])
            ->get()
            ->map(function($evento) {
                $evento->participaciones_count = $evento->participantes_count ?? 0;
                $evento->engagement = ($evento->reacciones_count ?? 0) + 
                                     ($evento->compartidos_count ?? 0) + 
                                     ($evento->participantes_count ?? 0);
                return $evento;
            })
            ->sortByDesc('engagement')
            ->take(8)
            ->values();

        // Actividad semanal
        $actividadSemanal = [];
        for ($i = 7; $i >= 0; $i--) {
            $semanaInicio = Carbon::now()->subWeeks($i)->startOfWeek();
            $semanaFin = Carbon::now()->subWeeks($i)->endOfWeek();
            $semanaLabel = $semanaInicio->format('d/m') . '-' . $semanaFin->format('d/m');
            
            $actividadSemanal[$semanaLabel] = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })
            ->whereBetween('created_at', [$semanaInicio, $semanaFin])
            ->count();
        }

        // Actividad reciente
        $actividadReciente = [];
        for ($i = 19; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $fechaFormato = Carbon::now()->subDays($i)->format('d/m/Y');
            
            $reacciones = EventoReaccion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->whereDate('created_at', $fecha)->count();
            
            $compartidos = EventoCompartido::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->whereDate('created_at', $fecha)->count();
            
            $inscripciones = EventoParticipacion::whereHas('evento', function($q) use ($ongId) {
                $q->where('ong_id', $ongId);
            })->whereDate('created_at', $fecha)->count();
            
            $actividadReciente[] = [
                'fecha' => $fechaFormato,
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
                'inscripciones' => $inscripciones,
                'total' => $reacciones + $compartidos + $inscripciones
            ];
        }

        // Top 10 eventos
        $topEventos = Evento::where('ong_id', $ongId)
            ->withCount(['reacciones', 'compartidos', 'participantes'])
            ->get()
            ->map(function($evento) {
                $evento->participaciones_count = $evento->participantes_count ?? 0;
                $evento->engagement = ($evento->reacciones_count ?? 0) + 
                                     ($evento->compartidos_count ?? 0) + 
                                     ($evento->participantes_count ?? 0);
                return $evento;
            })
            ->sortByDesc('engagement')
            ->take(10)
            ->values();

        // Top 10 voluntarios
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
            $user = User::find($item->externo_id);
            if ($user) {
                $user->participaciones_count = $item->participaciones_count;
                $user->horas_contribuidas = $item->participaciones_count * 2;
                return $user;
            }
            return null;
        })
        ->filter();

        return [
            'eventos_activos' => $eventosActivos,
            'total_reacciones' => $totalReacciones,
            'total_compartidos' => $totalCompartidos,
            'total_voluntarios' => $totalVoluntarios,
            'total_participantes' => $totalParticipantes,
            'eventos_finalizados' => $eventosFinalizados,
            'top_eventos' => $topEventos,
            'top_voluntarios' => $topVoluntarios,
            'tendencias_mensuales' => $tendenciasMensuales,
            'actividad_reciente' => $actividadReciente,
            'actividad_semanal' => $actividadSemanal,
            'distribucion_estados' => $eventosPorEstado,
            'top8Eventos' => $top8Eventos
        ];
    }

    /**
     * Obtener datos de evento específico
     */
    private static function obtenerDatosEvento($eventoId, $evento, $dateFormat, $isPostgreSQL)
    {
        // Query optimizado para obtener datos del evento en una sola consulta
        $eventoData = DB::table('eventos as e')
            ->leftJoin('ongs as o', 'e.ong_id', '=', 'o.user_id')
            ->where('e.id', $eventoId)
            ->select(
                'e.*',
                'o.nombre_ong',
                DB::raw('(SELECT COUNT(*) FROM evento_participaciones WHERE evento_id = e.id) as total_participantes'),
                DB::raw('(SELECT COUNT(*) FROM evento_participaciones WHERE evento_id = e.id AND externo_id IS NOT NULL) as total_voluntarios'),
                DB::raw('(SELECT COUNT(*) FROM evento_reacciones WHERE evento_id = e.id) as total_reacciones'),
                DB::raw('(SELECT COUNT(*) FROM evento_compartidos WHERE evento_id = e.id) as total_compartidos'),
                DB::raw('(SELECT COUNT(DISTINCT externo_id) FROM evento_participaciones WHERE evento_id = e.id AND externo_id IS NOT NULL) as voluntarios_unicos')
            )
            ->first();

        if (!$eventoData) {
            throw new \Exception('Error al obtener datos del evento', 500);
        }

        $eventosActivos = (in_array($evento->estado, ['activo', 'Publicado', 'publicado']) && 
                          (!$evento->fecha_fin || Carbon::parse($evento->fecha_fin)->isFuture())) ? 1 : 0;
        
        $eventosFinalizados = ($evento->estado === 'finalizado') ? 1 : 0;

        // Tendencias mensuales del evento
        $tendenciasMensuales = EventoParticipacion::where('evento_id', $eventoId)
            ->select(DB::raw("{$dateFormat} as mes"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw($dateFormat))
            ->orderBy('mes', 'desc')
            ->limit(12)
            ->get()
            ->pluck('total', 'mes')
            ->toArray();

        // Distribución de estados (solo este evento)
        $eventosPorEstado = [$evento->estado => 1];

        // Top evento (solo este)
        $eventoConConteos = Evento::where('id', $eventoId)
            ->withCount(['reacciones', 'compartidos', 'participantes'])
            ->first();
        
        if ($eventoConConteos) {
            $eventoConConteos->participaciones_count = $eventoConConteos->participantes_count ?? 0;
            $eventoConConteos->engagement = ($eventoConConteos->reacciones_count ?? 0) + 
                                            ($eventoConConteos->compartidos_count ?? 0) + 
                                            ($eventoConConteos->participantes_count ?? 0);
        }

        $top8Eventos = collect([$eventoConConteos])->filter();
        $topEventos = collect([$eventoConConteos])->filter()->take(10);

        // Actividad semanal
        $actividadSemanal = [];
        for ($i = 7; $i >= 0; $i--) {
            $semanaInicio = Carbon::now()->subWeeks($i)->startOfWeek();
            $semanaFin = Carbon::now()->subWeeks($i)->endOfWeek();
            $semanaLabel = $semanaInicio->format('d/m') . '-' . $semanaFin->format('d/m');
            
            $actividadSemanal[$semanaLabel] = EventoParticipacion::where('evento_id', $eventoId)
                ->whereBetween('created_at', [$semanaInicio, $semanaFin])
                ->count();
        }

        // Actividad reciente
        $actividadReciente = [];
        for ($i = 19; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $fechaFormato = Carbon::now()->subDays($i)->format('d/m/Y');
            
            $reacciones = EventoReaccion::where('evento_id', $eventoId)
                ->whereDate('created_at', $fecha)->count();
            
            $compartidos = EventoCompartido::where('evento_id', $eventoId)
                ->whereDate('created_at', $fecha)->count();
            
            $inscripciones = EventoParticipacion::where('evento_id', $eventoId)
                ->whereDate('created_at', $fecha)->count();
            
            $actividadReciente[] = [
                'fecha' => $fechaFormato,
                'reacciones' => $reacciones,
                'compartidos' => $compartidos,
                'inscripciones' => $inscripciones,
                'total' => $reacciones + $compartidos + $inscripciones
            ];
        }

        // Top voluntarios del evento
        $topVoluntarios = EventoParticipacion::where('evento_id', $eventoId)
            ->whereNotNull('externo_id')
            ->select('externo_id', DB::raw('COUNT(*) as participaciones_count'))
            ->groupBy('externo_id')
            ->orderByDesc('participaciones_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $user = User::find($item->externo_id);
                if ($user) {
                    $user->participaciones_count = $item->participaciones_count;
                    $user->horas_contribuidas = $item->participaciones_count * 2;
                    return $user;
                }
                return null;
            })
            ->filter();

        return [
            'eventos_activos' => $eventosActivos,
            'total_reacciones' => $eventoData->total_reacciones ?? 0,
            'total_compartidos' => $eventoData->total_compartidos ?? 0,
            'total_voluntarios' => $eventoData->voluntarios_unicos ?? 0,
            'total_participantes' => $eventoData->total_participantes ?? 0,
            'eventos_finalizados' => $eventosFinalizados,
            'top_eventos' => $topEventos,
            'top_voluntarios' => $topVoluntarios,
            'tendencias_mensuales' => $tendenciasMensuales,
            'actividad_reciente' => $actividadReciente,
            'actividad_semanal' => $actividadSemanal,
            'distribucion_estados' => $eventosPorEstado,
            'top8Eventos' => $top8Eventos
        ];
    }

    /**
     * Generar URLs de gráficas para ONG
     */
    private static function generarGraficasOng($datos, $ong)
    {
        // Tendencias
        $tendenciasLabels = array_keys($datos['tendencias_mensuales']);
        $tendenciasData = array_values($datos['tendencias_mensuales']);
        if (empty($tendenciasLabels)) {
            $tendenciasLabels = ['Sin datos'];
            $tendenciasData = [0];
        }
        
        $grafica_tendencias = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
            'type' => 'line',
            'data' => [
                'labels' => $tendenciasLabels,
                'datasets' => [[
                    'label' => 'Participantes',
                    'data' => $tendenciasData,
                    'borderColor' => '#00A36C',
                    'backgroundColor' => 'rgba(0,163,108,0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Tendencias Mensuales de Participantes', 'font' => ['size' => 14]]
                ],
                'scales' => ['y' => ['beginAtZero' => true]]
            ]
        ])) . '&width=680&height=300&backgroundColor=white&devicePixelRatio=2';

        // Distribución
        $estadosLabels = array_keys($datos['distribucion_estados']);
        $estadosData = array_values($datos['distribucion_estados']);
        if (empty($estadosLabels)) {
            $estadosLabels = ['Sin datos'];
            $estadosData = [1];
        }
        
        $grafica_distribucion = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
            'type' => 'doughnut',
            'data' => [
                'labels' => $estadosLabels,
                'datasets' => [[
                    'data' => $estadosData,
                    'backgroundColor' => ['#00A36C', '#0C2B44', '#dc3545', '#17a2b8', '#ffc107']
                ]]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Distribución de Estados de Eventos', 'font' => ['size' => 14]],
                    'legend' => ['display' => true, 'position' => 'right']
                ]
            ]
        ])) . '&width=680&height=300&backgroundColor=white&devicePixelRatio=2';

        // Comparativa
        $top8Labels = $datos['top8Eventos']->pluck('titulo')->map(function($titulo) {
            return Str::limit($titulo, 20);
        })->toArray();
        $top8Reacciones = $datos['top8Eventos']->pluck('reacciones_count')->toArray();
        $top8Compartidos = $datos['top8Eventos']->pluck('compartidos_count')->toArray();
        
        if (empty($top8Labels)) {
            $top8Labels = ['Sin datos'];
            $top8Reacciones = [0];
            $top8Compartidos = [0];
        }
        
        $grafica_comparativa = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $top8Labels,
                'datasets' => [
                    [
                        'label' => 'Reacciones',
                        'data' => $top8Reacciones,
                        'backgroundColor' => '#dc3545'
                    ],
                    [
                        'label' => 'Compartidos',
                        'data' => $top8Compartidos,
                        'backgroundColor' => '#00A36C'
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => ['display' => true, 'text' => 'Comparativa Top 8 Eventos', 'font' => ['size' => 14]],
                    'legend' => ['display' => true, 'position' => 'top']
                ],
                'scales' => ['y' => ['beginAtZero' => true]]
            ]
        ])) . '&width=680&height=300&backgroundColor=white&devicePixelRatio=2';

        // Actividad semanal - usar actividad_semanal de datos si está disponible
        if (isset($datos['actividad_semanal']) && is_array($datos['actividad_semanal']) && !empty($datos['actividad_semanal'])) {
            $semanalLabels = array_keys($datos['actividad_semanal']);
            $semanalData = array_values($datos['actividad_semanal']);
        } else {
            // Fallback: calcular desde actividad_reciente
            $semanalLabels = [];
            $semanalData = [];
            for ($i = 7; $i >= 0; $i--) {
                $semanaInicio = Carbon::now()->subWeeks($i)->startOfWeek();
                $semanaFin = Carbon::now()->subWeeks($i)->endOfWeek();
                $semanaLabel = $semanaInicio->format('d/m') . '-' . $semanaFin->format('d/m');
                
                $total = 0;
                if (isset($datos['actividad_reciente']) && is_array($datos['actividad_reciente'])) {
                    foreach ($datos['actividad_reciente'] as $act) {
                        try {
                            if (isset($act['fecha'])) {
                                $fechaAct = Carbon::createFromFormat('d/m/Y', $act['fecha']);
                                if ($fechaAct->between($semanaInicio, $semanaFin)) {
                                    $total += $act['total'] ?? 0;
                                }
                            }
                        } catch (\Exception $e) {
                            // Ignorar errores de parseo de fecha
                        }
                    }
                }
                
                $semanalLabels[] = $semanaLabel;
                $semanalData[] = $total;
            }
        }
        
        if (empty($semanalLabels)) {
            $semanalLabels = ['Sin datos'];
            $semanalData = [0];
        }

        $grafica_actividad_semanal = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
            'type' => 'line',
            'data' => [
                'labels' => $semanalLabels,
                'datasets' => [[
                    'label' => 'Actividad Semanal',
                    'data' => $semanalData,
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
                    'title' => ['display' => true, 'text' => 'Actividad Semanal Agregada', 'font' => ['size' => 14]]
                ],
                'scales' => ['y' => ['beginAtZero' => true]]
            ]
        ])) . '&width=680&height=300&backgroundColor=white&devicePixelRatio=2';

        return [
            'tendencias' => $grafica_tendencias,
            'distribucion' => $grafica_distribucion,
            'comparativa' => $grafica_comparativa,
            'actividad_semanal' => $grafica_actividad_semanal
        ];
    }

    /**
     * Generar URLs de gráficas para evento
     */
    private static function generarGraficasEvento($datos, $evento)
    {
        // Reutilizar la misma lógica pero adaptada
        return self::generarGraficasOng($datos, $evento->ong);
    }
}