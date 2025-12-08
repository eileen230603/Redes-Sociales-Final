import 'evento.dart';

class EventoParticipacion {
  final int id;
  final int eventoId;
  final int externoId;
  final bool asistio;
  final int puntos;
  final Evento? evento;

  EventoParticipacion({
    required this.id,
    required this.eventoId,
    required this.externoId,
    required this.asistio,
    required this.puntos,
    this.evento,
  });

  factory EventoParticipacion.fromJson(Map<String, dynamic> json) {
    return EventoParticipacion(
      id: (json['id'] as num?)?.toInt() ?? 0,
      eventoId:
          (json['evento_id'] as num?)?.toInt() ??
          (json['eventoId'] as num?)?.toInt() ??
          0,
      externoId:
          (json['externo_id'] as num?)?.toInt() ??
          (json['externoId'] as num?)?.toInt() ??
          0,
      asistio: json['asistio'] == 1 || json['asistio'] == true,
      puntos:
          (json['puntos'] is num)
              ? (json['puntos'] as num).toInt()
              : int.tryParse(json['puntos']?.toString() ?? '') ?? 0,
      evento:
          json['evento'] != null
              ? Evento.fromJson(json['evento'] as Map<String, dynamic>)
              : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'evento_id': eventoId,
      'externo_id': externoId,
      'asistio': asistio,
      'puntos': puntos,
      'evento': evento?.toJson(),
    };
  }
}
