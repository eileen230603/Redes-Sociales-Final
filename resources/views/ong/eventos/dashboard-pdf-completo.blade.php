<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Dashboard del Evento - {{ $evento->titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.5;
            background: #FFFFFF;
        }
        
        /* Portada */
        .portada {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: white;
            padding: 40mm 30mm;
            position: relative;
            page-break-after: always;
        }
        
        .portada::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ $logo_uni2 }}');
            background-size: 200px;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
        }
        
        .portada-header {
            text-align: center;
            margin-bottom: 30mm;
        }
        
        .portada-logo {
            max-width: 150px;
            max-height: 150px;
            margin: 0 auto 20px;
            display: block;
        }
        
        .portada-titulo {
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 10mm;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .portada-subtitulo {
            font-size: 18pt;
            margin-bottom: 5mm;
            opacity: 0.9;
        }
        
        .portada-evento {
            font-size: 24pt;
            font-weight: bold;
            margin-top: 20mm;
            padding: 15mm;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .portada-meta {
            margin-top: auto;
            text-align: center;
            font-size: 11pt;
            opacity: 0.8;
        }
        
        /* Contenido */
        .page {
            page-break-after: always;
        }
        
        .page:last-child {
            page-break-after: auto;
        }
        
        .header-page {
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: white;
            padding: 15mm 0 10mm;
            margin: -15mm -15mm 10mm -15mm;
            text-align: center;
        }
        
        .header-page h2 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5mm;
        }
        
        .header-page .meta {
            font-size: 9pt;
            opacity: 0.9;
        }
        
        /* Tarjetas de métricas */
        .metricas-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 15mm;
        }
        
        .metrica-card {
            display: table-cell;
            width: 20%;
            padding: 10mm;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            vertical-align: top;
        }
        
        .metrica-icono {
            font-size: 24pt;
            margin-bottom: 5mm;
        }
        
        .metrica-valor {
            font-size: 28pt;
            font-weight: bold;
            color: #0C2B44;
            margin: 5mm 0;
        }
        
        .metrica-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .metrica-card.reacciones { border-color: #dc3545; }
        .metrica-card.compartidos { border-color: #00A36C; }
        .metrica-card.voluntarios { border-color: #17a2b8; }
        .metrica-card.participantes { border-color: #ffc107; }
        
        /* Gráficos */
        .grafico-container {
            margin: 10mm 0;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .grafico-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .grafico-titulo {
            font-size: 12pt;
            font-weight: bold;
            color: #0C2B44;
            margin-bottom: 5mm;
            text-align: center;
        }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10mm 0;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        
        th {
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: white;
            padding: 8pt;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        
        td {
            padding: 6pt;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3pt 8pt;
            border-radius: 4pt;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success { background-color: #00A36C; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #0C2B44; color: white; }
        
        /* Comparativas */
        .comparativa-box {
            background: #f8f9fa;
            border-left: 4px solid #00A36C;
            padding: 8mm;
            margin: 10mm 0;
            border-radius: 4px;
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
        }
        
        .comparativa-valor {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-size: 14pt;
            font-weight: bold;
        }
        
        .comparativa-crecimiento {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-weight: bold;
        }
        
        .crecimiento-positivo { color: #00A36C; }
        .crecimiento-negativo { color: #dc3545; }
        .crecimiento-estable { color: #666; }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 5mm;
            border-top: 1px solid #e0e0e0;
        }
        
        .page-number {
            position: absolute;
            bottom: 10mm;
            right: 15mm;
            font-size: 9pt;
            color: #999;
        }
        
        /* Resumen ejecutivo */
        .resumen-ejecutivo {
            background: #f8f9fa;
            padding: 10mm;
            border-radius: 8px;
            margin: 10mm 0;
            border-left: 4px solid #0C2B44;
        }
        
        .resumen-ejecutivo h3 {
            color: #0C2B44;
            margin-bottom: 5mm;
            font-size: 14pt;
        }
        
        .resumen-ejecutivo p {
            font-size: 10pt;
            line-height: 1.6;
            color: #555;
        }
        
        /* Alerta de métricas críticas */
        .alerta {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 8mm;
            margin: 10mm 0;
            border-radius: 4px;
        }
        
        .alerta-critica {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .alerta h4 {
            color: #856404;
            margin-bottom: 3mm;
            font-size: 11pt;
        }
        
        .alerta-critica h4 {
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- PORTADA -->
    <div class="portada">
        <div class="portada-header">
            @if($logo_ong)
                <img src="{{ $logo_ong }}" alt="Logo ONG" class="portada-logo">
            @endif
            <div class="portada-titulo">Dashboard del Evento</div>
            <div class="portada-subtitulo">Reporte Completo de Métricas y Estadísticas</div>
        </div>
        
        <div class="portada-evento">
            {{ $evento->titulo }}
        </div>
        
        <div class="portada-meta">
            <div>Generado el {{ $fecha_generacion }}</div>
            <div>Período: {{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</div>
            @if(isset($evento->ong) && $evento->ong)
                <div>{{ $evento->ong->nombre_ong ?? 'ONG' }}</div>
            @else
                <div>ONG</div>
            @endif
        </div>
    </div>
    
    <!-- PÁGINA 2: RESUMEN EJECUTIVO Y MÉTRICAS PRINCIPALES -->
    <div class="page">
        <div class="header-page">
            <h2>Resumen Ejecutivo</h2>
            <div class="meta">Dashboard del Evento - {{ $evento->titulo }}</div>
        </div>
        
        <div class="resumen-ejecutivo">
            <h3>📊 Resumen del Evento</h3>
            <p>
                Este reporte presenta un análisis completo de las métricas y estadísticas del evento 
                <strong>{{ $evento->titulo }}</strong> durante el período comprendido entre 
                {{ $fecha_inicio->format('d/m/Y') }} y {{ $fecha_fin->format('d/m/Y') }}.
            </p>
            <p>
                El evento se encuentra en estado <strong>{{ ucfirst($evento->estado ?? 'activo') }}</strong> 
                y ha generado un total de <strong>{{ $datos['metricas']['reacciones'] ?? 0 }} reacciones</strong>, 
                <strong>{{ $datos['metricas']['compartidos'] ?? 0 }} compartidos</strong> y 
                <strong>{{ $datos['metricas']['participantes_total'] ?? 0 }} participantes</strong>.
            </p>
        </div>
        
        <!-- Tarjetas de Métricas -->
        <div class="metricas-grid">
            <div class="metrica-card reacciones">
                <div class="metrica-icono">❤️</div>
                <div class="metrica-valor">{{ number_format($datos['metricas']['reacciones'] ?? 0, 0, ',', '.') }}</div>
                <div class="metrica-label">Reacciones</div>
            </div>
            <div class="metrica-card compartidos">
                <div class="metrica-icono">📤</div>
                <div class="metrica-valor">{{ number_format($datos['metricas']['compartidos'] ?? 0, 0, ',', '.') }}</div>
                <div class="metrica-label">Compartidos</div>
            </div>
            <div class="metrica-card voluntarios">
                <div class="metrica-icono">👥</div>
                <div class="metrica-valor">{{ number_format($datos['metricas']['voluntarios'] ?? 0, 0, ',', '.') }}</div>
                <div class="metrica-label">Voluntarios</div>
            </div>
            <div class="metrica-card participantes">
                <div class="metrica-icono">✓</div>
                <div class="metrica-valor">{{ number_format($datos['metricas']['participantes_total'] ?? 0, 0, ',', '.') }}</div>
                <div class="metrica-label">Participantes</div>
            </div>
        </div>
        
        <!-- Comparativas -->
        <div class="comparativa-box">
            <h3 style="color: #0C2B44; margin-bottom: 8mm; font-size: 12pt;">📈 Comparativa con Período Anterior</h3>
            @foreach($datos['comparativas'] ?? [] as $metrica => $comparativa)
            <div class="comparativa-item">
                <div class="comparativa-label">{{ ucfirst(str_replace('_', ' ', $metrica)) }}:</div>
                <div class="comparativa-valor">{{ number_format($comparativa['actual'] ?? 0, 0, ',', '.') }}</div>
                <div class="comparativa-crecimiento {{ $comparativa['tendencia'] === 'up' ? 'crecimiento-positivo' : ($comparativa['tendencia'] === 'down' ? 'crecimiento-negativo' : 'crecimiento-estable') }}">
                    {{ $comparativa['crecimiento'] > 0 ? '+' : '' }}{{ number_format($comparativa['crecimiento'] ?? 0, 2) }}%
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Alertas de métricas críticas -->
        @php
            $participantesAprobados = $datos['metricas']['participantes_por_estado']['aprobada'] ?? 0;
            $totalParticipantes = $datos['metricas']['participantes_total'] ?? 1;
            $tasaAprobacion = ($totalParticipantes > 0) ? ($participantesAprobados / $totalParticipantes) * 100 : 0;
        @endphp
        
        @if($tasaAprobacion < 50 && $totalParticipantes > 10)
        <div class="alerta alerta-critica">
            <h4>⚠️ Alerta: Tasa de Aprobación Baja</h4>
            <p>La tasa de aprobación de participantes es del {{ number_format($tasaAprobacion, 2) }}%, 
            lo cual está por debajo del umbral recomendado del 50%.</p>
        </div>
        @endif
    </div>
    
    <!-- PÁGINA 3: GRÁFICOS DE TENDENCIAS -->
    <div class="page">
        <div class="header-page">
            <h2>Tendencias Temporales</h2>
            <div class="meta">Análisis de actividad a lo largo del tiempo</div>
        </div>
        
        <div class="grafico-container">
            <div class="grafico-titulo">Reacciones y Compartidos por Día</div>
            @if(isset($graficos_urls['tendencias']))
                <img src="{{ $graficos_urls['tendencias'] }}" alt="Gráfico de Tendencias">
            @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
            @endif
        </div>
        
        <div class="grafico-container">
            <div class="grafico-titulo">Actividad Semanal</div>
            @if(isset($graficos_urls['actividad_semanal']))
                <img src="{{ $graficos_urls['actividad_semanal'] }}" alt="Gráfico de Actividad Semanal">
            @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
            @endif
        </div>
    </div>
    
    <!-- PÁGINA 4: DISTRIBUCIÓN Y GRÁFICO RADAR -->
    <div class="page">
        <div class="header-page">
            <h2>Distribución y Métricas Generales</h2>
            <div class="meta">Análisis de distribución y comparación de métricas</div>
        </div>
        
        <div class="grafico-container">
            <div class="grafico-titulo">Distribución de Participantes por Estado</div>
            @if(isset($graficos_urls['distribucion_estados']))
                <img src="{{ $graficos_urls['distribucion_estados'] }}" alt="Gráfico de Distribución">
            @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
            @endif
        </div>
        
        <div class="grafico-container">
            <div class="grafico-titulo">Comparativa de Métricas (Período Actual vs Anterior)</div>
            @if(isset($graficos_urls['comparativas']))
                <img src="{{ $graficos_urls['comparativas'] }}" alt="Gráfico de Comparativas">
            @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
            @endif
        </div>
        
        <div class="grafico-container">
            <div class="grafico-titulo">Métricas Generales (Gráfico Radar)</div>
            @if(isset($graficos_urls['radar']))
                <img src="{{ $graficos_urls['radar'] }}" alt="Gráfico Radar">
            @else
                <p style="text-align: center; color: #999; padding: 20mm;">Gráfico no disponible</p>
            @endif
        </div>
    </div>
    
    <!-- PÁGINA 5: TABLA DE ACTIVIDAD RECIENTE -->
    <div class="page">
        <div class="header-page">
            <h2>Actividad de los Últimos 10 Días</h2>
            <div class="meta">Desglose diario de actividades</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th style="text-align: center;">Reacciones</th>
                    <th style="text-align: center;">Compartidos</th>
                    <th style="text-align: center;">Inscripciones</th>
                    <th style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['actividad_reciente'] ?? [] as $fecha => $actividad)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ $actividad['reacciones'] ?? 0 }}</td>
                    <td style="text-align: center;">{{ $actividad['compartidos'] ?? 0 }}</td>
                    <td style="text-align: center;">{{ $actividad['inscripciones'] ?? 0 }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $actividad['total'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>TOTAL</td>
                    <td style="text-align: center;">{{ array_sum(array_column($datos['actividad_reciente'] ?? [], 'reacciones')) }}</td>
                    <td style="text-align: center;">{{ array_sum(array_column($datos['actividad_reciente'] ?? [], 'compartidos')) }}</td>
                    <td style="text-align: center;">{{ array_sum(array_column($datos['actividad_reciente'] ?? [], 'inscripciones')) }}</td>
                    <td style="text-align: center;">{{ array_sum(array_column($datos['actividad_reciente'] ?? [], 'total')) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- PÁGINA 6: TOP PARTICIPANTES Y DISTRIBUCIÓN POR ESTADOS -->
    <div class="page">
        <div class="header-page">
            <h2>Top 10 Participantes Más Activos</h2>
            <div class="meta">Ranking de participantes con mayor actividad</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th style="text-align: center;">Total Actividades</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($datos['top_participantes'] ?? [], 0, 10) as $index => $participante)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>{{ $participante['nombre'] ?? 'Participante' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $participante['total_actividades'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20mm;">
            <div class="header-page" style="margin: 0 -15mm 10mm -15mm;">
                <h2>Distribución por Estados de Inscripción</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th style="text-align: center;">Cantidad</th>
                        <th style="text-align: center;">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEstados = array_sum($datos['distribucion_estados'] ?? []);
                    @endphp
                    @foreach($datos['distribucion_estados'] ?? [] as $estado => $cantidad)
                    @php
                        $porcentaje = $totalEstados > 0 ? round(($cantidad / $totalEstados) * 100, 2) : 0;
                        $badgeClass = match($estado) {
                            'aprobada' => 'badge-success',
                            'rechazada' => 'badge-danger',
                            'pendiente' => 'badge-warning',
                            default => 'badge-info'
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($estado) }}</span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($cantidad, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold; color: #00A36C;">{{ $porcentaje }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td>TOTAL</td>
                        <td style="text-align: center;">{{ number_format($totalEstados, 0, ',', '.') }}</td>
                        <td style="text-align: center;">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Footer con numeración -->
    <div class="footer">
        <div style="text-align: center; margin-bottom: 3mm;">
            <strong>{{ isset($evento->ong) && $evento->ong ? ($evento->ong->nombre_ong ?? 'Sistema UNI2') : 'Sistema UNI2' }}</strong>
        </div>
        <div style="text-align: center; font-size: 8pt; color: #999;">
            © {{ date('Y') }} UNI2 - Todos los derechos reservados
        </div>
    </div>
    
    <div class="page-number">
        Página <span class="page"></span>
    </div>
</body>
</html>

