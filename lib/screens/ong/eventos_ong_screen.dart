import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';
import '../../config/api_config.dart';
import '../evento_detail_screen.dart';

class EventosOngScreen extends StatefulWidget {
  const EventosOngScreen({super.key});

  @override
  State<EventosOngScreen> createState() => _EventosOngScreenState();
}

class _EventosOngScreenState extends State<EventosOngScreen> {
  List<Evento> _eventos = [];
  bool _isLoading = true;
  String? _error;
  int? _ongId;

  @override
  void initState() {
    super.initState();
    _loadOngId();
    _loadEventos();
  }

  Future<void> _loadOngId() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _ongId = userData?['entity_id'] as int?;
    });
  }

  Future<void> _loadEventos() async {
    if (_ongId == null) {
      setState(() {
        _isLoading = false;
        _error = 'No se pudo identificar la ONG';
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventosOng(_ongId!);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _eventos = result['eventos'] as List<Evento>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  Future<void> _eliminarEvento(Evento evento) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Eliminar Evento'),
            content: Text(
              '¿Estás seguro de que deseas eliminar el evento "${evento.titulo}"?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.of(context).pop(true),
                style: TextButton.styleFrom(foregroundColor: Colors.red),
                child: const Text('Eliminar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result = await ApiService.eliminarEvento(evento.id);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Evento eliminado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      _loadEventos();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al eliminar evento',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos'),
      appBar: AppBar(
        title: const Text('Mis Eventos'),
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
                      'No tienes eventos creados',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Crea tu primer evento',
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
    if (evento.imagenes != null && evento.imagenes!.isNotEmpty) {
      // Obtener la primera imagen válida
      for (var img in evento.imagenes!) {
        if (img != null) {
          final imgPath = img.toString().trim();
          // Validar que la ruta sea válida
          if (imgPath.isNotEmpty &&
              imgPath != 'null' &&
              imgPath != '[]' &&
              !imgPath.startsWith('[') &&
              !imgPath.startsWith('{')) {
            final url = _getImageUrl(imgPath);
            if (url != null && url.isNotEmpty) {
              imagenUrl = url;
              // Debug: imprimir la URL construida
              print('🖼️ Imagen del evento ${evento.id}:');
              print('   Ruta original: $imgPath');
              print('   URL construida: $imagenUrl');
              break; // Usar la primera imagen válida
            }
          }
        }
      }
    }

    // Debug: si no hay imagen, mostrar info
    if (imagenUrl == null && evento.imagenes != null) {
      print('⚠️ Evento ${evento.id} sin imagen válida:');
      print('   imagenes: ${evento.imagenes}');
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
                  CachedNetworkImage(
                    imageUrl: imagenUrl,
                    height: 200,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    placeholder:
                        (context, url) => Container(
                          height: 200,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: const Center(
                            child: CircularProgressIndicator(),
                          ),
                        ),
                    errorWidget:
                        (context, url, error) => Container(
                          height: 200,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: Icon(
                            Icons.image_not_supported,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                        ),
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
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              evento.titulo,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color:
                                  evento.estado == 'publicado'
                                      ? Colors.green[100]
                                      : evento.estado == 'cancelado'
                                      ? Colors.red[100]
                                      : Colors.grey[300],
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              evento.estado.toUpperCase(),
                              style: TextStyle(
                                fontSize: 12,
                                color:
                                    evento.estado == 'publicado'
                                        ? Colors.green[800]
                                        : evento.estado == 'cancelado'
                                        ? Colors.red[800]
                                        : Colors.grey[800],
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
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
            child: Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder:
                              (context) =>
                                  EventoDetailScreen(eventoId: evento.id),
                        ),
                      ).then((_) => _loadEventos());
                    },
                    icon: const Icon(Icons.visibility),
                    label: const Text('Ver detalles'),
                  ),
                ),
                const SizedBox(width: 8),
                IconButton(
                  onPressed: () => _eliminarEvento(evento),
                  icon: const Icon(Icons.delete),
                  color: Colors.red,
                  tooltip: 'Eliminar',
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  String? _getImageUrl(String imgPath) {
    if (imgPath.isEmpty || imgPath == 'null') return null;

    // Si ya es una URL completa, retornarla
    if (imgPath.startsWith('http://') || imgPath.startsWith('https://')) {
      return imgPath;
    }

    // Construir URL base (remover /api del final)
    final apiBaseUrl = ApiConfig.baseUrl;
    final baseUrl = apiBaseUrl
        .replaceAll('/api', '')
        .replaceAll(RegExp(r'/$'), '');

    // Normalizar la ruta de la imagen
    String normalizedPath = imgPath;

    // Si la ruta ya empieza con /storage, convertirla a /api/storage para CORS
    if (normalizedPath.startsWith('/storage')) {
      return '$baseUrl/api$normalizedPath';
    }

    // Si empieza con storage/ (sin /), convertirla a /api/storage/
    if (normalizedPath.startsWith('storage/')) {
      return '$baseUrl/api/$normalizedPath';
    }

    // Si no empieza con /, agregarlo
    if (!normalizedPath.startsWith('/')) {
      normalizedPath = '/$normalizedPath';
    }

    return '$baseUrl$normalizedPath';
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
