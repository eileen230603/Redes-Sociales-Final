<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Export class para Reporte 2: Análisis Temporal
 */
class AnalisisTemporalExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $ongId;
    protected $filtros;
    protected $reportService;

    public function __construct(int $ongId, array $filtros = [])
    {
        $this->ongId = $ongId;
        $this->filtros = $filtros;
        $this->reportService = new ReportService();
    }

    public function collection()
    {
        $datos = $this->reportService->getAnalisisTemporal($this->ongId, $this->filtros);
        
        return collect($datos['tendencias'])->map(function ($tendencia) {
            return [
                'Mes' => $tendencia['mes'],
                'Año Actual' => $tendencia['cantidad_actual'],
                'Año Anterior' => $tendencia['cantidad_anterior'],
                'Crecimiento %' => $tendencia['crecimiento_porcentual'] . '%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Mes',
            'Año Actual',
            'Año Anterior',
            'Crecimiento %'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A36C'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        return $sheet;
    }

    public function title(): string
    {
        return 'Análisis Temporal';
    }
}

