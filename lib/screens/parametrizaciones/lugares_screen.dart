import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/molecules/empty_state.dart';

class LugaresScreen extends StatelessWidget {
  const LugaresScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Lugares')),
      body: const EmptyState(
        icon: Icons.place,
        title: 'Lugares',
        message: 'Pantalla en desarrollo.',
      ),
    );
  }
}
