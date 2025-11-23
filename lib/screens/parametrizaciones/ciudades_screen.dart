import 'package:flutter/material.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/ciudad.dart';
import '../../widgets/app_drawer.dart';

class CiudadesScreen extends StatefulWidget {
  const CiudadesScreen({super.key});

  @override
  State<CiudadesScreen> createState() => _CiudadesScreenState();
}

class _CiudadesScreenState extends State<CiudadesScreen> {
  List<Ciudad> _ciudades = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _cargarCiudades();
  }

  Future<void> _cargarCiudades() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ParametrizacionService.getCiudades();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _ciudades = result['ciudades'] as List<Ciudad>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar ciudades';
        _ciudades = [];
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(
        title: const Text('Ciudades'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _cargarCiudades,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body:
          _isLoading
              ? const Center(child: CircularProgressIndicator())
              : _error != null
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                    const SizedBox(height: 16),
                    Text(
                      _error!,
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.red[700]),
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _cargarCiudades,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _ciudades.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.location_city,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay ciudades disponibles',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _cargarCiudades,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _ciudades.length,
                  itemBuilder: (context, index) {
                    final ciudad = _ciudades[index];
                    return _buildCiudadCard(ciudad);
                  },
                ),
              ),
    );
  }

  Widget _buildCiudadCard(Ciudad ciudad) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      elevation: 2,
      child: ListTile(
        leading: const CircleAvatar(
          backgroundColor: Colors.green,
          child: Icon(Icons.location_city, color: Colors.white),
        ),
        title: Text(
          ciudad.nombre,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Text(ciudad.nombreCompleto),
        trailing:
            ciudad.activo
                ? Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.green[100],
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    'Activa',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.green[800],
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                )
                : null,
      ),
    );
  }
}
