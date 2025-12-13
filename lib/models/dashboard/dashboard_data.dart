class DashboardData {
  final EventoInfo? evento;
  final OngInfo? ong;
  final Map<String, dynamic>? filtros;
  final MetricasPrincipales? metricas;
  final Map<String, dynamic>? tendencias;
  final Map<String, int>? distribucionEstados;
  final Map<String, int>? actividadSemanal;
  final List<TopParticipante>? topParticipantes;
  final Map<String, ActividadDiaria>? actividadReciente;
  final Map<String, Comparativa>? comparativas;
  final Map<String, double>? metricasRadar;

  DashboardData({
    this.evento,
    this.ong,
    this.filtros,
    this.metricas,
    this.tendencias,
    this.distribucionEstados,
    this.actividadSemanal,
    this.topParticipantes,
    this.actividadReciente,
    this.comparativas,
    this.metricasRadar,
  });

  factory DashboardData.fromJson(Map<String, dynamic> json) {
    return DashboardData(
      evento:
          json['evento'] != null ? EventoInfo.fromJson(json['evento']) : null,
      ong: json['ong'] != null ? OngInfo.fromJson(json['ong']) : null,
      filtros: json['filtros'],
      metricas:
          json['metricas'] != null
              ? MetricasPrincipales.fromJson(json['metricas'])
              : null,
      tendencias: json['tendencias'],
      distribucionEstados:
          json['distribucion_estados'] != null
              ? Map<String, int>.from(
                json['distribucion_estados'].map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : null,
      actividadSemanal:
          json['actividad_semanal'] != null
              ? Map<String, int>.from(
                json['actividad_semanal'].map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : null,
      topParticipantes:
          json['top_participantes'] != null
              ? (json['top_participantes'] as List)
                  .map((e) => TopParticipante.fromJson(e))
                  .toList()
              : null,
      actividadReciente:
          json['actividad_reciente'] != null
              ? Map<String, ActividadDiaria>.from(
                (json['actividad_reciente'] as Map).map(
                  (k, v) => MapEntry(k.toString(), ActividadDiaria.fromJson(v)),
                ),
              )
              : null,
      comparativas:
          json['comparativas'] != null
              ? Map<String, Comparativa>.from(
                (json['comparativas'] as Map).map(
                  (k, v) => MapEntry(k.toString(), Comparativa.fromJson(v)),
                ),
              )
              : null,
      metricasRadar:
          json['metricas_radar'] != null
              ? Map<String, double>.from(
                (json['metricas_radar'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    (v is num)
                        ? v.toDouble()
                        : double.tryParse(v.toString()) ?? 0.0,
                  ),
                ),
              )
              : null,
    );
  }
}

class EventoInfo {
  final int id;
  final String titulo;
  final String? descripcion;
  final String? fechaInicio;
  final String? fechaFin;
  final String? ubicacion;
  final String? categoria;
  final String? estado;

  EventoInfo({
    required this.id,
    required this.titulo,
    this.descripcion,
    this.fechaInicio,
    this.fechaFin,
    this.ubicacion,
    this.categoria,
    this.estado,
  });

  factory EventoInfo.fromJson(Map<String, dynamic> json) {
    return EventoInfo(
      id: json['id'],
      titulo: json['titulo'] ?? '',
      descripcion: json['descripcion'],
      fechaInicio: json['fecha_inicio'],
      fechaFin: json['fecha_fin'],
      ubicacion: json['ubicacion'],
      categoria: json['categoria'],
      estado: json['estado'],
    );
  }
}

class OngInfo {
  final String nombre;
  final String? logoUrl;

  OngInfo({required this.nombre, this.logoUrl});

  factory OngInfo.fromJson(Map<String, dynamic> json) {
    return OngInfo(nombre: json['nombre'] ?? 'ONG', logoUrl: json['logo_url']);
  }
}

class MetricasPrincipales {
  final int reacciones;
  final int compartidos;
  final int voluntarios;
  final int participantesTotal;
  final Map<String, int> participantesPorEstado;

  MetricasPrincipales({
    required this.reacciones,
    required this.compartidos,
    required this.voluntarios,
    required this.participantesTotal,
    required this.participantesPorEstado,
  });

  factory MetricasPrincipales.fromJson(Map<String, dynamic> json) {
    return MetricasPrincipales(
      reacciones: json['reacciones'] ?? 0,
      compartidos: json['compartidos'] ?? 0,
      voluntarios: json['voluntarios'] ?? 0,
      participantesTotal: json['participantes_total'] ?? 0,
      participantesPorEstado:
          json['participantes_por_estado'] != null
              ? Map<String, int>.from(
                (json['participantes_por_estado'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : {},
    );
  }
}

class TopParticipante {
  final String nombre;
  final int totalActividades;
  final String? email;
  final int? eventosParticipados;

  TopParticipante({
    required this.nombre,
    required this.totalActividades,
    this.email,
    this.eventosParticipados,
  });

  factory TopParticipante.fromJson(Map<String, dynamic> json) {
    return TopParticipante(
      nombre: json['nombre'] ?? 'Usuario',
      totalActividades: json['total_actividades'] ?? 0,
      email: json['email'],
      eventosParticipados: json['eventos_participados'],
    );
  }
}

class ActividadDiaria {
  final int reacciones;
  final int compartidos;
  final int inscripciones;
  final int total;

  ActividadDiaria({
    required this.reacciones,
    required this.compartidos,
    required this.inscripciones,
    required this.total,
  });

  factory ActividadDiaria.fromJson(Map<String, dynamic> json) {
    return ActividadDiaria(
      reacciones: json['reacciones'] ?? 0,
      compartidos: json['compartidos'] ?? 0,
      inscripciones: json['inscripciones'] ?? 0,
      total: json['total'] ?? 0,
    );
  }
}

class Comparativa {
  final int actual;
  final int anterior;
  final double crecimiento;
  final String tendencia; // 'up', 'down', 'stable'

  Comparativa({
    required this.actual,
    required this.anterior,
    required this.crecimiento,
    required this.tendencia,
  });

  factory Comparativa.fromJson(Map<String, dynamic> json) {
    return Comparativa(
      actual: json['actual'] ?? 0,
      anterior: json['anterior'] ?? 0,
      crecimiento:
          (json['crecimiento'] is num)
              ? json['crecimiento'].toDouble()
              : double.tryParse(json['crecimiento']?.toString() ?? '0') ?? 0.0,
      tendencia: json['tendencia'] ?? 'stable',
    );
  }
}
