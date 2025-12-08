class TipoEvento {
  final int id;
  final String codigo;
  final String nombre;
  final String? descripcion;
  final String? icono;
  final String? color;
  final int orden;
  final bool activo;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  TipoEvento({
    required this.id,
    required this.codigo,
    required this.nombre,
    this.descripcion,
    this.icono,
    this.color,
    required this.orden,
    required this.activo,
    this.createdAt,
    this.updatedAt,
  });

  factory TipoEvento.fromJson(Map<String, dynamic> json) {
    return TipoEvento(
      id: json['id'] as int,
      codigo: json['codigo'] as String,
      nombre: json['nombre'] as String,
      descripcion: json['descripcion'] as String?,
      icono: json['icono'] as String?,
      color: json['color'] as String?,
      orden: json['orden'] as int? ?? 0,
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
      'codigo': codigo,
      'nombre': nombre,
      'descripcion': descripcion,
      'icono': icono,
      'color': color,
      'orden': orden,
      'activo': activo,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
