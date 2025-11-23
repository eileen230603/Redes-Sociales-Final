import 'package:flutter/material.dart';
import '../../widgets/app_drawer.dart';

class EstadosEventoScreen extends StatelessWidget {
  const EstadosEventoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Estados de Evento')),
      body: const Center(
        child: Text('Pantalla de Estados de Evento - En desarrollo'),
      ),
    );
  }
}
