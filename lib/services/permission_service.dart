import 'storage_service.dart';

class PermissionService {
  // Verificar si el usuario tiene un rol específico
  static Future<bool> hasRole(String role) async {
    final userData = await StorageService.getUserData();
    if (userData == null) return false;
    
    final roles = (userData['roles'] as List).cast<String>();
    return roles.contains(role);
  }

  // Verificar si el usuario tiene cualquiera de los roles dados
  static Future<bool> hasAnyRole(List<String> rolesToCheck) async {
    final userData = await StorageService.getUserData();
    if (userData == null) return false;
    
    final userRoles = (userData['roles'] as List).cast<String>();
    return rolesToCheck.any((role) => userRoles.contains(role));
  }

  // Verificar si el usuario tiene un permiso específico
  static Future<bool> can(String permission) async {
    final userData = await StorageService.getUserData();
    if (userData == null) return false;
    
    final permissions = (userData['permissions'] as List).cast<String>();
    return permissions.contains(permission);
  }
}
