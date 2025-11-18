import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static const String _tokenKey = 'auth_token';
  static const String _userIdKey = 'user_id';
  static const String _userNameKey = 'user_name';
  static const String _userTypeKey = 'user_type';
  static const String _entityIdKey = 'entity_id';

  // Guardar token
  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  // Obtener token
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  // Guardar información del usuario
  static Future<void> saveUserData({
    required int userId,
    required String userName,
    required String userType,
    int? entityId,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_userIdKey, userId);
    await prefs.setString(_userNameKey, userName);
    await prefs.setString(_userTypeKey, userType);
    if (entityId != null) {
      await prefs.setInt(_entityIdKey, entityId);
    }
  }

  // Obtener información del usuario
  static Future<Map<String, dynamic>?> getUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getInt(_userIdKey);
    if (userId == null) return null;

    return {
      'user_id': userId,
      'user_name': prefs.getString(_userNameKey) ?? '',
      'user_type': prefs.getString(_userTypeKey) ?? '',
      'entity_id': prefs.getInt(_entityIdKey),
    };
  }

  // Verificar si hay sesión activa
  static Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  // Limpiar datos de sesión
  static Future<void> clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userIdKey);
    await prefs.remove(_userNameKey);
    await prefs.remove(_userTypeKey);
    await prefs.remove(_entityIdKey);
  }
}
