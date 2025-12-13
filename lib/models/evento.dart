import 'dart:convert';

class Evento {
  final int id;
  final int? ongId;
  final String titulo;
  final String? descripcion;
  final String tipoEvento;
  final DateTime fechaInicio;
  final DateTime? fechaFin;
  final DateTime? fechaLimiteInscripcion;
  final int? capacidadMaxima;
  final String estado;
  final String? ciudad;
  final String? direccion;
  final double? lat;
  final double? lng;
  final bool inscripcionAbierta;
  final List<dynamic>? patrocinadores;
  final List<dynamic>? invitados;
  final List<dynamic>? imagenes;
  final List<dynamic>? auspiciadores;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Evento({
    required this.id,
    this.ongId,
    required this.titulo,
    this.descripcion,
    required this.tipoEvento,
    required this.fechaInicio,
    this.fechaFin,
    this.fechaLimiteInscripcion,
    this.capacidadMaxima,
    required this.estado,
    this.ciudad,
    this.direccion,
    this.lat,
    this.lng,
    required this.inscripcionAbierta,
    this.patrocinadores,
    this.invitados,
    this.imagenes,
    this.auspiciadores,
    this.createdAt,
    this.updatedAt,
  });

  // Helper para parsear arrays que pueden venir como JSON string o array
  static List<dynamic>? _parseJsonArray(dynamic value) {
    if (value == null) return null;
    if (value is List) {
      // Filtrar valores nulos y arrays vacíos
      final filtered =
          value
              .where(
                (item) =>
                    item != null &&
                    item.toString().trim().isNotEmpty &&
                    item.toString() != '[]' &&
                    item.toString() != 'null',
              )
              .toList();
      return filtered.isEmpty ? null : filtered;
    }
    if (value is String) {
      // Si es string vacío o representa array vacío, retornar null
      final trimmed = value.trim();
      if (trimmed.isEmpty || trimmed == '[]' || trimmed == 'null') return null;

      try {
        final decoded = jsonDecode(value);
        if (decoded is List) {
          final filtered =
              decoded
                  .where(
                    (item) =>
                        item != null &&
                        item.toString().trim().isNotEmpty &&
                        item.toString() != '[]' &&
                        item.toString() != 'null',
                  )
                  .toList();
          return filtered.isEmpty ? null : filtered;
        }
        // Si no es array, tratar como array con un solo string válido
        return [value];
      } catch (e) {
        // Si no es JSON válido, tratar como array con un solo string
        return [value];
      }
    }
    return [value];
  }

  // Helper para parsear int que puede venir como String o int
  static int? _parseInt(dynamic value) {
    if (value == null) return null;
    if (value is int) return value;
    if (value is String) {
      final parsed = int.tryParse(value);
      return parsed;
    }
    if (value is num) return value.toInt();
    return null;
  }

  // Helper para parsear int requerido que puede venir como String o int
  static int _parseIntRequired(dynamic value) {
    if (value is int) return value;
    if (value is String) {
      final parsed = int.tryParse(value);
      if (parsed != null) return parsed;
    }
    if (value is num) return value.toInt();
    throw FormatException('No se pudo parsear como int: $value');
  }

  // Helper para parsear String que puede ser null
  static String _parseString(dynamic value, [String defaultValue = '']) {
    if (value == null) return defaultValue;
    return value.toString();
  }

  // Helper para parsear DateTime que puede ser null
  static DateTime? _parseDateTime(dynamic value) {
    if (value == null) return null;
    if (value is String && value.isNotEmpty) {
      return DateTime.tryParse(value);
    }
    return null;
  }

  factory Evento.fromJson(Map<String, dynamic> json) {
    return Evento(
      id: _parseIntRequired(json['id']),
      ongId: _parseInt(json['ong_id']),
      titulo: _parseString(json['titulo'], 'Sin título'),
      descripcion: json['descripcion']?.toString(),
      tipoEvento: _parseString(json['tipo_evento'], ''),
      fechaInicio: _parseDateTime(json['fecha_inicio']) ?? DateTime.now(),
      fechaFin: _parseDateTime(json['fecha_fin']),
      fechaLimiteInscripcion: _parseDateTime(json['fecha_limite_inscripcion']),
      capacidadMaxima: _parseInt(json['capacidad_maxima']),
      estado: _parseString(json['estado'], 'pendiente'),
      ciudad: json['ciudad']?.toString(),
      direccion: json['direccion']?.toString(),
      lat:
          json['lat'] != null
              ? (json['lat'] is String
                  ? double.tryParse(json['lat'] as String)
                  : (json['lat'] is num ? (json['lat'] as num).toDouble() : null))
              : null,
      lng:
          json['lng'] != null
              ? (json['lng'] is String
                  ? double.tryParse(json['lng'] as String)
                  : (json['lng'] is num ? (json['lng'] as num).toDouble() : null))
              : null,
      inscripcionAbierta:
          json['inscripcion_abierta'] == 1 ||
          json['inscripcion_abierta'] == true,
      patrocinadores: _parseJsonArray(json['patrocinadores']),
      invitados: _parseJsonArray(json['invitados']),
      imagenes: _parseJsonArray(json['imagenes']),
      auspiciadores: _parseJsonArray(json['auspiciadores']),
      createdAt: _parseDateTime(json['created_at']),
      updatedAt: _parseDateTime(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ong_id': ongId,
      'titulo': titulo,
      'descripcion': descripcion,
      'tipo_evento': tipoEvento,
      'fecha_inicio': fechaInicio.toIso8601String(),
      'fecha_fin': fechaFin?.toIso8601String(),
      'fecha_limite_inscripcion': fechaLimiteInscripcion?.toIso8601String(),
      'capacidad_maxima': capacidadMaxima,
      'estado': estado,
      'ciudad': ciudad,
      'direccion': direccion,
      'lat': lat,
      'lng': lng,
      'inscripcion_abierta': inscripcionAbierta,
      'patrocinadores': patrocinadores,
      'invitados': invitados,
      'imagenes': imagenes,
      'auspiciadores': auspiciadores,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  bool get puedeInscribirse {
    if (!inscripcionAbierta) return false;
    if (fechaLimiteInscripcion != null &&
        DateTime.now().isAfter(fechaLimiteInscripcion!)) {
      return false;
    }
    return true;
  }
}
