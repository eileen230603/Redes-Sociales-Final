import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../models/mega_evento.dart';
import '../../utils/image_helper.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'mega_evento_detail_screen.dart';
import 'mega_evento_seguimiento_screen.dart';
import 'crear_mega_evento_screen.dart';

class MegaEventosListScreen extends StatefulWidget {
  const MegaEventosListScreen({super.key});

  @override
  State<MegaEventosListScreen> createState() => _MegaEventosListScreenState();
}

class _MegaEventosListScreenState extends State<MegaEventosListScreen> {
  List<MegaEvento> _megaEventos = [];
  bool _isLoading = true;
  String? _error;
  String _filtroEstado = 'todos';
  String _filtroCategoria = 'todos';
  final TextEditingController _buscarController = TextEditingController();

  final List<Map<String, dynamic>> _estados = [
    {
      'codigo': 'todos',
      'nombre': 'Todos',
      'icon': Icons.list,
      'color': Colors.grey,
    },
    {
      'codigo': 'planificacion',
      'nombre': 'Planificación',
      'icon': Icons.edit,
      'color': Colors.blue,
    },
    {
      'codigo': 'activo',
      'nombre': 'Activo',
      'icon': Icons.play_circle,
      'color': Colors.green,
    },
    {
      'codigo': 'en_curso',
      'nombre': 'En Curso',
      'icon': Icons.schedule,
      'color': Colors.orange,
    },
    {
      'codigo': 'finalizado',
      'nombre': 'Finalizado',
      'icon': Icons.check_circle,
      'color': Colors.grey,
    },
    {
      'codigo': 'cancelado',
      'nombre': 'Cancelado',
      'icon': Icons.cancel,
      'color': Colors.red,
    },
  ];

  @override
  void initState() {
    super.initState();
    _loadDatos();
  }

  @override
  void dispose() {
    _buscarController.dispose();
    super.dispose();
  }

  Future<void> _loadDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final result = await ApiService.getMegaEventos(
        categoria: _filtroCategoria != 'todos' ? _filtroCategoria : null,
        estado: _filtroEstado != 'todos' ? _filtroEstado : null,
        buscar:
            _buscarController.text.isNotEmpty ? _buscarController.text : null,
      );

      if (result['success'] == true) {
        final megaEventosData = result['mega_eventos'] as List<dynamic>;
        setState(() {
          _megaEventos =
              megaEventosData
                  .map((e) => MegaEvento.fromJson(e as Map<String, dynamic>))
                  .toList();
          _isLoading = false;
        });
      } else {
        setState(() {
          _error = result['error'] as String? ?? 'Error al cargar mega eventos';
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _error = 'Error de conexión: ${e.toString()}';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mega Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () async {
              final result = await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const CrearMegaEventoScreen(),
                ),
              );
              if (result == true) {
                _loadDatos();
              }
            },
            tooltip: 'Nuevo Mega Evento',
          ),
        ],
      ),
      drawer: const AppDrawer(),
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
                      style: const TextStyle(color: Colors.red),
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
                  // Filtros y búsqueda
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.grey[100],
                    child: Column(
                      children: [
                        // Barra de búsqueda
                        TextField(
                          controller: _buscarController,
                          decoration: InputDecoration(
                            hintText: 'Buscar mega eventos...',
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
                            filled: true,
                            fillColor: Colors.white,
                          ),
                          onSubmitted: (_) => _loadDatos(),
                        ),
                        const SizedBox(height: 12),
                        // Filtros
                        Row(
                          children: [
                            Expanded(
                              child: DropdownButtonFormField<String>(
                                value: _filtroEstado,
                                decoration: InputDecoration(
                                  labelText: 'Estado',
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  filled: true,
                                  fillColor: Colors.white,
                                ),
                                items:
                                    _estados.map((estado) {
                                      return DropdownMenuItem<String>(
                                        value: estado['codigo'] as String,
                                        child: Row(
                                          children: [
                                            Icon(
                                              estado['icon'] as IconData,
                                              size: 20,
                                              color: estado['color'] as Color,
                                            ),
                                            const SizedBox(width: 8),
                                            Text(estado['nombre'] as String),
                                          ],
                                        ),
                                      );
                                    }).toList(),
                                onChanged: (value) {
                                  setState(() {
                                    _filtroEstado = value ?? 'todos';
                                  });
                                  _loadDatos();
                                },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Lista de mega eventos
                  Expanded(
                    child:
                        _megaEventos.isEmpty
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
                                    'No hay mega eventos',
                                    style: TextStyle(
                                      fontSize: 18,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            )
                            : RefreshIndicator(
                              onRefresh: _loadDatos,
                              child: ListView.builder(
                                padding: const EdgeInsets.all(16),
                                itemCount: _megaEventos.length,
                                itemBuilder: (context, index) {
                                  final megaEvento = _megaEventos[index];
                                  return _buildMegaEventoCard(megaEvento);
                                },
                              ),
                            ),
                  ),
                ],
              ),
    );
  }

  Widget _buildMegaEventoCard(MegaEvento megaEvento) {
    final estadoInfo = _estados.firstWhere(
      (e) => e['codigo'] == megaEvento.estado,
      orElse: () => _estados[0],
    );

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: () async {
          final result = await Navigator.push(
            context,
            MaterialPageRoute(
              builder:
                  (context) => MegaEventoDetailScreen(
                    megaEventoId: megaEvento.megaEventoId,
                  ),
            ),
          );
          if (result == true) {
            _loadDatos();
          }
        },
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen
            if (megaEvento.imagenes != null && megaEvento.imagenes!.isNotEmpty)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(12),
                ),
                child: CachedNetworkImage(
                  imageUrl:
                      ImageHelper.buildImageUrl(
                        megaEvento.imagenes!.first.toString(),
                      ) ??
                      '',
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder:
                      (context, url) => Container(
                        height: 200,
                        color: Colors.grey[300],
                        child: const Center(child: CircularProgressIndicator()),
                      ),
                  errorWidget:
                      (context, url, error) => Container(
                        height: 200,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported),
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
                          megaEvento.titulo,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      Chip(
                        label: Text(
                          estadoInfo['nombre'] as String,
                          style: const TextStyle(fontSize: 12),
                        ),
                        avatar: Icon(
                          estadoInfo['icon'] as IconData,
                          size: 16,
                          color: Colors.white,
                        ),
                        backgroundColor: estadoInfo['color'] as Color,
                        labelStyle: const TextStyle(color: Colors.white),
                      ),
                    ],
                  ),

                  const SizedBox(height: 8),

                  // Descripción
                  if (megaEvento.descripcion != null &&
                      megaEvento.descripcion!.isNotEmpty)
                    Text(
                      megaEvento.descripcion!,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[600], fontSize: 14),
                    ),

                  const SizedBox(height: 12),

                  // Fechas
                  Row(
                    children: [
                      Icon(
                        Icons.calendar_today,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 8),
                      Text(
                        '${_formatDate(megaEvento.fechaInicio)} - ${_formatDate(megaEvento.fechaFin)}',
                        style: TextStyle(color: Colors.grey[600], fontSize: 12),
                      ),
                    ],
                  ),

                  if (megaEvento.ubicacion != null &&
                      megaEvento.ubicacion!.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            megaEvento.ubicacion!,
                            style: TextStyle(
                              color: Colors.grey[600],
                              fontSize: 12,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],

                  const SizedBox(height: 12),

                  // Botones de acción
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      TextButton.icon(
                        onPressed: () async {
                          await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder:
                                  (context) => MegaEventoSeguimientoScreen(
                                    megaEventoId: megaEvento.megaEventoId,
                                  ),
                            ),
                          );
                        },
                        icon: const Icon(Icons.track_changes, size: 18),
                        label: const Text('Seguimiento'),
                      ),
                      const SizedBox(width: 8),
                      TextButton.icon(
                        onPressed: () async {
                          final result = await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder:
                                  (context) => MegaEventoDetailScreen(
                                    megaEventoId: megaEvento.megaEventoId,
                                  ),
                            ),
                          );
                          if (result == true) {
                            _loadDatos();
                          }
                        },
                        icon: const Icon(Icons.visibility, size: 18),
                        label: const Text('Ver'),
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
