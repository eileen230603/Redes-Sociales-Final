import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'dart:io';
import 'package:universal_html/html.dart' as html;
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/line_chart_widget.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/bar_chart_widget.dart';
import '../../widgets/charts/multi_line_chart_widget.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
import '../../services/cache_service.dart';

class DashboardExternoMejoradoScreen extends StatefulWidget {
  const DashboardExternoMejoradoScreen({super.key});

  @override
  State<DashboardExternoMejoradoScreen> createState() =>
      _DashboardExternoMejoradoScreenState();
}

class _DashboardExternoMejoradoScreenState
    extends State<DashboardExternoMejoradoScreen>
    with SingleTickerProviderStateMixin {
  Map<String, dynamic>? _estadisticas;
  Map<String, dynamic>? _graficas;
  Map<String, dynamic>? _usuario;
  bool _isLoading = true;
  String? _error;
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadData({bool useCache = true}) async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    // Verificar cache primero
    if (useCache) {
      final cachedData = await CacheService.getCachedData('dashboard_externo');
      if (cachedData != null) {
        setState(() {
          _isLoading = false;
          if (cachedData['success'] == true) {
            _estadisticas = cachedData['estadisticas'];
            _graficas = cachedData['graficas'];
            _usuario = cachedData['usuario'];
          }
        });
        return;
      }
    }

    final result = await ApiService.getEstadisticasGeneralesExterno();

    // Guardar en cache
    if (result['success'] == true) {
      await CacheService.setCachedData('dashboard_externo', result);
    }

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _estadisticas = result['estadisticas'] as Map<String, dynamic>?;
        _graficas = result['graficas'] as Map<String, dynamic>?;
        _usuario = result['usuario'] as Map<String, dynamic>?;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar estadísticas';
      }
    });
  }

  Future<void> _descargarPdf() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Descargar Reporte PDF'),
            content: const Text(
              '¿Deseas descargar tu reporte completo de actividad en formato PDF?',
            ),
            actions: [
              AppButton.text(
                label: 'Cancelar',
                onPressed: () => Navigator.of(context).pop(false),
              ),
              AppButton.primary(
                label: 'Descargar',
                icon: Icons.picture_as_pdf,
                onPressed: () => Navigator.of(context).pop(true),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder:
          (context) => Center(
            child: SizedBox(
              width: 320,
              child: LoadingState.card(),
            ),
          ),
    );

    try {
      final result = await ApiService.descargarPdfCompletoExterno();

      if (!mounted) return;
      Navigator.of(context).pop();

      if (result['success'] == true) {
        final pdfBytes = result['pdf_bytes'] as List<int>?;

        if (pdfBytes != null) {
          if (kIsWeb) {
            final blob = html.Blob([pdfBytes], 'application/pdf');
            final url = html.Url.createObjectUrlFromBlob(blob);
            final anchor =
                html.AnchorElement(href: url)
                  ..setAttribute("download", "mi_actividad_reporte.pdf")
                  ..click();
            html.Url.revokeObjectUrl(url);
          } else {
            final directory = await getApplicationDocumentsDirectory();
            final file = File(
              '${directory.path}/mi_actividad_reporte_${DateTime.now().millisecondsSinceEpoch}.pdf',
            );
            await file.writeAsBytes(pdfBytes);
            await OpenFile.open(file.path);
          }

          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('PDF descargado exitosamente'),
              backgroundColor: AppColors.success,
            ),
          );
        }
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al descargar PDF',
            ),
            backgroundColor: AppColors.error,
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
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
        title: const Text('Mi dashboard'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: () => _loadData(useCache: false),
            tooltip: 'Actualizar',
          ),
        ],
        bottom:
            !_isLoading && _error == null
                ? TabBar(
                  controller: _tabController,
                  tabs: [
                    Tab(
                      text: 'Resumen',
                      icon: AppIcon.sm(Icons.dashboard),
                    ),
                    Tab(
                      text: 'Mis eventos',
                      icon: AppIcon.sm(Icons.event),
                    ),
                    Tab(
                      text: 'Actividad',
                      icon: AppIcon.sm(Icons.timeline),
                    ),
                  ],
                )
                : null,
      ),
      body:
          _isLoading
              ? SkeletonLoader.dashboard()
              : _error != null
              ? ErrorView.serverError(onRetry: _loadData, errorDetails: _error)
              : TabBarView(
                controller: _tabController,
                children: [
                  _buildResumenTab(),
                  _buildEventosTab(),
                  _buildActividadTab(),
                ],
              ),
    );
  }

  Widget _buildResumenTab() {
    if (_estadisticas == null) {
      return const EmptyState(
        icon: Icons.insights_outlined,
        title: 'Sin estadísticas',
        message: 'No hay información disponible para mostrar.',
      );
    }

    final eventosInscritos =
        _estadisticas!['total_eventos_inscritos'] as int? ?? 0;
    final eventosAsistidos =
        _estadisticas!['total_eventos_asistidos'] as int? ?? 0;
    final reaccionesTotales = _estadisticas!['total_reacciones'] as int? ?? 0;
    final compartidosTotales = _estadisticas!['total_compartidos'] as int? ?? 0;
    final horasAcumuladas = _estadisticas!['horas_acumuladas'] as int? ?? 0;
    final megaEventosInscritos =
        _estadisticas!['total_mega_eventos_inscritos'] as int? ?? 0;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Información del usuario
          if (_usuario != null) _buildUserInfo(),
          const SizedBox(height: 24),

          // Métricas principales
          Text('Mis estadísticas', style: AppTypography.titleLarge),
          const SizedBox(height: AppSpacing.md),

          MetricGrid(
            crossAxisCount: 2,
            metrics: [
              MetricCard(
                label: 'Eventos Inscritos',
                value: eventosInscritos.toString(),
                icon: Icons.event_available,
                color: AppColors.info,
              ),
              MetricCard(
                label: 'Eventos Asistidos',
                value: eventosAsistidos.toString(),
                icon: Icons.check_circle,
                color: AppColors.success,
              ),
              MetricCard(
                label: 'Reacciones',
                value: reaccionesTotales.toString(),
                icon: Icons.favorite,
                color: AppColors.error,
              ),
              MetricCard(
                label: 'Compartidos',
                value: compartidosTotales.toString(),
                icon: Icons.share,
                color: AppColors.primary,
              ),
              MetricCard(
                label: 'Mega Eventos',
                value: megaEventosInscritos.toString(),
                icon: Icons.event_note,
                color: AppColors.primaryLight,
              ),
              MetricCard(
                label: 'Horas Acumuladas',
                value: horasAcumuladas.toString(),
                icon: Icons.access_time,
                color: AppColors.accentDark,
              ),
              MetricCard(
                label: 'Tasa Asistencia',
                value:
                    eventosInscritos > 0
                        ? '${((eventosAsistidos / eventosInscritos) * 100).toStringAsFixed(0)}%'
                        : '0%',
                icon: Icons.analytics,
                color: AppColors.accent,
              ),
            ],
          ),

          const SizedBox(height: 24),

          // Tasa de asistencia
          AppCard(
            elevated: true,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Tasa de asistencia', style: AppTypography.titleLarge),
                const SizedBox(height: AppSpacing.md),
                Center(
                  child: SizedBox(
                    height: 150,
                    width: 150,
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        CircularProgressIndicator(
                          value:
                              eventosInscritos > 0
                                  ? eventosAsistidos / eventosInscritos
                                  : 0,
                          strokeWidth: 12,
                          backgroundColor: AppColors.borderLight,
                          valueColor: const AlwaysStoppedAnimation<Color>(
                            AppColors.success,
                          ),
                        ),
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              eventosInscritos > 0
                                  ? '${((eventosAsistidos / eventosInscritos) * 100).toStringAsFixed(1)}%'
                                  : '0%',
                              style: AppTypography.headlineSmall.copyWith(
                                color: AppColors.success,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            Text('Asistencia', style: AppTypography.bodySecondary),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
                Text(
                  '$eventosAsistidos de $eventosInscritos eventos asistidos',
                  style: AppTypography.bodySecondary,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEventosTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        children: [
          // Tipo de eventos
          if (_graficas != null &&
              _graficas!['tipo_eventos'] != null &&
              (_graficas!['tipo_eventos'] as Map).isNotEmpty)
            PieChartWidget(
              title: 'Mis Eventos por Tipo',
              subtitle: 'Distribución de eventos según su tipo',
              data: Map<String, int>.from(_graficas!['tipo_eventos']),
            ),

          const SizedBox(height: 16),

          // Estado de participaciones
          if (_graficas != null &&
              _graficas!['estado_participaciones'] != null &&
              (_graficas!['estado_participaciones'] as Map).isNotEmpty)
            PieChartWidget(
              title: 'Estado de Mis Participaciones',
              subtitle: 'Distribución según estado actual',
              data: Map<String, int>.from(_graficas!['estado_participaciones']),
              colors: const [
                AppColors.success,
                AppColors.warning,
                AppColors.info,
                AppColors.grey500,
              ],
            ),
          const SizedBox(height: 16),

          // Historial de participación (inscritos vs asistidos)
          if (_graficas != null &&
              _graficas!['historial_participacion'] != null &&
              (_graficas!['historial_participacion'] as Map).isNotEmpty) ...[
            MultiLineChartWidget(
              title: 'Historial de Participación',
              subtitle: 'Eventos inscritos vs asistidos por mes',
              data: [
                MultiLineData(
                  label: 'Inscritos',
                  values: Map<String, int>.from(
                    (_graficas!['historial_participacion'] as Map).map(
                      (k, v) => MapEntry(
                        k.toString(),
                        (v is Map && v['inscritos'] != null)
                            ? (v['inscritos'] is int
                                ? v['inscritos']
                                : int.tryParse(v['inscritos'].toString()) ?? 0)
                            : 0,
                      ),
                    ),
                  ),
                  color: AppColors.info,
                ),
                MultiLineData(
                  label: 'Asistidos',
                  values: Map<String, int>.from(
                    (_graficas!['historial_participacion'] as Map).map(
                      (k, v) => MapEntry(
                        k.toString(),
                        (v is Map && v['asistidos'] != null)
                            ? (v['asistidos'] is int
                                ? v['asistidos']
                                : int.tryParse(v['asistidos'].toString()) ?? 0)
                            : 0,
                      ),
                    ),
                  ),
                  color: AppColors.success,
                ),
              ],
            ),
            const SizedBox(height: 16),
          ],

          // Top eventos con más interacciones
          if (_graficas != null &&
              _graficas!['eventos_interacciones'] != null &&
              (_graficas!['eventos_interacciones'] is List &&
                  (_graficas!['eventos_interacciones'] as List)
                      .isNotEmpty)) ...[
            Align(
              alignment: Alignment.centerLeft,
              child: Text('Mis eventos favoritos', style: AppTypography.titleLarge),
            ),
            const SizedBox(height: AppSpacing.md),
            ...(_graficas!['eventos_interacciones'] as List).take(5).map((e) {
              final evento = e as Map;
              return Padding(
                padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                child: AppCard(
                  elevated: true,
                  padding: EdgeInsets.zero,
                  child: AppListTile(
                    leading: AppAvatar.sm(
                      icon: Icons.favorite,
                      backgroundColor: AppColors.errorLight,
                      foregroundColor: AppColors.error,
                    ),
                    title: evento['titulo']?.toString() ?? 'Evento',
                    subtitle:
                        '${evento['reacciones'] ?? 0} reacciones • ${evento['compartidos'] ?? 0} compartidos',
                    trailing: AppBadge.info(
                      label: '${evento['total'] ?? 0}',
                      icon: Icons.bolt,
                    ),
                  ),
                ),
              );
            }),
          ],
        ],
      ),
    );
  }

  Widget _buildActividadTab() {
    if (_graficas == null) {
      return const EmptyState(
        icon: Icons.timeline_outlined,
        title: 'Sin actividad',
        message: 'No hay datos de actividad para mostrar.',
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        children: [
          // Reacciones por mes
          if (_graficas!['reacciones_por_mes'] != null &&
              (_graficas!['reacciones_por_mes'] as Map).isNotEmpty)
            LineChartWidget(
              title: 'Mis Reacciones por Mes',
              subtitle: 'Interacciones mensuales con eventos',
              data: Map<String, int>.from(_graficas!['reacciones_por_mes']),
              lineColor: AppColors.error,
            ),
          const SizedBox(height: 16),

          // Historial de participación
          if (_graficas!['historial_participacion'] != null &&
              (_graficas!['historial_participacion'] as Map).isNotEmpty) ...[
            BarChartWidget(
              title: 'Participación Mensual',
              subtitle: 'Total de eventos inscritos por mes',
              data: Map<String, int>.from(
                (_graficas!['historial_participacion'] as Map).map(
                  (k, v) => MapEntry(
                    k.toString(),
                    (v is Map && v['inscritos'] != null)
                        ? (v['inscritos'] is int
                            ? v['inscritos']
                            : int.tryParse(v['inscritos'].toString()) ?? 0)
                        : 0,
                  ),
                ),
              ),
              barColor: AppColors.info,
            ),
            const SizedBox(height: 16),
          ],

          // Ubicaciones
          if (_graficas!['ubicaciones'] != null &&
              (_graficas!['ubicaciones'] is List &&
                  (_graficas!['ubicaciones'] as List).isNotEmpty)) ...[
            Align(
              alignment: Alignment.centerLeft,
              child: Text(
                'Ciudades donde he participado',
                style: AppTypography.titleLarge,
              ),
            ),
            const SizedBox(height: AppSpacing.md),
            ...(_graficas!['ubicaciones'] as List).map((ubicacion) {
              final u = ubicacion as Map;
              return Padding(
                padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                child: AppCard(
                  elevated: true,
                  padding: EdgeInsets.zero,
                  child: AppListTile(
                    leading: AppAvatar.sm(
                      icon: Icons.location_on,
                      backgroundColor: AppColors.infoLight,
                      foregroundColor: AppColors.infoDark,
                    ),
                    title: u['ciudad']?.toString() ?? 'Ciudad desconocida',
                    trailing: AppBadge.neutral(
                      label: '${u['cantidad'] ?? 0} eventos',
                      icon: Icons.event,
                    ),
                  ),
                ),
              );
            }),
          ],
        ],
      ),
    );
  }

  Widget _buildUserInfo() {
    if (_usuario == null) return const SizedBox.shrink();

    final nombres = _usuario!['nombres']?.toString() ?? '';
    final apellidos = _usuario!['apellidos']?.toString() ?? '';
    final correo = _usuario!['correo_electronico']?.toString() ?? '';
    final initial = (nombres.isNotEmpty ? nombres[0] : 'U').toUpperCase();

    return AppCard(
      elevated: true,
      child: Row(
        children: [
          AppAvatar.lg(
            initials: initial,
            backgroundColor: AppColors.primaryLight,
            foregroundColor: AppColors.textOnPrimary,
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('$nombres $apellidos'.trim(), style: AppTypography.titleMedium),
                const SizedBox(height: AppSpacing.xxs),
                Text(correo, style: AppTypography.bodySecondary),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
