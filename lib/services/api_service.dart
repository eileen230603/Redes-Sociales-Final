import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import '../config/api_config.dart';
import '../models/auth_response.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import '../models/notificacion.dart';
import 'storage_service.dart';
import 'cache_service.dart';

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

  // Helper para parsear JSON de forma segura, limpiando contenido extra
  static Map<String, dynamic> _parseJsonSafely(String body, String endpoint) {
    try {
      // Limpiar la respuesta
      String cleanedBody = body.trim();

      // Verificar si contiene HTML (error de Laravel)
      if (cleanedBody.contains('<!DOCTYPE') ||
          cleanedBody.contains('<html') ||
          cleanedBody.contains('<!doctype')) {
        print(
          '❌ Error: La respuesta contiene HTML en lugar de JSON para $endpoint',
        );
        // Intentar extraer JSON si está dentro de un script tag o similar
        final jsonMatch = RegExp(
          r'\{[\s\S]*\}',
          dotAll: true,
        ).firstMatch(cleanedBody);
        if (jsonMatch != null) {
          cleanedBody = jsonMatch.group(0)!;
          print('✅ JSON extraído del HTML');
        } else {
          throw FormatException(
            'La respuesta del servidor contiene HTML en lugar de JSON',
          );
        }
      }

      // Buscar el inicio del JSON (puede haber espacios o caracteres antes)
      final jsonStart = cleanedBody.indexOf('{');
      if (jsonStart > 0) {
        cleanedBody = cleanedBody.substring(jsonStart);
        print(
          '⚠️ Se encontraron ${jsonStart} caracteres antes del JSON, limpiados',
        );
      }

      // Buscar el final del JSON (puede haber contenido después)
      int jsonEnd = cleanedBody.lastIndexOf('}');
      if (jsonEnd > 0 && jsonEnd < cleanedBody.length - 1) {
        // Verificar si hay más contenido después del JSON válido
        final afterJson = cleanedBody.substring(jsonEnd + 1).trim();
        if (afterJson.isNotEmpty && !afterJson.startsWith('}')) {
          cleanedBody = cleanedBody.substring(0, jsonEnd + 1);
          print(
            '⚠️ Se encontraron ${afterJson.length} caracteres después del JSON, limpiados',
          );
        }
      }

      // Verificar que el body no esté vacío
      if (cleanedBody.isEmpty) {
        throw FormatException('La respuesta del servidor está vacía');
      }

      // Intentar parsear el JSON
      return jsonDecode(cleanedBody) as Map<String, dynamic>;
    } on FormatException catch (e) {
      print('❌ Error parseando JSON para $endpoint: ${e.message}');
      if (body.length > 0) {
        final preview = body.length > 500 ? body.substring(0, 500) : body;
        print('📄 Primeros caracteres de la respuesta: $preview...');
        if (body.length > 500) {
          final lastChars =
              body.length > 1000
                  ? body.substring(body.length - 500)
                  : body.substring(500);
          print('📄 Últimos caracteres: ...$lastChars');
        }
      }
      rethrow;
    }
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
            roles: authResponse.user!.roles,
            permissions: authResponse.user!.permissions,
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
            '   - Web/Chrome: http://localhost:8000/api\n'
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
            roles: authResponse.user!.roles,
            permissions: authResponse.user!.permissions,
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
      final url = Uri.parse('${ApiConfig.baseUrl}/eventos');
      print('🔗 Intentando obtener eventos desde: $url');

      final response = await http
          .get(url, headers: await _getHeaders(includeAuth: true))
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () {
              throw Exception(
                'Tiempo de espera agotado. Verifica que el servidor esté corriendo en ${ApiConfig.baseUrl}',
              );
            },
          );

      // Manejar errores del servidor (500, etc.)
      if (response.statusCode >= 500) {
        print('❌ Error del servidor: ${response.statusCode}');
        return {
          'success': false,
          'error':
              'Error del servidor (${response.statusCode}). Intenta nuevamente.',
        };
      }

      // Verificar Content-Type
      final contentType = response.headers['content-type'] ?? '';
      if (!contentType.contains('application/json') &&
          response.statusCode == 200) {
        print('⚠️ Advertencia: Content-Type no es JSON: $contentType');
      }

      // Parsear JSON de forma segura
      final data = _parseJsonSafely(response.body, 'getEventosPublicados');

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
    } on SocketException catch (e) {
      print('❌ SocketException: ${e.message}');
      return {
        'success': false,
        'error':
            'No se pudo conectar al servidor.\n\n'
            'Verifica que el servidor Laravel esté corriendo:\n'
            'cd Redes-Sociales-Final\n'
            'php artisan serve',
      };
    } on TimeoutException catch (e) {
      print('❌ TimeoutException: ${e.message}');
      return {
        'success': false,
        'error':
            'Tiempo de espera agotado.\n\n'
            'El servidor no respondió a tiempo.\n'
            'Verifica que el servidor esté corriendo:\n'
            'cd Redes-Sociales-Final\n'
            'php artisan serve',
      };
    } catch (e) {
      print('❌ Error: ${e.toString()}');
      String errorMessage = 'Error de conexión';
      if (e.toString().contains('Failed to fetch') ||
          e.toString().contains('ClientException')) {
        errorMessage =
            'No se pudo conectar al servidor.\n\n'
            'Verifica que el servidor Laravel esté corriendo.\n\n'
            'Para iniciar el servidor:\n'
            'cd Redes-Sociales-Final\n'
            'php artisan serve';
      } else {
        errorMessage = 'Error de conexión: ${e.toString()}';
      }
      return {'success': false, 'error': errorMessage};
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

  // Obtener participantes de un evento (ONG)
  static Future<Map<String, dynamic>> getParticipantesEvento(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/participaciones/evento/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'participantes': data['participantes'] as List? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener participantes',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Aprobar participación
  static Future<Map<String, dynamic>> aprobarParticipacion(
    int participacionId,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones/$participacionId/aprobar',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Participación aprobada exitosamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al aprobar participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Marcar / desmarcar asistencia (participante registrado)
  static Future<Map<String, dynamic>> marcarAsistenciaParticipacion(
    int participacionId, {
    required bool asistio,
  }) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones/$participacionId/asistencia',
        ),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'asistio': asistio}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Asistencia actualizada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al actualizar asistencia',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Registrar asistencia desde el móvil (usuario externo)
  static Future<Map<String, dynamic>> registrarAsistenciaExterno(
    int participacionId, {
    required String codigo,
  }) async {
    try {
      final response = await http.post(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones/$participacionId/registrar-asistencia',
        ),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'codigo': codigo}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Asistencia registrada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al registrar asistencia',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Rechazar participación
  static Future<Map<String, dynamic>> rechazarParticipacion(
    int participacionId,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones/$participacionId/rechazar',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Participación rechazada exitosamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al rechazar participación',
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
      final url = Uri.parse('${ApiConfig.baseUrl}/eventos/ong/$ongId');
      print('🔗 Intentando obtener eventos de ONG desde: $url');

      final response = await http
          .get(url, headers: await _getHeaders(includeAuth: true))
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () {
              throw Exception(
                'Tiempo de espera agotado. Verifica que el servidor esté corriendo en ${ApiConfig.baseUrl}',
              );
            },
          );

      // Verificar Content-Type
      final contentType = response.headers['content-type'] ?? '';
      if (!contentType.contains('application/json') &&
          response.statusCode == 200) {
        print('⚠️ Advertencia: Content-Type no es JSON: $contentType');
      }

      // Parsear JSON de forma segura
      final data = _parseJsonSafely(response.body, 'getEventosOng');

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
    } on SocketException catch (e) {
      print('❌ SocketException: ${e.message}');
      return {
        'success': false,
        'error':
            'No se pudo conectar al servidor. Verifica que el servidor Laravel esté corriendo en ${ApiConfig.baseUrl}',
      };
    } on TimeoutException catch (e) {
      print('❌ TimeoutException: ${e.message}');
      return {
        'success': false,
        'error':
            'Tiempo de espera agotado. Verifica que el servidor esté corriendo.',
      };
    } catch (e) {
      print('❌ Error: ${e.toString()}');
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Crear evento
  static Future<Map<String, dynamic>> crearEvento(
    Map<String, dynamic> eventoData, {
    List<XFile>? imagenes,
  }) async {
    try {
      final token = await StorageService.getToken();
      final headers = {'Accept': 'application/json'};

      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }

      // Si hay imágenes, usar multipart/form-data
      if (imagenes != null && imagenes.isNotEmpty) {
        final request = http.MultipartRequest(
          'POST',
          Uri.parse('${ApiConfig.baseUrl}/eventos'),
        );

        // Agregar headers
        request.headers.addAll(headers);

        // Preparar datos para enviar
        eventoData.forEach((key, value) {
          // CRÍTICO: Para arrays vacíos, NO enviarlos en absoluto
          if (value is List && value.isEmpty) {
            // No enviar arrays vacíos - Laravel los manejará como null/array vacío
            return; // Saltar este campo completamente
          }

          if (value != null) {
            if (value is List) {
              // Para arrays con elementos, enviar cada uno con índice
              // Laravel espera: patrocinadores[0], patrocinadores[1], etc.
              for (int i = 0; i < value.length; i++) {
                request.fields['$key[$i]'] = value[i].toString();
              }
            } else if (value is DateTime) {
              request.fields[key] = value.toIso8601String();
            } else if (value is bool) {
              // CRÍTICO: En multipart/form-data, Laravel espera "1" o "0" para booleanos
              request.fields[key] = value ? '1' : '0';
            } else if (value is int || value is double) {
              request.fields[key] = value.toString();
            } else {
              request.fields[key] = value.toString();
            }
          }
        });

        // Agregar imágenes
        for (var imagen in imagenes) {
          try {
            final bytes = await imagen.readAsBytes();
            final filename =
                imagen.name.isNotEmpty
                    ? imagen.name
                    : 'imagen_${DateTime.now().millisecondsSinceEpoch}.jpg';

            final multipartFile = http.MultipartFile.fromBytes(
              'imagenes[]',
              bytes,
              filename: filename,
            );
            request.files.add(multipartFile);
          } catch (e) {
            print('⚠️ Error al procesar imagen: $e');
          }
        }

        print('📤 Enviando evento con multipart/form-data');
        print(
          '📋 Campos finales que se enviarán: ${request.fields.keys.toList()}',
        );
        print('📋 Valores: ${request.fields}');
        print('🖼️ Imágenes: ${request.files.length}');

        final streamedResponse = await request.send();
        final response = await http.Response.fromStream(streamedResponse);

        print('📥 Respuesta: ${response.statusCode}');
        print('📄 Body: ${response.body}');

        final data = jsonDecode(response.body) as Map<String, dynamic>;

        if (response.statusCode == 201 && data['success'] == true) {
          return {
            'success': true,
            'message': data['message'] ?? 'Evento creado exitosamente',
            'evento': data['evento'],
          };
        }

        // Manejar errores de validación
        String errorMessage = 'Error al crear evento';
        if (data.containsKey('errors')) {
          final errors = data['errors'];
          if (errors is Map) {
            final errorList = <String>[];
            errors.forEach((key, value) {
              if (value is List) {
                errorList.addAll(value.cast<String>());
              } else {
                errorList.add(value.toString());
              }
            });
            errorMessage = errorList.join('\n');
          }
        } else if (data.containsKey('error')) {
          errorMessage = data['error'].toString();
        } else if (data.containsKey('message')) {
          errorMessage = data['message'].toString();
        }

        return {
          'success': false,
          'error': errorMessage,
          'errors': data['errors'],
        };
      } else {
        // Sin imágenes, usar JSON normal
        final response = await http.post(
          Uri.parse('${ApiConfig.baseUrl}/eventos'),
          headers: {...headers, 'Content-Type': 'application/json'},
          body: jsonEncode(eventoData),
        );

        print('📥 Respuesta JSON: ${response.statusCode}');
        print('📄 Body: ${response.body}');

        final data = jsonDecode(response.body) as Map<String, dynamic>;

        if (response.statusCode == 201 && data['success'] == true) {
          return {
            'success': true,
            'message': data['message'] ?? 'Evento creado exitosamente',
            'evento': data['evento'],
          };
        }

        // Manejar errores de validación
        String errorMessage = 'Error al crear evento';
        if (data.containsKey('errors')) {
          final errors = data['errors'];
          if (errors is Map) {
            final errorList = <String>[];
            errors.forEach((key, value) {
              if (value is List) {
                errorList.addAll(value.cast<String>());
              } else {
                errorList.add(value.toString());
              }
            });
            errorMessage = errorList.join('\n');
          }
        } else if (data.containsKey('error')) {
          errorMessage = data['error'].toString();
        } else if (data.containsKey('message')) {
          errorMessage = data['message'].toString();
        }

        return {
          'success': false,
          'error': errorMessage,
          'errors': data['errors'],
        };
      }
    } catch (e, stackTrace) {
      print('❌ Error en crearEvento: $e');
      print('📚 Stack trace: $stackTrace');
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Actualizar evento
  static Future<Map<String, dynamic>> actualizarEvento(
    int eventoId,
    Map<String, dynamic> eventoData, {
    List<XFile>? imagenes,
  }) async {
    try {
      final token = await StorageService.getToken();
      final headers = {'Accept': 'application/json'};

      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }

      // Si hay imágenes, usar multipart/form-data
      if (imagenes != null && imagenes.isNotEmpty) {
        final request = http.MultipartRequest(
          'PUT',
          Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId'),
        );

        request.headers.addAll(headers);

        // Preparar datos para enviar
        eventoData.forEach((key, value) {
          if (value is List && value.isEmpty) {
            return; // No enviar arrays vacíos
          }

          if (value != null) {
            if (value is List) {
              for (int i = 0; i < value.length; i++) {
                request.fields['$key[$i]'] = value[i].toString();
              }
            } else if (value is DateTime) {
              request.fields[key] = value.toIso8601String();
            } else if (value is bool) {
              request.fields[key] = value ? '1' : '0';
            } else if (value is int || value is double) {
              request.fields[key] = value.toString();
            } else {
              request.fields[key] = value.toString();
            }
          }
        });

        // Agregar imágenes
        for (var imagen in imagenes) {
          try {
            final bytes = await imagen.readAsBytes();
            final filename =
                imagen.name.isNotEmpty
                    ? imagen.name
                    : 'imagen_${DateTime.now().millisecondsSinceEpoch}.jpg';

            final multipartFile = http.MultipartFile.fromBytes(
              'imagenes[]',
              bytes,
              filename: filename,
            );
            request.files.add(multipartFile);
          } catch (e) {
            print('⚠️ Error al procesar imagen: $e');
          }
        }

        final streamedResponse = await request.send();
        final response = await http.Response.fromStream(streamedResponse);

        final data = jsonDecode(response.body) as Map<String, dynamic>;

        if (response.statusCode == 200 && data['success'] == true) {
          return {
            'success': true,
            'message': data['message'] ?? 'Evento actualizado exitosamente',
            'evento': data['evento'],
          };
        }

        String errorMessage = 'Error al actualizar evento';
        if (data.containsKey('errors')) {
          final errors = data['errors'];
          if (errors is Map) {
            final errorList = <String>[];
            errors.forEach((key, value) {
              if (value is List) {
                errorList.addAll(value.cast<String>());
              } else {
                errorList.add(value.toString());
              }
            });
            errorMessage = errorList.join('\n');
          }
        } else if (data.containsKey('error')) {
          errorMessage = data['error'].toString();
        } else if (data.containsKey('message')) {
          errorMessage = data['message'].toString();
        }

        return {
          'success': false,
          'error': errorMessage,
          'errors': data['errors'],
        };
      } else {
        // Sin imágenes, usar JSON normal
        final response = await http.put(
          Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId'),
          headers: {...headers, 'Content-Type': 'application/json'},
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
      }
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

  // ========== EMPRESAS E INVITADOS ==========

  // Obtener empresas disponibles para patrocinar
  static Future<Map<String, dynamic>> getEmpresasDisponibles() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/empresas/disponibles'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'empresas': data['empresas'] as List};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener empresas',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener invitados disponibles
  static Future<Map<String, dynamic>> getInvitadosDisponibles() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/invitados'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'invitados': data['invitados'] as List};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener invitados',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== NOTIFICACIONES ==========

  // Listar notificaciones
  static Future<Map<String, dynamic>> getNotificaciones() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/notificaciones'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final notificacionesList =
            (data['notificaciones'] as List)
                .map((n) => Notificacion.fromJson(n as Map<String, dynamic>))
                .toList();
        return {
          'success': true,
          'notificaciones': notificacionesList,
          'no_leidas': data['no_leidas'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener notificaciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener contador de notificaciones no leídas
  static Future<Map<String, dynamic>> getContadorNotificaciones() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/notificaciones/contador'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'no_leidas': data['no_leidas'] as int? ?? 0};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener contador',
        'no_leidas': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'no_leidas': 0,
      };
    }
  }

  // Marcar notificación como leída
  static Future<Map<String, dynamic>> marcarNotificacionLeida(int id) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/notificaciones/$id/leida'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Notificación marcada como leída',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al marcar notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Marcar todas las notificaciones como leídas
  static Future<Map<String, dynamic>> marcarTodasLeidas() async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/notificaciones/marcar-todas'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Todas las notificaciones marcadas como leídas',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al marcar notificaciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== NOTIFICACIONES (EMPRESA) ==========

  // Listar notificaciones de empresa
  static Future<Map<String, dynamic>> getNotificacionesEmpresa() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/empresas/notificaciones'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final notificacionesList =
            (data['notificaciones'] as List)
                .map((n) => Notificacion.fromJson(n as Map<String, dynamic>))
                .toList();
        return {
          'success': true,
          'notificaciones': notificacionesList,
          'no_leidas': data['no_leidas'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener notificaciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener contador de notificaciones no leídas (empresa)
  static Future<Map<String, dynamic>> getContadorNotificacionesEmpresa() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/empresas/notificaciones/contador'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'no_leidas': data['no_leidas'] as int? ?? 0};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener contador',
        'no_leidas': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'no_leidas': 0,
      };
    }
  }

  // Marcar notificación como leída (empresa)
  static Future<Map<String, dynamic>> marcarNotificacionLeidaEmpresa(
    int id,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/empresas/notificaciones/$id/leida'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Notificación marcada como leída',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al marcar notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Marcar todas las notificaciones como leídas (empresa)
  static Future<Map<String, dynamic>> marcarTodasLeidasEmpresa() async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/empresas/notificaciones/marcar-todas'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Todas las notificaciones marcadas como leídas',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al marcar notificaciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== DASHBOARD ONG ==========

  // Obtener estadísticas generales
  static Future<Map<String, dynamic>> getEstadisticasGeneralesOng() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/dashboard-ong/estadisticas-generales'),
        headers: await _getHeaders(includeAuth: true),
      );

      // Verificar si la respuesta es HTML (error de Laravel)
      final contentType = response.headers['content-type'] ?? '';
      if (contentType.contains('text/html')) {
        print('❌ Error: El servidor devolvió HTML en lugar de JSON');
        print(
          '📄 Respuesta: ${response.body.substring(0, 500)}...',
        ); // Primeros 500 caracteres
        return {
          'success': false,
          'error':
              'Error del servidor. Verifica que estés autenticado como ONG.',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'ong': data['ong'],
          'estadisticas': data['estadisticas'],
          'distribuciones': data['distribuciones'],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener estadísticas',
      };
    } catch (e) {
      print('❌ Error en getEstadisticasGeneralesOng: $e');
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener estadísticas de participantes
  static Future<Map<String, dynamic>> getEstadisticasParticipantes() async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/dashboard-ong/participantes/estadisticas',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'estadisticas_por_evento': data['estadisticas_por_evento'],
          'totales': data['totales'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ?? 'Error al obtener estadísticas de participantes',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener lista de participantes
  static Future<Map<String, dynamic>> getListaParticipantes({
    int? eventoId,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/dashboard-ong/participantes/lista';
      if (eventoId != null) {
        url += '?evento_id=$eventoId';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'participantes': data['participantes'] as List,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener participantes',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener estadísticas de reacciones
  static Future<Map<String, dynamic>> getEstadisticasReacciones() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/dashboard-ong/reacciones/estadisticas'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'estadisticas_por_evento': data['estadisticas_por_evento'],
          'total_reacciones': data['total_reacciones'],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener estadísticas de reacciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener lista de reacciones
  static Future<Map<String, dynamic>> getListaReacciones({
    int? eventoId,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/dashboard-ong/reacciones/lista';
      if (eventoId != null) {
        url += '?evento_id=$eventoId';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'reacciones': data['reacciones'] as List};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener reacciones',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== DASHBOARD EVENTOS POR ESTADO ==========

  // Obtener dashboard de eventos por estado
  static Future<Map<String, dynamic>> getDashboardEventosPorEstado(
    int ongId, {
    String? estado,
    String? buscar,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/eventos/ong/$ongId/dashboard';
      final queryParams = <String, String>{};

      if (estado != null && estado.isNotEmpty && estado != 'todos') {
        queryParams['estado'] = estado;
      }
      if (buscar != null && buscar.isNotEmpty) {
        queryParams['buscar'] = buscar;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final eventosList =
            (data['eventos'] as List)
                .map((e) => Evento.fromJson(e as Map<String, dynamic>))
                .toList();
        return {
          'success': true,
          'eventos': eventosList,
          'estadisticas': data['estadisticas'] as Map?,
          'filtro_estado': data['filtro_estado'] as String?,
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener dashboard de eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== DASHBOARD EXTERNO ==========

  // Obtener estadísticas generales del dashboard externo
  static Future<Map<String, dynamic>> getEstadisticasGeneralesExterno() async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/dashboard-externo/estadisticas-generales',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'usuario': data['usuario'] as Map<String, dynamic>?,
          'estadisticas': data['estadisticas'] as Map<String, dynamic>?,
          'graficas': data['graficas'] as Map<String, dynamic>?,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener estadísticas',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener datos detallados del dashboard externo
  static Future<Map<String, dynamic>> getDatosDetalladosExterno() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/dashboard-externo/datos-detallados'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'eventos_inscritos':
              data['eventos_inscritos'] as List<dynamic>? ?? [],
          'eventos_asistidos':
              data['eventos_asistidos'] as List<dynamic>? ?? [],
          'reacciones': data['reacciones'] as List<dynamic>? ?? [],
          'compartidos': data['compartidos'] as List<dynamic>? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener datos detallados',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener eventos disponibles para participar
  static Future<Map<String, dynamic>> getEventosDisponiblesExterno() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/dashboard-externo/eventos-disponibles'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'eventos': data['eventos'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos disponibles',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Descargar PDF completo del dashboard externo
  static Future<Map<String, dynamic>> descargarPdfCompletoExterno() async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/dashboard-externo/descargar-pdf-completo',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      // Si es PDF, retornar los bytes
      if (response.headers['content-type']?.contains('application/pdf') ==
          true) {
        return {
          'success': true,
          'pdf_bytes': response.bodyBytes,
          'content_type': 'application/pdf',
        };
      }

      // Si es JSON (datos para generar PDF en la app)
      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'data': data['data'] as Map<String, dynamic>?,
          'message': data['message'] as String?,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al generar PDF',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== PERFIL ==========

  // Obtener perfil completo
  static Future<Map<String, dynamic>> getPerfil() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/perfil'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'data': data['data'] as Map<String, dynamic>};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener perfil',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Actualizar perfil
  static Future<Map<String, dynamic>> actualizarPerfil(
    Map<String, dynamic> perfilData, {
    XFile? fotoPerfil,
  }) async {
    try {
      final token = await StorageService.getToken();
      final headers = {'Accept': 'application/json'};

      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }

      // Si hay foto de perfil, usar multipart/form-data
      if (fotoPerfil != null) {
        final request = http.MultipartRequest(
          'PUT',
          Uri.parse('${ApiConfig.baseUrl}/perfil'),
        );

        request.headers.addAll(headers);

        // Agregar campos del formulario
        perfilData.forEach((key, value) {
          if (value != null) {
            if (value is DateTime) {
              request.fields[key] = value.toIso8601String();
            } else if (value is bool) {
              request.fields[key] = value ? '1' : '0';
            } else {
              request.fields[key] = value.toString();
            }
          }
        });

        // Agregar foto de perfil
        try {
          final bytes = await fotoPerfil.readAsBytes();
          final filename =
              fotoPerfil.name.isNotEmpty
                  ? fotoPerfil.name
                  : 'foto_perfil_${DateTime.now().millisecondsSinceEpoch}.jpg';

          final multipartFile = http.MultipartFile.fromBytes(
            'foto_perfil',
            bytes,
            filename: filename,
          );
          request.files.add(multipartFile);
        } catch (e) {
          print('⚠️ Error al procesar foto de perfil: $e');
        }

        final streamedResponse = await request.send();
        final response = await http.Response.fromStream(streamedResponse);

        final data = jsonDecode(response.body) as Map<String, dynamic>;

        if (response.statusCode == 200 && data['success'] == true) {
          return {
            'success': true,
            'message': data['message'] ?? 'Perfil actualizado exitosamente',
            'foto_perfil': data['foto_perfil'],
          };
        }

        String errorMessage = 'Error al actualizar perfil';
        if (data.containsKey('errors')) {
          final errors = data['errors'];
          if (errors is Map) {
            final errorList = <String>[];
            errors.forEach((key, value) {
              if (value is List) {
                errorList.addAll(value.cast<String>());
              } else {
                errorList.add(value.toString());
              }
            });
            errorMessage = errorList.join('\n');
          }
        } else if (data.containsKey('error')) {
          errorMessage = data['error'].toString();
        }

        return {
          'success': false,
          'error': errorMessage,
          'errors': data['errors'],
        };
      } else {
        // Sin foto, usar JSON normal
        final response = await http.put(
          Uri.parse('${ApiConfig.baseUrl}/perfil'),
          headers: {...headers, 'Content-Type': 'application/json'},
          body: jsonEncode(perfilData),
        );

        final data = jsonDecode(response.body) as Map<String, dynamic>;

        if (response.statusCode == 200 && data['success'] == true) {
          return {
            'success': true,
            'message': data['message'] ?? 'Perfil actualizado exitosamente',
            'foto_perfil': data['foto_perfil'],
          };
        }

        String errorMessage = 'Error al actualizar perfil';
        if (data.containsKey('errors')) {
          final errors = data['errors'];
          if (errors is Map) {
            final errorList = <String>[];
            errors.forEach((key, value) {
              if (value is List) {
                errorList.addAll(value.cast<String>());
              } else {
                errorList.add(value.toString());
              }
            });
            errorMessage = errorList.join('\n');
          }
        } else if (data.containsKey('error')) {
          errorMessage = data['error'].toString();
        }

        return {
          'success': false,
          'error': errorMessage,
          'errors': data['errors'],
        };
      }
    } catch (e, stackTrace) {
      print('❌ Error en actualizarPerfil: $e');
      print('📚 Stack trace: $stackTrace');
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== REACCIONES ==========

  // Toggle reacción (agregar/quitar)
  static Future<Map<String, dynamic>> toggleReaccion(int eventoId) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/reacciones/toggle'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'evento_id': eventoId}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'reaccionado': data['reaccionado'] as bool? ?? false,
          'total_reacciones': data['total_reacciones'] as int? ?? 0,
          'message': data['message'] as String?,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al procesar reacción',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Verificar si el usuario reaccionó al evento
  static Future<Map<String, dynamic>> verificarReaccion(int eventoId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/reacciones/verificar/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'reaccionado': data['reaccionado'] as bool? ?? false,
          'total_reacciones': data['total_reacciones'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al verificar reacción',
        'reaccionado': false,
        'total_reacciones': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'reaccionado': false,
        'total_reacciones': 0,
      };
    }
  }

  // Obtener lista de usuarios que reaccionaron (para ONG)
  static Future<Map<String, dynamic>> getUsuariosQueReaccionaron(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/reacciones/evento/$eventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'reacciones': data['reacciones'] as List? ?? [],
          'total': data['total'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener reacciones',
        'reacciones': [],
        'total': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'reacciones': [],
        'total': 0,
      };
    }
  }

  // ========== DASHBOARD DE EVENTO INDIVIDUAL ==========

  // Obtener dashboard detallado de un evento
  static Future<Map<String, dynamic>> getDashboardEvento(
    int eventoId, {
    Map<String, dynamic>? params,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/eventos/$eventoId/dashboard';

      if (params != null && params.isNotEmpty) {
        final queryParams = <String, String>{};
        params.forEach((key, value) {
          if (value != null) {
            queryParams[key] = value.toString();
          }
        });

        if (queryParams.isNotEmpty) {
          url += '?${Uri(queryParameters: queryParams).query}';
        }
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          ...data, // Incluye todos los datos del dashboard
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener dashboard del evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Descargar dashboard de evento en PDF
  static Future<Map<String, dynamic>> descargarDashboardEventoPdf(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/dashboard/pdf'),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        // Si la respuesta es un PDF, retornar los bytes
        return {
          'success': true,
          'pdfBytes': response.bodyBytes,
          'contentType': response.headers['content-type'] ?? 'application/pdf',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al descargar PDF',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Descargar dashboard de evento en Excel (CSV)
  static Future<Map<String, dynamic>> descargarDashboardEventoExcel(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/dashboard/excel'),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        // Si la respuesta es un CSV, retornar los bytes
        return {
          'success': true,
          'csvBytes': response.bodyBytes,
          'contentType': response.headers['content-type'] ?? 'text/csv',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al descargar Excel',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== DASHBOARD ONG COMPLETO ==========

  /// Obtener dashboard completo de ONG
  /// Endpoint: /api/ong/dashboard
  /// Incluye todas las métricas, gráficos, top eventos, top voluntarios, etc.
  static Future<Map<String, dynamic>> getDashboardOngCompleto({
    String? fechaInicio,
    String? fechaFin,
    String? estadoEvento,
    String? tipoParticipacion,
    String? busquedaEvento,
    bool useCache = true,
  }) async {
    try {
      // Verificar cache primero
      if (useCache) {
        final cacheKey =
            'ong_dashboard_${fechaInicio}_${fechaFin}_${estadoEvento}_${tipoParticipacion}_${busquedaEvento}';
        final cachedData = await CacheService.getCachedData(cacheKey);
        if (cachedData != null) {
          print('✅ Datos obtenidos del cache');
          return cachedData;
        }
      }

      String url = '${ApiConfig.baseUrl}/ong/dashboard';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }
      if (estadoEvento != null && estadoEvento.isNotEmpty) {
        queryParams['estado_evento'] = estadoEvento;
      }
      if (tipoParticipacion != null && tipoParticipacion.isNotEmpty) {
        queryParams['tipo_participacion'] = tipoParticipacion;
      }
      if (busquedaEvento != null && busquedaEvento.isNotEmpty) {
        queryParams['busqueda_evento'] = busquedaEvento;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = _parseJsonSafely(response.body, 'getDashboardOngCompleto');

      if (response.statusCode == 200 && data['success'] == true) {
        final result = {'success': true, 'data': data['data']};

        // Guardar en cache
        if (useCache) {
          final cacheKey =
              'ong_dashboard_${fechaInicio}_${fechaFin}_${estadoEvento}_${tipoParticipacion}_${busquedaEvento}';
          await CacheService.setCachedData(cacheKey, result);
        }

        return result;
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener dashboard',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Obtener dashboard completo de evento individual
  /// Endpoint: /api/eventos/{id}/dashboard-completo
  static Future<Map<String, dynamic>> getDashboardEventoCompleto(
    int eventoId, {
    String? fechaInicio,
    String? fechaFin,
    bool useCache = true,
  }) async {
    try {
      // Verificar cache primero
      if (useCache) {
        final cacheKey =
            'evento_dashboard_${eventoId}_${fechaInicio}_${fechaFin}';
        final cachedData = await CacheService.getCachedData(cacheKey);
        if (cachedData != null) {
          print('✅ Datos obtenidos del cache');
          return cachedData;
        }
      }

      String url = '${ApiConfig.baseUrl}/eventos/$eventoId/dashboard-completo';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = _parseJsonSafely(
        response.body,
        'getDashboardEventoCompleto',
      );

      if (response.statusCode == 200 && data['success'] == true) {
        final result = {'success': true, ...data};

        // Guardar en cache
        if (useCache) {
          final cacheKey =
              'evento_dashboard_${eventoId}_${fechaInicio}_${fechaFin}';
          await CacheService.setCachedData(cacheKey, result);
        }

        return result;
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener dashboard del evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Exportar dashboard ONG en PDF
  /// Endpoint: /api/ong/dashboard/export-pdf
  static Future<Map<String, dynamic>> exportarDashboardOngPdf({
    String? fechaInicio,
    String? fechaFin,
    String? estadoEvento,
    String? tipoParticipacion,
    String? busquedaEvento,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/ong/dashboard/export-pdf';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }
      if (estadoEvento != null && estadoEvento.isNotEmpty) {
        queryParams['estado_evento'] = estadoEvento;
      }
      if (tipoParticipacion != null && tipoParticipacion.isNotEmpty) {
        queryParams['tipo_participacion'] = tipoParticipacion;
      }
      if (busquedaEvento != null && busquedaEvento.isNotEmpty) {
        queryParams['busqueda_evento'] = busquedaEvento;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        return {
          'success': true,
          'pdfBytes': response.bodyBytes,
          'contentType': response.headers['content-type'] ?? 'application/pdf',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al exportar PDF',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Exportar dashboard ONG en Excel
  /// Endpoint: /api/ong/dashboard/export-excel
  static Future<Map<String, dynamic>> exportarDashboardOngExcel({
    String? fechaInicio,
    String? fechaFin,
    String? estadoEvento,
    String? tipoParticipacion,
    String? busquedaEvento,
  }) async {
    try {
      String url = '${ApiConfig.baseUrl}/ong/dashboard/export-excel';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }
      if (estadoEvento != null && estadoEvento.isNotEmpty) {
        queryParams['estado_evento'] = estadoEvento;
      }
      if (tipoParticipacion != null && tipoParticipacion.isNotEmpty) {
        queryParams['tipo_participacion'] = tipoParticipacion;
      }
      if (busquedaEvento != null && busquedaEvento.isNotEmpty) {
        queryParams['busqueda_evento'] = busquedaEvento;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        return {
          'success': true,
          'excelBytes': response.bodyBytes,
          'contentType':
              response.headers['content-type'] ??
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al exportar Excel',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Exportar dashboard evento en PDF (endpoint completo)
  /// Endpoint: /api/eventos/{id}/dashboard-completo/pdf
  static Future<Map<String, dynamic>> exportarDashboardEventoPdfCompleto(
    int eventoId, {
    String? fechaInicio,
    String? fechaFin,
  }) async {
    try {
      String url =
          '${ApiConfig.baseUrl}/eventos/$eventoId/dashboard-completo/pdf';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        return {
          'success': true,
          'pdfBytes': response.bodyBytes,
          'contentType': response.headers['content-type'] ?? 'application/pdf',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al exportar PDF',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Exportar dashboard evento en Excel (endpoint completo)
  /// Endpoint: /api/eventos/{id}/dashboard-completo/excel
  static Future<Map<String, dynamic>> exportarDashboardEventoExcelCompleto(
    int eventoId, {
    String? fechaInicio,
    String? fechaFin,
  }) async {
    try {
      String url =
          '${ApiConfig.baseUrl}/eventos/$eventoId/dashboard-completo/excel';
      final queryParams = <String, String>{};

      if (fechaInicio != null && fechaInicio.isNotEmpty) {
        queryParams['fecha_inicio'] = fechaInicio;
      }
      if (fechaFin != null && fechaFin.isNotEmpty) {
        queryParams['fecha_fin'] = fechaFin;
      }

      if (queryParams.isNotEmpty) {
        url += '?${Uri(queryParameters: queryParams).query}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        return {
          'success': true,
          'excelBytes': response.bodyBytes,
          'contentType':
              response.headers['content-type'] ??
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al exportar Excel',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== PARTICIPACIONES NO REGISTRADAS ==========

  // Aprobar participación no registrada
  static Future<Map<String, dynamic>> aprobarParticipacionNoRegistrada(
    int participacionId,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones-no-registradas/$participacionId/aprobar',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Participación no registrada aprobada exitosamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al aprobar participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Rechazar participación no registrada
  static Future<Map<String, dynamic>> rechazarParticipacionNoRegistrada(
    int participacionId,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones-no-registradas/$participacionId/rechazar',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Participación no registrada rechazada exitosamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al rechazar participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Marcar / desmarcar asistencia (participante NO registrado)
  static Future<Map<String, dynamic>> marcarAsistenciaParticipacionNoRegistrada(
    int participacionId, {
    required bool asistio,
  }) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/participaciones-no-registradas/$participacionId/asistencia',
        ),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'asistio': asistio}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Asistencia actualizada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al actualizar asistencia',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== EMPRESAS PARTICIPANTES ==========

  // Asignar empresas a un evento (ONG)
  static Future<Map<String, dynamic>> asignarEmpresasAEvento(
    int eventoId,
    List<int> empresasIds,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/empresas/asignar'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'empresas': empresasIds}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] as String? ?? 'Empresas asignadas correctamente',
          'empresas_asignadas': data['empresas_asignadas'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al asignar empresas',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Remover empresas de un evento (ONG)
  static Future<Map<String, dynamic>> removerEmpresasDeEvento(
    int eventoId,
    List<int> empresasIds,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/empresas/remover'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'empresas': empresasIds}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] as String? ?? 'Empresas removidas correctamente',
          'empresas_removidas': data['empresas_removidas'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al remover empresas',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener empresas participantes de un evento
  static Future<Map<String, dynamic>> getEmpresasParticipantes(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/empresas'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'empresas': data['empresas'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener empresas participantes',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Verificar participación de empresa en un evento
  static Future<Map<String, dynamic>> verificarParticipacionEmpresa(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/empresas/verificar'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'participando': data['participando'] as bool? ?? false,
          'participacion': data['participacion'] as Map<String, dynamic>?,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al verificar participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Confirmar participación de empresa (Empresa)
  static Future<Map<String, dynamic>> confirmarParticipacionEmpresa(
    int eventoId, {
    String? tipoColaboracion,
    String? descripcionColaboracion,
  }) async {
    try {
      final body = <String, dynamic>{};
      if (tipoColaboracion != null && tipoColaboracion.isNotEmpty) {
        body['tipo_colaboracion'] = tipoColaboracion;
      }
      if (descripcionColaboracion != null &&
          descripcionColaboracion.isNotEmpty) {
        body['descripcion_colaboracion'] = descripcionColaboracion;
      }

      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/empresas/eventos/$eventoId/confirmar'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] as String? ??
              'Participación confirmada correctamente',
          'participacion': data['participacion'] as Map<String, dynamic>?,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al confirmar participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener mis eventos como empresa colaboradora
  static Future<Map<String, dynamic>> getMisEventosEmpresa() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/empresas/mis-eventos'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'eventos': data['eventos'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
          'colaboradores': data['colaboradores'] as int? ?? 0,
          'patrocinadores': data['patrocinadores'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== COMPARTIR EVENTOS ==========

  // Compartir evento (usuario autenticado)
  static Future<Map<String, dynamic>> compartirEvento(
    int eventoId, {
    String metodo = 'link',
  }) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/compartir'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'metodo': metodo}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento compartido exitosamente',
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al compartir evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Compartir evento público (sin autenticación)
  static Future<Map<String, dynamic>> compartirEventoPublico(
    int eventoId, {
    String metodo = 'link',
    String? nombres,
    String? apellidos,
    String? email,
  }) async {
    try {
      final body = <String, dynamic>{'metodo': metodo};
      if (nombres != null && nombres.isNotEmpty) {
        body['nombres'] = nombres;
      }
      if (apellidos != null && apellidos.isNotEmpty) {
        body['apellidos'] = apellidos;
      }
      if (email != null && email.isNotEmpty) {
        body['email'] = email;
      }

      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/compartir-publico'),
        headers: await _getHeaders(includeAuth: false),
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Evento compartido exitosamente',
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al compartir evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener total de compartidos de un evento
  static Future<Map<String, dynamic>> getTotalCompartidosEvento(
    int eventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/$eventoId/compartidos/total'),
        headers: await _getHeaders(includeAuth: false),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener total de compartidos',
        'total_compartidos': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'total_compartidos': 0,
      };
    }
  }

  // ========== MEGA EVENTOS ==========

  // Listar mega eventos (ONG autenticada)
  static Future<Map<String, dynamic>> getMegaEventos({
    String? categoria,
    String? estado,
    String? buscar,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (categoria != null && categoria.isNotEmpty && categoria != 'todos') {
        queryParams['categoria'] = categoria;
      }
      if (estado != null && estado.isNotEmpty && estado != 'todos') {
        queryParams['estado'] = estado;
      }
      if (buscar != null && buscar.isNotEmpty) {
        queryParams['buscar'] = buscar;
      }

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/mega-eventos',
      ).replace(queryParameters: queryParams);

      final response = await http.get(
        uri,
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'mega_eventos': data['mega_eventos'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener mega eventos',
        'mega_eventos': [],
        'count': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'mega_eventos': [],
        'count': 0,
      };
    }
  }

  // Listar mega eventos públicos
  static Future<Map<String, dynamic>> getMegaEventosPublicos({
    String? categoria,
    String? buscar,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (categoria != null && categoria.isNotEmpty && categoria != 'todos') {
        queryParams['categoria'] = categoria;
      }
      if (buscar != null && buscar.isNotEmpty) {
        queryParams['buscar'] = buscar;
      }

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/mega-eventos/publicos',
      ).replace(queryParameters: queryParams);

      print('🔍 Obteniendo mega eventos públicos desde: $uri');

      final response = await http
          .get(uri, headers: await _getHeaders(includeAuth: false))
          .timeout(
            const Duration(seconds: 30),
            onTimeout: () {
              throw Exception(
                'Tiempo de espera agotado al obtener mega eventos públicos',
              );
            },
          );

      print('📡 Respuesta recibida: ${response.statusCode}');
      print('📥 Body: ${response.body}');

      // Si la respuesta no es exitosa, intentar parsear el error
      if (response.statusCode != 200) {
        try {
          final errorData = jsonDecode(response.body) as Map<String, dynamic>;
          return {
            'success': false,
            'error':
                errorData['error'] ??
                errorData['message'] ??
                'Error al obtener mega eventos públicos (${response.statusCode})',
            'mega_eventos': [],
            'count': 0,
          };
        } catch (e) {
          return {
            'success': false,
            'error':
                'Error del servidor (${response.statusCode}): ${response.body}',
            'mega_eventos': [],
            'count': 0,
          };
        }
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (data['success'] == true) {
        return {
          'success': true,
          'mega_eventos': data['mega_eventos'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener mega eventos públicos',
        'mega_eventos': [],
        'count': 0,
      };
    } catch (e) {
      print('❌ Error en getMegaEventosPublicos: $e');
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'mega_eventos': [],
        'count': 0,
      };
    }
  }

  // Obtener detalle de mega evento
  static Future<Map<String, dynamic>> getMegaEventoDetalle(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'mega_evento': data['mega_evento']};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener detalle del mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Crear mega evento
  static Future<Map<String, dynamic>> crearMegaEvento({
    required String titulo,
    String? descripcion,
    required DateTime fechaInicio,
    required DateTime fechaFin,
    String? ubicacion,
    double? lat,
    double? lng,
    String? categoria,
    String? estado,
    required int ongOrganizadoraPrincipal,
    int? capacidadMaxima,
    bool? esPublico,
    bool? activo,
    List<String>? imagenesUrls,
    List<http.MultipartFile>? imagenesFiles,
  }) async {
    try {
      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos'),
      );

      final headers = await _getHeaders(includeAuth: true);
      request.headers.addAll({
        'Authorization': headers['Authorization'] ?? '',
        'Accept': 'application/json',
      });

      request.fields['titulo'] = titulo;
      if (descripcion != null) request.fields['descripcion'] = descripcion;
      request.fields['fecha_inicio'] = fechaInicio.toIso8601String();
      request.fields['fecha_fin'] = fechaFin.toIso8601String();
      if (ubicacion != null) request.fields['ubicacion'] = ubicacion;
      if (lat != null) request.fields['lat'] = lat.toString();
      if (lng != null) request.fields['lng'] = lng.toString();
      if (categoria != null) request.fields['categoria'] = categoria;
      if (estado != null) request.fields['estado'] = estado;
      request.fields['ong_organizadora_principal'] =
          ongOrganizadoraPrincipal.toString();
      if (capacidadMaxima != null) {
        request.fields['capacidad_maxima'] = capacidadMaxima.toString();
      }
      // Laravel espera "1" o "0" para booleanos en formularios multipart
      // Siempre enviar es_publico (si es null, usar true por defecto)
      request.fields['es_publico'] = (esPublico ?? true) ? '1' : '0';
      // Siempre enviar activo (si es null, usar true por defecto)
      request.fields['activo'] = (activo ?? true) ? '1' : '0';

      // Agregar imágenes como archivos
      if (imagenesFiles != null) {
        for (var file in imagenesFiles) {
          request.files.add(file);
        }
      }

      // Agregar imágenes como URLs
      if (imagenesUrls != null && imagenesUrls.isNotEmpty) {
        request.fields['imagenes_urls'] = jsonEncode(imagenesUrls);
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Mega evento creado correctamente',
          'mega_evento': data['mega_evento'],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al crear mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Actualizar mega evento
  static Future<Map<String, dynamic>> actualizarMegaEvento({
    required int id,
    String? titulo,
    String? descripcion,
    DateTime? fechaInicio,
    DateTime? fechaFin,
    String? ubicacion,
    double? lat,
    double? lng,
    String? categoria,
    String? estado,
    int? capacidadMaxima,
    bool? esPublico,
    bool? activo,
    List<String>? imagenesUrls,
    List<String>? imagenesJson,
    List<http.MultipartFile>? imagenesFiles,
  }) async {
    try {
      final request = http.MultipartRequest(
        'PUT',
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id'),
      );

      final headers = await _getHeaders(includeAuth: true);
      request.headers.addAll({
        'Authorization': headers['Authorization'] ?? '',
        'Accept': 'application/json',
      });

      if (titulo != null) request.fields['titulo'] = titulo;
      if (descripcion != null) request.fields['descripcion'] = descripcion;
      if (fechaInicio != null) {
        request.fields['fecha_inicio'] = fechaInicio.toIso8601String();
      }
      if (fechaFin != null) {
        request.fields['fecha_fin'] = fechaFin.toIso8601String();
      }
      if (ubicacion != null) request.fields['ubicacion'] = ubicacion;
      if (lat != null) request.fields['lat'] = lat.toString();
      if (lng != null) request.fields['lng'] = lng.toString();
      if (categoria != null) request.fields['categoria'] = categoria;
      if (estado != null) request.fields['estado'] = estado;
      if (capacidadMaxima != null) {
        request.fields['capacidad_maxima'] = capacidadMaxima.toString();
      }
      // Laravel espera "1" o "0" para booleanos en formularios multipart
      // En actualizar, solo enviar si tiene valor
      if (esPublico != null) {
        request.fields['es_publico'] = esPublico ? '1' : '0';
      }
      if (activo != null) {
        request.fields['activo'] = activo ? '1' : '0';
      }

      // Agregar imágenes como archivos
      if (imagenesFiles != null) {
        for (var file in imagenesFiles) {
          request.files.add(file);
        }
      }

      // Agregar imágenes como URLs
      if (imagenesUrls != null && imagenesUrls.isNotEmpty) {
        request.fields['imagenes_urls'] = jsonEncode(imagenesUrls);
      }

      // Agregar imágenes existentes (para mantenerlas al editar)
      if (imagenesJson != null && imagenesJson.isNotEmpty) {
        request.fields['imagenes_json'] = jsonEncode(imagenesJson);
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Mega evento actualizado correctamente',
          'mega_evento': data['mega_evento'],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al actualizar mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Eliminar mega evento
  static Future<Map<String, dynamic>> eliminarMegaEvento(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Mega evento eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Eliminar imagen de mega evento
  static Future<Map<String, dynamic>> eliminarImagenMegaEvento(
    int id,
    String imagenUrl,
  ) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/imagen'),
        headers: {
          ...await _getHeaders(includeAuth: true),
          'Content-Type': 'application/json',
        },
        body: jsonEncode({'imagen_url': imagenUrl}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Imagen eliminada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar imagen',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Participar en mega evento
  static Future<Map<String, dynamic>> participarMegaEvento(int id) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/participar'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Participación registrada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al participar en mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Verificar participación en mega evento
  static Future<Map<String, dynamic>> verificarParticipacionMegaEvento(
    int id,
  ) async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/mega-eventos/$id/verificar-participacion',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'participa': data['participa'] == true};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al verificar participación',
        'participa': false,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'participa': false,
      };
    }
  }

  // Mis participaciones en mega eventos
  static Future<Map<String, dynamic>> misParticipacionesMegaEventos() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/mis-participaciones'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'mega_eventos': data['mega_eventos'] as List<dynamic>? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener participaciones',
        'mega_eventos': [],
        'count': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'mega_eventos': [],
        'count': 0,
      };
    }
  }

  // ========== REACCIONES MEGA EVENTOS ==========

  // Toggle reacción a mega evento
  static Future<Map<String, dynamic>> toggleReaccionMegaEvento(
    int megaEventoId,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/reacciones/toggle'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'mega_evento_id': megaEventoId}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'reaccionado': data['reaccionado'] == true,
          'message': data['message'] ?? '',
          'total_reacciones': data['total_reacciones'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al reaccionar',
        'reaccionado': false,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'reaccionado': false,
      };
    }
  }

  // Verificar reacción a mega evento
  static Future<Map<String, dynamic>> verificarReaccionMegaEvento(
    int megaEventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/mega-eventos/reacciones/verificar/$megaEventoId',
        ),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'reaccionado': data['reaccionado'] == true,
          'total_reacciones': data['total_reacciones'] as int? ?? 0,
        };
      }

      // Si falla la verificación, aún intentar obtener el total
      final totalResult = await getTotalReaccionesMegaEvento(megaEventoId);
      return {
        'success': false,
        'error': data['error'] ?? 'Error al verificar reacción',
        'reaccionado': false,
        'total_reacciones': totalResult['total_reacciones'] as int? ?? 0,
      };
    } catch (e) {
      // Si hay error, intentar obtener el total de todas formas
      try {
        final totalResult = await getTotalReaccionesMegaEvento(megaEventoId);
        return {
          'success': false,
          'error': 'Error de conexión: ${e.toString()}',
          'reaccionado': false,
          'total_reacciones': totalResult['total_reacciones'] as int? ?? 0,
        };
      } catch (_) {
        return {
          'success': false,
          'error': 'Error de conexión: ${e.toString()}',
          'reaccionado': false,
          'total_reacciones': 0,
        };
      }
    }
  }

  // Usuarios que reaccionaron a mega evento
  static Future<Map<String, dynamic>> usuariosQueReaccionaronMegaEvento(
    int megaEventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/reacciones/$megaEventoId'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'usuarios': data['usuarios'] as List<dynamic>? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener usuarios',
        'usuarios': [],
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'usuarios': [],
      };
    }
  }

  // Total reacciones de mega evento (público)
  static Future<Map<String, dynamic>> getTotalReaccionesMegaEvento(
    int megaEventoId,
  ) async {
    try {
      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/reacciones/mega-evento/$megaEventoId/total',
      );

      print('🔍 Obteniendo total de reacciones desde: $uri');

      final response = await http.get(
        uri,
        headers: await _getHeaders(includeAuth: false),
      );

      print('📡 Respuesta recibida: ${response.statusCode}');
      print('📥 Body: ${response.body}');

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final total = data['total_reacciones'] as int? ?? 0;
        print('✅ Total de reacciones obtenido: $total');
        return {'success': true, 'total_reacciones': total};
      }

      print('❌ Error en respuesta: ${data['error']}');
      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener total de reacciones',
        'total_reacciones': 0,
      };
    } catch (e) {
      print('❌ Excepción al obtener total de reacciones: $e');
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'total_reacciones': 0,
      };
    }
  }

  // Reaccionar a mega evento (público)
  static Future<Map<String, dynamic>> reaccionarMegaEventoPublico(
    int megaEventoId, {
    String? nombres,
    String? apellidos,
    String? email,
  }) async {
    try {
      final body = <String, dynamic>{};
      if (nombres != null) body['nombres'] = nombres;
      if (apellidos != null) body['apellidos'] = apellidos;
      if (email != null) body['email'] = email;

      final response = await http.post(
        Uri.parse(
          '${ApiConfig.baseUrl}/reacciones/mega-evento/$megaEventoId/reaccionar-publico',
        ),
        headers: await _getHeaders(includeAuth: false),
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Reacción registrada',
          'reaccionado': data['reaccionado'] as bool? ?? false,
          'total_reacciones': data['total_reacciones'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al reaccionar',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== COMPARTIR MEGA EVENTOS ==========

  // Compartir mega evento (usuario autenticado)
  static Future<Map<String, dynamic>> compartirMegaEvento(
    int megaEventoId, {
    String metodo = 'link',
  }) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$megaEventoId/compartir'),
        headers: await _getHeaders(includeAuth: true),
        body: jsonEncode({'metodo': metodo}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Mega evento compartido exitosamente',
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al compartir mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Compartir mega evento público (sin autenticación)
  static Future<Map<String, dynamic>> compartirMegaEventoPublico(
    int megaEventoId, {
    String metodo = 'link',
    String? nombres,
    String? apellidos,
    String? email,
  }) async {
    try {
      final body = <String, dynamic>{'metodo': metodo};
      if (nombres != null && nombres.isNotEmpty) {
        body['nombres'] = nombres;
      }
      if (apellidos != null && apellidos.isNotEmpty) {
        body['apellidos'] = apellidos;
      }
      if (email != null && email.isNotEmpty) {
        body['email'] = email;
      }

      final response = await http.post(
        Uri.parse(
          '${ApiConfig.baseUrl}/mega-eventos/$megaEventoId/compartir-publico',
        ),
        headers: await _getHeaders(includeAuth: false),
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Mega evento compartido exitosamente',
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al compartir mega evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Obtener total de compartidos de un mega evento
  static Future<Map<String, dynamic>> getTotalCompartidosMegaEvento(
    int megaEventoId,
  ) async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/mega-eventos/$megaEventoId/compartidos/total',
        ),
        headers: await _getHeaders(includeAuth: false),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'total_compartidos': data['total_compartidos'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener total de compartidos',
        'total_compartidos': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'total_compartidos': 0,
      };
    }
  }

  // ========== SEGUIMIENTO MEGA EVENTOS ==========

  // Seguimiento de mega evento
  static Future<Map<String, dynamic>> getSeguimientoMegaEvento(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/seguimiento'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'seguimiento': data['seguimiento']};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener seguimiento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Participantes de mega evento
  static Future<Map<String, dynamic>> getParticipantesMegaEvento(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/participantes'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'participantes': data['participantes'] as List<dynamic>? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener participantes',
        'participantes': [],
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'participantes': [],
      };
    }
  }

  // Historial de mega evento
  static Future<Map<String, dynamic>> getHistorialMegaEvento(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/historial'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'historial': data['historial'] as List<dynamic>? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener historial',
        'historial': [],
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'historial': [],
      };
    }
  }

  // Exportar Excel de mega evento
  static Future<Map<String, dynamic>> exportarExcelMegaEvento(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/$id/exportar-excel'),
        headers: await _getHeaders(includeAuth: true),
      );

      if (response.statusCode == 200) {
        return {'success': true, 'data': response.bodyBytes};
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return {
        'success': false,
        'error': data['error'] ?? 'Error al exportar Excel',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // Seguimiento general de mega eventos
  static Future<Map<String, dynamic>> getSeguimientoGeneralMegaEventos() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/mega-eventos/seguimiento/general'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'estadisticas_agregadas':
              data['estadisticas_agregadas'] as Map<String, dynamic>? ?? {},
          'mega_eventos_detalle':
              data['mega_eventos_detalle'] as List<dynamic>? ?? [],
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener seguimiento general',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== EMPRESAS - EVENTOS PATROCINADOS ==========

  // Obtener eventos patrocinados por la empresa
  static Future<Map<String, dynamic>> getEventosPatrocinados() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/eventos/empresa/patrocinados'),
        headers: await _getHeaders(includeAuth: true),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'eventos_patrocinados': data['eventos_patrocinados'] as List? ?? [],
          'count': data['count'] as int? ?? 0,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener eventos patrocinados',
        'eventos_patrocinados': [],
        'count': 0,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión: ${e.toString()}',
        'eventos_patrocinados': [],
        'count': 0,
      };
    }
  }
}
