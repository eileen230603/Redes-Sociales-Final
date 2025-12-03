<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Evento - {{ $evento->titulo }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #1E293B;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            position: relative;
            background: #FFFFFF;
        }
        
        /* Imagen de fondo hoja.png */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.05;
            pointer-events: none;
        }
        
        .background-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 15mm;
        }
        
        /* Espaciado adicional después del header */
        .main-content {
            margin-top: 25px;
        }
        
        /* Header con fondo azul */
        .header {
            background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
            color: white;
            padding: 25px 25px;
            margin: -15mm -15mm 30px -15mm;
            border-radius: 0;
            text-align: center;
        }
        
        .header h1 {
            font-size: 22pt;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: white;
        }
        
        .header .subtitle {
            font-size: 9pt;
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 15px 0;
            line-height: 1.5;
        }
        
        .header .event-title {
            font-size: 18pt;
            font-weight: 600;
            margin: 15px 0 0 0;
            color: white;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Layout de dos columnas */
        .two-column-layout {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
            margin-bottom: 20px;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        /* Centrar paneles individuales */
        .centered-panel {
            margin-left: auto;
            margin-right: auto;
            max-width: 95%;
        }
        
        /* Paneles */
        .panel {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 15px;
            page-break-inside: avoid;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .panel-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .panel-title {
            font-size: 11pt;
            font-weight: 700;
            color: #1E40AF;
            margin: 0;
            flex: 1;
        }
        
        .panel-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            font-size: 14pt;
        }
        
        /* Tabla de información del evento */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        
        .info-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .info-table td:first-child {
            color: #6B7280;
            width: 45%;
            font-weight: 500;
        }
        
        .info-table td:last-child {
            color: #1E293B;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: 600;
            background: #3B82F6;
            color: white;
        }
        
        /* Estadísticas principales */
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 15px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            vertical-align: middle;
            color: white;
            font-weight: 600;
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }
        
        .stat-card h3 {
            font-size: 7pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .stat-card .value {
            font-size: 20pt;
            margin: 0;
            font-weight: 700;
        }
        
        /* Gráficos visuales con barras */
        .chart-container {
            margin-top: 10px;
        }
        
        .chart-bar {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .chart-label {
            width: 35%;
            font-size: 8pt;
            color: #4B5563;
            font-weight: 500;
        }
        
        .chart-bar-visual {
            flex: 1;
            height: 20px;
            background: #E5E7EB;
            border-radius: 4px;
            margin: 0 10px;
            position: relative;
            overflow: hidden;
        }
        
        .chart-bar-fill {
            height: 100%;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 6px;
            color: white;
            font-size: 7pt;
            font-weight: 600;
        }
        
        .chart-bar-fill.blue {
            background: linear-gradient(90deg, #3B82F6 0%, #2563EB 100%);
        }
        
        .chart-bar-fill.green {
            background: linear-gradient(90deg, #10B981 0%, #059669 100%);
        }
        
        .chart-value {
            width: 15%;
            text-align: right;
            font-size: 8pt;
            font-weight: 600;
            color: #1E293B;
        }
        
        /* Tabla de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 10px;
        }
        
        .data-table th {
            background: #F3F4F6;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            color: #1E293B;
            border-bottom: 2px solid #E5E7EB;
            font-size: 8pt;
        }
        
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #F3F4F6;
            color: #4B5563;
        }
        
        .data-table tr:hover {
            background: #F9FAFB;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .badge-success {
            background-color: #10B981;
            color: white;
        }
        
        .badge-warning {
            background-color: #F59E0B;
            color: white;
        }
        
        .badge-danger {
            background-color: #EF4444;
            color: white;
        }
        
        .badge-info {
            background-color: #3B82F6;
            color: white;
        }
        
        /* Progreso circular simulado */
        .progress-circle {
            width: 120px;
            height: 120px;
            margin: 15px auto;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .progress-circle-inner {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 12px solid #E5E7EB;
            border-top-color: #3B82F6;
            border-right-color: #10B981;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .progress-percentage {
            font-size: 24pt;
            font-weight: 700;
            color: #1E40AF;
        }
        
        .progress-label {
            font-size: 7pt;
            color: #6B7280;
            margin-top: 4px;
        }
        
        /* Lista de participantes/reacciones */
        .participant-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .participant-item {
            padding: 6px 0;
            border-bottom: 1px solid #F3F4F6;
            font-size: 7.5pt;
        }
        
        .participant-item:last-child {
            border-bottom: none;
        }
        
        .participant-name {
            font-weight: 600;
            color: #1E293B;
        }
        
        .participant-email {
            color: #6B7280;
            font-size: 7pt;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #6B7280;
            padding: 8px;
            border-top: 1px solid #E5E7EB;
            background: white;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Colores específicos para gráficos */
        .color-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            font-size: 7pt;
            justify-content: center;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- Imagen de fondo -->
    @if($hoja_exists)
    <div class="background-image">
        <img src="{{ $hoja_path }}" alt="Fondo">
    </div>
    @endif
    
    <div class="content-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>📊 Dashboard del Evento - Reporte de Estado</h1>
            <p class="subtitle">Este reporte incluye estadísticas detalladas del evento, incluyendo reacciones, compartidos, voluntarios, participantes y análisis de participación.</p>
        </div>
        
        <!-- Título del Evento -->
        <div style="text-align: center; margin: 25px auto 30px auto; padding: 20px 0; border-bottom: 3px solid #1E40AF; max-width: 90%;">
            <h2 style="font-size: 20pt; font-weight: 700; color: #1E40AF; margin: 0 0 10px 0;">{{ $evento->titulo }}</h2>
            <p style="font-size: 12pt; font-weight: 600; color: #6B7280; margin: 0;">ONG: {{ $evento->ong->nombre_ong ?? 'No disponible' }}</p>
        </div>
        
        <!-- Información del Evento y Estadísticas -->
        <div class="main-content">
        <div class="two-column-layout" style="margin-top: 150px;">
            <div class="column">
                <!-- Información del Evento -->
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">📋</span>
                        <h3 class="panel-title">Información del Evento</h3>
                    </div>
                    <table class="info-table">
                        <tr>
                            <td>ONG Organizadora:</td>
                            <td>{{ $evento->ong->nombre_ong ?? 'No disponible' }}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Inicio:</td>
                            <td>{{ $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y H:i') : 'No definida' }}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Fin:</td>
                            <td>{{ $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m/Y H:i') : 'No definida' }}</td>
                        </tr>
                        <tr>
                            <td>Ciudad:</td>
                            <td>{{ $evento->ciudad ?? 'No disponible' }}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Reporte:</td>
                            <td>{{ $fecha_generacion }}</td>
                        </tr>
                        <tr>
                            <td>Estado del Evento:</td>
                            <td><span class="status-badge">Activo</span></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Estadísticas por Categoría -->
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">📈</span>
                        <h3 class="panel-title">Estadísticas por Categoría</h3>
                    </div>
                    <div class="chart-container">
                        @php
                            $total = $estadisticas['reacciones'] + $estadisticas['compartidos'] + $estadisticas['voluntarios'] + $estadisticas['participantes'];
                            $maxValue = max($estadisticas['reacciones'], $estadisticas['compartidos'], $estadisticas['voluntarios'], $estadisticas['participantes'], 1);
                        @endphp
                        <div class="chart-bar">
                            <span class="chart-label">Reacciones</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill blue" style="width: {{ ($estadisticas['reacciones'] / $maxValue) * 100 }}%;">
                                    @if(($estadisticas['reacciones'] / $maxValue) * 100 > 15){{ $estadisticas['reacciones'] }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $estadisticas['reacciones'] }}</span>
                        </div>
                        <div class="chart-bar">
                            <span class="chart-label">Compartidos</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill green" style="width: {{ ($estadisticas['compartidos'] / $maxValue) * 100 }}%;">
                                    @if(($estadisticas['compartidos'] / $maxValue) * 100 > 15){{ $estadisticas['compartidos'] }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $estadisticas['compartidos'] }}</span>
                        </div>
                        <div class="chart-bar">
                            <span class="chart-label">Voluntarios</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill blue" style="width: {{ ($estadisticas['voluntarios'] / $maxValue) * 100 }}%;">
                                    @if(($estadisticas['voluntarios'] / $maxValue) * 100 > 15){{ $estadisticas['voluntarios'] }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $estadisticas['voluntarios'] }}</span>
                        </div>
                        <div class="chart-bar">
                            <span class="chart-label">Participantes</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill green" style="width: {{ ($estadisticas['participantes'] / $maxValue) * 100 }}%;">
                                    @if(($estadisticas['participantes'] / $maxValue) * 100 > 15){{ $estadisticas['participantes'] }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $estadisticas['participantes'] }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Participantes por Estado -->
                @if(!empty($graficas['participantes_por_estado']))
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">👥</span>
                        <h3 class="panel-title">Participantes por Estado</h3>
                    </div>
                    <div class="chart-container">
                        @php
                            $maxEstado = max(array_values($graficas['participantes_por_estado']), [1])[0];
                        @endphp
                        @foreach($graficas['participantes_por_estado'] as $estado => $cantidad)
                        <div class="chart-bar">
                            <span class="chart-label">{{ ucfirst($estado) }}</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill {{ $estado === 'aprobada' ? 'green' : ($estado === 'pendiente' ? 'blue' : 'blue') }}" style="width: {{ ($cantidad / $maxEstado) * 100 }}%;">
                                    @if(($cantidad / $maxEstado) * 100 > 15){{ $cantidad }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $cantidad }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <div class="column">
                <!-- Estadísticas Principales -->
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">📊</span>
                        <h3 class="panel-title">Progreso del Evento</h3>
                    </div>
                    <div class="progress-circle">
                        <div class="progress-circle-inner">
                            @php
                                $progreso = $estadisticas['participantes'] > 0 
                                    ? round(($estadisticas['participantes_aprobados'] / $estadisticas['participantes']) * 100) 
                                    : 0;
                            @endphp
                            <div class="progress-percentage">{{ $progreso }}%</div>
                            <div class="progress-label">Completado</div>
                        </div>
                    </div>
                    <div class="color-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #3B82F6;"></div>
                            <span>En Progreso</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #10B981;"></div>
                            <span>Completado</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tarjetas de Estadísticas -->
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-card primary">
                            <h3>Reacciones</h3>
                            <div class="value">{{ $estadisticas['reacciones'] }}</div>
                        </div>
                        <div class="stat-card success">
                            <h3>Compartidos</h3>
                            <div class="value">{{ $estadisticas['compartidos'] }}</div>
                        </div>
                    </div>
                    <div class="stats-row">
                        <div class="stat-card info">
                            <h3>Voluntarios</h3>
                            <div class="value">{{ $estadisticas['voluntarios'] }}</div>
                        </div>
                        <div class="stat-card warning">
                            <h3>Participantes</h3>
                            <div class="value">{{ $estadisticas['participantes'] }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Detalles de Participación -->
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">📋</span>
                        <h3 class="panel-title">Detalles de Participación</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Métrica</th>
                                <th style="text-align: right;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Participantes Aprobados</td>
                                <td style="text-align: right;"><span class="badge badge-success">{{ $estadisticas['participantes_aprobados'] }}</span></td>
                            </tr>
                            <tr>
                                <td>Participantes Pendientes</td>
                                <td style="text-align: right;"><span class="badge badge-warning">{{ $estadisticas['participantes_pendientes'] }}</span></td>
                            </tr>
                            <tr>
                                <td>Total Reacciones</td>
                                <td style="text-align: right;"><span class="badge badge-info">{{ $estadisticas['reacciones'] }}</span></td>
                            </tr>
                            <tr>
                                <td>Total Compartidos</td>
                                <td style="text-align: right;"><span class="badge badge-success">{{ $estadisticas['compartidos'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Reacciones y Compartidos por Día -->
        <div class="two-column-layout" style="margin-top: 75px;">
            <div class="column">
                @if(!empty($graficas['reacciones_por_dia']))
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">❤️</span>
                        <h3 class="panel-title">Reacciones por Día</h3>
                    </div>
                    <div class="chart-container">
                        @php
                            $maxReacciones = max(array_values($graficas['reacciones_por_dia']), [1])[0];
                        @endphp
                        @foreach($graficas['reacciones_por_dia'] as $fecha => $cantidad)
                        <div class="chart-bar">
                            <span class="chart-label">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill blue" style="width: {{ ($cantidad / $maxReacciones) * 100 }}%;">
                                    @if(($cantidad / $maxReacciones) * 100 > 15){{ $cantidad }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $cantidad }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <div class="column">
                @if(!empty($graficas['compartidos_por_dia']))
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-icon">📤</span>
                        <h3 class="panel-title">Compartidos por Día</h3>
                    </div>
                    <div class="chart-container">
                        @php
                            $maxCompartidos = max(array_values($graficas['compartidos_por_dia']), [1])[0];
                        @endphp
                        @foreach($graficas['compartidos_por_dia'] as $fecha => $cantidad)
                        <div class="chart-bar">
                            <span class="chart-label">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                            <div class="chart-bar-visual">
                                <div class="chart-bar-fill green" style="width: {{ ($cantidad / $maxCompartidos) * 100 }}%;">
                                    @if(($cantidad / $maxCompartidos) * 100 > 15){{ $cantidad }}@endif
                                </div>
                            </div>
                            <span class="chart-value">{{ $cantidad }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Inscripciones por Día -->
        @if(!empty($graficas['inscripciones_por_dia']))
        <div class="panel centered-panel" style="margin-left: auto; margin-right: auto; max-width: 95%;">
            <div class="panel-header">
                <span class="panel-icon">📅</span>
                <h3 class="panel-title">Inscripciones por Día</h3>
            </div>
            <div class="chart-container">
                @php
                    $maxInscripciones = max(array_values($graficas['inscripciones_por_dia']), [1])[0];
                @endphp
                @foreach($graficas['inscripciones_por_dia'] as $fecha => $cantidad)
                <div class="chart-bar">
                    <span class="chart-label">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                    <div class="chart-bar-visual">
                        <div class="chart-bar-fill green" style="width: {{ ($cantidad / $maxInscripciones) * 100 }}%;">
                            @if(($cantidad / $maxInscripciones) * 100 > 15){{ $cantidad }}@endif
                        </div>
                    </div>
                    <span class="chart-value">{{ $cantidad }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Lista de Personas que Reaccionaron -->
        @if(!empty($reacciones_con_nombres))
        <div class="panel page-break centered-panel" style="margin-left: auto; margin-right: auto; max-width: 95%;">
            <div class="panel-header">
                <span class="panel-icon">❤️</span>
                <h3 class="panel-title">Personas que Reaccionaron ({{ count($reacciones_con_nombres) }})</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Nombre</th>
                        <th style="width: 35%;">Email</th>
                        <th style="width: 20%; text-align: right;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reacciones_con_nombres as $index => $reaccion)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="participant-name">{{ $reaccion['nombre'] }}</span></td>
                        <td><span class="participant-email">{{ $reaccion['email'] ?: 'No disponible' }}</span></td>
                        <td style="text-align: right;">{{ $reaccion['fecha'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Lista de Participantes -->
        @if(!empty($participantes_con_nombres))
        <div class="panel page-break centered-panel" style="margin-left: auto; margin-right: auto; margin-top: 75px; max-width: 95%;">
            <div class="panel-header">
                <span class="panel-icon">👥</span>
                <h3 class="panel-title">Participantes del Evento ({{ count($participantes_con_nombres) }})</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Nombre</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 10%; text-align: center;">Asistió</th>
                        <th style="width: 15%; text-align: right;">Fecha Inscripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participantes_con_nombres as $index => $participante)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="participant-name">{{ $participante['nombre'] }}</span></td>
                        <td><span class="participant-email">{{ $participante['email'] ?: 'No disponible' }}</span></td>
                        <td>
                            @if($participante['estado'] === 'aprobada')
                                <span class="badge badge-success">{{ ucfirst($participante['estado']) }}</span>
                            @elseif($participante['estado'] === 'pendiente')
                                <span class="badge badge-warning">{{ ucfirst($participante['estado']) }}</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($participante['estado']) }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($participante['asistio'])
                                <span style="color: #10B981; font-weight: 600;">✓ Sí</span>
                            @else
                                <span style="color: #9CA3AF;">No</span>
                            @endif
                        </td>
                        <td style="text-align: right;">{{ $participante['fecha_inscripcion'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        </div>
    </div>
    
    <div class="footer">
        <p>Dashboard generado por UNI2 - {{ $fecha_generacion }}</p>
    </div>
</body>
</html>

