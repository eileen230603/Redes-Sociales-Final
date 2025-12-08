import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/mega_evento.dart';
import '../widgets/app_drawer.dart';
import '../utils/image_helper.dart';
import 'externo/mega_evento_detail_screen.dart';

class MegaEventosListScreen extends StatefulWidget {
  const MegaEventosListScreen({super.key});

  @override
  State<MegaEventosListScreen> createState() => _MegaEventosListScreenState();
}

class _MegaEventosListScreenState extends State<MegaEventosListScreen> {
  List<MegaEvento> _megaEventos = [];
  bool _isLoading = true;
  String? _error;
  String _filtroCategoria = 'todos';
  final TextEditingController _buscarController = TextEditingController();
  // Mapa para almacenar estado de reacciones por mega evento
  Map<int, bool> _megaEventosReaccionados = {}; // megaEventoId -> reaccionado
  Map<int, int> _totalReaccionesPorMegaEvento = {}; // megaEventoId -> total
  Map<int, bool> _megaEventosParticipando = {}; // megaEventoId -> participando

  @override
  void initState() {
    super.initState();
    _loadMegaEventos();
  }

  @override
  void dispose() {
    _buscarController.dispose();
    super.dispose();
  }

  Future<void> _loadMegaEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final result = await ApiService.getMegaEventosPublicos(
        categoria: _filtroCategoria != 'todos' ? _filtroCategoria : null,
        buscar:
            _buscarController.text.isNotEmpty ? _buscarController.text : null,
      );

      if (!mounted) return;

      setState(() {
        _isLoading = false;
        if (result['success'] == true) {
          final megaEventosData = result['mega_eventos'] as List<dynamic>;
          _megaEventos =
              megaEventosData
                  .map((e) => MegaEvento.fromJson(e as Map<String, dynamic>))
                  .toList();
          // Verificar reacciones y participación para cada mega evento
          _verificarReacciones();
          _verificarParticipaciones();
        } else {
          _error = result['error'] as String? ?? 'Error al cargar mega eventos';
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
        _error = 'Error de conexión: ${e.toString()}';
      });
    }
  }

  Future<void> _verificarReacciones() async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      // Para usuarios no autenticados, solo cargar totales
      for (final megaEvento in _megaEventos) {
        final totalResult = await ApiService.getTotalReaccionesMegaEvento(
          megaEvento.megaEventoId,
        );
        if (totalResult['success'] == true) {
          _totalReaccionesPorMegaEvento[megaEvento.megaEventoId] =
              totalResult['total_reacciones'] as int? ?? 0;
        }
      }
      if (mounted) setState(() {});
      return;
    }

    Map<int, bool> megaEventosReaccionadosMap = {};
    Map<int, int> totalReaccionesMap = {};

    for (final megaEvento in _megaEventos) {
      final reaccionResult = await ApiService.verificarReaccionMegaEvento(
        megaEvento.megaEventoId,
      );
      if (reaccionResult['success'] == true) {
        megaEventosReaccionadosMap[megaEvento.megaEventoId] =
            reaccionResult['reaccionado'] as bool? ?? false;
      }

      final totalResult = await ApiService.getTotalReaccionesMegaEvento(
        megaEvento.megaEventoId,
      );
      if (totalResult['success'] == true) {
        totalReaccionesMap[megaEvento.megaEventoId] =
            totalResult['total_reacciones'] as int? ?? 0;
      }
    }

    if (!mounted) return;

    setState(() {
      _megaEventosReaccionados = megaEventosReaccionadosMap;
      _totalReaccionesPorMegaEvento = totalReaccionesMap;
    });
  }

  Future<void> _verificarParticipaciones() async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      return;
    }

    Map<int, bool> megaEventosParticipandoMap = {};

    for (final megaEvento in _megaEventos) {
      final participacionResult =
          await ApiService.verificarParticipacionMegaEvento(
            megaEvento.megaEventoId,
          );
      if (participacionResult['success'] == true) {
        megaEventosParticipandoMap[megaEvento.megaEventoId] =
            participacionResult['participa'] as bool? ?? false;
      }
    }

    if (!mounted) return;

    setState(() {
      _megaEventosParticipando = megaEventosParticipandoMap;
    });
  }

  Future<void> _toggleReaccionEnCard(int megaEventoId) async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      // Para usuarios no autenticados, usar reacción pública
      final result = await ApiService.reaccionarMegaEventoPublico(megaEventoId);
      if (result['success'] == true && mounted) {
        // Recargar total de reacciones
        final totalResult = await ApiService.getTotalReaccionesMegaEvento(
          megaEventoId,
        );
        if (totalResult['success'] == true && mounted) {
          setState(() {
            _totalReaccionesPorMegaEvento[megaEventoId] =
                totalResult['total_reacciones'] as int? ?? 0;
          });
        }
      }
      return;
    }

    final result = await ApiService.toggleReaccionMegaEvento(megaEventoId);

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _megaEventosReaccionados[megaEventoId] =
            result['reaccionado'] as bool? ?? false;
      });

      // Actualizar total de reacciones
      final totalResult = await ApiService.getTotalReaccionesMegaEvento(
        megaEventoId,
      );
      if (totalResult['success'] == true && mounted) {
        setState(() {
          _totalReaccionesPorMegaEvento[megaEventoId] =
              totalResult['total_reacciones'] as int? ?? 0;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/mega-eventos'),
      appBar: AppBar(
        title: const Text('Mega Eventos Disponibles'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadMegaEventos,
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
                      onPressed: _loadMegaEventos,
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
                                        _loadMegaEventos();
                                      },
                                    )
                                    : null,
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            filled: true,
                            fillColor: Colors.white,
                          ),
                          onSubmitted: (_) => _loadMegaEventos(),
                        ),
                        const SizedBox(height: 12),
                        // Filtro de categoría
                        DropdownButtonFormField<String>(
                          value: _filtroCategoria,
                          decoration: InputDecoration(
                            labelText: 'Categoría',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            filled: true,
                            fillColor: Colors.white,
                          ),
                          items: const [
                            DropdownMenuItem(
                              value: 'todos',
                              child: Text('Todas las categorías'),
                            ),
                            DropdownMenuItem(
                              value: 'social',
                              child: Text('Social'),
                            ),
                            DropdownMenuItem(
                              value: 'cultural',
                              child: Text('Cultural'),
                            ),
                            DropdownMenuItem(
                              value: 'deportivo',
                              child: Text('Deportivo'),
                            ),
                            DropdownMenuItem(
                              value: 'educativo',
                              child: Text('Educativo'),
                            ),
                            DropdownMenuItem(
                              value: 'benefico',
                              child: Text('Benéfico'),
                            ),
                            DropdownMenuItem(
                              value: 'ambiental',
                              child: Text('Ambiental'),
                            ),
                            DropdownMenuItem(
                              value: 'otro',
                              child: Text('Otro'),
                            ),
                          ],
                          onChanged: (value) {
                            setState(() {
                              _filtroCategoria = value ?? 'todos';
                            });
                            _loadMegaEventos();
                          },
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
                                    'No hay mega eventos disponibles',
                                    style: TextStyle(
                                      fontSize: 18,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            )
                            : RefreshIndicator(
                              onRefresh: _loadMegaEventos,
                              child: ListView.builder(
                                padding: const EdgeInsets.all(8),
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
    final estaReaccionado =
        _megaEventosReaccionados[megaEvento.megaEventoId] ?? false;
    final totalReacciones =
        _totalReaccionesPorMegaEvento[megaEvento.megaEventoId] ?? 0;
    final estaParticipando =
        _megaEventosParticipando[megaEvento.megaEventoId] ?? false;

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side:
            estaParticipando
                ? const BorderSide(color: Colors.green, width: 2)
                : BorderSide.none,
      ),
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
            _loadMegaEventos();
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
                  // Título y badges
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
                      if (megaEvento.categoria != null)
                        Chip(
                          label: Text(
                            megaEvento.categoria!,
                            style: const TextStyle(fontSize: 10),
                          ),
                          backgroundColor: Colors.blue[100],
                        ),
                    ],
                  ),

                  if (estaParticipando) ...[
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.green[100],
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: const [
                          Icon(
                            Icons.check_circle,
                            size: 16,
                            color: Colors.green,
                          ),
                          SizedBox(width: 4),
                          Text(
                            'Ya estás participando',
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: Colors.green,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],

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
                      Expanded(
                        child: Text(
                          '${_formatDate(megaEvento.fechaInicio)} - ${_formatDate(megaEvento.fechaFin)}',
                          style: TextStyle(
                            color: Colors.grey[600],
                            fontSize: 12,
                          ),
                        ),
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
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Botón de reacción
                      TextButton.icon(
                        onPressed:
                            () =>
                                _toggleReaccionEnCard(megaEvento.megaEventoId),
                        icon: Icon(
                          estaReaccionado
                              ? Icons.favorite
                              : Icons.favorite_border,
                          color: estaReaccionado ? Colors.red : Colors.grey,
                        ),
                        label: Text(
                          '$totalReacciones',
                          style: TextStyle(
                            color: estaReaccionado ? Colors.red : Colors.grey,
                          ),
                        ),
                      ),
                      // Botón de ver más
                      TextButton(
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
                            _loadMegaEventos();
                          }
                        },
                        child: const Text('Ver más'),
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
