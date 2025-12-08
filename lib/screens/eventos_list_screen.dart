import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/evento.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../utils/image_helper.dart';
import '../utils/navigation_helper.dart';
import 'evento_detail_screen.dart';

class EventosListScreen extends StatefulWidget {
  const EventosListScreen({super.key});

  @override
  State<EventosListScreen> createState() => _EventosListScreenState();
}

class _EventosListScreenState extends State<EventosListScreen> {
  List<Evento> _eventos = [];
  bool _isLoading = true;
  String? _error;
  // Mapa para almacenar estado de reacciones por evento
  Map<int, bool> _eventosReaccionados = {}; // eventoId -> reaccionado
  Map<int, int> _totalReaccionesPorEvento = {}; // eventoId -> total

  @override
  void initState() {
    super.initState();
    _loadEventos();
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
        _eventos = result['eventos'] as List<Evento>;
        // Verificar reacciones para cada evento
        _verificarReacciones();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  Future<void> _verificarReacciones() async {
    Map<int, bool> eventosReaccionadosMap = {};
    Map<int, int> totalReaccionesMap = {};

    for (final evento in _eventos) {
      final reaccionResult = await ApiService.verificarReaccion(evento.id);
      if (reaccionResult['success'] == true) {
        eventosReaccionadosMap[evento.id] =
            reaccionResult['reaccionado'] as bool? ?? false;
        totalReaccionesMap[evento.id] =
            reaccionResult['total_reacciones'] as int? ?? 0;
      }
    }

    if (!mounted) return;

    setState(() {
      _eventosReaccionados = eventosReaccionadosMap;
      _totalReaccionesPorEvento = totalReaccionesMap;
    });
  }

  Future<void> _toggleReaccionEnCard(int eventoId) async {
    final result = await ApiService.toggleReaccion(eventoId);

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _eventosReaccionados[eventoId] =
            result['reaccionado'] as bool? ?? false;
        _totalReaccionesPorEvento[eventoId] =
            result['total_reacciones'] as int? ?? 0;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/eventos'),
      appBar: AppBar(
        title: const Text('Eventos Disponibles'),
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
                      'No hay eventos disponibles',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
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
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 1, userType: userType);
        },
      ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    // Obtener la primera imagen usando el helper
    final imagenUrl = ImageHelper.getFirstImageUrl(evento.imagenes);

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      clipBehavior: Clip.antiAlias,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            NavigationHelper.slideRightRoute(
              EventoDetailScreen(eventoId: evento.id),
            ),
          ).then((_) => _loadEventos());
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen del evento con botón de reacción
            Stack(
              children: [
                if (imagenUrl != null)
                  CachedNetworkImage(
                    imageUrl: imagenUrl,
                    height: 180,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    placeholder:
                        (context, url) => Container(
                          height: 180,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: const Center(
                            child: CircularProgressIndicator(),
                          ),
                        ),
                    errorWidget:
                        (context, url, error) => Container(
                          height: 180,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: Icon(
                            Icons.image_not_supported,
                            size: 48,
                            color: Colors.grey[400],
                          ),
                        ),
                  )
                else
                  Container(
                    height: 180,
                    width: double.infinity,
                    color: Colors.grey[200],
                    child: Icon(Icons.event, size: 64, color: Colors.grey[400]),
                  ),
                // Botón de reacción
                Positioned(
                  top: 12,
                  right: 12,
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.9),
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.2),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: IconButton(
                      icon: Icon(
                        _eventosReaccionados[evento.id] == true
                            ? Icons.favorite
                            : Icons.favorite_border,
                        color:
                            _eventosReaccionados[evento.id] == true
                                ? Colors.red
                                : Colors.grey[700],
                        size: 24,
                      ),
                      onPressed: () => _toggleReaccionEnCard(evento.id),
                      tooltip:
                          _eventosReaccionados[evento.id] == true
                              ? 'Quitar reacción'
                              : 'Reaccionar',
                    ),
                  ),
                ),
                // Contador de reacciones
                if ((_totalReaccionesPorEvento[evento.id] ?? 0) > 0)
                  Positioned(
                    bottom: 12,
                    right: 12,
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.9),
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.2),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(
                            Icons.favorite,
                            color: Colors.red,
                            size: 16,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            '${_totalReaccionesPorEvento[evento.id] ?? 0}',
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),
            // Contenido del card
            Padding(
              padding: const EdgeInsets.all(16),
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
                      if (!evento.puedeInscribirse)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Text(
                            'Cerrado',
                            style: TextStyle(fontSize: 12),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(Icons.category, size: 16, color: Colors.grey[600]),
                      const SizedBox(width: 4),
                      Text(
                        evento.tipoEvento,
                        style: TextStyle(color: Colors.grey[600]),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
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
                    const SizedBox(height: 4),
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
                    const SizedBox(height: 8),
                    Text(
                      evento.descripcion!,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[700]),
                    ),
                  ],
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        'Ver detalles',
                        style: TextStyle(
                          color: Theme.of(context).primaryColor,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Icon(
                        Icons.arrow_forward_ios,
                        size: 16,
                        color: Theme.of(context).primaryColor,
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
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
