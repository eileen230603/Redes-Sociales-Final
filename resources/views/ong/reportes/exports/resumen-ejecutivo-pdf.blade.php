<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Reporte: Resumen Ejecutivo Consolidado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        @page {
            margin: 10%;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.5;
            background: #FFFFFF;
            -webkit-font-smoothing: antialiased;
        }
        .main-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }
        /* Header Section */
        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .logo-container {
            width: 120px;
            height: 80px;
            margin-bottom: 10px;
            border: 1px solid #e0e0e0;
            background: #f9f9f9;
            display: block;
            text-align: center;
            line-height: 80px;
            color: #999;
            font-size: 9pt;
        }
        .ong-name {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .ong-contact {
            font-size: 9pt;
            color: #666;
            line-height: 1.6;
        }
        .report-title-main {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .report-subtitle-main {
            font-size: 11pt;
            color: #666;
            margin-bottom: 10px;
        }
        .report-meta {
            text-align: right;
            font-size: 9pt;
            color: #666;
            line-height: 1.8;
        }
        /* Metadata Section */
        .metadata-box {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .metadata-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .metadata-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #2c3e50;
        }
        .metadata-value {
            display: table-cell;
            width: 60%;
            color: #666;
        }
        /* KPI Cards */
        .kpi-section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 12px;
            text-transform: uppercase;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0e0e0;
        }
        .kpi-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 15px;
        }
        .kpi-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px 10px;
            text-align: center;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            vertical-align: top;
        }
        .kpi-value {
            font-size: 24pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 8px 0;
            line-height: 1.2;
        }
        .kpi-value.success {
            color: #27ae60;
        }
        .kpi-value.danger {
            color: #e74c3c;
        }
        .kpi-value.info {
            color: #3498db;
        }
        .kpi-label {
            font-size: 9pt;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .kpi-detail {
            font-size: 8pt;
            color: #999;
            margin-top: 5px;
        }
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            border: 1px solid #34495e;
        }
        th.text-center {
            text-align: center;
        }
        th.text-right {
            text-align: right;
        }
        td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            font-size: 9pt;
        }
        td.text-center {
            text-align: center;
        }
        td.text-right {
            text-align: right;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        .badge-info {
            background-color: #3498db;
            color: white;
        }
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        /* Progress Bars */
        .progress-container {
            width: 100%;
            height: 18px;
            background-color: #ecf0f1;
            border-radius: 9px;
            overflow: hidden;
            margin-top: 3px;
            border: 1px solid #e0e0e0;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(to right, #27ae60 0%, #2ecc71 100%);
            border-radius: 9px;
            display: block;
            text-align: center;
            color: white;
            font-size: 7pt;
            font-weight: bold;
            line-height: 18px;
        }
        /* Comparison Box */
        .comparison-box {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .comparison-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px;
        }
        .comparison-item {
            display: table-cell;
            width: 50%;
            padding: 12px;
            text-align: center;
            background: #FFFFFF;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        .comparison-value {
            font-size: 22pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 8px 0;
        }
        .comparison-label {
            font-size: 9pt;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        /* Insights Box */
        .insights-box {
            background: #e8f5e9;
            border-left: 4px solid #27ae60;
            padding: 12px 15px;
            margin: 20px 0;
            font-size: 9pt;
            line-height: 1.6;
        }
        .insights-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        /* Summary Section */
        .summary-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .summary-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 15px;
            font-size: 9pt;
            color: #666;
            font-weight: 500;
        }
        .summary-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 10pt;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-total {
            border-top: 2px solid #2c3e50;
            padding-top: 8px;
            margin-top: 8px;
        }
        .summary-total .summary-label {
            font-size: 11pt;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-total .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #2c3e50;
        }
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 8pt;
            color: #666;
            line-height: 1.6;
        }
        .page-number {
            position: absolute;
            bottom: 10%;
            right: 10%;
            font-size: 8pt;
            color: #999;
        }
        /* Word wrap for tables */
        td, th {
            word-wrap: break-word;
            overflow: hidden;
        }
        /* Image handling */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        /* Prevent page breaks inside important sections */
        .kpi-section, .comparison-box {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    @php
                        $logoPath = null;
                        if(isset($pdfData['ong']['logo_url']) && $pdfData['ong']['logo_url']) {
                            $logoUrl = $pdfData['ong']['logo_url'];
                            // Si es una ruta absoluta y el archivo existe, usarla
                            if(file_exists($logoUrl)) {
                                $logoPath = $logoUrl;
                            } elseif(filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                                // Es una URL, intentar convertir a base64 o ignorar
                                $logoPath = null;
                            } elseif(strpos($logoUrl, '/storage/') === 0) {
                                // Ruta relativa, intentar convertir a absoluta
                                $path = str_replace('/storage/', '', $logoUrl);
                                $localPath = storage_path('app/public/' . $path);
                                if(file_exists($localPath)) {
                                    $logoPath = $localPath;
                                } else {
                                    $publicPath = public_path('storage/' . $path);
                                    if(file_exists($publicPath)) {
                                        $logoPath = $publicPath;
                                    }
                                }
                            }
                        }
                    @endphp
                    @if($logoPath && file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo" class="logo-container" style="max-width: 120px; max-height: 80px; object-fit: contain;">
                    @else
                        <div class="logo-container">LOGO</div>
                    @endif
                    <div class="ong-name">{{ htmlspecialchars($pdfData['ong']['nombre'] ?? 'ONG', ENT_QUOTES, 'UTF-8') }}</div>
                    <div class="ong-contact">
                        {{ htmlspecialchars($pdfData['ong']['telefono'] ?? '', ENT_QUOTES, 'UTF-8') }}<br>
                        {{ htmlspecialchars($pdfData['ong']['email'] ?? '', ENT_QUOTES, 'UTF-8') }}<br>
                        {{ htmlspecialchars($pdfData['ong']['direccion'] ?? '', ENT_QUOTES, 'UTF-8') }}
                    </div>
                </div>
                <div class="header-right">
                    <div class="report-title-main">{{ $pdfData['titulo'] ?? 'RESUMEN EJECUTIVO' }}</div>
                    <div class="report-subtitle-main">{{ $pdfData['subtitulo'] ?? 'Análisis Consolidado de Eventos' }}</div>
                    <div class="report-meta">
                        <div style="font-weight: bold; margin-bottom: 5px;">REP-{{ date('Ymd') }}-{{ str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata Section -->
        <div class="metadata-box">
            <div class="metadata-row">
                <div class="metadata-label">Fecha de Generación:</div>
                <div class="metadata-value">{{ $pdfData['fecha_generacion'] ?? $fechaFormateada ?? now()->format('d/m/Y') }}</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Hora de Generación:</div>
                <div class="metadata-value">{{ $pdfData['hora_generacion'] ?? $horaFormateada ?? now()->format('H:i:s') }}</div>
            </div>
            @if(!empty($pdfData['filtros_aplicados']))
            <div class="metadata-row">
                <div class="metadata-label">Filtros Aplicados:</div>
                <div class="metadata-value">
                    @if(isset($pdfData['filtros_aplicados']['fecha_inicio']))
                        Desde: {{ \Carbon\Carbon::parse($pdfData['filtros_aplicados']['fecha_inicio'])->format('d/m/Y') }}
                    @endif
                    @if(isset($pdfData['filtros_aplicados']['fecha_fin']))
                        Hasta: {{ \Carbon\Carbon::parse($pdfData['filtros_aplicados']['fecha_fin'])->format('d/m/Y') }}
                    @endif
                    @if(isset($pdfData['filtros_aplicados']['categoria']) && $pdfData['filtros_aplicados']['categoria'])
                        | Categoría: {{ htmlspecialchars(ucfirst($pdfData['filtros_aplicados']['categoria']), ENT_QUOTES, 'UTF-8') }}
                    @endif
                    @if(isset($pdfData['filtros_aplicados']['estado']) && $pdfData['filtros_aplicados']['estado'])
                        | Estado: {{ htmlspecialchars(ucfirst(str_replace('_', ' ', $pdfData['filtros_aplicados']['estado'])), ENT_QUOTES, 'UTF-8') }}
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- KPIs Section -->
        <div class="kpi-section">
            <h2 class="section-title">Indicadores Clave de Rendimiento</h2>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Eventos</div>
                    <div class="kpi-value">{{ number_format($datos['totales']['total_eventos'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-detail">Consolidado</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Finalizados</div>
                    <div class="kpi-value success">{{ number_format($datos['kpis']['eventos_finalizados'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-detail">{{ number_format($datos['kpis']['tasa_finalizacion'] ?? 0, 2) }}% del total</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Participantes</div>
                    <div class="kpi-value info">{{ number_format($datos['kpis']['total_participantes'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-detail">Total consolidado</div>
                </div>
            </div>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Tasa Finalización</div>
                    <div class="kpi-value success">{{ number_format($datos['kpis']['tasa_finalizacion'] ?? 0, 2) }}%</div>
                    <div class="kpi-detail">Eventos completados</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Eventos Activos</div>
                    <div class="kpi-value info">{{ number_format($datos['kpis']['eventos_activos'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-detail">En curso</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Cancelados</div>
                    <div class="kpi-value danger">{{ number_format($datos['kpis']['eventos_cancelados'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-detail">{{ number_format($datos['kpis']['tasa_cancelacion'] ?? 0, 2) }}%</div>
                </div>
            </div>
        </div>

        <!-- Comparison Section -->
        <div class="kpi-section">
            <h2 class="section-title">Comparación por Tipo de Evento</h2>
            <div class="comparison-box">
                <div class="comparison-grid">
                    <div class="comparison-item">
                        <div class="comparison-label">Eventos Regulares</div>
                        <div class="comparison-value">{{ number_format($datos['totales']['total_eventos_regulares'] ?? 0, 0, ',', '.') }}</div>
                        <div style="font-size: 9pt; color: #666; margin-top: 6px;">
                            Participantes: {{ number_format($datos['kpis']['total_participantes_eventos'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="comparison-item">
                        <div class="comparison-label">Mega Eventos</div>
                        <div class="comparison-value">{{ number_format($datos['totales']['total_mega_eventos'] ?? 0, 0, ',', '.') }}</div>
                        <div style="font-size: 9pt; color: #666; margin-top: 6px;">
                            Participantes: {{ number_format($datos['kpis']['total_participantes_mega'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution by Category -->
        <div class="kpi-section">
            <h2 class="section-title">Distribución por Categoría</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Categoría</th>
                        <th style="width: 20%;" class="text-center">Cantidad</th>
                        <th style="width: 15%;" class="text-center">Porcentaje</th>
                        <th style="width: 15%;">Visualización</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEventos = $datos['totales']['total_eventos'] ?? 1;
                    @endphp
                    @foreach($datos['totales']['por_categoria'] ?? [] as $categoria => $cantidad)
                    @php
                        $porcentaje = $totalEventos > 0 ? round(($cantidad / $totalEventos) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ htmlspecialchars(ucfirst($categoria), ENT_QUOTES, 'UTF-8') }}</strong></td>
                        <td class="text-center" style="font-weight: bold;">{{ number_format($cantidad, 0, ',', '.') }}</td>
                        <td class="text-center" style="font-weight: bold; color: #27ae60;">{{ $porcentaje }}%</td>
                        <td>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: {{ $porcentaje }}%;">
                                    @if($porcentaje > 5){{ $porcentaje }}%@endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Distribution by State -->
        <div class="kpi-section">
            <h2 class="section-title">Distribución por Estado</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Estado</th>
                        <th style="width: 20%;" class="text-center">Cantidad</th>
                        <th style="width: 15%;" class="text-center">Porcentaje</th>
                        <th style="width: 15%;">Badge</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['totales']['por_estado'] ?? [] as $estado => $cantidad)
                    @php
                        $porcentaje = $totalEventos > 0 ? round(($cantidad / $totalEventos) * 100, 2) : 0;
                        $estadoLabel = ucfirst(str_replace('_', ' ', $estado));
                        $badgeClass = 'badge-info';
                        if($estado == 'finalizado') $badgeClass = 'badge-success';
                        elseif($estado == 'cancelado') $badgeClass = 'badge-danger';
                        elseif($estado == 'en_curso' || $estado == 'activo') $badgeClass = 'badge-info';
                        elseif($estado == 'planificacion' || $estado == 'pendiente') $badgeClass = 'badge-warning';
                    @endphp
                    <tr>
                        <td><strong>{{ htmlspecialchars($estadoLabel, ENT_QUOTES, 'UTF-8') }}</strong></td>
                        <td class="text-center" style="font-weight: bold;">{{ number_format($cantidad, 0, ',', '.') }}</td>
                        <td class="text-center" style="font-weight: bold; color: #27ae60;">{{ $porcentaje }}%</td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }}">{{ htmlspecialchars($estadoLabel, ENT_QUOTES, 'UTF-8') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Insights -->
        @php
            $tasaFinalizacion = $datos['kpis']['tasa_finalizacion'] ?? 0;
            $insight = '';
            if($tasaFinalizacion >= 80) {
                $insight = '✅ Excelente tasa de finalización. La organización muestra un alto nivel de cumplimiento en la ejecución de eventos.';
            } elseif($tasaFinalizacion >= 60) {
                $insight = '📈 Buena tasa de finalización. Se observa un rendimiento satisfactorio en la gestión de eventos.';
            } elseif($tasaFinalizacion >= 40) {
                $insight = '⚠️ Tasa de finalización moderada. Se recomienda revisar los procesos para mejorar la ejecución.';
            } else {
                $insight = '🔴 Tasa de finalización baja. Es necesario analizar las causas de cancelación o postergación de eventos.';
            }
        @endphp
        @if($insight)
        <div class="insights-box">
            <div class="insights-title">💡 Análisis Automático</div>
            <div>{{ $insight }}</div>
        </div>
        @endif

        <!-- Summary Section -->
        <div class="kpi-section">
            <h2 class="section-title">Resumen Consolidado</h2>
            <div class="summary-section">
                <div class="summary-row">
                    <div class="summary-label">Total Eventos (General):</div>
                    <div class="summary-value">{{ number_format($datos['totales']['total_eventos'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Eventos Regulares:</div>
                    <div class="summary-value">{{ number_format($datos['totales']['total_eventos_regulares'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Mega Eventos:</div>
                    <div class="summary-value">{{ number_format($datos['totales']['total_mega_eventos'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total Participantes:</div>
                    <div class="summary-value">{{ number_format($datos['kpis']['total_participantes'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row summary-total">
                    <div class="summary-label">Tasa de Finalización:</div>
                    <div class="summary-value">{{ number_format($datos['kpis']['tasa_finalizacion'] ?? 0, 2) }}%</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div style="font-weight: bold; margin-bottom: 5px;">{{ htmlspecialchars($pdfData['ong']['nombre'] ?? 'Sistema de Reportes Avanzados', ENT_QUOTES, 'UTF-8') }}</div>
            <div>© {{ date('Y') }} - Todos los derechos reservados</div>
            <div style="margin-top: 8px;">
                Generado el {{ htmlspecialchars($pdfData['fecha_generacion'] ?? $fechaFormateada ?? now()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') }} 
                a las {{ htmlspecialchars($pdfData['hora_generacion'] ?? $horaFormateada ?? now()->format('H:i:s'), ENT_QUOTES, 'UTF-8') }}
            </div>
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">
        <span style="background: #2c3e50; color: white; padding: 3px 8px; border-radius: 3px;">{PAGENO} / {nbpg}</span>
    </div>
</body>
</html>
