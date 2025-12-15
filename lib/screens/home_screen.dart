import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/storage_service.dart';
import '../services/api_service.dart';
import '../services/auth_helper.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
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
                  icon: AppIcon.md(Icons.notifications),
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
                    child: AppBadge.error(
                      label:
                          _notificacionesNoLeidas > 99
                              ? '99+'
                              : _notificacionesNoLeidas.toString(),
                    ),
                  ),
              ],
            ),
          IconButton(
            icon: AppIcon.md(Icons.refresh),
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
              ? LoadingState.detail()
              : RefreshIndicator(
                onRefresh: _loadEventos,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Saludo y nombre de usuario
                      AppCard(
                        elevated: true,
                        child: Row(
                          children: [
                            AppIcon.lg(
                              Icons.person_outline,
                              color: AppColors.primary,
                            ),
                            const SizedBox(width: AppSpacing.md),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('Bienvenido', style: AppTypography.titleLarge),
                                  const SizedBox(height: AppSpacing.xxs),
                                  Text(
                                    _userData!['user_name'] ?? 'Usuario',
                                    style: AppTypography.bodyLarge,
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: AppSpacing.lg),
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
                            style: AppTypography.titleLarge,
                          ),
                        ],
                      ),
                      const SizedBox(height: AppSpacing.md),
                      // Lista de eventos
                      if (_isLoadingEventos)
                        Column(
                          children: List.generate(
                            3,
                            (index) => LoadingState.card(),
                          ),
                        )
                      else if (_errorEventos != null)
                        ErrorView.serverError(
                          onRetry: _loadEventos,
                          errorDetails: _errorEventos,
                        )
                      else if (_eventos.isEmpty)
                        EmptyState(
                          icon: Icons.event_busy,
                          title:
                              _userData?['user_type'] == 'Empresa'
                                  ? 'Sin eventos patrocinados'
                                  : _userData?['user_type'] == 'ONG'
                                  ? 'Sin eventos creados'
                                  : 'Sin eventos disponibles',
                          message:
                              _userData?['user_type'] == 'Empresa'
                                  ? 'Explora eventos disponibles para patrocinar.'
                                  : _userData?['user_type'] == 'ONG'
                                  ? 'Crea tu primer evento para empezar.'
                                  : 'Vuelve a intentarlo en unos minutos.',
                          actionLabel: 'Actualizar',
                          onAction: _loadEventos,
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

    final isExterno = _userData?['user_type'] == 'Integrante externo';
    final isEmpresa = _userData?['user_type'] == 'Empresa';
    final isInscrito = isExterno && _eventosInscritos.contains(evento.id);
    final reaccionado = _eventosReaccionados[evento.id] == true;
    final totalReacciones = _totalReaccionesPorEvento[evento.id] ?? 0;

    void openDetail() {
      final userType = _userData?['user_type'] as String?;
      if (userType == 'Integrante externo') {
        _mostrarDetalleEventoModal(evento);
        return;
      }
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => EventoDetailScreen(eventoId: evento.id),
        ),
      ).then((_) => _loadEventos());
    }

    Widget imageFallback({required IconData icon}) {
      return Container(
        height: AppSizes.cardImageHeight,
        width: double.infinity,
        color: AppColors.grey100,
        child: Center(
          child: AppIcon.xl(icon, color: AppColors.textTertiary),
        ),
      );
    }

    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.md),
      child: AppCard(
        elevated: true,
        padding: EdgeInsets.zero,
        onTap: openDetail,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(
                    top: Radius.circular(AppRadius.card),
                  ),
                  child: SizedBox(
                    height: AppSizes.cardImageHeight,
                    width: double.infinity,
                    child:
                        imagenUrl != null
                            ? CachedNetworkImage(
                              imageUrl: imagenUrl,
                              height: AppSizes.cardImageHeight,
                              width: double.infinity,
                              fit: BoxFit.cover,
                              placeholder:
                                  (context, url) => imageFallback(
                                    icon: Icons.image_outlined,
                                  ),
                              errorWidget:
                                  (context, url, error) => imageFallback(
                                    icon: Icons.image_not_supported,
                                  ),
                            )
                            : imageFallback(icon: Icons.event),
                  ),
                ),
                if (isExterno)
                  Positioned(
                    top: AppSpacing.sm,
                    right: AppSpacing.sm,
                    child: Container(
                      decoration: BoxDecoration(
                        color: AppColors.white,
                        shape: BoxShape.circle,
                        boxShadow: AppElevation.cardShadow,
                      ),
                      child: IconButton(
                        icon: AppIcon.md(
                          reaccionado ? Icons.favorite : Icons.favorite_border,
                          color:
                              reaccionado
                                  ? AppColors.error
                                  : AppColors.textSecondary,
                        ),
                        onPressed: () => _toggleReaccionEnCard(evento.id),
                        tooltip: reaccionado ? 'Quitar reacción' : 'Reaccionar',
                      ),
                    ),
                  ),
                if (isExterno && totalReacciones > 0)
                  Positioned(
                    bottom: AppSpacing.sm,
                    right: AppSpacing.sm,
                    child: AppBadge.error(
                      label: totalReacciones.toString(),
                      icon: Icons.favorite,
                    ),
                  ),
              ],
            ),
            Padding(
              padding: const EdgeInsets.all(AppSpacing.md),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          evento.titulo,
                          style: AppTypography.titleMedium,
                        ),
                      ),
                      if (isEmpresa)
                        AppBadge.success(label: 'Patrocinado', icon: Icons.handshake)
                      else if (isInscrito)
                        AppBadge.info(label: 'Inscrito', icon: Icons.verified)
                      else if (!evento.puedeInscribirse)
                        AppBadge.neutral(label: 'Cerrado', icon: Icons.lock),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  Row(
                    children: [
                      AppIcon.xs(Icons.category, color: AppColors.textSecondary),
                      const SizedBox(width: AppSpacing.xs),
                      Expanded(
                        child: Text(
                          evento.tipoEvento,
                          style: AppTypography.bodySecondary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Row(
                    children: [
                      AppIcon.xs(Icons.calendar_today, color: AppColors.textSecondary),
                      const SizedBox(width: AppSpacing.xs),
                      Expanded(
                        child: Text(
                          _formatDate(evento.fechaInicio),
                          style: AppTypography.bodySecondary,
                        ),
                      ),
                    ],
                  ),
                  if (evento.ciudad != null) ...[
                    const SizedBox(height: AppSpacing.xs),
                    Row(
                      children: [
                        AppIcon.xs(Icons.location_on, color: AppColors.textSecondary),
                        const SizedBox(width: AppSpacing.xs),
                        Expanded(
                          child: Text(
                            evento.ciudad!,
                            style: AppTypography.bodySecondary,
                          ),
                        ),
                      ],
                    ),
                  ],
                  if (evento.descripcion != null &&
                      evento.descripcion!.isNotEmpty) ...[
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      evento.descripcion!,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: AppTypography.bodySmall,
                    ),
                  ],
                  const SizedBox(height: AppSpacing.sm),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        'Ver detalles',
                        style: AppTypography.labelLarge.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      const SizedBox(width: AppSpacing.xs),
                      AppIcon.xs(Icons.arrow_forward_ios, color: AppColors.primary),
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
                    backgroundColor: AppColors.success,
                  ),
                );
                _loadEventos();
              } else {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      result['error'] as String? ?? 'Error al inscribirse',
                    ),
                    backgroundColor: AppColors.error,
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
                      backgroundColor: AppColors.warning,
                    ),
                  );
                  _loadEventos();
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        result['error'] as String? ?? 'Error al cancelar',
                      ),
                      backgroundColor: AppColors.error,
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

    Widget imageFallback({required IconData icon}) {
      return Container(
        height: AppSizes.cardImageHeight,
        width: double.infinity,
        color: AppColors.grey100,
        child: Center(
          child: AppIcon.xl(icon, color: AppColors.textTertiary),
        ),
      );
    }

    return Dialog(
      insetPadding: const EdgeInsets.all(AppSpacing.lg),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.modal),
      ),
      child: ConstrainedBox(
        constraints: BoxConstraints(
          maxHeight: MediaQuery.of(context).size.height * 0.9,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      'Detalles del evento',
                      style: AppTypography.titleLarge,
                    ),
                  ),
                  IconButton(
                    icon: AppIcon.md(Icons.close),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            Flexible(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(AppSpacing.lg),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(AppRadius.card),
                      child: SizedBox(
                        height: AppSizes.cardImageHeight,
                        width: double.infinity,
                        child:
                            imagenUrl != null
                                ? CachedNetworkImage(
                                  imageUrl: imagenUrl,
                                  height: AppSizes.cardImageHeight,
                                  width: double.infinity,
                                  fit: BoxFit.cover,
                                  placeholder:
                                      (context, url) => imageFallback(
                                        icon: Icons.image_outlined,
                                      ),
                                  errorWidget:
                                      (context, url, error) => imageFallback(
                                        icon: Icons.image_not_supported,
                                      ),
                                )
                                : imageFallback(icon: Icons.event),
                      ),
                    ),
                    const SizedBox(height: AppSpacing.lg),
                    Text(widget.evento.titulo, style: AppTypography.headlineSmall),
                    const SizedBox(height: AppSpacing.md),
                    Wrap(
                      spacing: AppSpacing.sm,
                      runSpacing: AppSpacing.sm,
                      children: [
                        widget.evento.estado == 'publicado'
                            ? AppBadge.success(
                              label: widget.evento.estado.toUpperCase(),
                              icon: Icons.public,
                            )
                            : AppBadge.neutral(
                              label: widget.evento.estado.toUpperCase(),
                              icon: Icons.info_outline,
                            ),
                        if (widget.isInscrito)
                          AppBadge.info(label: 'Inscrito', icon: Icons.verified)
                        else if (!widget.evento.puedeInscribirse)
                          AppBadge.neutral(label: 'Cerrado', icon: Icons.lock),
                      ],
                    ),
                    const SizedBox(height: AppSpacing.lg),
                    _buildInfoRow(
                      Icons.category,
                      'Tipo',
                      widget.evento.tipoEvento,
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    _buildInfoRow(
                      Icons.calendar_today,
                      'Fecha',
                      widget.formatDate(widget.evento.fechaInicio),
                    ),
                    if (widget.evento.ciudad != null) ...[
                      const SizedBox(height: AppSpacing.sm),
                      _buildInfoRow(
                        Icons.location_on,
                        'Ciudad',
                        widget.evento.ciudad!,
                      ),
                    ],
                    if (widget.evento.direccion != null) ...[
                      const SizedBox(height: AppSpacing.sm),
                      _buildInfoRow(
                        Icons.place,
                        'Dirección',
                        widget.evento.direccion!,
                      ),
                    ],
                    if (widget.evento.descripcion != null &&
                        widget.evento.descripcion!.isNotEmpty) ...[
                      const SizedBox(height: AppSpacing.lg),
                      Text('Descripción', style: AppTypography.titleMedium),
                      const SizedBox(height: AppSpacing.xs),
                      Text(widget.evento.descripcion!, style: AppTypography.bodyMedium),
                    ],
                    const SizedBox(height: AppSpacing.xl),
                    if (widget.isChecking)
                      LoadingState.card()
                    else if (widget.isInscrito)
                      SizedBox(
                        width: double.infinity,
                        child: AppButton.outlined(
                          onPressed:
                              _isProcessing
                                  ? null
                                  : () {
                                    setState(() => _isProcessing = true);
                                    widget.onCancelarInscripcion();
                                  },
                          icon: Icons.cancel,
                          label:
                              _isProcessing
                                  ? 'Cancelando...'
                                  : 'Cancelar inscripción',
                          isLoading: _isProcessing,
                        ),
                      )
                    else if (widget.evento.puedeInscribirse)
                      SizedBox(
                        width: double.infinity,
                        child: AppButton.primary(
                          onPressed:
                              _isProcessing
                                  ? null
                                  : () {
                                    setState(() => _isProcessing = true);
                                    widget.onInscribirse();
                                  },
                          icon: Icons.check_circle,
                          label:
                              _isProcessing
                                  ? 'Inscribiendo...'
                                  : 'Inscribirse al evento',
                          isLoading: _isProcessing,
                        ),
                      )
                    else
                      SizedBox(
                        width: double.infinity,
                        child: AppButton.outlined(
                          onPressed: null,
                          icon: Icons.lock,
                          label: 'Inscripciones cerradas',
                        ),
                      ),
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
        AppIcon.sm(icon, color: AppColors.textSecondary),
        const SizedBox(width: AppSpacing.sm),
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
                style: AppTypography.bodyMedium,
              ),
            ],
          ),
        ),
      ],
    );
  }
}
