<?php

namespace App\Http\Requests;

use App\Enums\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'contact_info' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'request_type' => ['required', 'string', Rule::in(array_column(RequestType::cases(), 'value'))],
            'incident_date' => ['nullable', 'date', 'before_or_equal:today'],
            'involved_people' => ['nullable', 'string', 'max:2000'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
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
            'full_name.required' => 'Escribe tu nombre completo.',
            'contact_info.required' => 'Indica un correo o teléfono de contacto para poder dar seguimiento.',
            'contact_info.max' => 'El contacto no debe superar los 255 caracteres.',
            'department.required' => 'Indica el área o departamento.',
            'request_type.required' => 'Selecciona el tipo de mensaje.',
            'request_type.in' => 'El tipo de mensaje seleccionado no es válido.',
            'incident_date.date' => 'La fecha indicada no es válida.',
            'incident_date.before_or_equal' => 'La fecha indicada no puede ser futura.',
            'description.required' => 'Cuéntanos tu mensaje para poder continuar.',
            'description.min' => 'Por favor cuéntanos con más detalle (mínimo 20 caracteres).',
            'description.max' => 'El mensaje es demasiado largo (máximo 5000 caracteres).',
            'accepted_terms.accepted' => 'Debes confirmar la información para continuar.',
            'attachments.max' => 'Puedes adjuntar un máximo de 5 archivos.',
            'attachments.*.max' => 'Cada archivo debe pesar como máximo 20 MB.',
            'attachments.*.mimes' => 'Formato de archivo no permitido.',
        ];
    }
}
