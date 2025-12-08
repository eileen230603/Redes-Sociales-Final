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

  /// Obtener grupos de parámetros
  static Future<Map<String, dynamic>> getGrupos() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/grupos'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'grupos': data['data'] as List<String>};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener grupos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Obtener parámetro por ID
  static Future<Map<String, dynamic>> getParametroPorId(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/$id'),
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

  /// Crear nuevo parámetro
  static Future<Map<String, dynamic>> crearParametro(
    Map<String, dynamic> parametroData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/configuracion'),
        headers: await _getHeaders(),
        body: jsonEncode(parametroData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        final parametro = Parametro.fromJson(
          data['data'] as Map<String, dynamic>,
        );
        return {
          'success': true,
          'message': data['message'] ?? 'Parámetro creado correctamente',
          'parametro': parametro,
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear parámetro',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar solo el valor de un parámetro
  static Future<Map<String, dynamic>> actualizarValorParametro(
    int id,
    dynamic valor,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/$id/valor'),
        headers: await _getHeaders(),
        body: jsonEncode({'valor': valor}),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final parametro = Parametro.fromJson(
          data['data'] as Map<String, dynamic>,
        );
        return {
          'success': true,
          'message': data['message'] ?? 'Valor actualizado correctamente',
          'parametro': parametro,
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al actualizar valor',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar parámetro
  static Future<Map<String, dynamic>> eliminarParametro(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/configuracion/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Parámetro eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar parámetro',
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

      print('🔗 Cargando tipos de evento desde: $uri');

      final response = await http.get(uri, headers: await _getHeaders());

      print('📡 Respuesta HTTP: ${response.statusCode}');
      print('📄 Body: ${response.body}');

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        final dataList = data['data'] as List?;
        if (dataList != null && dataList.isNotEmpty) {
          final tiposList =
              dataList
                  .map((t) => TipoEvento.fromJson(t as Map<String, dynamic>))
                  .toList();
          print('✅ Tipos parseados: ${tiposList.length}');
          return {'success': true, 'tipos': tiposList};
        } else {
          print('⚠️ No hay tipos de evento en la respuesta');
          return {'success': true, 'tipos': <TipoEvento>[]};
        }
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener tipos de evento',
      };
    } catch (e, stackTrace) {
      print('❌ Error en getTiposEvento: $e');
      print('📚 Stack trace: $stackTrace');
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear tipo de evento
  static Future<Map<String, dynamic>> crearTipoEvento(
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-evento'),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Tipo de evento creado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear tipo de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar tipo de evento
  static Future<Map<String, dynamic>> actualizarTipoEvento(
    int id,
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-evento/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de evento actualizado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar tipo de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar tipo de evento
  static Future<Map<String, dynamic>> eliminarTipoEvento(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-evento/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de evento eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar tipo de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== CATEGORÍAS DE MEGA EVENTOS ==========

  /// Obtener categorías de mega eventos
  static Future<Map<String, dynamic>> getCategoriasMegaEvento({
    bool? activo,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/categorias-mega-evento',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'categorias': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener categorías de mega eventos',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear categoría de mega evento
  static Future<Map<String, dynamic>> crearCategoriaMegaEvento(
    Map<String, dynamic> categoriaData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/categorias-mega-evento',
        ),
        headers: await _getHeaders(),
        body: jsonEncode(categoriaData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Categoría creada correctamente',
          'categoria': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear categoría',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar categoría de mega evento
  static Future<Map<String, dynamic>> actualizarCategoriaMegaEvento(
    int id,
    Map<String, dynamic> categoriaData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/categorias-mega-evento/$id',
        ),
        headers: await _getHeaders(),
        body: jsonEncode(categoriaData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Categoría actualizada correctamente',
          'categoria': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar categoría',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar categoría de mega evento
  static Future<Map<String, dynamic>> eliminarCategoriaMegaEvento(
    int id,
  ) async {
    try {
      final response = await http.delete(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/categorias-mega-evento/$id',
        ),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Categoría eliminada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar categoría',
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

  /// Crear ciudad
  static Future<Map<String, dynamic>> crearCiudad(
    Map<String, dynamic> ciudadData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/ciudades'),
        headers: await _getHeaders(),
        body: jsonEncode(ciudadData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Ciudad creada correctamente',
          'ciudad': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear ciudad',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar ciudad
  static Future<Map<String, dynamic>> actualizarCiudad(
    int id,
    Map<String, dynamic> ciudadData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/ciudades/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(ciudadData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Ciudad actualizada correctamente',
          'ciudad': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar ciudad',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar ciudad
  static Future<Map<String, dynamic>> eliminarCiudad(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/ciudades/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Ciudad eliminada correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar ciudad',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== LUGARES ==========

  /// Obtener lugares
  static Future<Map<String, dynamic>> getLugares({
    bool? activo,
    String? buscar,
    int? ciudadId,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();
      if (buscar != null) queryParams['buscar'] = buscar;
      if (ciudadId != null) queryParams['ciudad_id'] = ciudadId.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/lugares',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'lugares': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener lugares',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear lugar
  static Future<Map<String, dynamic>> crearLugar(
    Map<String, dynamic> lugarData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/lugares'),
        headers: await _getHeaders(),
        body: jsonEncode(lugarData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Lugar creado correctamente',
          'lugar': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear lugar',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar lugar
  static Future<Map<String, dynamic>> actualizarLugar(
    int id,
    Map<String, dynamic> lugarData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/lugares/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(lugarData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Lugar actualizado correctamente',
          'lugar': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar lugar',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar lugar
  static Future<Map<String, dynamic>> eliminarLugar(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/lugares/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Lugar eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar lugar',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== ESTADOS DE PARTICIPACIÓN ==========

  /// Obtener estados de participación
  static Future<Map<String, dynamic>> getEstadosParticipacion({
    bool? activo,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/estados-participacion',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'estados': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener estados de participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear estado de participación
  static Future<Map<String, dynamic>> crearEstadoParticipacion(
    Map<String, dynamic> estadoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/estados-participacion',
        ),
        headers: await _getHeaders(),
        body: jsonEncode(estadoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Estado de participación creado correctamente',
          'estado': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear estado de participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar estado de participación
  static Future<Map<String, dynamic>> actualizarEstadoParticipacion(
    int id,
    Map<String, dynamic> estadoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/estados-participacion/$id',
        ),
        headers: await _getHeaders(),
        body: jsonEncode(estadoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Estado de participación actualizado correctamente',
          'estado': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar estado de participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar estado de participación
  static Future<Map<String, dynamic>> eliminarEstadoParticipacion(
    int id,
  ) async {
    try {
      final response = await http.delete(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/estados-participacion/$id',
        ),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Estado de participación eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar estado de participación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== TIPOS DE NOTIFICACIÓN ==========

  /// Obtener tipos de notificación
  static Future<Map<String, dynamic>> getTiposNotificacion({
    bool? activo,
  }) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/tipos-notificacion',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'tipos': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener tipos de notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear tipo de notificación
  static Future<Map<String, dynamic>> crearTipoNotificacion(
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-notificacion'),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de notificación creado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear tipo de notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar tipo de notificación
  static Future<Map<String, dynamic>> actualizarTipoNotificacion(
    int id,
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/tipos-notificacion/$id',
        ),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ??
              'Tipo de notificación actualizado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar tipo de notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar tipo de notificación
  static Future<Map<String, dynamic>> eliminarTipoNotificacion(int id) async {
    try {
      final response = await http.delete(
        Uri.parse(
          '${ApiConfig.baseUrl}/parametrizaciones/tipos-notificacion/$id',
        ),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de notificación eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar tipo de notificación',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== ESTADOS DE EVENTO ==========

  /// Obtener estados de evento
  static Future<Map<String, dynamic>> getEstadosEvento({bool? activo}) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/estados-evento',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'estados': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener estados de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear estado de evento
  static Future<Map<String, dynamic>> crearEstadoEvento(
    Map<String, dynamic> estadoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/estados-evento'),
        headers: await _getHeaders(),
        body: jsonEncode(estadoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Estado de evento creado correctamente',
          'estado': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear estado de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar estado de evento
  static Future<Map<String, dynamic>> actualizarEstadoEvento(
    int id,
    Map<String, dynamic> estadoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/estados-evento/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(estadoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Estado de evento actualizado correctamente',
          'estado': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar estado de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar estado de evento
  static Future<Map<String, dynamic>> eliminarEstadoEvento(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/estados-evento/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Estado de evento eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar estado de evento',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  // ========== TIPOS DE USUARIO ==========

  /// Obtener tipos de usuario
  static Future<Map<String, dynamic>> getTiposUsuario({bool? activo}) async {
    try {
      final queryParams = <String, String>{};
      if (activo != null) queryParams['activo'] = activo.toString();

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/parametrizaciones/tipos-usuario',
      ).replace(queryParameters: queryParams);

      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'tipos': data['data'] as List? ?? []};
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al obtener tipos de usuario',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Crear tipo de usuario
  static Future<Map<String, dynamic>> crearTipoUsuario(
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-usuario'),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Tipo de usuario creado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al crear tipo de usuario',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Actualizar tipo de usuario
  static Future<Map<String, dynamic>> actualizarTipoUsuario(
    int id,
    Map<String, dynamic> tipoData,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-usuario/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(tipoData),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de usuario actualizado correctamente',
          'tipo': data['data'],
        };
      }

      return {
        'success': false,
        'error':
            data['error'] ??
            data['errors']?.toString() ??
            'Error al actualizar tipo de usuario',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }

  /// Eliminar tipo de usuario
  static Future<Map<String, dynamic>> eliminarTipoUsuario(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/parametrizaciones/tipos-usuario/$id'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body) as Map<String, dynamic>;

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message':
              data['message'] ?? 'Tipo de usuario eliminado correctamente',
        };
      }

      return {
        'success': false,
        'error': data['error'] ?? 'Error al eliminar tipo de usuario',
      };
    } catch (e) {
      return {'success': false, 'error': 'Error de conexión: ${e.toString()}'};
    }
  }
}
