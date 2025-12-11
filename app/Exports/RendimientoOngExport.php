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
 * Export class para Reporte 5: Rendimiento por ONG
 */
class RendimientoOngExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $datos = $this->reportService->getRendimientoOng($this->ongId, $this->filtros);
        
        $collection = collect();
        
        // Agregar datos de la ONG actual
        $collection->push([
            'ONG' => $datos['ong_actual']['nombre'] ?? 'ONG Actual',
            'Total Eventos' => $datos['ong_actual']['total_eventos'],
            'Eventos Finalizados' => $datos['ong_actual']['eventos_finalizados'],
            'Tasa Finalización' => $datos['ong_actual']['tasa_finalizacion'] . '%',
            'Promedio Asistentes' => $datos['ong_actual']['promedio_asistentes'],
            'Posición Ranking' => $datos['ong_actual']['posicion_ranking'] ?? 'N/A',
        ]);

        // Agregar ranking de otras ONGs
        foreach ($datos['ranking_ongs'] as $ong) {
            $collection->push([
                'ONG' => $ong['nombre'],
                'Total Eventos' => $ong['total_eventos'],
                'Eventos Finalizados' => $ong['eventos_finalizados'],
                'Tasa Finalización' => $ong['tasa_finalizacion'] . '%',
                'Promedio Asistentes' => 'N/A',
                'Posición Ranking' => 'N/A',
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [
            'ONG',
            'Total Eventos',
            'Eventos Finalizados',
            'Tasa Finalización',
            'Promedio Asistentes',
            'Posición Ranking'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);

        return $sheet;
    }

    public function title(): string
    {
        return 'Rendimiento por ONG';
    }
}

