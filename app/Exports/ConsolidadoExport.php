<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Export class para reporte consolidado con múltiples sheets
 */
class ConsolidadoExport implements WithMultipleSheets
{
    protected $ongId;
    protected $filtros;

    public function __construct(int $ongId, array $filtros = [])
    {
        $this->ongId = $ongId;
        $this->filtros = $filtros;
    }

    /**
     * Retornar array de sheets
     */
    public function sheets(): array
    {
        return [
            new EventosExport($this->ongId, $this->filtros),
            new MegaEventosExport($this->ongId, $this->filtros),
            new ResumenConsolidadoExport($this->ongId, $this->filtros),
        ];
    }
}

/**
 * Sheet de resumen consolidado
 */
class ResumenConsolidadoExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $ongId;
    protected $filtros;
    protected $reportService;

    public function __construct(int $ongId, array $filtros = [])
    {
        $this->ongId = $ongId;
        $this->filtros = $filtros;
        $this->reportService = new \App\Services\ReportService();
    }

    public function title(): string
    {
        return 'Resumen Consolidado';
    }

    public function headings(): array
    {
        return [
            'Métrica',
            'Eventos Regulares',
            'Mega Eventos',
            'Total General',
        ];
    }

    public function array(): array
    {
        $metricas = $this->reportService->getConsolidadoMetrics($this->ongId, $this->filtros);
        
        return [
            ['Total Eventos', $metricas['metricas_eventos']['total_eventos'], $metricas['metricas_mega_eventos']['total_mega_eventos'], $metricas['total_eventos_general']],
            ['Total Participantes', $metricas['metricas_eventos']['total_participantes'], $metricas['metricas_mega_eventos']['total_participantes'], $metricas['total_participantes_general']],
            ['Eventos Activos', $metricas['metricas_eventos']['eventos_activos'], $metricas['metricas_mega_eventos']['mega_eventos_activos'], $metricas['metricas_eventos']['eventos_activos'] + $metricas['metricas_mega_eventos']['mega_eventos_activos']],
            ['Promedio Participantes', $metricas['metricas_eventos']['promedio_participantes'], $metricas['metricas_mega_eventos']['promedio_participantes'], 'N/A'],
            ['Tasa Ocupación Promedio', $metricas['metricas_eventos']['tasa_ocupacion_promedio'] . '%', $metricas['metricas_mega_eventos']['tasa_ocupacion_promedio'] . '%', 'N/A'],
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0C2B44'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        return $sheet;
    }
}

