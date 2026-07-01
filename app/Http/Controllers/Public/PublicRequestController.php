<?php

namespace App\Http\Controllers\Public;

use App\Enums\Department;
use App\Enums\RequestType;
use App\Enums\SenderType;
use App\Enums\UrgencyLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestRequest;
use App\Mail\NewRequestNotification;
use App\Models\Request as BuzonRequest;
use App\Models\RequestAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicRequestController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('public/Reportar', [
            'options' => $this->formOptions(),
        ]);
    }

    public function store(StoreRequestRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $attachments = $request->file('attachments', []);

        $buzonRequest = DB::transaction(function () use ($validated, $attachments, $request) {
            $buzonRequest = BuzonRequest::create([
                ...collect($validated)->except('attachments')->all(),
                'has_evidence' => $validated['has_evidence'] || ! empty($attachments),
                'ip_address' => $request->ip(),
            ]);

            foreach ($attachments as $file) {
                $storedName = Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs("buzon/{$buzonRequest->folio}", $storedName, 'local');

                RequestAttachment::create([
                    'request_id' => $buzonRequest->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => $storedName,
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            return $buzonRequest;
        });

        try {
            Mail::to(config('buzon.mail_to'))->send(new NewRequestNotification($buzonRequest));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de notificación del buzón.', [
                'folio' => $buzonRequest->folio,
                'error' => $e->getMessage(),
            ]);
        }

        return to_route('reportar.exito', ['folio' => $buzonRequest->folio]);
    }

    public function success(string $folio): Response
    {
        BuzonRequest::where('folio', $folio)->firstOrFail();

        return Inertia::render('public/ReportarExito', [
            'folio' => $folio,
        ]);
    }

    /**
     * @return array<string, array<int, array{value: string, label: string, description?: string}>>
     */
    private function formOptions(): array
    {
        return [
            'requestTypes' => collect(RequestType::cases())
                ->map(fn (RequestType $type) => ['value' => $type->value, 'label' => $type->label()])
                ->all(),
            'senderTypes' => collect(SenderType::cases())
                ->map(fn (SenderType $type) => ['value' => $type->value, 'label' => $type->label()])
                ->all(),
            'departments' => collect(Department::cases())
                ->map(fn (Department $department) => ['value' => $department->value, 'label' => $department->label()])
                ->all(),
            'urgencyLevels' => collect(UrgencyLevel::cases())
                ->map(fn (UrgencyLevel $level) => [
                    'value' => $level->value,
                    'label' => $level->label(),
                    'description' => $level->description(),
                ])
                ->all(),
        ];
    }
}
