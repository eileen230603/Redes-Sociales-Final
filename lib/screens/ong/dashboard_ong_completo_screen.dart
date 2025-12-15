import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/filters/advanced_filter_widget.dart';
import '../../models/dashboard/ong_dashboard_data.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';

/// Dashboard ONG Refactorizado con el nuevo sistema de diseño
class DashboardOngCompletoScreen extends StatefulWidget {
  const DashboardOngCompletoScreen({super.key});

  @override
  State<DashboardOngCompletoScreen> createState() =>
      _DashboardOngCompletoScreenState();
}

class _DashboardOngCompletoScreenState
    extends State<DashboardOngCompletoScreen> {
  OngDashboardData? _dashboardData;
  bool _isLoading = true;
  String? _error;

  // Filtros
  DateTime? _fechaInicio;
  DateTime? _fechaFin;
  String? _estadoEvento;
  String? _tipoParticipacion;
  String? _busquedaEvento;

  @override
  void initState() {
    super.initState();
    _loadDashboard(useCache: false);
  }

  Future<void> _loadDashboard({bool useCache = true}) async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final result = await ApiService.getDashboardOngCompleto(
        fechaInicio:
            _fechaInicio != null
                ? DateFormat('yyyy-MM-dd').format(_fechaInicio!)
                : null,
        fechaFin:
            _fechaFin != null
                ? DateFormat('yyyy-MM-dd').format(_fechaFin!)
                : null,
        estadoEvento: _estadoEvento,
        tipoParticipacion: _tipoParticipacion,
        busquedaEvento: _busquedaEvento,
        useCache: useCache,
      );

      if (!mounted) return;

      setState(() {
        _isLoading = false;
        if (result['success'] == true) {
          final rawData = result['data'];
          if (rawData != null && rawData is Map<String, dynamic>) {
            _dashboardData = OngDashboardData.fromJson(rawData);
          } else {
            _error = 'Datos de dashboard vacíos o con formato incorrecto';
          }
        } else {
          _error = result['error'] as String? ?? 'Error al cargar dashboard';
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.backgroundPrimary,
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: Text('Dashboard ONG', style: AppTypography.titleLarge),
        actions: [
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: () {
              // TODO: Implementar exportación PDF
            },
            tooltip: 'Exportar PDF',
          ),
          IconButton(
            icon: const Icon(Icons.table_chart),
            onPressed: () {
              // TODO: Implementar exportación Excel
            },
            tooltip: 'Exportar Excel',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadDashboard(useCache: false),
            tooltip: 'Actualizar',
          ),
          const SizedBox(width: AppSpacing.xs),
        ],
      ),
      body: AnimatedSwitcher(
        duration: AppDuration.normal,
        child: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return SkeletonLoader.dashboard();
    }

    if (_error != null) {
      return ErrorView.network(
        onRetry: () => _loadDashboard(useCache: false),
      );
    }

    if (_dashboardData == null) {
      return ErrorView(
        icon: Icons.dashboard_outlined,
        title: 'Sin datos',
        message: 'No hay información disponible en el dashboard',
        actionLabel: 'Recargar',
        onRetry: () => _loadDashboard(useCache: false),
      );
    }

    return Column(
      children: [
        // Filtros
        AdvancedFilterWidget(
          fechaInicio: _fechaInicio,
          fechaFin: _fechaFin,
          estadoEvento: _estadoEvento,
          tipoParticipacion: _tipoParticipacion,
          busquedaEvento: _busquedaEvento,
          onApply: ({
            DateTime? fechaInicio,
            DateTime? fechaFin,
            String? estadoEvento,
            String? tipoParticipacion,
            String? busquedaEvento,
          }) {
            setState(() {
              _fechaInicio = fechaInicio;
              _fechaFin = fechaFin;
              _estadoEvento = estadoEvento;
              _tipoParticipacion = tipoParticipacion;
              _busquedaEvento = busquedaEvento;
            });
            _loadDashboard(useCache: false);
          },
          onClear: () {
            setState(() {
              _fechaInicio = null;
              _fechaFin = null;
              _estadoEvento = null;
              _tipoParticipacion = null;
              _busquedaEvento = null;
            });
            _loadDashboard(useCache: false);
          },
        ),
        // Contenido
        Expanded(child: _buildDashboardContent()),
      ],
    );
  }

  Widget _buildDashboardContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Resumen de métricas
          if (_dashboardData!.metricas != null) ...[
            Text(
              'Resumen General',
              style: AppTypography.headlineMedium,
            ),
            const SizedBox(height: AppSpacing.md),
            _buildMetricasGrid(),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Distribución de estados
          if (_dashboardData!.distribucionEstados != null &&
              _dashboardData!.distribucionEstados!.isNotEmpty) ...[
            Text(
              'Distribución de Eventos',
              style: AppTypography.headlineMedium,
            ),
            const SizedBox(height: AppSpacing.md),
            _buildDistribucionEstados(),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Top Eventos
          if (_dashboardData!.topEventos != null &&
              _dashboardData!.topEventos!.isNotEmpty) ...[
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Top Eventos',
                  style: AppTypography.headlineMedium,
                ),
                AppButton.text(
                  label: 'Ver todos',
                  onPressed: () {
                    // TODO: Navegar a eventos
                  },
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.md),
            _buildTopEventos(),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Top Voluntarios
          if (_dashboardData!.topVoluntarios != null &&
              _dashboardData!.topVoluntarios!.isNotEmpty) ...[
            Text(
              'Top Voluntarios',
              style: AppTypography.headlineMedium,
            ),
            const SizedBox(height: AppSpacing.md),
            _buildTopVoluntarios(),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Alertas
          if (_dashboardData!.alertas != null &&
              _dashboardData!.alertas!.isNotEmpty) ...[
            Text(
              'Alertas e Insights',
              style: AppTypography.headlineMedium,
            ),
            const SizedBox(height: AppSpacing.md),
            _buildAlertas(),
          ],
        ],
      ),
    );
  }

  Widget _buildMetricasGrid() {
    final metricas = _dashboardData!.metricas!;

    return LayoutBuilder(
      builder: (context, constraints) {
        final crossAxisCount = constraints.maxWidth > 600 ? 4 : 2;

        return GridView.count(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisCount: crossAxisCount,
          crossAxisSpacing: AppSpacing.md,
          mainAxisSpacing: AppSpacing.md,
          childAspectRatio: 1.1,
          children: [
            _buildMetricCard(
              'Eventos Activos',
              metricas.eventosActivos.toString(),
              Icons.event_available,
              AppColors.success,
            ),
            _buildMetricCard(
              'Participantes',
              metricas.totalParticipantes.toString(),
              Icons.people,
              AppColors.info,
            ),
            _buildMetricCard(
              'Reacciones',
              metricas.totalReacciones.toString(),
              Icons.favorite,
              AppColors.error,
            ),
            _buildMetricCard(
              'Voluntarios',
              metricas.totalVoluntarios.toString(),
              Icons.volunteer_activism,
              AppColors.accent,
            ),
          ],
        );
      },
    );
  }

  Widget _buildMetricCard(String label, String value, IconData icon, Color color) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: AppDuration.normal,
      curve: AppCurves.emphasizedDecelerate,
      builder: (context, animValue, child) {
        return Opacity(
          opacity: animValue,
          child: Transform.scale(
            scale: 0.95 + (0.05 * animValue),
            child: child,
          ),
        );
      },
      child: AppCard(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(AppSpacing.sm),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(AppRadius.md),
              ),
              child: Icon(icon, size: AppSizes.iconLg, color: color),
            ),
            const SizedBox(height: AppSpacing.sm),
            Text(
              value,
              style: AppTypography.headlineMedium.copyWith(
                color: color,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: AppSpacing.xxs),
            Text(
              label,
              style: AppTypography.labelMedium,
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDistribucionEstados() {
    final distribucion = _dashboardData!.distribucionEstados!;

    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: distribucion.entries.map((entry) {
          final estado = entry.key;
          final count = entry.value;
          final color = _getEstadoColor(estado);

          return Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.sm),
            child: Row(
              children: [
                Container(
                  width: 12,
                  height: 12,
                  decoration: BoxDecoration(
                    color: color,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: Text(
                    estado[0].toUpperCase() + estado.substring(1),
                    style: AppTypography.bodyMedium,
                  ),
                ),
                AppBadge.neutral(
                  label: count.toString(),
                ),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildTopEventos() {
    return Column(
      children: _dashboardData!.topEventos!.take(5).map((evento) {
        final index = _dashboardData!.topEventos!.indexOf(evento);

        return TweenAnimationBuilder<double>(
          tween: Tween(begin: 0.0, end: 1.0),
          duration: Duration(milliseconds: 200 + (index * 50)),
          curve: AppCurves.emphasizedDecelerate,
          builder: (context, animValue, child) {
            return Opacity(
              opacity: animValue,
              child: Transform.translate(
                offset: Offset(0, 20 * (1 - animValue)),
                child: child,
              ),
            );
          },
          child: Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.sm),
            child: AppCard(
              onTap: () {
                // TODO: Navegar a detalle
              },
              child: Row(
                children: [
                  // Ranking badge
                  AppAvatar.md(
                    initials: '${index + 1}',
                    backgroundColor: _getRankColor(index),
                    foregroundColor: AppColors.white,
                  ),
                  const SizedBox(width: AppSpacing.md),
                  // Información del evento
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          evento.titulo,
                          style: AppTypography.titleSmall,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: AppSpacing.xxs),
                        Row(
                          children: [
                            Icon(Icons.star, size: 14, color: AppColors.warning),
                            const SizedBox(width: AppSpacing.xxs),
                            Text(
                              'Engagement: ${evento.engagement}',
                              style: AppTypography.bodySecondary,
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  // Métricas
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.favorite, size: 14, color: AppColors.error),
                          const SizedBox(width: AppSpacing.xxs),
                          Text(
                            evento.reacciones.toString(),
                            style: AppTypography.labelMedium,
                          ),
                        ],
                      ),
                      const SizedBox(height: AppSpacing.xxs),
                      Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.people, size: 14, color: AppColors.info),
                          const SizedBox(width: AppSpacing.xxs),
                          Text(
                            evento.inscripciones.toString(),
                            style: AppTypography.labelMedium,
                          ),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildTopVoluntarios() {
    return Column(
      children: _dashboardData!.topVoluntarios!.take(5).map((voluntario) {
        final index = _dashboardData!.topVoluntarios!.indexOf(voluntario);

        return TweenAnimationBuilder<double>(
          tween: Tween(begin: 0.0, end: 1.0),
          duration: Duration(milliseconds: 200 + (index * 50)),
          curve: AppCurves.emphasizedDecelerate,
          builder: (context, animValue, child) {
            return Opacity(
              opacity: animValue,
              child: Transform.translate(
                offset: Offset(0, 20 * (1 - animValue)),
                child: child,
              ),
            );
          },
          child: Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.sm),
            child: AppListTile(
              leading: AppAvatar.md(
                initials: _getInitials(voluntario.nombre),
                backgroundColor: _getRankColor(index),
              ),
              title: voluntario.nombre,
              subtitle: '${voluntario.eventosParticipados} eventos participados',
              trailing: AppBadge.primary(
                label: '#${index + 1}',
                icon: Icons.star,
              ),
              showDivider: index < _dashboardData!.topVoluntarios!.length - 1,
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildAlertas() {
    return Column(
      children: _dashboardData!.alertas!.map((alerta) {
        return Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.sm),
          child: AppCard(
            onTap: alerta.eventoId != null
                ? () {
                    // TODO: Navegar a evento
                  }
                : null,
            backgroundColor: alerta.color.withOpacity(0.05),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(AppSpacing.sm),
                  decoration: BoxDecoration(
                    color: alerta.color.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(AppRadius.sm),
                  ),
                  child: Icon(
                    alerta.icon,
                    color: alerta.color,
                    size: AppSizes.iconMd,
                  ),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _getSeverityLabel(alerta.severidad),
                        style: AppTypography.labelSmall.copyWith(
                          color: alerta.color,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: AppSpacing.xxs),
                      Text(
                        alerta.mensaje,
                        style: AppTypography.bodyMedium,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                if (alerta.eventoId != null)
                  Icon(
                    Icons.chevron_right,
                    color: alerta.color,
                    size: AppSizes.iconMd,
                  ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Color _getRankColor(int index) {
    switch (index) {
      case 0:
        return AppColors.warning;
      case 1:
        return AppColors.grey400;
      case 2:
        return const Color(0xFF8D6E63);
      default:
        return AppColors.info;
    }
  }

  Color _getEstadoColor(String? estado) {
    switch (estado?.toLowerCase()) {
      case 'activo':
      case 'publicado':
        return AppColors.success;
      case 'inactivo':
      case 'borrador':
        return AppColors.warning;
      case 'finalizado':
        return AppColors.grey500;
      default:
        return AppColors.info;
    }
  }

  String _getSeverityLabel(String severidad) {
    switch (severidad) {
      case 'danger':
        return 'URGENTE';
      case 'warning':
        return 'ADVERTENCIA';
      case 'info':
        return 'INFORMACIÓN';
      default:
        return 'ALERTA';
    }
  }

  String _getInitials(String name) {
    final parts = name.split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.substring(0, name.length >= 2 ? 2 : 1).toUpperCase();
  }
}
