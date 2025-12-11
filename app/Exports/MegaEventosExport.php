<?php

namespace App\Exports;

use App\Models\MegaEvento;
use Illuminate\Support\Facades\DB;
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
 * Export class para reporte de mega eventos en Excel
 */
class MegaEventosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $ongId;
    protected $filtros;

    public function __construct(int $ongId, array $filtros = [])
    {
        $this->ongId = $ongId;
        $this->filtros = $filtros;
    }

    /**
     * Obtener colección de mega eventos
     */
    public function collection()
    {
        $query = MegaEvento::where('ong_organizadora_principal', $this->ongId);

        // Aplicar filtros
        if (isset($this->filtros['fecha_inicio'])) {
            $query->where('fecha_creacion', '>=', $this->filtros['fecha_inicio']);
        }
        if (isset($this->filtros['fecha_fin'])) {
            $query->where('fecha_creacion', '<=', $this->filtros['fecha_fin']);
        }
        if (isset($this->filtros['categoria']) && $this->filtros['categoria'] !== '') {
            $query->where('categoria', $this->filtros['categoria']);
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
            'Público',
        ];
    }

    /**
     * Mapear cada fila
     */
    public function map($megaEvento): array
    {
        // Contar participantes
        $participantesRegistrados = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $megaEvento->mega_evento_id)
            ->where('activo', true)
            ->count();
        
        $participantesNoRegistrados = DB::table('mega_evento_participantes_no_registrados')
            ->where('mega_evento_id', $megaEvento->mega_evento_id)
            ->where('estado', '!=', 'rechazada')
            ->count();
        
        $participantes = $participantesRegistrados + $participantesNoRegistrados;
        $capacidad = $megaEvento->capacidad_maxima ?? 0;
        $tasaOcupacion = $capacidad > 0 
            ? round(($participantes / $capacidad) * 100, 2) 
            : 0;

        $duracion = 0;
        if ($megaEvento->fecha_inicio && $megaEvento->fecha_fin) {
            $duracion = $megaEvento->fecha_inicio->diffInDays($megaEvento->fecha_fin);
        }

        return [
            $megaEvento->mega_evento_id,
            $megaEvento->titulo,
            ucfirst($megaEvento->categoria ?? 'N/A'),
            ucfirst($megaEvento->estado ?? 'N/A'),
            $megaEvento->fecha_inicio?->format('d/m/Y') ?? 'N/A',
            $megaEvento->fecha_fin?->format('d/m/Y') ?? 'N/A',
            $megaEvento->ubicacion ?? 'N/A',
            $participantes,
            $capacidad,
            $tasaOcupacion,
            $duracion,
            $megaEvento->es_publico ? 'Sí' : 'No',
        ];
    }

    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para encabezados
        $sheet->getStyle('A1:L1')->applyFromArray([
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
        $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([
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
                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
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
            'L' => 12,  // Público
        ];
    }
}

