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
 * Export class para Reporte 3: Participación y Colaboración
 */
class ParticipacionColaboracionExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $datos = $this->reportService->getParticipacionColaboracion($this->ongId, $this->filtros);
        
        $collection = collect();
        
        // Agregar top empresas
        foreach ($datos['top_empresas'] as $empresa) {
            $collection->push([
                'Tipo' => 'Empresa Patrocinadora',
                'Nombre' => $empresa->nombre,
                'Total Eventos' => $empresa->total_eventos,
                'Total Patrocinios' => $empresa->total_patrocinios,
            ]);
        }

        // Agregar top voluntarios
        foreach ($datos['top_voluntarios'] as $voluntario) {
            $collection->push([
                'Tipo' => 'Voluntario',
                'Nombre' => $voluntario->nombre,
                'Total Eventos' => $voluntario->total_eventos,
                'Total Participaciones' => $voluntario->total_participaciones,
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'Nombre',
            'Total Eventos',
            'Total Patrocinios/Participaciones'
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
                'startColor' => ['rgb' => '0C2B44'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);

        return $sheet;
    }

    public function title(): string
    {
        return 'Participación y Colaboración';
    }
}

