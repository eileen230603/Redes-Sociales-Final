import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/molecules/empty_state.dart';

class TiposNotificacionScreen extends StatelessWidget {
  const TiposNotificacionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Tipos de Notificación')),
      body: const EmptyState(
        icon: Icons.notifications,
        title: 'Tipos de Notificación',
        message: 'Pantalla en desarrollo.',
      ),
    );
  }
}
