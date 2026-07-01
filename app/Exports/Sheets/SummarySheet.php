<?php

namespace App\Exports\Sheets;

use App\Enums\RequestStatus;
use App\Models\Request as BuzonRequest;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummarySheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = []) {}

    public function title(): string
    {
        return 'Resumen';
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function array(): array
    {
        $filtered = BuzonRequest::query()->filter($this->filters);

        $filtrosAplicados = collect($this->filters)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->implode(' | ');

        return [
            ['Reporte de mensajes — Buzón Solariega', ''],
            ['Generado el', now()->format('d/m/Y H:i')],
            ['Filtros aplicados', $filtrosAplicados !== '' ? $filtrosAplicados : 'Ninguno'],
            ['', ''],
            ['Total de mensajes', (string) (clone $filtered)->count()],
            ['Recibidos', (string) (clone $filtered)->where('status', RequestStatus::Recibido)->count()],
            ['En revisión', (string) (clone $filtered)->where('status', RequestStatus::EnRevision)->count()],
            ['Atendidos', (string) (clone $filtered)->where('status', RequestStatus::Atendido)->count()],
            ['Cerrados', (string) (clone $filtered)->where('status', RequestStatus::Cerrado)->count()],
            ['Descartados', (string) (clone $filtered)->where('status', RequestStatus::Descartado)->count()],
            ['Con evidencia', (string) (clone $filtered)->where('has_evidence', true)->count()],
            ['Sin evidencia', (string) (clone $filtered)->where('has_evidence', false)->count()],
        ];
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:B1');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'D4AF37']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '171717'],
                ],
            ],
            'A2:A12' => ['font' => ['bold' => true]],
        ];
    }
}
