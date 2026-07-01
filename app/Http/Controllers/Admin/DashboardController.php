<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LabeledEnum;
use App\Enums\Department;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\UrgencyLevel;
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
                'byType' => $this->countBy('request_type', RequestType::cases()),
                'byUrgency' => $this->countBy('urgency_level', UrgencyLevel::cases()),
                'byDepartment' => $this->countBy('department', Department::cases()),
                'byStatus' => $this->countBy('status', RequestStatus::cases()),
                'byMonth' => $this->countByMonth(),
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
                    'department_label' => Department::tryFrom($request->department)?->label() ?? $request->department,
                    'urgency_level' => $request->urgency_level->value,
                    'urgency_level_label' => $request->urgency_level->label(),
                    'status' => $request->status->value,
                    'status_label' => $request->status->label(),
                    'is_anonymous' => $request->is_anonymous,
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
            'criticas' => BuzonRequest::where('urgency_level', UrgencyLevel::Critico)->count(),
            'anonimas' => BuzonRequest::where('is_anonymous', true)->count(),
            'con_evidencia' => BuzonRequest::where('has_evidence', true)->count(),
        ];
    }

    /**
     * @param  array<int, LabeledEnum>  $cases
     * @return array<int, array{label: string, value: int}>
     */
    private function countBy(string $column, array $cases): array
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
            ])
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: int}>
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
            ];

            $cursor->addMonth();
        }

        return $months;
    }
}
