class Notificacion {
  final int id;
  final String tipo;
  final String titulo;
  final String mensaje;
  final bool leida;
  final int? eventoId;
  final String? eventoTitulo;
  final int? externoId;
  final DateTime fecha;

  Notificacion({
    required this.id,
    required this.tipo,
    required this.titulo,
    required this.mensaje,
    required this.leida,
    this.eventoId,
    this.eventoTitulo,
    this.externoId,
    required this.fecha,
  });

  factory Notificacion.fromJson(Map<String, dynamic> json) {
    return Notificacion(
      id: json['id'] as int,
      tipo: json['tipo'] as String? ?? 'general',
      titulo: json['titulo'] as String? ?? '',
      mensaje: json['mensaje'] as String? ?? '',
      leida: json['leida'] as bool? ?? false,
      eventoId: json['evento_id'] as int?,
      eventoTitulo: json['evento_titulo'] as String?,
      externoId: json['externo_id'] as int?,
      fecha:
          json['fecha'] != null
              ? DateTime.parse(json['fecha'] as String)
              : DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'tipo': tipo,
      'titulo': titulo,
      'mensaje': mensaje,
      'leida': leida,
      'evento_id': eventoId,
      'evento_titulo': eventoTitulo,
      'externo_id': externoId,
      'fecha': fecha.toIso8601String(),
    };
  }

  Notificacion copyWith({
    int? id,
    String? tipo,
    String? titulo,
    String? mensaje,
    bool? leida,
    int? eventoId,
    String? eventoTitulo,
    int? externoId,
    DateTime? fecha,
  }) {
    return Notificacion(
      id: id ?? this.id,
      tipo: tipo ?? this.tipo,
      titulo: titulo ?? this.titulo,
      mensaje: mensaje ?? this.mensaje,
      leida: leida ?? this.leida,
      eventoId: eventoId ?? this.eventoId,
      eventoTitulo: eventoTitulo ?? this.eventoTitulo,
      externoId: externoId ?? this.externoId,
      fecha: fecha ?? this.fecha,
    );
  }
}
