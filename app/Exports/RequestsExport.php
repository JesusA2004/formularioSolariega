<?php

namespace App\Exports;

use App\Models\Request as BuzonRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @implements WithMapping<BuzonRequest>
 */
class RequestsExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private const COLUMN_COUNT = 15;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = []) {}

    /**
     * @return Collection<int, BuzonRequest>
     */
    public function collection(): Collection
    {
        return BuzonRequest::query()
            ->withCount('attachments')
            ->with('attachments')
            ->filter($this->filters)
            ->latest()
            ->get();
    }

    public function title(): string
    {
        return 'Mensajes';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Fecha de envío',
            'Nombre completo',
            'Contacto',
            'Área o departamento',
            'Tipo de mensaje',
            'Fecha aproximada',
            'Personas relacionadas',
            'Mensaje',
            'Estado',
            'Tiene evidencia',
            'Cantidad de archivos',
            'Nombres de archivos',
            'Notas internas',
            'Fecha de revisión',
            'Fecha de cierre',
        ];
    }

    /**
     * @param  BuzonRequest  $request
     * @return array<int, string|null>
     */
    public function map($request): array
    {
        return [
            $request->created_at?->format('d/m/Y H:i') ?? '',
            $request->full_name ?? 'No proporcionado',
            $request->contact_info ?? 'No proporcionado',
            $request->department,
            $request->request_type->label(),
            $request->incident_date?->format('d/m/Y') ?? '',
            $request->involved_people ?? '',
            $request->description,
            $request->status->label(),
            $request->has_evidence ? 'Sí' : 'No',
            (string) $request->attachments_count,
            $request->attachments->pluck('original_name')->implode(', '),
            $request->internal_notes ?? '',
            $request->reviewed_at?->format('d/m/Y H:i') ?? '',
            $request->closed_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'D4AF37']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '171717'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $lastColumn = Coordinate::stringFromColumnIndex(self::COLUMN_COUNT);
                $event->sheet->getDelegate()->freezePane('A2');
                $event->sheet->getDelegate()->setAutoFilter("A1:{$lastColumn}1");
            },
        ];
    }
}
