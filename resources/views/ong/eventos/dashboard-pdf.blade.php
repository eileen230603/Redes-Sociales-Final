<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Dashboard Estadístico del Evento - {{ $evento->titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 2cm 2cm 2cm 2cm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.6;
            background: #FFFFFF;
        }
        
        /* Marca de agua centrada en todas las páginas */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 400px;
            height: auto;
            pointer-events: none;
        }
        
        /* Header consistente */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: #FFFFFF;
            border-bottom: 1px solid #E0E0E0;
            padding: 5px 2cm;
            display: table;
            width: 100%;
            page-break-inside: avoid;
            z-index: 100;
        }
        
        .pdf-header-left {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
        }
        
        .pdf-header-center {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
            color: #0C2B44;
            font-weight: 600;
        }
        
        .pdf-header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 8pt;
            color: #6c757d;
        }
        
        .pdf-header-logo {
            width: 40px;
            height: auto;
        }
        
        /* Footer consistente */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: #FFFFFF;
            border-top: 1px solid #E0E0E0;
            padding: 5px 2cm;
            display: table;
            width: 100%;
            page-break-inside: avoid;
            z-index: 100;
        }
        
        .pdf-footer-left {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            font-size: 8pt;
            color: #6c757d;
        }
        
        .pdf-footer-center {
            display: table-cell;
            width: 20%;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
            color: #0C2B44;
            font-weight: 600;
        }
        
        .pdf-footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
            font-size: 8pt;
            color: #6c757d;
        }
        
        /* Contenido principal */
        .content {
            margin-top: 40px;
            margin-bottom: 40px;
            padding: 0;
        }
        
        /* Títulos jerárquicos */
        .titulo-principal {
            font-size: 28pt;
            font-weight: bold;
            color: #0C2B44;
            margin-bottom: 15mm;
            text-align: center;
        }
        
        .titulo-seccion-1 {
            font-size: 18pt;
            font-weight: bold;
            color: #00A36C;
            margin-top: 10mm;
            margin-bottom: 8mm;
            padding-bottom: 5mm;
            border-bottom: 2px solid #00A36C;
        }
        
        .titulo-seccion-2 {
            font-size: 14pt;
            font-weight: 600;
            color: #0C2B44;
            margin-top: 8mm;
            margin-bottom: 5mm;
        }
        
        .titulo-subseccion {
            font-size: 12pt;
            font-weight: 500;
            color: #333333;
            margin-top: 5mm;
            margin-bottom: 3mm;
        }
        
        /* Portada */
        .portada {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40mm 30mm;
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: white;
            position: relative;
            page-break-after: always;
        }
        
        .portada-logo-uni2 {
            width: 120px;
            height: auto;
            margin-bottom: 20mm;
        }
        
        .portada-titulo {
            font-size: 32pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10mm;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .portada-evento {
            font-size: 24pt;
            font-weight: 600;
            text-align: center;
            margin: 20mm 0;
            padding: 15mm;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .portada-logo-ong {
            max-width: 150px;
            max-height: 150px;
            margin: 10mm 0;
        }
        
        .portada-meta {
            text-align: center;
            font-size: 11pt;
            opacity: 0.9;
            margin-top: auto;
        }
        
        .portada-resumen {
            background: rgba(255, 255, 255, 0.15);
            padding: 10mm;
            border-radius: 8px;
            margin-top: 15mm;
            width: 100%;
        }
        
        .portada-resumen-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .portada-resumen-item {
            display: table-cell;
            text-align: center;
            padding: 5mm;
        }
        
        .portada-resumen-valor {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        .portada-resumen-label {
            font-size: 9pt;
            opacity: 0.9;
        }
        
        /* Página */
        .page {
            page-break-after: always;
            position: relative;
        }
        
        .page:last-child {
            page-break-after: auto;
        }
        
        /* Métricas principales */
        .metricas-grid {
            display: table;
            width: 100%;
            margin: 10mm 0;
            border-collapse: separate;
            border-spacing: 5mm;
        }
        
        .metrica-item {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        /* Tablas profesionales */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8mm 0;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        thead {
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        }
        
        thead th {
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
            letter-spacing: 0.5px;
            padding: 10px;
            text-align: left;
            border: none;
        }
        
        tbody td {
            padding: 10px;
            border: 1px solid #E0E0E0;
            font-size: 9pt;
        }
        
        tbody tr:nth-child(even) {
            background-color: #F8F9FA;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #FFFFFF;
        }
        
        tbody tr:hover {
            background-color: #E9ECEF;
        }
        
        tfoot {
            background: #0C2B44;
        }
        
        tfoot td {
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: right;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Badges de estado */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        /* Gráficos */
        .grafico-container {
            margin: 10mm 0;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .grafico-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .grafico-titulo {
            font-size: 12pt;
            font-weight: 600;
            color: #0C2B44;
            margin-bottom: 5mm;
        }
        
        /* Comparativas */
        .comparativa-box {
            background: #f8f9fa;
            border-left: 4px solid #00A36C;
            padding: 8mm;
            margin: 10mm 0;
            border-radius: 4px;
            page-break-inside: avoid;
        }
        
        .comparativa-item {
            display: table;
            width: 100%;
            margin-bottom: 5mm;
        }
        
        .comparativa-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #0C2B44;
        }
        
        .comparativa-valor {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-size: 14pt;
            font-weight: bold;
            color: #0C2B44;
        }
        
        .comparativa-crecimiento {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-weight: bold;
        }
        
        .crecimiento-positivo {
            color: #00A36C;
        }
        
        .crecimiento-negativo {
            color: #dc3545;
        }
        
        .crecimiento-estable {
            color: #6c757d;
        }
        
        /* Resumen ejecutivo */
        .resumen-ejecutivo {
            background: #f8f9fa;
            padding: 10mm;
            border-radius: 8px;
            margin: 10mm 0;
            border-left: 4px solid #0C2B44;
            page-break-inside: avoid;
        }
        
        /* Insights */
        .insights-box {
            background: #e7f3ff;
            border-left: 4px solid #17a2b8;
            padding: 8mm;
            margin: 10mm 0;
            border-radius: 4px;
            page-break-inside: avoid;
        }
        
        .insights-box ul {
            list-style: none;
            padding-left: 0;
        }
        
        .insights-box li {
            margin-bottom: 3mm;
            padding-left: 20px;
            position: relative;
        }
        
        .insights-box li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #17a2b8;
            font-weight: bold;
            font-size: 14pt;
        }
        
        /* Iconos usando símbolos Unicode */
        .icon {
            display: inline-block;
            margin-right: 5px;
            font-weight: bold;
        }
        
        /* Colores de estado en tablas */
        .celda-aprobada {
            background-color: #d4edda !important;
            color: #155724;
        }
        
        .celda-pendiente {
            background-color: #fff3cd !important;
            color: #856404;
        }
        
        .celda-rechazada {
            background-color: #f8d7da !important;
            color: #721c24;
        }
        
        /* Logros destacados */
        .logros-grid {
            display: table;
            width: 100%;
            margin: 10mm 0;
            border-collapse: separate;
            border-spacing: 5mm;
        }
        
        .logro-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 5mm;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #00A36C;
        }
        
        /* Áreas de mejora */
        .mejoras-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 8mm;
            margin: 10mm 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Marca de agua en todas las páginas -->
    @if($logo_uni2 && file_exists($logo_uni2))
    <img src="{{ $logo_uni2 }}" alt="UNI2" class="watermark">
    @endif
    
    <!-- Header fijo en todas las páginas -->
    <div class="pdf-header">
        <div class="pdf-header-left">
            @if($logo_uni2 && file_exists($logo_uni2))
            <img src="{{ $logo_uni2 }}" alt="UNI2" class="pdf-header-logo">
            @endif
        </div>
        <div class="pdf-header-center">
            {{ Str::limit($evento->titulo, 40) }}
        </div>
        <div class="pdf-header-right">
            {{ $fecha_generacion->format('d/m/Y H:i') }}
        </div>
    </div>
    
    <!-- Footer fijo en todas las páginas -->
    <div class="pdf-footer">
        <div class="pdf-footer-left">
            {{ $ong->nombre_ong ?? 'ONG' }}
        </div>
        <div class="pdf-footer-center">
            Página <span class="page"></span>
        </div>
        <div class="pdf-footer-right">
            Dashboard Estadístico - Confidencial
        </div>
    </div>
    
    <!-- PÁGINA 1: PORTADA -->
    <div class="page">
        <div class="portada">
            <div style="text-align: center;">
                @if($logo_uni2 && file_exists($logo_uni2))
                <img src="{{ $logo_uni2 }}" alt="UNI2" class="portada-logo-uni2">
                @endif
                
                <div class="portada-titulo">Dashboard Estadístico del Evento</div>
                
                @if($logo_ong)
                <img src="{{ $logo_ong }}" alt="Logo ONG" class="portada-logo-ong">
                @endif
            </div>
            
            <div class="portada-evento">
                {{ $evento->titulo }}
            </div>
            
            <div class="portada-resumen">
                <div class="portada-resumen-grid">
                    <div class="portada-resumen-item">
                        <div class="portada-resumen-valor">{{ number_format($datos['metricas']['reacciones'] ?? 0, 0, ',', '.') }}</div>
                        <div class="portada-resumen-label">Reacciones</div>
                    </div>
                    <div class="portada-resumen-item">
                        <div class="portada-resumen-valor">{{ number_format($datos['metricas']['compartidos'] ?? 0, 0, ',', '.') }}</div>
                        <div class="portada-resumen-label">Compartidos</div>
                    </div>
                    <div class="portada-resumen-item">
                        <div class="portada-resumen-valor">{{ number_format($datos['metricas']['voluntarios'] ?? 0, 0, ',', '.') }}</div>
                        <div class="portada-resumen-label">Voluntarios</div>
                    </div>
                    <div class="portada-resumen-item">
                        <div class="portada-resumen-valor">{{ number_format($datos['metricas']['participantes_total'] ?? 0, 0, ',', '.') }}</div>
                        <div class="portada-resumen-label">Participantes</div>
                    </div>
                </div>
            </div>
            
            <div class="portada-meta">
                <div style="margin-bottom: 5mm;">
                    <strong>Generado el:</strong> {{ $fecha_generacion->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] HH:mm') }}
                </div>
                <div style="margin-bottom: 5mm;">
                    <strong>Período del reporte:</strong> {{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}
                </div>
                <div style="margin-top: 15mm; font-size: 9pt; opacity: 0.8;">
                    Documento confidencial - Solo para uso interno de la organización
                </div>
            </div>
        </div>
    </div>
    
    <!-- PÁGINA 2: RESUMEN DE MÉTRICAS PRINCIPALES -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">📊</span> Métricas Generales del Evento
            </h1>
            
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="icon" style="color: #dc3545;">❤️</span> Total de Reacciones</td>
                        <td class="text-right">{{ number_format($datos['metricas']['reacciones'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #00A36C;">📤</span> Total de Compartidos</td>
                        <td class="text-right">{{ number_format($datos['metricas']['compartidos'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #17a2b8;">👥</span> Total de Voluntarios</td>
                        <td class="text-right">{{ number_format($datos['metricas']['voluntarios'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #ffc107;">✓</span> Total de Participantes</td>
                        <td class="text-right">{{ number_format($datos['metricas']['participantes_total'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #28a745;">✅</span> Participantes Aprobados</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['participantes_aprobados'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #ffc107;">⏳</span> Participantes Pendientes</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['participantes_pendientes'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #dc3545;">❌</span> Participantes Rechazados</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['participantes_rechazados'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="icon" style="color: #00A36C;">%</span> Tasa de Aprobación</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['tasa_aprobacion'] ?? 0, 2, ',', '.') }}%</td>
                    </tr>
                </tbody>
            </table>
            
            @if(!empty($datos['comparativas']))
            <h2 class="titulo-seccion-2">Comparativa con Período Anterior</h2>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th class="text-right">Valor Actual</th>
                        <th class="text-right">Valor Anterior</th>
                        <th class="text-right">Cambio Absoluto</th>
                        <th class="text-right">Cambio Porcentual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['comparativas'] as $metrica => $comparativa)
                    @php
                        $cambioAbsoluto = ($comparativa['actual'] ?? 0) - ($comparativa['anterior'] ?? 0);
                        $cambioPorcentual = $comparativa['crecimiento'] ?? 0;
                        $tendencia = $comparativa['tendencia'] ?? 'stable';
                    @endphp
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $metrica)) }}</td>
                        <td class="text-right">{{ number_format($comparativa['actual'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($comparativa['anterior'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right {{ $cambioAbsoluto >= 0 ? 'crecimiento-positivo' : 'crecimiento-negativo' }}">
                            {{ $cambioAbsoluto >= 0 ? '+' : '' }}{{ number_format($cambioAbsoluto, 0, ',', '.') }}
                        </td>
                        <td class="text-right {{ $tendencia === 'up' ? 'crecimiento-positivo' : ($tendencia === 'down' ? 'crecimiento-negativo' : 'crecimiento-estable') }}">
                            @if($tendencia === 'up')
                                ↑ {{ number_format(abs($cambioPorcentual), 2, ',', '.') }}%
                            @elseif($tendencia === 'down')
                                ↓ {{ number_format(abs($cambioPorcentual), 2, ',', '.') }}%
                            @else
                                → {{ number_format($cambioPorcentual, 2, ',', '.') }}%
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    
    <!-- PÁGINA 3: GRÁFICOS DE TENDENCIAS TEMPORALES -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">📈</span> Análisis de Tendencias
            </h1>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Reacciones por Día</div>
                @if(isset($graficos_urls['reacciones']))
                <img src="{{ $graficos_urls['reacciones'] }}" alt="Gráfico de Reacciones">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Compartidos por Día</div>
                @if(isset($graficos_urls['compartidos']))
                <img src="{{ $graficos_urls['compartidos'] }}" alt="Gráfico de Compartidos">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Inscripciones por Día</div>
                @if(isset($graficos_urls['inscripciones']))
                <img src="{{ $graficos_urls['inscripciones'] }}" alt="Gráfico de Inscripciones">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Comparativa Reacciones vs Compartidos</div>
                @if(isset($graficos_urls['comparativa']))
                <img src="{{ $graficos_urls['comparativa'] }}" alt="Gráfico Comparativo">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- PÁGINA 4: DISTRIBUCIÓN Y ANÁLISIS -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">🔍</span> Análisis Detallado
            </h1>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Distribución de Participantes por Estado</div>
                @if(isset($graficos_urls['distribucion_estados']))
                <img src="{{ $graficos_urls['distribucion_estados'] }}" alt="Gráfico de Distribución">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Actividad por Día de la Semana</div>
                @if(isset($graficos_urls['actividad_semana']))
                <img src="{{ $graficos_urls['actividad_semana'] }}" alt="Gráfico de Actividad Semanal">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <h2 class="titulo-seccion-2">Top 10 Participantes Más Activos</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th class="text-right">Total Reacciones</th>
                        <th class="text-right">Eventos Participados</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($datos['top_participantes'] ?? [], 0, 10) as $index => $participante)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participante['nombre'] ?? 'Participante' }}</td>
                        <td>{{ $participante['email'] ?? 'N/A' }}</td>
                        <td class="text-right">{{ $participante['total_actividades'] ?? 0 }}</td>
                        <td class="text-right">{{ $participante['eventos_participados'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- PÁGINA 5: TABLAS DETALLADAS -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">📋</span> Datos Detallados
            </h1>
            
            <h2 class="titulo-seccion-2">Reacciones por Día (Últimos 14 días)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-right">Cantidad de Reacciones</th>
                        <th class="text-right">Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $acumuladoReacciones = 0;
                        $reaccionesPorDia = $datos['reacciones_por_dia'] ?? [];
                    @endphp
                    @foreach($reaccionesPorDia as $fecha => $datosFecha)
                    @php
                        $acumuladoReacciones += $datosFecha['cantidad'] ?? 0;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($datosFecha['cantidad'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($acumuladoReacciones, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($reaccionesPorDia, 'cantidad')), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($acumuladoReacciones, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <h2 class="titulo-seccion-2">Compartidos por Día (Últimos 14 días)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-right">Cantidad de Compartidos</th>
                        <th class="text-right">Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $acumuladoCompartidos = 0;
                        $compartidosPorDia = $datos['compartidos_por_dia'] ?? [];
                    @endphp
                    @foreach($compartidosPorDia as $fecha => $datosFecha)
                    @php
                        $acumuladoCompartidos += $datosFecha['cantidad'] ?? 0;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($datosFecha['cantidad'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($acumuladoCompartidos, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($compartidosPorDia, 'cantidad')), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($acumuladoCompartidos, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <h2 class="titulo-seccion-2">Inscripciones por Día (Últimos 14 días)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-right">Total Inscripciones</th>
                        <th class="text-right">Aprobadas</th>
                        <th class="text-right">Pendientes</th>
                        <th class="text-right">Rechazadas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $inscripcionesPorDia = $datos['inscripciones_por_dia'] ?? [];
                    @endphp
                    @foreach($inscripcionesPorDia as $fecha => $datosFecha)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($datosFecha['total'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right celda-aprobada">{{ number_format($datosFecha['aprobadas'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right celda-pendiente">{{ number_format($datosFecha['pendientes'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right celda-rechazada">{{ number_format($datosFecha['rechazadas'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($inscripcionesPorDia, 'total')), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($inscripcionesPorDia, 'aprobadas')), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($inscripcionesPorDia, 'pendientes')), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(array_sum(array_column($inscripcionesPorDia, 'rechazadas')), 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- PÁGINA 6: ANÁLISIS AVANZADO -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">📊</span> Métricas Avanzadas
            </h1>
            
            <div class="grafico-container">
                <div class="grafico-titulo">Métricas Generales (Gráfico Radar)</div>
                @if(isset($graficos_urls['radar']))
                <img src="{{ $graficos_urls['radar'] }}" alt="Gráfico Radar">
                @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
                @endif
            </div>
            
            <h2 class="titulo-seccion-2">Análisis de Engagement</h2>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th>Fórmula</th>
                        <th class="text-right">Valor</th>
                        <th>Interpretación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Tasa de Conversión</strong></td>
                        <td>Inscripciones / Vistas estimadas</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['tasa_conversion'] ?? 0, 2, ',', '.') }}%</td>
                        <td>
                            @if(($metricas_adicionales['tasa_conversion'] ?? 0) >= 5)
                                Excelente tasa de conversión
                            @elseif(($metricas_adicionales['tasa_conversion'] ?? 0) >= 2)
                                Buena tasa de conversión
                            @else
                                Tasa de conversión mejorable
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tasa de Participación</strong></td>
                        <td>Asistentes / Inscritos</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['tasa_participacion'] ?? 0, 2, ',', '.') }}%</td>
                        <td>
                            @if(($metricas_adicionales['tasa_participacion'] ?? 0) >= 70)
                                Alta participación
                            @elseif(($metricas_adicionales['tasa_participacion'] ?? 0) >= 50)
                                Participación moderada
                            @else
                                Participación baja
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Engagement Rate</strong></td>
                        <td>(Reacciones + Compartidos) / Participantes</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['engagement_rate'] ?? 0, 2, ',', '.') }}%</td>
                        <td>
                            @if(($metricas_adicionales['engagement_rate'] ?? 0) >= 50)
                                Alto engagement
                            @elseif(($metricas_adicionales['engagement_rate'] ?? 0) >= 25)
                                Engagement moderado
                            @else
                                Engagement bajo
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Promedio de Reacciones por Participante</strong></td>
                        <td>Total Reacciones / Total Participantes</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['promedio_reacciones_por_participante'] ?? 0, 2, ',', '.') }}</td>
                        <td>
                            @if(($metricas_adicionales['promedio_reacciones_por_participante'] ?? 0) >= 2)
                                Alto interés de los participantes
                            @elseif(($metricas_adicionales['promedio_reacciones_por_participante'] ?? 0) >= 1)
                                Interés moderado
                            @else
                                Bajo interés
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
            @if(!empty($insights))
            <h2 class="titulo-seccion-2">Insights y Recomendaciones</h2>
            <div class="insights-box">
                <ul>
                    @foreach($insights as $insight)
                    <li>{{ $insight }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    
    <!-- PÁGINA 7: RESUMEN EJECUTIVO FINAL -->
    <div class="page">
        <div class="content">
            <h1 class="titulo-seccion-1">
                <span class="icon">✅</span> Conclusiones
            </h1>
            
            <h2 class="titulo-seccion-2">Resumen Consolidado</h2>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th class="text-right">Valor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total de Reacciones</td>
                        <td class="text-right">{{ number_format($datos['metricas']['reacciones'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if(($datos['metricas']['reacciones'] ?? 0) >= 100)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($datos['metricas']['reacciones'] ?? 0) >= 50)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Total de Compartidos</td>
                        <td class="text-right">{{ number_format($datos['metricas']['compartidos'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if(($datos['metricas']['compartidos'] ?? 0) >= 50)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($datos['metricas']['compartidos'] ?? 0) >= 25)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Total de Voluntarios</td>
                        <td class="text-right">{{ number_format($datos['metricas']['voluntarios'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if(($datos['metricas']['voluntarios'] ?? 0) >= 20)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($datos['metricas']['voluntarios'] ?? 0) >= 10)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Total de Participantes</td>
                        <td class="text-right">{{ number_format($datos['metricas']['participantes_total'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if(($datos['metricas']['participantes_total'] ?? 0) >= 50)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($datos['metricas']['participantes_total'] ?? 0) >= 25)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Tasa de Aprobación</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['tasa_aprobacion'] ?? 0, 2, ',', '.') }}%</td>
                        <td class="text-center">
                            @if(($metricas_adicionales['tasa_aprobacion'] ?? 0) >= 70)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($metricas_adicionales['tasa_aprobacion'] ?? 0) >= 50)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Engagement Rate</td>
                        <td class="text-right">{{ number_format($metricas_adicionales['engagement_rate'] ?? 0, 2, ',', '.') }}%</td>
                        <td class="text-center">
                            @if(($metricas_adicionales['engagement_rate'] ?? 0) >= 50)
                                <span class="badge badge-success">Excelente</span>
                            @elseif(($metricas_adicionales['engagement_rate'] ?? 0) >= 25)
                                <span class="badge badge-info">Bueno</span>
                            @else
                                <span class="badge badge-warning">Mejorable</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
            @php
                $logros = [];
                if (($datos['metricas']['reacciones'] ?? 0) >= 100) {
                    $logros[] = ['icono' => '❤️', 'texto' => 'Más de 100 reacciones alcanzadas'];
                }
                if (($datos['metricas']['participantes_total'] ?? 0) >= 50) {
                    $logros[] = ['icono' => '👥', 'texto' => 'Más de 50 participantes registrados'];
                }
                if (($metricas_adicionales['tasa_aprobacion'] ?? 0) >= 70) {
                    $logros[] = ['icono' => '✅', 'texto' => 'Tasa de aprobación superior al 70%'];
                }
                if (($metricas_adicionales['engagement_rate'] ?? 0) >= 50) {
                    $logros[] = ['icono' => '📈', 'texto' => 'Engagement rate superior al 50%'];
                }
                
                $mejoras = [];
                if (($datos['metricas']['reacciones'] ?? 0) < 50) {
                    $mejoras[] = 'Aumentar la promoción del evento para generar más reacciones';
                }
                if (($metricas_adicionales['tasa_aprobacion'] ?? 0) < 50) {
                    $mejoras[] = 'Revisar los criterios de selección para mejorar la tasa de aprobación';
                }
                if (($metricas_adicionales['engagement_rate'] ?? 0) < 25) {
                    $mejoras[] = 'Implementar estrategias para aumentar el engagement de los participantes';
                }
            @endphp
            
            @if(!empty($logros))
            <h2 class="titulo-seccion-2">Logros Destacados</h2>
            <div class="logros-grid">
                @foreach($logros as $logro)
                <div class="logro-item">
                    <span style="font-size: 24pt; color: #00A36C; margin-bottom: 3mm; display: block;">{{ $logro['icono'] }}</span>
                    <div style="font-size: 10pt; color: #0C2B44; font-weight: 600;">{{ $logro['texto'] }}</div>
                </div>
                @endforeach
            </div>
            @endif
            
            @if(!empty($mejoras))
            <h2 class="titulo-seccion-2">Áreas de Mejora</h2>
            <div class="mejoras-box">
                <ul style="list-style: none; padding-left: 0;">
                    @foreach($mejoras as $mejora)
                    <li style="margin-bottom: 3mm; padding-left: 20px; position: relative;">
                        <span style="position: absolute; left: 0; color: #ffc107; font-weight: bold;">→</span>
                        {{ $mejora }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="resumen-ejecutivo" style="margin-top: 15mm;">
                <h3 style="color: #0C2B44; margin-bottom: 5mm; font-size: 14pt;">
                    <span class="icon">💡</span> Recomendación Final
                </h3>
                <p style="font-size: 10pt; line-height: 1.6; color: #555;">
                    Basado en el análisis de las métricas del evento <strong>{{ $evento->titulo }}</strong>, 
                    se recomienda continuar con estrategias similares para futuros eventos, 
                    prestando especial atención a las áreas de mejora identificadas. 
                    El evento ha demostrado un impacto significativo en la comunidad, 
                    con {{ number_format($datos['metricas']['participantes_total'] ?? 0, 0, ',', '.') }} participantes 
                    y {{ number_format($datos['metricas']['reacciones'] ?? 0, 0, ',', '.') }} interacciones, 
                    lo cual refleja un alto nivel de engagement y participación.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
