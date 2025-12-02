<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Participación y Patrocinio</title>
    <style>
        @page {
            margin: 18mm 22mm 18mm 22mm;
        }
        body {
            font-family: 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #1E293B;
            line-height: 1.7;
            position: relative;
            background: #FFFFFF;
        }
        
        /* MARCA DE AGUA MEJORADA */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.03;
            z-index: 0;
            width: 300px;
            height: auto;
            pointer-events: none;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
        
        /* PORTADA MEJORADA - DISEÑO MODERNO Y ELEGANTE */
        .portada {
            text-align: center;
            page-break-after: always;
            background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 50%, #F0F9FF 100%);
            margin: 0;
            padding: 30mm 25mm;
            color: #1E293B;
            position: relative;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            min-height: auto;
        }
        
        .portada:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8mm;
            background: linear-gradient(90deg, #0C2B44 0%, #00A36C 50%, #FF8C42 100%);
            border-radius: 20px 20px 0 0;
            z-index: 1;
        }
        
        .portada .logo-container {
            margin-bottom: 15mm;
            position: relative;
            z-index: 2;
        }
        
        .portada .logo-box {
            display: inline-block;
            background: white;
            padding: 10mm 18mm;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(12, 43, 68, 0.15);
            border: 3px solid #E0F2FE;
            margin-top: 5mm;
        }
        
        .portada h1 {
            font-size: 32pt;
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 15mm 0 10mm 0;
            font-weight: 800;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 2;
            line-height: 1.1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .portada h2 {
            font-size: 18pt;
            color: #00A36C;
            margin: 8mm 0;
            font-weight: 700;
            position: relative;
            z-index: 2;
            letter-spacing: 0.5px;
        }
        
        .portada-info {
            background: white;
            padding: 8mm 15mm;
            border-radius: 15px;
            margin-top: 15mm;
            box-shadow: 0 8px 25px rgba(12, 43, 68, 0.12);
            border: 2px solid #E0F2FE;
            text-align: left;
            max-width: 150mm;
            margin-left: auto;
            margin-right: auto;
        }
        
        .portada-info-row {
            display: flex;
            justify-content: space-between;
            padding: 3.5mm 0;
            border-bottom: 1px solid #E2E8F0;
            align-items: center;
        }
        
        .portada-info-row:last-child {
            border-bottom: none;
        }
        
        .portada-info-label {
            font-weight: 700;
            color: #64748B;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .portada-info-value {
            color: #0C2B44;
            font-weight: 600;
            font-size: 9.5pt;
        }
        
        .portada .periodo {
            font-size: 10pt;
            color: #0C2B44;
            margin-top: 10mm;
            text-align: center;
            padding: 5mm 12mm;
            background: linear-gradient(135deg, #E0F2FE 0%, #F0F9FF 100%);
            border-radius: 12px;
            display: inline-block;
            border: 2px solid #BAE6FD;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(12, 43, 68, 0.08);
        }
        
        .logo {
            max-width: 150px;
            height: auto;
            filter: none;
        }
        
        /* RESUMEN EJECUTIVO MEJORADO - DISEÑO MODERNO - UNA SOLA HOJA */
        .resumen-ejecutivo {
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
            padding: 10mm;
            border-radius: 18px;
            margin: 6mm 0;
            page-break-inside: avoid;
            box-shadow: 0 10px 35px rgba(12, 43, 68, 0.1);
            border: 3px solid #E0F2FE;
            min-height: auto;
        }
        
        .resumen-ejecutivo h3 {
            color: #0C2B44;
            font-size: 16pt;
            margin-bottom: 8mm;
            padding-bottom: 4mm;
            font-weight: 800;
            position: relative;
            border-bottom: none;
            letter-spacing: -0.3px;
        }
        
        .resumen-ejecutivo h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #00A36C 0%, #0C2B44 100%);
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);
        }
        
        /* MÉTRICAS MEJORADAS - DISEÑO COMPACTO PARA UNA HOJA */
        .metricas {
            display: table;
            width: 100%;
            margin-top: 5mm;
            border-spacing: 2.5mm 0;
            table-layout: fixed;
        }
        
        .metrica {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 7mm 3mm;
            vertical-align: top;
            background: linear-gradient(135deg, #FFFFFF 0%, #F0F9FF 100%);
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(12, 43, 68, 0.12);
            border: 2px solid #E0F2FE;
            position: relative;
            overflow: hidden;
        }
        
        .metrica:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0C2B44 0%, #00A36C 100%);
        }
        
        .metrica-valor {
            font-size: 38pt;
            font-weight: 900;
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2mm;
            line-height: 1;
        }
        
        .metrica-label {
            font-size: 8pt;
            color: #475569;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }
        
        /* SECCIONES MEJORADAS - DISEÑO MODERNO */
        .seccion {
            margin: 15mm 0;
            page-break-inside: avoid;
        }
        
        .seccion h3 {
            color: #0C2B44;
            font-size: 18pt;
            margin-bottom: 10mm;
            padding-bottom: 5mm;
            font-weight: 800;
            position: relative;
            border-bottom: none;
            letter-spacing: -0.2px;
        }
        
        .seccion h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, #00A36C 0%, #0C2B44 100%);
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);
        }
        
        /* TABLAS MEJORADAS - DISEÑO MODERNO */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 8mm 0;
            font-size: 9pt;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(12, 43, 68, 0.12);
            background: white;
            border: 2px solid #E0F2FE;
        }
        
        table thead {
            background: linear-gradient(135deg, #0C2B44 0%, #1E40AF 100%);
            color: #FFFFFF;
        }
        
        table th {
            padding: 10mm 6mm;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.8px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        table th:last-child {
            border-right: none;
        }
        
        table td {
            padding: 8mm 6mm;
            border-bottom: 1px solid #E2E8F0;
            border-right: 1px solid #F1F5F9;
        }
        
        table td:last-child {
            border-right: none;
        }
        
        table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        table tbody tr:nth-child(even) {
            background: #F8FAFC;
        }
        
        table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* BADGES MEJORADOS - DISEÑO MODERNO */
        .badge {
            display: inline-block;
            padding: 3mm 8mm;
            border-radius: 20px;
            font-size: 8.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }
        
        .badge-success {
            background: linear-gradient(135deg, #00A36C 0%, #10B981 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);
        }
        
        .badge-primary {
            background: linear-gradient(135deg, #0C2B44 0%, #1E40AF 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(12, 43, 68, 0.3);
        }
        
        .badge-secondary {
            background: linear-gradient(135deg, #64748B 0%, #94A3B8 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }
        
        /* EVENTO DETALLE MEJORADO - DISEÑO MODERNO */
        .evento-detalle {
            margin: 12mm 0;
            padding: 12mm;
            border: 3px solid #E0F2FE;
            border-radius: 16px;
            page-break-inside: avoid;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
            box-shadow: 0 8px 25px rgba(12, 43, 68, 0.1);
            position: relative;
        }
        
        .evento-detalle:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0C2B44 0%, #00A36C 100%);
            border-radius: 16px 16px 0 0;
        }
        
        .evento-detalle h4 {
            color: #0C2B44;
            font-size: 14pt;
            margin-bottom: 8mm;
            font-weight: 800;
            border-bottom: 3px solid #E0F2FE;
            padding-bottom: 4mm;
            letter-spacing: -0.2px;
        }
        
        .evento-info {
            margin: 3mm 0;
            font-size: 8.5pt;
            line-height: 1.7;
        }
        
        .evento-info p {
            margin: 3mm 0;
        }
        
        .evento-info strong {
            color: #2C3E50;
            font-weight: 600;
        }
        
        .evento-imagen {
            max-width: 100%;
            max-height: 80mm;
            margin: 5mm 0;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* IMPACTO SECTION - DISEÑO COMPACTO */
        .impacto-box {
            background: linear-gradient(135deg, #F0F9FF 0%, #FFFFFF 100%);
            padding: 8mm 10mm;
            border-radius: 14px;
            margin-top: 6mm;
            border: 3px solid #BAE6FD;
            box-shadow: 0 6px 20px rgba(12, 43, 68, 0.1);
        }
        
        .impacto-box p {
            font-size: 10pt;
            font-weight: 700;
            color: #0C2B44;
            margin-bottom: 4mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid #BAE6FD;
        }
        
        .impacto-box ul {
            list-style: none;
            padding: 0;
            margin: 3mm 0 0 0;
        }
        
        .impacto-box li {
            padding: 2mm 0;
            border-bottom: 1px solid #E2E8F0;
            font-size: 9pt;
            line-height: 1.6;
        }
        
        .impacto-box li:last-child {
            border-bottom: none;
        }
        
        .impacto-box strong {
            color: #0C2B44;
            font-weight: 700;
        }
        
        /* FOOTER MEJORADO - ESTILO MAILPRO */
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 8pt;
            color: #94A3B8;
            padding: 4mm;
            background: transparent;
            border-top: 1px solid #E2E8F0;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-left {
            text-align: left;
            font-size: 7pt;
        }
        
        .footer-center {
            text-align: center;
            font-weight: 500;
        }
        
        .footer-right {
            text-align: right;
            font-size: 7pt;
        }
        
        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }
        
        /* AGRADECIMIENTOS MEJORADO - DISEÑO MODERNO */
        .agradecimientos {
            text-align: center;
            padding: 30mm 25mm;
            background: linear-gradient(135deg, #F8FAFC 0%, #F0F9FF 100%);
            border-radius: 20px;
            margin: 10mm 0;
            box-shadow: 0 10px 35px rgba(12, 43, 68, 0.12);
            border: 3px solid #E0F2FE;
            position: relative;
        }
        
        .agradecimientos:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0C2B44 0%, #00A36C 50%, #FF8C42 100%);
            border-radius: 20px 20px 0 0;
        }
        
        .agradecimientos h3 {
            border: none;
            margin-bottom: 18mm;
            color: #0C2B44;
            font-size: 22pt;
            font-weight: 800;
            letter-spacing: -0.3px;
        }
        
        .agradecimientos h3:after {
            display: none;
        }
        
        .agradecimientos p {
            font-size: 11pt;
            line-height: 2;
            color: #475569;
            max-width: 85%;
            margin: 10mm auto;
            font-weight: 500;
        }
        
        /* DECORATIVE ELEMENTS - DISEÑO MODERNO */
        .decorative-line {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #BAE6FD 20%, #00A36C 50%, #BAE6FD 80%, transparent 100%);
            margin: 12mm 0;
            opacity: 0.6;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
        }
    </style>
</head>
<body>
    <!-- MARCA DE AGUA -->
    @if(isset($logo_path) && $logo_path && file_exists($logo_path))
    <div class="watermark">
        <img src="{{ $logo_path }}" alt="Logo Marca de Agua">
    </div>
    @endif
    
    <div class="content-wrapper">
    <!-- PORTADA -->
    <div class="portada">
        <div class="logo-container">
            <div class="logo-box">
                @if(isset($logo_path) && $logo_path && file_exists($logo_path))
                <img src="{{ $logo_path }}" alt="Logo" class="logo">
                @else
                <h1 style="color: #0C2B44; font-size: 36pt; margin: 0;">UNI2</h1>
                <h2 style="color: #00A36C; font-size: 22pt; margin: 5mm 0;">Sistema de Gestión</h2>
                @endif
            </div>
        </div>
        
        <h1>Reporte de Participación<br>y Patrocinio</h1>
        <h2>{{ $portada['nombre_usuario'] }}</h2>
        
        <div class="portada-info">
            <div class="portada-info-row">
                <span class="portada-info-label">Fecha de generación:</span>
                <span class="portada-info-value">{{ $portada['fecha_generacion'] }}</span>
            </div>
            <div class="portada-info-row">
                <span class="portada-info-label">Hora de generación:</span>
                <span class="portada-info-value">{{ $portada['hora_generacion'] }}</span>
            </div>
            <div class="portada-info-row">
                <span class="portada-info-label">Sistema:</span>
                <span class="portada-info-value">Gestión de Eventos Sociales UNI2</span>
            </div>
        </div>
        
        <div class="periodo">
            <strong>Periodo del reporte:</strong> {{ $portada['periodo'] }}
        </div>
    </div>

    <!-- RESUMEN EJECUTIVO -->
    <div class="resumen-ejecutivo seccion">
        <h3>Resumen Ejecutivo</h3>
        <div class="metricas">
            <div class="metrica">
                <div class="metrica-valor">{{ $estadisticas['total_eventos_inscritos'] }}</div>
                <div class="metrica-label">Eventos<br>Inscritos</div>
            </div>
            <div class="metrica">
                <div class="metrica-valor">{{ $estadisticas['total_eventos_asistidos'] }}</div>
                <div class="metrica-label">Eventos<br>Asistidos</div>
            </div>
            <div class="metrica">
                <div class="metrica-valor">{{ $estadisticas['total_mega_eventos'] }}</div>
                <div class="metrica-label">Mega<br>Eventos</div>
            </div>
            <div class="metrica">
                <div class="metrica-valor">{{ $estadisticas['total_reacciones'] }}</div>
                <div class="metrica-label">Reacciones</div>
            </div>
        </div>
        
        <div class="impacto-box">
            <p>📊 Impacto Generado</p>
            <ul>
                <li><strong>Ciudades impactadas:</strong> {{ $estadisticas['ciudades_impactadas'] }}</li>
                <li><strong>Ciudades:</strong> {{ implode(', ', $estadisticas['ciudades']) ?: 'No especificadas' }}</li>
                <li><strong>Total de compartidos:</strong> {{ $estadisticas['total_compartidos'] }}</li>
            </ul>
        </div>
    </div>

    <div class="decorative-line"></div>

    <!-- TABLA RESUMEN DE EVENTOS INSCRITOS -->
    <div class="seccion">
        <h3>Tabla Resumen de Eventos Inscritos</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Evento</th>
                    <th>Fecha y Lugar</th>
                    <th>Tipo de Evento</th>
                    <th>ONG Organizadora</th>
                    <th>Estado</th>
                    <th>Confirmación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos_inscritos as $evento)
                <tr>
                    <td><strong>{{ $evento['titulo'] }}</strong></td>
                    <td>
                        {{ $evento['fecha_inicio'] ? \Carbon\Carbon::parse($evento['fecha_inicio'])->format('d/m/Y H:i') : 'No especificada' }}
                        <br>
                        <small style="color: #78909C;">{{ $evento['ciudad'] ?? $evento['ubicacion'] ?? 'No especificada' }}</small>
                    </td>
                    <td>{{ $evento['tipo_evento'] ?? 'No especificado' }}</td>
                    <td>{{ $evento['organizador'] }}</td>
                    <td>
                        <span class="badge badge-{{ $evento['estado'] === 'asistido' ? 'success' : 'primary' }}">
                            {{ ucfirst($evento['estado']) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $evento['asistio'] ? 'success' : 'secondary' }}">
                            {{ $evento['asistio'] ? 'Sí' : 'No' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #78909C; padding: 15mm;">No hay eventos inscritos</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- EVENTOS ASISTIDOS -->
    @if($eventos_asistidos->count() > 0)
    <div class="seccion page-break">
        <h3>Eventos Asistidos</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Evento</th>
                    <th>Fecha</th>
                    <th>Lugar</th>
                    <th>ONG Organizadora</th>
                    <th>Puntos Obtenidos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos_asistidos as $evento)
                <tr>
                    <td><strong>{{ $evento['titulo'] }}</strong></td>
                    <td>{{ $evento['fecha_inicio'] ? \Carbon\Carbon::parse($evento['fecha_inicio'])->format('d/m/Y') : 'No especificada' }}</td>
                    <td>{{ $evento['ciudad'] ?? 'No especificada' }}</td>
                    <td>{{ $evento['organizador'] }}</td>
                    <td><strong style="color: #FF8C42; font-size: 10pt;">{{ $evento['puntos'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- DETALLE POR EVENTO -->
    @if($eventos_inscritos->count() > 0)
    <div class="seccion page-break">
        <h3>Detalle por Evento</h3>
        @foreach($eventos_inscritos->take(5) as $evento)
        <div class="evento-detalle">
            <h4>{{ $evento['titulo'] }}</h4>
            <div class="evento-info">
                <p><strong>Tipo de Evento:</strong> {{ $evento['tipo_evento'] ?? 'No especificado' }}</p>
                <p><strong>Fecha de Inicio:</strong> {{ $evento['fecha_inicio'] ? \Carbon\Carbon::parse($evento['fecha_inicio'])->format('d/m/Y H:i') : 'No especificada' }}</p>
                <p><strong>Fecha de Fin:</strong> {{ $evento['fecha_fin'] ? \Carbon\Carbon::parse($evento['fecha_fin'])->format('d/m/Y H:i') : 'No especificada' }}</p>
                <p><strong>Ubicación:</strong> {{ $evento['ciudad'] ?? $evento['ubicacion'] ?? 'No especificada' }}</p>
                <p><strong>ONG Organizadora:</strong> {{ $evento['organizador'] }}</p>
                <p><strong>Fecha de Inscripción:</strong> {{ \Carbon\Carbon::parse($evento['fecha_inscripcion'])->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> 
                    <span class="badge badge-{{ $evento['estado'] === 'asistido' ? 'success' : 'primary' }}">
                        {{ ucfirst($evento['estado']) }}
                    </span>
                </p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- AGRADECIMIENTOS -->
    <div class="seccion page-break">
        <div class="agradecimientos">
            <h3>Agradecimientos Institucionales</h3>
            <div class="decorative-line"></div>
            <p>
                Agradecemos su participación activa en los eventos sociales y su compromiso con las causas comunitarias.
                Su contribución ha sido fundamental para el éxito de estas iniciativas.
            </p>
            <p>
                Este reporte refleja su impacto positivo en la comunidad y su dedicación al voluntariado.
                Juntos estamos construyendo un futuro mejor.
            </p>
            <div class="decorative-line"></div>
        </div>
    </div>

    </div> <!-- content-wrapper -->
    
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">UNI2 © 2025</div>
            <div class="footer-center">Sistema de Gestión de Eventos Sociales</div>
            <div class="footer-right">Generado: {{ $portada['fecha_generacion'] }} {{ $portada['hora_generacion'] }}</div>
        </div>
    </div>
</body>
</html>