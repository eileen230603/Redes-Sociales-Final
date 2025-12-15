import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:io';
import 'package:universal_html/html.dart' as html;
import 'package:flutter/foundation.dart';
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import 'package:intl/intl.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../models/evento.dart';
import '../../models/dashboard/dashboard_data.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/line_chart_widget.dart';
import '../../widgets/charts/bar_chart_widget.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/area_chart_widget.dart';
import '../../widgets/charts/grouped_bar_chart_widget.dart';
import '../../widgets/charts/multi_line_chart_widget.dart';
import '../../widgets/charts/radar_chart_widget.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
import '../../services/cache_service.dart';
import '../evento_detail_screen.dart';

class DashboardEventoMejoradoScreen extends StatefulWidget {
  final int eventoId;
  final String? eventoTitulo;

  const DashboardEventoMejoradoScreen({
    super.key,
    required this.eventoId,
    this.eventoTitulo,
  });

  @override
  State<DashboardEventoMejoradoScreen> createState() =>
      _DashboardEventoMejoradoScreenState();
}

class _DashboardEventoMejoradoScreenState
    extends State<DashboardEventoMejoradoScreen>
    with SingleTickerProviderStateMixin {
  DashboardData? _dashboardData;
  bool _isLoading = true;
  String? _error;
  Evento? _evento;
  late TabController _tabController;

  DateTime? _fechaInicio;
  DateTime? _fechaFin;

  // Datos de participantes y asistencia
  List<dynamic> _participantes = [];
  bool _isLoadingParticipantes = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 5, vsync: this);
    _loadDashboard();
    _loadParticipantes();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadDashboard({bool useCache = true}) async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    Map<String, dynamic> queryParams = {};
    if (_fechaInicio != null) {
      queryParams['fecha_inicio'] = DateFormat(
        'yyyy-MM-dd',
      ).format(_fechaInicio!);
    }
    if (_fechaFin != null) {
      queryParams['fecha_fin'] = DateFormat('yyyy-MM-dd').format(_fechaFin!);
    }

    // Usar el endpoint completo
    final result = await ApiService.getDashboardEventoCompleto(
      widget.eventoId,
      fechaInicio:
          _fechaInicio != null
              ? DateFormat('yyyy-MM-dd').format(_fechaInicio!)
              : null,
      fechaFin:
          _fechaFin != null
              ? DateFormat('yyyy-MM-dd').format(_fechaFin!)
              : null,
      useCache: useCache,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _dashboardData = DashboardData.fromJson(result);
        if (result['evento'] != null) {
          _evento = Evento.fromJson(result['evento'] as Map<String, dynamic>);
        }
      } else {
        _error = result['error'] as String? ?? 'Error al cargar dashboard';
      }
    });
  }

  Future<void> _selectDateRange() async {
    final DateTimeRange? picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDateRange:
          _fechaInicio != null && _fechaFin != null
              ? DateTimeRange(start: _fechaInicio!, end: _fechaFin!)
              : null,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: AppColors.primary,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        _fechaInicio = picked.start;
        _fechaFin = picked.end;
      });
      _loadDashboard();
    }
  }

  Future<void> _descargarPdf() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Descargar Dashboard PDF'),
            content: const Text(
              '¿Deseas descargar el dashboard del evento en formato PDF?',
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
      final result = await ApiService.exportarDashboardEventoPdfCompleto(
        widget.eventoId,
        fechaInicio:
            _fechaInicio != null
                ? DateFormat('yyyy-MM-dd').format(_fechaInicio!)
                : null,
        fechaFin:
            _fechaFin != null
                ? DateFormat('yyyy-MM-dd').format(_fechaFin!)
                : null,
      );

      if (!mounted) return;
      Navigator.of(context).pop();

      if (result['success'] == true) {
        final pdfBytes = result['pdfBytes'] as List<int>?;

        if (pdfBytes != null) {
          if (kIsWeb) {
            final blob = html.Blob([pdfBytes], 'application/pdf');
            final url = html.Url.createObjectUrlFromBlob(blob);
            final anchor =
                html.AnchorElement(href: url)
                  ..setAttribute(
                    "download",
                    "dashboard_evento_${widget.eventoId}.pdf",
                  )
                  ..click();
            html.Url.revokeObjectUrl(url);
          } else {
            final directory = await getApplicationDocumentsDirectory();
            final file = File(
              '${directory.path}/dashboard_evento_${widget.eventoId}_${DateTime.now().millisecondsSinceEpoch}.pdf',
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

  Future<void> _descargarExcel() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Descargar Dashboard Excel'),
            content: const Text(
              '¿Deseas descargar el dashboard del evento en formato Excel (CSV)?',
            ),
            actions: [
              AppButton.text(
                label: 'Cancelar',
                onPressed: () => Navigator.of(context).pop(false),
              ),
              AppButton.primary(
                label: 'Descargar',
                icon: Icons.table_chart,
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
      final result = await ApiService.exportarDashboardEventoExcelCompleto(
        widget.eventoId,
        fechaInicio:
            _fechaInicio != null
                ? DateFormat('yyyy-MM-dd').format(_fechaInicio!)
                : null,
        fechaFin:
            _fechaFin != null
                ? DateFormat('yyyy-MM-dd').format(_fechaFin!)
                : null,
      );

      if (!mounted) return;
      Navigator.of(context).pop();

      if (result['success'] == true) {
        final csvBytes = result['csvBytes'] as List<int>?;

        if (csvBytes != null) {
          if (kIsWeb) {
            final blob = html.Blob([csvBytes], 'text/csv;charset=utf-8');
            final url = html.Url.createObjectUrlFromBlob(blob);
            final anchor =
                html.AnchorElement(href: url)
                  ..setAttribute(
                    "download",
                    "dashboard_evento_${widget.eventoId}.csv",
                  )
                  ..click();
            html.Url.revokeObjectUrl(url);
          } else {
            final directory = await getApplicationDocumentsDirectory();
            final file = File(
              '${directory.path}/dashboard_evento_${widget.eventoId}_${DateTime.now().millisecondsSinceEpoch}.csv',
            );
            await file.writeAsBytes(csvBytes);
            await OpenFile.open(file.path);
          }

          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Excel descargado exitosamente'),
              backgroundColor: AppColors.success,
            ),
          );
        }
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al descargar Excel',
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

  Future<void> _loadParticipantes() async {
    setState(() {
      _isLoadingParticipantes = true;
    });

    try {
      final result = await ApiService.getParticipantesEvento(widget.eventoId);

      if (!mounted) return;

      setState(() {
        _isLoadingParticipantes = false;
        if (result['success'] == true) {
          _participantes = result['participantes'] as List? ?? [];
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoadingParticipantes = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Dashboard del evento'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.date_range),
            onPressed: _selectDateRange,
            tooltip: 'Filtrar por fechas',
          ),
          IconButton(
            icon: AppIcon.md(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
          IconButton(
            icon: AppIcon.md(Icons.table_chart),
            onPressed: _descargarExcel,
            tooltip: 'Descargar Excel',
          ),
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: () => _loadDashboard(useCache: false),
            tooltip: 'Actualizar',
          ),
        ],
        bottom:
            _isLoading || _error != null
                ? null
                : TabBar(
                  controller: _tabController,
                  isScrollable: true,
                  tabs: [
                    Tab(text: 'Resumen', icon: AppIcon.sm(Icons.dashboard)),
                    Tab(text: 'Tendencias', icon: AppIcon.sm(Icons.trending_up)),
                    Tab(text: 'Participantes', icon: AppIcon.sm(Icons.people)),
                    Tab(text: 'Asistencia', icon: AppIcon.sm(Icons.check_circle)),
                    Tab(text: 'Actividad', icon: AppIcon.sm(Icons.timeline)),
                  ],
                ),
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
              BreadcrumbItem(
                label: widget.eventoTitulo ?? 'Dashboard',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder:
                          (context) =>
                              EventoDetailScreen(eventoId: widget.eventoId),
                    ),
                  );
                },
              ),
              BreadcrumbItem(label: 'Dashboard'),
            ],
          ),
          if (_fechaInicio != null && _fechaFin != null)
            Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.md,
                vertical: AppSpacing.sm,
              ),
              child: AppCard(
                backgroundColor: AppColors.infoLight,
                child: Row(
                  children: [
                    AppIcon.sm(Icons.filter_list, color: AppColors.infoDark),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Text(
                        'Filtrado: ${DateFormat('dd/MM/yyyy').format(_fechaInicio!)} - ${DateFormat('dd/MM/yyyy').format(_fechaFin!)}',
                        style: AppTypography.labelMedium.copyWith(
                          color: AppColors.infoDark,
                        ),
                      ),
                    ),
                    AppButton.text(
                      label: 'Limpiar',
                      onPressed: () {
                        setState(() {
                          _fechaInicio = null;
                          _fechaFin = null;
                        });
                        _loadDashboard();
                      },
                    ),
                  ],
                ),
              ),
            ),
          Expanded(
            child:
                _isLoading
                    ? SkeletonLoader.dashboard()
                    : _error != null
                    ? ErrorView.serverError(
                      onRetry: _loadDashboard,
                      errorDetails: _error,
                    )
                    : _dashboardData == null
                    ? const EmptyState(
                      icon: Icons.dashboard_outlined,
                      title: 'Sin datos',
                      message: 'No hay información disponible para mostrar.',
                    )
                    : TabBarView(
                      controller: _tabController,
                      children: [
                        _buildResumenTab(),
                        _buildTendenciasTab(),
                        _buildParticipantesTab(),
                        _buildAsistenciaTab(),
                        _buildActividadTab(),
                      ],
                    ),
          ),
        ],
      ),
    );
  }

  Widget _buildResumenTab() {
    final metricas = _dashboardData!.metricas;
    final comparativas = _dashboardData!.comparativas;

    if (metricas == null) {
      return const EmptyState(
        icon: Icons.insights_outlined,
        title: 'Sin métricas',
        message: 'No hay métricas disponibles para este evento.',
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Información del evento (mejorada)
          if (_evento != null) _buildInfoEventoMejorado(),
          const SizedBox(height: 24),

          // Métricas principales en grid estilo Laravel
          LayoutBuilder(
            builder: (context, constraints) {
              final crossAxisCount =
                  constraints.maxWidth > 700
                      ? 4
                      : constraints.maxWidth > 500
                      ? 3
                      : 2;
              return GridView.count(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisCount: crossAxisCount,
                crossAxisSpacing: AppSpacing.md,
                mainAxisSpacing: AppSpacing.md,
                childAspectRatio: 1.0,
                children: [
                  _buildMetricCardLaravel(
                    'Total Participantes',
                    metricas.participantesTotal.toString(),
                    Icons.people,
                    AppColors.primary,
                    comparativas?['participantes_total']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Aprobados',
                    (metricas.participantesPorEstado['aprobada'] ?? 0)
                        .toString(),
                    Icons.check_circle,
                    AppColors.success,
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Pendientes',
                    (metricas.participantesPorEstado['pendiente'] ?? 0)
                        .toString(),
                    Icons.schedule,
                    AppColors.primary,
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Rechazados',
                    (metricas.participantesPorEstado['rechazada'] ?? 0)
                        .toString(),
                    Icons.cancel,
                    AppColors.error,
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Reacciones',
                    metricas.reacciones.toString(),
                    Icons.favorite,
                    AppColors.error,
                    comparativas?['reacciones']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Compartidos',
                    metricas.compartidos.toString(),
                    Icons.share,
                    AppColors.accent,
                    comparativas?['compartidos']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Voluntarios',
                    metricas.voluntarios.toString(),
                    Icons.volunteer_activism,
                    AppColors.warning,
                    comparativas?['voluntarios']?.crecimiento,
                  ),
                  if (metricas.participantesTotal > 0)
                    _buildMetricCardLaravel(
                      'Tasa Aprobación',
                      '${((metricas.participantesPorEstado['aprobada'] ?? 0) / metricas.participantesTotal * 100).toStringAsFixed(1)}%',
                      Icons.trending_up,
                      AppColors.success,
                      null,
                    ),
                ],
              );
            },
          ),

          const SizedBox(height: 24),

          // Comparativa actual vs período anterior
          if (_dashboardData!.comparativas != null &&
              _dashboardData!.comparativas!.isNotEmpty) ...[
            GroupedBarChartWidget(
              title: 'Comparativa Actual vs Período Anterior',
              subtitle: 'Comparación de métricas entre períodos',
              data: [
                GroupedBarData(
                  label: 'Reacciones',
                  values: {
                    'Actual':
                        (_dashboardData!.comparativas!['reacciones']?.actual ??
                                0)
                            .toDouble(),
                    'Anterior':
                        (_dashboardData!
                                    .comparativas!['reacciones']
                                    ?.anterior ??
                                0)
                            .toDouble(),
                  },
                ),
                GroupedBarData(
                  label: 'Compartidos',
                  values: {
                    'Actual':
                        (_dashboardData!.comparativas!['compartidos']?.actual ??
                                0)
                            .toDouble(),
                    'Anterior':
                        (_dashboardData!
                                    .comparativas!['compartidos']
                                    ?.anterior ??
                                0)
                            .toDouble(),
                  },
                ),
                GroupedBarData(
                  label: 'Voluntarios',
                  values: {
                    'Actual':
                        (_dashboardData!.comparativas!['voluntarios']?.actual ??
                                0)
                            .toDouble(),
                    'Anterior':
                        (_dashboardData!
                                    .comparativas!['voluntarios']
                                    ?.anterior ??
                                0)
                            .toDouble(),
                  },
                ),
                GroupedBarData(
                  label: 'Participantes',
                  values: {
                    'Actual':
                        (_dashboardData!
                                    .comparativas!['participantes_total']
                                    ?.actual ??
                                0)
                            .toDouble(),
                    'Anterior':
                        (_dashboardData!
                                    .comparativas!['participantes_total']
                                    ?.anterior ??
                                0)
                            .toDouble(),
                  },
                ),
              ],
              seriesNames: ['Actual', 'Anterior'],
              colors: const [AppColors.success, AppColors.grey500],
            ),
            const SizedBox(height: 16),
          ],

          // Distribución por estados
          if (_dashboardData!.distribucionEstados != null &&
              _dashboardData!.distribucionEstados!.isNotEmpty)
            PieChartWidget(
              title: 'Distribución por Estados',
              subtitle: 'Participantes por estado de inscripción',
              data: _dashboardData!.distribucionEstados!,
              colors: const [
                AppColors.success,
                AppColors.warning,
                AppColors.error,
                AppColors.grey500,
              ],
            ),
          const SizedBox(height: 16),

          // Métricas radar
          if (_dashboardData!.metricasRadar != null &&
              _dashboardData!.metricasRadar!.isNotEmpty)
            RadarChartWidget(
              title: 'Métricas Generales',
              subtitle: 'Vista general del rendimiento del evento',
              data: _dashboardData!.metricasRadar!,
              fillColor: AppColors.accent,
            ),
        ],
      ),
    );
  }

  Widget _buildTendenciasTab() {
    final tendencias = _dashboardData!.tendencias;

    if (tendencias == null) {
      return const EmptyState(
        icon: Icons.trending_up,
        title: 'Sin tendencias',
        message: 'No hay datos de tendencias disponibles.',
      );
    }

    final reaccionesPorDia = tendencias['reacciones_por_dia'] as Map? ?? {};
    final compartidosPorDia = tendencias['compartidos_por_dia'] as Map? ?? {};
    final inscripcionesPorDia =
        tendencias['inscripciones_por_dia'] as Map? ?? {};

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          AppCard(
            backgroundColor: AppColors.primary,
            elevated: true,
            child: Row(
              children: [
                AppIcon.lg(Icons.trending_up, color: AppColors.textOnPrimary),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Análisis de tendencias',
                        style: AppTypography.titleOnPrimary,
                      ),
                      const SizedBox(height: AppSpacing.xxs),
                      Text(
                        'Visualización de datos temporales y comparativas',
                        style: AppTypography.bodyOnPrimary,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // Gráficos en grid estilo Laravel (2 columnas)
          LayoutBuilder(
            builder: (context, constraints) {
              final crossAxisCount = constraints.maxWidth > 700 ? 2 : 1;
              return GridView.count(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisCount: crossAxisCount,
                crossAxisSpacing: AppSpacing.md,
                mainAxisSpacing: AppSpacing.md,
                childAspectRatio: 1.1,
                children: [
                  // Reacciones por día
                  if (reaccionesPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Reacciones por Día',
                      Icons.favorite,
                      const Color(0xFFDC3545),
                      LineChartWidget(
                        title: '',
                        subtitle: '',
                        data: Map<String, int>.from(reaccionesPorDia),
                        lineColor: const Color(0xFFDC3545),
                      ),
                    ),
                  // Compartidos por día
                  if (compartidosPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Compartidos por Día',
                      Icons.share,
                      AppColors.accent,
                      BarChartWidget(
                        title: '',
                        subtitle: '',
                        data: Map<String, int>.from(compartidosPorDia),
                        barColor: AppColors.accent,
                      ),
                    ),
                  // Inscripciones por día
                  if (inscripcionesPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Inscripciones por Día',
                      Icons.calendar_today,
                      AppColors.info,
                      LineChartWidget(
                        title: '',
                        subtitle: '',
                        data: Map<String, int>.from(inscripcionesPorDia),
                        lineColor: AppColors.info,
                      ),
                    ),
                  // Gráfico múltiple de tendencias
                  if (reaccionesPorDia.isNotEmpty ||
                      compartidosPorDia.isNotEmpty ||
                      inscripcionesPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Tendencias Temporales',
                      Icons.trending_up,
                      AppColors.primary,
                      MultiLineChartWidget(
                        title: '',
                        subtitle: '',
                        data: [
                          if (reaccionesPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Reacciones',
                              values: Map<String, int>.from(reaccionesPorDia),
                              color: AppColors.error,
                            ),
                          if (compartidosPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Compartidos',
                              values: Map<String, int>.from(compartidosPorDia),
                              color: AppColors.accent,
                            ),
                          if (inscripcionesPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Inscripciones',
                              values: Map<String, int>.from(
                                inscripcionesPorDia,
                              ),
                              color: AppColors.info,
                            ),
                        ],
                      ),
                    ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildParticipantesTab() {
    final topParticipantes = _dashboardData!.topParticipantes;
    final metricas = _dashboardData!.metricas;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Participantes por estado
          if (metricas?.participantesPorEstado != null &&
              metricas!.participantesPorEstado.isNotEmpty)
            BarChartWidget(
              title: 'Participantes por Estado',
              subtitle: 'Distribución de participantes según su estado',
              data: metricas.participantesPorEstado,
              barColor: AppColors.info,
            ),

          const SizedBox(height: AppSpacing.lg),

          // Top participantes
          if (topParticipantes != null && topParticipantes.isNotEmpty) ...[
            Text('Top participantes', style: AppTypography.titleLarge),
            const SizedBox(height: AppSpacing.md),
            ...topParticipantes.asMap().entries.map((entry) {
              final index = entry.key;
              final participante = entry.value;

              return Padding(
                padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                child: AppCard(
                  elevated: true,
                  padding: EdgeInsets.zero,
                  child: AppListTile(
                    leading: AppAvatar.sm(
                      initials: '${index + 1}',
                      backgroundColor: _getTopColor(index),
                      foregroundColor: AppColors.textOnPrimary,
                    ),
                    title: participante.nombre,
                    trailing: AppBadge.info(
                      label: '${participante.totalActividades} actividades',
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

  Widget _buildAsistenciaTab() {
    if (_isLoadingParticipantes) {
      return LoadingState.list();
    }

    // Separar participantes por asistencia
    final participantesAsistieron =
        _participantes
            .where((p) => p['asistencia'] == true || p['asistio'] == true)
            .toList();
    final participantesNoAsistieron =
        _participantes
            .where(
              (p) =>
                  (p['asistencia'] == false || p['asistio'] == false) &&
                  (p['asistencia'] != null || p['asistio'] != null),
            )
            .toList();
    final participantesSinRegistro =
        _participantes
            .where((p) => p['asistencia'] == null && p['asistio'] == null)
            .toList();

    return DefaultTabController(
      length: 3,
      child: Column(
        children: [
          // Resumen de asistencia
          Container(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Expanded(
                  child: _buildAsistenciaCard(
                    'Asistieron',
                    participantesAsistieron.length,
                    AppColors.success,
                    Icons.check_circle,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildAsistenciaCard(
                    'No Asistieron',
                    participantesNoAsistieron.length,
                    AppColors.error,
                    Icons.cancel,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildAsistenciaCard(
                    'Sin Registro',
                    participantesSinRegistro.length,
                    AppColors.warning,
                    Icons.help_outline,
                  ),
                ),
              ],
            ),
          ),
          // Tabs para ver cada categoría
          TabBar(
            tabs: const [
              Tab(icon: Icon(Icons.check_circle), text: 'Asistieron'),
              Tab(icon: Icon(Icons.cancel), text: 'No Asistieron'),
              Tab(icon: Icon(Icons.help_outline), text: 'Sin Registro'),
            ],
          ),
          Expanded(
            child: TabBarView(
              children: [
                _buildListaParticipantes(
                  participantesAsistieron,
                  'Asistieron',
                  AppColors.success,
                ),
                _buildListaParticipantes(
                  participantesNoAsistieron,
                  'No Asistieron',
                  AppColors.error,
                ),
                _buildListaParticipantes(
                  participantesSinRegistro,
                  'Sin Registro de Asistencia',
                  AppColors.warning,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAsistenciaCard(
    String label,
    int count,
    Color color,
    IconData icon,
  ) {
    return AppCard(
      elevated: true,
      child: Column(
        children: [
          AppIcon.lg(icon, color: color),
          const SizedBox(height: AppSpacing.xs),
          Text(
            count.toString(),
            style: AppTypography.headlineSmall.copyWith(
              color: color,
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: AppSpacing.xxxs),
          Text(label, style: AppTypography.labelSmall, textAlign: TextAlign.center),
        ],
      ),
    );
  }

  Widget _buildListaParticipantes(
    List<dynamic> participantes,
    String titulo,
    Color color,
  ) {
    if (participantes.isEmpty) {
      return const EmptyState(
        icon: Icons.people_outline,
        title: 'Sin participantes',
        message: 'No hay participantes en esta categoría.',
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(AppSpacing.md),
      itemCount: participantes.length,
      itemBuilder: (context, index) {
        final participante = participantes[index];
        final nombre =
            participante['nombre'] ??
            participante['nombre_completo'] ??
            participante['nombres'] ??
            'Sin nombre';
        final email =
            participante['correo'] ?? participante['email'] ?? 'Sin email';
        final fotoPerfil =
            participante['foto_perfil'] ??
            participante['foto_perfil_url'] ??
            participante['avatar'];
        final fechaInscripcion =
            participante['fecha_inscripcion'] ?? participante['created_at'];
        final estado = participante['estado'] ?? 'pendiente';
        final asistio =
            participante['asistencia'] ?? participante['asistio'] ?? false;
        final fechaCheckin =
            participante['fecha_checkin'] ?? participante['checkin_at'];

        return TweenAnimationBuilder<double>(
          tween: Tween(begin: 0.0, end: 1.0),
          duration: AppDuration.fast + Duration(milliseconds: index * 30),
          curve: AppCurves.decelerate,
          builder: (context, value, child) {
            return Opacity(
              opacity: value,
              child: Transform.translate(
                offset: Offset(0, 20 * (1 - value)),
                child: child,
              ),
            );
          },
          child: Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.sm),
            child: AppCard(
              elevated: true,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  AppAvatar.md(
                    imageUrl: fotoPerfil?.toString(),
                    initials: nombre.isNotEmpty ? nombre[0] : '?',
                    backgroundColor: color.withOpacity(0.12),
                    foregroundColor: color,
                  ),
                  const SizedBox(width: AppSpacing.md),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(nombre, style: AppTypography.titleSmall),
                        const SizedBox(height: AppSpacing.xxs),
                        Text(email, style: AppTypography.bodySecondary),
                        if (fechaInscripcion != null) ...[
                          const SizedBox(height: AppSpacing.xxs),
                          Row(
                            children: [
                              AppIcon.xs(
                                Icons.calendar_today,
                                color: AppColors.textTertiary,
                              ),
                              const SizedBox(width: AppSpacing.xxs),
                              Text(
                                _formatFechaParticipante(fechaInscripcion),
                                style: AppTypography.labelSmall,
                              ),
                            ],
                          ),
                        ],
                        if (fechaCheckin != null && asistio) ...[
                          const SizedBox(height: AppSpacing.xxs),
                          Row(
                            children: [
                              AppIcon.xs(
                                Icons.access_time,
                                color: AppColors.successDark,
                              ),
                              const SizedBox(width: AppSpacing.xxs),
                              Text(
                                'Check-in: ${_formatFechaParticipante(fechaCheckin)}',
                                style: AppTypography.labelSmall.copyWith(
                                  color: AppColors.successDark,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      _buildEstadoBadge(estado.toString()),
                      if (asistio) ...[
                        const SizedBox(height: AppSpacing.xs),
                        AppIcon.sm(Icons.check_circle, color: AppColors.success),
                      ],
                    ],
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildEstadoBadge(String estado) {
    final normalized = estado.toLowerCase();

    if (normalized == 'aprobada' || normalized == 'aprobado') {
      return AppBadge.success(label: estado.toUpperCase(), icon: Icons.check_circle);
    }
    if (normalized == 'rechazada' || normalized == 'rechazado') {
      return AppBadge.error(label: estado.toUpperCase(), icon: Icons.cancel);
    }
    if (normalized == 'pendiente') {
      return AppBadge.warning(label: estado.toUpperCase(), icon: Icons.schedule);
    }

    return AppBadge.neutral(label: estado.toUpperCase(), icon: Icons.info_outline);
  }

  String _formatFechaParticipante(dynamic fecha) {
    try {
      if (fecha is String) {
        final date = DateTime.parse(fecha);
        return DateFormat('dd/MM/yyyy HH:mm').format(date);
      } else if (fecha is DateTime) {
        return DateFormat('dd/MM/yyyy HH:mm').format(fecha);
      }
      return fecha.toString();
    } catch (e) {
      return fecha.toString();
    }
  }

  Widget _buildActividadTab() {
    final actividadSemanal = _dashboardData!.actividadSemanal;
    final actividadReciente = _dashboardData!.actividadReciente;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        children: [
          // Actividad semanal (Area Chart)
          if (actividadSemanal != null && actividadSemanal.isNotEmpty)
            AreaChartWidget(
              title: 'Actividad Semanal',
              subtitle: 'Total de interacciones por semana',
              data: actividadSemanal,
              areaColor: AppColors.primary,
              borderColor: AppColors.primary,
            ),

          const SizedBox(height: AppSpacing.lg),

          // Actividad reciente (30 días)
          if (actividadReciente != null && actividadReciente.isNotEmpty) ...[
            Align(
              alignment: Alignment.centerLeft,
              child: Text(
                'Actividad reciente (últimos 30 días)',
                style: AppTypography.titleLarge,
              ),
            ),
            const SizedBox(height: AppSpacing.md),

            AppCard(
              elevated: true,
              child: Column(
                children:
                    actividadReciente.entries.take(30).map((entry) {
                      final fecha = entry.key;
                      final actividad = entry.value;

                      return Padding(
                        padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(_formatFecha(fecha), style: AppTypography.titleSmall),
                            const SizedBox(height: AppSpacing.xs),
                            Wrap(
                              spacing: AppSpacing.sm,
                              runSpacing: AppSpacing.sm,
                              children: [
                                _buildActividadChip('Reacciones', actividad.reacciones),
                                _buildActividadChip('Compartidos', actividad.compartidos),
                                _buildActividadChip('Inscripciones', actividad.inscripciones),
                              ],
                            ),
                            const SizedBox(height: AppSpacing.sm),
                            const Divider(height: 1),
                          ],
                        ),
                      );
                    }).toList(),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildMetricCardLaravel(
    String label,
    String value,
    IconData icon,
    Color color,
    double? trend,
  ) {
    return MetricCard(
      label: label,
      value: value,
      icon: icon,
      color: color,
      trend: trend,
    );
  }

  Widget _buildInfoEventoMejorado() {
    if (_evento == null) return const SizedBox.shrink();

    return AppCard(
      elevated: true,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(AppSpacing.sm),
                decoration: BoxDecoration(
                  color: AppColors.infoLight,
                  borderRadius: BorderRadius.circular(AppRadius.sm),
                ),
                child: AppIcon.lg(Icons.event, color: AppColors.infoDark),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(_evento!.titulo, style: AppTypography.headlineSmall),
                    if (_evento!.descripcion != null &&
                        _evento!.descripcion!.isNotEmpty) ...[
                      const SizedBox(height: AppSpacing.xs),
                      Text(
                        _evento!.descripcion!,
                        style: AppTypography.bodySecondary,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          Wrap(
            spacing: AppSpacing.md,
            runSpacing: AppSpacing.sm,
            children: [
              _buildInfoChip(
                Icons.calendar_today,
                _formatDate(_evento!.fechaInicio),
                AppColors.info,
              ),
              if (_evento!.ciudad != null)
                _buildInfoChip(
                  Icons.location_on,
                  _evento!.ciudad!,
                  AppColors.success,
                ),
              if (_evento!.estado != null)
                _buildInfoChip(
                  Icons.info_outline,
                  _evento!.estado!,
                  AppColors.warning,
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildChartCardLaravel(
    String title,
    IconData icon,
    Color accentColor,
    Widget chart,
  ) {
    return AppCard(
      elevated: true,
      padding: EdgeInsets.zero,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(AppSpacing.md),
            decoration: BoxDecoration(
              border: Border(
                bottom: BorderSide(color: accentColor, width: 2),
              ),
            ),
            child: Row(
              children: [
                AppIcon.sm(icon, color: accentColor),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: Text(title, style: AppTypography.titleSmall),
                ),
              ],
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(AppSpacing.md),
              child: chart,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoChip(IconData icon, String text, Color color) {
    return AppBadge.custom(
      label: text,
      icon: icon,
      backgroundColor: color.withOpacity(0.12),
      textColor: color,
    );
  }

  Widget _buildInfoEvento() {
    if (_evento == null) return const SizedBox.shrink();

    return AppCard(
      elevated: true,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              AppIcon.md(Icons.event, color: AppColors.info),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Text(_evento!.titulo, style: AppTypography.headlineSmall),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Row(
            children: [
              AppIcon.xs(Icons.calendar_today, color: AppColors.textSecondary),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                child: Text(
                  _formatDate(_evento!.fechaInicio),
                  style: AppTypography.bodySecondary,
                ),
              ),
            ],
          ),
          if (_evento!.ciudad != null) ...[
            const SizedBox(height: AppSpacing.xs),
            Row(
              children: [
                AppIcon.xs(Icons.location_on, color: AppColors.textSecondary),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: Text(_evento!.ciudad!, style: AppTypography.bodySecondary),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildActividadChip(String label, int value) {
    final normalized = label.toLowerCase();

    if (normalized.contains('reaccion')) {
      return AppBadge.error(label: value.toString(), icon: Icons.favorite);
    }
    if (normalized.contains('compart')) {
      return AppBadge.success(label: value.toString(), icon: Icons.share);
    }
    return AppBadge.info(label: value.toString(), icon: Icons.person_add);
  }

  Color _getTopColor(int index) {
    switch (index) {
      case 0:
        return AppColors.warning;
      case 1:
        return AppColors.grey600;
      case 2:
        return AppColors.primary;
      default:
        return AppColors.info;
    }
  }

  String _formatDate(DateTime date) {
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
    return '${date.day} de ${months[date.month - 1]} de ${date.year}';
  }

  String _formatFecha(String fecha) {
    try {
      final date = DateTime.parse(fecha);
      return DateFormat('EEEE, d MMMM', 'es').format(date);
    } catch (e) {
      return fecha;
    }
  }
}
