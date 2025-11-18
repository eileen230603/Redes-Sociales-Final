class User {
  final int idUsuario;
  final String nombreUsuario;
  final String tipoUsuario;
  final int? idEntidad;

  User({
    required this.idUsuario,
    required this.nombreUsuario,
    required this.tipoUsuario,
    this.idEntidad,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      idUsuario: json['id_usuario'] as int,
      nombreUsuario: json['nombre_usuario'] as String,
      tipoUsuario: json['tipo_usuario'] as String,
      idEntidad: json['id_entidad'] as int?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id_usuario': idUsuario,
      'nombre_usuario': nombreUsuario,
      'tipo_usuario': tipoUsuario,
      'id_entidad': idEntidad,
    };
  }
}
