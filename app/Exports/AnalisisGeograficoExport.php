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
 * Export class para Reporte 4: Análisis Geográfico
 */
class AnalisisGeograficoExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $datos = $this->reportService->getAnalisisGeografico($this->ongId, $this->filtros);
        
        $collection = collect();
        
        // Agregar ciudades
        foreach ($datos['ciudades'] as $ciudad) {
            $collection->push([
                'Tipo' => 'Ciudad',
                'Ubicación' => $ciudad['ciudad'],
                'Cantidad Eventos' => $ciudad['cantidad_eventos'],
                'Porcentaje' => $ciudad['porcentaje'] . '%',
            ]);
        }

        // Agregar departamentos
        foreach ($datos['departamentos'] as $departamento) {
            $collection->push([
                'Tipo' => 'Departamento',
                'Ubicación' => $departamento['departamento'],
                'Cantidad Eventos' => $departamento['cantidad_eventos'],
                'Porcentaje' => $departamento['porcentaje'] . '%',
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'Ubicación',
            'Cantidad Eventos',
            'Porcentaje'
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

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);

        return $sheet;
    }

    public function title(): string
    {
        return 'Análisis Geográfico';
    }
}

