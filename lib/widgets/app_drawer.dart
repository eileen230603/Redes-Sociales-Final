import 'package:flutter/material.dart';

import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../screens/empresa/ayuda_eventos_screen.dart';
import '../screens/empresa/eventos_patrocinados_screen.dart';
import '../screens/eventos_list_screen.dart';
import '../screens/home_screen.dart';
import '../screens/login_screen.dart';
import '../screens/mis_eventos_screen.dart';
import '../screens/notificaciones_screen.dart';
import '../screens/ong/crear_evento_screen.dart';
import '../screens/ong/crear_mega_evento_screen.dart';
import '../screens/ong/eventos_ong_screen.dart';
import '../screens/ong/historial_eventos_screen.dart';
import '../screens/ong/mega_eventos_list_screen.dart' as ong;
import '../screens/ong/voluntarios_ong_screen.dart';
import '../screens/ong/dashboard_ong_completo_screen.dart';
import '../screens/empresa/dashboard_empresa_screen.dart';
import '../screens/perfil_screen.dart';
import '../screens/reportes_screen.dart';
import '../screens/mega_eventos_list_screen.dart' as externo;

/// Drawer principal de la app con diseño Material 3 moderno
/// utilizando `NavigationDrawer` y la paleta de colores global.
class AppDrawer extends StatefulWidget {
  final String? currentRoute;

  const AppDrawer({super.key, this.currentRoute});

  @override
  State<AppDrawer> createState() => _AppDrawerState();
}

class _AppDrawerState extends State<AppDrawer> {
  int _selectedIndex = 0;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return FutureBuilder<Map<String, dynamic>?>(
      future: StorageService.getUserData(),
      builder: (context, snapshot) {
        final userData = snapshot.data;
        final userType = userData?['user_type'] as String? ?? '';

        // Contenedor con bordes redondeados y sombra suave (coincide con DrawerTheme)
        return Drawer(
          child: SafeArea(
            child: Container(
              decoration: BoxDecoration(
                color: colorScheme.surface,
                borderRadius: const BorderRadius.horizontal(
                  right: Radius.circular(24),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.06),
                    blurRadius: 16,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                children: [
                  _buildHeader(context, userData),
                  const SizedBox(height: 8),
                  Expanded(
                    child: _buildNavigationDrawerForUserType(
                      context,
                      userType,
                      userData,
                    ),
                  ),
                  const Divider(height: 1),
                  _buildAboutOption(context),
                  _buildLogoutSection(context),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  /// Header con avatar, nombre y email/rol
  Widget _buildHeader(BuildContext context, Map<String, dynamic>? userData) {
    final colorScheme = Theme.of(context).colorScheme;
    final name = (userData?['user_name'] as String?)?.trim();
    final role = userData?['user_type'] as String? ?? '';
    final initial =
        (name != null && name.isNotEmpty)
            ? name.characters.first.toUpperCase()
            : 'U';

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 12),
      child: Row(
        children: [
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: LinearGradient(
                colors: [colorScheme.primary, colorScheme.secondary],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            alignment: Alignment.center,
            child: Text(
              initial,
              style: TextStyle(
                color: colorScheme.onPrimary,
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name ?? 'Usuario',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w600,
                    color: colorScheme.onSurface,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  role.isNotEmpty ? role : 'Cuenta activa',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: colorScheme.onSurfaceVariant,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Construye el bloque principal de navegación según el tipo de usuario
  Widget _buildNavigationDrawerForUserType(
    BuildContext context,
    String userType,
    Map<String, dynamic>? userData,
  ) {
    if (userType == 'Integrante externo') {
      return _buildExternoNavigation(context);
    }
    if (userType == 'Empresa') {
      return _buildEmpresaNavigation(context);
    }
    if (userType == 'ONG') {
      return _buildOngNavigation(context);
    }

    // Menú genérico para otros tipos de usuario
    return _buildGenericNavigation(context, userData);
  }

  /// Navegación para Integrante externo
  Widget _buildExternoNavigation(BuildContext context) {
    return NavigationDrawer(
      selectedIndex: _selectedIndex,
      onDestinationSelected: (index) {
        _handleExternoDestinationTap(context, index);
      },
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 8, 24, 4),
          child: Text(
            'Navegación principal',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        const SizedBox(height: 4),
        NavigationDrawerDestination(
          icon: const Icon(Icons.home_outlined),
          selectedIcon: const Icon(Icons.home),
          label: const Text('Inicio'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.calendar_today_outlined),
          selectedIcon: const Icon(Icons.calendar_today),
          label: const Text('Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.star_border),
          selectedIcon: const Icon(Icons.star),
          label: const Text('Mega Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.event_note_outlined),
          selectedIcon: const Icon(Icons.event_note),
          label: const Text('Mis Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.bar_chart_outlined),
          selectedIcon: const Icon(Icons.bar_chart),
          label: const Text('Reportes'),
        ),
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 16, 24, 4),
          child: Text(
            'Otras opciones',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.person_outline),
          selectedIcon: const Icon(Icons.person),
          label: const Text('Mi Perfil'),
        ),
      ],
    );
  }

  /// Navegación para Empresa
  Widget _buildEmpresaNavigation(BuildContext context) {
    return NavigationDrawer(
      selectedIndex: _selectedIndex,
      onDestinationSelected: (index) {
        _handleEmpresaDestinationTap(context, index);
      },
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 8, 24, 4),
          child: Text(
            'Navegación principal',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        const SizedBox(height: 4),
        NavigationDrawerDestination(
          icon: const Icon(Icons.home_outlined),
          selectedIcon: const Icon(Icons.home),
          label: const Text('Inicio'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.event_available_outlined),
          selectedIcon: const Icon(Icons.event_available),
          label: const Text('Eventos Patrocinados'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.favorite_border),
          selectedIcon: const Icon(Icons.favorite),
          label: const Text('Ayuda a Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.bar_chart_outlined),
          selectedIcon: const Icon(Icons.bar_chart),
          label: const Text('Reportes'),
        ),
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 16, 24, 4),
          child: Text(
            'Otras opciones',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.person_outline),
          selectedIcon: const Icon(Icons.person),
          label: const Text('Mi Perfil'),
        ),
      ],
    );
  }

  /// Navegación para ONG
  Widget _buildOngNavigation(BuildContext context) {
    return NavigationDrawer(
      selectedIndex: _selectedIndex,
      onDestinationSelected: (index) {
        _handleOngDestinationTap(context, index);
      },
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 8, 24, 4),
          child: Text(
            'Navegación principal',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        const SizedBox(height: 4),
        NavigationDrawerDestination(
          icon: const Icon(Icons.home_outlined),
          selectedIcon: const Icon(Icons.home),
          label: const Text('Inicio'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.dashboard_customize_outlined),
          selectedIcon: const Icon(Icons.dashboard_customize),
          label: const Text('Dashboard'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.event_outlined),
          selectedIcon: const Icon(Icons.event),
          label: const Text('Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.history),
          selectedIcon: const Icon(Icons.history_rounded),
          label: const Text('Historial'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.add_circle_outline),
          selectedIcon: const Icon(Icons.add_circle),
          label: const Text('Crear Evento'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.star_border),
          selectedIcon: const Icon(Icons.star),
          label: const Text('Mega Eventos'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.add_circle_outline),
          selectedIcon: const Icon(Icons.add_circle),
          label: const Text('Crear Mega Evento'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.people_outline),
          selectedIcon: const Icon(Icons.people),
          label: const Text('Voluntarios'),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.notifications_outlined),
          selectedIcon: const Icon(Icons.notifications),
          label: const Text('Notificaciones'),
        ),
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 16, 24, 4),
          child: Text(
            'Otras opciones',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.person_outline),
          selectedIcon: const Icon(Icons.person),
          label: const Text('Mi Perfil'),
        ),
      ],
    );
  }

  /// Navegación genérica cuando no hay tipo de usuario específico
  Widget _buildGenericNavigation(
    BuildContext context,
    Map<String, dynamic>? userData,
  ) {
    return NavigationDrawer(
      selectedIndex: _selectedIndex,
      onDestinationSelected: (index) {
        _handleGenericDestinationTap(context, index);
      },
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(24, 8, 24, 4),
          child: Text(
            'Navegación',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
        ),
        NavigationDrawerDestination(
          icon: const Icon(Icons.home_outlined),
          selectedIcon: const Icon(Icons.home),
          label: const Text('Inicio'),
        ),
      ],
    );
  }

  Widget _buildAboutOption(BuildContext context) {
    return ListTile(
      leading: const Icon(Icons.info_outline),
      title: const Text('Acerca de'),
      onTap: () {
        showAboutDialog(
          context: context,
          applicationName: 'Redes Sociales',
          applicationVersion: '1.0.0',
          applicationIcon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Theme.of(context).primaryColor,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.volunteer_activism,
              color: Colors.white,
              size: 32,
            ),
          ),
          children: [
            const SizedBox(height: 16),
            const Text(
              'Plataforma digital para conectar ONGs, Empresas y Voluntarios para transformar el mundo.',
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            const Text(
              'Desarrollado por estudiantes de Ingeniería de Sistemas.',
              style: TextStyle(fontSize: 12, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            const Text(
              '© 2025 UNI2',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
          ],
        );
      },
    );
  }

  /// Sección inferior para cerrar sesión con estilo acento
  Widget _buildLogoutSection(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 8, 20, 20),
      child: FilledButton.tonalIcon(
        style: FilledButton.styleFrom(
          backgroundColor: colorScheme.errorContainer.withOpacity(0.16),
          foregroundColor: colorScheme.error,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(18),
          ),
        ),
        onPressed: () async {
          final confirm = await showDialog<bool>(
            context: context,
            builder:
                (dialogContext) => AlertDialog(
                  title: const Text('Cerrar sesión'),
                  content: const Text(
                    '¿Estás seguro de que deseas cerrar sesión?',
                  ),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.of(dialogContext).pop(false),
                      child: const Text('Cancelar'),
                    ),
                    FilledButton(
                      onPressed: () => Navigator.of(dialogContext).pop(true),
                      child: const Text('Cerrar sesión'),
                    ),
                  ],
                ),
          );

          if (confirm != true) return;

          await ApiService.logout();
          if (!context.mounted) return;

          Navigator.of(context).pushAndRemoveUntil(
            MaterialPageRoute(builder: (context) => const LoginScreen()),
            (route) => false,
          );
        },
        icon: const Icon(Icons.logout),
        label: const Text('Cerrar sesión'),
      ),
    );
  }

  // ====== Handlers de navegación, manteniendo la misma lógica que antes ======

  void _handleExternoDestinationTap(BuildContext context, int index) {
    setState(() => _selectedIndex = index);
    Navigator.pop(context);

    switch (index) {
      case 0:
        if (widget.currentRoute != '/home') {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
          );
        }
        break;
      case 1:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const EventosListScreen()),
        );
        break;
      case 2:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const externo.MegaEventosListScreen(),
          ),
        );
        break;
      case 3:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const MisEventosScreen()),
        );
        break;
      case 4:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const ReportesScreen()),
        );
        break;
      case 5:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const PerfilScreen()),
        );
        break;
    }
  }

  void _handleEmpresaDestinationTap(BuildContext context, int index) {
    setState(() => _selectedIndex = index);
    Navigator.pop(context);

    switch (index) {
      case 0:
        if (widget.currentRoute != '/home') {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
          );
        }
        break;
      case 1:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const EventosPatrocinadosScreen(),
          ),
        );
        break;
      case 2:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const AyudaEventosScreen()),
        );
        break;
      case 3:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const DashboardEmpresaScreen(),
          ),
        );
        break;
      case 4:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const PerfilScreen()),
        );
        break;
    }
  }

  void _handleOngDestinationTap(BuildContext context, int index) {
    setState(() => _selectedIndex = index);
    Navigator.pop(context);

    switch (index) {
      case 0:
        if (widget.currentRoute != '/home') {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
          );
        }
        break;
      case 1:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const DashboardOngCompletoScreen(),
          ),
        );
        break;
      case 2:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const EventosOngScreen()),
        );
        break;
      case 3:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const HistorialEventosScreen(),
          ),
        );
        break;
      case 4:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const CrearEventoScreen()),
        );
        break;
      case 5:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const ong.MegaEventosListScreen(),
          ),
        );
        break;
      case 6:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const CrearMegaEventoScreen(),
          ),
        );
        break;
      case 7:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const VoluntariosOngScreen()),
        );
        break;
      case 8:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const NotificacionesScreen()),
        );
        break;
      case 9:
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const PerfilScreen()),
        );
        break;
    }
  }

  void _handleGenericDestinationTap(BuildContext context, int index) {
    setState(() => _selectedIndex = index);
    Navigator.pop(context);

    switch (index) {
      case 0:
        if (widget.currentRoute != '/home') {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
          );
        }
        break;
    }
  }
}
