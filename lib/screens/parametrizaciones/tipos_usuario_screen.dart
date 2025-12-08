import 'package:flutter/material.dart';
import '../../widgets/app_drawer.dart';

class TiposUsuarioScreen extends StatelessWidget {
  const TiposUsuarioScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Tipos de Usuario')),
      body: const Center(
        child: Text('Pantalla de Tipos de Usuario - En desarrollo'),
      ),
    );
  }
}
