import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/auth_response.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import 'storage_service.dart';

class ApiService {
  // Obtener headers con autenticación
  static Future<Map<String, String>> _getHeaders({
    bool includeAuth = false,
  }) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (includeAuth) {
      final token = await StorageService.getToken();
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }

    return headers;
  }

  // Login
  static Future<AuthResponse> login({
    required String email,
    required String password,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}${ApiConfig.loginEndpoint}');
      print('🔗 Intentando conectar a: $url');

      final response = await http
          .post(
            url,
            headers: await _getHeaders(),
            body: jsonEncode({
              'correo_electronico': email,
              'contrasena': password,
            }),
          )
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () {
              throw Exception(
                'Tiempo de espera agotado. Verifica que el servidor Laravel esté corriendo.',
              );
            },
          );

      print('📡 Respuesta recibida: ${response.statusCode}');

      if (response.statusCode != 200) {
        return AuthResponse(
          success: false,
          error:
              'Error del servidor (${response.statusCode}). Verifica que el servidor Laravel esté corriendo.',
        );
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      final authResponse = AuthResponse.fromJson(data);

      if (authResponse.success && authResponse.token != null) {
        // Guardar token y datos del usuario
        await StorageService.saveToken(authResponse.token!);
        if (authResponse.user != null) {
          await StorageService.saveUserData(
            userId: authResponse.user!.idUsuario,
            userName: authResponse.user!.nombreUsuario,
            userType: authResponse.user!.tipoUsuario,
            entityId: authResponse.user!.idEntidad,
          );
        }
      }

      return authResponse;
    } on SocketException catch (e) {
      print('❌ SocketException: ${e.message}');
      return AuthResponse(
        success: false,
        error:
            'No se pudo conectar al servidor.\n\n'
            'Verifica:\n'
            '1. Que el servidor Laravel esté corriendo:\n'
            '   cd Redes-Sociales-Final\n'
            '   php artisan serve\n\n'
            '2. Que la URL en lib/config/api_config.dart sea correcta:\n'
            '   - Emulador Android: http://10.0.2.2:8000/api\n'
            '   - Dispositivo físico: http://TU_IP_LOCAL:8000/api\n\n'
            '3. Que el firewall no bloquee la conexión',
      );
    } on TimeoutException catch (e) {
      print('❌ TimeoutException: ${e.message}');
      return AuthResponse(
        success: false,
        error:
            'Tiempo de espera agotado.\n\n'
            'El servidor no responde. Verifica que:\n'
            '1. El servidor Laravel esté corriendo\n'
            '2. La URL sea correcta\n'
            '3. No haya problemas de red',
      );
    } catch (e) {
      print('❌ Error: ${e.toString()}');
      return AuthResponse(
        success: false,
        error:
            'Error de conexión: ${e.toString()}\n\n'
            'Asegúrate de que:\n'
            '- El servidor Laravel esté corriendo (php artisan serve)\n'
            '- La URL sea correcta en lib/config/api_config.dart',
      );
    }
  }

  // Register
  static Future<AuthResponse> register({
    required String tipoUsuario,
    required String nombreUsuario,
    required String correoElectronico,
    required String contrasena,
    // Campos opcionales según tipo
    String? nombres,
    String? apellidos,
    String? nombreOng,
    String? nombreEmpresa,
    String? nit,
    String? telefono,
    String? direccion,
    String? sitioWeb,
    String? descripcion,
    String? fechaNacimiento,
  }) async {
    try {
      final body = {
        'tipo_usuario': tipoUsuario,
        'nombre_usuario': nombreUsuario,
        'correo_electronico': correoElectronico,
        'contrasena': contrasena,
      };

      // Agregar campos según tipo de usuario (exactamente como Laravel los espera)
      // Ver: app/Http/Controllers/Auth/AuthController.php líneas 21-41
      // Validación Laravel:
      // - Integrante externo: nombres, apellidos (required_if)
      // - ONG: nombre_ong (required_if)
      // - Empresa: nombre_empresa (required_if)
      // - Opcionales: NIT, telefono, direccion, sitio_web, descripcion, fecha_nacimiento

      if (tipoUsuario == 'Integrante externo') {
        // Campos requeridos (required_if:tipo_usuario,Integrante externo)
        // La validación del frontend ya asegura que estos campos tengan valor
        body['nombres'] = nombres?.trim() ?? '';
        body['apellidos'] = apellidos?.trim() ?? '';
        // Campos opcionales (nullable) - solo se envían si tienen valor
        if (fechaNacimiento != null && fechaNacimiento.trim().isNotEmpty) {
          body['fecha_nacimiento'] = fechaNacimiento.trim();
        }
        if (telefono != null && telefono.trim().isNotEmpty) {
          body['telefono'] = telefono.trim();
        }
        if (descripcion != null && descripcion.trim().isNotEmpty) {
          body['descripcion'] = descripcion.trim();
        }
      } else if (tipoUsuario == 'ONG') {
        // Campo requerido (required_if:tipo_usuario,ONG)
        // La validación del frontend ya asegura que este campo tenga valor
        body['nombre_ong'] = nombreOng?.trim() ?? '';
        // Campos opcionales (nullable) - solo se envían si tienen valor
        if (nit != null && nit.trim().isNotEmpty) {
          body['NIT'] = nit.trim();
        }
        if (telefono != null && telefono.trim().isNotEmpty) {
          body['telefono'] = telefono.trim();
        }
        if (direccion != null && direccion.trim().isNotEmpty) {
          body['direccion'] = direccion.trim();
        }
        if (sitioWeb != null && sitioWeb.trim().isNotEmpty) {
          body['sitio_web'] = sitioWeb.trim();
        }
        if (descripcion != null && descripcion.trim().isNotEmpty) {
          body['descripcion'] = descripcion.trim();
        }
      } else if (tipoUsuario == 'Empresa') {
        // Campo requerido (required_if:tipo_usuario,Empresa)
        // La validación del frontend ya asegura que este campo tenga valor
        body['nombre_empresa'] = nombreEmpresa?.trim() ?? '';
        // Campos opcionales (nullable) - solo se envían si tienen valor
        if (nit != null && nit.trim().isNotEmpty) {
          body['NIT'] = nit.trim();
        }
        if (telefono != null && telefono.trim().isNotEmpty) {
          body['telefono'] = telefono.trim();
        }
        if (direccion != null && direccion.trim().isNotEmpty) {
          body['direccion'] = direccion.trim();
        }
        if (sitioWeb != null && sitioWeb.trim().isNotEmpty) {
          body['sitio_web'] = sitioWeb.trim();
        }
        if (descripcion != null && descripcion.trim().isNotEmpty) {
          body['descripcion'] = descripcion.trim();
        }
      }

      final url = Uri.parse(
        '${ApiConfig.baseUrl}${ApiConfig.registerEndpoint}',
      );
      print('🔗 Intentando conectar a: $url');
      print('📦 Body a enviar: ${jsonEncode(body)}');

      final response = await http
          .post(url, headers: await _getHeaders(), body: jsonEncode(body))
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () {
              throw TimeoutException('Tiempo de espera agotado');
            },
          );

      print('📡 Respuesta recibida: ${response.statusCode}');

      // Leer el body de la respuesta
      final responseBody = response.body;
      print('📥 Respuesta del servidor: $responseBody');

      // Si hay error, intentar parsear el mensaje
      if (response.statusCode != 200 && response.statusCode != 201) {
        try {
          final errorData = jsonDecode(responseBody) as Map<String, dynamic>;
          final errorMessage =
              errorData['error'] ??
              errorData['message'] ??
              'Error del servidor (${response.statusCode})';

          // Si hay errores de validación, mostrarlos todos
          if (errorData.containsKey('errors')) {
            final errors = errorData['errors'] as Map<String, dynamic>;
            final errorList = errors.values
                .expand((e) => (e as List).cast<String>())
                .join('\n');
            return AuthResponse(
              success: false,
              error: 'Errores de validación:\n$errorList',
            );
          }

          return AuthResponse(success: false, error: errorMessage.toString());
        } catch (e) {
          return AuthResponse(
            success: false,
            error: 'Error del servidor (${response.statusCode}): $responseBody',
          );
        }
      }

      final data = jsonDecode(responseBody) as Map<String, dynamic>;
      final authResponse = AuthResponse.fromJson(data);

      if (authResponse.success && authResponse.token != null) {
        // Guardar token y datos del usuario
        await StorageService.saveToken(authResponse.token!);
        if (authResponse.user != null) {
          await StorageService.saveUserData(
            userId: authResponse.user!.idUsuario,
            userName: authResponse.user!.nombreUsuario,
            userType: authResponse.user!.tipoUsuario,
            entityId: authResponse.user!.idEntidad,
          );
        }
      }

      return authResponse;
    } on SocketException {
      return AuthResponse(
        success: false,
        error:
            'No se pudo conectar al servidor. Verifica que el servidor Laravel esté corriendo.',
      );
    } on TimeoutException {
      return AuthResponse(
        success: false,
        error: 'Tiempo de espera agotado. El servidor no responde.',
      );
    } catch (e) {
      return AuthResponse(
        success: false,
        error: 'Error de conexión: ${e.toString()}',
      );
    }
  }

  // Logout
  static Future<bool> logout() async {
    try {
      // Intentar cerrar sesión en el servidor
      try {
        await http.post(
          Uri.parse('${ApiConfig.baseUrl}${ApiConfig.logoutEndpoint}'),
          headers: await _getHeaders(includeAuth: true),
        );
        // No importa el resultado, siempre limpiar la sesión local
      } catch (e) {
        // Si falla la llamada, continuar con la limpieza local
      }

      // Siempre limpiar la sesión local
      await StorageService.clearSession();
      return true;
    } catch (e) {
      // Aún así limpiar la sesión local
      await StorageService.clearSession();
      return true;
    }
  }

  // ========== EVENTOS ==========

  // Listar eventos publicados
  static Future<Map<String, dynamic>> getEventosPublicados() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final eventosList =
            (data['eventos'] as List)
                .map((e) => Evento.fromJson(e as Map<String, dynamic>))
                .toList();
        return {'success': true, 'eventos': eventosList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener detalle de evento
  static Future<Map<String, dynamic>> getEventoDetalle(int eventoId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/detalle/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final evento = Evento.fromJson(data['evento'] as Map<String, dynamic>);
        return {'success': true, 'evento': evento};
      }

      return {
        'success': false,
        'error': data['error'] ?? data['message'] ?? 'Error al obtener evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== PARTICIPACIONES ==========

  // Inscribirse en evento
  static Future<Map<String, dynamic>> inscribirEnEvento(int eventoId) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/participaciones/inscribir'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'evento_id': eventoId}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Inscripción exitosa',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al inscribirse',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Cancelar inscripción
  static Future<Map<String, dynamic>> cancelarInscripcion(int eventoId) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/participaciones/cancelar'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'evento_id': eventoId}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Inscripción cancelada',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al cancelar inscripción',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener mis eventos inscritos
  static Future<Map<String, dynamic>> getMisEventos() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/participaciones/mis-eventos'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final participacionesList =
            (data['eventos'] as List)
                .map(
                  (e) =>
                      EventoParticipacion.fromJson(e as Map<String, dynamic>),
                )
                .toList();
        return {'success': true, 'participaciones': participacionesList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== EMPRESAS - PATROCINIOS ==========

  // Patrocinar un evento
  static Future<Map<String, dynamic>> patrocinarEvento({
    required int eventoId,
    required int empresaId,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/patrocinar'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'empresa_id': empresaId}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento patrocinado exitosamente',
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ?? data['message'] ?? 'Error al patrocinar evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== ONG - EVENTOS ==========

  // Listar eventos de una ONG
  static Future<Map<String, dynamic>> getEventosOng(int ongId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/ong/$ongId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final eventosList =
            (data['eventos'] as List)
                .map((e) => Evento.fromJson(e as Map<String, dynamic>))
                .toList();
        return {'success': true, 'eventos': eventosList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Crear evento
  static Future<Map<String, dynamic>> crearEvento(
    Map<String, dynamic> eventoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode(eventoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento creado exitosamente',
          'evento': data['evento'],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? data['message'] ?? 'Error al crear evento',
        'errors': data['errors'],
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Actualizar evento
  static Future<Map<String, dynamic>> actualizarEvento(
    int eventoId,
    Map<String, dynamic> eventoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode(eventoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento actualizado exitosamente',
          'evento': data['evento'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ?? data['message'] ?? 'Error al actualizar evento',
        'errors': data['errors'],
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Eliminar evento
  static Future<Map<String, dynamic>> eliminarEvento(int eventoId) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento eliminado exitosamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? data['message'] ?? 'Error al eliminar evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== ONG - VOLUNTARIOS ==========

  // Listar voluntarios de una ONG
  static Future<Map<String, dynamic>> getVoluntariosOng(int ongId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/voluntarios/ong/$ongId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'voluntarios': data['voluntarios'] as List};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener voluntarios',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }
}
