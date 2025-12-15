import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/molecules/empty_state.dart';

class EstadosParticipacionScreen extends StatelessWidget {
  const EstadosParticipacionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Estados de Participación')),
      body: const EmptyState(
        icon: Icons.how_to_reg,
        title: 'Estados de Participación',
        message: 'Pantalla en desarrollo.',
      ),
    );
  }
}
