<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Log;

/**
 * Export class para Reporte: Resumen Ejecutivo Consolidado
 * Exporta a Excel tipo Power BI con múltiples hojas y gráficas
 */
class MegaEventosResumenExport implements WithMultipleSheets
{
    protected $ongId;
    protected $filtros;
    protected $reportService;
    protected $datos;

    public function __construct(int $ongId, array $filtros = [])
    {
        try {
            $this->ongId = $ongId;
            $this->filtros = $filtros;
            $this->reportService = new ReportService();
            $this->datos = $this->reportService->getResumenEjecutivo($this->ongId, $this->filtros);
            
            // Validar que los datos estén correctos
            if (!is_array($this->datos)) {
                throw new \Exception('Los datos del reporte no son válidos');
            }
            
            // Asegurar que las claves necesarias existan
            if (!isset($this->datos['totales'])) {
                $this->datos['totales'] = [];
            }
            if (!isset($this->datos['kpis'])) {
                $this->datos['kpis'] = [];
            }
        } catch (\Throwable $e) {
            Log::error('Error en constructor de MegaEventosResumenExport:', [
                'error' => $e->getMessage(),
                'ongId' => $ongId,
                'filtros' => $filtros
            ]);
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new ResumenEjecutivoSheet($this->datos),
            new KPIsSheet($this->datos),
            new CategoriasSheet($this->datos),
            new EstadosSheet($this->datos),
            new ComparacionSheet($this->datos),
        ];
    }
}

/**
 * Hoja 1: Resumen Ejecutivo
 */
class ResumenEjecutivoSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        return [
            ['RESUMEN EJECUTIVO CONSOLIDADO'],
            ['Generado el ' . now()->format('d/m/Y H:i:s')],
            [''],
            ['=== TOTALES CONSOLIDADOS ==='],
            ['Total de Eventos (General)', $this->datos['totales']['total_eventos'] ?? 0, 'Eventos regulares + Mega eventos'],
            ['Eventos Regulares', $this->datos['totales']['total_eventos_regulares'] ?? 0, ''],
            ['Mega Eventos', $this->datos['totales']['total_mega_eventos'] ?? 0, ''],
            [''],
            ['=== KPIs PRINCIPALES ==='],
            ['Eventos Finalizados', $this->datos['kpis']['eventos_finalizados'] ?? 0, ''],
            ['Eventos Activos', $this->datos['kpis']['eventos_activos'] ?? 0, ''],
            ['Eventos Cancelados', $this->datos['kpis']['eventos_cancelados'] ?? 0, ''],
            ['Tasa de Finalización', ($this->datos['kpis']['tasa_finalizacion'] ?? 0) . '%', ''],
            ['Tasa de Cancelación', ($this->datos['kpis']['tasa_cancelacion'] ?? 0) . '%', ''],
            ['Total Participantes', $this->datos['kpis']['total_participantes'] ?? 0, 'Eventos regulares + Mega eventos'],
            ['Participantes Eventos Regulares', $this->datos['kpis']['total_participantes_eventos'] ?? 0, ''],
            ['Participantes Mega Eventos', $this->datos['kpis']['total_participantes_mega'] ?? 0, ''],
        ];
    }

    public function title(): string
    {
        return 'Resumen Ejecutivo';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo del título
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:C1');

        // Estilo de fecha
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6C757D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A2:C2');

        // Estilo de secciones
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0C2B44']],
        ]);

        // Estilo de datos
        $dataRows = count($this->array());
        for ($row = 5; $row <= $dataRows && $row <= 20; $row++) {
            try {
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E9ECEF']],
                    ],
                ]);
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '00A36C']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            } catch (\Exception $e) {
                // Ignorar errores de celdas que no existen
                continue;
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 20,
            'C' => 40,
        ];
    }
}

/**
 * Hoja 2: KPIs Detallados
 */
class KPIsSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        return [
            ['INDICADORES CLAVE DE RENDIMIENTO (KPIs)'],
            [''],
            ['Métrica', 'Valor', 'Descripción'],
            ['Total Eventos', $this->datos['totales']['total_eventos'] ?? 0, 'Suma de eventos regulares y mega eventos'],
            ['Eventos Regulares', $this->datos['totales']['total_eventos_regulares'] ?? 0, 'Eventos estándar de la ONG'],
            ['Mega Eventos', $this->datos['totales']['total_mega_eventos'] ?? 0, 'Eventos de gran escala'],
            ['Eventos Finalizados', $this->datos['kpis']['eventos_finalizados'] ?? 0, 'Eventos completados exitosamente'],
            ['Eventos Activos', $this->datos['kpis']['eventos_activos'] ?? 0, 'Eventos en curso o activos'],
            ['Eventos Cancelados', $this->datos['kpis']['eventos_cancelados'] ?? 0, 'Eventos cancelados'],
            ['Tasa de Finalización', ($this->datos['kpis']['tasa_finalizacion'] ?? 0) . '%', 'Porcentaje de eventos finalizados'],
            ['Tasa de Cancelación', ($this->datos['kpis']['tasa_cancelacion'] ?? 0) . '%', 'Porcentaje de eventos cancelados'],
            ['Total Participantes', $this->datos['kpis']['total_participantes'] ?? 0, 'Total consolidado de participantes'],
            ['Participantes Eventos Regulares', $this->datos['kpis']['total_participantes_eventos'] ?? 0, ''],
            ['Participantes Mega Eventos', $this->datos['kpis']['total_participantes_mega'] ?? 0, ''],
        ];
    }

    public function title(): string
    {
        return 'KPIs';
    }

    public function styles(Worksheet $sheet)
    {
        // Header
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:C1');

        // Encabezados de tabla
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Datos
        $dataRows = count($this->array());
        for ($row = 4; $row <= $dataRows && $row <= 20; $row++) {
            try {
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0C2B44']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                if ($row % 2 == 0) {
                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                    ]);
                }
            } catch (\Exception $e) {
                // Ignorar errores de celdas que no existen
                continue;
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 50,
        ];
    }
}

/**
 * Hoja 3: Distribución por Categoría
 */
class CategoriasSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $data = [
            ['DISTRIBUCIÓN POR CATEGORÍA'],
            [''],
            ['Categoría', 'Cantidad', 'Porcentaje'],
        ];

        $porCategoria = $this->datos['totales']['por_categoria'] ?? [];
        $totalEventos = $this->datos['totales']['total_eventos'] ?? 1;
        
        if (empty($porCategoria)) {
            $data[] = ['No hay datos disponibles', '', ''];
        } else {
            foreach ($porCategoria as $categoria => $cantidad) {
                $porcentaje = $totalEventos > 0 ? round(($cantidad / $totalEventos) * 100, 2) : 0;
                $data[] = [
                    ucfirst($categoria),
                    $cantidad,
                    $porcentaje . '%'
                ];
            }
        }

        return $data;
    }

    public function title(): string
    {
        return 'Por Categoría';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:C1');

        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);

        $porCategoria = $this->datos['totales']['por_categoria'] ?? [];
        $lastRow = min(count($porCategoria) + 3, 50);
        for ($row = 4; $row <= $lastRow; $row++) {
            try {
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getStyle("C{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '00A36C']],
                ]);
            } catch (\Exception $e) {
                // Ignorar errores de celdas que no existen
                continue;
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 15,
        ];
    }
}

/**
 * Hoja 4: Distribución por Estado
 */
class EstadosSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $data = [
            ['DISTRIBUCIÓN POR ESTADO'],
            [''],
            ['Estado', 'Cantidad', 'Porcentaje'],
        ];

        $porEstado = $this->datos['totales']['por_estado'] ?? [];
        $totalEventos = $this->datos['totales']['total_eventos'] ?? 1;
        
        if (empty($porEstado)) {
            $data[] = ['No hay datos disponibles', '', ''];
        } else {
            foreach ($porEstado as $estado => $cantidad) {
                $porcentaje = $totalEventos > 0 ? round(($cantidad / $totalEventos) * 100, 2) : 0;
                $data[] = [
                    ucfirst(str_replace('_', ' ', $estado)),
                    $cantidad,
                    $porcentaje . '%'
                ];
            }
        }

        return $data;
    }

    public function title(): string
    {
        return 'Por Estado';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:C1');

        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
        ]);

        $porEstado = $this->datos['totales']['por_estado'] ?? [];
        $lastRow = min(count($porEstado) + 3, 50);
        for ($row = 4; $row <= $lastRow; $row++) {
            try {
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getStyle("C{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '00A36C']],
                ]);
            } catch (\Exception $e) {
                // Ignorar errores de celdas que no existen
                continue;
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 15,
        ];
    }
}

/**
 * Hoja 5: Comparación Eventos Regulares vs Mega Eventos
 */
class ComparacionSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        return [
            ['COMPARACIÓN: EVENTOS REGULARES VS MEGA EVENTOS'],
            [''],
            ['Métrica', 'Eventos Regulares', 'Mega Eventos', 'Total'],
            ['Total Eventos', $this->datos['totales']['total_eventos_regulares'] ?? 0, $this->datos['totales']['total_mega_eventos'] ?? 0, $this->datos['totales']['total_eventos'] ?? 0],
            ['Participantes', $this->datos['kpis']['total_participantes_eventos'] ?? 0, $this->datos['kpis']['total_participantes_mega'] ?? 0, $this->datos['kpis']['total_participantes'] ?? 0],
            ['Promedio Participantes/Evento', 
                $this->datos['totales']['total_eventos_regulares'] > 0 
                    ? round(($this->datos['kpis']['total_participantes_eventos'] ?? 0) / $this->datos['totales']['total_eventos_regulares'], 2)
                    : 0,
                $this->datos['totales']['total_mega_eventos'] > 0
                    ? round(($this->datos['kpis']['total_participantes_mega'] ?? 0) / $this->datos['totales']['total_mega_eventos'], 2)
                    : 0,
                $this->datos['totales']['total_eventos'] > 0
                    ? round(($this->datos['kpis']['total_participantes'] ?? 0) / $this->datos['totales']['total_eventos'], 2)
                    : 0
            ],
        ];
    }

    public function title(): string
    {
        return 'Comparación';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2B44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:D1');

        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A36C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $dataRows = min(count($this->array()), 20);
        for ($row = 4; $row <= $dataRows; $row++) {
            try {
                $sheet->getStyle("B{$row}:D{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                if ($row % 2 == 0) {
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                    ]);
                }
            } catch (\Exception $e) {
                // Ignorar errores de celdas que no existen
                continue;
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
            'D' => 20,
        ];
    }
}
