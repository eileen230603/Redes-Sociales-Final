import 'package:flutter/material.dart';

/// Modelo completo para Dashboard ONG General
/// Basado en el endpoint /api/ong/dashboard de Laravel

class OngDashboardData {
  final MetricasOng? metricas;
  final Map<String, int>? tendenciasMensuales;
  final Map<String, int>? distribucionEstados;
  final Map<String, int>? actividadSemanal;
  final List<ComparativaEvento>? comparativaEventos;
  final List<TopEvento>? topEventos;
  final List<TopVoluntario>? topVoluntarios;
  final Map<String, int>? distribucionParticipantes;
  final List<EventoListado>? listadoEventos;
  final Map<String, ActividadDiaria>? actividadReciente;
  final Map<String, Comparativa>? comparativas;
  final Map<String, double>? metricasRadar;
  final List<Alerta>? alertas;

  OngDashboardData({
    this.metricas,
    this.tendenciasMensuales,
    this.distribucionEstados,
    this.actividadSemanal,
    this.comparativaEventos,
    this.topEventos,
    this.topVoluntarios,
    this.distribucionParticipantes,
    this.listadoEventos,
    this.actividadReciente,
    this.comparativas,
    this.metricasRadar,
    this.alertas,
  });

  /// Constructor vacío para cuando no hay datos
  factory OngDashboardData.empty() {
    return OngDashboardData(
      metricas: null,
      tendenciasMensuales: null,
      distribucionEstados: null,
      actividadSemanal: null,
      comparativaEventos: null,
      topEventos: null,
      topVoluntarios: null,
      distribucionParticipantes: null,
      listadoEventos: null,
      actividadReciente: null,
      comparativas: null,
      metricasRadar: null,
      alertas: null,
    );
  }

  factory OngDashboardData.fromJson(Map<String, dynamic> json) {
    // Adaptador para estructura del backend:
    // Backend retorna: { ong: {...}, estadisticas: {...}, distribuciones: {...} }
    // Modelo espera: { metricas: {...}, tendencias_mensuales: {...}, etc. }

    // Si viene la estructura antigua (metricas), usar esa
    if (json['metricas'] != null) {
      return OngDashboardData(
        metricas: MetricasOng.fromJson(json['metricas']),
      tendenciasMensuales:
          json['tendencias_mensuales'] != null
              ? Map<String, int>.from(
                (json['tendencias_mensuales'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : null,
      distribucionEstados:
          json['distribucion_estados'] != null
              ? Map<String, int>.from(
                (json['distribucion_estados'] as Map).map(
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
                (json['actividad_semanal'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : null,
      comparativaEventos:
          json['comparativa_eventos'] != null
              ? (json['comparativa_eventos'] as List)
                  .map((e) => ComparativaEvento.fromJson(e))
                  .toList()
              : null,
      topEventos:
          json['top_eventos'] != null
              ? (json['top_eventos'] as List)
                  .map((e) => TopEvento.fromJson(e))
                  .toList()
              : null,
      topVoluntarios:
          json['top_voluntarios'] != null
              ? (json['top_voluntarios'] as List)
                  .map((e) => TopVoluntario.fromJson(e))
                  .toList()
              : null,
      distribucionParticipantes:
          json['distribucion_participantes'] != null
              ? Map<String, int>.from(
                (json['distribucion_participantes'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    v is int ? v : int.tryParse(v.toString()) ?? 0,
                  ),
                ),
              )
              : null,
      listadoEventos:
          json['listado_eventos'] != null
              ? (json['listado_eventos'] as List)
                  .map((e) => EventoListado.fromJson(e))
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
      alertas:
          json['alertas'] != null
              ? (json['alertas'] as List)
                  .map((e) => Alerta.fromJson(e))
                  .toList()
              : null,
      );
    }

    // Si viene la estructura nueva del backend (estadisticas, distribuciones)
    // Adaptar a la estructura esperada
    final estadisticas = json['estadisticas'];
    final distribuciones = json['distribuciones'];

    if (estadisticas != null) {
      return OngDashboardData(
        metricas: _buildMetricasFromEstadisticas(estadisticas),
        tendenciasMensuales: null,
        distribucionEstados: null,
        actividadSemanal: null,
        comparativaEventos: null,
        topEventos: null,
        topVoluntarios: null,
        distribucionParticipantes: distribuciones != null
            ? _convertToIntMap(distribuciones['participantes_por_estado'])
            : null,
        listadoEventos: null,
        actividadReciente: null,
        comparativas: null,
        metricasRadar: null,
        alertas: null,
      );
    }

    // Si no hay datos reconocibles, retornar vacío
    return OngDashboardData.empty();
  }

  /// Convertir estadisticas del backend a MetricasOng
  static MetricasOng? _buildMetricasFromEstadisticas(Map<String, dynamic> est) {
    try {
      final eventos = est['eventos'] as Map<String, dynamic>?;
      final voluntarios = est['voluntarios'] as Map<String, dynamic>?;
      final reacciones = est['reacciones'] as Map<String, dynamic>?;

      return MetricasOng(
        eventosActivos: eventos?['activos'] ?? 0,
        eventosInactivos: 0, // No viene del backend
        eventosFinalizados: eventos?['finalizados'] ?? 0,
        totalReacciones: reacciones?['total'] ?? 0,
        totalCompartidos: 0, // No viene del backend
        totalVoluntarios: voluntarios?['total_unicos'] ?? 0,
        totalParticipantes: voluntarios?['total_inscripciones'] ?? 0,
      );
    } catch (e) {
      return null;
    }
  }

  /// Convertir Map dinámico a Map<String, int>
  static Map<String, int>? _convertToIntMap(dynamic data) {
    if (data == null) return null;
    try {
      return Map<String, int>.from(
        (data as Map).map(
          (k, v) => MapEntry(
            k.toString(),
            v is int ? v : int.tryParse(v.toString()) ?? 0,
          ),
        ),
      );
    } catch (e) {
      return null;
    }
  }
}

class MetricasOng {
  final int eventosActivos;
  final int eventosInactivos;
  final int eventosFinalizados;
  final int totalReacciones;
  final int totalCompartidos;
  final int totalVoluntarios;
  final int totalParticipantes;

  MetricasOng({
    required this.eventosActivos,
    required this.eventosInactivos,
    required this.eventosFinalizados,
    required this.totalReacciones,
    required this.totalCompartidos,
    required this.totalVoluntarios,
    required this.totalParticipantes,
  });

  factory MetricasOng.fromJson(Map<String, dynamic> json) {
    return MetricasOng(
      eventosActivos: json['eventos_activos'] ?? 0,
      eventosInactivos: json['eventos_inactivos'] ?? 0,
      eventosFinalizados: json['eventos_finalizados'] ?? 0,
      totalReacciones: json['total_reacciones'] ?? 0,
      totalCompartidos: json['total_compartidos'] ?? 0,
      totalVoluntarios: json['total_voluntarios'] ?? 0,
      totalParticipantes: json['total_participantes'] ?? 0,
    );
  }
}

class ComparativaEvento {
  final int eventoId;
  final String titulo;
  final int reacciones;
  final int compartidos;
  final int participantes;

  ComparativaEvento({
    required this.eventoId,
    required this.titulo,
    required this.reacciones,
    required this.compartidos,
    required this.participantes,
  });

  factory ComparativaEvento.fromJson(Map<String, dynamic> json) {
    return ComparativaEvento(
      eventoId: json['evento_id'] ?? 0,
      titulo: json['titulo'] ?? '',
      reacciones: json['reacciones'] ?? 0,
      compartidos: json['compartidos'] ?? 0,
      participantes: json['participantes'] ?? 0,
    );
  }
}

class TopEvento {
  final int eventoId;
  final String titulo;
  final String? fechaInicio;
  final String? ubicacion;
  final String? estado;
  final int reacciones;
  final int compartidos;
  final int inscripciones;
  final int engagement;

  TopEvento({
    required this.eventoId,
    required this.titulo,
    this.fechaInicio,
    this.ubicacion,
    this.estado,
    required this.reacciones,
    required this.compartidos,
    required this.inscripciones,
    required this.engagement,
  });

  factory TopEvento.fromJson(Map<String, dynamic> json) {
    return TopEvento(
      eventoId: json['evento_id'] ?? 0,
      titulo: json['titulo'] ?? '',
      fechaInicio: json['fecha_inicio'],
      ubicacion: json['ubicacion'],
      estado: json['estado'],
      reacciones: json['reacciones'] ?? 0,
      compartidos: json['compartidos'] ?? 0,
      inscripciones: json['inscripciones'] ?? 0,
      engagement: json['engagement'] ?? 0,
    );
  }
}

class TopVoluntario {
  final int externoId;
  final String nombre;
  final String? email;
  final int eventosParticipados;
  final int? horasContribuidas;

  TopVoluntario({
    required this.externoId,
    required this.nombre,
    this.email,
    required this.eventosParticipados,
    this.horasContribuidas,
  });

  factory TopVoluntario.fromJson(Map<String, dynamic> json) {
    return TopVoluntario(
      externoId: json['externo_id'] ?? 0,
      nombre: json['nombre'] ?? 'Usuario',
      email: json['email'],
      eventosParticipados: json['eventos_participados'] ?? 0,
      horasContribuidas: json['horas_contribuidas'],
    );
  }
}

class EventoListado {
  final int id;
  final String titulo;
  final String? fechaInicio;
  final String? fechaFin;
  final String? ubicacion;
  final String? estado;
  final int totalParticipantes;
  final String tipo; // 'evento' o 'mega_evento'

  EventoListado({
    required this.id,
    required this.titulo,
    this.fechaInicio,
    this.fechaFin,
    this.ubicacion,
    this.estado,
    required this.totalParticipantes,
    required this.tipo,
  });

  factory EventoListado.fromJson(Map<String, dynamic> json) {
    return EventoListado(
      id: json['id'] ?? 0,
      titulo: json['titulo'] ?? '',
      fechaInicio: json['fecha_inicio'],
      fechaFin: json['fecha_fin'],
      ubicacion: json['ubicacion'],
      estado: json['estado'],
      totalParticipantes: json['total_participantes'] ?? 0,
      tipo: json['tipo'] ?? 'evento',
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

class Alerta {
  final String tipo;
  final String severidad; // 'warning', 'danger', 'info'
  final String mensaje;
  final int? eventoId;

  Alerta({
    required this.tipo,
    required this.severidad,
    required this.mensaje,
    this.eventoId,
  });

  factory Alerta.fromJson(Map<String, dynamic> json) {
    return Alerta(
      tipo: json['tipo'] ?? '',
      severidad: json['severidad'] ?? 'info',
      mensaje: json['mensaje'] ?? '',
      eventoId: json['evento_id'],
    );
  }

  Color get color {
    switch (severidad) {
      case 'danger':
        return Colors.red;
      case 'warning':
        return Colors.orange;
      case 'info':
        return Colors.blue;
      default:
        return Colors.grey;
    }
  }

  IconData get icon {
    switch (tipo) {
      case 'baja_participacion':
        return Icons.people_outline;
      case 'sin_voluntarios':
        return Icons.volunteer_activism;
      case 'pendiente_evaluacion':
        return Icons.assessment;
      default:
        return Icons.info_outline;
    }
  }
}
