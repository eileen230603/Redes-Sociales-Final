import 'package:flutter/material.dart';
import '../services/storage_service.dart';
import '../services/api_service.dart';
import '../screens/home_screen.dart';
import '../screens/eventos_list_screen.dart';
import '../screens/mis_eventos_screen.dart';
import '../screens/reportes_screen.dart';
import '../screens/empresa/eventos_patrocinados_screen.dart';
import '../screens/empresa/ayuda_eventos_screen.dart';
import '../screens/empresa/reportes_empresa_screen.dart';
import '../screens/ong/eventos_ong_screen.dart';
import '../screens/ong/crear_evento_screen.dart';
import '../screens/ong/voluntarios_ong_screen.dart';
import '../screens/ong/reportes_ong_screen.dart';
import '../screens/login_screen.dart';

class AppDrawer extends StatelessWidget {
  final String? currentRoute;

  const AppDrawer({super.key, this.currentRoute});

  @override
  Widget build(BuildContext context) {
    // Capturar el contexto del Scaffold padre antes de cualquier operación
    final scaffoldContext = context;

    return FutureBuilder<Map<String, dynamic>?>(
      future: StorageService.getUserData(),
      builder: (context, snapshot) {
        final userData = snapshot.data;
        final userType = userData?['user_type'] as String? ?? '';

        // Si es integrante externo, mostrar menú específico
        if (userType == 'Integrante externo') {
          return _buildExternoDrawer(scaffoldContext, userData);
        }

        // Si es empresa, mostrar menú específico
        if (userType == 'Empresa') {
          return _buildEmpresaDrawer(scaffoldContext, userData);
        }

        // Si es ONG, mostrar menú específico
        if (userType == 'ONG') {
          return _buildOngDrawer(scaffoldContext, userData);
        }

        // Menú genérico para otros tipos de usuario
        return _buildGenericDrawer(scaffoldContext, userData);
      },
    );
  }

  Widget _buildExternoDrawer(
    BuildContext scaffoldContext,
    Map<String, dynamic>? userData,
  ) {
    return Drawer(
      child: Container(
        color: const Color(0xFF343A40), // Color oscuro similar a AdminLTE
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // Header con logo/brand
            DrawerHeader(
              decoration: const BoxDecoration(color: Color(0xFF343A40)),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text(
                    'UNI2',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 32,
                      fontWeight: FontWeight.w300,
                      letterSpacing: 2,
                    ),
                  ),
                  if (userData != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      userData['user_name'] as String? ?? '',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ],
              ),
            ),

            // NAVEGACIÓN PRINCIPAL
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 16, 16, 8),
              child: Text(
                'NAVEGACIÓN PRINCIPAL',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.home,
              title: 'Inicio',
              route: '/home',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(scaffoldContext);
                if (currentRoute != '/home') {
                  Navigator.pushReplacement(
                    scaffoldContext,
                    MaterialPageRoute(builder: (context) => const HomeScreen()),
                  );
                }
              },
            ),

            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.calendar_today,
              title: 'Eventos',
              route: '/eventos',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(scaffoldContext);
                Navigator.push(
                  scaffoldContext,
                  MaterialPageRoute(
                    builder: (context) => const EventosListScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.event_note,
              title: 'Mis Eventos',
              route: '/mis-eventos',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(scaffoldContext);
                Navigator.push(
                  scaffoldContext,
                  MaterialPageRoute(
                    builder: (context) => const MisEventosScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.bar_chart,
              title: 'Reportes',
              route: '/reportes',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(scaffoldContext);
                Navigator.push(
                  scaffoldContext,
                  MaterialPageRoute(
                    builder: (context) => const ReportesScreen(),
                  ),
                );
              },
            ),

            // OTRAS OPCIONES
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 24, 16, 8),
              child: Text(
                'OTRAS OPCIONES',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.public,
              title: 'Ir a página pública',
              route: '/publica',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(scaffoldContext);
                ScaffoldMessenger.of(scaffoldContext).showSnackBar(
                  const SnackBar(
                    content: Text('Página pública - Próximamente'),
                  ),
                );
              },
            ),

            const Divider(color: Colors.white24),

            // Cerrar sesión
            _buildDrawerItem(
              scaffoldContext,
              icon: Icons.logout,
              title: 'Cerrar sesión',
              route: '/logout',
              currentRoute: currentRoute,
              textColor: Colors.red[300],
              iconColor: Colors.red[300],
              onTap: () async {
                // Mostrar diálogo de confirmación primero (antes de cerrar el drawer)
                final confirm = await showDialog<bool>(
                  context: scaffoldContext,
                  builder:
                      (dialogContext) => AlertDialog(
                        title: const Text('Cerrar sesión'),
                        content: const Text(
                          '¿Estás seguro de que deseas cerrar sesión?',
                        ),
                        actions: [
                          TextButton(
                            onPressed:
                                () => Navigator.of(dialogContext).pop(false),
                            child: const Text('Cancelar'),
                          ),
                          TextButton(
                            onPressed:
                                () => Navigator.of(dialogContext).pop(true),
                            child: const Text('Cerrar sesión'),
                          ),
                        ],
                      ),
                );

                // Si el usuario canceló, no hacer nada
                if (confirm != true) return;

                // Cerrar el drawer
                Navigator.pop(scaffoldContext);

                // Cerrar sesión
                await ApiService.logout();

                // Verificar que el contexto aún esté disponible
                if (!scaffoldContext.mounted) return;

                // Redirigir al login usando el contexto del Scaffold
                Navigator.of(scaffoldContext).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmpresaDrawer(
    BuildContext context,
    Map<String, dynamic>? userData,
  ) {
    return Drawer(
      child: Container(
        color: const Color(0xFF343A40),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // Header con logo/brand
            DrawerHeader(
              decoration: const BoxDecoration(color: Color(0xFF343A40)),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text(
                    'UNI2',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 32,
                      fontWeight: FontWeight.w300,
                      letterSpacing: 2,
                    ),
                  ),
                  if (userData != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      userData['user_name'] as String? ?? '',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ],
              ),
            ),

            // NAVEGACIÓN PRINCIPAL
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 16, 16, 8),
              child: Text(
                'NAVEGACIÓN PRINCIPAL',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              context,
              icon: Icons.home,
              title: 'Inicio',
              route: '/home',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                if (currentRoute != '/home') {
                  Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (context) => const HomeScreen()),
                  );
                }
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.event_available,
              title: 'Eventos Patrocinados',
              route: '/empresa/eventos',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const EventosPatrocinadosScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.favorite,
              title: 'Ayuda a Eventos',
              route: '/empresa/eventos/disponibles',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const AyudaEventosScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.bar_chart,
              title: 'Reportes',
              route: '/empresa/reportes',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const ReportesEmpresaScreen(),
                  ),
                );
              },
            ),

            // OTRAS OPCIONES
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 24, 16, 8),
              child: Text(
                'OTRAS OPCIONES',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              context,
              icon: Icons.public,
              title: 'Ir a página pública',
              route: '/publica',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Página pública - Próximamente'),
                  ),
                );
              },
            ),

            const Divider(color: Colors.white24),

            // Cerrar sesión
            _buildDrawerItem(
              context,
              icon: Icons.logout,
              title: 'Cerrar sesión',
              route: '/logout',
              currentRoute: currentRoute,
              textColor: Colors.red[300],
              iconColor: Colors.red[300],
              onTap: () async {
                Navigator.pop(context);
                final confirm = await showDialog<bool>(
                  context: context,
                  builder:
                      (context) => AlertDialog(
                        title: const Text('Cerrar sesión'),
                        content: const Text(
                          '¿Estás seguro de que deseas cerrar sesión?',
                        ),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.of(context).pop(false),
                            child: const Text('Cancelar'),
                          ),
                          TextButton(
                            onPressed: () => Navigator.of(context).pop(true),
                            child: const Text('Cerrar sesión'),
                          ),
                        ],
                      ),
                );

                if (confirm == true) {
                  await ApiService.logout();
                  if (!context.mounted) return;
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(
                      builder: (context) => const LoginScreen(),
                    ),
                    (route) => false,
                  );
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOngDrawer(BuildContext context, Map<String, dynamic>? userData) {
    return Drawer(
      child: Container(
        color: const Color(0xFF343A40),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // Header con logo/brand
            DrawerHeader(
              decoration: const BoxDecoration(color: Color(0xFF343A40)),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text(
                    'UNI2',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 32,
                      fontWeight: FontWeight.w300,
                      letterSpacing: 2,
                    ),
                  ),
                  if (userData != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      userData['user_name'] as String? ?? '',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ],
              ),
            ),

            // NAVEGACIÓN PRINCIPAL
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 16, 16, 8),
              child: Text(
                'NAVEGACIÓN PRINCIPAL',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              context,
              icon: Icons.home,
              title: 'Inicio',
              route: '/home',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                if (currentRoute != '/home') {
                  Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (context) => const HomeScreen()),
                  );
                }
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.event,
              title: 'Eventos',
              route: '/ong/eventos',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const EventosOngScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.add_circle,
              title: 'Crear Evento',
              route: '/ong/eventos/crear',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const CrearEventoScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.people,
              title: 'Voluntarios',
              route: '/ong/voluntarios',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const VoluntariosOngScreen(),
                  ),
                );
              },
            ),

            _buildDrawerItem(
              context,
              icon: Icons.bar_chart,
              title: 'Reportes',
              route: '/ong/reportes',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const ReportesOngScreen(),
                  ),
                );
              },
            ),

            // OTRAS OPCIONES
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 24, 16, 8),
              child: Text(
                'OTRAS OPCIONES',
                style: TextStyle(
                  color: Colors.white54,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ),

            _buildDrawerItem(
              context,
              icon: Icons.public,
              title: 'Ir a página pública',
              route: '/publica',
              currentRoute: currentRoute,
              onTap: () {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Página pública - Próximamente'),
                  ),
                );
              },
            ),

            const Divider(color: Colors.white24),

            // Cerrar sesión
            _buildDrawerItem(
              context,
              icon: Icons.logout,
              title: 'Cerrar sesión',
              route: '/logout',
              currentRoute: currentRoute,
              textColor: Colors.red[300],
              iconColor: Colors.red[300],
              onTap: () async {
                Navigator.pop(context);
                final confirm = await showDialog<bool>(
                  context: context,
                  builder:
                      (context) => AlertDialog(
                        title: const Text('Cerrar sesión'),
                        content: const Text(
                          '¿Estás seguro de que deseas cerrar sesión?',
                        ),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.of(context).pop(false),
                            child: const Text('Cancelar'),
                          ),
                          TextButton(
                            onPressed: () => Navigator.of(context).pop(true),
                            child: const Text('Cerrar sesión'),
                          ),
                        ],
                      ),
                );

                if (confirm == true) {
                  await ApiService.logout();
                  if (!context.mounted) return;
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(
                      builder: (context) => const LoginScreen(),
                    ),
                    (route) => false,
                  );
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildGenericDrawer(
    BuildContext context,
    Map<String, dynamic>? userData,
  ) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          DrawerHeader(
            decoration: BoxDecoration(color: Theme.of(context).primaryColor),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  'UNI2',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.w300,
                  ),
                ),
                if (userData != null) ...[
                  const SizedBox(height: 8),
                  Text(
                    userData['user_name'] as String? ?? '',
                    style: const TextStyle(color: Colors.white70, fontSize: 14),
                  ),
                ],
              ],
            ),
          ),
          ListTile(
            leading: const Icon(Icons.home),
            title: const Text('Inicio'),
            onTap: () {
              Navigator.pop(context);
              if (currentRoute != '/home') {
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(builder: (context) => const HomeScreen()),
                );
              }
            },
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.logout, color: Colors.red),
            title: const Text(
              'Cerrar sesión',
              style: TextStyle(color: Colors.red),
            ),
            onTap: () async {
              Navigator.pop(context);
              final confirm = await showDialog<bool>(
                context: context,
                builder:
                    (context) => AlertDialog(
                      title: const Text('Cerrar sesión'),
                      content: const Text(
                        '¿Estás seguro de que deseas cerrar sesión?',
                      ),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(false),
                          child: const Text('Cancelar'),
                        ),
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(true),
                          child: const Text('Cerrar sesión'),
                        ),
                      ],
                    ),
              );

              if (confirm == true) {
                await ApiService.logout();
                if (!context.mounted) return;
                Navigator.of(context).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
          ),
        ],
      ),
    );
  }

  Widget _buildDrawerItem(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String route,
    String? currentRoute,
    Color? textColor,
    Color? iconColor,
    required VoidCallback onTap,
  }) {
    final isActive = currentRoute == route;
    final itemTextColor =
        textColor ?? (isActive ? Colors.white : Colors.white70);
    final itemIconColor =
        iconColor ?? (isActive ? Colors.white : Colors.white70);
    final backgroundColor =
        isActive ? Colors.white.withOpacity(0.1) : Colors.transparent;

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: ListTile(
        leading: Icon(icon, color: itemIconColor),
        title: Text(
          title,
          style: TextStyle(
            color: itemTextColor,
            fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        onTap: onTap,
      ),
    );
  }
}
