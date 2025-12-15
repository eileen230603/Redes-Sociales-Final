import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/evento.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
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
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadEventos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body: _responsiveBody(_buildBody()),
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 1, userType: userType);
        },
      ),
    );
  }

  Widget _responsiveBody(Widget child) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final maxWidth =
            constraints.maxWidth >= AppBreakpoints.desktop ? 900.0 : double.infinity;
        return Center(
          child: ConstrainedBox(
            constraints: BoxConstraints(maxWidth: maxWidth),
            child: child,
          ),
        );
      },
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return LoadingState.list();
    }

    if (_error != null) {
      return ErrorView.serverError(
        onRetry: _loadEventos,
        errorDetails: _error,
      );
    }

    if (_eventos.isEmpty) {
      return EmptyState(
        icon: Icons.event_busy,
        title: 'No hay eventos disponibles',
        message: 'Vuelve a intentarlo en unos minutos.',
        actionLabel: 'Actualizar',
        onAction: _loadEventos,
      );
    }

    return RefreshIndicator(
      onRefresh: _loadEventos,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _eventos.length,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.md),
        itemBuilder: (context, index) {
          final evento = _eventos[index];
          return _buildEventoCard(evento);
        },
      ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    final imagenUrl = ImageHelper.getFirstImageUrl(evento.imagenes);

    return AppCard(
      elevated: true,
      padding: EdgeInsets.zero,
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
          ClipRRect(
            borderRadius: const BorderRadius.vertical(
              top: Radius.circular(AppRadius.card),
            ),
            child: Stack(
              children: [
                _buildEventoImage(imagenUrl),
                Positioned(
                  top: AppSpacing.sm,
                  right: AppSpacing.sm,
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      color: AppColors.white.withOpacity(0.92),
                      shape: BoxShape.circle,
                      boxShadow: AppElevation.cardShadow,
                    ),
                    child: IconButton(
                      icon: AppIcon.md(
                        _eventosReaccionados[evento.id] == true
                            ? Icons.favorite
                            : Icons.favorite_border,
                        color:
                            _eventosReaccionados[evento.id] == true
                                ? AppColors.error
                                : AppColors.textSecondary,
                      ),
                      onPressed: () => _toggleReaccionEnCard(evento.id),
                      tooltip:
                          _eventosReaccionados[evento.id] == true
                              ? 'Quitar reacción'
                              : 'Reaccionar',
                    ),
                  ),
                ),
                if ((_totalReaccionesPorEvento[evento.id] ?? 0) > 0)
                  Positioned(
                    bottom: AppSpacing.sm,
                    right: AppSpacing.sm,
                    child: AppBadge.error(
                      label: '${_totalReaccionesPorEvento[evento.id] ?? 0}',
                      icon: Icons.favorite,
                    ),
                  ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        evento.titulo,
                        style: AppTypography.titleLarge,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    if (!evento.puedeInscribirse)
                      AppBadge.neutral(
                        label: 'Cerrado',
                        icon: Icons.lock_outline,
                      ),
                  ],
                ),
                const SizedBox(height: AppSpacing.sm),
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.xs,
                  children: [
                    _MetaPill(
                      icon: Icons.category,
                      label: evento.tipoEvento,
                    ),
                    _MetaPill(
                      icon: Icons.calendar_today,
                      label: _formatDate(evento.fechaInicio),
                    ),
                    if (evento.ciudad != null && evento.ciudad!.isNotEmpty)
                      _MetaPill(
                        icon: Icons.location_on,
                        label: evento.ciudad!,
                      ),
                  ],
                ),
                if (evento.descripcion != null && evento.descripcion!.isNotEmpty) ...[
                  const SizedBox(height: AppSpacing.sm),
                  Text(
                    evento.descripcion!,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: AppTypography.bodySecondary,
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
                      ),
                    ),
                    const SizedBox(width: AppSpacing.xxs),
                    AppIcon.xs(
                      Icons.arrow_forward_ios,
                      color: AppColors.primary,
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEventoImage(String? imagenUrl) {
    const height = 180.0;

    if (imagenUrl == null) {
      return Container(
        height: height,
        width: double.infinity,
        color: AppColors.grey100,
        child: Center(
          child: AppIcon.xl(Icons.event, color: AppColors.textTertiary),
        ),
      );
    }

    return CachedNetworkImage(
      imageUrl: imagenUrl,
      height: height,
      width: double.infinity,
      fit: BoxFit.cover,
      placeholder: (context, url) {
        return Container(
          height: height,
          width: double.infinity,
          color: AppColors.grey100,
        );
      },
      errorWidget: (context, url, error) {
        return Container(
          height: height,
          width: double.infinity,
          color: AppColors.grey100,
          child: Center(
            child: AppIcon.lg(
              Icons.image_not_supported,
              color: AppColors.textTertiary,
            ),
          ),
        );
      },
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

class _MetaPill extends StatelessWidget {
  final IconData icon;
  final String label;

  const _MetaPill({
    required this.icon,
    required this.label,
  });

  @override
  Widget build(BuildContext context) {
    return DecoratedBox(
      decoration: BoxDecoration(
        color: AppColors.grey100,
        borderRadius: BorderRadius.circular(AppRadius.full),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.sm,
          vertical: AppSpacing.xxs,
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            AppIcon.xs(icon, color: AppColors.textSecondary),
            const SizedBox(width: AppSpacing.xxs),
            Text(
              label,
              style: AppTypography.labelMedium,
            ),
          ],
        ),
      ),
    );
  }
}
