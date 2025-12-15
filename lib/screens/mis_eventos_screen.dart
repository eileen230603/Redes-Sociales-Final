import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../models/evento_participacion.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../utils/navigation_helper.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav_bar.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
import 'evento_detail_screen.dart';

class MisEventosScreen extends StatefulWidget {
  const MisEventosScreen({super.key});

  @override
  State<MisEventosScreen> createState() => _MisEventosScreenState();
}

class _MisEventosScreenState extends State<MisEventosScreen> {
  List<EventoParticipacion> _participaciones = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadMisEventos();
  }

  Future<void> _loadMisEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getMisEventos();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _participaciones =
            result['participaciones'] as List<EventoParticipacion>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/mis-eventos'),
      appBar: AppBar(
        title: const Text('Mis Eventos'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadMisEventos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body: _responsiveBody(_buildBody()),
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 2, userType: userType);
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
        onRetry: _loadMisEventos,
        errorDetails: _error,
      );
    }

    if (_participaciones.isEmpty) {
      return EmptyState(
        icon: Icons.event_available_outlined,
        title: 'No tienes eventos inscritos',
        message: 'Explora los eventos disponibles y únete a uno.',
        actionLabel: 'Actualizar',
        onAction: _loadMisEventos,
      );
    }

    return RefreshIndicator(
      onRefresh: _loadMisEventos,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _participaciones.length,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.md),
        itemBuilder: (context, index) {
          final participacion = _participaciones[index];
          return _buildParticipacionCard(participacion);
        },
      ),
    );
  }

  Widget _buildParticipacionCard(EventoParticipacion participacion) {
    final evento = participacion.evento;
    if (evento == null) {
      return const SizedBox.shrink();
    }

    return AppCard(
      elevated: true,
      onTap: () {
        Navigator.push(
          context,
          NavigationHelper.slideRightRoute(
            EventoDetailScreen(eventoId: evento.id),
          ),
        ).then((_) => _loadMisEventos());
      },
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
              const SizedBox(width: AppSpacing.sm),
              participacion.asistio
                  ? AppBadge.success(label: 'Asistió', icon: Icons.check_circle)
                  : AppBadge.info(label: 'Inscrito', icon: Icons.how_to_reg),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Wrap(
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.xs,
            children: [
              _MetaPill(icon: Icons.category, label: evento.tipoEvento),
              _MetaPill(icon: Icons.calendar_today, label: _formatDate(evento.fechaInicio)),
              if (evento.ciudad != null && evento.ciudad!.isNotEmpty)
                _MetaPill(icon: Icons.location_on, label: evento.ciudad!),
            ],
          ),
          if (participacion.puntos > 0) ...[
            const SizedBox(height: AppSpacing.sm),
            AppBadge.warning(
              label: '${participacion.puntos} puntos',
              icon: Icons.star,
            ),
          ],
          const SizedBox(height: AppSpacing.md),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  IconButton(
                    icon: AppIcon.md(Icons.qr_code, color: AppColors.primary),
                    onPressed: () => _mostrarTicketQR(context, participacion),
                    tooltip: 'Ver QR del ticket',
                  ),
                  IconButton(
                    icon: AppIcon.md(Icons.copy, color: AppColors.primary),
                    onPressed: () => _copiarTicket(context, participacion),
                    tooltip: 'Copiar código del ticket',
                  ),
                ],
              ),
              Row(
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
        ],
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

  String _getTicketCode(EventoParticipacion participacion) {
    return 'EVT-${participacion.id}-${participacion.eventoId}';
  }

  void _mostrarTicketQR(
    BuildContext context,
    EventoParticipacion participacion,
  ) {
    final ticketCode = _getTicketCode(participacion);
    final evento = participacion.evento;

    showDialog(
      context: context,
      builder: (context) {
        return Dialog(
          child: Container(
            constraints: const BoxConstraints(maxWidth: 400, maxHeight: 600),
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        'Ticket - ${evento?.titulo ?? "Evento"}',
                        style: AppTypography.titleLarge,
                      ),
                    ),
                    IconButton(
                      icon: AppIcon.md(Icons.close),
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                  ],
                ),
                const SizedBox(height: AppSpacing.lg),
                AppCard(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: QrImageView(
                    data: ticketCode,
                    version: QrVersions.auto,
                    size: 250.0,
                    backgroundColor: AppColors.white,
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
                AppCard(
                  padding: const EdgeInsets.all(AppSpacing.sm),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(
                          'Código: $ticketCode',
                          style: AppTypography.labelLarge,
                        ),
                      ),
                      IconButton(
                        icon: AppIcon.sm(Icons.copy, color: AppColors.primary),
                        onPressed: () {
                          Clipboard.setData(ClipboardData(text: ticketCode));
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: const Text('Código copiado al portapapeles'),
                              backgroundColor: AppColors.success,
                              duration: const Duration(seconds: 2),
                            ),
                          );
                        },
                        tooltip: 'Copiar código',
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: AppSpacing.lg),
                SizedBox(
                  width: double.infinity,
                  child: AppButton.primary(
                    label: 'Cerrar',
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void _copiarTicket(BuildContext context, EventoParticipacion participacion) {
    final ticketCode = _getTicketCode(participacion);
    Clipboard.setData(ClipboardData(text: ticketCode));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: const Text('Código del ticket copiado al portapapeles'),
        backgroundColor: AppColors.success,
      ),
    );
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
            Text(label, style: AppTypography.labelMedium),
          ],
        ),
      ),
    );
  }
}
