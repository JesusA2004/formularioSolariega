<x-mail::message>
# Nueva solicitud recibida

Se ha registrado una nueva solicitud en el Buzón de Quejas, Sugerencias y Reportes de Solariega Cenit.

| | |
|---|---|
| **Folio** | {{ $request->folio }} |
| **Tipo de solicitud** | {{ $request->request_type->label() }} |
| **Nivel de urgencia** | {{ $request->urgency_level->label() }} |
| **Área / Departamento** | {{ \App\Enums\Department::tryFrom($request->department)?->label() ?? $request->department }} |
| **Ubicación** | {{ $request->location }} |
| **Fecha aproximada del hecho** | {{ $request->incident_date?->format('d/m/Y') ?? 'No especificada' }} |
| **Solicitud anónima** | {{ $request->is_anonymous ? 'Sí' : 'No' }} |
@if (! $request->is_anonymous)
| **Nombre** | {{ $request->full_name ?? 'No proporcionado' }} |
| **Contacto** | {{ $request->contact_info ?? 'No proporcionado' }} |
@endif
| **Personas involucradas** | {{ $request->involved_people ?? 'No especificadas' }} |
| **Fecha de envío** | {{ $request->created_at->format('d/m/Y H:i') }} |

## Descripción

{{ $request->description }}

@if ($request->has_evidence)
Esta solicitud cuenta con archivos adjuntos. Revísalos desde el panel administrativo.
@endif

<x-mail::button :url="$panelUrl">
Ver en el panel administrativo
</x-mail::button>

Este correo fue generado automáticamente por el Buzón de Solariega Cenit. No respondas a este mensaje.

{{ config('app.name') }}
</x-mail::message>
