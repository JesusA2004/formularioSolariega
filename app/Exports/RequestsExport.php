<?php

namespace App\Exports;

use App\Enums\Department;
use App\Models\Request as BuzonRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @implements WithMapping<BuzonRequest>
 */
class RequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
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
            ->filter($this->filters)
            ->latest()
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Folio',
            'Tipo de mensaje',
            'Anónima',
            'Nombre',
            'Departamento',
            'Ubicación',
            'Fecha aproximada del hecho',
            'Urgencia',
            'Estado',
            'Desea seguimiento',
            'Contacto',
            'Tiene evidencia',
            'Descripción',
            'Personas involucradas',
            'Fecha de envío',
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
            $request->folio,
            $request->request_type->label(),
            $request->is_anonymous ? 'Sí' : 'No',
            $request->is_anonymous ? '' : $request->full_name,
            Department::tryFrom($request->department)?->label() ?? $request->department,
            $request->location,
            $request->incident_date?->format('d/m/Y') ?? '',
            $request->urgency_level->label(),
            $request->status->label(),
            $request->wants_follow_up ? 'Sí' : 'No',
            $request->is_anonymous ? '' : $request->contact_info,
            $request->has_evidence ? 'Sí' : 'No',
            $request->description,
            $request->involved_people,
            $request->created_at?->format('d/m/Y H:i') ?? '',
            $request->closed_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
