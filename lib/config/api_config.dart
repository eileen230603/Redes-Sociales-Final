import 'package:flutter/foundation.dart' show kIsWeb;

// Import condicional: dart:io solo en plataformas no-web
import 'dart:io' if (dart.library.html) 'api_config_stub.dart';

class ApiConfig {
  // Configuración de URL base de la API
  // Se detecta automáticamente según la plataforma

  // IMPORTANTE:
  // - Si ejecutas en Chrome/Web: usa localhost o 127.0.0.1
  // - Si ejecutas en emulador Android: usa 10.0.2.2
  // - Si ejecutas en dispositivo físico: usa tu IP local (ej: 192.168.1.100)
  // - Para producción: https://tu-dominio.com

  // Si deseas sobrescribir la URL automática, cambia este valor
  // Deja como null para usar la detección automática
  // Para desarrollo local, usa null para detección automática
  static const String? _overrideBaseUrl = null;

  // Para dispositivo físico, cambia esta IP por la IP local de tu máquina
  // Encuentra tu IP local ejecutando: ipconfig (Windows) o ifconfig (Linux/Mac)
  // Ejemplo: static const String _localIp = '192.168.1.100';

  /// Obtiene la URL base según la plataforma
  static String get baseUrl {
    // Si hay una URL sobrescrita, usarla
    if (_overrideBaseUrl != null) {
      return _overrideBaseUrl!;
    }

    // Para web/chrome - usar 127.0.0.1 (más confiable que localhost)
    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api';
    }

    // Para móviles (Android/iOS) - solo se ejecuta si no es web
    if (Platform.isAndroid) {
      // Para Android emulador (10.0.2.2 apunta a localhost de la máquina host)
      return 'http://10.0.2.2:8000/api';
      // Para dispositivo físico Android, cambia a: 'http://192.168.1.XXX:8000/api'
    } else if (Platform.isIOS) {
      // Para iOS emulador/dispositivo físico
      return 'http://localhost:8000/api';
      // Para dispositivo físico iOS, cambia a: 'http://192.168.1.XXX:8000/api'
    }

    // Por defecto
    return 'http://localhost:8000/api';
  }

  // Endpoints
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String logoutEndpoint = '/auth/logout';
}
