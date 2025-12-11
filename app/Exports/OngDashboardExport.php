<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithAutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Ong;
use Carbon\Carbon;

// Verificar si la interfaz existe, si no, definirla
if (!interface_exists('Maatwebsite\Excel\Concerns\WithMultipleSheets')) {
    namespace Maatwebsite\Excel\Concerns {
        interface WithMultipleSheets {
            public function sheets(): array;
        }
    }
}

class OngDashboardExport implements \Maatwebsite\Excel\Concerns\WithMultipleSheets
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

    public function sheets(): array
    {
        return [
            new OngDashboardResumenSheet($this->ong, $this->datos, $this->fechaInicio, $this->fechaFin),
            new OngDashboardEventosSheet($this->datos),
            new OngDashboardTendenciasSheet($this->datos),
            new OngDashboardReaccionesCompartidosSheet($this->datos),
            new OngDashboardInscripcionesSheet($this->datos),
            new OngDashboardTopEventosSheet($this->datos),
            new OngDashboardTopVoluntariosSheet($this->datos),
            new OngDashboardAnalisisEstadoSheet($this->datos),
        ];
    }
}

// Hoja 1: Resumen Ejecutivo
class OngDashboardResumenSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
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
        $metricas = $this->datos['metricas'];
        $comparativas = $this->datos['comparativas'] ?? [];
        
        return [
            ['RESUMEN EJECUTIVO DEL DASHBOARD ONG'],
            [''],
            ['ONG:', $this->ong->nombre_ong ?? 'ONG'],
            ['Período de Análisis:'],
            ['Desde:', $this->fechaInicio->format('d/m/Y')],
            ['Hasta:', $this->fechaFin->format('d/m/Y')],
            [''],
            ['MÉTRICAS PRINCIPALES'],
            [''],
            ['Métrica', 'Valor Actual', 'Valor Anterior', 'Crecimiento %', 'Tendencia'],
            [
                'Eventos Activos',
                $metricas['eventos_activos'] ?? 0,
                '',
                '',
                ''
            ],
            [
                'Eventos Finalizados',
                $metricas['eventos_finalizados'] ?? 0,
                '',
                '',
                ''
            ],
            [
                'Total Reacciones',
                $metricas['total_reacciones'] ?? 0,
                $comparativas['reacciones']['anterior'] ?? 0,
                ($comparativas['reacciones']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['reacciones']['tendencia'] ?? 'stable')
            ],
            [
                'Total Compartidos',
                $metricas['total_compartidos'] ?? 0,
                $comparativas['compartidos']['anterior'] ?? 0,
                ($comparativas['compartidos']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['compartidos']['tendencia'] ?? 'stable')
            ],
            [
                'Total Voluntarios',
                $metricas['total_voluntarios'] ?? 0,
                $comparativas['voluntarios']['anterior'] ?? 0,
                ($comparativas['voluntarios']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['voluntarios']['tendencia'] ?? 'stable')
            ],
            [
                'Total Participantes',
                $metricas['total_participantes'] ?? 0,
                $comparativas['participantes']['anterior'] ?? 0,
                ($comparativas['participantes']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['participantes']['tendencia'] ?? 'stable')
            ],
        ];
    }

    public function title(): string
    {
        return '1-Resumen Ejecutivo';
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 25, 'B' => 20, 'C' => 20, 'D' => 15, 'E' => 15];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0C2B44']],
        ]);
        $sheet->getStyle('A10:E10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
        ]);
        return [];
    }

    private function getTendenciaIcono($tendencia)
    {
        return match($tendencia) {
            'up' => '↑',
            'down' => '↓',
            default => '→'
        };
    }
}

// Hoja 2: Eventos Detallados
class OngDashboardEventosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $eventos = $this->datos['listado_eventos'] ?? [];
        $array = [['Tipo', 'ID', 'Título', 'Fecha Inicio', 'Fecha Fin', 'Ubicación', 'Total Participantes', 'Estado']];
        
        // Separar por tipo
        $eventosRegulares = array_filter($eventos, fn($e) => ($e['tipo'] ?? 'evento') === 'evento');
        $megaEventos = array_filter($eventos, fn($e) => ($e['tipo'] ?? '') === 'mega_evento');
        
        // Eventos regulares
        foreach ($eventosRegulares as $evento) {
            $array[] = [
                'Evento',
                $evento['id'] ?? '',
                $evento['titulo'] ?? '',
                $evento['fecha_inicio'] ? Carbon::parse($evento['fecha_inicio'])->format('d/m/Y') : '',
                $evento['fecha_fin'] ? Carbon::parse($evento['fecha_fin'])->format('d/m/Y') : '',
                $evento['ubicacion'] ?? '',
                $evento['total_participantes'] ?? 0,
                $evento['estado'] ?? ''
            ];
        }
        
        // Mega eventos
        foreach ($megaEventos as $evento) {
            $array[] = [
                'Mega Evento',
                $evento['id'] ?? '',
                $evento['titulo'] ?? '',
                $evento['fecha_inicio'] ? Carbon::parse($evento['fecha_inicio'])->format('d/m/Y') : '',
                $evento['fecha_fin'] ? Carbon::parse($evento['fecha_fin'])->format('d/m/Y') : '',
                $evento['ubicacion'] ?? '',
                $evento['total_participantes'] ?? 0,
                $evento['estado'] ?? ''
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '2-Eventos Detallados';
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 10, 'C' => 40, 'D' => 15, 'E' => 15, 'F' => 20, 'G' => 15, 'H' => 15];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);
        return [];
    }
}

// Hoja 3: Tendencias Mensuales
class OngDashboardTendenciasSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $tendencias = $this->datos['tendencias_mensuales'] ?? [];
        $array = [['Mes', 'Total Participantes']];
        
        foreach ($tendencias as $mes => $total) {
            $array[] = [
                $mes,
                $total
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '3-Tendencias Mensuales';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 20, 'B' => 20]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17a2b8']],
        ]);
        return [];
    }
}

// Hoja 4: Reacciones y Compartidos
class OngDashboardReaccionesCompartidosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $comparativa = $this->datos['comparativa_eventos'] ?? [];
        $array = [['Evento', 'Reacciones', 'Compartidos', 'Total']];
        
        foreach ($comparativa as $evento) {
            $array[] = [
                $evento['titulo'] ?? '',
                $evento['reacciones'] ?? 0,
                $evento['compartidos'] ?? 0,
                ($evento['reacciones'] ?? 0) + ($evento['compartidos'] ?? 0)
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '4-Reacciones y Compartidos';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 40, 'B' => 15, 'C' => 15, 'D' => 15]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dc3545']],
        ]);
        return [];
    }
}

// Hoja 5: Inscripciones Completas
class OngDashboardInscripcionesSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $actividad = $this->datos['actividad_reciente'] ?? [];
        $array = [['Fecha', 'Reacciones', 'Compartidos', 'Inscripciones', 'Total']];
        
        foreach ($actividad as $fecha => $datos) {
            $array[] = [
                Carbon::parse($fecha)->format('d/m/Y'),
                $datos['reacciones'] ?? 0,
                $datos['compartidos'] ?? 0,
                $datos['inscripciones'] ?? 0,
                $datos['total'] ?? 0
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '5-Inscripciones';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 15]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17a2b8']],
        ]);
        return [];
    }
}

// Hoja 6: Top Eventos
class OngDashboardTopEventosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $topEventos = $this->datos['top_eventos'] ?? [];
        $array = [['#', 'Título', 'Reacciones', 'Compartidos', 'Inscripciones', 'Engagement Total']];
        
        $posicion = 1;
        foreach ($topEventos as $evento) {
            $array[] = [
                $posicion++,
                $evento['titulo'] ?? '',
                $evento['reacciones'] ?? 0,
                $evento['compartidos'] ?? 0,
                $evento['inscripciones'] ?? 0,
                $evento['engagement'] ?? 0
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '6-Top Eventos';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 10, 'B' => 40, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 20]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffc107']],
        ]);
        return [];
    }
}

// Hoja 7: Top Voluntarios
class OngDashboardTopVoluntariosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $topVoluntarios = $this->datos['top_voluntarios'] ?? [];
        $array = [['#', 'Nombre', 'Email', 'Eventos Participados', 'Horas Contribuidas']];
        
        $posicion = 1;
        foreach ($topVoluntarios as $voluntario) {
            $array[] = [
                $posicion++,
                $voluntario['nombre'] ?? '',
                $voluntario['email'] ?? '',
                $voluntario['eventos_participados'] ?? 0,
                $voluntario['horas_contribuidas'] ?? 0
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '7-Top Voluntarios';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 10, 'B' => 30, 'C' => 30, 'D' => 20, 'E' => 20]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);
        return [];
    }
}

// Hoja 8: Análisis por Estado
class OngDashboardAnalisisEstadoSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $distribucionEstados = $this->datos['distribucion_estados'] ?? [];
        $distribucionParticipantes = $this->datos['distribucion_participantes'] ?? [];
        
        $array = [
            ['DISTRIBUCIÓN DE ESTADOS DE EVENTOS'],
            [''],
            ['Estado', 'Cantidad', 'Porcentaje']
        ];
        
        $totalEstados = array_sum($distribucionEstados);
        foreach ($distribucionEstados as $estado => $cantidad) {
            $porcentaje = $totalEstados > 0 ? round(($cantidad / $totalEstados) * 100, 2) : 0;
            $array[] = [
                ucfirst($estado),
                $cantidad,
                $porcentaje . '%'
            ];
        }
        
        $array[] = ['', '', ''];
        $array[] = ['DISTRIBUCIÓN DE PARTICIPANTES POR ESTADO'];
        $array[] = ['Estado', 'Cantidad', 'Porcentaje'];
        
        $porEstado = $distribucionParticipantes['por_estado'] ?? [];
        $totalParticipantes = array_sum($porEstado);
        foreach ($porEstado as $estado => $cantidad) {
            $porcentaje = $totalParticipantes > 0 ? round(($cantidad / $totalParticipantes) * 100, 2) : 0;
            $array[] = [
                ucfirst($estado),
                $cantidad,
                $porcentaje . '%'
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '8-Análisis por Estado';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 25, 'B' => 15, 'C' => 15]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0C2B44']],
        ]);
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
        ]);
        $sheet->getStyle('A' . (count($this->array()) - count($this->datos['distribucion_participantes']['por_estado'] ?? []) - 1) . ':C' . (count($this->array()) - count($this->datos['distribucion_participantes']['por_estado'] ?? []) - 1))->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);
        return [];
    }
}

