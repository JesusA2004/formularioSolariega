<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de mensajes</title>
    <style>
        @page {
            margin: 90px 32px 60px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #1E1E1E;
        }

        .masthead {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
        }

        .masthead table {
            width: 100%;
        }

        .masthead img {
            height: 32px;
        }

        .masthead h1 {
            font-size: 19px;
            margin: 0;
            color: #171717;
            letter-spacing: 0.2px;
        }

        .masthead p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #6B6457;
        }

        .footer {
            position: fixed;
            bottom: -45px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #E7E2D8;
            padding-top: 6px;
            font-size: 8px;
            color: #94A3B8;
        }

        .footer table {
            width: 100%;
        }

        .footer .page-number:after {
            content: "Página " counter(page);
        }

        h2.section-title {
            font-size: 13px;
            font-weight: bold;
            color: #171717;
            margin: 0 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #D4AF37;
        }

        .meta-bar {
            background-color: #FAF9F5;
            border: 1px solid #E7E2D8;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9px;
            color: #6B6457;
        }

        .meta-bar strong {
            color: #171717;
        }

        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin: 0 0 16px -6px;
        }

        .kpi-card {
            border: 1px solid #E7E2D8;
            border-radius: 8px;
            padding: 9px 10px;
            text-align: left;
        }

        .kpi-card .kpi-value {
            font-size: 19px;
            font-weight: bold;
            line-height: 1.1;
        }

        .kpi-card .kpi-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
            color: #6B6457;
        }

        .charts-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 8px;
            margin: 0 0 4px -8px;
        }

        .charts-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .chart-card {
            border: 1px solid #E7E2D8;
            border-radius: 8px;
            overflow: hidden;
        }

        .chart-card .chart-card-header {
            background-color: #171717;
            color: #D4AF37;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 10px;
        }

        .chart-card .chart-card-body {
            padding: 8px;
        }

        .chart-card img {
            width: 100%;
            height: auto;
            display: block;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            background-color: #171717;
            color: #D4AF37;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
            padding: 6px 7px;
        }

        .detail-table td {
            border-bottom: 1px solid #E7E2D8;
            padding: 6px 7px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
        }

        .detail-table tr:nth-child(even) td {
            background-color: #FAF9F5;
        }

        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9px;
            font-size: 8px;
            font-weight: bold;
        }

        .evidence-yes {
            color: #8B5CF6;
            font-weight: bold;
        }

        .evidence-no {
            color: #94A3B8;
        }

        .files {
            font-size: 7.5px;
            color: #6B6457;
            margin-top: 2px;
        }

        .muted {
            color: #94A3B8;
            text-align: center;
            padding: 24px 0;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="masthead">
        <table>
            <tr>
                <td style="width: 90px;">
                    @if (file_exists(public_path('images/logoLetras.png')))
                        <img src="{{ public_path('images/logoLetras.png') }}" alt="Solariega Cenit">
                    @endif
                </td>
                <td style="text-align: right;">
                    <h1>Reporte de mensajes</h1>
                    <p>Buzón Solariega &middot; Generado el {{ $generatedAt }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>Buzón Solariega Cenit &middot; Documento confidencial de uso interno</td>
                <td style="text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </div>

    <div class="meta-bar">
        <strong>Filtros aplicados:</strong>
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
    </div>

    <h2 class="section-title">Resumen general</h2>
    <table class="kpi-table">
        <tr>
            <td style="width: 16%;">
                <div class="kpi-card" style="border-color: #171717;">
                    <div class="kpi-value">{{ $requests->count() }}</div>
                    <div class="kpi-label">Total de mensajes</div>
                </div>
            </td>
            @foreach ($byStatus as $status)
                <td style="width: 16%;">
                    <div class="kpi-card" style="border-color: #{{ $statusColors[$status['key']] ?? 'E7E2D8' }};">
                        <div class="kpi-value" style="color: #{{ $statusColors[$status['key']] ?? '171717' }};">{{ $status['value'] }}</div>
                        <div class="kpi-label">{{ $status['label'] }}</div>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <h2 class="section-title">Gráficas</h2>
    <table class="charts-grid">
        <tr>
            <td>
                <div class="chart-card">
                    <div class="chart-card-header">Mensajes por tipo</div>
                    <div class="chart-card-body">
                        <img src="{{ $chartImages['byType'] }}" alt="Mensajes por tipo">
                    </div>
                </div>
            </td>
            <td>
                <div class="chart-card">
                    <div class="chart-card-header">Mensajes por departamento</div>
                    <div class="chart-card-body">
                        <img src="{{ $chartImages['byDepartment'] }}" alt="Mensajes por departamento">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="chart-card">
                    <div class="chart-card-header">Mensajes por estado</div>
                    <div class="chart-card-body">
                        <img src="{{ $chartImages['byStatus'] }}" alt="Mensajes por estado">
                    </div>
                </div>
            </td>
            <td>
                <div class="chart-card">
                    <div class="chart-card-header">Evidencia</div>
                    <div class="chart-card-body">
                        <img src="{{ $chartImages['byEvidence'] }}" alt="Evidencia">
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <h2 class="section-title page-break">Detalle de mensajes</h2>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Fecha de envío</th>
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
                    <td>
                        <span
                            class="status-pill"
                            style="background-color: #{{ $statusTints[$request->status->value] ?? 'F0EDE4' }}; color: #{{ $statusColors[$request->status->value] ?? '171717' }};"
                        >{{ $request->status->label() }}</span>
                    </td>
                    <td>
                        <span class="{{ $request->has_evidence ? 'evidence-yes' : 'evidence-no' }}">
                            {{ $request->has_evidence ? 'Sí' : 'No' }}
                        </span>
                        @if ($request->attachments->isNotEmpty())
                            <div class="files">
                                @foreach ($request->attachments as $file)
                                    {{ $file->original_name }}{{ ! $loop->last ? ', ' : '' }}
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
