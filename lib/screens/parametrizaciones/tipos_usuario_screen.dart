import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/molecules/empty_state.dart';

class TiposUsuarioScreen extends StatelessWidget {
  const TiposUsuarioScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Tipos de Usuario')),
      body: const EmptyState(
        icon: Icons.people,
        title: 'Tipos de Usuario',
        message: 'Pantalla en desarrollo.',
      ),
    );
  }
}
