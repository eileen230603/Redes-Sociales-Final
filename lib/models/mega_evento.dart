import 'dart:convert';

class MegaEvento {
  final int megaEventoId;
  final String titulo;
  final String? descripcion;
  final DateTime fechaInicio;
  final DateTime fechaFin;
  final String? ubicacion;
  final double? lat;
  final double? lng;
  final String? categoria;
  final String estado;
  final int ongOrganizadoraPrincipal;
  final int? capacidadMaxima;
  final bool esPublico;
  final bool activo;
  final List<dynamic>? imagenes;
  final DateTime? fechaCreacion;
  final DateTime? fechaActualizacion;
  final Map<String, dynamic>? ongPrincipal;

  MegaEvento({
    required this.megaEventoId,
    required this.titulo,
    this.descripcion,
    required this.fechaInicio,
    required this.fechaFin,
    this.ubicacion,
    this.lat,
    this.lng,
    this.categoria,
    required this.estado,
    required this.ongOrganizadoraPrincipal,
    this.capacidadMaxima,
    required this.esPublico,
    required this.activo,
    this.imagenes,
    this.fechaCreacion,
    this.fechaActualizacion,
    this.ongPrincipal,
  });

  // Helper para parsear arrays que pueden venir como JSON string o array
  static List<dynamic>? _parseJsonArray(dynamic value) {
    if (value == null) return null;
    if (value is List) {
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
      final trimmed = value.trim();
      if (trimmed.isEmpty || trimmed == '[]' || trimmed == 'null') {
        return null;
      }

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
        return [value];
      } catch (e) {
        return [value];
      }
    }
    return [value];
  }

  factory MegaEvento.fromJson(Map<String, dynamic> json) {
    return MegaEvento(
      megaEventoId: json['mega_evento_id'] as int,
      titulo: json['titulo'] as String,
      descripcion: json['descripcion'] as String?,
      fechaInicio: DateTime.parse(json['fecha_inicio'] as String),
      fechaFin: DateTime.parse(json['fecha_fin'] as String),
      ubicacion: json['ubicacion'] as String?,
      lat:
          json['lat'] != null
              ? (json['lat'] is String
                  ? double.tryParse(json['lat'] as String)
                  : (json['lat'] as num).toDouble())
              : null,
      lng:
          json['lng'] != null
              ? (json['lng'] is String
                  ? double.tryParse(json['lng'] as String)
                  : (json['lng'] as num).toDouble())
              : null,
      categoria: json['categoria'] as String?,
      estado: json['estado'] as String,
      ongOrganizadoraPrincipal: json['ong_organizadora_principal'] as int,
      capacidadMaxima: json['capacidad_maxima'] as int?,
      esPublico: json['es_publico'] == 1 || json['es_publico'] == true,
      activo: json['activo'] == 1 || json['activo'] == true,
      imagenes: _parseJsonArray(json['imagenes']),
      fechaCreacion:
          json['fecha_creacion'] != null
              ? DateTime.parse(json['fecha_creacion'] as String)
              : null,
      fechaActualizacion:
          json['fecha_actualizacion'] != null
              ? DateTime.parse(json['fecha_actualizacion'] as String)
              : null,
      ongPrincipal:
          json['ong_principal'] != null
              ? json['ong_principal'] as Map<String, dynamic>
              : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'mega_evento_id': megaEventoId,
      'titulo': titulo,
      'descripcion': descripcion,
      'fecha_inicio': fechaInicio.toIso8601String(),
      'fecha_fin': fechaFin.toIso8601String(),
      'ubicacion': ubicacion,
      'lat': lat,
      'lng': lng,
      'categoria': categoria,
      'estado': estado,
      'ong_organizadora_principal': ongOrganizadoraPrincipal,
      'capacidad_maxima': capacidadMaxima,
      'es_publico': esPublico,
      'activo': activo,
      'imagenes': imagenes,
      'fecha_creacion': fechaCreacion?.toIso8601String(),
      'fecha_actualizacion': fechaActualizacion?.toIso8601String(),
      'ong_principal': ongPrincipal,
    };
  }

  bool get estaFinalizado {
    return estado == 'finalizado' || (fechaFin.isBefore(DateTime.now()));
  }

  bool get estaActivo {
    return activo && estado == 'activo' || estado == 'en_curso';
  }
}
