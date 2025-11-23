import 'dart:convert';

class Parametro {
  final int id;
  final String codigo;
  final String nombre;
  final String? descripcion;
  final String categoria;
  final String tipo; // texto, numero, booleano, json, fecha, select
  final String? valor;
  final String? valorDefecto;
  final List<dynamic>? opciones;
  final String? grupo;
  final int orden;
  final bool editable;
  final bool visible;
  final bool requerido;
  final String? validacion;
  final String? ayuda;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Parametro({
    required this.id,
    required this.codigo,
    required this.nombre,
    this.descripcion,
    required this.categoria,
    required this.tipo,
    this.valor,
    this.valorDefecto,
    this.opciones,
    this.grupo,
    required this.orden,
    required this.editable,
    required this.visible,
    required this.requerido,
    this.validacion,
    this.ayuda,
    this.createdAt,
    this.updatedAt,
  });

  factory Parametro.fromJson(Map<String, dynamic> json) {
    return Parametro(
      id: json['id'] as int,
      codigo: json['codigo'] as String,
      nombre: json['nombre'] as String,
      descripcion: json['descripcion'] as String?,
      categoria: json['categoria'] as String,
      tipo: json['tipo'] as String,
      valor: json['valor'] as String?,
      valorDefecto: json['valor_defecto'] as String?,
      opciones: json['opciones'] as List<dynamic>?,
      grupo: json['grupo'] as String?,
      orden: json['orden'] as int? ?? 0,
      editable: json['editable'] == 1 || json['editable'] == true,
      visible: json['visible'] == 1 || json['visible'] == true,
      requerido: json['requerido'] == 1 || json['requerido'] == true,
      validacion: json['validacion'] as String?,
      ayuda: json['ayuda'] as String?,
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
      'categoria': categoria,
      'tipo': tipo,
      'valor': valor,
      'valor_defecto': valorDefecto,
      'opciones': opciones,
      'grupo': grupo,
      'orden': orden,
      'editable': editable,
      'visible': visible,
      'requerido': requerido,
      'validacion': validacion,
      'ayuda': ayuda,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  // Obtener valor formateado según tipo
  dynamic get valorFormateado {
    final valorFinal = valor ?? valorDefecto;
    if (valorFinal == null) return null;

    switch (tipo) {
      case 'numero':
        return double.tryParse(valorFinal.toString()) ?? 0;
      case 'booleano':
        if (valorFinal is bool) return valorFinal;
        return valorFinal == '1' || valorFinal == 'true';
      case 'json':
        try {
          if (valorFinal is String) {
            return jsonDecode(valorFinal) as Map<String, dynamic>;
          }
          return valorFinal;
        } catch (e) {
          return valorFinal;
        }
      case 'fecha':
        try {
          return DateTime.parse(valorFinal.toString());
        } catch (e) {
          return valorFinal;
        }
      default:
        return valorFinal;
    }
  }
}
