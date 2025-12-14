<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Clase principal para exportar Dashboard ONG a Excel
 * Diseño Power BI profesional con 10 hojas
 * 
 * @package App\Exports
 * @author UNI2 Analytics Platform
 * @version 1.0
 * 
 * Hojas incluidas:
 * 1. Portada - Información general y folio
 * 2. Resumen Ejecutivo - KPIs principales con comparativas
 * 3. Métricas Principales - Análisis detallado de métricas
 * 4. Top Eventos - Ranking de eventos por engagement
 * 5. Top Voluntarios - Hall of Fame de voluntarios
 * 6. Tendencias Temporales - Análisis de series de tiempo
 * 7. Distribución Estados - Distribución con semáforo visual
 * 8. Listado Completo - Tabla filtrable de todos los eventos
 * 9. Análisis Comparativo - Comparación período actual vs anterior
 * 10. Alertas - Sistema de monitoreo y recomendaciones
 */
class OngDashboardExport implements WithMultipleSheets
{
    /** @var \App\Models\Ong Instancia del modelo ONG */
    protected $ong;
    
    /** @var array Datos del dashboard obtenidos del controlador */
    protected $datos;
    
    /** @var \Carbon\Carbon Fecha de inicio del período analizado */
    protected $fechaInicio;
    
    /** @var \Carbon\Carbon Fecha de fin del período analizado */
    protected $fechaFin;
    
    /** @var int Número de exportación del día (para folio único) */
    protected $numeroExportacion;

    /**
     * Constructor de la clase
     * 
     * @param \App\Models\Ong $ong Instancia del modelo ONG
     * @param array $datos Datos del dashboard (métricas, tendencias, etc.)
     * @param \Carbon\Carbon $fechaInicio Fecha de inicio del período
     * @param \Carbon\Carbon $fechaFin Fecha de fin del período
     * @param int $numeroExportacion Número de exportación del día (default: 1)
     */
    public function __construct($ong, $datos, $fechaInicio, $fechaFin, $numeroExportacion = 1)
    {
        $this->ong = $ong;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->numeroExportacion = $numeroExportacion;
    }

    /**
     * Retorna array con todas las hojas del Excel
     * 
     * @return array Array de instancias de clases Sheet
     */
    public function sheets(): array
    {
        return [
            new PortadaSheet($this->ong, $this->fechaInicio, $this->fechaFin, $this->numeroExportacion),
            new ResumenEjecutivoSheet($this->ong, $this->datos, $this->fechaInicio, $this->fechaFin),
            new MetricasPrincipalesSheet($this->datos),
            new TopEventosSheet($this->datos),
            new TopVoluntariosSheet($this->datos),
            new TendenciasTemporalesSheet($this->datos),
            new DistribucionEstadosSheet($this->datos),
            new ListadoCompletaEventosSheet($this->datos),
            new AnalisisComparativoSheet($this->datos),
            new AlertasSheet($this->datos),
        ];
    }
}

/**
 * HOJA 1: PORTADA
 * Diseño profesional Power BI
 */
class PortadaSheet implements FromCollection, WithStyles, WithTitle, WithColumnWidths
{
    protected $ong;
    protected $fechaInicio;
    protected $fechaFin;
    protected $numeroExportacion;

    public function __construct($ong, $fechaInicio, $fechaFin, $numeroExportacion)
    {
        $this->ong = $ong;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->numeroExportacion = $numeroExportacion;
    }

    public function collection()
    {
        $data = [];
        
        // Filas 1-3: vacías (espaciado superior)
        for ($i = 1; $i <= 3; $i++) {
            $data[] = ['', ''];
        }
        
        // Fila 4: Título principal
        $data[] = ['DASHBOARD ANALÍTICO', ''];
        
        // Fila 5: Subtítulo
        $data[] = ['REPORTE DE GESTIÓN ONG', ''];
        
        // Filas 6-7: vacías
        $data[] = ['', ''];
        $data[] = ['', ''];
        
        // Fila 8: Organización
        $data[] = ['Organización:', $this->ong->nombre_ong ?? 'ONG'];
        
        // Fila 9: vacía
        $data[] = ['', ''];
        
        // Fila 10: Número de Folio
        $folio = 'DASH-' . str_pad($this->numeroExportacion, 6, '0', STR_PAD_LEFT);
        $data[] = ['Número de Folio:', $folio];
        
        // Fila 11: vacía
        $data[] = ['', ''];
        
        // Fila 12: Período Analizado
        $periodo = $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y');
        $data[] = ['Período Analizado:', $periodo];
        
        // Fila 13: vacía
        $data[] = ['', ''];
        
        // Fila 14: Fecha de Generación
        $data[] = ['Fecha de Generación:', Carbon::now()->format('d/m/Y H:i:s')];
        
        // Filas 15-18: vacías
        for ($i = 15; $i <= 18; $i++) {
            $data[] = ['', ''];
        }
        
        // Fila 19: CONFIDENCIAL
        $data[] = ['CONFIDENCIAL', ''];
        
        // Fila 20: Mensaje confidencial
        $data[] = ['Este documento contiene información privilegiada', ''];
        
        // Filas 21-22: vacías
        $data[] = ['', ''];
        $data[] = ['', ''];
        
        // Fila 23: Footer
        $data[] = ['Powered by UNI2 Analytics Platform', ''];
        
        // Completar hasta fila 30
        for ($i = 24; $i <= 30; $i++) {
            $data[] = ['', ''];
        }
        
        return new Collection($data);
    }

    public function title(): string
    {
        return 'Portada';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 50
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Fondo azul oscuro para todo el rango A1:B30
        $sheet->getStyle('A1:B30')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ]
        ]);

        // Título principal (A4:B4)
        $sheet->mergeCells('A4:B4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 32,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Subtítulo (A5:B5)
        $sheet->mergeCells('A5:B5');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 24,
                'color' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Información general (A8:B8, A10:B10, A12:B12, A14:B14)
        foreach ([8, 10, 12, 14] as $row) {
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'font' => [
                    'size' => 14,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]);
            // Etiquetas en verde bold
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '00A36C']
                ]
            ]);
        }

        // CONFIDENCIAL (A19:B20)
        $sheet->mergeCells('A19:B20');
        $sheet->getStyle('A19')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => 'FF6B6B']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Footer (A23:B23)
        $sheet->mergeCells('A23:B23');
        $sheet->getStyle('A23')->applyFromArray([
            'font' => [
                'size' => 9,
                'color' => ['rgb' => 'AAAAAA']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        return [];
    }
}

/**
 * HOJA 2: RESUMEN EJECUTIVO
 * KPIs principales con comparativas y fórmulas
 */
class ResumenEjecutivoSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $ong;
    protected $datos;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($ong, $datos, $fechaInicio, $fechaFin)
    {
        $this->ong = $ong;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function array(): array
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return [
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ];
        }
        
        $metricas = $this->datos['metricas'] ?? [];
        $comparativas = $this->datos['comparativas'] ?? [];
        
        $data = [];
        
        // SECCIÓN 1 - Información General (filas 1-3)
        $data[] = ['INFORMACIÓN GENERAL', '', '', ''];
        $data[] = ['Organización:', $this->ong->nombre_ong ?? 'ONG', '', ''];
        $periodo = $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y');
        $data[] = ['Período:', $periodo, '', ''];
        $data[] = ['', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - KPIs Principales (filas 5-10)
        $data[] = ['KPIs PRINCIPALES', 'VALOR ACTUAL', 'PERÍODO ANTERIOR', 'VARIACIÓN'];
        
        // Eventos Activos
        $eventosActivos = $metricas['eventos_activos'] ?? 0;
        $eventosActivosAnterior = $comparativas['eventos_activos']['anterior'] ?? 0;
        $data[] = [
                'Eventos Activos',
            $eventosActivos,
            $eventosActivosAnterior,
            $this->formatearVariacion($comparativas['eventos_activos'] ?? [])
        ];
        
        // Total Reacciones
        $totalReacciones = $metricas['total_reacciones'] ?? 0;
        $reaccionesAnterior = $comparativas['reacciones']['anterior'] ?? 0;
        $data[] = [
            'Total Reacciones',
            $totalReacciones,
            $reaccionesAnterior,
            $this->formatearVariacion($comparativas['reacciones'] ?? [])
        ];
        
        // Total Compartidos
        $totalCompartidos = $metricas['total_compartidos'] ?? 0;
        $compartidosAnterior = $comparativas['compartidos']['anterior'] ?? 0;
        $data[] = [
            'Total Compartidos',
            $totalCompartidos,
            $compartidosAnterior,
            $this->formatearVariacion($comparativas['compartidos'] ?? [])
        ];
        
        // Total Voluntarios
        $totalVoluntarios = $metricas['total_voluntarios'] ?? 0;
        $voluntariosAnterior = $comparativas['voluntarios']['anterior'] ?? 0;
        $data[] = [
            'Total Voluntarios',
            $totalVoluntarios,
            $voluntariosAnterior,
            $this->formatearVariacion($comparativas['voluntarios'] ?? [])
        ];
        
        // Total Participantes
        $totalParticipantes = $metricas['total_participantes'] ?? 0;
        $participantesAnterior = $comparativas['participantes']['anterior'] ?? 0;
        $data[] = [
            'Total Participantes',
            $totalParticipantes,
            $participantesAnterior,
            $this->formatearVariacion($comparativas['participantes'] ?? [])
        ];
        
        $data[] = ['', '', '', '']; // Fila vacía
        
        // SECCIÓN 3 - Distribución Eventos (filas 13-16)
        $data[] = ['DISTRIBUCIÓN DE EVENTOS', 'CANTIDAD', 'PORCENTAJE', ''];
        
        $eventosInactivos = $metricas['eventos_inactivos'] ?? 0;
        $eventosFinalizados = $metricas['eventos_finalizados'] ?? 0;
        $totalEventos = $eventosActivos + $eventosInactivos + $eventosFinalizados;
        
        // Usar fórmulas para porcentajes
        $data[] = [
            'Eventos Activos',
            $eventosActivos,
            $totalEventos > 0 ? "=B14/(B14+B15+B16)" : '0%',
            ''
        ];
        
        $data[] = [
            'Eventos Inactivos',
            $eventosInactivos,
            $totalEventos > 0 ? "=B15/(B14+B15+B16)" : '0%',
            ''
        ];
        
        $data[] = [
                'Eventos Finalizados',
            $eventosFinalizados,
            $totalEventos > 0 ? "=B16/(B14+B15+B16)" : '0%',
            ''
        ];
        
        $data[] = ['', '', '', '']; // Fila vacía
        
        // SECCIÓN 4 - Métricas Engagement (filas 18-21)
        $data[] = ['MÉTRICAS DE ENGAGEMENT', 'VALOR', '', ''];
        
        $data[] = [
            'Tasa de Reacción por Evento',
            $totalEventos > 0 ? "=B7/(B14+B15+B16)" : '0.00',
                '',
            ''
        ];
        
        $data[] = [
            'Tasa de Compartidos por Evento',
            $totalEventos > 0 ? "=B8/(B14+B15+B16)" : '0.00',
                '',
                ''
        ];
        
        $data[] = [
            'Promedio Participantes por Evento',
            $totalEventos > 0 ? "=B10/(B14+B15+B16)" : '0.00',
            '',
            ''
        ];
        
        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '📊 Resumen Ejecutivo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 20,
            'C' => 20,
            'D' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // SECCIÓN 1 - Información General
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '0C2B44']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ]
        ]);
        
        // SECCIÓN 2 - KPIs Principales
        $sheet->getStyle('A5:D5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Filas de datos con bordes y filas alternas
        for ($row = 6; $row <= 10; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
            
            // Formato numérico para valores
            // Formato numérico - aplicar celda por celda
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle("C{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // SECCIÓN 3 - Distribución Eventos
        $sheet->getStyle('A13:D13')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 14; $row <= 16; $row++) {
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
            
            // Formato porcentaje para columna C
            $sheet->getStyle("C{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
        }
        
        // SECCIÓN 4 - Métricas Engagement
        $sheet->getStyle('A18:D18')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17A2B8']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 19; $row <= 21; $row++) {
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Formato decimal para valores
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        return [];
    }

    private function formatearVariacion($comparativa)
    {
        if (empty($comparativa)) {
            return 'N/A';
        }
        
        $crecimiento = $comparativa['crecimiento'] ?? 0;
        $tendencia = $comparativa['tendencia'] ?? 'stable';
        
        $simbolo = match($tendencia) {
            'up' => '↑',
            'down' => '↓',
            default => '→'
        };
        
        return $simbolo . ' ' . number_format($crecimiento, 2) . '%';
    }
}

/**
 * HOJA 3: MÉTRICAS PRINCIPALES
 * Análisis detallado de métricas con categorías y fórmulas
 */
class MetricasPrincipalesSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return [
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ];
        }
        
        $metricas = $this->datos['metricas'] ?? [];
        
        $data = [];
        
        // SECCIÓN 1 - Métricas Generales (filas 1-5)
        $data[] = ['MÉTRICAS GENERALES', 'VALOR', 'CATEGORÍA'];
        
        $data[] = [
            'Eventos Activos',
            $metricas['eventos_activos'] ?? 0,
            'Estado'
        ];
        
        $data[] = [
            'Eventos Inactivos',
            $metricas['eventos_inactivos'] ?? 0,
            'Estado'
        ];
        
        $data[] = [
            'Eventos Finalizados',
            $metricas['eventos_finalizados'] ?? 0,
            'Estado'
        ];
        
        $data[] = [
            'Eventos Cancelados',
            $metricas['eventos_cancelados'] ?? 0,
            'Estado'
        ];
        
        $data[] = ['', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Engagement (filas 7-11)
        $data[] = ['ENGAGEMENT', 'VALOR', 'CATEGORÍA'];
        
        $totalReacciones = $metricas['total_reacciones'] ?? 0;
        $totalCompartidos = $metricas['total_compartidos'] ?? 0;
        
        $data[] = [
            'Total Reacciones',
            $totalReacciones,
            'Interacción'
        ];
        
        $data[] = [
            'Total Compartidos',
            $totalCompartidos,
            'Interacción'
        ];
        
        // Fórmulas para estimados
        $data[] = [
            'Me Gusta Estimado',
            "=B8*0.7",
            'Estimado'
        ];
        
        $data[] = [
            'Comentarios Estimado',
            "=B8*0.3",
            'Estimado'
        ];
        
        $data[] = ['', '', '']; // Fila vacía
        
        // SECCIÓN 3 - Participación (filas 13-17)
        $data[] = ['PARTICIPACIÓN', 'VALOR', 'CATEGORÍA'];
        
        $totalVoluntarios = $metricas['total_voluntarios'] ?? 0;
        $totalParticipantes = $metricas['total_participantes'] ?? 0;
        
        $data[] = [
            'Total Voluntarios',
            $totalVoluntarios,
            'Personas'
        ];
        
        $data[] = [
            'Total Participantes',
            $totalParticipantes,
            'Personas'
            ];
        
        // Fórmulas para estimados
        $data[] = [
            'Únicos',
            "=B15*0.85",
            'Estimado'
        ];
        
        $data[] = [
            'Recurrentes',
            "=B15*0.15",
            'Estimado'
        ];
        
        $data[] = ['', '', '']; // Fila vacía
        
        // SECCIÓN 4 - Ratios (filas 19-23)
        $data[] = ['RATIOS Y PROMEDIOS', 'VALOR', 'FÓRMULA'];
        
        $totalEventos = ($metricas['eventos_activos'] ?? 0) + 
                       ($metricas['eventos_inactivos'] ?? 0) + 
                       ($metricas['eventos_finalizados'] ?? 0) + 
                       ($metricas['eventos_cancelados'] ?? 0);
        
        // Validar división por cero
        $denominador = $totalEventos > 0 ? "(B2+B3+B4+B5)" : "1";
        
        $data[] = [
            'Promedio Reacciones/Evento',
            "=B8/{$denominador}",
            'Total Reacciones / Total Eventos'
        ];
        
        $data[] = [
            'Promedio Compartidos/Evento',
            "=B9/{$denominador}",
            'Total Compartidos / Total Eventos'
        ];
        
        $data[] = [
            'Promedio Participantes/Evento',
            "=B15/{$denominador}",
            'Total Participantes / Total Eventos'
        ];
        
        $denominadorEngagement = $totalParticipantes > 0 ? "B15" : "1";
        $data[] = [
            'Tasa Engagement',
            "=(B8+B9)/{$denominadorEngagement}",
            '(Reacciones + Compartidos) / Participantes'
        ];
        
        $denominadorConversion = $totalParticipantes > 0 ? "B15" : "1";
        $data[] = [
            'Tasa Conversión Voluntarios',
            "=B14/{$denominadorConversion}",
            'Voluntarios / Participantes'
            ];

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '📈 Métricas Principales';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 18,
            'C' => 45
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // SECCIÓN 1 - Métricas Generales
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 2; $row <= 5; $row++) {
            $fillColor = ($row % 2 == 0) ? 'E3F2FD' : 'FFFFFF';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // SECCIÓN 2 - Engagement
        $sheet->getStyle('A7:C7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 8; $row <= 11; $row++) {
            $fillColor = ($row % 2 == 0) ? 'E8F5E9' : 'FFFFFF';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // SECCIÓN 3 - Participación
        $sheet->getStyle('A13:C13')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17A2B8']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 14; $row <= 17; $row++) {
            $fillColor = ($row % 2 == 0) ? 'E1F5FE' : 'FFFFFF';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // SECCIÓN 4 - Ratios
        $sheet->getStyle('A19:C19')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 20; $row <= 23; $row++) {
            $fillColor = ($row % 2 == 0) ? 'FFF3E0' : 'FFFFFF';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Formato decimal para ratios
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        return [];
    }
}

/**
 * HOJA 4: TOP EVENTOS
 * Ranking con formato condicional y barras de datos
 */
class TopEventosSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $topEventos = $this->datos['top_eventos'] ?? [];
        
        if (empty($topEventos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DE EVENTOS DISPONIBLES'],
                ['No se encontraron eventos en el período seleccionado.'],
                ['Intente ampliar el rango de fechas o ajustar los filtros.']
            ]);
        }
        
        $data = [];
        
        // Limitar a top 10
        $topEventos = array_slice($topEventos, 0, 10);
        
        // Mapear eventos con fórmulas
        foreach ($topEventos as $index => $evento) {
            $row = $index + 3; // Empezar en fila 3 (después de headers)
            $data[] = [
                '#' . ($index + 1),
                $evento['titulo'] ?? 'Sin título',
                $evento['reacciones'] ?? 0,
                $evento['compartidos'] ?? 0,
                $evento['inscripciones'] ?? 0,
                "=C{$row}+D{$row}+E{$row}", // Engagement total
                ucfirst($evento['estado'] ?? 'N/A')
            ];
        }
        
        // Fila de totales
        $lastDataRow = count($data) + 2; // +2 por headers
        $data[] = [
            'TOTAL',
            '',
            "=SUMA(C3:C{$lastDataRow})",
            "=SUMA(D3:D{$lastDataRow})",
            "=SUMA(E3:E{$lastDataRow})",
            "=SUMA(F3:F{$lastDataRow})",
            ''
        ];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            ['TOP 10 EVENTOS POR ENGAGEMENT'],
            ['#', 'TÍTULO DEL EVENTO', 'REACCIONES', 'COMPARTIDOS', 'INSCRIPCIONES', 'ENGAGEMENT TOTAL', 'ESTADO']
        ];
    }

    public function title(): string
    {
        return '🏆 Top Eventos';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 35,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 18,
            'G' => 15
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título principal (A1:G1)
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Header tabla (A2:G2)
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C62828']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Obtener número de filas de datos
        $topEventos = $this->datos['top_eventos'] ?? [];
        $dataRows = min(count($topEventos), 10);
        $lastDataRow = $dataRows + 2; // +2 por headers
        $totalRow = $lastDataRow + 1;
        
        // Columna ranking destacada (A3:A12)
        for ($row = 3; $row <= $lastDataRow; $row++) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '757575']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
        }
        
        // Datos con bordes y filas alternas (B3:G12)
        for ($row = 3; $row <= $lastDataRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
            
            // Formato numérico para columnas numéricas
            $sheet->getStyle("C{$row}:F{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // Fila totales (A13:G13)
        $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Formato numérico para totales - aplicar celda por celda
        for ($col = 'C'; $col <= 'F'; $col++) {
            $sheet->getStyle("{$col}{$totalRow}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // Congelar paneles en fila 2
        $sheet->freezePane('A3');
        
        // Auto-filter en headers (solo si hay datos - excluir fila de totales)
        if ($dataRows > 0 && $lastDataRow > 2) {
            $sheet->setAutoFilter("A2:G{$lastDataRow}");
        }
        
        return [];
    }
}

/**
 * HOJA 5: TOP VOLUNTARIOS
 * Hall of Fame con reconocimientos y badges
 */
class TopVoluntariosSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $topVoluntarios = $this->datos['top_voluntarios'] ?? [];
        
        if (empty($topVoluntarios)) {
            return new Collection([
                ['⚠️ NO SE ENCONTRARON VOLUNTARIOS REGISTRADOS'],
                ['No hay voluntarios registrados en el período seleccionado.'],
                ['Intente ampliar el rango de fechas o ajustar los filtros.']
            ]);
        }
        
        $data = [];
        
        // Mapear voluntarios
        foreach ($topVoluntarios as $index => $voluntario) {
            $row = $index + 4; // Empezar en fila 4 (después de título y header)
            $eventosParticipados = $voluntario['eventos_participados'] ?? 0;
            
            $data[] = [
                '#' . ($index + 1),
                $voluntario['nombre'] ?? 'Sin nombre',
                $voluntario['email'] ?? 'N/A',
                $eventosParticipados,
                "=D{$row}*2", // Horas calculadas
                $this->badge($eventosParticipados)
            ];
        }
        
        // Fila resumen
        $lastDataRow = count($data) + 3; // +3 por título y headers
        $data[] = [
            'TOTALES',
            '',
            '',
            "=SUMA(D4:D{$lastDataRow})",
            "=SUMA(E4:E{$lastDataRow})",
            ''
        ];
        
        // Nota al pie
        $data[] = ['', '', '', '', '', ''];
        $data[] = ['Nota: Horas calculadas estimando 2 horas promedio por evento', '', '', '', '', ''];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            ['HALL OF FAME DE VOLUNTARIOS'],
            [''],
            ['#', 'NOMBRE COMPLETO', 'EMAIL', 'EVENTOS PARTICIPADOS', 'HORAS CONTRIBUIDAS', 'RECONOCIMIENTO']
        ];
    }

    public function title(): string
    {
        return '👥 Top Voluntarios';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 35,
            'D' => 20,
            'E' => 20,
            'F' => 18
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título principal (A1:F1)
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFD700']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'FFD700']
                ]
            ]
        ]);
        
        // Header tabla (A3:F3)
        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Obtener número de filas
        $topVoluntarios = $this->datos['top_voluntarios'] ?? [];
        $dataRows = count($topVoluntarios);
        $lastDataRow = $dataRows + 3; // +3 por título y headers
        $totalRow = $lastDataRow + 1;
        $notaRow = $totalRow + 2;
        
        // Columna ranking destacada (A4:A13)
        for ($row = 4; $row <= $lastDataRow; $row++) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00796B']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
        }
        
        // Datos con bordes y filas alternas (B4:F13)
        for ($row = 4; $row <= $lastDataRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Formato numérico - aplicar celda por celda
            $sheet->getStyle("D{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle("E{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            // Formato especial para badges en columna F
            $badgeCell = $sheet->getCell("F{$row}")->getValue();
            if (strpos($badgeCell, 'Gold') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFD700']
                    ]
                ]);
            } elseif (strpos($badgeCell, 'Silver') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
            } elseif (strpos($badgeCell, 'Bronze') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'CD7F32']
                    ]
                ]);
            }
        }
        
        // Fila totales
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Formato numérico para totales - aplicar celda por celda
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()
            ->setFormatCode('#,##0');
        $sheet->getStyle("E{$totalRow}")->getNumberFormat()
            ->setFormatCode('#,##0');
        
        // Nota al pie
        $sheet->mergeCells("A{$notaRow}:F{$notaRow}");
        $sheet->getStyle("A{$notaRow}")->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 9,
                'color' => ['rgb' => '616161']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ]);
        
        // Congelar paneles en fila 3
        $sheet->freezePane('A4');
        
        // Auto-filter en headers (solo si hay datos)
        $topVoluntarios = $this->datos['top_voluntarios'] ?? [];
        $dataRows = min(count($topVoluntarios), 10);
        $lastDataRow = $dataRows + 3; // +3 por título y headers
        if ($dataRows > 0 && $lastDataRow > 3) {
            $sheet->setAutoFilter("A3:F{$lastDataRow}");
        }
        
        return [];
    }

    private function badge($eventosParticipados)
    {
        if ($eventosParticipados > 10) {
            return '⭐⭐⭐ Gold';
        } elseif ($eventosParticipados >= 5) {
            return '⭐⭐ Silver';
        } else {
            return '⭐ Bronze';
        }
    }
}

/**
 * HOJA 6: TENDENCIAS TEMPORALES
 * Análisis de series de tiempo con fórmulas avanzadas
 */
class TendenciasTemporalesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $tendencias = $this->datos['tendencias_mensuales'] ?? [];
        
        if (empty($tendencias)) {
            return new Collection([
                ['⚠️ DATOS INSUFICIENTES PARA ANÁLISIS TEMPORAL'],
                ['No hay suficientes datos mensuales en el período seleccionado.'],
                ['Se requiere al menos un mes de datos para generar el análisis.']
            ]);
        }
        
        $data = [];
        
        // SECCIÓN 1 - KPIs del Período (filas 1-5)
        $data[] = ['RESUMEN DEL PERÍODO', '', '', '', ''];
        
        $firstRow = 7; // Primera fila de datos
        $lastRow = count($tendencias) + 6; // Última fila de datos
        
        $data[] = [
            'Crecimiento Total:',
            "=SI(B{$firstRow}=0, 0, (B{$lastRow}-B{$firstRow})/B{$firstRow})",
            'Mejor Mes:',
            "=MAX(B{$firstRow}:B{$lastRow})",
            ''
        ];
        
        $data[] = [
            'Promedio Mensual:',
            "=PROMEDIO(B{$firstRow}:B{$lastRow})",
            'Peor Mes:',
            "=MIN(B{$firstRow}:B{$lastRow})",
            ''
        ];
        
        $data[] = [
            'Tendencia:',
            "=SI(B{$lastRow}>B{$firstRow}, \"↑ Creciendo\", SI(B{$lastRow}<B{$firstRow}, \"↓ Decreciendo\", \"→ Estable\"))",
            'Volatilidad:',
            "=DESVEST(B{$firstRow}:B{$lastRow})",
            ''
        ];
        
        $data[] = ['', '', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Tabla de Tendencias (filas 6-N)
        $data[] = ['MES', 'PARTICIPANTES', 'VARIACIÓN %', 'PROMEDIO MÓVIL 3M', 'TENDENCIA'];
        
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        $rowIndex = $firstRow;
        $previousRow = null;
        
        foreach ($tendencias as $mes => $participantes) {
            // Convertir YYYY-MM a nombre de mes
            $parts = explode('-', $mes);
            $mesNombre = $meses[$parts[1] ?? '01'] ?? $mes;
            
            // Variación %
            if ($previousRow === null) {
                $variacion = 'N/A';
            } else {
                $variacion = "=SI(B{$previousRow}=0, 0, (B{$rowIndex}-B{$previousRow})/B{$previousRow})";
            }
            
            // Promedio móvil 3M
            if ($rowIndex < $firstRow + 2) {
                $promedioMovil = 'N/A';
            } else {
                $promedioMovil = "=PROMEDIO(B" . ($rowIndex - 2) . ":B{$rowIndex})";
            }
            
            // Tendencia
            if ($previousRow === null) {
                $tendencia = 'N/A';
            } else {
                $tendencia = "=SI(C{$rowIndex}>0,\"↑ Creciendo\",SI(C{$rowIndex}<0,\"↓ Decreciendo\",\"→ Estable\"))";
            }
            
            $data[] = [
                $mesNombre,
                $participantes,
                $variacion,
                $promedioMovil,
                $tendencia
            ];
            
            $previousRow = $rowIndex;
            $rowIndex++;
        }
        
        // Fila de totales
        $data[] = [
            'TOTALES/PROMEDIOS',
            "=SUMA(B{$firstRow}:B{$lastRow})",
            "=PROMEDIO(C{$firstRow}:C{$lastRow})",
            "=PROMEDIO(D{$firstRow}:D{$lastRow})",
            ''
        ];
        
        // SECCIÓN 3 - Estadísticas (después de tabla)
        $data[] = ['', '', '', '', ''];
        $data[] = ['ESTADÍSTICAS AVANZADAS', '', '', '', ''];
        
        $data[] = [
            'Máximo Mensual:',
            "=MAX(B{$firstRow}:B{$lastRow})",
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Mínimo Mensual:',
            "=MIN(B{$firstRow}:B{$lastRow})",
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Rango:',
            "=MAX(B{$firstRow}:B{$lastRow})-MIN(B{$firstRow}:B{$lastRow})",
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Coeficiente Variación:',
            "=SI(PROMEDIO(B{$firstRow}:B{$lastRow})=0, 0, DESVEST(B{$firstRow}:B{$lastRow})/PROMEDIO(B{$firstRow}:B{$lastRow}))",
            '',
            '',
            ''
        ];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '📊 Tendencias Temporales';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 18,
            'C' => 15,
            'D' => 20,
            'E' => 18
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $tendencias = $this->datos['tendencias_mensuales'] ?? [];
        $firstRow = 7;
        $lastRow = count($tendencias) + 6;
        $totalRow = $lastRow + 1;
        $statsStartRow = $totalRow + 2;
        
        // SECCIÓN 1 - KPIs del Período
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17A2B8']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // KPIs tarjetas (A2:E4)
        for ($row = 2; $row <= 4; $row++) {
            $fillColor = ($row % 2 == 0) ? 'E3F2FD' : 'FFFFFF';
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Etiquetas bold
            $sheet->getStyle("A{$row},C{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
            
            // Valores grandes
            $sheet->getStyle("B{$row},D{$row}")->applyFromArray([
                'font' => [
                    'size' => 14,
                    'bold' => true
                ]
            ]);
        }
        
        // SECCIÓN 2 - Header tabla (A6:E6)
        $sheet->getStyle('A6:E6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Datos tabla (A7:E{lastRow})
        for ($row = $firstRow; $row <= $lastRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Formatos numéricos
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            $sheet->getStyle("C{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
            
            $sheet->getStyle("D{$row}")->getNumberFormat()
                ->setFormatCode('#,##0.0');
        }
        
        // Fila totales
        $sheet->getStyle("A{$totalRow}:E{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00796B']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // SECCIÓN 3 - Estadísticas
        $sheet->mergeCells("A{$statsStartRow}:E{$statsStartRow}");
        $sheet->getStyle("A{$statsStartRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0C2B44']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = $statsStartRow + 1; $row <= $statsStartRow + 4; $row++) {
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
        }
        
        // Congelar paneles en fila 6
        $sheet->freezePane('A7');
        
        // Auto-filter en headers (solo si hay datos)
        $tendenciasMensuales = $this->datos['tendencias_mensuales'] ?? [];
        $dataRows = count($tendenciasMensuales);
        $lastDataRow = $dataRows + 6; // +6 por headers (header en fila 6, datos desde 7)
        if ($dataRows > 0 && $lastDataRow > 6) {
            $sheet->setAutoFilter("A6:E{$lastDataRow}");
        }
        
        return [];
    }
}

/**
 * HOJA 7: DISTRIBUCIÓN DE ESTADOS
 * Con semáforo visual y métricas derivadas
 */
class DistribucionEstadosSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $distribucion = $this->datos['distribucion_estados'] ?? [];
        
        if (empty($distribucion)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DE DISTRIBUCIÓN DISPONIBLES'],
                ['No se encontraron datos de distribución de estados.'],
                ['Intente ampliar el rango de fechas o ajustar los filtros.']
            ]);
        }
        
        $data = [];
        
        // SECCIÓN 1 - Título y Total
        $data[] = ['DISTRIBUCIÓN DE EVENTOS POR ESTADO', '', '', '', ''];
        $data[] = ['Total de Eventos:', "=SUMA(B5:B8)", '', '', ''];
        $data[] = ['', '', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Tabla de Frecuencias
        $data[] = ['ESTADO', 'CANTIDAD', 'PORCENTAJE', '% ACUMULADO', 'VISUALIZACIÓN'];
        
        $activo = $distribucion['activo'] ?? 0;
        $inactivo = $distribucion['inactivo'] ?? 0;
        $finalizado = $distribucion['finalizado'] ?? 0;
        $cancelado = $distribucion['cancelado'] ?? 0;
        
        // Ordenar por cantidad descendente
        $estados = [
            ['Activo', $activo, "=SI(\$B\$2=0, 0, B5/\$B\$2)", "=C5", "BARRA"],
            ['Inactivo', $inactivo, "=SI(\$B\$2=0, 0, B6/\$B\$2)", "=C6+D5", "BARRA"],
            ['Finalizado', $finalizado, "=SI(\$B\$2=0, 0, B7/\$B\$2)", "=C7+D6", "BARRA"],
            ['Cancelado', $cancelado, "=SI(\$B\$2=0, 0, B8/\$B\$2)", "=C8+D7", "BARRA"]
        ];
        
        // Ordenar por cantidad descendente
        usort($estados, function($a, $b) {
            return $b[1] <=> $a[1];
        });
        
        foreach ($estados as $estado) {
            $data[] = $estado;
        }
        
        // Fila totales
        $data[] = ['TOTAL', "=SUMA(B5:B8)", "=SUMA(C5:C8)", '100%', ''];
        
        // SECCIÓN 3 - Análisis de Ratios
        $data[] = ['', '', '', '', ''];
        $data[] = ['MÉTRICAS DERIVADAS', '', '', '', ''];
        
        $data[] = [
            'Tasa de Finalización:',
            "=SI(\$B\$2=0, 0, B7/\$B\$2)",
            'Eventos finalizados / Total eventos'
        ];
        
        $data[] = [
            'Tasa de Actividad:',
            "=SI(\$B\$2=0, 0, B5/\$B\$2)",
            'Eventos activos / Total eventos'
        ];
        
        $data[] = [
            'Ratio Activo/Inactivo:',
            "=SI(B6=0,\"N/A\",B5/B6)",
            'Proporción eventos activos vs inactivos'
        ];
        
        $data[] = [
            'Tasa de Cancelación:',
            "=SI(\$B\$2=0, 0, B8/\$B\$2)",
            'Eventos cancelados / Total eventos'
        ];
        
        // SECCIÓN 4 - Interpretación
        $data[] = ['', '', '', '', ''];
        $data[] = ['INTERPRETACIÓN', '', '', '', ''];
        
        $data[] = [
            'Estado Predominante:',
            "=SI(B5=MAX(\$B\$5:\$B\$8),\"Activo\",SI(B6=MAX(\$B\$5:\$B\$8),\"Inactivo\",SI(B7=MAX(\$B\$5:\$B\$8),\"Finalizado\",\"Cancelado\")))",
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Porcentaje del Predominante:',
            "=MAX(C5:C8)",
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Salud del Programa:',
            "=SI(C5>0.5,\"Excelente\",SI(C5>0.3,\"Bueno\",SI(C5>0.2,\"Regular\",\"Necesita Atención\")))",
            '',
            '',
            ''
        ];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '📊 Distribución Estados';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 15,
            'C' => 15,
            'D' => 18,
            'E' => 25
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // SECCIÓN 1 - Título
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Total de Eventos (B2:E2)
        $sheet->mergeCells('B2:E2');
        $sheet->getStyle('B2:E2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // SECCIÓN 2 - Header tabla (A4:E4)
        $sheet->getStyle('A4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Filas de datos con colores según estado
        // Fila 5 (Activo)
        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '2E7D32']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Fila 6 (Inactivo)
        $sheet->getStyle('A6:E6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'EF6C00']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Fila 7 (Finalizado)
        $sheet->getStyle('A7:E7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'C62828']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFCDD2']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Fila 8 (Cancelado)
        $sheet->getStyle('A8:E8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '424242']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Formatos numéricos - aplicar celda por celda
        for ($row = 5; $row <= 8; $row++) {
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle("C{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
            $sheet->getStyle("D{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
        }
        
        // Fila totales (A9:E9)
        $sheet->getStyle('A9:E9')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // SECCIÓN 3 - Métricas Derivadas
        $sheet->mergeCells('A11:B11');
        $sheet->getStyle('A11')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17A2B8']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 12; $row <= 15; $row++) {
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
            
            $sheet->getStyle("C{$row}")->applyFromArray([
                'font' => ['italic' => true]
            ]);
        }
        
        // SECCIÓN 4 - Interpretación
        $sheet->mergeCells('A17:E17');
        $sheet->getStyle('A17')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        for ($row = 18; $row <= 20; $row++) {
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("B{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14
                ]
            ]);
        }
        
        return [];
    }
}

/**
 * HOJA 8: LISTADO COMPLETO DE EVENTOS
 * Tabla filtrable con resumen estadístico
 */
class ListadoCompletaEventosSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $eventos = $this->datos['listado_eventos'] ?? [];
        
        if (empty($eventos)) {
            return new Collection([
                ['⚠️ NO HAY EVENTOS REGISTRADOS'],
                ['No hay eventos registrados en el período seleccionado.'],
                ['Intente ampliar el rango de fechas o ajustar los filtros.']
            ]);
        }
        
        $data = [];
        
        // SECCIÓN 1 - Instrucciones
        $data[] = ['LISTADO COMPLETO DE EVENTOS', '', '', '', '', '', '', '', ''];
        $data[] = ['💡 Instrucciones: Use los filtros en el encabezado para filtrar eventos por cualquier columna. Haga clic en las flechas del encabezado.', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Tabla de Eventos
        $data[] = ['ID', 'TÍTULO', 'FECHA INICIO', 'FECHA FIN', 'DURACIÓN (DÍAS)', 'UBICACIÓN', 'ESTADO', 'PARTICIPANTES', 'TIPO'];
        
        foreach ($eventos as $evento) {
            $row = count($data) + 1; // Fila actual
            
            $titulo = $evento['titulo'] ?? 'Sin título';
            if (strlen($titulo) > 50) {
                $titulo = substr($titulo, 0, 47) . '...';
            }
            
            $fechaInicio = $evento['fecha_inicio'] ?? null;
            $fechaInicioFormato = $fechaInicio ? Carbon::parse($fechaInicio)->format('d/m/Y') : 'N/A';
            
            $fechaFin = $evento['fecha_fin'] ?? null;
            $fechaFinFormato = $fechaFin ? Carbon::parse($fechaFin)->format('d/m/Y') : 'N/A';
            
            $duracion = $fechaFinFormato === 'N/A' ? 'N/A' : "=SI(D{$row}=\"N/A\",\"N/A\",D{$row}-C{$row})";
            
            $data[] = [
                $evento['id'] ?? '',
                $titulo,
                $fechaInicioFormato,
                $fechaFinFormato,
                $duracion,
                $evento['ubicacion'] ?? 'N/A',
                ucfirst($evento['estado'] ?? 'N/A'),
                $evento['total_participantes'] ?? 0,
                $evento['tipo'] === 'mega_evento' ? 'Mega Evento' : 'Evento'
            ];
        }
        
        // Fila totales
        $lastRow = count($data);
        $data[] = [
            '',
            'TOTALES:',
            '',
            '',
            '',
            '',
            '',
            "=SUMA(H5:H{$lastRow})",
            ''
        ];
        
        // SECCIÓN 3 - Resumen Estadístico
        $data[] = ['', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', ''];
        $data[] = ['RESUMEN ESTADÍSTICO', '', '', '', '', '', '', '', ''];
        
        $data[] = [
            'Total de Eventos:',
            "=CONTARA(A5:A{$lastRow})",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Eventos Regulares:',
            "=CONTAR.SI(I5:I{$lastRow},\"Evento\")",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Mega Eventos:',
            "=CONTAR.SI(I5:I{$lastRow},\"Mega Evento\")",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Promedio Participantes:',
            "=PROMEDIO(H5:H{$lastRow})",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Evento con Más Participantes:',
            "=INDICE(B5:B{$lastRow},COINCIDIR(MAX(H5:H{$lastRow}),H5:H{$lastRow},0))",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Duración Promedio:',
            "=PROMEDIO(E5:E{$lastRow})",
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '📋 Listado Completo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 40,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 25,
            'G' => 15,
            'H' => 15,
            'I' => 15
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $eventos = $this->datos['listado_eventos'] ?? [];
        $lastRow = count($eventos) + 4; // +4 por headers e instrucciones
        $totalRow = $lastRow + 1;
        $resumenStartRow = $totalRow + 3;
        
        // SECCIÓN 1 - Título
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Instrucciones (A2:I2)
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '1565C0']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // SECCIÓN 2 - Header tabla (A4:I4)
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Datos tabla (A5:I{lastRow})
        for ($row = 5; $row <= $lastRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Alineaciones específicas
            $sheet->getStyle("A{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            $sheet->getStyle("B{$row},F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'alignment' => ['wrapText' => true]
            ]);
            
            $sheet->getStyle("C{$row},D{$row},E{$row},G{$row},H{$row},I{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            // Formatos
            $sheet->getStyle("C{$row},D{$row}")->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            
            $sheet->getStyle("E{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            $sheet->getStyle("H{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            // Formato condicional por estado en columna G
            $estadoCell = $sheet->getCell("G{$row}")->getValue();
            if (stripos($estadoCell, 'activo') !== false) {
                $sheet->getStyle("G{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ],
                    'font' => ['bold' => true]
                ]);
            } elseif (stripos($estadoCell, 'inactivo') !== false) {
                $sheet->getStyle("G{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9C4']
                    ],
                    'font' => ['bold' => true]
                ]);
            } elseif (stripos($estadoCell, 'finalizado') !== false) {
                $sheet->getStyle("G{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCDD2']
                    ],
                    'font' => ['bold' => true]
                ]);
            }
            
            // Formato condicional por tipo en columna I
            $tipoCell = $sheet->getCell("I{$row}")->getValue();
            if ($tipoCell === 'Evento') {
                $sheet->getStyle("I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E3F2FD']
                    ]
                ]);
            } elseif ($tipoCell === 'Mega Evento') {
                $sheet->getStyle("I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3E5F5']
                    ]
                ]);
            }
        }
        
        // Fila totales
        $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        $sheet->getStyle("H{$totalRow}")->getNumberFormat()
            ->setFormatCode('#,##0');
        
        // SECCIÓN 3 - Resumen Estadístico
        $sheet->mergeCells("A{$resumenStartRow}:I{$resumenStartRow}");
        $sheet->getStyle("A{$resumenStartRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0C2B44']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        for ($row = $resumenStartRow + 1; $row <= $resumenStartRow + 6; $row++) {
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // Congelar paneles en fila 4 y columna B
        $sheet->freezePane('C5');
        
        // Auto-filter en headers de la tabla principal (solo si hay eventos - excluir fila de totales)
        $eventos = $this->datos['listado_eventos'] ?? [];
        $eventosCount = count($eventos);
        // lastRow ya está calculado arriba, pero necesitamos verificar que haya datos
        if ($eventosCount > 0 && $lastRow > 4) {
            $sheet->setAutoFilter("A4:I{$lastRow}");
        }
        
        return [];
    }
}

/**
 * HOJA 9: ANÁLISIS COMPARATIVO
 * Período actual vs anterior con insights y recomendaciones
 */
class AnalisisComparativoSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        // VALIDACIÓN: Verificar que los datos existen
        if (empty($this->datos) || !is_array($this->datos)) {
            return new Collection([
                ['⚠️ NO HAY DATOS DISPONIBLES'],
                ['No se encontraron datos para este período o filtros aplicados.'],
                ['Por favor, ajuste los filtros y vuelva a intentar.']
            ]);
        }
        
        $comparativas = $this->datos['comparativas'] ?? [];
        
        if (empty($comparativas)) {
            return new Collection([
                ['⚠️ NO HAY DATOS COMPARATIVOS DISPONIBLES'],
                ['No hay suficientes datos históricos para realizar comparación.'],
                ['Se requiere al menos un período anterior con datos para comparar.']
            ]);
        }
        
        $data = [];
        
        // SECCIÓN 1 - Título y Explicación
        $data[] = ['ANÁLISIS COMPARATIVO DE PERÍODOS', '', '', '', '', ''];
        $data[] = ['Comparación entre período actual y período anterior de igual duración', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Tabla Comparativa
        $data[] = ['MÉTRICA', 'PERÍODO ACTUAL', 'PERÍODO ANTERIOR', 'DIFERENCIA', 'VARIACIÓN %', 'TENDENCIA'];
        
        $metricas = [
            'reacciones' => ['Total Reacciones', $comparativas['reacciones'] ?? []],
            'compartidos' => ['Total Compartidos', $comparativas['compartidos'] ?? []],
            'voluntarios' => ['Voluntarios Activos', $comparativas['voluntarios'] ?? []],
            'participantes' => ['Participantes Únicos', $comparativas['participantes'] ?? []]
        ];
        
        $row = 5;
        foreach ($metricas as $key => $metrica) {
            $actual = $metrica[1]['actual'] ?? 0;
            $anterior = $metrica[1]['anterior'] ?? 0;
            $tendencia = $metrica[1]['tendencia'] ?? 'stable';
            
            $data[] = [
                $metrica[0],
                $actual,
                $anterior,
                "=B{$row}-C{$row}",
                "=SI(C{$row}=0, 0, (B{$row}-C{$row})/C{$row})",
                "=SI(E{$row}>0,\"👍 Crecimiento\",SI(E{$row}<0,\"👎 Decrecimiento\",\"➡️ Estable\"))"
            ];
            $row++;
        }
        
        // Métricas calculadas adicionales
        $data[] = [
            'Tasa de Engagement',
            "=SI((B5+B6)=0, 0, (B5+B6)/(B7+B8))",
            "=SI((C5+C6)=0, 0, (C5+C6)/(C7+C8))",
            "=B9-C9",
            "=SI(C9=0, 0, (B9-C9)/C9)",
            "=SI(E9>0,\"👍 Crecimiento\",SI(E9<0,\"👎 Decrecimiento\",\"➡️ Estable\"))"
        ];
        
        $data[] = [
            'Promedio Participantes/Evento',
            "=SI(B8=0, 0, B8/10)", // Asumiendo 10 eventos promedio
            "=SI(C8=0, 0, C8/10)",
            "=B10-C10",
            "=SI(C10=0, 0, (B10-C10)/C10)",
            "=SI(E10>0,\"👍 Crecimiento\",SI(E10<0,\"👎 Decrecimiento\",\"➡️ Estable\"))"
        ];
        
        // SECCIÓN 3 - Insights Clave
        $data[] = ['', '', '', '', '', ''];
        $lastDataRow = $row;
        $insightsRow = $lastDataRow + 1;
        
        $data[] = ['INSIGHTS CLAVE', '', '', '', '', ''];
        
        $data[] = [
            'Métrica con Mayor Crecimiento:',
            "=INDICE(A5:A{$lastDataRow},COINCIDIR(MAX(E5:E{$lastDataRow}),E5:E{$lastDataRow},0))",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Métrica con Mayor Decrecimiento:',
            "=INDICE(A5:A{$lastDataRow},COINCIDIR(MIN(E5:E{$lastDataRow}),E5:E{$lastDataRow},0))",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Métricas Estables (variación <5%):',
            "=CONTAR.SI(E5:E{$lastDataRow},\"<0.05\")",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Tendencia General:',
            "=SI(PROMEDIO(E5:E{$lastDataRow})>0,\"Positiva\",\"Negativa\")",
            '',
            '',
            '',
            ''
        ];
        
        // SECCIÓN 4 - Recomendaciones
        $data[] = ['', '', '', '', '', ''];
        $recomendacionesRow = count($data) + 1;
        
        $data[] = ['RECOMENDACIONES BASADAS EN ANÁLISIS', '', '', '', '', ''];
        
        $data[] = [
            "=SI(E6<-0.1,\"⚠️ Alerta: Caída significativa en reacciones. Revisar estrategia de contenido.\",\"✅ Nivel de reacciones aceptable\")",
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            "=SI(E7>0.2,\"🎉 Excelente: Crecimiento notable en voluntarios. Mantener iniciativas actuales.\",\"💡 Oportunidad: Implementar campañas de reclutamiento\")",
            '',
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            "=SI(E8<0,\"📉 Atención: Menos participantes que período anterior. Evaluar causas.\",\"📈 Participación en buen nivel\")",
            '',
            '',
            '',
            '',
            ''
        ];
        
        return new Collection($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '🔍 Análisis Comparativo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 18,
            'C' => 18,
            'D' => 15,
            'E' => 15,
            'F' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $comparativas = $this->datos['comparativas'] ?? [];
        $lastDataRow = 10; // Aproximado, ajustar según datos reales
        $insightsRow = $lastDataRow + 2;
        $recomendacionesRow = $insightsRow + 6;
        
        // SECCIÓN 1 - Título
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Explicación (A2:F2)
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 11,
                'color' => ['rgb' => '2E7D32']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // SECCIÓN 2 - Header tabla (A4:F4)
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Datos tabla (A5:F{lastDataRow})
        for ($row = 5; $row <= $lastDataRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Alineaciones
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ]);
            
            $sheet->getStyle("B{$row},C{$row},D{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            // Formatos
            $sheet->getStyle("B{$row},C{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            $sheet->getStyle("D{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            $sheet->getStyle("E{$row}")->getNumberFormat()
                ->setFormatCode('0.0%');
            
            $sheet->getStyle("F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            // Formato condicional para columna D (Diferencia)
            // Se aplicará en el código según el valor
            
            // Formato condicional para columna F (Tendencia)
            $tendenciaCell = $sheet->getCell("F{$row}")->getValue();
            if (strpos($tendenciaCell, 'Crecimiento') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ]
                ]);
            } elseif (strpos($tendenciaCell, 'Decrecimiento') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCDD2']
                    ]
                ]);
            } else {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
            }
        }
        
        // Colores especiales para columnas B y C - aplicar celda por celda
        for ($row = 5; $row <= $lastDataRow; $row++) {
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ]);
            
            $sheet->getStyle("C{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0']
                ]
            ]);
        }
        
        // SECCIÓN 3 - Insights Clave
        $sheet->mergeCells("A{$insightsRow}:F{$insightsRow}");
        $sheet->getStyle("A{$insightsRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        for ($row = $insightsRow + 1; $row <= $insightsRow + 4; $row++) {
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF9C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
            
            $sheet->getStyle("B{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ]
            ]);
        }
        
        // SECCIÓN 4 - Recomendaciones
        $sheet->mergeCells("A{$recomendacionesRow}:F{$recomendacionesRow}");
        $sheet->getStyle("A{$recomendacionesRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0C2B44']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        for ($row = $recomendacionesRow + 1; $row <= $recomendacionesRow + 3; $row++) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true
                ]
            ]);
        }
        
        return [];
    }
}

/**
 * HOJA 10: ALERTAS Y RECOMENDACIONES
 * Sistema de monitoreo con dashboard de salud
 */
class AlertasSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        $alertas = $this->datos['alertas'] ?? [];
        
        if (empty($alertas)) {
            return new Collection([
                ['✅ No hay alertas. Todo en orden.'],
            [''],
                ['El sistema no detectó problemas que requieran atención inmediata.']
            ]);
        }
        
        $data = [];
        
        // SECCIÓN 1 - Dashboard de Salud
        $data[] = ['SISTEMA DE ALERTAS Y MONITOREO', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '']; // Fila vacía
        $data[] = ['📊 DASHBOARD DE SALUD DEL PROGRAMA', '', '', '', '', ''];
        
        // Tarjetas KPI (fila 4)
        $data[] = [
            'Alertas Críticas',
            "=CONTAR.SI(B10:B1000,\"danger\")",
            '⚠️',
            'Advertencias',
            "=CONTAR.SI(B10:B1000,\"warning\")",
            '⚡'
        ];
        
        $data[] = [
            'Informativas',
            "=CONTAR.SI(B10:B1000,\"info\")",
            'ℹ️',
            'Salud General:',
            "=SI(B4=0,SI(D4<3,\"Excelente\",\"Bueno\"),SI(B4<3,\"Regular\",\"Crítico\"))",
            'Indicador basado en alertas'
        ];
        
        $data[] = ['', '', '', '', '', '']; // Fila vacía
        
        // SECCIÓN 2 - Instrucciones y Tabla
        $data[] = ['💡 Las alertas están ordenadas por severidad (críticas primero). Use los filtros para ver tipos específicos.', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '']; // Fila vacía
        
        // Header tabla
        $data[] = ['SEVERIDAD', 'TIPO', 'MENSAJE', 'EVENTO AFECTADO', 'ACCIÓN RECOMENDADA', 'PRIORIDAD'];
        
        // Ordenar alertas por severidad (danger > warning > info)
        $severidadOrden = ['danger' => 1, 'warning' => 2, 'info' => 3];
        usort($alertas, function($a, $b) use ($severidadOrden) {
            $severidadA = $severidadOrden[$a['severidad'] ?? 'info'] ?? 3;
            $severidadB = $severidadOrden[$b['severidad'] ?? 'info'] ?? 3;
            return $severidadA <=> $severidadB;
        });
        
        // Mapear tipos de alerta a descripciones
        $tiposDescripcion = [
            'evento_proximo' => 'Evento Próximo',
            'baja_participacion' => 'Baja Participación',
            'sin_voluntarios' => 'Sin Voluntarios',
            'pendiente_evaluacion' => 'Pendiente Evaluación'
        ];
        
        // Mapear tipos a acciones recomendadas
        $accionesRecomendadas = [
            'evento_proximo' => 'Revisar logística y confirmar recursos necesarios para el evento.',
            'baja_participacion' => 'Implementar estrategia de promoción y difusión del evento.',
            'sin_voluntarios' => 'Lanzar campaña de reclutamiento de voluntarios urgentemente.',
            'pendiente_evaluacion' => 'Completar evaluación post-evento y documentar resultados.'
        ];
        
        $row = 10;
        foreach ($alertas as $alerta) {
            $severidad = $alerta['severidad'] ?? 'info';
            $tipo = $alerta['tipo'] ?? 'desconocido';
            $mensaje = $alerta['mensaje'] ?? 'Sin descripción';
            $eventoId = $alerta['evento_id'] ?? null;
            
            // Obtener título del evento si existe
            $eventoTitulo = 'General';
            if ($eventoId) {
                // Intentar obtener del listado de eventos
                $eventos = $this->datos['listado_eventos'] ?? [];
                foreach ($eventos as $evento) {
                    if (($evento['id'] ?? null) == $eventoId) {
                        $eventoTitulo = $evento['titulo'] ?? 'Evento #' . $eventoId;
                        break;
                    }
                }
                if ($eventoTitulo === 'General') {
                    $eventoTitulo = 'Evento #' . $eventoId;
                }
            }
            
            $data[] = [
                $severidad,
                $tiposDescripcion[$tipo] ?? ucfirst($tipo),
                $mensaje,
                $eventoTitulo,
                $accionesRecomendadas[$tipo] ?? 'Revisar situación y tomar acción apropiada.',
                "=SI(A{$row}=\"danger\",\"ALTA\",SI(A{$row}=\"warning\",\"MEDIA\",\"BAJA\"))"
            ];
            $row++;
        }
        
        // SECCIÓN 3 - Resumen por Tipo
        $data[] = ['', '', '', '', '', ''];
        $resumenRow = $row;
        $data[] = ['RESUMEN POR TIPO DE ALERTA', '', '', '', '', ''];
        
        $data[] = [
            'Eventos Próximos a Iniciar',
            "=CONTAR.SI(B10:B{$row},\"Evento Próximo\")",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Eventos con Baja Participación',
            "=CONTAR.SI(B10:B{$row},\"Baja Participación\")",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Eventos sin Voluntarios Suficientes',
            "=CONTAR.SI(B10:B{$row},\"Sin Voluntarios\")",
            '',
            '',
            '',
            ''
        ];
        
        $data[] = [
            'Eventos Pendientes de Evaluación',
            "=CONTAR.SI(B10:B{$row},\"Pendiente Evaluación\")",
            '',
            '',
            '',
            ''
        ];
        
        // SECCIÓN 4 - Acciones Prioritarias
        $data[] = ['', '', '', '', '', ''];
        $accionesRow = $resumenRow + 6;
        $data[] = ['🎯 ACCIONES PRIORITARIAS INMEDIATAS', '', '', '', '', ''];
        
        // Top 5 alertas críticas (danger)
        $alertasCriticas = array_filter($alertas, function($a) {
            return ($a['severidad'] ?? '') === 'danger';
        });
        $alertasCriticas = array_slice($alertasCriticas, 0, 5);
        
        if (empty($alertasCriticas)) {
            $data[] = [
                'No hay alertas críticas que requieran acción inmediata.',
                '',
                '',
                '',
                '',
                ''
            ];
        } else {
            foreach ($alertasCriticas as $index => $alerta) {
                $tipo = $alerta['tipo'] ?? 'desconocido';
                $mensaje = $alerta['mensaje'] ?? 'Sin descripción';
                $accion = $accionesRecomendadas[$tipo] ?? 'Revisar situación.';
                
                $data[] = [
                    ($index + 1) . '. ' . $mensaje,
                    '',
                    '',
                    '',
                    $accion,
                    ''
            ];
            }
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '⚠️ Alertas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 20,
            'C' => 35,
            'D' => 25,
            'E' => 30,
            'F' => 12
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $alertas = $this->datos['alertas'] ?? [];
        $alertasCount = count($alertas);
        $lastDataRow = $alertasCount > 0 ? (10 + $alertasCount) : 10;
        $resumenRow = $lastDataRow + 2;
        $accionesRow = $resumenRow + 6;
        
        // SECCIÓN 1 - Título principal
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Dashboard de Salud (A3:F3)
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Tarjetas KPI (fila 4)
        // Tarjeta Críticas (A4:C4)
        $sheet->mergeCells('A4:C4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'C62828']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFCDD2']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        $sheet->getStyle('B4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ]
        ]);
        
        // Tarjeta Advertencias (D4:F4)
        $sheet->mergeCells('D4:F4');
        $sheet->getStyle('D4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'EF6C00']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        $sheet->getStyle('E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ]
        ]);
        
        // Tarjeta Informativas y Salud General (fila 5)
        $sheet->mergeCells('A5:C5');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '1565C0']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BBDEFB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        $sheet->getStyle('B5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ]
        ]);
        
        // Salud General (D5:F5)
        $sheet->mergeCells('D5:F5');
        $sheet->getStyle('D5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        $sheet->getStyle('E5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ]
        ]);
        
        // Instrucciones (A7:F7)
        $sheet->mergeCells('A7:F7');
        $sheet->getStyle('A7')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '1565C0']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ]);
        
        // Header tabla (A9:F9)
        $sheet->getStyle('A9:F9')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
        
        // Datos tabla (A10:F{lastDataRow})
        for ($row = 10; $row <= $lastDataRow; $row++) {
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Formato condicional por severidad en columna A
            $severidadCell = $sheet->getCell("A{$row}")->getValue();
            if ($severidadCell === 'danger') {
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'C62828']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCDD2']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            } elseif ($severidadCell === 'warning') {
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'EF6C00']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9C4']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            } elseif ($severidadCell === 'info') {
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1565C0']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BBDEFB']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            }
            
            // Alineaciones específicas
            $sheet->getStyle("B{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            $sheet->getStyle("C{$row},D{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true
                ]
            ]);
            
            $sheet->getStyle("E{$row}")->applyFromArray([
                'font' => ['italic' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true
                ]
            ]);
            
            // Formato condicional por prioridad en columna F
            $prioridadCell = $sheet->getCell("F{$row}")->getValue();
            if (strpos($prioridadCell, 'ALTA') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'C62828']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCDD2']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            } elseif (strpos($prioridadCell, 'MEDIA') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'EF6C00']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9C4']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            } elseif (strpos($prioridadCell, 'BAJA') !== false) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '2E7D32']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            }
        }
        
        // SECCIÓN 3 - Resumen por Tipo
        $sheet->mergeCells("A{$resumenRow}:F{$resumenRow}");
        $sheet->getStyle("A{$resumenRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        for ($row = $resumenRow + 1; $row <= $resumenRow + 4; $row++) {
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true]
            ]);
            
            $sheet->getStyle("B{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            $sheet->getStyle("B{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // SECCIÓN 4 - Acciones Prioritarias
        $sheet->mergeCells("A{$accionesRow}:F{$accionesRow}");
        $sheet->getStyle("A{$accionesRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFD54F']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => 'DC3545']
                ]
            ]
        ]);
        
        $accionesCount = min(count(array_filter($alertas, fn($a) => ($a['severidad'] ?? '') === 'danger')), 5);
        for ($row = $accionesRow + 1; $row <= $accionesRow + $accionesCount; $row++) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFD54F']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DC3545']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true
                ]
            ]);
        }
        
        // Congelar paneles en fila 9
        $sheet->freezePane('A10');
        
        // Auto-filter en headers de alertas (solo si hay alertas)
        $alertas = $this->datos['alertas'] ?? [];
        $alertasCount = count($alertas);
        $lastAlertRow = $alertasCount + 9; // +9 por headers e instrucciones (header en fila 9, datos desde 10)
        if ($alertasCount > 0 && $lastAlertRow > 9) {
            $sheet->setAutoFilter("A9:F{$lastAlertRow}");
        }
        
        return [];
    }
}
