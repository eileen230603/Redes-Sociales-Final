import 'storage_service.dart';
import 'api_service.dart';

/// Helper para manejar autenticación y validación de usuarios
class AuthHelper {
  /// Obtener datos del usuario autenticado con validación
  static Future<Map<String, dynamic>?> getUserDataValidated() async {
    try {
      // Verificar si hay token
      final token = await StorageService.getToken();
      if (token == null || token.isEmpty) {
        return null;
      }

      // Obtener datos del usuario
      final userData = await StorageService.getUserData();
      if (userData == null) {
        return null;
      }

      return userData;
    } catch (e) {
      print('❌ Error al obtener datos del usuario: $e');
      return null;
    }
  }

  /// Obtener ID de la ONG con validación
  /// Retorna null si el usuario no es ONG o no tiene entity_id
  static Future<int?> getOngId() async {
    try {
      final userData = await getUserDataValidated();
      if (userData == null) return null;

      final userType = userData['user_type'] as String?;
      if (userType != 'ONG') {
        print('⚠️ Usuario no es ONG, tipo: $userType');
        return null;
      }

      final entityId = userData['entity_id'] as int?;
      if (entityId == null) {
        print('⚠️ ONG no tiene entity_id');
        return null;
      }

      return entityId;
    } catch (e) {
      print('❌ Error al obtener ID de ONG: $e');
      return null;
    }
  }

  /// Obtener ID de la empresa con validación
  static Future<int?> getEmpresaId() async {
    try {
      final userData = await getUserDataValidated();
      if (userData == null) return null;

      final userType = userData['user_type'] as String?;
      if (userType != 'Empresa') {
        return null;
      }

      return userData['entity_id'] as int?;
    } catch (e) {
      return null;
    }
  }

  /// Obtener ID del integrante externo con validación
  static Future<int?> getExternoId() async {
    try {
      final userData = await getUserDataValidated();
      if (userData == null) return null;

      final userType = userData['user_type'] as String?;
      if (userType != 'Integrante Externo') {
        return null;
      }

      return userData['user_id'] as int?;
    } catch (e) {
      return null;
    }
  }

  /// Verificar si el usuario está autenticado
  static Future<bool> isAuthenticated() async {
    final token = await StorageService.getToken();
    return token != null && token.isNotEmpty;
  }

  /// Verificar si el usuario es ONG
  static Future<bool> isOng() async {
    final userData = await getUserDataValidated();
    return userData?['user_type'] == 'ONG';
  }

  /// Verificar si el usuario es Empresa
  static Future<bool> isEmpresa() async {
    final userData = await getUserDataValidated();
    return userData?['user_type'] == 'Empresa';
  }

  /// Verificar si el usuario es Integrante Externo
  static Future<bool> isExterno() async {
    final userData = await getUserDataValidated();
    return userData?['user_type'] == 'Integrante Externo';
  }

  /// Obtener tipo de usuario
  static Future<String?> getUserType() async {
    final userData = await getUserDataValidated();
    return userData?['user_type'] as String?;
  }

  /// Validar y refrescar datos del usuario desde el servidor
  /// Útil cuando entity_id puede estar faltando
  static Future<Map<String, dynamic>?> refreshUserData() async {
    try {
      // Obtener perfil desde el servidor
      final result = await ApiService.getPerfil();
      if (result['success'] == true && result['data'] != null) {
        final perfil = result['data'] as Map<String, dynamic>;

        // Actualizar datos locales
        final userType = perfil['tipo_usuario'] as String?;
        final userId = perfil['id_usuario'] as int?;
        final userName = perfil['nombre_usuario'] as String? ?? '';
        final entityId = perfil['id_entidad'] as int?;

        if (userId != null && userType != null) {
          await StorageService.saveUserData(
            userId: userId,
            userName: userName,
            userType: userType,
            entityId: entityId,
          );

          return {
            'user_id': userId,
            'user_name': userName,
            'user_type': userType,
            'entity_id': entityId,
          };
        }
      }
      return null;
    } catch (e) {
      print('❌ Error al refrescar datos del usuario: $e');
      return null;
    }
  }

  /// Obtener ID de ONG con reintento y refresco de datos
  static Future<int?> getOngIdWithRetry() async {
    // Primero intentar obtener directamente
    var ongId = await getOngId();
    if (ongId != null) return ongId;

    // Si no se encontró, intentar refrescar datos del servidor
    print('🔄 Refrescando datos del usuario desde el servidor...');
    final refreshedData = await refreshUserData();
    if (refreshedData != null) {
      final userType = refreshedData['user_type'] as String?;
      if (userType == 'ONG') {
        ongId = refreshedData['entity_id'] as int?;
        if (ongId != null) {
          print('✅ ONG ID obtenido después de refrescar: $ongId');
          return ongId;
        }
      }
    }

    return null;
  }
}
