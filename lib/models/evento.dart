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

  factory Evento.fromJson(Map<String, dynamic> json) {
    return Evento(
      id: json['id'] as int,
      ongId: json['ong_id'] as int?,
      titulo: json['titulo'] as String,
      descripcion: json['descripcion'] as String?,
      tipoEvento: json['tipo_evento'] as String,
      fechaInicio: DateTime.parse(json['fecha_inicio'] as String),
      fechaFin:
          json['fecha_fin'] != null
              ? DateTime.parse(json['fecha_fin'] as String)
              : null,
      fechaLimiteInscripcion:
          json['fecha_limite_inscripcion'] != null
              ? DateTime.parse(json['fecha_limite_inscripcion'] as String)
              : null,
      capacidadMaxima: json['capacidad_maxima'] as int?,
      estado: json['estado'] as String,
      ciudad: json['ciudad'] as String?,
      direccion: json['direccion'] as String?,
      lat: json['lat'] != null ? (json['lat'] as num).toDouble() : null,
      lng: json['lng'] != null ? (json['lng'] as num).toDouble() : null,
      inscripcionAbierta:
          json['inscripcion_abierta'] == 1 ||
          json['inscripcion_abierta'] == true,
      patrocinadores: json['patrocinadores'] as List<dynamic>?,
      invitados: json['invitados'] as List<dynamic>?,
      imagenes: json['imagenes'] as List<dynamic>?,
      auspiciadores: json['auspiciadores'] as List<dynamic>?,
      createdAt:
          json['created_at'] != null
              ? DateTime.parse(json['created_at'] as String)
              : null,
      updatedAt:
          json['updated_at'] != null
              ? DateTime.parse(json['updated_at'] as String)
              : null,
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
