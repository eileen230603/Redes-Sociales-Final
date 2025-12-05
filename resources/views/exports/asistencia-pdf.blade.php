<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Control de Asistencia - {{ $evento->titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0C2B44;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #0C2B44;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .resumen {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .resumen-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .resumen-item.total {
            background-color: #f8f9fa;
        }
        .resumen-item.asistieron {
            background-color: #d4edda;
            color: #155724;
        }
        .resumen-item.no-asistieron {
            background-color: #f8d7da;
            color: #721c24;
        }
        .resumen-item h3 {
            margin: 0;
            font-size: 28px;
        }
        .resumen-item p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #0C2B44;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Control de Asistencia</h1>
        <p><strong>Evento:</strong> {{ $evento->titulo }}</p>
        <p><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="resumen">
        <div class="resumen-item total">
            <h3>{{ $total }}</h3>
            <p>Total Participantes</p>
        </div>
        <div class="resumen-item asistieron">
            <h3>{{ $asistieron }}</h3>
            <p>Asistieron</p>
        </div>
        <div class="resumen-item no-asistieron">
            <h3>{{ $no_asistieron }}</h3>
            <p>No Asistieron</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Participante</th>
                <th>Fecha de Inscripción</th>
                <th>Estado de Asistencia</th>
                <th>Validado por</th>
                <th>Fecha de Validación</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participantes as $participante)
            <tr>
                <td>{{ $participante['participante'] }}</td>
                <td>{{ $participante['fecha_inscripcion'] }}</td>
                <td>{{ $participante['estado_asistencia'] }}</td>
                <td>{{ $participante['validado_por'] ?? '—' }}</td>
                <td>{{ $participante['fecha_registro_asistencia'] ?? '—' }}</td>
                <td>{{ $participante['observaciones'] ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No hay participantes inscritos en este evento.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y a las H:i') }}</p>
    </div>
</body>
</html>
