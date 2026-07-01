<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequestRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(array_column(RequestStatus::cases(), 'value'))],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Selecciona un estado.',
            'status.in' => 'El estado seleccionado no es válido.',
            'internal_notes.max' => 'Las notas internas son demasiado largas.',
        ];
    }
}
