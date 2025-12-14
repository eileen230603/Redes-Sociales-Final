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
use App\Models\IntegranteExterno;
use Carbon\Carbon;

class DashboardExternoExport implements WithMultipleSheets
{
    protected $integranteExterno;
    protected $datos;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($integranteExterno, $datos, $fechaInicio, $fechaFin)
    {
        $this->integranteExterno = $integranteExterno;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function sheets(): array
    {
        return [
            new DashboardExternoResumenSheet($this->integranteExterno, $this->datos, $this->fechaInicio, $this->fechaFin),
            new DashboardExternoEventosSheet($this->datos),
            new DashboardExternoHistorialSheet($this->datos),
            new DashboardExternoTipoEventosSheet($this->datos),
            new DashboardExternoTopEventosSheet($this->datos),
        ];
    }
}

// Hoja 1: Resumen Ejecutivo
class DashboardExternoResumenSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $integranteExterno;
    protected $datos;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($integranteExterno, $datos, $fechaInicio, $fechaFin)
    {
        $this->integranteExterno = $integranteExterno;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function array(): array
    {
        $metricas = $this->datos['metricas'];
        $nombreCompleto = trim(($this->integranteExterno->nombres ?? '') . ' ' . ($this->integranteExterno->apellidos ?? ''));
        
        return [
            ['RESUMEN EJECUTIVO DEL DASHBOARD EXTERNO'],
            [''],
            ['Usuario:', $nombreCompleto],
            ['Email:', $this->integranteExterno->email ?? 'N/A'],
            ['Período de Análisis:'],
            ['Desde:', $this->fechaInicio->format('d/m/Y')],
            ['Hasta:', $this->fechaFin->format('d/m/Y')],
            [''],
            ['MÉTRICAS PRINCIPALES'],
            [''],
            ['Métrica', 'Valor'],
            [
                'Total Eventos Inscritos',
                $metricas['total_eventos_inscritos'] ?? 0
            ],
            [
                'Total Eventos Asistidos',
                $metricas['total_eventos_asistidos'] ?? 0
            ],
            [
                'Total Mega Eventos Inscritos',
                $metricas['total_mega_eventos_inscritos'] ?? 0
            ],
            [
                'Total Reacciones',
                $metricas['total_reacciones'] ?? 0
            ],
            [
                'Total Compartidos',
                $metricas['total_compartidos'] ?? 0
            ],
            [
                'Horas Acumuladas',
                $metricas['horas_acumuladas'] ?? 0
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
        return ['A' => 30, 'B' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0C2B44']],
        ]);
        $sheet->getStyle('A11:B11')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
        ]);
        return [];
    }
}

// Hoja 2: Eventos Detallados
class DashboardExternoEventosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $eventos = $this->datos['listado_eventos'] ?? [];
        $array = [['Título', 'Fecha Inicio', 'Fecha Fin', 'Ciudad', 'Tipo Evento', 'Estado', 'Asistió']];
        
        foreach ($eventos as $evento) {
            $array[] = [
                $evento['titulo'] ?? '',
                $evento['fecha_inicio'] ? Carbon::parse($evento['fecha_inicio'])->format('d/m/Y') : '',
                $evento['fecha_fin'] ? Carbon::parse($evento['fecha_fin'])->format('d/m/Y') : '',
                $evento['ciudad'] ?? '',
                $evento['tipo_evento'] ?? '',
                $evento['estado'] ?? '',
                $evento['asistio'] ? 'Sí' : 'No'
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
        return ['A' => 40, 'B' => 15, 'C' => 15, 'D' => 20, 'E' => 15, 'F' => 15, 'G' => 15];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);
        return [];
    }
}

// Hoja 3: Historial de Participación
class DashboardExternoHistorialSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $historial = $this->datos['historial_participacion'] ?? [];
        $array = [['Mes', 'Inscritos', 'Asistidos']];
        
        foreach ($historial as $mes => $datos) {
            $array[] = [
                $mes,
                $datos['inscritos'] ?? 0,
                $datos['asistidos'] ?? 0
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '3-Historial Participación';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 20, 'B' => 15, 'C' => 15]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17a2b8']],
        ]);
        return [];
    }
}

// Hoja 4: Tipo de Eventos
class DashboardExternoTipoEventosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $tipos = $this->datos['tipo_eventos'] ?? [];
        $array = [['Tipo de Evento', 'Cantidad', 'Porcentaje']];
        
        $total = array_sum($tipos);
        foreach ($tipos as $tipo => $cantidad) {
            $porcentaje = $total > 0 ? round(($cantidad / $total) * 100, 2) : 0;
            $array[] = [
                $tipo,
                $cantidad,
                $porcentaje . '%'
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '4-Tipo de Eventos';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 30, 'B' => 15, 'C' => 15]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dc3545']],
        ]);
        return [];
    }
}

// Hoja 5: Top Eventos
class DashboardExternoTopEventosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $topEventos = $this->datos['top_eventos'] ?? [];
        $array = [['#', 'Título', 'Reacciones', 'Compartidos', 'Total Interacciones']];
        
        $posicion = 1;
        foreach ($topEventos as $evento) {
            $array[] = [
                $posicion++,
                $evento['titulo'] ?? '',
                $evento['reacciones'] ?? 0,
                $evento['compartidos'] ?? 0,
                $evento['total'] ?? 0
            ];
        }

        return $array;
    }

    public function title(): string
    {
        return '5-Top Eventos';
    }

    public function headings(): array { return []; }
    public function columnWidths(): array { return ['A' => 10, 'B' => 40, 'C' => 15, 'D' => 15, 'E' => 20]; }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffc107']],
        ]);
        return [];
    }
}
