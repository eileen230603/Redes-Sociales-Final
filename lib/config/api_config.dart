class ApiConfig {
  // Cambia esta URL por la URL de tu servidor Laravel
  // Para desarrollo local con Chrome/Web: http://localhost:8000 o http://127.0.0.1:8000
  // Para desarrollo local con emulador Android: http://10.0.2.2:8000
  // Para desarrollo local con dispositivo físico: http://TU_IP_LOCAL:8000
  // Para producción: https://tu-dominio.com

  // IMPORTANTE:
  // - Si ejecutas en Chrome/Web: usa localhost o 127.0.0.1
  // - Si ejecutas en emulador Android: usa 10.0.2.2
  // - Si ejecutas en dispositivo físico: usa tu IP local (ej: 192.168.1.100)

  // Para Chrome/Web (cambia a 10.0.2.2 si usas emulador Android)
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  // Endpoints
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String logoutEndpoint = '/auth/logout';
}
