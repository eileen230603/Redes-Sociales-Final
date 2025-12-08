import 'package:flutter/material.dart';
import '../../widgets/app_drawer.dart';

class EstadosParticipacionScreen extends StatelessWidget {
  const EstadosParticipacionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Estados de Participación')),
      body: const Center(
        child: Text('Pantalla de Estados de Participación - En desarrollo'),
      ),
    );
  }
}
