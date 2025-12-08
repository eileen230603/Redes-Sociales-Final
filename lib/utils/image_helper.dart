import '../config/api_config.dart';

/// Helper para construir URLs de imágenes con soporte CORS
/// Convierte rutas de storage a /api/storage/ para que funcionen con CORS
class ImageHelper {
  /// Construye una URL completa para una imagen desde una ruta relativa o absoluta
  /// 
  /// Acepta:
  /// - URLs completas (http://... o https://...)
  /// - Rutas absolutas (/storage/...)
  /// - Rutas relativas (storage/...)
  /// 
  /// SIEMPRE retorna una URL absoluta que apunta al servidor Laravel
  static String? buildImageUrl(String? imgPath) {
    if (imgPath == null || imgPath.trim().isEmpty) {
      return null;
    }

    final trimmedPath = imgPath.trim();

    // Obtener la URL base del servidor Laravel (SIEMPRE usar esta, nunca el origen de Flutter)
    final apiBaseUrl = ApiConfig.baseUrl; // http://127.0.0.1:8000/api
    final baseUrl = apiBaseUrl.replaceAll('/api', '').replaceAll(RegExp(r'/$'), ''); // http://127.0.0.1:8000

    // Si ya es una URL completa (http:// o https://)
    if (trimmedPath.startsWith('http://') || trimmedPath.startsWith('https://')) {
      // Extraer el dominio y puerto de la URL
      final uri = Uri.tryParse(trimmedPath);
      if (uri == null) {
        print('❌ ImageHelper: No se pudo parsear la URL: $trimmedPath');
        return null;
      }

      // Si es una URL de nuestro servidor Laravel (coincide con baseUrl)
      final serverHost = Uri.parse(baseUrl).host;
      final serverPort = Uri.parse(baseUrl).port;
      final urlHost = uri.host;
      final urlPort = uri.port;

      // Comparar host y puerto (también considerar localhost vs 127.0.0.1)
      final isOurServer = (urlHost == serverHost || 
                          (urlHost == 'localhost' && serverHost == '127.0.0.1') ||
                          (urlHost == '127.0.0.1' && serverHost == 'localhost')) &&
                         (urlPort == serverPort || (urlPort == 0 && serverPort == 8000));

      if (isOurServer) {
        // Si contiene /storage/ pero NO contiene /api/storage/, convertirla
        if (trimmedPath.contains('/storage/') && !trimmedPath.contains('/api/storage/')) {
          // Reemplazar la primera ocurrencia de /storage/ por /api/storage/
          final converted = trimmedPath.replaceFirst('/storage/', '/api/storage/');
          print('🖼️ ImageHelper: Convirtiendo URL de nuestro servidor');
          print('   Original: $trimmedPath');
          print('   Convertida: $converted');
          return converted;
        }
        // Si ya tiene /api/storage/, retornarla tal cual
        print('✅ ImageHelper: URL ya tiene /api/storage/');
        print('   URL: $trimmedPath');
        return trimmedPath;
      }
      
      // Si es una URL externa (no de nuestro servidor), retornarla tal cual
      // Las URLs externas pueden tener sus propios problemas de CORS que no podemos resolver
      print('⚠️ ImageHelper: URL externa detectada (puede tener problemas de CORS)');
      print('   URL: $trimmedPath');
      return trimmedPath;
    }

    // IMPORTANTE: Si NO es una URL completa, SIEMPRE construir una URL absoluta
    // que apunte al servidor Laravel, NUNCA usar URLs relativas

    // Si empieza con /storage/, convertirla a URL absoluta con /api/storage/
    if (trimmedPath.startsWith('/storage/')) {
      final url = '$baseUrl/api$trimmedPath';
      print('🖼️ ImageHelper: Construyendo URL absoluta desde /storage/');
      print('   Ruta relativa: $trimmedPath');
      print('   URL absoluta: $url');
      return url;
    }

    // Si empieza con storage/ (sin /), convertirla a URL absoluta con /api/storage/
    if (trimmedPath.startsWith('storage/')) {
      final url = '$baseUrl/api/$trimmedPath';
      print('🖼️ ImageHelper: Construyendo URL absoluta desde storage/');
      print('   Ruta relativa: $trimmedPath');
      print('   URL absoluta: $url');
      return url;
    }

    // Si empieza con /, construir URL absoluta
    if (trimmedPath.startsWith('/')) {
      // Si parece ser una ruta de storage, convertirla
      if (trimmedPath.contains('storage')) {
        final url = '$baseUrl/api$trimmedPath';
        print('🖼️ ImageHelper: Construyendo URL absoluta desde ruta con / que contiene storage');
        print('   Ruta relativa: $trimmedPath');
        print('   URL absoluta: $url');
        return url;
      }
      // Para otras rutas que empiezan con /, construir URL absoluta
      final url = '$baseUrl$trimmedPath';
      print('🖼️ ImageHelper: Construyendo URL absoluta desde ruta con /');
      print('   Ruta relativa: $trimmedPath');
      print('   URL absoluta: $url');
      return url;
    }

    // Por defecto, asumir que es relativa a storage y construir URL absoluta
    final cleanPath = trimmedPath.replaceFirst(RegExp(r'^storage/'), '');
    final url = '$baseUrl/api/storage/$cleanPath';
    print('🖼️ ImageHelper: Construyendo URL absoluta por defecto');
    print('   Ruta relativa: $trimmedPath');
    print('   URL absoluta: $url');
    return url;
  }

  /// Obtiene la primera imagen válida de un array de imágenes
  static String? getFirstImageUrl(List<dynamic>? imagenes) {
    if (imagenes == null || imagenes.isEmpty) {
      print('⚠️ ImageHelper.getFirstImageUrl: Array de imágenes vacío o null');
      return null;
    }

    print('🔄 ImageHelper.getFirstImageUrl: Procesando ${imagenes.length} imágenes');

    for (var img in imagenes) {
      if (img != null) {
        final imgPath = img.toString().trim();
        print('   📸 Procesando imagen: $imgPath');
        
        // Validar que la ruta sea válida
        if (imgPath.isNotEmpty &&
            imgPath != 'null' &&
            imgPath != '[]' &&
            !imgPath.startsWith('[') &&
            !imgPath.startsWith('{')) {
          final url = buildImageUrl(imgPath);
          if (url != null && url.isNotEmpty) {
            print('✅ ImageHelper.getFirstImageUrl: URL de imagen generada exitosamente');
            print('   Ruta original: $imgPath');
            print('   URL final: $url');
            return url;
          } else {
            print('❌ ImageHelper.getFirstImageUrl: buildImageUrl retornó null para: $imgPath');
          }
        } else {
          print('⚠️ ImageHelper.getFirstImageUrl: Ruta inválida: $imgPath');
        }
      }
    }

    print('⚠️ ImageHelper.getFirstImageUrl: No se encontró ninguna imagen válida');
    return null;
  }

  /// Obtiene todas las URLs válidas de imágenes
  static List<String> getAllImageUrls(List<dynamic>? imagenes) {
    if (imagenes == null || imagenes.isEmpty) {
      return [];
    }

    final List<String> urls = [];
    for (var img in imagenes) {
      if (img != null) {
        final imgPath = img.toString().trim();
        if (imgPath.isNotEmpty &&
            imgPath != 'null' &&
            imgPath != '[]' &&
            !imgPath.startsWith('[') &&
            !imgPath.startsWith('{')) {
          final url = buildImageUrl(imgPath);
          if (url != null && url.isNotEmpty) {
            urls.add(url);
          }
        }
      }
    }

    return urls;
  }
}
