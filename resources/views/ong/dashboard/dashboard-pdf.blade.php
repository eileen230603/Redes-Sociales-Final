<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard ONG - {{ $ong->nombre_ong }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 2cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: -1;
            width: 400px;
        }
        
        .header {
            background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%);
            color: white;
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24pt;
            margin-bottom: 10px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metric-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 2px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .metric-card .value {
            font-size: 32pt;
            font-weight: bold;
            color: #00A36C;
            margin-bottom: 5px;
        }
        
        .metric-card .label {
            font-size: 10pt;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .section-title {
            background: #0C2B44;
            color: white;
            padding: 15px;
            margin: 20px 0 10px 0;
            font-size: 14pt;
            font-weight: bold;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: white;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
            border: 1px solid #333;
        }
        
        table td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            font-size: 9pt;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .chart-container {
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        
        .chart-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #6c757d;
            padding: 10px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('assets/img/uni2.png');
        $logoExists = file_exists($logoPath);
    @endphp
    
    @if($logoExists)
        <img src="{{ $logoPath }}" class="watermark" alt="Marca de agua">
    @endif
    
    <div class="header">
        <h1>Dashboard Estadístico</h1>
        <h2>{{ $ong->nombre_ong }}</h2>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>
    
    <div class="section-title">📊 Resumen General</div>
    
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="value">{{ $estadisticas['totalEventos'] }}</div>
            <div class="label">Total Eventos</div>
        </div>
        <div class="metric-card">
            <div class="value">{{ $estadisticas['totalReacciones'] }}</div>
            <div class="label">Reacciones</div>
        </div>
        <div class="metric-card">
            <div class="value">{{ $estadisticas['totalCompartidos'] }}</div>
            <div class="label">Compartidos</div>
        </div>
        <div class="metric-card">
            <div class="value">{{ $estadisticas['totalParticipantes'] }}</div>
            <div class="label">Participantes</div>
        </div>
    </div>
    
    <div class="section-title">📈 Tendencias Mensuales</div>
    
    <div class="chart-container">
        <img src="{{ $graficos['chartReacciones'] }}" alt="Gráfico Reacciones">
    </div>
    
    <div class="chart-container">
        <img src="{{ $graficos['chartCompartidos'] }}" alt="Gráfico Compartidos">
    </div>
    
    <div class="chart-container">
        <img src="{{ $graficos['chartInscripciones'] }}" alt="Gráfico Inscripciones">
    </div>
    
    <div class="section-title">🏆 Top 10 Eventos</div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Evento</th>
                <th>Reacciones</th>
                <th>Compartidos</th>
                <th>Participantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topEventos as $index => $evento)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $evento->titulo }}</td>
                <td>{{ $evento->reacciones_count ?? 0 }}</td>
                <td>{{ $evento->compartidos_count ?? 0 }}</td>
                <td>{{ $evento->participantes_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($topVoluntarios->count() > 0)
    <div class="section-title">👥 Top 10 Voluntarios</div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Participaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topVoluntarios as $index => $voluntario)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $voluntario->nombre_usuario ?? 'N/A' }}</td>
                <td>{{ $voluntario->correo_electronico ?? 'N/A' }}</td>
                <td>{{ $voluntario->participaciones_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="footer">
        {{ $ong->nombre_ong }} - Dashboard Estadístico - Página 1
    </div>
</body>
</html>

