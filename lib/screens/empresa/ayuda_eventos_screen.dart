import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';
import '../../config/api_config.dart';
import '../evento_detail_screen.dart';

class AyudaEventosScreen extends StatefulWidget {
  const AyudaEventosScreen({super.key});

  @override
  State<AyudaEventosScreen> createState() => _AyudaEventosScreenState();
}

class _AyudaEventosScreenState extends State<AyudaEventosScreen> {
  List<Evento> _eventos = [];
  bool _isLoading = true;
  String? _error;
  int? _empresaId;

  @override
  void initState() {
    super.initState();
    _loadEmpresaId();
    _loadEventos();
  }

  Future<void> _loadEmpresaId() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _empresaId = userData?['entity_id'] as int?;
    });
  }

  Future<void> _loadEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventosPublicados();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        final todosEventos = result['eventos'] as List<Evento>;
        // Filtrar eventos donde esta empresa NO es patrocinadora
        _eventos =
            todosEventos.where((evento) {
              if (_empresaId == null) return true;
              if (evento.patrocinadores == null) return true;
              final patrocinadores = evento.patrocinadores as List;
              return !patrocinadores.any(
                (p) => p.toString() == _empresaId.toString() || p == _empresaId,
              );
            }).toList();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  Future<void> _patrocinarEvento(Evento evento) async {
    if (_empresaId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: No se pudo identificar la empresa'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Patrocinar Evento'),
            content: Text('¿Deseas patrocinar el evento "${evento.titulo}"?'),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Patrocinar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result = await ApiService.patrocinarEvento(
      eventoId: evento.id,
      empresaId: _empresaId!,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Evento patrocinado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      _loadEventos();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al patrocinar evento',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/empresa/eventos/disponibles'),
      appBar: AppBar(
        title: const Text('Ayuda a Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadEventos,
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
                      onPressed: _loadEventos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _eventos.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
                    const SizedBox(height: 16),
                    Text(
                      'No hay eventos disponibles para patrocinar',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Todos los eventos ya tienen tu patrocinio',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadEventos,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _eventos.length,
                  itemBuilder: (context, index) {
                    final evento = _eventos[index];
                    return _buildEventoCard(evento);
                  },
                ),
              ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    String? imagenUrl;
    if (evento.imagenes != null &&
        evento.imagenes!.isNotEmpty &&
        evento.imagenes![0] != null) {
      final imgPath = evento.imagenes![0].toString().trim();
      if (imgPath.isNotEmpty) {
        imagenUrl = _getImageUrl(imgPath);
      }
    }

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      clipBehavior: Clip.antiAlias,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          InkWell(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => EventoDetailScreen(eventoId: evento.id),
                ),
              ).then((_) => _loadEventos());
            },
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (imagenUrl != null)
                  Image.network(
                    imagenUrl,
                    height: 200,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) {
                      return Container(
                        height: 200,
                        width: double.infinity,
                        color: Colors.grey[200],
                        child: Icon(
                          Icons.image_not_supported,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                      );
                    },
                    loadingBuilder: (context, child, loadingProgress) {
                      if (loadingProgress == null) return child;
                      return Container(
                        height: 200,
                        width: double.infinity,
                        color: Colors.grey[200],
                        child: Center(
                          child: CircularProgressIndicator(
                            value:
                                loadingProgress.expectedTotalBytes != null
                                    ? loadingProgress.cumulativeBytesLoaded /
                                        loadingProgress.expectedTotalBytes!
                                    : null,
                          ),
                        ),
                      );
                    },
                  )
                else
                  Container(
                    height: 200,
                    width: double.infinity,
                    color: Colors.grey[200],
                    child: Icon(Icons.event, size: 64, color: Colors.grey[400]),
                  ),
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        evento.titulo,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Icon(
                            Icons.category,
                            size: 16,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            evento.tipoEvento,
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(
                            Icons.calendar_today,
                            size: 16,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            _formatDate(evento.fechaInicio),
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ],
                      ),
                      if (evento.ciudad != null) ...[
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(
                              Icons.location_on,
                              size: 16,
                              color: Colors.grey[600],
                            ),
                            const SizedBox(width: 4),
                            Text(
                              evento.ciudad!,
                              style: TextStyle(color: Colors.grey[600]),
                            ),
                          ],
                        ),
                      ],
                      if (evento.descripcion != null &&
                          evento.descripcion!.isNotEmpty) ...[
                        const SizedBox(height: 12),
                        Text(
                          evento.descripcion!,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(color: Colors.grey[700]),
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => _patrocinarEvento(evento),
                icon: const Icon(Icons.favorite),
                label: const Text('Patrocinar Evento'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Theme.of(context).primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  String? _getImageUrl(String imgPath) {
    if (imgPath.isEmpty) return null;
    if (imgPath.startsWith('http://') || imgPath.startsWith('https://')) {
      return imgPath;
    }
    final apiBaseUrl = ApiConfig.baseUrl;
    final baseUrl = apiBaseUrl.replaceAll('/api', '');
    if (imgPath.startsWith('/')) {
      return '$baseUrl$imgPath';
    }
    if (imgPath.startsWith('storage/')) {
      return '$baseUrl/$imgPath';
    }
    return '$baseUrl/$imgPath';
  }

  String _formatDate(DateTime date) {
    final months = [
      'Ene',
      'Feb',
      'Mar',
      'Abr',
      'May',
      'Jun',
      'Jul',
      'Ago',
      'Sep',
      'Oct',
      'Nov',
      'Dic',
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }
}
