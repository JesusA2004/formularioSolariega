<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LabeledEnum;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Http\Controllers\Controller;
use App\Models\Request as BuzonRequest;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => $this->stats(),
            'charts' => [
                'byMonth' => $this->countByMonth(),
                'byType' => $this->countByEnum('request_type', RequestType::cases()),
                'byDepartment' => $this->countByDepartment(),
                'byStatus' => $this->countByEnum('status', RequestStatus::cases()),
                'byEvidence' => $this->countByEvidence(),
            ],
            'recent' => BuzonRequest::query()
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (BuzonRequest $request) => [
                    'id' => $request->id,
                    'folio' => $request->folio,
                    'request_type' => $request->request_type->value,
                    'request_type_label' => $request->request_type->label(),
                    'department_label' => $request->department,
                    'status' => $request->status->value,
                    'status_label' => $request->status->label(),
                    'has_evidence' => $request->has_evidence,
                    'created_at' => $request->created_at?->toIso8601String(),
                ]),
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function stats(): array
    {
        return [
            'total' => BuzonRequest::count(),
            'recibido' => BuzonRequest::where('status', RequestStatus::Recibido)->count(),
            'en_revision' => BuzonRequest::where('status', RequestStatus::EnRevision)->count(),
            'atendido' => BuzonRequest::where('status', RequestStatus::Atendido)->count(),
            'cerrado' => BuzonRequest::where('status', RequestStatus::Cerrado)->count(),
            'con_evidencia' => BuzonRequest::where('has_evidence', true)->count(),
            'este_mes' => BuzonRequest::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];
    }

    /**
     * @param  array<int, LabeledEnum>  $cases
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByEnum(string $column, array $cases): array
    {
        $counts = BuzonRequest::query()
            ->select($column)
            ->selectRaw('count(*) as total')
            ->groupBy($column)
            ->pluck('total', $column);

        return collect($cases)
            ->map(fn (LabeledEnum $case) => [
                'label' => $case->label(),
                'value' => (int) ($counts[$case->value] ?? 0),
                'key' => $case->value,
            ])
            ->all();
    }

    /**
     * El área/departamento ahora es texto libre, así que se agrupa por los
     * valores reales capturados en vez de un catálogo fijo.
     *
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByDepartment(): array
    {
        return BuzonRequest::query()
            ->select('department')
            ->selectRaw('count(*) as total')
            ->groupBy('department')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->department,
                'value' => (int) $row->total,
                'key' => $row->department,
            ])
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: int, key: string}>
     */
    private function countByEvidence(): array
    {
        return [
            [
                'label' => 'Con evidencia',
                'value' => BuzonRequest::where('has_evidence', true)->count(),
                'key' => '1',
            ],
            [
                'label' => 'Sin evidencia',
                'value' => BuzonRequest::where('has_evidence', false)->count(),
                'key' => '0',
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, value: int, dateFrom: string, dateTo: string}>
     */
    private function countByMonth(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();

        $counts = BuzonRequest::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->countBy(fn (BuzonRequest $request) => $request->created_at?->format('Y-m') ?? '');

        $months = [];
        $cursor = $start->copy();

        for ($i = 0; $i < 12; $i++) {
            $key = $cursor->format('Y-m');
            $cursor->locale('es');
            $label = $cursor->isoFormat('MMM YYYY');

            $months[] = [
                'label' => ucfirst($label),
                'value' => (int) ($counts[$key] ?? 0),
                'dateFrom' => $cursor->copy()->startOfMonth()->format('Y-m-d'),
                'dateTo' => $cursor->copy()->endOfMonth()->format('Y-m-d'),
            ];

            $cursor->addMonth();
        }

        return $months;
    }
}
