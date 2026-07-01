<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte del buzón</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 2px; color: #1E1E1E; }
        p.subtitle { margin-top: 0; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #1E1E1E; color: #D4AF37; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .summary { margin-bottom: 16px; }
        .summary span { display: inline-block; margin-right: 18px; font-size: 11px; }
        .summary strong { color: #1E1E1E; }
    </style>
</head>
<body>
    <h1>Solariega Cenit — Reporte del buzón</h1>
    <p class="subtitle">Generado el {{ $generatedAt }}</p>

    <div class="summary">
        <span><strong>Total de solicitudes:</strong> {{ $requests->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Tipo</th>
                <th>Departamento</th>
                <th>Ubicación</th>
                <th>Urgencia</th>
                <th>Estado</th>
                <th>Anónima</th>
                <th>Evidencia</th>
                <th>Fecha de envío</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->folio }}</td>
                    <td>{{ $request->request_type->label() }}</td>
                    <td>{{ \App\Enums\Department::tryFrom($request->department)?->label() ?? $request->department }}</td>
                    <td>{{ $request->location ?? '—' }}</td>
                    <td>{{ $request->urgency_level->label() }}</td>
                    <td>{{ $request->status->label() }}</td>
                    <td>{{ $request->is_anonymous ? 'Sí' : 'No' }}</td>
                    <td>{{ $request->has_evidence ? 'Sí' : 'No' }}</td>
                    <td>{{ $request->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
