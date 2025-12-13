class User {
  final int idUsuario;
  final String nombreUsuario;
  final String tipoUsuario;
  final int? idEntidad;
  final List<String> roles;
  final List<String> permissions;

  User({
    required this.idUsuario,
    required this.nombreUsuario,
    required this.tipoUsuario,
    this.idEntidad,
    this.roles = const [],
    this.permissions = const [],
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      idUsuario: json['id_usuario'] as int,
      nombreUsuario: json['nombre_usuario'] as String,
      tipoUsuario: json['tipo_usuario'] as String,
      idEntidad: json['id_entidad'] as int?,
      roles:
          json['roles'] != null
              ? (json['roles'] as List).map((e) => e.toString()).toList()
              : [],
      permissions:
          json['permissions'] != null
              ? (json['permissions'] as List).map((e) => e.toString()).toList()
              : [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id_usuario': idUsuario,
      'nombre_usuario': nombreUsuario,
      'tipo_usuario': tipoUsuario,
      'id_entidad': idEntidad,
      'roles': roles,
      'permissions': permissions,
    };
  }
}
