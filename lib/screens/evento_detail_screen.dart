import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:image_picker/image_picker.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../widgets/breadcrumbs.dart';
import '../widgets/atoms/app_avatar.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
import '../widgets/organisms/skeleton_loader.dart';
import '../utils/image_helper.dart';
import '../utils/navigation_helper.dart';
import '../config/api_config.dart';
import 'ong/gestion_participantes_screen.dart';
import 'ong/dashboard_evento_mejorado_screen.dart';
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
          backgroundColor: _reaccionado ? AppColors.error : AppColors.grey600,
          duration: AppDuration.slow,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar reacción',
          ),
          backgroundColor: AppColors.error,
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
          backgroundColor: AppColors.warning,
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
      backgroundColor: AppColors.black.withOpacity(0),
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
          AppIcon.md(Icons.favorite, color: AppColors.error),
          const SizedBox(width: 8),
          Text('${_usuariosQueReaccionaron.length} Reacciones'),
        ],
      ),
      content: SizedBox(
        width: double.maxFinite,
        child:
            _isLoadingUsuariosReaccion
                ? LoadingState.list()
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
                      leading: AppAvatar.sm(
                        imageUrl:
                            fotoPerfil != null
                                ? (ImageHelper.buildImageUrl(fotoPerfil) ?? '')
                                : null,
                        initials: nombre.isNotEmpty ? nombre[0] : '?',
                        backgroundColor: AppColors.errorLight,
                        foregroundColor: AppColors.error,
                      ),
                      title: Text(nombre),
                      subtitle: correo.isNotEmpty ? Text(correo) : null,
                      trailing: AppIcon.sm(Icons.favorite, color: AppColors.error),
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
          backgroundColor: AppColors.success,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al inscribirse'),
          backgroundColor: AppColors.error,
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
          backgroundColor: AppColors.warning,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al cancelar'),
          backgroundColor: AppColors.error,
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
              icon: AppIcon.md(Icons.edit),
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
                    ? SkeletonLoader.eventDetail()
                    : _error != null
                    ? ErrorView.serverError(onRetry: _loadEvento, errorDetails: _error)
                    : _evento == null
                    ? ErrorView.notFound()
                    : SingleChildScrollView(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Galería de imágenes
                          if (_evento!.imagenes != null &&
                              _evento!.imagenes!.isNotEmpty) ...[
                            Text('Imágenes del Evento', style: AppTypography.headlineSmall),
                            const SizedBox(height: AppSpacing.sm),
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

                                  return Padding(
                                    padding: const EdgeInsets.only(right: AppSpacing.sm),
                                    child: SizedBox(
                                      width: 300,
                                      child: AppCard(
                                        elevated: true,
                                        padding: EdgeInsets.zero,
                                        child: ClipRRect(
                                          borderRadius:
                                              BorderRadius.circular(AppRadius.card),
                                          child: CachedNetworkImage(
                                            imageUrl: imageUrl,
                                            fit: BoxFit.cover,
                                            placeholder: (context, url) {
                                              return Container(
                                                color: AppColors.grey100,
                                              );
                                            },
                                            errorWidget: (context, url, error) {
                                              return Container(
                                                color: AppColors.grey100,
                                                child: Center(
                                                  child: AppIcon.lg(
                                                    Icons.image_not_supported,
                                                    color: AppColors.textTertiary,
                                                  ),
                                                ),
                                              );
                                            },
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
                                  style: AppTypography.headlineSmall,
                                ),
                              ),
                              const SizedBox(width: 12),
                              // Botón de reacción
                              Column(
                                children: [
                                  IconButton(
                                    icon: AppIcon.lg(
                                      _reaccionado
                                          ? Icons.favorite
                                          : Icons.favorite_border,
                                      color:
                                          _reaccionado
                                              ? AppColors.error
                                              : AppColors.textSecondary,
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
                                      style: AppTypography.labelSmall,
                                    ),
                                  if (_userType == 'ONG' &&
                                      _totalReacciones > 0)
                                    AppButton.text(
                                      label: 'Ver quién reaccionó',
                                      icon: Icons.people,
                                      onPressed: _cargarUsuariosQueReaccionaron,
                                      minimumSize: const Size(0, AppSizes.buttonHeightSm),
                                    ),
                                ],
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),

                          // Estado e inscripción
                          Row(
                            children: [
                              _evento!.estado == 'publicado'
                                  ? AppBadge.success(
                                      label: _evento!.estado.toUpperCase(),
                                      icon: Icons.public,
                                    )
                                  : AppBadge.neutral(
                                      label: _evento!.estado.toUpperCase(),
                                      icon: Icons.info_outline,
                                    ),
                              if (_isInscrito) ...[
                                const SizedBox(width: 8),
                                AppBadge.info(
                                  label: 'INSCRITO',
                                  icon: Icons.verified,
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
                            Text('Descripción', style: AppTypography.titleLarge),
                            const SizedBox(height: AppSpacing.xs),
                            Text(
                              _evento!.descripcion!,
                              style: AppTypography.bodyMedium.copyWith(
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ],

                          // Botones de acción
                          const SizedBox(height: 32),
                          if (_isCheckingInscripcion)
                            LoadingState.card()
                          else if (_isInscrito) ...[
                            // Botón de registrar asistencia para inscritos (si aún no asistieron)
                            if (_evento != null && !_asistio)
                              SizedBox(
                                width: double.infinity,
                                child: AppButton.primary(
                                  onPressed: _mostrarModalRegistrarAsistencia,
                                  icon: Icons.check_circle,
                                  label: 'Registrar Asistencia',
                                ),
                              ),
                            // Mostrar estado de asistencia si ya asistió
                            if (_asistio) ...[
                              const SizedBox(height: 8),
                              AppCard(
                                backgroundColor: AppColors.successLight,
                                child: Row(
                                  children: [
                                    AppIcon.md(
                                      Icons.check_circle,
                                      color: AppColors.successDark,
                                    ),
                                    const SizedBox(width: AppSpacing.md),
                                    Expanded(
                                      child: Text('Asistencia registrada', style: AppTypography.titleSmall),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                            const SizedBox(height: 16),
                            SizedBox(
                              width: double.infinity,
                              child: AppButton.outlined(
                                onPressed: _isProcessing ? null : _cancelarInscripcion,
                                icon: Icons.cancel,
                                label: _isProcessing ? 'Cancelando...' : 'Cancelar Inscripción',
                                isLoading: _isProcessing,
                              ),
                            ),
                          ] else if (_userType != 'ONG' &&
                              _userType != 'empresa') ...[
                            if (_evento!.puedeInscribirse)
                              SizedBox(
                                width: double.infinity,
                                child: AppButton.primary(
                                  onPressed: _isProcessing ? null : _inscribirse,
                                  icon: Icons.check_circle,
                                  label: _isProcessing ? 'Inscribiendo...' : 'Inscribirse al Evento',
                                  isLoading: _isProcessing,
                                ),
                              )
                            else
                              SizedBox(
                                width: double.infinity,
                                child: AppButton.outlined(
                                  onPressed: null,
                                  label: 'Inscripciones Cerradas',
                                  icon: Icons.lock_outline,
                                ),
                              ),
                          ],

                          // Botón de compartir
                          const SizedBox(height: 16),
                          SizedBox(
                            width: double.infinity,
                            child: AppButton.secondary(
                              onPressed: _compartirEvento,
                              icon: Icons.share,
                              label:
                                  _isLoadingCompartidos
                                      ? 'Cargando...'
                                      : 'Compartir${_totalCompartidos > 0 ? ' ($_totalCompartidos)' : ''}',
                              isLoading: _isLoadingCompartidos,
                            ),
                          ),

                          // Botones de gestión (solo para ONG)
                          if (_userType == 'ONG') ...[
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Expanded(
                                  child: AppButton.secondary(
                                    onPressed: () {
                                      Navigator.push(
                                        context,
                                        NavigationHelper.slideRightRoute(
                                          DashboardEventoMejoradoScreen(
                                            eventoId: widget.eventoId,
                                            eventoTitulo: _evento?.titulo,
                                          ),
                                        ),
                                      );
                                    },
                                    icon: Icons.dashboard,
                                    label: 'Dashboard',
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: AppButton.outlined(
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
                                    icon: Icons.people,
                                    label: 'Participantes',
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
        AppIcon.sm(icon, color: AppColors.textSecondary),
        const SizedBox(width: AppSpacing.md),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: AppTypography.labelSmall,
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: AppTypography.bodyLarge,
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
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppColors.black.withOpacity(0),
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
          backgroundColor: AppColors.success,
          duration: AppDuration.slow,
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al copiar: ${e.toString()}'),
          backgroundColor: AppColors.error,
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
        color: AppColors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(AppRadius.modal)),
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
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: const BoxDecoration(
                border: Border(
                  bottom: BorderSide(color: AppColors.borderLight, width: 1),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Compartir',
                    style: AppTypography.titleLarge,
                  ),
                  IconButton(
                    icon: AppIcon.md(Icons.close),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),

            // Contenido
            Padding(
              padding: const EdgeInsets.all(AppSpacing.xl),
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
                          backgroundColor: AppColors.grey100,
                          foregroundColor: AppColors.textPrimary,
                          onTap: _copiarEnlace,
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Código QR
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.qr_code,
                          label: 'Código QR',
                          backgroundColor: AppColors.primary,
                          foregroundColor: AppColors.textOnPrimary,
                          onTap: _mostrarCodigoQR,
                        ),
                      ),
                    ],
                  ),

                  // Contenedor para el QR
                  if (_mostrarQR) ...[
                    const SizedBox(height: 24),
                    AppCard(
                      elevated: true,
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        children: [
                          QrImageView(
                            data: widget.eventoUrl,
                            version: QrVersions.auto,
                            size: 250.0,
                            backgroundColor: AppColors.white,
                          ),
                          const SizedBox(height: 12),
                          Text(
                            'Escanea este código para acceder al evento',
                            textAlign: TextAlign.center,
                            style: AppTypography.bodySecondary,
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
    required Color backgroundColor,
    required Color foregroundColor,
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
              color: backgroundColor,
              borderRadius: BorderRadius.circular(16),
              boxShadow: AppElevation.cardShadow,
            ),
            child: Icon(icon, size: 32, color: foregroundColor),
          ),
          const SizedBox(height: 12),
          Text(
            label,
            style: AppTypography.labelLarge,
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
                    AppIcon.md(Icons.check_circle, color: AppColors.successDark),
                    const SizedBox(width: AppSpacing.md),
                    Expanded(
                      child: Text(
                        'Asistencia completada',
                        style: AppTypography.titleLarge,
                      ),
                    ),
                  ],
                ),
                content: Text(
                  'Tu asistencia ha sido registrada exitosamente. ¡Gracias por participar!',
                  style: AppTypography.bodyMedium,
                ),
                actions: [
                  AppButton.primary(
                    label: 'Aceptar',
                    icon: Icons.check,
                    minimumSize: const Size(0, AppSizes.buttonHeightSm),
                    onPressed: () => Navigator.of(context).pop(),
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
        color: AppColors.white,
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(AppRadius.modal),
        ),
      ),
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.xl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Registrar asistencia',
                  style: AppTypography.titleLarge,
                ),
                IconButton(
                  icon: AppIcon.md(Icons.close),
                  onPressed: () => Navigator.of(context).pop(),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              widget.eventoTitulo,
              style: AppTypography.bodySecondary,
            ),
            const SizedBox(height: 24),

            // Instrucciones
            AppCard(
              backgroundColor: AppColors.infoLight,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      AppIcon.sm(Icons.info_outline, color: AppColors.infoDark),
                      const SizedBox(width: AppSpacing.xs),
                      Expanded(
                        child: Text(
                          'Ingresa el código de tu ticket',
                          style: AppTypography.titleSmall.copyWith(
                            color: AppColors.infoDark,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Puedes encontrar el código en tu ticket o escanear el código QR desde la pantalla de tus participaciones.',
                    style: AppTypography.bodySmall.copyWith(
                      color: AppColors.infoDark,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Opciones: Escanear QR, subir imagen o pegar código
            LayoutBuilder(
              builder: (context, constraints) {
                final isNarrow = constraints.maxWidth < AppBreakpoints.mobile;

                final scanQrButton = SizedBox(
                  width: isNarrow ? double.infinity : null,
                  child: AppButton.outlined(
                    onPressed: () {
                      setState(() {
                        _mostrarScanner = true;
                        _scannerController = MobileScannerController();
                      });
                    },
                    icon: Icons.qr_code_scanner,
                    label: 'Escanear QR',
                  ),
                );

                final scanImageButton = SizedBox(
                  width: isNarrow ? double.infinity : null,
                  child: AppButton.outlined(
                    onPressed: _isScanningImage ? null : _escanearDesdeGaleria,
                    icon: Icons.image_search,
                    label: _isScanningImage ? 'Leyendo...' : 'Desde galería',
                    isLoading: _isScanningImage,
                  ),
                );

                final pasteCodeButton = SizedBox(
                  width: isNarrow ? double.infinity : null,
                  child: AppButton.outlined(
                    onPressed: () {
                      setState(() {
                        _mostrarScanner = false;
                        _scannerController?.dispose();
                        _scannerController = null;
                      });
                    },
                    icon: Icons.edit,
                    label: 'Pegar código',
                  ),
                );

                if (isNarrow) {
                  return Column(
                    children: [
                      scanQrButton,
                      const SizedBox(height: AppSpacing.sm),
                      scanImageButton,
                      const SizedBox(height: AppSpacing.sm),
                      pasteCodeButton,
                    ],
                  );
                }

                return Row(
                  children: [
                    Expanded(child: scanQrButton),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(child: scanImageButton),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(child: pasteCodeButton),
                  ],
                );
              },
            ),
            const SizedBox(height: 16),

            // Scanner QR o campo de código
            if (_mostrarScanner) ...[
              Container(
                height: 300,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(AppRadius.md),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(AppRadius.md),
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
                                AppDuration.slow,
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
                          icon: AppIcon.md(Icons.close, color: AppColors.white),
                          onPressed: () {
                            setState(() {
                              _mostrarScanner = false;
                              _scannerController?.dispose();
                              _scannerController = null;
                            });
                          },
                          style: IconButton.styleFrom(
                            backgroundColor: AppColors.scrim,
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
                  prefixIcon: AppIcon.md(Icons.qr_code),
                  suffixIcon:
                      _codigoController.text.isNotEmpty
                          ? IconButton(
                            icon: AppIcon.sm(Icons.clear),
                            onPressed: () {
                              _codigoController.clear();
                              setState(() {
                                _error = null;
                              });
                            },
                          )
                          : null,
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
              child: AppButton.primary(
                onPressed: _isValidating ? null : _validarYRegistrarAsistencia,
                icon: Icons.check_circle,
                label: 'Validar y registrar asistencia',
                isLoading: _isValidating,
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }
}
