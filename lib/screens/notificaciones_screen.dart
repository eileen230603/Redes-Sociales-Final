import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/notificacion.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
import 'evento_detail_screen.dart';

class NotificacionesScreen extends StatefulWidget {
  const NotificacionesScreen({super.key});

  @override
  State<NotificacionesScreen> createState() => _NotificacionesScreenState();
}

class _NotificacionesScreenState extends State<NotificacionesScreen> {
  List<Notificacion> _notificaciones = [];
  int _noLeidas = 0;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadNotificaciones();
  }

  Future<void> _loadNotificaciones() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getNotificaciones();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _notificaciones = result['notificaciones'] as List<Notificacion>;
        _noLeidas = result['no_leidas'] as int? ?? 0;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar notificaciones';
        _notificaciones = [];
      }
    });
  }

  Future<void> _marcarComoLeida(Notificacion notificacion) async {
    if (notificacion.leida) return;

    final result = await ApiService.marcarNotificacionLeida(notificacion.id);

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        final index = _notificaciones.indexWhere(
          (n) => n.id == notificacion.id,
        );
        if (index != -1) {
          _notificaciones[index] = notificacion.copyWith(leida: true);
          if (_noLeidas > 0) {
            _noLeidas--;
          }
        }
      });
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al marcar notificación',
            ),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  Future<void> _marcarTodasLeidas() async {
    if (_noLeidas == 0) return;

    final result = await ApiService.marcarTodasLeidas();

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _notificaciones =
            _notificaciones.map((n) => n.copyWith(leida: true)).toList();
        _noLeidas = 0;
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Todas las notificaciones marcadas como leídas'),
            backgroundColor: AppColors.success,
          ),
        );
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al marcar notificaciones',
            ),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  void _navegarAEvento(int? eventoId) {
    if (eventoId == null) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => EventoDetailScreen(eventoId: eventoId),
      ),
    ).then((_) {
      // Recargar notificaciones al volver
      _loadNotificaciones();
    });
  }

  IconData _getIconoTipo(String tipo) {
    switch (tipo.toLowerCase()) {
      case 'reaccion':
      case 'reaccion_evento':
        return Icons.favorite;
      case 'nueva_participacion':
      case 'participacion':
        return Icons.person_add;
      case 'evento':
        return Icons.event;
      default:
        return Icons.notifications;
    }
  }

  Color _getColorTipo(String tipo) {
    switch (tipo.toLowerCase()) {
      case 'reaccion':
      case 'reaccion_evento':
        return AppColors.error;
      case 'nueva_participacion':
      case 'participacion':
        return AppColors.info;
      case 'evento':
        return AppColors.success;
      default:
        return AppColors.textSecondary;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            const Expanded(
              child: Text('Notificaciones', overflow: TextOverflow.ellipsis),
            ),
            if (_noLeidas > 0)
              AppBadge.error(
                label: _noLeidas > 99 ? '99+' : _noLeidas.toString(),
                icon: Icons.notifications_active,
              ),
          ],
        ),
        actions: [
          if (_noLeidas > 0)
            Padding(
              padding: const EdgeInsets.only(right: AppSpacing.xs),
              child: AppButton.text(
                label: 'Marcar todas',
                icon: Icons.done_all,
                onPressed: _marcarTodasLeidas,
                minimumSize: const Size(0, AppSizes.buttonHeightSm),
              ),
            ),
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadNotificaciones,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return LoadingState.list();
    }

    if (_error != null) {
      return ErrorView.serverError(
        onRetry: _loadNotificaciones,
        errorDetails: _error,
      );
    }

    if (_notificaciones.isEmpty) {
      return const EmptyState(
        icon: Icons.notifications_none,
        title: 'No tienes notificaciones',
        message: 'Cuando ocurra algo relevante, aparecerá aquí.',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadNotificaciones,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _notificaciones.length,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.sm),
        itemBuilder: (context, index) {
          final notificacion = _notificaciones[index];
          return _buildNotificacionItem(notificacion);
        },
      ),
    );
  }

  Widget _buildNotificacionItem(Notificacion notificacion) {
    final colorTipo = _getColorTipo(notificacion.tipo);
    final iconoTipo = _getIconoTipo(notificacion.tipo);

    final bool isUnread = !notificacion.leida;

    return Material(
      color: AppColors.black.withOpacity(0),
      child: InkWell(
        borderRadius: BorderRadius.circular(AppRadius.card),
        onTap: () {
          _marcarComoLeida(notificacion);
          if (notificacion.eventoId != null) {
            _navegarAEvento(notificacion.eventoId);
          }
        },
        child: Container(
          padding: const EdgeInsets.all(AppSpacing.md),
          decoration: BoxDecoration(
            color: isUnread ? AppColors.grey50 : AppColors.white,
            borderRadius: BorderRadius.circular(AppRadius.card),
            border: Border.all(
              color: isUnread ? colorTipo.withOpacity(0.35) : AppColors.borderLight,
            ),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: AppSizes.avatarLg,
                height: AppSizes.avatarLg,
                decoration: BoxDecoration(
                  color: colorTipo.withOpacity(0.12),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: AppIcon.md(iconoTipo, color: colorTipo),
                ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            notificacion.titulo,
                            style: isUnread
                                ? AppTypography.titleSmall.copyWith(
                                    fontWeight: FontWeight.w700,
                                  )
                                : AppTypography.titleSmall,
                          ),
                        ),
                        if (isUnread) ...[
                          const SizedBox(width: AppSpacing.sm),
                          Container(
                            width: 8,
                            height: 8,
                            decoration: BoxDecoration(
                              color: colorTipo,
                              shape: BoxShape.circle,
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      notificacion.mensaje,
                      style: AppTypography.bodySecondary,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    if (notificacion.eventoTitulo != null &&
                        notificacion.eventoTitulo!.isNotEmpty) ...[
                      const SizedBox(height: AppSpacing.xs),
                      Row(
                        children: [
                          AppIcon.xs(Icons.event, color: AppColors.textTertiary),
                          const SizedBox(width: AppSpacing.xxs),
                          Expanded(
                            child: Text(
                              notificacion.eventoTitulo!,
                              style: AppTypography.bodySmall,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      _formatFecha(notificacion.fecha),
                      style: AppTypography.labelSmall,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _formatFecha(DateTime fecha) {
    final ahora = DateTime.now();
    final diferencia = ahora.difference(fecha);

    if (diferencia.inDays == 0) {
      if (diferencia.inHours == 0) {
        if (diferencia.inMinutes == 0) {
          return 'Hace unos momentos';
        }
        return 'Hace ${diferencia.inMinutes} minuto${diferencia.inMinutes > 1 ? 's' : ''}';
      }
      return 'Hace ${diferencia.inHours} hora${diferencia.inHours > 1 ? 's' : ''}';
    } else if (diferencia.inDays == 1) {
      return 'Ayer';
    } else if (diferencia.inDays < 7) {
      return 'Hace ${diferencia.inDays} días';
    } else {
      return '${fecha.day}/${fecha.month}/${fecha.year}';
    }
  }
}
