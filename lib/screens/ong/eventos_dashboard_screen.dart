import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../services/auth_helper.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';
import '../../utils/image_helper.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../evento_detail_screen.dart';
import 'dashboard_evento_screen.dart';

class EventosDashboardScreen extends StatefulWidget {
  const EventosDashboardScreen({super.key});

  @override
  State<EventosDashboardScreen> createState() => _EventosDashboardScreenState();
}

class _EventosDashboardScreenState extends State<EventosDashboardScreen> {
  List<Evento> _eventos = [];
  Map<String, dynamic>? _estadisticas;
  bool _isLoading = true;
  String? _error;
  int? _ongId;
  String _filtroEstado = 'todos';
  final TextEditingController _buscarController = TextEditingController();

  final List<Map<String, dynamic>> _estados = [
    {
      'codigo': 'todos',
      'nombre': 'Todos',
      'icon': Icons.list,
      'color': Colors.grey,
    },
    {
      'codigo': 'activos',
      'nombre': 'Activos',
      'icon': Icons.play_circle,
      'color': Colors.green,
    },
    {
      'codigo': 'proximos',
      'nombre': 'Próximos',
      'icon': Icons.schedule,
      'color': Colors.blue,
    },
    {
      'codigo': 'finalizados',
      'nombre': 'Finalizados',
      'icon': Icons.check_circle,
      'color': Colors.orange,
    },
    {
      'codigo': 'borradores',
      'nombre': 'Borradores',
      'icon': Icons.edit,
      'color': Colors.grey,
    },
    {
      'codigo': 'cancelados',
      'nombre': 'Cancelados',
      'icon': Icons.cancel,
      'color': Colors.red,
    },
  ];

  @override
  void initState() {
    super.initState();
    _loadOngId();
  }

  @override
  void dispose() {
    _buscarController.dispose();
    super.dispose();
  }

  Future<void> _loadOngId() async {
    // Usar AuthHelper para obtener ONG ID con validación y reintento
    final ongId = await AuthHelper.getOngIdWithRetry();
    setState(() {
      _ongId = ongId;
    });
    if (_ongId != null) {
      _loadDatos();
    } else {
      setState(() {
        _isLoading = false;
        _error =
            'No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.';
      });
    }
  }

  Future<void> _loadDatos() async {
    if (_ongId == null) return;

    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getDashboardEventosPorEstado(
      _ongId!,
      estado: _filtroEstado == 'todos' ? null : _filtroEstado,
      buscar:
          _buscarController.text.trim().isEmpty
              ? null
              : _buscarController.text.trim(),
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _eventos = result['eventos'] as List<Evento>;
        _estadisticas = result['estadisticas'] as Map<String, dynamic>?;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
        _eventos = [];
      }
    });
  }

  String _getEstadoDinamico(Evento evento) {
    final ahora = DateTime.now();

    if (evento.estado == 'cancelado') {
      return 'cancelado';
    }
    if (evento.estado == 'borrador') {
      return 'borrador';
    }

    if (evento.fechaInicio.isAfter(ahora)) {
      return 'proximo';
    } else if (evento.fechaFin != null && evento.fechaFin!.isBefore(ahora)) {
      return 'finalizado';
    } else {
      return 'activo';
    }
  }

  Color _getColorEstado(String estado) {
    switch (estado) {
      case 'activo':
        return Colors.green;
      case 'proximo':
        return Colors.blue;
      case 'finalizado':
        return Colors.orange;
      case 'borrador':
        return Colors.grey;
      case 'cancelado':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getIconoEstado(String estado) {
    switch (estado) {
      case 'activo':
        return Icons.play_circle;
      case 'proximo':
        return Icons.schedule;
      case 'finalizado':
        return Icons.check_circle;
      case 'borrador':
        return Icons.edit;
      case 'cancelado':
        return Icons.cancel;
      default:
        return Icons.help;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos-dashboard'),
      appBar: AppBar(
        title: const Text('Dashboard de Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDatos,
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
                    Icon(
                      Icons.error_outline,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      _error!,
                      style: TextStyle(color: Colors.grey[600]),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadDatos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : Column(
                children: [
                  // Búsqueda
                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: TextField(
                      controller: _buscarController,
                      decoration: InputDecoration(
                        hintText: 'Buscar eventos...',
                        prefixIcon: const Icon(Icons.search),
                        suffixIcon:
                            _buscarController.text.isNotEmpty
                                ? IconButton(
                                  icon: const Icon(Icons.clear),
                                  onPressed: () {
                                    _buscarController.clear();
                                    _loadDatos();
                                  },
                                )
                                : null,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onSubmitted: (_) => _loadDatos(),
                    ),
                  ),

                  // Métricas por estado
                  if (_estadisticas != null) _buildMetricasEstados(),

                  // Filtros de estado
                  _buildFiltrosEstado(),

                  const Divider(height: 1),

                  // Lista de eventos
                  Expanded(
                    child: RefreshIndicator(
                      onRefresh: _loadDatos,
                      child:
                          _eventos.isEmpty
                              ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(
                                      Icons.event_busy,
                                      size: 64,
                                      color: Colors.grey[400],
                                    ),
                                    const SizedBox(height: 16),
                                    Text(
                                      'No hay eventos',
                                      style: TextStyle(color: Colors.grey[600]),
                                    ),
                                  ],
                                ),
                              )
                              : ListView.builder(
                                padding: const EdgeInsets.all(16),
                                itemCount: _eventos.length,
                                itemBuilder: (context, index) {
                                  return _buildEventoCard(_eventos[index]);
                                },
                              ),
                    ),
                  ),
                ],
              ),
    );
  }

  Widget _buildMetricasEstados() {
    if (_estadisticas == null) return const SizedBox.shrink();

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      color: Colors.grey[100],
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildMiniMetrica(
            'Total',
            _estadisticas!['total']?.toString() ?? '0',
            Colors.blue,
          ),
          _buildMiniMetrica(
            'Activos',
            _estadisticas!['activos']?.toString() ?? '0',
            Colors.green,
          ),
          _buildMiniMetrica(
            'Próximos',
            _estadisticas!['proximos']?.toString() ?? '0',
            Colors.blue,
          ),
          _buildMiniMetrica(
            'Finalizados',
            _estadisticas!['finalizados']?.toString() ?? '0',
            Colors.orange,
          ),
        ],
      ),
    );
  }

  Widget _buildMiniMetrica(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 4),
        Text(label, style: TextStyle(fontSize: 11, color: Colors.grey[600])),
      ],
    );
  }

  Widget _buildFiltrosEstado() {
    return Container(
      height: 60,
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 8),
        itemCount: _estados.length,
        itemBuilder: (context, index) {
          final estado = _estados[index];
          final isSelected = _filtroEstado == estado['codigo'];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 4),
            child: FilterChip(
              selected: isSelected,
              label: Text(estado['nombre'] as String),
              avatar: Icon(
                estado['icon'] as IconData,
                size: 18,
                color: isSelected ? Colors.white : estado['color'] as Color,
              ),
              selectedColor: estado['color'] as Color,
              checkmarkColor: Colors.white,
              labelStyle: TextStyle(
                color: isSelected ? Colors.white : Colors.black87,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              ),
              onSelected: (selected) {
                if (selected) {
                  setState(() {
                    _filtroEstado = estado['codigo'] as String;
                  });
                  _loadDatos();
                }
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    final estadoDinamico = _getEstadoDinamico(evento);
    final colorEstado = _getColorEstado(estadoDinamico);
    final iconoEstado = _getIconoEstado(estadoDinamico);
    final primeraImagen = ImageHelper.getFirstImageUrl(evento.imagenes);

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => EventoDetailScreen(eventoId: evento.id),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen
            if (primeraImagen != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(12),
                ),
                child: CachedNetworkImage(
                  imageUrl: primeraImagen,
                  height: 180,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder:
                      (context, url) => Container(
                        height: 180,
                        color: Colors.grey[300],
                        child: const Center(child: CircularProgressIndicator()),
                      ),
                  errorWidget:
                      (context, url, error) => Container(
                        height: 180,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported, size: 48),
                      ),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Título y estado
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          evento.titulo,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Chip(
                        avatar: Icon(iconoEstado, size: 16, color: colorEstado),
                        label: Text(
                          estadoDinamico.toUpperCase(),
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: colorEstado,
                          ),
                        ),
                        backgroundColor: colorEstado.withOpacity(0.1),
                        padding: const EdgeInsets.symmetric(horizontal: 8),
                      ),
                    ],
                  ),

                  const SizedBox(height: 8),

                  // Descripción
                  if (evento.descripcion != null &&
                      evento.descripcion!.isNotEmpty)
                    Text(
                      evento.descripcion!,
                      style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),

                  const SizedBox(height: 12),

                  // Información del evento
                  Row(
                    children: [
                      Icon(
                        Icons.calendar_today,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 4),
                      Text(
                        _formatFecha(evento.fechaInicio),
                        style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                      ),
                      const SizedBox(width: 16),
                      Icon(
                        Icons.location_on,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          evento.ciudad ??
                              evento.direccion ??
                              'Ubicación no especificada',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),

                  if (evento.capacidadMaxima != null) ...[
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.people, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          'Capacidad: ${evento.capacidadMaxima}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ],
                  const SizedBox(height: 12),
                  // Botones de acción
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder:
                                    (context) => DashboardEventoScreen(
                                      eventoId: evento.id,
                                      eventoTitulo: evento.titulo,
                                    ),
                              ),
                            );
                          },
                          icon: const Icon(Icons.dashboard, size: 18),
                          label: const Text('Dashboard'),
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 8),
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder:
                                    (context) =>
                                        EventoDetailScreen(eventoId: evento.id),
                              ),
                            );
                          },
                          icon: const Icon(Icons.info, size: 18),
                          label: const Text('Detalles'),
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 8),
                          ),
                        ),
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

  String _formatFecha(DateTime fecha) {
    final meses = [
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
    return '${fecha.day} ${meses[fecha.month - 1]} ${fecha.year}';
  }
}
