<?php

namespace App\Exports;

use App\Models\Evento;
use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Export class para reporte de eventos regulares en Excel
 */
class EventosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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

    /**
     * Obtener colección de eventos con participantes
     */
    public function collection()
    {
        $query = Evento::where('ong_id', $this->ongId)
            ->with(['participantes' => function($q) {
                $q->where('estado', 'aprobada');
            }]);

        // Aplicar filtros
        if (isset($this->filtros['fecha_inicio'])) {
            $query->where('fecha_inicio', '>=', $this->filtros['fecha_inicio']);
        }
        if (isset($this->filtros['fecha_fin'])) {
            $query->where('fecha_inicio', '<=', $this->filtros['fecha_fin']);
        }
        if (isset($this->filtros['categoria']) && $this->filtros['categoria'] !== '') {
            $query->where('tipo_evento', $this->filtros['categoria']);
        }
        if (isset($this->filtros['estado']) && $this->filtros['estado'] !== '') {
            $query->where('estado', $this->filtros['estado']);
        }

        return $query->get();
    }

    /**
     * Encabezados de columnas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Categoría',
            'Estado',
            'Fecha Inicio',
            'Fecha Fin',
            'Ubicación',
            'Participantes',
            'Capacidad',
            'Tasa Ocupación %',
            'Duración (días)',
        ];
    }

    /**
     * Mapear cada fila
     */
    public function map($evento): array
    {
        $participantes = $evento->participantes->count();
        $capacidad = $evento->capacidad_maxima ?? 0;
        $tasaOcupacion = $capacidad > 0 
            ? round(($participantes / $capacidad) * 100, 2) 
            : 0;

        $duracion = 0;
        if ($evento->fecha_inicio && $evento->fecha_fin) {
            $duracion = $evento->fecha_inicio->diffInDays($evento->fecha_fin);
        }

        return [
            $evento->id,
            $evento->titulo,
            ucfirst($evento->tipo_evento ?? 'N/A'),
            ucfirst($evento->estado ?? 'N/A'),
            $evento->fecha_inicio?->format('d/m/Y') ?? 'N/A',
            $evento->fecha_fin?->format('d/m/Y') ?? 'N/A',
            $evento->ciudad ?? $evento->direccion ?? 'N/A',
            $participantes,
            $capacidad,
            $tasaOcupacion,
            $duracion,
        ];
    }

    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para encabezados
        $sheet->getStyle('A1:K1')->applyFromArray([
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
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Bordes para todas las celdas con datos
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:K{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E9ECEF'],
                ],
            ],
        ]);

        // Alternar colores en filas
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }
        }

        return $sheet;
    }

    /**
     * Ancho de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 40,  // Título
            'C' => 15,  // Categoría
            'D' => 15,  // Estado
            'E' => 15,  // Fecha Inicio
            'F' => 15,  // Fecha Fin
            'G' => 30,  // Ubicación
            'H' => 15,  // Participantes
            'I' => 12,  // Capacidad
            'J' => 18,  // Tasa Ocupación
            'K' => 15,  // Duración
        ];
    }
}

