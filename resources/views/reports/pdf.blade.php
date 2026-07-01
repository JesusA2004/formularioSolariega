<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de mensajes</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1E1E1E; }
        .header { width: 100%; margin-bottom: 12px; }
        .header img { height: 34px; }
        h1 { font-size: 18px; margin: 10px 0 2px; color: #171717; }
        p.subtitle { margin-top: 0; color: #6B6457; margin-bottom: 4px; }
        p.filters { margin-top: 0; color: #6B6457; font-size: 9px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #E7E2D8; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #171717; color: #D4AF37; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #FAF9F5; }
        .summary { margin-bottom: 16px; }
        .summary span { display: inline-block; margin-right: 18px; font-size: 11px; }
        .summary strong { color: #171717; }
        .muted { color: #94A3B8; }
        .files { font-size: 8px; color: #6B6457; }
    </style>
</head>
<body>
    <div class="header">
        @if (file_exists(public_path('images/logoLetras.png')))
            <img src="{{ public_path('images/logoLetras.png') }}" alt="Solariega Cenit">
        @endif
    </div>

    <h1>Reporte de mensajes</h1>
    <p class="subtitle">Generado el {{ $generatedAt }}</p>
    <p class="filters">
        Filtros aplicados:
        @php
            $labels = [
                'search' => 'Búsqueda',
                'status' => 'Estado',
                'request_type' => 'Tipo de mensaje',
                'department' => 'Departamento',
                'has_evidence' => 'Con evidencia',
                'date_from' => 'Desde',
                'date_to' => 'Hasta',
            ];
            $applied = collect($filters ?? [])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value, $key) => ($labels[$key] ?? $key).': '.$value)
                ->implode(' · ');
        @endphp
        {{ $applied !== '' ? $applied : 'Ninguno' }}
    </p>

    <div class="summary">
        <span><strong>Total de mensajes:</strong> {{ $requests->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Departamento</th>
                <th>Tipo de mensaje</th>
                <th>Estado</th>
                <th>Evidencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $request->full_name ?? 'No proporcionado' }}</td>
                    <td>{{ $request->contact_info ?? 'No proporcionado' }}</td>
                    <td>{{ $request->department }}</td>
                    <td>{{ $request->request_type->label() }}</td>
                    <td>{{ $request->status->label() }}</td>
                    <td>
                        Cuenta con evidencia: {{ $request->has_evidence ? 'Sí' : 'No' }}
                        @if ($request->attachments->isNotEmpty())
                            <div class="files">
                                @foreach ($request->attachments as $file)
                                    {{ $file->original_name }} ({{ $file->mime_type ?? 'archivo' }}){{ ! $loop->last ? ', ' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if ($requests->isEmpty())
                <tr>
                    <td colspan="7" class="muted">No hay mensajes para los filtros seleccionados.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
