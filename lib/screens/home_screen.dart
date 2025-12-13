import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/storage_service.dart';
import '../services/api_service.dart';
import '../services/auth_helper.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import 'evento_detail_screen.dart';
import 'notificaciones_screen.dart';
import '../utils/image_helper.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  Map<String, dynamic>? _userData;
  List<Evento> _eventos = [];
  Set<int> _eventosInscritos =
      {}; // IDs de eventos donde el usuario está inscrito
  bool _isLoadingEventos = true;
  String? _errorEventos;
  int _notificacionesNoLeidas = 0;
  // Mapa para almacenar estado de reacciones por evento
  Map<int, bool> _eventosReaccionados = {}; // eventoId -> reaccionado
  Map<int, int> _totalReaccionesPorEvento = {}; // eventoId -> total

  @override
  void initState() {
    super.initState();
    _loadUserData();
    _loadEventos();
    _loadContadorNotificaciones();
  }

  Future<void> _loadContadorNotificaciones() async {
    final userType = _userData?['user_type'] as String?;
    // Solo cargar notificaciones para ONG
    if (userType != 'ONG') return;

    final result = await ApiService.getContadorNotificaciones();
    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _notificacionesNoLeidas = result['no_leidas'] as int? ?? 0;
      });
    }
  }

  Future<void> _loadUserData() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _userData = userData;
    });
  }

  Future<void> _loadEventos() async {
    setState(() {
      _isLoadingEventos = true;
      _errorEventos = null;
    });

    final userType = _userData?['user_type'] as String?;

    // Si es ONG, cargar eventos de la ONG
    if (userType == 'ONG') {
      // Usar AuthHelper para obtener ONG ID con validación y reintento
      final ongId = await AuthHelper.getOngIdWithRetry();
      if (ongId != null) {
        final resultOng = await ApiService.getEventosOng(ongId);
        if (!mounted) return;
        setState(() {
          _isLoadingEventos = false;
          if (resultOng['success'] == true) {
            _eventos = resultOng['eventos'] as List<Evento>;
          } else {
            _errorEventos =
                resultOng['error'] as String? ?? 'Error al cargar eventos';
            _eventos = [];
          }
        });
        return;
      } else {
        // Si no se pudo obtener el ONG ID, mostrar error
        if (!mounted) return;
        setState(() {
          _isLoadingEventos = false;
          _errorEventos =
              'No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.';
          _eventos = [];
        });
        return;
      }
    }

    // Para empresas y externos, usar eventos publicados
    final result = await ApiService.getEventosPublicados();

    if (!mounted) return;

    List<Evento> eventosCargados = [];
    String? errorCargado;

    if (result['success'] == true) {
      final todosEventos = result['eventos'] as List<Evento>;

      // Si es empresa, mostrar solo eventos patrocinados
      if (userType == 'Empresa') {
        final empresaId = _userData?['entity_id'] as int?;
        if (empresaId != null) {
          eventosCargados =
              todosEventos.where((evento) {
                if (evento.patrocinadores == null) return false;
                final patrocinadores = evento.patrocinadores as List;
                return patrocinadores.any(
                  (p) => p.toString() == empresaId.toString() || p == empresaId,
                );
              }).toList();
        }
      } else {
        // Para externos, mostrar todos los eventos publicados
        eventosCargados = todosEventos;
      }
    } else {
      errorCargado = result['error'] as String? ?? 'Error al cargar eventos';
    }

    // Cargar eventos inscritos para mostrar badge (solo para integrantes externos)
    Set<int> eventosInscritosSet = {};
    Map<int, bool> eventosReaccionadosMap = {};
    Map<int, int> totalReaccionesMap = {};

    if (userType == 'Integrante externo' && eventosCargados.isNotEmpty) {
      final misEventosResult = await ApiService.getMisEventos();
      if (misEventosResult['success'] == true) {
        final participaciones =
            misEventosResult['participaciones'] as List<EventoParticipacion>;
        eventosInscritosSet = participaciones.map((p) => p.eventoId).toSet();
      }

      // Verificar reacciones para cada evento
      for (final evento in eventosCargados) {
        final reaccionResult = await ApiService.verificarReaccion(evento.id);
        if (reaccionResult['success'] == true) {
          eventosReaccionadosMap[evento.id] =
              reaccionResult['reaccionado'] as bool? ?? false;
          totalReaccionesMap[evento.id] =
              reaccionResult['total_reacciones'] as int? ?? 0;
        }
      }
    }

    if (!mounted) return;

    setState(() {
      _isLoadingEventos = false;
      _eventos = eventosCargados;
      _errorEventos = errorCargado;
      _eventosInscritos = eventosInscritosSet;
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
      drawer: const AppDrawer(currentRoute: '/home'),
      appBar: AppBar(
        title: Text(
          _userData?['user_type'] == 'Empresa'
              ? 'Panel de Empresa'
              : _userData?['user_type'] == 'ONG'
              ? 'Panel ONG'
              : 'Panel del Integrante Externo',
        ),
        actions: [
          // Badge de notificaciones (solo para ONG)
          if (_userData?['user_type'] == 'ONG')
            Stack(
              children: [
                IconButton(
                  icon: const Icon(Icons.notifications),
                  onPressed: () async {
                    await Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const NotificacionesScreen(),
                      ),
                    );
                    // Recargar contador al volver
                    _loadContadorNotificaciones();
                  },
                  tooltip: 'Notificaciones',
                ),
                if (_notificacionesNoLeidas > 0)
                  Positioned(
                    right: 8,
                    top: 8,
                    child: Container(
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        shape: BoxShape.circle,
                      ),
                      constraints: const BoxConstraints(
                        minWidth: 16,
                        minHeight: 16,
                      ),
                      child: Text(
                        _notificacionesNoLeidas > 99
                            ? '99+'
                            : _notificacionesNoLeidas.toString(),
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ),
              ],
            ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _loadEventos();
              _loadContadorNotificaciones();
            },
            tooltip: 'Actualizar eventos',
          ),

        ],
      ),
      body:
          _userData == null
              ? const Center(child: CircularProgressIndicator())
              : RefreshIndicator(
                onRefresh: _loadEventos,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Saludo y nombre de usuario
                      Card(
                        elevation: 2,
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Row(
                            children: [
                              Icon(
                                Icons.person_outline,
                                color: Theme.of(context).primaryColor,
                                size: 32,
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      'Bienvenido',
                                      style: Theme.of(
                                        context,
                                      ).textTheme.headlineSmall?.copyWith(
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      _userData!['user_name'] ?? 'Usuario',
                                      style:
                                          Theme.of(context).textTheme.bodyLarge,
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                      // Título de eventos
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            _userData?['user_type'] == 'Empresa'
                                ? 'Eventos Patrocinados'
                                : _userData?['user_type'] == 'ONG'
                                ? 'Mis Eventos'
                                : 'Eventos Disponibles',
                            style: Theme.of(context).textTheme.titleLarge,
                          ),
                          if (_isLoadingEventos)
                            const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      // Lista de eventos
                      if (_isLoadingEventos)
                        const Center(
                          child: Padding(
                            padding: EdgeInsets.all(32.0),
                            child: CircularProgressIndicator(),
                          ),
                        )
                      else if (_errorEventos != null)
                        Card(
                          color: Colors.red[50],
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              children: [
                                Icon(
                                  Icons.error_outline,
                                  color: Colors.red[300],
                                  size: 48,
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  _errorEventos!,
                                  textAlign: TextAlign.center,
                                  style: TextStyle(color: Colors.red[700]),
                                ),
                                const SizedBox(height: 12),
                                FilledButton.tonal(
                                  onPressed: _loadEventos,
                                  child: const Text('Reintentar'),
                                ),
                              ],
                            ),
                          ),
                        )
                      else if (_eventos.isEmpty)
                        Card(
                          child: Padding(
                            padding: const EdgeInsets.all(32.0),
                            child: Column(
                              children: [
                                Icon(
                                  Icons.event_busy,
                                  size: 64,
                                  color: Colors.grey[400],
                                ),
                                const SizedBox(height: 16),
                                Text(
                                  _userData?['user_type'] == 'Empresa'
                                      ? 'No tienes eventos patrocinados'
                                      : _userData?['user_type'] == 'ONG'
                                      ? 'No tienes eventos creados'
                                      : 'No hay eventos disponibles',
                                  style: TextStyle(
                                    fontSize: 16,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                if (_userData?['user_type'] == 'Empresa') ...[
                                  const SizedBox(height: 8),
                                  Text(
                                    'Explora eventos disponibles para patrocinar',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[500],
                                    ),
                                  ),
                                ] else if (_userData?['user_type'] ==
                                    'ONG') ...[
                                  const SizedBox(height: 8),
                                  Text(
                                    'Crea tu primer evento',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[500],
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ),
                        )
                      else
                        ..._eventos.map((evento) => _buildEventoCard(evento)),
                    ],
                  ),
                ),
              ),
      bottomNavigationBar: BottomNavBar(
        currentIndex: 0,
        userType: _userData?['user_type'] as String?,
      ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    // Obtener la primera imagen usando el helper
    final imagenUrl = ImageHelper.getFirstImageUrl(evento.imagenes);

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      clipBehavior: Clip.antiAlias,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: () {
          final userType = _userData?['user_type'] as String?;
          // Para integrantes externos, mostrar modal
          if (userType == 'Integrante externo') {
            _mostrarDetalleEventoModal(evento);
          } else {
            // Para otros tipos de usuario, navegar a la pantalla de detalles
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => EventoDetailScreen(eventoId: evento.id),
              ),
            ).then((_) => _loadEventos());
          }
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen del evento con botón de reacción (solo para integrantes externos)
            Stack(
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
                // Botón de reacción (solo para integrantes externos)
                if (_userData?['user_type'] == 'Integrante externo')
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
                // Contador de reacciones (solo para integrantes externos)
                if (_userData?['user_type'] == 'Integrante externo' &&
                    (_totalReaccionesPorEvento[evento.id] ?? 0) > 0)
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
                      if (_userData?['user_type'] == 'Empresa')
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.green[100],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            'Patrocinado',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.green[800],
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        )
                      else if (_userData?['user_type'] ==
                              'Integrante externo' &&
                          _eventosInscritos.contains(evento.id))
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.blue[100],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Text(
                            'INSCRITO',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.blue,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        )
                      else if (!evento.puedeInscribirse)
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
                  const SizedBox(height: 12),
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

  Future<void> _mostrarDetalleEventoModal(Evento evento) async {
    // Cargar detalles completos del evento
    final detalleResult = await ApiService.getEventoDetalle(evento.id);
    Evento? eventoCompleto = evento;

    if (detalleResult['success'] == true) {
      eventoCompleto = detalleResult['evento'] as Evento;
    }

    // Verificar si está inscrito
    bool isInscrito = false;
    bool isChecking = true;

    final misEventosResult = await ApiService.getMisEventos();
    if (misEventosResult['success'] == true) {
      final participaciones =
          misEventosResult['participaciones'] as List<EventoParticipacion>;
      isInscrito = participaciones.any((p) => p.eventoId == evento.id);
    }
    isChecking = false;

    if (!mounted) return;

    // Mostrar el modal
    await showDialog(
      context: context,
      builder:
          (context) => _EventoDetalleModal(
            evento: eventoCompleto!,
            isInscrito: isInscrito,
            isChecking: isChecking,
            onInscribirse: () async {
              Navigator.of(context).pop();
              final result = await ApiService.inscribirEnEvento(evento.id);
              if (!mounted) return;

              if (result['success'] == true) {
                setState(() {
                  _eventosInscritos.add(evento.id);
                });
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      result['message'] as String? ?? 'Inscripción exitosa',
                    ),
                    backgroundColor: Colors.green,
                  ),
                );
                _loadEventos();
              } else {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      result['error'] as String? ?? 'Error al inscribirse',
                    ),
                    backgroundColor: Colors.red,
                  ),
                );
              }
            },
            onCancelarInscripcion: () async {
              final confirm = await showDialog<bool>(
                context: context,
                builder:
                    (context) => AlertDialog(
                      title: const Text('Cancelar inscripción'),
                      content: const Text(
                        '¿Estás seguro de que deseas cancelar tu inscripción?',
                      ),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(false),
                          child: const Text('No'),
                        ),
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(true),
                          child: const Text('Sí, cancelar'),
                        ),
                      ],
                    ),
              );

              if (confirm == true) {
                Navigator.of(context).pop(); // Cerrar el modal de detalles
                final result = await ApiService.cancelarInscripcion(evento.id);
                if (!mounted) return;

                if (result['success'] == true) {
                  setState(() {
                    _eventosInscritos.remove(evento.id);
                  });
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        result['message'] as String? ?? 'Inscripción cancelada',
                      ),
                      backgroundColor: Colors.orange,
                    ),
                  );
                  _loadEventos();
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        result['error'] as String? ?? 'Error al cancelar',
                      ),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              }
            },
            getImageUrl: ImageHelper.buildImageUrl,
            formatDate: _formatDate,
          ),
    );
  }
}

// Widget del modal de detalles del evento
class _EventoDetalleModal extends StatefulWidget {
  final Evento evento;
  final bool isInscrito;
  final bool isChecking;
  final VoidCallback onInscribirse;
  final VoidCallback onCancelarInscripcion;
  final String? Function(String) getImageUrl;
  final String Function(DateTime) formatDate;

  const _EventoDetalleModal({
    required this.evento,
    required this.isInscrito,
    required this.isChecking,
    required this.onInscribirse,
    required this.onCancelarInscripcion,
    required this.getImageUrl,
    required this.formatDate,
  });

  @override
  State<_EventoDetalleModal> createState() => _EventoDetalleModalState();
}

class _EventoDetalleModalState extends State<_EventoDetalleModal> {
  bool _isProcessing = false;

  @override
  Widget build(BuildContext context) {
    String? imagenUrl;
    if (widget.evento.imagenes != null &&
        widget.evento.imagenes!.isNotEmpty &&
        widget.evento.imagenes![0] != null) {
      final imgPath = widget.evento.imagenes![0].toString().trim();
      if (imgPath.isNotEmpty) {
        imagenUrl = widget.getImageUrl(imgPath);
      }
    }

    return Dialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Container(
        constraints: BoxConstraints(
          maxHeight: MediaQuery.of(context).size.height * 0.9,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header con botón cerrar
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Text(
                    'Detalles del Evento',
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.of(context).pop(),
                ),
              ],
            ),
            Flexible(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Imagen
                    if (imagenUrl != null)
                      ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: CachedNetworkImage(
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
                        ),
                      )
                    else
                      Container(
                        height: 200,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Icon(
                          Icons.event,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                      ),
                    const SizedBox(height: 16),

                    // Título
                    Text(
                      widget.evento.titulo,
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),

                    // Estado e inscripción
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color:
                                widget.evento.estado == 'publicado'
                                    ? Colors.green[100]
                                    : Colors.grey[300],
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Text(
                            widget.evento.estado.toUpperCase(),
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  widget.evento.estado == 'publicado'
                                      ? Colors.green[800]
                                      : Colors.grey[800],
                            ),
                          ),
                        ),
                        if (widget.isInscrito) ...[
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 6,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.blue[100],
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: const Text(
                              'INSCRITO',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.blue,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Información del evento
                    _buildInfoRow(
                      Icons.category,
                      'Tipo',
                      widget.evento.tipoEvento,
                    ),
                    const SizedBox(height: 8),
                    _buildInfoRow(
                      Icons.calendar_today,
                      'Fecha',
                      widget.formatDate(widget.evento.fechaInicio),
                    ),
                    if (widget.evento.ciudad != null) ...[
                      const SizedBox(height: 8),
                      _buildInfoRow(
                        Icons.location_on,
                        'Ciudad',
                        widget.evento.ciudad!,
                      ),
                    ],
                    if (widget.evento.direccion != null) ...[
                      const SizedBox(height: 8),
                      _buildInfoRow(
                        Icons.place,
                        'Dirección',
                        widget.evento.direccion!,
                      ),
                    ],
                    if (widget.evento.descripcion != null &&
                        widget.evento.descripcion!.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      const Text(
                        'Descripción',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        widget.evento.descripcion!,
                        style: TextStyle(color: Colors.grey[700]),
                      ),
                    ],
                    const SizedBox(height: 24),

                    // Botones de acción
                    if (widget.isChecking)
                      const Center(child: CircularProgressIndicator())
                    else if (widget.isInscrito)
                      SizedBox(
                        width: double.infinity,
                        child: FilledButton.tonalIcon(
                          onPressed:
                              _isProcessing
                                  ? null
                                  : () {
                                    setState(() => _isProcessing = true);
                                    widget.onCancelarInscripcion();
                                  },
                          icon: const Icon(Icons.cancel),
                          label:
                              _isProcessing
                                  ? const Text('Cancelando...')
                                  : const Text('Cancelar Inscripción'),
                        ),
                      )
                    else if (widget.evento.puedeInscribirse)
                      SizedBox(
                        width: double.infinity,
                        child: FilledButton.icon(
                          onPressed:
                              _isProcessing
                                  ? null
                                  : () {
                                    setState(() => _isProcessing = true);
                                    widget.onInscribirse();
                                  },
                          icon: const Icon(Icons.check_circle),
                          label:
                              _isProcessing
                                  ? const Text('Inscribiendo...')
                                  : const Text('Inscribirse al Evento'),
                        ),
                      )
                    else
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: null,
                          child: const Text('Inscripciones Cerradas'),
                        ),
                      ),
                    const SizedBox(height: 16),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey[600],
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
