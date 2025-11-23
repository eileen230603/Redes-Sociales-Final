import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/parametro.dart';
import '../models/tipo_evento.dart';
import '../models/ciudad.dart';
import 'storage_service.dart';

class ParametrizacionService {
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

  // ========== PARÁMETROS / CONFIGURACIÓN ==========

  /// Obtener todos los parámetros
  static Future<Map<String, dynamic>> getParametros({
    String? categoria,
    String? grupo,
    bool? visible,
    bool? editable,
    String? buscar,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (categoria != null) queryParams['categoria'] = categoria;
      if (grupo != null) queryParams['grupo'] = grupo;
      if (visible != null) queryParams['visible'] = visible.toString();
      if (editable != null) queryParams['editable'] = editable.toString();
      if (buscar != null) queryParams['buscar'] = buscar;

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/configuracion',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final parametrosList =
            (data['data'] as List)
                .map((p) => Parametro.fromJson(p as Map<String, dynamic>))
                .toList();
        return {'success': true, 'parametros': parametrosList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener parámetros',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Obtener parámetro por código
  static Future<Map<String, dynamic>> getParametroPorCodigo(
    String codigo,
  ) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/codigo/$codigo'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final parametro = Parametro.fromJson(
          data['data'] as Map<String, dynamic>,
        );
        return {'success': true, 'parametro': parametro};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Parámetro no encontrado',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar valor de parámetro
  static Future<Map<String, dynamic>> actualizarParametro(
    int id,
    Map<String, dynamic> parametroData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(parametroData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final parametro = Parametro.fromJson(
          data['data'] as Map<String, dynamic>,
        );
        return {
          'success': true,
          'message': data['message'] ?? 'Parámetro actualizado',
          'parametro': parametro,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al actualizar parámetro',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Obtener categorías de parámetros
  static Future<Map<String, dynamic>> getCategorias() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/categorias'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'categorias': data['data'] as List<String>};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener categorías',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== TIPOS DE EVENTO ==========

  /// Obtener tipos de evento
  static Future<Map<String, dynamic>> getTiposEvento({bool? activo}) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/tipos-evento',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final tiposList =
            (data['data'] as List)
                .map((t) => TipoEvento.fromJson(t as Map<String, dynamic>))
                .toList();
        return {'success': true, 'tipos': tiposList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener tipos de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== CIUDADES ==========

  /// Obtener ciudades
  static Future<Map<String, dynamic>> getCiudades({
    bool? activo,
    String? buscar,
    String? departamento,
    String? pais,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();
      if (buscar != null) queryParams['buscar'] = buscar;
      if (departamento != null) queryParams['departamento'] = departamento;
      if (pais != null) queryParams['pais'] = pais;

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/ciudades',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final ciudadesList =
            (data['data'] as List)
                .map((c) => Ciudad.fromJson(c as Map<String, dynamic>))
                .toList();
        return {'success': true, 'ciudades': ciudadesList};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener ciudades',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }
}
