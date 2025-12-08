import 'package:flutter/material.dart';
import '../../widgets/app_drawer.dart';

class LugaresScreen extends StatelessWidget {
  const LugaresScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(title: const Text('Lugares')),
      body: const Center(child: Text('Pantalla de Lugares - En desarrollo')),
    );
  }
}
