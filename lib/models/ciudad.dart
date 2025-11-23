class Ciudad {
  final int id;
  final String nombre;
  final String? codigoPostal;
  final String? departamento;
  final String? pais;
  final double? lat;
  final double? lng;
  final bool activo;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Ciudad({
    required this.id,
    required this.nombre,
    this.codigoPostal,
    this.departamento,
    this.pais,
    this.lat,
    this.lng,
    required this.activo,
    this.createdAt,
    this.updatedAt,
  });

  factory Ciudad.fromJson(Map<String, dynamic> json) {
    return Ciudad(
      id: json['id'] as int,
      nombre: json['nombre'] as String,
      codigoPostal: json['codigo_postal'] as String?,
      departamento: json['departamento'] as String?,
      pais: json['pais'] as String?,
      lat: json['lat'] != null ? (json['lat'] as num).toDouble() : null,
      lng: json['lng'] != null ? (json['lng'] as num).toDouble() : null,
      activo: json['activo'] == 1 || json['activo'] == true,
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
      'nombre': nombre,
      'codigo_postal': codigoPostal,
      'departamento': departamento,
      'pais': pais,
      'lat': lat,
      'lng': lng,
      'activo': activo,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  String get nombreCompleto {
    final parts = <String>[nombre];
    if (departamento != null) parts.add(departamento!);
    if (pais != null) parts.add(pais!);
    return parts.join(', ');
  }
}
