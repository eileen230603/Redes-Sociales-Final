<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard ONG - {{ $ong->nombre_ong ?? $ong->nombre ?? 'ONG' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 30px 50px;
            size: letter portrait;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
            background: #fff;
            margin: 30px 50px;
        }
        
        /* Marca de agua */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.05;
            z-index: -1;
            font-size: 120pt;
            font-weight: bold;
            color: #0C2B44;
            white-space: nowrap;
        }
        
        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo-container {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }
        
        .logo-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #0C2B44;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
        }
        
        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-info {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            padding-left: 15px;
        }
        
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0C2B44;
            margin-bottom: 5px;
        }
        
        .header-subtitle {
            font-size: 10pt;
            color: #666;
        }
        
        .header-right {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: right;
        }
        
        .ong-name {
            font-size: 12pt;
            font-weight: 600;
            color: #0C2B44;
            margin-bottom: 5px;
        }
        
        .folio {
            font-size: 9pt;
            color: #666;
            font-weight: bold;
        }
        
        /* Títulos de sección */
        h3 {
            font-size: 13pt;
            font-weight: bold;
            color: #0C2B44;
            margin: 20px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #0C2B44;
        }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #f8f8f8;
            border-bottom: 2px solid #000;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            color: #0C2B44;
            border: 1px solid #ccc;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #eee;
            font-size: 9pt;
            border-bottom: 1px solid #eee;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .numero-formateado {
            font-family: 'Courier New', monospace;
        }
        
        /* Sección de totales */
        .totales-section {
            margin-top: 20px;
            width: 40%;
            margin-left: auto;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .total-label {
            display: table-cell;
            width: 60%;
            text-align: right;
            padding-right: 10px;
            font-size: 10pt;
        }
        
        .total-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-size: 10pt;
            font-weight: 600;
        }
        
        .total-final {
            border-top: 2px solid #000;
            background: #f5f5f5;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .total-final-label {
            font-weight: bold;
            font-size: 11pt;
        }
        
        .total-final-value {
            font-weight: bold;
            font-size: 12pt;
        }
        
        /* Gráficas */
        .grafica-container {
            margin-bottom: 25px;
            text-align: center;
        }
        
        .grafica-container img {
            width: 100%;
            max-width: 680px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        
        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ccc;
            background: #fff;
        }
        
        .footer-info {
            margin-top: 5px;
            font-size: 8pt;
            color: #999;
        }
        
        /* Saltos de página */
        .page-break {
            page-break-before: always;
        }
        
        /* Medallas */
        .medalla {
            font-size: 14pt;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Marca de agua -->
    <div class="watermark">UNI2</div>
    
    <!-- ========== PÁGINA 1: HEADER Y TOP 10 EVENTOS ========== -->
    <div class="header">
        <div class="logo-container">
            <div class="logo-circle">
                @php
                    $logoPath = public_path('assets/img/UNI2.png');
                    $logoExists = file_exists($logoPath);
                @endphp
                @if($logoExists)
                    <img src="{{ $logoPath }}" alt="Logo UNI2">
                @else
                    <span style="font-size: 20pt; color: #0C2B44;">U2</span>
                @endif
            </div>
        </div>
        <div class="header-info">
            <div class="header-title">
                @if(isset($evento))
                    DASHBOARD DE EVENTO - {{ Str::upper($evento->titulo ?? 'EVENTO') }}
                @else
                    DASHBOARD ANALÍTICO ONG
                @endif
            </div>
            <div class="header-subtitle">
                @if(isset($evento))
                    Reporte de Estadísticas del Evento
                @else
                    Reporte de Gestión y Métricas
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="ong-name">{{ $ong->nombre_ong ?? $ong->nombre ?? 'ONG' }}</div>
            <div class="folio">
                Folio: {{ $folio ?? 'DASH-000001' }}<br>
                {{ $fecha_generacion->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
    
    <!-- Tabla de Top 10 Eventos -->
    @if(isset($evento))
        <h3>ESTADÍSTICAS DEL EVENTO</h3>
    @else
        <h3>TOP 10 EVENTOS POR ENGAGEMENT</h3>
    @endif
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">CANTIDAD</th>
                <th style="width: 5%;">CLAVE</th>
                <th style="width: 30%;">EVENTO</th>
                <th style="width: 12%;" class="text-right">REACCIONES</th>
                <th style="width: 12%;" class="text-right">COMPARTIDOS</th>
                <th style="width: 12%;" class="text-right">PARTICIPANTES</th>
                <th style="width: 12%;" class="text-right">ENGAGEMENT</th>
                <th style="width: 9%;" class="text-center">ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $topEventos = $datos['top_eventos'] ?? collect();
                $eventosCount = $topEventos->count();
                $eventoPdf = $evento ?? null; // Guardar referencia al evento del PDF si existe
            @endphp
            @foreach($topEventos as $index => $eventoItem)
            <tr>
                <td class="text-center numero-formateado">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="text-center">{{ $eventoItem->id ?? 'N/A' }}</td>
                <td><strong>{{ strlen($eventoItem->titulo ?? 'Sin título') > 40 ? substr($eventoItem->titulo ?? 'Sin título', 0, 40) . '...' : ($eventoItem->titulo ?? 'Sin título') }}</strong></td>
                <td class="text-right numero-formateado">{{ number_format($eventoItem->reacciones_count ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado">{{ number_format($eventoItem->compartidos_count ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado"><strong>{{ number_format($eventoItem->participaciones_count ?? 0, 0, ',', '.') }}</strong></td>
                <td class="text-right numero-formateado"><strong>{{ number_format($eventoItem->engagement ?? 0, 0, ',', '.') }}</strong></td>
                <td class="text-center">{{ ucfirst($eventoItem->estado ?? 'N/A') }}</td>
            </tr>
            @endforeach
            
            {{-- Rellenar con filas vacías hasta completar mínimo 6 filas --}}
            @for($i = $eventosCount; $i < 6; $i++)
            <tr>
                <td class="text-center">{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="text-center">-</td>
                <td>-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            @endfor
        </tbody>
    </table>
    
    <!-- Sección de Totales -->
    <div class="totales-section">
        <div class="total-row">
            <div class="total-label">Total Eventos Activos:</div>
            <div class="total-value numero-formateado">{{ number_format($datos['eventos_activos'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Reacciones:</div>
            <div class="total-value numero-formateado">{{ number_format($datos['total_reacciones'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Compartidos:</div>
            <div class="total-value numero-formateado">{{ number_format($datos['total_compartidos'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Voluntarios:</div>
            <div class="total-value numero-formateado">{{ number_format($datos['total_voluntarios'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="total-row total-final">
            <div class="total-label total-final-label">TOTAL IMPACTO:</div>
            <div class="total-value total-final-value numero-formateado">{{ number_format($datos['total_participantes'] ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
    
    <!-- Pie de página Página 1 -->
    <div class="footer">
        <div>Reporte N° {{ $folio ?? 'DASH-000001' }}</div>
        <div class="footer-info">{{ $fecha_generacion->format('d/m/Y H:i:s') }}</div>
    </div>
    
    <!-- ========== PÁGINA 2: GRÁFICAS ESTADÍSTICAS ========== -->
    <div class="page-break"></div>
    
    <h3>GRÁFICAS ESTADÍSTICAS</h3>
    
    <div class="grafica-container">
        <img src="{{ $grafica_tendencias ?? '' }}" alt="Tendencias Mensuales">
    </div>
    
    <div class="grafica-container">
        <img src="{{ $grafica_distribucion ?? '' }}" alt="Distribución de Estados">
    </div>
    
    <div class="grafica-container">
        <img src="{{ $grafica_comparativa ?? '' }}" alt="Comparativa Top 8 Eventos">
    </div>
    
    <div class="grafica-container">
        <img src="{{ $grafica_actividad_semanal ?? '' }}" alt="Actividad Semanal">
    </div>
    
    <!-- Pie de página Página 2 -->
    <div class="footer">
        <div>Reporte N° {{ $folio ?? 'DASH-000001' }}</div>
        <div class="footer-info">{{ $fecha_generacion->format('d/m/Y H:i:s') }}</div>
    </div>
    
    <!-- ========== PÁGINA 3: TOP 10 VOLUNTARIOS ========== -->
    <div class="page-break"></div>
    
    <h3>TOP 10 VOLUNTARIOS MÁS ACTIVOS</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 5%;"></th>
                <th style="width: 35%;">NOMBRE</th>
                <th style="width: 30%;">EMAIL</th>
                <th style="width: 11%;" class="text-right">EVENTOS</th>
                <th style="width: 11%;" class="text-right">HORAS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $topVoluntarios = $datos['top_voluntarios'] ?? collect();
            @endphp
            @foreach($topVoluntarios->take(10) as $index => $voluntario)
            <tr>
                <td class="text-center numero-formateado">{{ $index + 1 }}</td>
                <td class="text-center">
                    @if($index === 0)
                        <span class="medalla">🥇</span>
                    @elseif($index === 1)
                        <span class="medalla">🥈</span>
                    @elseif($index === 2)
                        <span class="medalla">🥉</span>
                    @else
                        &nbsp;
                    @endif
                </td>
                <td><strong>{{ $voluntario->nombre_usuario ?? $voluntario->nombre ?? 'N/A' }}</strong></td>
                <td>{{ $voluntario->correo_electronico ?? $voluntario->email ?? 'N/A' }}</td>
                <td class="text-right numero-formateado">{{ number_format($voluntario->participaciones_count ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado">{{ number_format($voluntario->horas_contribuidas ?? (($voluntario->participaciones_count ?? 0) * 2), 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            @if($topVoluntarios->count() === 0)
            <tr>
                <td colspan="6" class="text-center">No hay voluntarios registrados</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Pie de página Página 3 -->
    <div class="footer">
        <div>Reporte N° {{ $folio ?? 'DASH-000001' }}</div>
        <div class="footer-info">{{ $fecha_generacion->format('d/m/Y H:i:s') }}</div>
    </div>
    
    <!-- ========== PÁGINA 4: ACTIVIDAD ÚLTIMOS 30 DÍAS ========== -->
    <div class="page-break"></div>
    
    <h3>ACTIVIDAD ÚLTIMOS 30 DÍAS</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">FECHA</th>
                <th style="width: 20%;" class="text-right">REACCIONES</th>
                <th style="width: 20%;" class="text-right">COMPARTIDOS</th>
                <th style="width: 20%;" class="text-right">INSCRIPCIONES</th>
                <th style="width: 20%;" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $actividadReciente = $datos['actividad_reciente'] ?? [];
                $ultimos20Dias = array_slice($actividadReciente, -20, 20);
            @endphp
            @foreach($ultimos20Dias as $actividad)
            <tr>
                <td><strong>{{ $actividad['fecha'] ?? 'N/A' }}</strong></td>
                <td class="text-right numero-formateado">{{ number_format($actividad['reacciones'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado">{{ number_format($actividad['compartidos'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado">{{ number_format($actividad['inscripciones'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right numero-formateado"><strong>{{ number_format($actividad['total'] ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
            
            @if(empty($ultimos20Dias))
            <tr>
                <td colspan="5" class="text-center">No hay actividad registrada</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Pie de página Página 4 -->
    <div class="footer">
        <div>Reporte N° {{ $folio ?? 'DASH-000001' }}</div>
        <div class="footer-info">{{ $fecha_generacion->format('d/m/Y H:i:s') }} | Período: {{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</div>
    </div>
</body>
</html>
