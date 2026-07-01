<?php

namespace App\Http\Requests;

use App\Enums\Department;
use App\Enums\RequestType;
use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Si el solicitante elige permanecer anónimo, ignoramos cualquier dato
     * identificable que se haya enviado para no almacenarlo por error.
     */
    protected function prepareForValidation(): void
    {
        $isAnonymous = $this->boolean('is_anonymous');
        $wantsFollowUp = $this->boolean('wants_follow_up');

        $this->merge([
            'full_name' => $isAnonymous ? null : $this->input('full_name'),
            'contact_info' => ($isAnonymous || ! $wantsFollowUp) ? null : $this->input('contact_info'),
        ]);
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'request_type' => ['required', 'string', Rule::in(array_column(RequestType::cases(), 'value'))],
            'is_anonymous' => ['required', 'boolean'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'department' => ['required', 'string', Rule::in(array_column(Department::cases(), 'value'))],
            'location' => ['required', 'string', 'max:255'],
            'incident_date' => ['nullable', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'involved_people' => ['nullable', 'string', 'max:2000'],
            'urgency_level' => ['required', 'string', Rule::in(array_column(UrgencyLevel::cases(), 'value'))],
            'has_evidence' => ['required', 'boolean'],
            'wants_follow_up' => ['required', 'boolean'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'accepted_terms' => ['accepted'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:20480',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,mp4,mov',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'request_type.required' => 'Selecciona el tipo de solicitud.',
            'request_type.in' => 'El tipo de solicitud seleccionado no es válido.',
            'is_anonymous.required' => 'Indica si deseas que tu solicitud sea anónima.',
            'department.required' => 'Selecciona el área o departamento.',
            'department.in' => 'El área o departamento seleccionado no es válido.',
            'location.required' => 'Indica la sucursal, planta o ubicación.',
            'incident_date.date' => 'La fecha del hecho no es válida.',
            'incident_date.before_or_equal' => 'La fecha del hecho no puede ser futura.',
            'description.required' => 'Describe tu solicitud para poder continuar.',
            'description.min' => 'Por favor describe la situación con más detalle (mínimo 20 caracteres).',
            'description.max' => 'La descripción es demasiado larga (máximo 5000 caracteres).',
            'urgency_level.required' => 'Selecciona el nivel de urgencia.',
            'urgency_level.in' => 'El nivel de urgencia seleccionado no es válido.',
            'has_evidence.required' => 'Indica si cuentas con evidencia.',
            'wants_follow_up.required' => 'Indica si deseas recibir seguimiento.',
            'accepted_terms.accepted' => 'Debes confirmar que la información proporcionada es verdadera.',
            'attachments.max' => 'Puedes adjuntar un máximo de 5 archivos.',
            'attachments.*.max' => 'Cada archivo debe pesar como máximo 20 MB.',
            'attachments.*.mimes' => 'Formato de archivo no permitido.',
        ];
    }
}
