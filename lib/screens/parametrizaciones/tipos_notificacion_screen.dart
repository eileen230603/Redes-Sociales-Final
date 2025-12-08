import 'package:flutter/material.dart';
import '../../widgets/app_drawer.dart';

class TiposNotificacionScreen extends StatelessWidget {
  const TiposNotificacionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Tipos de Notificación')),
      body: const Center(
        child: Text('Pantalla de Tipos de Notificación - En desarrollo'),
      ),
    );
  }
}
