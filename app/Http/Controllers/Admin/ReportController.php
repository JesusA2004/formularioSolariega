<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LabeledEnum;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Exports\RequestsExcelExport;
use App\Exports\RequestsExport;
use App\Http\Controllers\Controller;
use App\Models\Request as BuzonRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const FILTER_KEYS = [
        'search', 'status', 'request_type', 'department', 'has_evidence', 'date_from', 'date_to',
    ];

    public function index(HttpRequest $request): Response
    {
        $filters = $request->only(self::FILTER_KEYS);
        $filtered = BuzonRequest::query()->filter($filters);

        return Inertia::render('admin/reportes/Index', [
            'filters' => $filters,
            'options' => [
                'requestTypes' => collect(RequestType::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
                'departments' => BuzonRequest::query()
                    ->select('department')
                    ->distinct()
                    ->orderBy('department')
                    ->pluck('department')
                    ->map(fn ($department) => ['value' => $department, 'label' => $department])
                    ->all(),
                'statuses' => collect(RequestStatus::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
            ],
            'summary' => [
                'total' => (clone $filtered)->count(),
                'pendientes' => (clone $filtered)->whereIn('status', [RequestStatus::Recibido, RequestStatus::EnRevision])->count(),
                'con_evidencia' => (clone $filtered)->where('has_evidence', true)->count(),
                'cerrados' => (clone $filtered)->where('status', RequestStatus::Cerrado)->count(),
            ],
            'charts' => [
                'byType' => $this->countByEnum($filtered, 'request_type', RequestType::cases()),
                'byDepartment' => $this->countByDepartment($filtered),
                'byStatus' => $this->countByEnum($filtered, 'status', RequestStatus::cases()),
                'byEvidence' => $this->countByEvidence($filtered),
            ],
            'requests' => (clone $filtered)
                ->latest()
                ->paginate(20)
                ->withQueryString()
                ->through(fn (BuzonRequest $r) => [
                    'id' => $r->id,
                    'folio' => $r->folio,
                    'request_type_label' => $r->request_type->label(),
                    'department_label' => $r->department,
                    'full_name' => $r->full_name,
                    'contact_info' => $r->contact_info,
                    'status_label' => $r->status->label(),
                    'has_evidence' => $r->has_evidence,
                    'created_at' => $r->created_at?->toIso8601String(),
                ]),
        ]);
    }

    public function exportExcel(HttpRequest $request): BinaryFileResponse
    {
        $filters = $request->only(self::FILTER_KEYS);

        return Excel::download(new RequestsExcelExport($filters), 'reporte-buzon-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportCsv(HttpRequest $request): BinaryFileResponse
    {
        $filters = $request->only(self::FILTER_KEYS);

        return Excel::download(new RequestsExport($filters), 'reporte-buzon-'.now()->format('Y-m-d').'.csv', ExcelFormat::CSV);
    }

    public function exportPdf(HttpRequest $request): StreamedResponse
    {
        $filters = $request->only(self::FILTER_KEYS);

        $requests = BuzonRequest::query()
            ->with('attachments')
            ->filter($filters)
            ->latest()
            ->get();

        $generatedAt = Date::now();
        $generatedAt->locale('es');

        $pdf = Pdf::loadView('reports.pdf', [
            'requests' => $requests,
            'filters' => $filters,
            'generatedAt' => $generatedAt->isoFormat('D [de] MMMM [de] YYYY, HH:mm'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            'reporte-buzon-'.now()->format('Y-m-d').'.pdf'
        );
    }

    /**
     * @param  Builder<BuzonRequest>  $query
     * @param  array<int, LabeledEnum>  $cases
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByEnum(Builder $query, string $column, array $cases): array
    {
        $counts = (clone $query)
            ->select($column)
            ->selectRaw('count(*) as total')
            ->groupBy($column)
            ->pluck('total', $column);

        return collect($cases)
            ->map(fn (LabeledEnum $case) => [
                'label' => $case->label(),
                'value' => (int) ($counts[$case->value] ?? 0),
                'key' => (string) $case->value,
            ])
            ->all();
    }

    /**
     * @param  Builder<BuzonRequest>  $query
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByDepartment(Builder $query): array
    {
        return (clone $query)
            ->select('department')
            ->selectRaw('count(*) as total')
            ->groupBy('department')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                /** @var object{department: string, total: int} $row */
                return [
                    'label' => $row->department,
                    'value' => (int) $row->total,
                    'key' => $row->department,
                ];
            })
            ->all();
    }

    /**
     * @param  Builder<BuzonRequest>  $query
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByEvidence(Builder $query): array
    {
        return [
            [
                'label' => 'Con evidencia',
                'value' => (clone $query)->where('has_evidence', true)->count(),
                'key' => '1',
            ],
            [
                'label' => 'Sin evidencia',
                'value' => (clone $query)->where('has_evidence', false)->count(),
                'key' => '0',
            ],
        ];
    }
}
