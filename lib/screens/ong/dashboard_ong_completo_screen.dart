import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/filters/advanced_filter_widget.dart';
import '../../widgets/charts/line_chart_widget.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/area_chart_widget.dart';
import '../../widgets/charts/grouped_bar_chart_widget.dart';
import '../../models/dashboard/ong_dashboard_data.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
import '../evento_detail_screen.dart';

/// Dashboard ONG Profesional con visualizaciones y análisis completo
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
      backgroundColor: AppColors.backgroundSecondary,
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: Text('Dashboard', style: AppTypography.titleLarge),
        elevation: 0,
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
        Container(
          color: AppColors.white,
          child: AdvancedFilterWidget(
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
          // Header ejecutivo
          _buildExecutiveHeader(),
          const SizedBox(height: AppSpacing.lg),

          // KPIs principales - SIEMPRE mostrar
          _buildKPIsSection(),
          const SizedBox(height: AppSpacing.lg),

          // Gráficos y análisis
          _buildAnalyticsSection(),
          const SizedBox(height: AppSpacing.lg),

          // Rankings y top performers
          _buildRankingsSection(),
          const SizedBox(height: AppSpacing.lg),

          // Alertas e insights
          if (_dashboardData!.alertas != null &&
              _dashboardData!.alertas!.isNotEmpty)
            _buildInsightsSection(),
        ],
      ),
    );
  }

  Widget _buildExecutiveHeader() {
    final metricas = _dashboardData!.metricas;
    if (metricas == null) return const SizedBox.shrink();

    final totalEventos = metricas.eventosActivos +
        metricas.eventosInactivos +
        metricas.eventosFinalizados;

    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(AppSpacing.sm),
                decoration: BoxDecoration(
                  color: AppColors.accent.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(AppRadius.md),
                ),
                child: Icon(
                  Icons.dashboard,
                  color: AppColors.accent,
                  size: AppSizes.iconLg,
                ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Resumen Ejecutivo',
                      style: AppTypography.headlineMedium,
                    ),
                    Text(
                      'Actualizado ${_formatDateTime(DateTime.now())}',
                      style: AppTypography.bodySecondary,
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.lg),
          // Indicador de rendimiento general
          _buildPerformanceIndicator(metricas),
          const SizedBox(height: AppSpacing.md),
          // Stats principales
          Row(
            children: [
              Expanded(
                child: _buildQuickStat(
                  'Total Eventos',
                  totalEventos.toString(),
                  Icons.event,
                  AppColors.primary,
                ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: _buildQuickStat(
                  'Participantes',
                  _formatNumber(metricas.totalParticipantes),
                  Icons.people,
                  AppColors.info,
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Row(
            children: [
              Expanded(
                child: _buildQuickStat(
                  'Engagement',
                  _formatNumber(metricas.totalReacciones),
                  Icons.favorite,
                  AppColors.error,
                ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: _buildQuickStat(
                  'Compartidos',
                  _formatNumber(metricas.totalCompartidos),
                  Icons.share,
                  AppColors.success,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickStat(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.sm),
      decoration: BoxDecoration(
        color: color.withOpacity(0.05),
        borderRadius: BorderRadius.circular(AppRadius.sm),
        border: Border.all(
          color: color.withOpacity(0.2),
          width: 1,
        ),
      ),
      child: Row(
        children: [
          Icon(icon, color: color, size: AppSizes.iconMd),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  value,
                  style: AppTypography.titleMedium.copyWith(
                    color: color,
                    fontWeight: FontWeight.bold,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  label,
                  style: AppTypography.labelSmall.copyWith(
                    color: AppColors.textSecondary,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPerformanceIndicator(MetricasOng metricas) {
    // Calcular puntuación de rendimiento basada en métricas clave
    final totalEventos = metricas.eventosActivos +
        metricas.eventosInactivos +
        metricas.eventosFinalizados;

    // Calcular porcentaje de eventos activos (meta: >30%)
    final tasaEventosActivos = totalEventos > 0
        ? (metricas.eventosActivos / totalEventos) * 100
        : 0.0;

    // Calcular engagement promedio (meta: >20 participantes/evento)
    final engagementPromedio = metricas.eventosActivos > 0
        ? metricas.totalParticipantes / metricas.eventosActivos
        : 0.0;

    // Calcular puntuación general (0-100)
    double score = 0;
    if (totalEventos > 0) score += 25; // Tiene eventos
    if (metricas.eventosActivos > 0) score += 25; // Tiene eventos activos
    if (tasaEventosActivos > 30) score += 25; // Buena tasa de eventos activos
    if (engagementPromedio > 20) score += 25; // Buen engagement

    final Color statusColor;
    final String statusText;
    final IconData statusIcon;

    if (score >= 75) {
      statusColor = AppColors.success;
      statusText = 'Excelente';
      statusIcon = Icons.trending_up;
    } else if (score >= 50) {
      statusColor = AppColors.info;
      statusText = 'Bueno';
      statusIcon = Icons.check_circle_outline;
    } else if (score >= 25) {
      statusColor = AppColors.warning;
      statusText = 'Regular';
      statusIcon = Icons.warning_amber_rounded;
    } else {
      statusColor = AppColors.error;
      statusText = 'Bajo';
      statusIcon = Icons.arrow_downward_rounded;
    }

    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            statusColor.withOpacity(0.1),
            statusColor.withOpacity(0.05),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(
          color: statusColor.withOpacity(0.3),
          width: 1.5,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(AppSpacing.xs),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(AppRadius.sm),
                ),
                child: Icon(statusIcon, color: statusColor, size: 20),
              ),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Rendimiento General',
                      style: AppTypography.labelMedium.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                    Text(
                      statusText,
                      style: AppTypography.titleMedium.copyWith(
                        color: statusColor,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
              Text(
                '${score.toInt()}%',
                style: AppTypography.headlineSmall.copyWith(
                  color: statusColor,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          ClipRRect(
            borderRadius: BorderRadius.circular(AppRadius.full),
            child: LinearProgressIndicator(
              value: score / 100,
              minHeight: 8,
              backgroundColor: AppColors.grey200,
              valueColor: AlwaysStoppedAnimation<Color>(statusColor),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKPIsSection() {
    final metricas = _dashboardData!.metricas!;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              'Indicadores Clave',
              style: AppTypography.headlineSmall,
            ),
            const SizedBox(width: AppSpacing.sm),
            AppBadge.info(
              label: 'KPIs',
              icon: Icons.analytics,
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        LayoutBuilder(
          builder: (context, constraints) {
            // Responsive breakpoints mejorados
            final int crossAxisCount;
            final double childAspectRatio;

            if (constraints.maxWidth > 900) {
              // Desktop: 2 columnas (2x2)
              crossAxisCount = 2;
              childAspectRatio = 1.3;
            } else if (constraints.maxWidth > 600) {
              // Tablet: 2 columnas (2x2)
              crossAxisCount = 2;
              childAspectRatio = 1.2;
            } else {
              // Mobile: 2 columnas (2x2) - más compacto
              crossAxisCount = 2;
              childAspectRatio = 0.9;
            }

            return GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: crossAxisCount,
              crossAxisSpacing: AppSpacing.lg,
              mainAxisSpacing: AppSpacing.lg,
              childAspectRatio: childAspectRatio,
              children: [
                _buildEnhancedMetricCard(
                  'Activos',
                  metricas.eventosActivos.toString(),
                  Icons.event_available,
                  AppColors.success,
                  _getComparativa('eventosActivos'),
                ),
                _buildEnhancedMetricCard(
                  'Participantes',
                  metricas.totalParticipantes.toString(),
                  Icons.people,
                  AppColors.info,
                  _getComparativa('participantes'),
                ),
                _buildEnhancedMetricCard(
                  'Reacciones',
                  metricas.totalReacciones.toString(),
                  Icons.favorite,
                  AppColors.error,
                  _getComparativa('reacciones'),
                ),
                _buildEnhancedMetricCard(
                  'Voluntarios',
                  metricas.totalVoluntarios.toString(),
                  Icons.volunteer_activism,
                  AppColors.accent,
                  _getComparativa('voluntarios'),
                ),
              ],
            );
          },
        ),
      ],
    );
  }

  Widget _buildEnhancedMetricCard(
    String label,
    String value,
    IconData icon,
    Color color,
    Comparativa? comparativa,
  ) {
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
      child: MouseRegion(
        cursor: SystemMouseCursors.click,
        child: AnimatedContainer(
          duration: AppDuration.fast,
          curve: AppCurves.decelerate,
          child: LayoutBuilder(
            builder: (context, constraints) {
              // Calcular tamaños dinámicos basados en el espacio disponible
              final cardWidth = constraints.maxWidth;
              final cardHeight = constraints.maxHeight;
              
              // Tamaños responsivos
              final iconSize = (cardWidth * 0.15).clamp(24.0, 40.0);
              final iconPadding = (cardWidth * 0.03).clamp(8.0, 12.0);
              final contentPadding = (cardWidth * 0.05).clamp(12.0, 20.0);
              final valueFontSize = (cardWidth * 0.15).clamp(24.0, 48.0);
              final labelFontSize = (cardWidth * 0.045).clamp(12.0, 16.0);
              
              return Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      color.withOpacity(0.08),
                      color.withOpacity(0.03),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(AppRadius.lg),
                  border: Border.all(
                    color: color.withOpacity(0.2),
                    width: 1.5,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: color.withOpacity(0.1),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () {
                      // Futura funcionalidad: mostrar detalles del KPI
                    },
                    borderRadius: BorderRadius.circular(AppRadius.lg),
                    child: Padding(
                      padding: EdgeInsets.all(contentPadding),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          // Icon
                          Container(
                            padding: EdgeInsets.all(iconPadding),
                            decoration: BoxDecoration(
                              color: color.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(AppRadius.md),
                              boxShadow: [
                                BoxShadow(
                                  color: color.withOpacity(0.2),
                                  blurRadius: 8,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: Icon(
                              icon,
                              size: iconSize,
                              color: color,
                            ),
                          ),
                          
                          // Spacer flexible
                          SizedBox(height: cardHeight * 0.05),
                          
                          // Value - con FittedBox para evitar overflow
                          Flexible(
                            child: FittedBox(
                              fit: BoxFit.scaleDown,
                              child: Text(
                                value,
                                style: TextStyle(
                                  fontSize: valueFontSize,
                                  color: color,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: -1,
                                  height: 1.1,
                                ),
                                maxLines: 1,
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ),
                          
                          SizedBox(height: cardHeight * 0.02),
                          
                          // Label - con FittedBox para evitar overflow
                          Flexible(
                            child: FittedBox(
                              fit: BoxFit.scaleDown,
                              child: Text(
                                label,
                                style: TextStyle(
                                  fontSize: labelFontSize,
                                  color: AppColors.textSecondary,
                                  fontWeight: FontWeight.w600,
                                  letterSpacing: 0.3,
                                ),
                                maxLines: 1,
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ),
                          
                          // Trend indicator at bottom if exists
                          if (comparativa != null) ...[
                            SizedBox(height: cardHeight * 0.03),
                            _buildTrendIndicator(comparativa),
                          ],
                        ],
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildTrendIndicator(Comparativa comparativa) {
    final isPositive = comparativa.crecimiento > 0;
    final isNegative = comparativa.crecimiento < 0;
    final color = isPositive
        ? AppColors.success
        : isNegative
            ? AppColors.error
            : AppColors.grey500;

    final icon = isPositive
        ? Icons.arrow_upward_rounded
        : isNegative
            ? Icons.arrow_downward_rounded
            : Icons.remove_rounded;

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xxs,
      ),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(AppRadius.full),
        border: Border.all(
          color: color.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            icon,
            size: 12,
            color: color,
          ),
          const SizedBox(width: AppSpacing.xxs),
          Text(
            '${isPositive ? '+' : ''}${comparativa.crecimiento.toStringAsFixed(1)}%',
            style: AppTypography.labelSmall.copyWith(
              color: color,
              fontWeight: FontWeight.bold,
              letterSpacing: 0.2,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _buildAnalyticsSection() {
    final hasDistribucion = _dashboardData!.distribucionEstados != null &&
        _dashboardData!.distribucionEstados!.isNotEmpty;
    final hasTendencias = _dashboardData!.tendenciasMensuales != null &&
        _dashboardData!.tendenciasMensuales!.isNotEmpty;
    final hasActividad = _dashboardData!.actividadSemanal != null &&
        _dashboardData!.actividadSemanal!.isNotEmpty;
    final hasComparativa = _dashboardData!.comparativaEventos != null &&
        _dashboardData!.comparativaEventos!.isNotEmpty;

    final hasAnyData = hasDistribucion || hasTendencias || hasActividad || hasComparativa;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              'Análisis y Tendencias',
              style: AppTypography.headlineSmall,
            ),
            const SizedBox(width: AppSpacing.sm),
            AppBadge.primary(
              label: 'Gráficos',
              icon: Icons.bar_chart,
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),

        // Estado vacío profesional
        if (!hasAnyData)
          _buildEmptyAnalytics()
        else ...[
          // Distribución de estados
          if (hasDistribucion) ...[
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Distribución de Eventos',
                    style: AppTypography.titleMedium.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.md),
                  SizedBox(
                    height: 200,
                    child: PieChartWidget(
                      title: '',
                      subtitle: '',
                      data: _dashboardData!.distribucionEstados!,
                      colors: [
                        AppColors.success,
                        AppColors.warning,
                        AppColors.grey500,
                        AppColors.error,
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: AppSpacing.md),
          ],

          // Tendencias mensuales
          if (hasTendencias) ...[
            AppCard(
              child: SizedBox(
                height: 250,
                child: LineChartWidget(
                  title: 'Tendencia de Participación',
                  subtitle: 'Participantes por mes',
                  data: _dashboardData!.tendenciasMensuales!,
                  lineColor: AppColors.accent,
                ),
              ),
            ),
            const SizedBox(height: AppSpacing.md),
          ],

          // Actividad semanal
          if (hasActividad) ...[
            AppCard(
              child: SizedBox(
                height: 250,
                child: AreaChartWidget(
                  title: 'Actividad Semanal',
                  subtitle: 'Interacciones por semana',
                  data: _dashboardData!.actividadSemanal!,
                  areaColor: AppColors.info,
                  borderColor: AppColors.info,
                ),
              ),
            ),
            const SizedBox(height: AppSpacing.md),
          ],

          // Comparativa de eventos
          if (hasComparativa) ...[
            AppCard(
              child: SizedBox(
                height: 280,
                child: GroupedBarChartWidget(
                  title: 'Top Eventos por Métricas',
                  subtitle: 'Comparativa de rendimiento',
                  data: _dashboardData!.comparativaEventos!.take(8).map((e) {
                    return GroupedBarData(
                      label: e.titulo.length > 15
                          ? '${e.titulo.substring(0, 12)}...'
                          : e.titulo,
                      values: {
                        'Reacciones': e.reacciones.toDouble(),
                        'Compartidos': e.compartidos.toDouble(),
                        'Participantes': e.participantes.toDouble(),
                      },
                    );
                  }).toList(),
                  seriesNames: ['Reacciones', 'Compartidos', 'Participantes'],
                  colors: [AppColors.error, AppColors.success, AppColors.info],
                ),
              ),
            ),
          ],
        ],
      ],
    );
  }

  Widget _buildEmptyAnalytics() {
    return AppCard(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.xl),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: BoxDecoration(
                color: AppColors.grey100,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.show_chart,
                size: 48,
                color: AppColors.grey400,
              ),
            ),
            const SizedBox(height: AppSpacing.md),
            Text(
              'Sin datos de análisis',
              style: AppTypography.titleMedium.copyWith(
                color: AppColors.textPrimary,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(
              'Los gráficos y tendencias aparecerán aquí cuando haya\nevento con actividad registrada',
              style: AppTypography.bodySmall.copyWith(
                color: AppColors.textSecondary,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRankingsSection() {
    final hasTopEventos = _dashboardData!.topEventos != null &&
        _dashboardData!.topEventos!.isNotEmpty;
    final hasTopVoluntarios = _dashboardData!.topVoluntarios != null &&
        _dashboardData!.topVoluntarios!.isNotEmpty;

    final hasAnyRanking = hasTopEventos || hasTopVoluntarios;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              'Rankings y Destacados',
              style: AppTypography.headlineSmall,
            ),
            const SizedBox(width: AppSpacing.sm),
            AppBadge.warning(
              label: 'Top',
              icon: Icons.star,
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),

        // Estado vacío profesional
        if (!hasAnyRanking)
          _buildEmptyRankings()
        else
          // Layout responsive para rankings
          LayoutBuilder(
            builder: (context, constraints) {
              if (constraints.maxWidth > 800) {
                // Desktop: 2 columnas
                return Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: hasTopEventos
                          ? _buildTopEventosCard()
                          : _buildEmptyTopEventos(),
                    ),
                    const SizedBox(width: AppSpacing.md),
                    Expanded(
                      child: hasTopVoluntarios
                          ? _buildTopVoluntariosCard()
                          : _buildEmptyTopVoluntarios(),
                    ),
                  ],
                );
              } else {
                // Mobile: 1 columna
                return Column(
                  children: [
                    hasTopEventos
                        ? _buildTopEventosCard()
                        : _buildEmptyTopEventos(),
                    const SizedBox(height: AppSpacing.md),
                    hasTopVoluntarios
                        ? _buildTopVoluntariosCard()
                        : _buildEmptyTopVoluntarios(),
                  ],
                );
              }
            },
          ),
      ],
    );
  }

  Widget _buildEmptyRankings() {
    return AppCard(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.xl),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: BoxDecoration(
                color: AppColors.warning.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.emoji_events,
                size: 48,
                color: AppColors.warning,
              ),
            ),
            const SizedBox(height: AppSpacing.md),
            Text(
              'Sin rankings disponibles',
              style: AppTypography.titleMedium.copyWith(
                color: AppColors.textPrimary,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(
              'Los rankings de eventos y voluntarios destacados\naparecerán cuando haya actividad suficiente',
              style: AppTypography.bodySmall.copyWith(
                color: AppColors.textSecondary,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyTopEventos() {
    return AppCard(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          children: [
            Icon(Icons.event_note, size: 40, color: AppColors.grey400),
            const SizedBox(height: AppSpacing.sm),
            Text(
              'Sin eventos destacados',
              style: AppTypography.titleSmall.copyWith(
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: AppSpacing.xxs),
            Text(
              'Crea eventos para ver el ranking',
              style: AppTypography.bodySmall.copyWith(
                color: AppColors.textTertiary,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyTopVoluntarios() {
    return AppCard(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          children: [
            Icon(Icons.people_outline, size: 40, color: AppColors.grey400),
            const SizedBox(height: AppSpacing.sm),
            Text(
              'Sin voluntarios destacados',
              style: AppTypography.titleSmall.copyWith(
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: AppSpacing.xxs),
            Text(
              'Los voluntarios más activos aparecerán aquí',
              style: AppTypography.bodySmall.copyWith(
                color: AppColors.textTertiary,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTopEventosCard() {
    if (_dashboardData!.topEventos == null ||
        _dashboardData!.topEventos!.isEmpty) {
      return const SizedBox.shrink();
    }

    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.emoji_events, color: AppColors.warning, size: 20),
              const SizedBox(width: AppSpacing.xs),
              Text(
                'Top Eventos',
                style: AppTypography.titleMedium.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          ..._dashboardData!.topEventos!.take(5).map((evento) {
            final index = _dashboardData!.topEventos!.indexOf(evento);
            return _buildTopEventoItem(evento, index);
          }).toList(),
        ],
      ),
    );
  }

  Widget _buildTopEventoItem(TopEvento evento, int index) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: Duration(milliseconds: 200 + (index * 50)),
      curve: AppCurves.emphasizedDecelerate,
      builder: (context, animValue, child) {
        return Opacity(
          opacity: animValue,
          child: Transform.translate(
            offset: Offset(20 * (1 - animValue), 0),
            child: child,
          ),
        );
      },
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => EventoDetailScreen(eventoId: evento.eventoId),
            ),
          );
        },
        borderRadius: BorderRadius.circular(AppRadius.sm),
        child: Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.sm),
          child: Row(
            children: [
              // Ranking badge
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: _getRankColor(index),
                  borderRadius: BorderRadius.circular(AppRadius.sm),
                ),
                child: Center(
                  child: Text(
                    '${index + 1}',
                    style: AppTypography.labelMedium.copyWith(
                      color: AppColors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              // Evento info
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
                        Icon(Icons.star, size: 12, color: AppColors.warning),
                        const SizedBox(width: AppSpacing.xxs),
                        Text(
                          '${evento.engagement}',
                          style: AppTypography.labelSmall,
                        ),
                        const SizedBox(width: AppSpacing.sm),
                        Icon(Icons.people, size: 12, color: AppColors.info),
                        const SizedBox(width: AppSpacing.xxs),
                        Text(
                          '${evento.inscripciones}',
                          style: AppTypography.labelSmall,
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              // Chevron
              Icon(
                Icons.chevron_right,
                color: AppColors.textTertiary,
                size: 20,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTopVoluntariosCard() {
    if (_dashboardData!.topVoluntarios == null ||
        _dashboardData!.topVoluntarios!.isEmpty) {
      return const SizedBox.shrink();
    }

    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.volunteer_activism, color: AppColors.accent, size: 20),
              const SizedBox(width: AppSpacing.xs),
              Text(
                'Top Voluntarios',
                style: AppTypography.titleMedium.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          ..._dashboardData!.topVoluntarios!.take(5).map((voluntario) {
            final index = _dashboardData!.topVoluntarios!.indexOf(voluntario);
            return _buildTopVoluntarioItem(voluntario, index);
          }).toList(),
        ],
      ),
    );
  }

  Widget _buildTopVoluntarioItem(TopVoluntario voluntario, int index) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: Duration(milliseconds: 200 + (index * 50)),
      curve: AppCurves.emphasizedDecelerate,
      builder: (context, animValue, child) {
        return Opacity(
          opacity: animValue,
          child: Transform.translate(
            offset: Offset(20 * (1 - animValue), 0),
            child: child,
          ),
        );
      },
      child: Padding(
        padding: const EdgeInsets.only(bottom: AppSpacing.sm),
        child: Row(
          children: [
            // Avatar con ranking
            Stack(
              children: [
                AppAvatar.md(
                  initials: _getInitials(voluntario.nombre),
                  backgroundColor: _getRankColor(index).withOpacity(0.2),
                  foregroundColor: _getRankColor(index),
                ),
                Positioned(
                  right: 0,
                  bottom: 0,
                  child: Container(
                    width: 16,
                    height: 16,
                    decoration: BoxDecoration(
                      color: _getRankColor(index),
                      shape: BoxShape.circle,
                      border: Border.all(color: AppColors.white, width: 2),
                    ),
                    child: Center(
                      child: Text(
                        '${index + 1}',
                        style: const TextStyle(
                          color: AppColors.white,
                          fontSize: 8,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(width: AppSpacing.sm),
            // Voluntario info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    voluntario.nombre,
                    style: AppTypography.titleSmall,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: AppSpacing.xxs),
                  Text(
                    '${voluntario.eventosParticipados} eventos',
                    style: AppTypography.labelSmall,
                  ),
                ],
              ),
            ),
            // Badge
            AppBadge.primary(
              label: '⭐',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInsightsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              'Alertas e Insights',
              style: AppTypography.headlineSmall,
            ),
            const SizedBox(width: AppSpacing.sm),
            AppBadge.error(
              label: '${_dashboardData!.alertas!.length}',
              icon: Icons.notifications_active,
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        ..._dashboardData!.alertas!.map((alerta) {
          return Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.sm),
            child: AppCard(
              onTap: alerta.eventoId != null
                  ? () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) =>
                              EventoDetailScreen(eventoId: alerta.eventoId!),
                        ),
                      );
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
      ],
    );
  }

  // Helper methods

  Comparativa? _getComparativa(String metric) {
    if (_dashboardData!.comparativas == null) return null;
    return _dashboardData!.comparativas![metric];
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

  String _formatDateTime(DateTime date) {
    final now = DateTime.now();
    final difference = now.difference(date);

    if (difference.inMinutes < 1) {
      return 'hace un momento';
    } else if (difference.inMinutes < 60) {
      return 'hace ${difference.inMinutes} min';
    } else if (difference.inHours < 24) {
      return 'hace ${difference.inHours} h';
    } else {
      return DateFormat('dd/MM/yyyy HH:mm').format(date);
    }
  }

  String _formatNumber(int number) {
    if (number >= 1000000) {
      return '${(number / 1000000).toStringAsFixed(1)}M';
    } else if (number >= 1000) {
      return '${(number / 1000).toStringAsFixed(1)}K';
    }
    return number.toString();
  }
}
