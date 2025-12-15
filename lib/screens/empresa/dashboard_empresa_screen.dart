import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/bar_chart_widget.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';

/// Dashboard Empresa Consolidado
/// Único dashboard para empresas con todas las métricas y reportes
class DashboardEmpresaScreen extends StatefulWidget {
  const DashboardEmpresaScreen({super.key});

  @override
  State<DashboardEmpresaScreen> createState() => _DashboardEmpresaScreenState();
}

class _DashboardEmpresaScreenState extends State<DashboardEmpresaScreen> {
  Map<String, dynamic>? _datos;
  bool _isLoading = true;
  String? _error;

  // Control de secciones colapsables
  final Map<String, bool> _expandedSections = {
    'resumen': true,
    'impacto': false,
    'eventos': false,
  };

  @override
  void initState() {
    super.initState();
    _loadDatos();
  }

  Future<void> _loadDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventosPatrocinados();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _datos = result;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar datos';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Dashboard empresa'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadDatos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body:
          _isLoading
              ? LoadingState.detail()
              : _error != null
              ? ErrorView.serverError(
                onRetry: _loadDatos,
                errorDetails: _error,
              )
              : _datos == null
              ? const EmptyState(
                icon: Icons.dashboard_outlined,
                title: 'Sin datos',
                message: 'No hay información disponible para mostrar.',
              )
              : SingleChildScrollView(
                padding: const EdgeInsets.all(AppSpacing.md),
                child: Column(
                  children: [
                    _buildCollapsibleSection(
                      key: 'resumen',
                      title: 'Resumen general',
                      subtitle: 'KPIs y métricas principales',
                      icon: Icons.dashboard,
                      child: _buildResumenSection(),
                    ),
                    _buildCollapsibleSection(
                      key: 'impacto',
                      title: 'Impacto',
                      subtitle: 'Alcance y distribución por categorías',
                      icon: Icons.trending_up,
                      child: _buildImpactoSection(),
                    ),
                    _buildCollapsibleSection(
                      key: 'eventos',
                      title: 'Eventos patrocinados',
                      subtitle: 'Listado completo de eventos',
                      icon: Icons.event,
                      child: _buildEventosSection(),
                    ),
                    const SizedBox(height: AppSpacing.lg),
                  ],
                ),
              ),
    );
  }

  Widget _buildCollapsibleSection({
    required String key,
    required String title,
    required String subtitle,
    required IconData icon,
    required Widget child,
  }) {
    final isExpanded = _expandedSections[key] ?? false;

    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.md),
      child: AppCard(
        elevated: true,
        padding: EdgeInsets.zero,
        child: ExpansionTile(
          key: ValueKey(key),
          initiallyExpanded: isExpanded,
          onExpansionChanged: (expanded) {
            setState(() {
              _expandedSections[key] = expanded;
            });
          },
          tilePadding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.md,
            vertical: AppSpacing.sm,
          ),
          childrenPadding: const EdgeInsets.fromLTRB(
            AppSpacing.md,
            0,
            AppSpacing.md,
            AppSpacing.md,
          ),
          leading: Container(
            padding: const EdgeInsets.all(AppSpacing.sm),
            decoration: BoxDecoration(
              color: AppColors.grey100,
              borderRadius: BorderRadius.circular(AppRadius.sm),
            ),
            child: AppIcon.md(icon, color: AppColors.primary),
          ),
          title: Text(title, style: AppTypography.titleMedium),
          subtitle: Text(subtitle, style: AppTypography.bodySecondary),
          children: [child],
        ),
      ),
    );
  }

  Widget _buildResumenSection() {
    final eventosPatrocinados = _datos?['eventos_patrocinados'] as List? ?? [];
    final totalEventos = eventosPatrocinados.length;

    // Calcular métricas agregadas
    int totalParticipantes = 0;
    int totalReacciones = 0;
    int totalCompartidos = 0;

    for (var evento in eventosPatrocinados) {
      totalParticipantes += (evento['total_participantes'] as int? ?? 0);
      totalReacciones += (evento['total_reacciones'] as int? ?? 0);
      totalCompartidos += (evento['total_compartidos'] as int? ?? 0);
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        MetricGrid(
          crossAxisCount: 2,
          metrics: [
            MetricCard(
              label: 'Eventos Patrocinados',
              value: totalEventos.toString(),
              icon: Icons.event,
              color: AppColors.info,
            ),
            MetricCard(
              label: 'Total Participantes',
              value: totalParticipantes.toString(),
              icon: Icons.people,
              color: AppColors.success,
            ),
            MetricCard(
              label: 'Total Reacciones',
              value: totalReacciones.toString(),
              icon: Icons.favorite,
              color: AppColors.error,
            ),
            MetricCard(
              label: 'Total Compartidos',
              value: totalCompartidos.toString(),
              icon: Icons.share,
              color: AppColors.primary,
            ),
            MetricCard(
              label: 'Promedio Participantes',
              value:
                  totalEventos > 0
                      ? (totalParticipantes / totalEventos).toStringAsFixed(1)
                      : '0',
              icon: Icons.analytics,
              color: AppColors.warning,
            ),
            MetricCard(
              label: 'Alcance Total',
              value:
                  (totalParticipantes + totalReacciones + totalCompartidos)
                      .toString(),
              icon: Icons.trending_up,
              color: AppColors.accent,
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildImpactoSection() {
    final eventosPatrocinados = _datos?['eventos_patrocinados'] as List? ?? [];

    if (eventosPatrocinados.isEmpty) {
      return const EmptyState(
        icon: Icons.insights_outlined,
        title: 'Sin impacto para mostrar',
        message: 'Cuando patrocines eventos, verás aquí el resumen de impacto.',
      );
    }

    // Agrupar eventos por categoría
    Map<String, int> eventosPorCategoria = {};
    Map<String, int> participantesPorCategoria = {};

    for (var evento in eventosPatrocinados) {
      final categoria = evento['categoria'] ?? 'Sin categoría';
      eventosPorCategoria[categoria] =
          (eventosPorCategoria[categoria] ?? 0) + 1;
      participantesPorCategoria[categoria] =
          (participantesPorCategoria[categoria] ?? 0) +
          (evento['total_participantes'] as int? ?? 0);
    }

    return Column(
      children: [
        PieChartWidget(
          title: 'Eventos por Categoría',
          subtitle: 'Distribución de patrocinios',
          data: eventosPorCategoria,
        ),
        const SizedBox(height: 16),
        BarChartWidget(
          title: 'Participantes por Categoría',
          subtitle: 'Alcance de tus patrocinios',
          data: participantesPorCategoria,
          barColor: AppColors.success,
        ),
        const SizedBox(height: 16),
        AppCard(
          elevated: true,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  AppIcon.md(Icons.stars, color: AppColors.warningDark),
                  const SizedBox(width: AppSpacing.sm),
                  Text('Impacto social', style: AppTypography.titleLarge),
                ],
              ),
              const SizedBox(height: AppSpacing.md),
              Text(
                'Tu empresa ha contribuido al impacto social patrocinando '
                '${eventosPatrocinados.length} eventos, llegando a '
                '${participantesPorCategoria.values.fold<int>(0, (a, b) => a + b)} '
                'participantes en diferentes categorías.',
                style: AppTypography.bodyMedium,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildEventosSection() {
    final eventosPatrocinados = _datos?['eventos_patrocinados'] as List? ?? [];

    if (eventosPatrocinados.isEmpty) {
      return const EmptyState(
        icon: Icons.event_busy,
        title: 'Aún sin eventos',
        message: 'Cuando patrocines eventos, aparecerán listados aquí.',
      );
    }

    return Column(
      children:
          eventosPatrocinados.map((evento) {
            final estado = evento['estado']?.toString() ?? 'Activo';
            final estadoLower = estado.toLowerCase();

            final Widget estadoBadge;
            if (estadoLower == 'activo' || estadoLower == 'publicado') {
              estadoBadge = AppBadge.success(label: estado);
            } else if (estadoLower == 'finalizado') {
              estadoBadge = AppBadge.info(label: estado);
            } else if (estadoLower == 'cancelado') {
              estadoBadge = AppBadge.error(label: estado);
            } else {
              estadoBadge = AppBadge.neutral(label: estado);
            }

            return Padding(
              padding: const EdgeInsets.only(bottom: AppSpacing.sm),
              child: AppCard(
                elevated: true,
                padding: EdgeInsets.zero,
                child: AppListTile(
                  leading: AppAvatar.sm(
                    icon: Icons.event,
                    backgroundColor: AppColors.primaryLight,
                    foregroundColor: AppColors.textOnPrimary,
                  ),
                  title: evento['titulo']?.toString() ?? 'Sin título',
                  subtitle:
                      '${evento['total_participantes'] ?? 0} participantes • '
                      '${evento['total_reacciones'] ?? 0} reacciones',
                  trailing: estadoBadge,
                ),
              ),
            );
          }).toList(),
    );
  }
}
