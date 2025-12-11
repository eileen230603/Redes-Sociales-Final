<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithAutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class EventoDashboardExport implements WithMultipleSheets
{
    protected $evento;
    protected $datos;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($evento, $datos, $fechaInicio, $fechaFin)
    {
        $this->evento = $evento;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function sheets(): array
    {
        return [
            new EventoDashboardResumenSheet($this->evento, $this->datos, $this->fechaInicio, $this->fechaFin),
            new EventoDashboardReaccionesSheet($this->datos),
            new EventoDashboardCompartidosSheet($this->datos),
            new EventoDashboardInscripcionesSheet($this->datos),
            new EventoDashboardTopParticipantesSheet($this->datos),
        ];
    }
}

// Hoja 1: Resumen General
class EventoDashboardResumenSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $evento;
    protected $datos;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($evento, $datos, $fechaInicio, $fechaFin)
    {
        $this->evento = $evento;
        $this->datos = $datos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function array(): array
    {
        $metricas = $this->datos['metricas'];
        $comparativas = $this->datos['comparativas'];
        
        return [
            ['RESUMEN EJECUTIVO DEL EVENTO'],
            [''],
            ['Evento:', $this->evento->titulo],
            ['Fecha de Inicio:', $this->evento->fecha_inicio ? Carbon::parse($this->evento->fecha_inicio)->format('d/m/Y') : 'N/A'],
            ['Fecha de Fin:', $this->evento->fecha_fin ? Carbon::parse($this->evento->fecha_fin)->format('d/m/Y') : 'N/A'],
            ['Ubicación:', $this->evento->ubicacion ?? 'N/A'],
            ['Categoría:', $this->evento->categoria ?? 'N/A'],
            ['Estado:', $this->evento->estado ?? 'N/A'],
            [''],
            ['Período de Análisis:'],
            ['Desde:', $this->fechaInicio->format('d/m/Y')],
            ['Hasta:', $this->fechaFin->format('d/m/Y')],
            [''],
            ['MÉTRICAS PRINCIPALES'],
            [''],
            ['Métrica', 'Valor Actual', 'Valor Anterior', 'Crecimiento %', 'Tendencia'],
            [
                'Reacciones',
                $metricas['reacciones'] ?? 0,
                $comparativas['reacciones']['anterior'] ?? 0,
                ($comparativas['reacciones']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['reacciones']['tendencia'] ?? 'stable')
            ],
            [
                'Compartidos',
                $metricas['compartidos'] ?? 0,
                $comparativas['compartidos']['anterior'] ?? 0,
                ($comparativas['compartidos']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['compartidos']['tendencia'] ?? 'stable')
            ],
            [
                'Voluntarios',
                $metricas['voluntarios'] ?? 0,
                $comparativas['voluntarios']['anterior'] ?? 0,
                ($comparativas['voluntarios']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['voluntarios']['tendencia'] ?? 'stable')
            ],
            [
                'Participantes Total',
                $metricas['participantes_total'] ?? 0,
                $comparativas['participantes_total']['anterior'] ?? 0,
                ($comparativas['participantes_total']['crecimiento'] ?? 0) . '%',
                $this->getTendenciaIcono($comparativas['participantes_total']['tendencia'] ?? 'stable')
            ],
            [''],
            ['DISTRIBUCIÓN POR ESTADOS'],
            [''],
            ['Estado', 'Cantidad', 'Porcentaje'],
        ];
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Resumen General';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 20,
            'D' => 15,
            'E' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el título
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Estilo para encabezados de métricas
        $sheet->getStyle('A15:E15')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Estilo para encabezados de distribución
        $sheet->getStyle('A' . (count($this->array()) - 2) . ':C' . (count($this->array()) - 2))->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Zebra striping para filas de datos
        $lastRow = count($this->array());
        for ($i = 16; $i < $lastRow - 3; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]);
            }
        }

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

// Hoja 2: Reacciones Detalladas
class EventoDashboardReaccionesSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $reacciones = $this->datos['reacciones_detalladas'] ?? [];
        $array = [['ID', 'Nombre', 'Fecha']];
        
        foreach ($reacciones as $reaccion) {
            $array[] = [
                $reaccion->id ?? '',
                $reaccion->nombre ?? 'Usuario',
                Carbon::parse($reaccion->created_at)->format('d/m/Y H:i:s')
            ];
        }

        // Agregar totales
        $array[] = ['', 'TOTAL:', '=COUNT(A2:A' . (count($array)) . ')'];

        return $array;
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Fecha'];
    }

    public function title(): string
    {
        return 'Reacciones Detalladas';
    }

    public function columnWidths(): array
    {
        return ['A' => 10, 'B' => 30, 'C' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->array());
        
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'dc3545']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Zebra striping
        for ($i = 2; $i < $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:C{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]);
            }
        }

        // Estilo para totales
        $sheet->getStyle("B{$lastRow}:C{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFE5E5']
            ]
        ]);

        return [];
    }
}

// Hoja 3: Compartidos
class EventoDashboardCompartidosSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $compartidos = $this->datos['compartidos_detallados'] ?? [];
        $array = [['ID', 'Nombre', 'Plataforma', 'Fecha']];
        
        foreach ($compartidos as $compartido) {
            $array[] = [
                $compartido->id ?? '',
                $compartido->nombre ?? 'Usuario',
                $compartido->plataforma ?? 'N/A',
                Carbon::parse($compartido->created_at)->format('d/m/Y H:i:s')
            ];
        }

        $array[] = ['', '', 'TOTAL:', '=COUNT(A2:A' . (count($array)) . ')'];

        return $array;
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Plataforma', 'Fecha'];
    }

    public function title(): string
    {
        return 'Compartidos';
    }

    public function columnWidths(): array
    {
        return ['A' => 10, 'B' => 30, 'C' => 15, 'D' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->array());
        
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        for ($i = 2; $i < $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:D{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]);
            }
        }

        $sheet->getStyle("C{$lastRow}:D{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5F5ED']
            ]
        ]);

        return [];
    }
}

// Hoja 4: Inscripciones
class EventoDashboardInscripcionesSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $inscripciones = $this->datos['inscripciones_detalladas'] ?? [];
        $array = [['ID', 'Nombre', 'Estado', 'Tipo', 'Fecha']];
        
        foreach ($inscripciones as $inscripcion) {
            $array[] = [
                $inscripcion->id ?? '',
                $inscripcion->nombre ?? 'Participante',
                $inscripcion->estado ?? 'pendiente',
                $inscripcion->tipo ?? 'registrado',
                Carbon::parse($inscripcion->created_at)->format('d/m/Y H:i:s')
            ];
        }

        $array[] = ['', '', '', 'TOTAL:', '=COUNT(A2:A' . (count($array)) . ')'];

        return $array;
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Estado', 'Tipo', 'Fecha'];
    }

    public function title(): string
    {
        return 'Inscripciones';
    }

    public function columnWidths(): array
    {
        return ['A' => 10, 'B' => 30, 'C' => 15, 'D' => 15, 'E' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->array());
        
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17a2b8']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        for ($i = 2; $i < $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]);
            }
        }

        $sheet->getStyle("D{$lastRow}:E{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5F0F3']
            ]
        ]);

        return [];
    }
}

// Hoja 5: Top Participantes
class EventoDashboardTopParticipantesSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithAutoFilter
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $topParticipantes = $this->datos['top_participantes'] ?? [];
        $array = [['#', 'Nombre', 'Total Actividades']];
        
        $posicion = 1;
        foreach ($topParticipantes as $participante) {
            $array[] = [
                $posicion++,
                $participante['nombre'] ?? 'Participante',
                $participante['total_actividades'] ?? 0
            ];
        }

        return $array;
    }

    public function headings(): array
    {
        return ['#', 'Nombre', 'Total Actividades'];
    }

    public function title(): string
    {
        return 'Top Participantes';
    }

    public function columnWidths(): array
    {
        return ['A' => 10, 'B' => 40, 'C' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->array());
        
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ffc107']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:C{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFBF0']
                    ]
                ]);
            }
        }

        return [];
    }
}

