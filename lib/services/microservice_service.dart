import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'storage_service.dart';

/// Servicio base para integración con microservicios
/// Este servicio proporciona la estructura para integrar servicios externos
/// siguiendo el patrón de microservicios
class MicroserviceService {
  // Obtener headers con autenticación
  static Future<Map<String, String>> _getHeaders() async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    final token = await StorageService.getToken();
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  /// Realizar petición a un microservicio externo
  ///
  /// [serviceUrl] - URL base del microservicio
  /// [endpoint] - Endpoint específico del servicio
  /// [method] - Método HTTP (GET, POST, PUT, DELETE)
  /// [body] - Cuerpo de la petición (opcional)
  /// [headers] - Headers adicionales (opcional)
  static Future<Map<String, dynamic>> callMicroservice({
    required String serviceUrl,
    required String endpoint,
    String method = 'GET',
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    try {
      final baseHeaders = await _getHeaders();
      if (headers != null) {
        baseHeaders.addAll(headers);
      }

      final uri = Uri.parse('$serviceUrl$endpoint');
      http.Response response;

      switch (method.toUpperCase()) {
        case 'GET':
          response = await http.get(uri, headers: baseHeaders);
          break;
        case 'POST':
          response = await http.post(
            uri,
            headers: baseHeaders,
            body: body != null ? jsonEncode(body) : null,
          );
          break;
        case 'PUT':
          response = await http.put(
            uri,
            headers: baseHeaders,
            body: body != null ? jsonEncode(body) : null,
          );
          break;
        case 'DELETE':
          response = await http.delete(uri, headers: baseHeaders);
          break;
        default:
          throw Exception('Método HTTP no soportado: $method');
      }

      final responseData = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode >= 200 && response.statusCode < 300) {
        return {
          'success': true,
          'data': responseData,
          'statusCode': response.statusCode,
        };
      }

      return {
        'success': false,
        'error':
            responseData['error'] ??
            responseData['message'] ??
            'Error en el microservicio',
        'statusCode': response.statusCode,
      };
    } catch (e) {
      return {
        'success': false,
        'error': 'Error de conexión con microservicio: ${e.toString()}',
      };
    }
  }

  /// Ejemplo: Integración con servicio de notificaciones push
  static Future<Map<String, dynamic>> enviarNotificacionPush({
    required String titulo,
    required String mensaje,
    required List<String> tokens,
    Map<String, dynamic>? data,
  }) async {
    // Ejemplo de integración con Firebase Cloud Messaging o similar
    // Esta es una estructura base que puede ser extendida
    return await callMicroservice(
      serviceUrl:
          'https://api.notificaciones.example.com', // URL del microservicio
      endpoint: '/v1/push/send',
      method: 'POST',
      body: {
        'titulo': titulo,
        'mensaje': mensaje,
        'tokens': tokens,
        'data': data,
      },
    );
  }

  /// Ejemplo: Integración con servicio de análisis/analytics
  static Future<Map<String, dynamic>> registrarEventoAnalytics({
    required String evento,
    Map<String, dynamic>? propiedades,
  }) async {
    return await callMicroservice(
      serviceUrl: 'https://api.analytics.example.com',
      endpoint: '/v1/events',
      method: 'POST',
      body: {
        'evento': evento,
        'propiedades': propiedades,
        'timestamp': DateTime.now().toIso8601String(),
      },
    );
  }

  /// Ejemplo: Integración con servicio de pagos
  static Future<Map<String, dynamic>> procesarPago({
    required double monto,
    required String moneda,
    required Map<String, dynamic> datosPago,
  }) async {
    return await callMicroservice(
      serviceUrl: 'https://api.pagos.example.com',
      endpoint: '/v1/payments/process',
      method: 'POST',
      body: {'monto': monto, 'moneda': moneda, 'datos': datosPago},
    );
  }

  /// Verificar salud del microservicio
  static Future<Map<String, dynamic>> healthCheck(String serviceUrl) async {
    return await callMicroservice(
      serviceUrl: serviceUrl,
      endpoint: '/health',
      method: 'GET',
    );
  }
}
