import 'package:flutter/material.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/tipo_evento.dart';
import '../../widgets/app_drawer.dart';

class TiposEventoScreen extends StatefulWidget {
  const TiposEventoScreen({super.key});

  @override
  State<TiposEventoScreen> createState() => _TiposEventoScreenState();
}

class _TiposEventoScreenState extends State<TiposEventoScreen> {
  List<TipoEvento> _tipos = [];
  bool _isLoading = true;
  String? _error;
  bool _soloActivos = true;

  @override
  void initState() {
    super.initState();
    _cargarTipos();
  }

  Future<void> _cargarTipos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ParametrizacionService.getTiposEvento(
      activo: _soloActivos ? true : null,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _tipos = result['tipos'] as List<TipoEvento>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar tipos';
        _tipos = [];
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(
        title: const Text('Tipos de Evento'),
        actions: [
          Switch(
            value: _soloActivos,
            onChanged: (value) {
              setState(() {
                _soloActivos = value;
              });
              _cargarTipos();
            },
          ),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 8),
            child: Center(
              child: Text('Solo activos', style: TextStyle(fontSize: 12)),
            ),
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _cargarTipos,
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
                      onPressed: _cargarTipos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _tipos.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
                    const SizedBox(height: 16),
                    Text(
                      'No hay tipos de evento disponibles',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _cargarTipos,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _tipos.length,
                  itemBuilder: (context, index) {
                    final tipo = _tipos[index];
                    return _buildTipoCard(tipo);
                  },
                ),
              ),
    );
  }

  Widget _buildTipoCard(TipoEvento tipo) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      elevation: 2,
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor:
              tipo.color != null
                  ? _parseColor(tipo.color!)
                  : Theme.of(context).primaryColor,
          child: Icon(_getIconFromString(tipo.icono), color: Colors.white),
        ),
        title: Text(
          tipo.nombre,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (tipo.descripcion != null) Text(tipo.descripcion!),
            Text('Código: ${tipo.codigo}'),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: tipo.activo ? Colors.green[100] : Colors.grey[300],
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    tipo.activo ? 'Activo' : 'Inactivo',
                    style: TextStyle(
                      fontSize: 12,
                      color: tipo.activo ? Colors.green[800] : Colors.grey[800],
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  'Orden: ${tipo.orden}',
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
          ],
        ),
        trailing: Icon(Icons.arrow_forward_ios, size: 16),
        onTap: () {
          // Aquí se podría abrir un diálogo para editar
          _mostrarDetallesTipo(tipo);
        },
      ),
    );
  }

  void _mostrarDetallesTipo(TipoEvento tipo) {
    showDialog(
      context: context,
      builder:
          (context) => AlertDialog(
            title: Text(tipo.nombre),
            content: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  _buildInfoRow('Código', tipo.codigo),
                  if (tipo.descripcion != null)
                    _buildInfoRow('Descripción', tipo.descripcion!),
                  _buildInfoRow('Icono', tipo.icono ?? 'Sin icono'),
                  _buildInfoRow('Color', tipo.color ?? 'Sin color'),
                  _buildInfoRow('Orden', tipo.orden.toString()),
                  _buildInfoRow('Estado', tipo.activo ? 'Activo' : 'Inactivo'),
                ],
              ),
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Cerrar'),
              ),
            ],
          ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              '$label:',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  Color _parseColor(String colorString) {
    try {
      // Si es un código hex
      if (colorString.startsWith('#')) {
        return Color(
          int.parse(colorString.substring(1), radix: 16) + 0xFF000000,
        );
      }
      // Si es un nombre de color común
      switch (colorString.toLowerCase()) {
        case 'blue':
          return Colors.blue;
        case 'green':
          return Colors.green;
        case 'red':
          return Colors.red;
        case 'orange':
          return Colors.orange;
        case 'purple':
          return Colors.purple;
        default:
          return Theme.of(context).primaryColor;
      }
    } catch (e) {
      return Theme.of(context).primaryColor;
    }
  }

  IconData _getIconFromString(String? iconString) {
    if (iconString == null) return Icons.event;
    // Mapeo básico de iconos comunes
    switch (iconString.toLowerCase()) {
      case 'conference':
      case 'conferencia':
        return Icons.business_center;
      case 'taller':
      case 'workshop':
        return Icons.build;
      case 'seminario':
      case 'seminar':
        return Icons.school;
      case 'voluntariado':
      case 'volunteer':
        return Icons.volunteer_activism;
      case 'cultural':
        return Icons.palette;
      case 'deportivo':
      case 'sport':
        return Icons.sports_soccer;
      default:
        return Icons.event;
    }
  }
}
