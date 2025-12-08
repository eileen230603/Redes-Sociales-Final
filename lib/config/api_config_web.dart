// Configuración alternativa para Web/Chrome
// Si ejecutas la app en Chrome, usa este archivo o cambia la URL a localhost

class ApiConfig {
  static const String baseUrl = 'http://localhost:8000/api';

  // Endpoints
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String logoutEndpoint = '/auth/logout';
}
