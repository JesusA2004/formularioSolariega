<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Department;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\UrgencyLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRequestRequest;
use App\Models\Request as BuzonRequest;
use App\Models\RequestAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminRequestController extends Controller
{
    public function index(HttpRequest $request): Response
    {
        $query = BuzonRequest::query()->filter($request->only([
            'search', 'status', 'request_type', 'urgency_level', 'department', 'is_anonymous', 'has_evidence',
        ]));

        $sort = $request->string('sort')->toString() ?: 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['created_at', 'folio', 'urgency_level', 'status', 'department'];

        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $requests = $query->withCount('attachments')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (BuzonRequest $r) => [
                'id' => $r->id,
                'folio' => $r->folio,
                'request_type' => $r->request_type->value,
                'request_type_label' => $r->request_type->label(),
                'department' => $r->department,
                'department_label' => Department::tryFrom($r->department)?->label() ?? $r->department,
                'location' => $r->location,
                'urgency_level' => $r->urgency_level->value,
                'urgency_level_label' => $r->urgency_level->label(),
                'status' => $r->status->value,
                'status_label' => $r->status->label(),
                'is_anonymous' => $r->is_anonymous,
                'has_evidence' => $r->has_evidence,
                'attachments_count' => $r->attachments_count,
                'created_at' => $r->created_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/solicitudes/Index', [
            'requests' => $requests,
            'filters' => $request->only([
                'search', 'status', 'request_type', 'urgency_level', 'department', 'is_anonymous', 'has_evidence', 'sort', 'direction',
            ]),
            'options' => $this->filterOptions(),
        ]);
    }

    public function show(BuzonRequest $solicitud): Response
    {
        $solicitud->load(['attachments', 'reviewedBy']);

        return Inertia::render('admin/solicitudes/Show', [
            'request' => [
                'id' => $solicitud->id,
                'folio' => $solicitud->folio,
                'request_type' => $solicitud->request_type->value,
                'request_type_label' => $solicitud->request_type->label(),
                'is_anonymous' => $solicitud->is_anonymous,
                'full_name' => $solicitud->full_name,
                'department' => $solicitud->department,
                'department_label' => Department::tryFrom($solicitud->department)?->label() ?? $solicitud->department,
                'location' => $solicitud->location,
                'incident_date' => $solicitud->incident_date?->format('Y-m-d'),
                'description' => $solicitud->description,
                'involved_people' => $solicitud->involved_people,
                'urgency_level' => $solicitud->urgency_level->value,
                'urgency_level_label' => $solicitud->urgency_level->label(),
                'has_evidence' => $solicitud->has_evidence,
                'wants_follow_up' => $solicitud->wants_follow_up,
                'contact_info' => $solicitud->contact_info,
                'status' => $solicitud->status->value,
                'status_label' => $solicitud->status->label(),
                'internal_notes' => $solicitud->internal_notes,
                'reviewed_by' => $solicitud->reviewedBy?->name,
                'reviewed_at' => $solicitud->reviewed_at?->toIso8601String(),
                'closed_at' => $solicitud->closed_at?->toIso8601String(),
                'created_at' => $solicitud->created_at?->toIso8601String(),
                'attachments' => $solicitud->attachments->map(fn (RequestAttachment $a) => [
                    'id' => $a->id,
                    'original_name' => $a->original_name,
                    'mime_type' => $a->mime_type,
                    'size' => $a->size,
                    'human_size' => $a->humanSize(),
                    'is_image' => $a->isImage(),
                ]),
            ],
            'statuses' => collect(RequestStatus::cases())
                ->map(fn (RequestStatus $s) => ['value' => $s->value, 'label' => $s->label()])
                ->all(),
        ]);
    }

    public function update(UpdateRequestRequest $request, BuzonRequest $solicitud): RedirectResponse
    {
        $validated = $request->validated();
        $newStatus = RequestStatus::from($validated['status']);

        $solicitud->internal_notes = $validated['internal_notes'] ?? $solicitud->internal_notes;
        $solicitud->status = $newStatus;

        if (is_null($solicitud->reviewed_at)) {
            $solicitud->reviewed_by = (int) Auth::id();
            $solicitud->reviewed_at = now();
        }

        if ($newStatus === RequestStatus::Cerrado && is_null($solicitud->closed_at)) {
            $solicitud->closed_at = now();
        }

        if ($newStatus !== RequestStatus::Cerrado) {
            $solicitud->closed_at = null;
        }

        $solicitud->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Solicitud actualizada correctamente.']);

        return back();
    }

    /**
     * @return array<string, array<int, array{value: string, label: string}>>
     */
    private function filterOptions(): array
    {
        return [
            'requestTypes' => collect(RequestType::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
            'departments' => collect(Department::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
            'urgencyLevels' => collect(UrgencyLevel::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
            'statuses' => collect(RequestStatus::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()])->all(),
        ];
    }
}
