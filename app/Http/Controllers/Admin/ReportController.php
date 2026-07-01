<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LabeledEnum;
use App\Enums\Department;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\UrgencyLevel;
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
        'search', 'status', 'request_type', 'urgency_level', 'department', 'is_anonymous', 'has_evidence', 'date_from', 'date_to',
    ];

    public function index(HttpRequest $request): Response
    {
        $filters = $request->only(self::FILTER_KEYS);
        $filtered = BuzonRequest::query()->filter($filters);

        return Inertia::render('admin/reportes/Index', [
            'filters' => $filters,
            'options' => [
                'requestTypes' => collect(RequestType::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
                'departments' => collect(Department::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
                'urgencyLevels' => collect(UrgencyLevel::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
                'statuses' => collect(RequestStatus::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
            ],
            'summary' => [
                'total' => (clone $filtered)->count(),
                'criticas' => (clone $filtered)->where('urgency_level', UrgencyLevel::Critico)->count(),
                'anonimas' => (clone $filtered)->where('is_anonymous', true)->count(),
                'con_evidencia' => (clone $filtered)->where('has_evidence', true)->count(),
            ],
            'charts' => [
                'byType' => $this->countBy($filtered, 'request_type', RequestType::cases()),
                'byUrgency' => $this->countBy($filtered, 'urgency_level', UrgencyLevel::cases()),
                'byDepartment' => $this->countBy($filtered, 'department', Department::cases()),
                'byStatus' => $this->countBy($filtered, 'status', RequestStatus::cases()),
            ],
            'requests' => (clone $filtered)
                ->latest()
                ->paginate(20)
                ->withQueryString()
                ->through(fn (BuzonRequest $r) => [
                    'id' => $r->id,
                    'folio' => $r->folio,
                    'request_type_label' => $r->request_type->label(),
                    'department_label' => Department::tryFrom($r->department)?->label() ?? $r->department,
                    'location' => $r->location,
                    'urgency_level_label' => $r->urgency_level->label(),
                    'status_label' => $r->status->label(),
                    'is_anonymous' => $r->is_anonymous,
                    'has_evidence' => $r->has_evidence,
                    'created_at' => $r->created_at?->toIso8601String(),
                ]),
        ]);
    }

    public function exportExcel(HttpRequest $request): BinaryFileResponse
    {
        $filters = $request->only(self::FILTER_KEYS);

        return Excel::download(new RequestsExport($filters), 'reporte-buzon-'.now()->format('Y-m-d').'.xlsx');
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
            ->filter($filters)
            ->latest()
            ->get();

        $generatedAt = Date::now();
        $generatedAt->locale('es');

        $pdf = Pdf::loadView('reports.pdf', [
            'requests' => $requests,
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
     * @return array<int, array{label: string, value: int}>
     */
    private function countBy(Builder $query, string $column, array $cases): array
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
            ])
            ->all();
    }
}
