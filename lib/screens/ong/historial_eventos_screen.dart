import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';
import '../../utils/image_helper.dart';
import '../evento_detail_screen.dart';

class HistorialEventosScreen extends StatefulWidget {
  const HistorialEventosScreen({super.key});

  @override
  State<HistorialEventosScreen> createState() => _HistorialEventosScreenState();
}

class _HistorialEventosScreenState extends State<HistorialEventosScreen> {
  List<Evento> _eventos = [];
  bool _isLoading = true;
  String? _error;
  int? _ongId;
  String _filtroTipo = 'todos';
  String _filtroOrden = 'recientes';
  final TextEditingController _buscarController = TextEditingController();

  // Tipos de evento disponibles
  final List<Map<String, dynamic>> _tiposEvento = [
    {'codigo': 'todos', 'nombre': 'Todos los tipos'},
    {'codigo': 'conferencia', 'nombre': 'Conferencia'},
    {'codigo': 'taller', 'nombre': 'Taller'},
    {'codigo': 'seminario', 'nombre': 'Seminario'},
    {'codigo': 'voluntariado', 'nombre': 'Voluntariado'},
    {'codigo': 'cultural', 'nombre': 'Cultural'},
    {'codigo': 'deportivo', 'nombre': 'Deportivo'},
    {'codigo': 'otro', 'nombre': 'Otro'},
  ];

  @override
  void initState() {
    super.initState();
    _loadEventos();
  }

  @override
  void dispose() {
    _buscarController.dispose();
    super.dispose();
  }

  Future<void> _loadEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final ongId = _ongId ?? await AuthHelper.getOngIdWithRetry();

    if (!mounted) return;

    if (ongId == null) {
      setState(() {
        _isLoading = false;
        _error =
            'No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.';
      });
      return;
    }

    setState(() {
      _ongId = ongId;
    });

    try {
      // Obtener todos los eventos de la ONG
      final result = await ApiService.getEventosOng(ongId);

      if (!mounted) return;

      if (result['success'] == true) {
        final todosEventos = result['eventos'] as List<Evento>;

        // Filtrar solo eventos finalizados
        final ahora = DateTime.now();
        final eventosFinalizados =
            todosEventos.where((evento) {
              // Un evento está finalizado si:
              // 1. Tiene fecha_fin y ya pasó
              // 2. O si fecha_inicio pasó y no tiene fecha_fin (considerar como finalizado después de un tiempo)
              if (evento.fechaFin != null) {
                return evento.fechaFin!.isBefore(ahora);
              }
              // Si no tiene fecha_fin, considerar finalizado si la fecha_inicio pasó hace más de 1 día
              return evento.fechaInicio.isBefore(
                ahora.subtract(const Duration(days: 1)),
              );
            }).toList();

        // Aplicar filtro de tipo
        List<Evento> eventosFiltrados = eventosFinalizados;
        if (_filtroTipo != 'todos') {
          eventosFiltrados =
              eventosFiltrados
                  .where((e) => e.tipoEvento.toLowerCase() == _filtroTipo)
                  .toList();
        }

        // Aplicar búsqueda
        if (_buscarController.text.trim().isNotEmpty) {
          final buscar = _buscarController.text.trim().toLowerCase();
          eventosFiltrados =
              eventosFiltrados
                  .where(
                    (e) =>
                        e.titulo.toLowerCase().contains(buscar) ||
                        (e.descripcion?.toLowerCase().contains(buscar) ??
                            false),
                  )
                  .toList();
        }

        // Ordenar
        if (_filtroOrden == 'recientes') {
          eventosFiltrados.sort((a, b) {
            final fechaA = a.fechaFin ?? a.fechaInicio;
            final fechaB = b.fechaFin ?? b.fechaInicio;
            return fechaB.compareTo(fechaA); // Más recientes primero
          });
        } else {
          eventosFiltrados.sort((a, b) {
            final fechaA = a.fechaFin ?? a.fechaInicio;
            final fechaB = b.fechaFin ?? b.fechaInicio;
            return fechaA.compareTo(fechaB); // Más antiguos primero
          });
        }

        setState(() {
          _eventos = eventosFiltrados;
          _isLoading = false;
        });
      } else {
        setState(() {
          _error = result['error'] as String? ?? 'Error al cargar eventos';
          _isLoading = false;
        });
      }
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = 'Error de conexión: ${e.toString()}';
        _isLoading = false;
      });
    }
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos/historial'),
      appBar: AppBar(
        title: const Text('Historial de Eventos'),
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
              : Column(
                children: [
                  // Filtros
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.grey[100],
                    child: Column(
                      children: [
                        // Filtro por tipo
                        DropdownButtonFormField<String>(
                          value: _filtroTipo,
                          decoration: const InputDecoration(
                            labelText: 'Tipo de Evento',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.category),
                          ),
                          items:
                              _tiposEvento.map((tipo) {
                                return DropdownMenuItem<String>(
                                  value: tipo['codigo'] as String,
                                  child: Text(tipo['nombre'] as String),
                                );
                              }).toList(),
                          onChanged: (value) {
                            if (value != null) {
                              setState(() {
                                _filtroTipo = value;
                              });
                              _loadEventos();
                            }
                          },
                        ),
                        const SizedBox(height: 12),
                        // Filtro de orden
                        DropdownButtonFormField<String>(
                          value: _filtroOrden,
                          decoration: const InputDecoration(
                            labelText: 'Ordenar por',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.sort),
                          ),
                          items: const [
                            DropdownMenuItem(
                              value: 'recientes',
                              child: Text('Más recientes'),
                            ),
                            DropdownMenuItem(
                              value: 'antiguos',
                              child: Text('Más antiguos'),
                            ),
                          ],
                          onChanged: (value) {
                            if (value != null) {
                              setState(() {
                                _filtroOrden = value;
                              });
                              _loadEventos();
                            }
                          },
                        ),
                        const SizedBox(height: 12),
                        // Búsqueda
                        TextField(
                          controller: _buscarController,
                          decoration: InputDecoration(
                            hintText: 'Buscar por título...',
                            prefixIcon: const Icon(Icons.search),
                            suffixIcon:
                                _buscarController.text.isNotEmpty
                                    ? IconButton(
                                      icon: const Icon(Icons.clear),
                                      onPressed: () {
                                        _buscarController.clear();
                                        _loadEventos();
                                      },
                                    )
                                    : null,
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          onSubmitted: (_) => _loadEventos(),
                          onChanged: (_) => setState(() {}),
                        ),
                      ],
                    ),
                  ),
                  // Lista de eventos
                  Expanded(
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
                                    'No hay eventos finalizados',
                                    style: TextStyle(color: Colors.grey[600]),
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
                  ),
                ],
              ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
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
                      child: const Center(child: CircularProgressIndicator()),
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
                          color: Colors.orange[100],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'FINALIZADO',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.orange[800],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
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
                        'Finalizado: ${_formatDate(evento.fechaFin ?? evento.fechaInicio)}',
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
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
