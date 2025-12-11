<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Dashboard - {{ $ong->nombre_ong ?? 'ONG' }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333333;
            margin: 40px;
        }
        
        /* Layout Tables */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }
        .layout-table td {
            vertical-align: top;
        }
        
        /* Header */
        .header-section {
            margin-bottom: 30px;
        }
        .logo-img {
            max-height: 60px;
            max-width: 200px;
        }
        .placeholder-logo {
            font-size: 24pt;
            color: #ccc;
            margin: 0;
            font-weight: bold;
        }
        .company-details {
            text-align: right;
            font-size: 9pt;
            line-height: 1.4;
        }
        .company-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
            display: block;
        }

        /* Invoice Info */
        .info-section {
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 30px;
            width: 100%;
            overflow: hidden;
        }

        .invoice-title {
            font-size: 18pt;
            color: #555555;
            margin-bottom: 15px;
        }

        .dates-table {
            width: 100%;
            border-collapse: collapse;
        }
        .date-cell {
            padding-right: 20px;
        }
        .date-label {
            font-weight: bold;
            font-size: 10pt;
            color: #333333;
        }
        .date-value {
            font-size: 10pt;
            color: #333333;
        }

        .bill-to-label {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .bill-to-text {
            font-size: 10pt;
            line-height: 1.4;
        }

        /* Data Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #000000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            color: #333333;
        }
        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #dddddd;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .col-desc { width: 40%; text-align: left; }
        .col-qty { width: 15%; text-align: right; }
        .col-price { width: 20%; text-align: right; }
        .col-taxes { width: 10%; text-align: right; }
        .col-amount { width: 15%; text-align: right; }

        .item-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            color: #333333;
        }
        .item-desc {
            font-size: 9pt;
            color: #666666;
            display: block;
        }
        .empty-row td {
            padding: 20px 0;
        }
        .no-data {
            color: #999999;
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        /* Totals */
        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .totals-table tr.last td {
            border-bottom: none;
            border-top: 1px solid #dddddd;
            font-weight: bold;
            font-size: 12pt;
            padding-top: 10px;
        }
        .total-label {
            text-align: left;
            font-weight: bold;
            color: #333333;
        }
        .total-value {
            text-align: right;
            color: #333333;
        }

        /* Footer Note & Terms */
        .footer-note {
            margin-top: 40px;
            border-top: 2px solid #000000;
            padding-top: 10px;
            font-size: 9pt;
            color: #333333;
        }
        .terms {
            margin-top: 10px;
            font-size: 9pt;
            color: #333333;
        }

        /* Bottom Fixed Footer */
        .bottom-footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            border-top: 2px solid #000000;
            padding-top: 10px;
            font-size: 8pt;
            color: #333333;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
        }
        .page-num {
            background: #000000;
            color: #ffffff;
            padding: 2px 8px;
            font-weight: bold;
            display: inline-block;
            border-radius: 0;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="layout-table header-section">
        <tr>
            <td width="50%">
                @if(isset($logo_ong) && $logo_ong)
                    <img src="{{ $logo_ong }}" alt="Logo" class="logo-img">
                @else
                    <h1 class="placeholder-logo">Su Logo Aquí</h1>
                @endif
            </td>
            <td width="50%" align="right">
                <div class="company-details">
                    <span class="company-name">{{ $ong->nombre_ong ?? 'Nombre de la Organización' }}</span>
                    <div>NIT: {{ $ong->NIT ?? 'N-A' }}</div>
                    <div>{{ $ong->telefono ?? 'Teléfono no disp.' }}</div>
                    <div>{{ $ong->email ?? ($ong->usuario->email ?? '') }}</div>
                    <div>{{ $ong->sitio_web ?? 'www.ejemplo.com' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Section -->
    <div class="info-section">
        <table class="layout-table">
            <tr>
                <td width="60%">
                    <div class="invoice-title">
                        Reporte Dashboard INV/{{ now()->format('Y/m') }}/{{ str_pad($ong->user_id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                    
                    <table class="dates-table">
                        <tr>
                            <td class="date-cell">
                                <span class="date-label">Fecha Reporte:</span>
                                <span class="date-value">{{ now()->format('d/m/Y') }}</span>
                            </td>
                            <td class="date-cell">
                                <span class="date-label">Período:</span>
                                <span class="date-value">{{ $fecha_inicio->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="40%" align="right">
                    <span class="bill-to-label">Reporte Para:</span>
                    <div class="bill-to-text">
                        Administración / Junta Directiva<br>
                        {{ $ong->direccion ?? 'Dirección Principal' }}<br>
                        {{ $ong->nombre_ong }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-desc">EVENTO/DESCRIPCIÓN</th>
                <th class="col-qty">REACCIONES</th>
                <th class="col-price">COMPARTIDOS</th>
                <th class="col-taxes">ESTADO</th>
                <th class="col-amount">PARTICIPANTES</th>
            </tr>
        </thead>
        <tbody>
            @php
                $topEventos = array_slice($datos['top_eventos'] ?? [], 0, 5);
                $rowCount = count($topEventos);
                $minRows = 5;
            @endphp

            @if($rowCount > 0)
                @foreach($topEventos as $evento)
                <tr>
                    <td class="col-desc">
                        <span class="item-title">{{ $evento['titulo'] ?? 'Evento' }}</span>
                        <span class="item-desc">{{ \Illuminate\Support\Str::limit($evento['descripcion'] ?? 'Sin descripción disponible', 60) }}</span>
                    </td>
                    <td class="col-qty">{{ number_format($evento['reacciones_count'] ?? $evento['reacciones'] ?? 0, 0, ',', '.') }}</td>
                    <td class="col-price">{{ number_format($evento['compartidos_count'] ?? $evento['compartidos'] ?? 0, 0, ',', '.') }}</td>
                    <td class="col-taxes">{{ ucfirst($evento['estado'] ?? 'Activo') }}</td>
                    <td class="col-amount">{{ number_format($evento['participantes_count'] ?? $evento['participantes'] ?? $evento['inscripciones'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="no-data">No hay datos disponibles para mostrar en este período.</td>
                </tr>
                @php $rowCount = 1; @endphp
            @endif
            
            @for($i = $rowCount; $i < $minRows; $i++)
                <tr class="empty-row">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td class="total-label">Subtotal (Voluntarios)</td>
            <td class="total-value">{{ number_format($datos['metricas']['total_voluntarios'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="total-label">Total Eventos Activos</td>
            <td class="total-value">{{ number_format($datos['metricas']['eventos_activos'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="last">
            <td class="total-label">Total Impacto (Personas)</td>
            <td class="total-value">{{ number_format($datos['metricas']['total_participantes'] ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Footer Notes -->
    <div class="footer-note">
        Please use the following communication for your internal records: <strong>INV/{{ now()->format('Y/m') }}/{{ str_pad($ong->user_id, 4, '0', STR_PAD_LEFT) }}</strong>
    </div>
    <div class="terms">
        <strong>Notes:</strong> Este reporte fue generado automáticamente por el sistema UNI2. La información presentada refleja los datos en tiempo real al momento de la generación del reporte.
    </div>

    <!-- Bottom Fixed Footer -->
    <div class="bottom-footer">
        <table class="footer-table">
            <tr>
                <td width="40%">
                    <div style="font-weight: bold;">+57 {{ $ong->telefono ?? '300 000 0000' }}</div>
                    <div>{{ $ong->email ?? 'info@uni2.com' }}</div>
                    <div>{{ $ong->sitio_web ?? 'www.uni2.com.co' }}</div>
                </td>
                <td width="50%">
                    <div style="font-weight: bold;">{{ $ong->nombre_ong }} (Colombia)</div>
                    <div>{{ $ong->direccion ?? 'Calle Principal #123' }}</div>
                    <div>{{ $ong->ciudad ?? 'Bogotá' }} - Colombia</div>
                </td>
                <td width="10%" align="right">
                    <span class="page-num">1</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
