import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

/// Servicio de cache local para dashboards
/// Cachea datos por 30 minutos para reducir carga del servidor
class CacheService {
  static const String _prefix = 'dashboard_cache_';
  static const int _cacheDurationMinutes = 30;

  /// Obtener datos del cache si aún son válidos
  static Future<Map<String, dynamic>?> getCachedData(String key) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final cacheKey = '$_prefix$key';
      final timestampKey = '${cacheKey}_timestamp';

      final cachedData = prefs.getString(cacheKey);
      final timestampStr = prefs.getString(timestampKey);

      if (cachedData == null || timestampStr == null) {
        return null;
      }

      final timestamp = DateTime.parse(timestampStr);
      final now = DateTime.now();
      final difference = now.difference(timestamp);

      // Si el cache expiró, retornar null
      if (difference.inMinutes > _cacheDurationMinutes) {
        await _clearCache(key);
        return null;
      }

      return jsonDecode(cachedData) as Map<String, dynamic>;
    } catch (e) {
      print('❌ Error obteniendo cache: $e');
      return null;
    }
  }

  /// Guardar datos en cache
  static Future<void> setCachedData(
    String key,
    Map<String, dynamic> data,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final cacheKey = '$_prefix$key';
      final timestampKey = '${cacheKey}_timestamp';

      await prefs.setString(cacheKey, jsonEncode(data));
      await prefs.setString(timestampKey, DateTime.now().toIso8601String());
    } catch (e) {
      print('❌ Error guardando cache: $e');
    }
  }

  /// Limpiar cache específico
  static Future<void> _clearCache(String key) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final cacheKey = '$_prefix$key';
      final timestampKey = '${cacheKey}_timestamp';

      await prefs.remove(cacheKey);
      await prefs.remove(timestampKey);
    } catch (e) {
      print('❌ Error limpiando cache: $e');
    }
  }

  /// Limpiar todo el cache de dashboards
  static Future<void> clearAllCache() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final keys = prefs.getKeys();

      for (final key in keys) {
        if (key.startsWith(_prefix)) {
          await prefs.remove(key);
        }
      }
    } catch (e) {
      print('❌ Error limpiando todo el cache: $e');
    }
  }

  /// Limpiar cache específico por key
  static Future<void> clearCache(String key) async {
    await _clearCache(key);
  }

  /// Verificar si hay datos en cache válidos
  static Future<bool> hasValidCache(String key) async {
    final data = await getCachedData(key);
    return data != null;
  }
}
