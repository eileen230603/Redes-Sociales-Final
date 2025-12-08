import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../widgets/breadcrumbs.dart';
import '../utils/image_helper.dart';
import '../utils/navigation_helper.dart';
import '../config/api_config.dart';
import 'ong/gestion_participantes_screen.dart';
import 'ong/dashboard_evento_screen.dart';
import 'ong/editar_evento_screen.dart';

class EventoDetailScreen extends StatefulWidget {
  final int eventoId;

  const EventoDetailScreen({super.key, required this.eventoId});

  @override
  State<EventoDetailScreen> createState() => _EventoDetailScreenState();
}

class _EventoDetailScreenState extends State<EventoDetailScreen> {
  Evento? _evento;
  bool _isLoading = true;
  String? _error;
  bool _isInscrito = false;
  bool _isCheckingInscripcion = false;
  bool _isProcessing = false;
  bool _asistio = false;
  EventoParticipacion? _participacion;
  bool _reaccionado = false;
  int _totalReacciones = 0;
  bool _isProcessingReaccion = false;
  List<dynamic> _usuariosQueReaccionaron = [];
  bool _isLoadingUsuariosReaccion = false;
  String? _userType;
  int _totalCompartidos = 0;
  bool _isLoadingCompartidos = false;

  @override
  void initState() {
    super.initState();
    _loadUserType();
    _loadEvento();
    _checkInscripcion();
    _checkReaccion();
    _loadTotalCompartidos();
  }

  Future<void> _loadUserType() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _userType = userData?['user_type'] as String?;
    });
  }

  Future<void> _loadEvento() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventoDetalle(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _evento = result['evento'] as Evento;
        // Recargar reacción después de cargar el evento
        _checkReaccion();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar evento';
      }
    });
  }

  Future<void> _checkInscripcion() async {
    setState(() {
      _isCheckingInscripcion = true;
    });

    final result = await ApiService.getMisEventos();

    if (!mounted) return;

    setState(() {
      _isCheckingInscripcion = false;
      if (result['success'] == true) {
        final participaciones =
            result['participaciones'] as List<EventoParticipacion>;
        _participacion = participaciones.firstWhere(
          (p) => p.eventoId == widget.eventoId,
          orElse: () => participaciones.first,
        );
        _isInscrito = participaciones.any((p) => p.eventoId == widget.eventoId);
        if (_participacion != null && _isInscrito) {
          _asistio = _participacion!.asistio;
        }
      }
    });
  }

  Future<void> _checkReaccion() async {
    final result = await ApiService.verificarReaccion(widget.eventoId);

    if (!mounted) return;

    setState(() {
      if (result['success'] == true) {
        _reaccionado = result['reaccionado'] as bool? ?? false;
        _totalReacciones = result['total_reacciones'] as int? ?? 0;
      }
    });
  }

  Future<void> _toggleReaccion() async {
    if (_isProcessingReaccion) return;

    setState(() {
      _isProcessingReaccion = true;
    });

    final result = await ApiService.toggleReaccion(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isProcessingReaccion = false;
      if (result['success'] == true) {
        _reaccionado = result['reaccionado'] as bool? ?? false;
        _totalReacciones = result['total_reacciones'] as int? ?? 0;
      }
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            _reaccionado ? '¡Reacción agregada! ❤️' : 'Reacción eliminada',
          ),
          backgroundColor: _reaccionado ? Colors.red : Colors.grey,
          duration: const Duration(seconds: 2),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar reacción',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _loadTotalCompartidos() async {
    setState(() {
      _isLoadingCompartidos = true;
    });

    final result = await ApiService.getTotalCompartidosEvento(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoadingCompartidos = false;
      if (result['success'] == true) {
        _totalCompartidos = result['total_compartidos'] as int? ?? 0;
      }
    });
  }

  Future<void> _compartirEvento() async {
    if (_evento == null) return;

    // Verificar si el evento está finalizado
    final eventoFinalizado =
        _evento!.estado == 'finalizado' ||
        (_evento!.fechaFin != null &&
            _evento!.fechaFin!.isBefore(DateTime.now()));

    if (eventoFinalizado) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Este evento fue finalizado. Ya no se puede compartir.',
          ),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    // Mostrar modal de compartir (igual que en Laravel)
    _mostrarModalCompartir();
  }

  void _mostrarModalCompartir() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder:
          (context) => _ModalCompartir(
            eventoId: widget.eventoId,
            eventoTitulo: _evento?.titulo ?? 'Evento',
            eventoUrl: _getEventoUrl(),
            onCompartido: () {
              _loadTotalCompartidos();
            },
          ),
    );
  }

  String _getEventoUrl() {
    final baseUrl = ApiConfig.baseUrl.replaceAll('/api', '');
    return '$baseUrl/evento/${widget.eventoId}/qr';
  }

  Future<void> _cargarUsuariosQueReaccionaron() async {
    setState(() {
      _isLoadingUsuariosReaccion = true;
    });

    final result = await ApiService.getUsuariosQueReaccionaron(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoadingUsuariosReaccion = false;
      if (result['success'] == true) {
        _usuariosQueReaccionaron = result['reacciones'] as List? ?? [];
      }
    });

    if (result['success'] == true && mounted) {
      showDialog(
        context: context,
        builder: (context) => _buildDialogUsuariosReaccion(),
      );
    }
  }

  Widget _buildDialogUsuariosReaccion() {
    return AlertDialog(
      title: Row(
        children: [
          const Icon(Icons.favorite, color: Colors.red),
          const SizedBox(width: 8),
          Text('${_usuariosQueReaccionaron.length} Reacciones'),
        ],
      ),
      content: SizedBox(
        width: double.maxFinite,
        child:
            _isLoadingUsuariosReaccion
                ? const Center(child: CircularProgressIndicator())
                : _usuariosQueReaccionaron.isEmpty
                ? const Center(child: Text('No hay reacciones aún'))
                : ListView.builder(
                  shrinkWrap: true,
                  itemCount: _usuariosQueReaccionaron.length,
                  itemBuilder: (context, index) {
                    final reaccion = _usuariosQueReaccionaron[index] as Map;
                    final nombre =
                        reaccion['nombre']?.toString() ?? 'Sin nombre';
                    final correo = reaccion['correo']?.toString() ?? '';
                    final fotoPerfil = reaccion['foto_perfil'] as String?;

                    return ListTile(
                      leading: CircleAvatar(
                        backgroundImage:
                            fotoPerfil != null
                                ? CachedNetworkImageProvider(
                                  ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                                )
                                : null,
                        child:
                            fotoPerfil == null
                                ? Text(
                                  nombre.isNotEmpty
                                      ? nombre[0].toUpperCase()
                                      : '?',
                                  style: const TextStyle(color: Colors.white),
                                )
                                : null,
                        backgroundColor: Colors.red,
                      ),
                      title: Text(nombre),
                      subtitle: correo.isNotEmpty ? Text(correo) : null,
                      trailing: const Icon(
                        Icons.favorite,
                        color: Colors.red,
                        size: 20,
                      ),
                    );
                  },
                ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Cerrar'),
        ),
      ],
    );
  }

  Future<void> _inscribirse() async {
    if (_evento == null || _isProcessing) return;

    setState(() {
      _isProcessing = true;
    });

    final result = await ApiService.inscribirEnEvento(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isProcessing = false;
    });

    if (result['success'] == true) {
      setState(() {
        _isInscrito = true;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] as String? ?? 'Inscripción exitosa'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al inscribirse'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _cancelarInscripcion() async {
    if (_evento == null || _isProcessing) return;

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

    if (confirm != true) return;

    setState(() {
      _isProcessing = true;
    });

    final result = await ApiService.cancelarInscripcion(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isProcessing = false;
    });

    if (result['success'] == true) {
      setState(() {
        _isInscrito = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Inscripción cancelada',
          ),
          backgroundColor: Colors.orange,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al cancelar'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Detalle del Evento'),
        actions: [
          if (_userType == 'ONG')
            IconButton(
              icon: const Icon(Icons.edit),
              onPressed: () async {
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder:
                        (context) =>
                            EditarEventoScreen(eventoId: widget.eventoId),
                  ),
                );
                if (result == true) {
                  _loadEvento();
                }
              },
              tooltip: 'Editar evento',
            ),
        ],
      ),
      body: Column(
        children: [
          Breadcrumbs(
            items: [
              BreadcrumbItem(
                label: 'Inicio',
                onTap: () => Navigator.pushReplacementNamed(context, '/home'),
              ),
              BreadcrumbItem(
                label: 'Eventos',
                onTap: () => Navigator.pop(context),
              ),
              BreadcrumbItem(label: _evento?.titulo ?? 'Detalle'),
            ],
          ),
          Expanded(
            child:
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
                            color: Colors.red[300],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            _error!,
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Colors.red[700]),
                          ),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            onPressed: _loadEvento,
                            child: const Text('Reintentar'),
                          ),
                        ],
                      ),
                    )
                    : _evento == null
                    ? const Center(child: Text('Evento no encontrado'))
                    : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Galería de imágenes
                          if (_evento!.imagenes != null &&
                              _evento!.imagenes!.isNotEmpty) ...[
                            const Text(
                              'Imágenes del Evento',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            SizedBox(
                              height: 200,
                              child: ListView.builder(
                                scrollDirection: Axis.horizontal,
                                itemCount: _evento!.imagenes!.length,
                                itemBuilder: (context, index) {
                                  final imgPath =
                                      _evento!.imagenes![index]
                                          ?.toString()
                                          .trim();
                                  if (imgPath == null || imgPath.isEmpty)
                                    return const SizedBox.shrink();

                                  final imageUrl = ImageHelper.buildImageUrl(
                                    imgPath,
                                  );
                                  if (imageUrl == null)
                                    return const SizedBox.shrink();

                                  return Container(
                                    width: 300,
                                    margin: const EdgeInsets.only(right: 12),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(12),
                                      boxShadow: [
                                        BoxShadow(
                                          color: Colors.black.withOpacity(0.1),
                                          blurRadius: 4,
                                          offset: const Offset(0, 2),
                                        ),
                                      ],
                                    ),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(12),
                                      child: CachedNetworkImage(
                                        imageUrl: imageUrl,
                                        fit: BoxFit.cover,
                                        placeholder:
                                            (context, url) => Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                child:
                                                    CircularProgressIndicator(),
                                              ),
                                            ),
                                        errorWidget:
                                            (context, url, error) => Container(
                                              color: Colors.grey[200],
                                              child: const Icon(
                                                Icons.image_not_supported,
                                                size: 48,
                                                color: Colors.grey,
                                              ),
                                            ),
                                      ),
                                    ),
                                  );
                                },
                              ),
                            ),
                            const SizedBox(height: 24),
                          ],

                          // Título y reacción
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                child: Text(
                                  _evento!.titulo,
                                  style: const TextStyle(
                                    fontSize: 24,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),
                              // Botón de reacción
                              Column(
                                children: [
                                  IconButton(
                                    icon: Icon(
                                      _reaccionado
                                          ? Icons.favorite
                                          : Icons.favorite_border,
                                      color:
                                          _reaccionado
                                              ? Colors.red
                                              : Colors.grey[600],
                                      size: 32,
                                    ),
                                    onPressed:
                                        _isProcessingReaccion
                                            ? null
                                            : _toggleReaccion,
                                    tooltip:
                                        _reaccionado
                                            ? 'Quitar reacción'
                                            : 'Reaccionar',
                                  ),
                                  if (_totalReacciones > 0)
                                    Text(
                                      _totalReacciones.toString(),
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: Colors.grey[600],
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  if (_userType == 'ONG' &&
                                      _totalReacciones > 0)
                                    TextButton(
                                      onPressed: _cargarUsuariosQueReaccionaron,
                                      child: const Text(
                                        'Ver quién reaccionó',
                                        style: TextStyle(fontSize: 11),
                                      ),
                                    ),
                                ],
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),

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
                                      _evento!.estado == 'publicado'
                                          ? Colors.green[100]
                                          : Colors.grey[300],
                                  borderRadius: BorderRadius.circular(16),
                                ),
                                child: Text(
                                  _evento!.estado.toUpperCase(),
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color:
                                        _evento!.estado == 'publicado'
                                            ? Colors.green[800]
                                            : Colors.grey[800],
                                  ),
                                ),
                              ),
                              if (_isInscrito) ...[
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
                          const SizedBox(height: 24),

                          // Información básica
                          _buildInfoRow(
                            Icons.category,
                            'Tipo de evento',
                            _evento!.tipoEvento,
                          ),
                          const SizedBox(height: 12),
                          _buildInfoRow(
                            Icons.calendar_today,
                            'Fecha de inicio',
                            _formatDateTime(_evento!.fechaInicio),
                          ),
                          if (_evento!.fechaFin != null) ...[
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Fecha de fin',
                              _formatDateTime(_evento!.fechaFin!),
                            ),
                          ],
                          if (_evento!.fechaLimiteInscripcion != null) ...[
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.event_available,
                              'Límite de inscripción',
                              _formatDateTime(_evento!.fechaLimiteInscripcion!),
                            ),
                          ],
                          if (_evento!.ciudad != null) ...[
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.location_on,
                              'Ciudad',
                              _evento!.ciudad!,
                            ),
                          ],
                          if (_evento!.direccion != null) ...[
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.place,
                              'Dirección',
                              _evento!.direccion!,
                            ),
                          ],
                          if (_evento!.capacidadMaxima != null) ...[
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.people,
                              'Capacidad máxima',
                              '${_evento!.capacidadMaxima} personas',
                            ),
                          ],

                          // Descripción
                          if (_evento!.descripcion != null &&
                              _evento!.descripcion!.isNotEmpty) ...[
                            const SizedBox(height: 24),
                            const Text(
                              'Descripción',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              _evento!.descripcion!,
                              style: TextStyle(
                                fontSize: 16,
                                color: Colors.grey[700],
                                height: 1.5,
                              ),
                            ),
                          ],

                          // Botones de acción
                          const SizedBox(height: 32),
                          if (_isCheckingInscripcion)
                            const Center(child: CircularProgressIndicator())
                          else if (_isInscrito) ...[
                            // Botón de registrar asistencia para inscritos (si aún no asistieron)
                            if (_evento != null && !_asistio)
                              SizedBox(
                                width: double.infinity,
                                child: ElevatedButton.icon(
                                  onPressed: _mostrarModalRegistrarAsistencia,
                                  icon: const Icon(Icons.check_circle),
                                  label: const Text('Registrar Asistencia'),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.green,
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                            // Mostrar estado de asistencia si ya asistió
                            if (_asistio) ...[
                              const SizedBox(height: 8),
                              Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.green[50],
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(color: Colors.green[200]!),
                                ),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.check_circle,
                                      color: Colors.green[700],
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Text(
                                        'Asistencia registrada',
                                        style: TextStyle(
                                          color: Colors.green[700],
                                          fontWeight: FontWeight.bold,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                            const SizedBox(height: 16),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton.icon(
                                onPressed:
                                    _isProcessing ? null : _cancelarInscripcion,
                                icon: const Icon(Icons.cancel),
                                label:
                                    _isProcessing
                                        ? const Text('Cancelando...')
                                        : const Text('Cancelar Inscripción'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.orange,
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 16,
                                  ),
                                ),
                              ),
                            ),
                          ] else if (_evento!.puedeInscribirse)
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton.icon(
                                onPressed: _isProcessing ? null : _inscribirse,
                                icon: const Icon(Icons.check_circle),
                                label:
                                    _isProcessing
                                        ? const Text('Inscribiendo...')
                                        : const Text('Inscribirse al Evento'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor:
                                      Theme.of(context).primaryColor,
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 16,
                                  ),
                                ),
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

                          // Botón de compartir
                          const SizedBox(height: 16),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed: _compartirEvento,
                              icon: const Icon(Icons.share),
                              label: Text(
                                _isLoadingCompartidos
                                    ? 'Cargando...'
                                    : 'Compartir${_totalCompartidos > 0 ? ' ($_totalCompartidos)' : ''}',
                              ),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.blue,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                              ),
                            ),
                          ),

                          // Botones de gestión (solo para ONG)
                          if (_userType == 'ONG') ...[
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Expanded(
                                  child: ElevatedButton.icon(
                                    onPressed: () {
                                      Navigator.push(
                                        context,
                                        NavigationHelper.slideRightRoute(
                                          DashboardEventoScreen(
                                            eventoId: widget.eventoId,
                                            eventoTitulo: _evento?.titulo,
                                          ),
                                        ),
                                      );
                                    },
                                    icon: const Icon(Icons.dashboard),
                                    label: const Text('Dashboard'),
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: Colors.blue,
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(
                                        vertical: 16,
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: OutlinedButton.icon(
                                    onPressed: () {
                                      Navigator.push(
                                        context,
                                        NavigationHelper.slideRightRoute(
                                          GestionParticipantesScreen(
                                            eventoId: widget.eventoId,
                                            eventoTitulo: _evento?.titulo,
                                          ),
                                        ),
                                      );
                                    },
                                    icon: const Icon(Icons.people),
                                    label: const Text('Participantes'),
                                    style: OutlinedButton.styleFrom(
                                      foregroundColor:
                                          Theme.of(context).primaryColor,
                                      side: BorderSide(
                                        color: Theme.of(context).primaryColor,
                                      ),
                                      padding: const EdgeInsets.symmetric(
                                        vertical: 16,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ],
                      ),
                    ),
          ),
        ],
      ),
      bottomNavigationBar: BottomNavBar(currentIndex: 1, userType: _userType),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: Colors.grey[600]),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  String _formatDateTime(DateTime date) {
    final months = [
      'Enero',
      'Febrero',
      'Marzo',
      'Abril',
      'Mayo',
      'Junio',
      'Julio',
      'Agosto',
      'Septiembre',
      'Octubre',
      'Noviembre',
      'Diciembre',
    ];
    return '${date.day} de ${months[date.month - 1]} de ${date.year}, '
        '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
  }

  void _mostrarModalRegistrarAsistencia() {
    if (_participacion == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('No se encontró la participación'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder:
          (context) => _ModalRegistrarAsistencia(
            eventoId: widget.eventoId,
            participacionId: _participacion!.id,
            eventoTitulo: _evento?.titulo ?? 'Evento',
            onAsistenciaRegistrada: () {
              setState(() {
                _asistio = true;
                if (_participacion != null) {
                  _participacion = EventoParticipacion(
                    id: _participacion!.id,
                    eventoId: _participacion!.eventoId,
                    externoId: _participacion!.externoId,
                    asistio: true,
                    puntos: _participacion!.puntos,
                    evento: _participacion!.evento,
                  );
                }
              });
            },
          ),
    );
  }
}

// Modal de compartir (igual que en Laravel)
class _ModalCompartir extends StatefulWidget {
  final int eventoId;
  final String eventoTitulo;
  final String eventoUrl;
  final VoidCallback onCompartido;

  const _ModalCompartir({
    required this.eventoId,
    required this.eventoTitulo,
    required this.eventoUrl,
    required this.onCompartido,
  });

  @override
  State<_ModalCompartir> createState() => _ModalCompartirState();
}

class _ModalCompartirState extends State<_ModalCompartir> {
  bool _mostrarQR = false;

  Future<void> _copiarEnlace() async {
    try {
      await Clipboard.setData(ClipboardData(text: widget.eventoUrl));

      // Registrar compartido en backend con método 'link'
      final userData = await StorageService.getUserData();
      final isAuthenticated = userData != null;

      if (isAuthenticated) {
        await ApiService.compartirEvento(widget.eventoId, metodo: 'link');
      } else {
        await ApiService.compartirEventoPublico(
          widget.eventoId,
          metodo: 'link',
        );
      }

      widget.onCompartido();

      if (!mounted) return;
      Navigator.of(context).pop(); // Cerrar modal
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('¡Enlace copiado al portapapeles!'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 2),
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al copiar: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _mostrarCodigoQR() async {
    // Registrar compartido en backend con método 'qr'
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (isAuthenticated) {
      await ApiService.compartirEvento(widget.eventoId, metodo: 'qr');
    } else {
      await ApiService.compartirEventoPublico(widget.eventoId, metodo: 'qr');
    }

    widget.onCompartido();

    if (!mounted) return;
    setState(() {
      _mostrarQR = true;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Padding(
        padding: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                border: Border(
                  bottom: BorderSide(color: Color(0xFFF5F5F5), width: 1),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Compartir',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF2c3e50),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),

            // Contenido
            Padding(
              padding: const EdgeInsets.all(32),
              child: Column(
                children: [
                  // Opciones de compartir
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      // Copiar enlace
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.link,
                          label: 'Copiar enlace',
                          color: const Color(0xFFF5F5F5),
                          iconColor: const Color(0xFF2c3e50),
                          onTap: _copiarEnlace,
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Código QR
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.qr_code,
                          label: 'Código QR',
                          color: const Color(0xFF667eea),
                          iconColor: Colors.white,
                          onTap: _mostrarCodigoQR,
                        ),
                      ),
                    ],
                  ),

                  // Contenedor para el QR
                  if (_mostrarQR) ...[
                    const SizedBox(height: 24),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          QrImageView(
                            data: widget.eventoUrl,
                            version: QrVersions.auto,
                            size: 250.0,
                            backgroundColor: Colors.white,
                          ),
                          const SizedBox(height: 12),
                          const Text(
                            'Escanea este código para acceder al evento',
                            textAlign: TextAlign.center,
                            style: TextStyle(fontSize: 14, color: Colors.grey),
                          ),
                        ],
                      ),
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

  Widget _buildOpcionCompartir({
    required IconData icon,
    required String label,
    required Color color,
    required Color iconColor,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: color,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Icon(icon, size: 32, color: iconColor),
          ),
          const SizedBox(height: 12),
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Color(0xFF333333),
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}

// Modal para registrar asistencia
class _ModalRegistrarAsistencia extends StatefulWidget {
  final int eventoId;
  final int participacionId;
  final String eventoTitulo;
  final VoidCallback onAsistenciaRegistrada;

  const _ModalRegistrarAsistencia({
    required this.eventoId,
    required this.participacionId,
    required this.eventoTitulo,
    required this.onAsistenciaRegistrada,
  });

  @override
  State<_ModalRegistrarAsistencia> createState() =>
      _ModalRegistrarAsistenciaState();
}

class _ModalRegistrarAsistenciaState extends State<_ModalRegistrarAsistencia> {
  final TextEditingController _codigoController = TextEditingController();
  bool _isValidating = false;
  String? _error;
  bool _mostrarScanner = false;
  MobileScannerController? _scannerController;
  bool _isScanningImage = false;

  @override
  void dispose() {
    _codigoController.dispose();
    _scannerController?.dispose();
    super.dispose();
  }

  String _getTicketCode() {
    return 'EVT-${widget.participacionId}-${widget.eventoId}';
  }

  Future<void> _escanearDesdeGaleria() async {
    try {
      setState(() {
        _isScanningImage = true;
        _error = null;
      });

      final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
      if (picked == null) {
        setState(() {
          _isScanningImage = false;
        });
        return;
      }

      _scannerController ??= MobileScannerController();
      final capture = await _scannerController!.analyzeImage(picked.path);

      if (capture != null && capture.barcodes.isNotEmpty) {
        final barcode = capture.barcodes.first;
        if (barcode.rawValue != null) {
          final codigo = barcode.rawValue!;
          setState(() {
            _codigoController.text = codigo;
            _mostrarScanner = false;
            _scannerController?.dispose();
            _scannerController = null;
          });
          await Future.delayed(const Duration(milliseconds: 300));
          await _validarYRegistrarAsistencia();
          return;
        }
      }

      setState(() {
        _error = 'No se encontró un código QR válido en la imagen';
      });
    } catch (e) {
      setState(() {
        _error = 'No se pudo leer el QR de la imagen';
      });
    } finally {
      if (mounted) {
        setState(() {
          _isScanningImage = false;
        });
      }
    }
  }

  Future<void> _validarYRegistrarAsistencia() async {
    final codigo = _codigoController.text.trim();

    if (codigo.isEmpty) {
      setState(() {
        _error = 'Por favor ingresa el código del ticket';
      });
      return;
    }

    setState(() {
      _isValidating = true;
      _error = null;
    });

    // Validar el código
    final ticketCodeEsperado = _getTicketCode();
    if (codigo != ticketCodeEsperado) {
      setState(() {
        _isValidating = false;
        _error = 'Código inválido. Verifica el código e intenta nuevamente.';
      });
      return;
    }

    // Registrar asistencia
    final result = await ApiService.registrarAsistenciaExterno(
      widget.participacionId,
      codigo: codigo,
    );

    if (!mounted) return;

    setState(() {
      _isValidating = false;
    });

    if (result['success'] == true) {
      widget.onAsistenciaRegistrada();
      Navigator.of(context).pop();

      // Mostrar diálogo de confirmación
      if (mounted) {
        showDialog(
          context: context,
          barrierDismissible: false,
          builder:
              (context) => AlertDialog(
                title: Row(
                  children: [
                    Icon(
                      Icons.check_circle,
                      color: Colors.green[700],
                      size: 32,
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Text(
                        'Asistencia Completada',
                        style: TextStyle(fontSize: 20),
                      ),
                    ),
                  ],
                ),
                content: const Text(
                  'Tu asistencia ha sido registrada exitosamente. ¡Gracias por participar!',
                ),
                actions: [
                  ElevatedButton(
                    onPressed: () => Navigator.of(context).pop(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                      foregroundColor: Colors.white,
                    ),
                    child: const Text('Aceptar'),
                  ),
                ],
              ),
        );
      }
    } else {
      setState(() {
        _error = result['error'] as String? ?? 'Error al registrar asistencia';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Registrar Asistencia',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.of(context).pop(),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              widget.eventoTitulo,
              style: TextStyle(fontSize: 14, color: Colors.grey[600]),
            ),
            const SizedBox(height: 24),

            // Instrucciones
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.blue[200]!),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(
                        Icons.info_outline,
                        color: Colors.blue[700],
                        size: 20,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Ingresa el código de tu ticket',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Colors.blue[700],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Puedes encontrar el código en tu ticket o escanear el código QR desde la pantalla de tus participaciones.',
                    style: TextStyle(fontSize: 12, color: Colors.blue[700]),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Opciones: Escanear QR, subir imagen o pegar código
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      setState(() {
                        _mostrarScanner = true;
                        _scannerController = MobileScannerController();
                      });
                    },
                    icon: const Icon(Icons.qr_code_scanner),
                    label: const Text('Escanear QR'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: _isScanningImage ? null : _escanearDesdeGaleria,
                    icon:
                        _isScanningImage
                            ? const SizedBox(
                              height: 16,
                              width: 16,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            )
                            : const Icon(Icons.image_search),
                    label: Text(
                      _isScanningImage ? 'Leyendo...' : 'Desde galería',
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      setState(() {
                        _mostrarScanner = false;
                        _scannerController?.dispose();
                        _scannerController = null;
                      });
                    },
                    icon: const Icon(Icons.edit),
                    label: const Text('Pegar código'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Scanner QR o campo de código
            if (_mostrarScanner) ...[
              Container(
                height: 300,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey[300]!),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Stack(
                    children: [
                      MobileScanner(
                        controller: _scannerController,
                        onDetect: (capture) {
                          final List<Barcode> barcodes = capture.barcodes;
                          for (final barcode in barcodes) {
                            if (barcode.rawValue != null) {
                              final codigo = barcode.rawValue!;
                              _scannerController?.stop();
                              setState(() {
                                _codigoController.text = codigo;
                                _mostrarScanner = false;
                                _scannerController?.dispose();
                                _scannerController = null;
                              });
                              // Validar automáticamente después de escanear
                              Future.delayed(
                                const Duration(milliseconds: 500),
                                () {
                                  _validarYRegistrarAsistencia();
                                },
                              );
                              break;
                            }
                          }
                        },
                      ),
                      Positioned(
                        top: 16,
                        right: 16,
                        child: IconButton(
                          icon: const Icon(Icons.close, color: Colors.white),
                          onPressed: () {
                            setState(() {
                              _mostrarScanner = false;
                              _scannerController?.dispose();
                              _scannerController = null;
                            });
                          },
                          style: IconButton.styleFrom(
                            backgroundColor: Colors.black54,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ] else ...[
              // Campo de código
              TextField(
                controller: _codigoController,
                decoration: InputDecoration(
                  labelText: 'Código del ticket',
                  hintText: 'Ej: EVT-123-456',
                  prefixIcon: const Icon(Icons.qr_code),
                  suffixIcon:
                      _codigoController.text.isNotEmpty
                          ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              _codigoController.clear();
                              setState(() {
                                _error = null;
                              });
                            },
                          )
                          : null,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  errorText: _error,
                ),
                textInputAction: TextInputAction.done,
                onChanged: (_) => setState(() {}),
                onSubmitted: (_) => _validarYRegistrarAsistencia(),
              ),
            ],
            const SizedBox(height: 24),

            // Botón de validar
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _isValidating ? null : _validarYRegistrarAsistencia,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child:
                    _isValidating
                        ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(
                              Colors.white,
                            ),
                          ),
                        )
                        : const Text(
                          'Validar y Registrar Asistencia',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }
}
