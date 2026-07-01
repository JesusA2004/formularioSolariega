<?php

namespace App\Exports\Sheets;

use App\Models\Request as BuzonRequest;
use App\Models\RequestAttachment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @implements WithMapping<RequestAttachment>
 */
class EvidenceSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = []) {}

    public function title(): string
    {
        return 'Evidencias';
    }

    /**
     * @return Collection<int, RequestAttachment>
     */
    public function collection(): Collection
    {
        $requestIds = BuzonRequest::query()
            ->filter($this->filters)
            ->pluck('id');

        return RequestAttachment::query()
            ->whereIn('request_id', $requestIds)
            ->with('request')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Folio',
            'Nombre del colaborador',
            'Nombre del archivo',
            'Tipo',
            'Peso',
            'Enlace de descarga (panel admin)',
        ];
    }

    /**
     * @param  RequestAttachment  $attachment
     * @return array<int, string|null>
     */
    public function map($attachment): array
    {
        /** @var BuzonRequest $request */
        $request = $attachment->request;

        return [
            $request->folio,
            $request->full_name ?? 'No proporcionado',
            $attachment->original_name,
            $attachment->mime_type ?? 'Desconocido',
            $attachment->humanSize(),
            route('solicitudes.adjuntos.download', [$request->id, $attachment->id]),
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
}
