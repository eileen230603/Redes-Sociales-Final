import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/molecules/empty_state.dart';

class EstadosEventoScreen extends StatelessWidget {
  const EstadosEventoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Estados de Evento')),
      body: const EmptyState(
        icon: Icons.tune,
        title: 'Estados de Evento',
        message: 'Pantalla en desarrollo.',
      ),
    );
  }
}
