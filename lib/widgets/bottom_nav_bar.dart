import 'package:flutter/material.dart';
import '../screens/home_screen.dart';
import '../screens/eventos_list_screen.dart';
import '../screens/mis_eventos_screen.dart';
import '../screens/notificaciones_screen.dart';
import '../screens/perfil_screen.dart';
import '../screens/ong/eventos_ong_screen.dart';
import '../screens/ong/dashboard_ong_completo_screen.dart';
import '../screens/empresa/eventos_patrocinados_screen.dart';
import '../screens/empresa/ayuda_eventos_screen.dart';
import '../services/storage_service.dart';

/// Widget de navegación inferior (Bottom Navigation Bar)
class BottomNavBar extends StatefulWidget {
  final int currentIndex;
  final String? userType;

  const BottomNavBar({super.key, this.currentIndex = 0, this.userType});

  @override
  State<BottomNavBar> createState() => _BottomNavBarState();
}

class _BottomNavBarState extends State<BottomNavBar> {
  late int _currentIndex;
  String? _userType;

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.currentIndex;
    _loadUserType();
  }

  Future<void> _loadUserType() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _userType = userData?['user_type'] as String?;
    });
  }

  void _onItemTapped(int index) {
    if (_currentIndex == index) return; // Ya está en esa pantalla

    setState(() {
      _currentIndex = index;
    });

    final userType = _userType ?? widget.userType;

    Widget? targetScreen;

    // Navegación según tipo de usuario
    if (userType == 'ONG') {
      switch (index) {
        case 0:
          targetScreen = const HomeScreen();
          break;
        case 1:
          targetScreen = const EventosOngScreen();
          break;
        case 2:
          targetScreen = const DashboardOngCompletoScreen();
          break;
        case 3:
          targetScreen = const NotificacionesScreen();
          break;
        case 4:
          targetScreen = const PerfilScreen();
          break;
      }
    } else if (userType == 'Empresa') {
      switch (index) {
        case 0:
          targetScreen = const HomeScreen();
          break;
        case 1:
          targetScreen = const EventosPatrocinadosScreen();
          break;
        case 2:
          targetScreen = const AyudaEventosScreen();
          break;
        case 3:
          targetScreen = const PerfilScreen();
          break;
      }
    } else {
      // Integrante externo u otros
      switch (index) {
        case 0:
          targetScreen = const HomeScreen();
          break;
        case 1:
          targetScreen = const EventosListScreen();
          break;
        case 2:
          targetScreen = const MisEventosScreen();
          break;
        case 3:
          targetScreen = const PerfilScreen();
          break;
      }
    }

    if (targetScreen != null) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => targetScreen!),
      );
    }
  }

  List<NavigationDestination> _buildDestinations() {
    final userType = _userType ?? widget.userType;

    if (userType == 'ONG') {
      return const [
        NavigationDestination(
          icon: Icon(Icons.home_outlined),
          selectedIcon: Icon(Icons.home),
          label: 'Inicio',
        ),
        NavigationDestination(
          icon: Icon(Icons.event_outlined),
          selectedIcon: Icon(Icons.event),
          label: 'Eventos',
        ),
        NavigationDestination(
          icon: Icon(Icons.dashboard_outlined),
          selectedIcon: Icon(Icons.dashboard),
          label: 'Dashboard',
        ),
        NavigationDestination(
          icon: Icon(Icons.notifications_outlined),
          selectedIcon: Icon(Icons.notifications),
          label: 'Notificaciones',
        ),
        NavigationDestination(
          icon: Icon(Icons.person_outline),
          selectedIcon: Icon(Icons.person),
          label: 'Perfil',
        ),
      ];
    } else if (userType == 'Empresa') {
      return const [
        NavigationDestination(
          icon: Icon(Icons.home_outlined),
          selectedIcon: Icon(Icons.home),
          label: 'Inicio',
        ),
        NavigationDestination(
          icon: Icon(Icons.business_outlined),
          selectedIcon: Icon(Icons.business),
          label: 'Patrocinados',
        ),
        NavigationDestination(
          icon: Icon(Icons.favorite_border),
          selectedIcon: Icon(Icons.favorite),
          label: 'Ayudar',
        ),
        NavigationDestination(
          icon: Icon(Icons.person_outline),
          selectedIcon: Icon(Icons.person),
          label: 'Perfil',
        ),
      ];
    } else {
      // Integrante externo u otros
      return const [
        NavigationDestination(
          icon: Icon(Icons.home_outlined),
          selectedIcon: Icon(Icons.home),
          label: 'Inicio',
        ),
        NavigationDestination(
          icon: Icon(Icons.calendar_today_outlined),
          selectedIcon: Icon(Icons.calendar_today),
          label: 'Eventos',
        ),
        NavigationDestination(
          icon: Icon(Icons.event_note_outlined),
          selectedIcon: Icon(Icons.event_note),
          label: 'Mis Eventos',
        ),
        NavigationDestination(
          icon: Icon(Icons.person_outline),
          selectedIcon: Icon(Icons.person),
          label: 'Perfil',
        ),
      ];
    }
  }

  @override
  Widget build(BuildContext context) {
    return NavigationBarTheme(
      data: NavigationBarThemeData(
        labelTextStyle: MaterialStateProperty.all(
          const TextStyle(fontSize: 11, fontWeight: FontWeight.w500),
        ),
      ),
      child: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: _onItemTapped,
        destinations: _buildDestinations(),
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        surfaceTintColor: Colors.transparent,
        height: 72,
      ),
    );
  }
}
