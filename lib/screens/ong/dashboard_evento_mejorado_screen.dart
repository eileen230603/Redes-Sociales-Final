import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:io';
import 'package:universal_html/html.dart' as html;
import 'package:flutter/foundation.dart';
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
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
              primary: Theme.of(context).primaryColor,
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
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Descargar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
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
              backgroundColor: Colors.green,
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
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
          backgroundColor: Colors.red,
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
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Descargar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
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
              backgroundColor: Colors.green,
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
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
          backgroundColor: Colors.red,
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
        title: const Text('Dashboard del Evento'),
        actions: [
          IconButton(
            icon: const Icon(Icons.date_range),
            onPressed: _selectDateRange,
            tooltip: 'Filtrar por fechas',
          ),
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
          IconButton(
            icon: const Icon(Icons.table_chart),
            onPressed: _descargarExcel,
            tooltip: 'Descargar Excel',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
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
                  tabs: const [
                    Tab(text: 'Resumen', icon: Icon(Icons.dashboard, size: 20)),
                    Tab(
                      text: 'Tendencias',
                      icon: Icon(Icons.trending_up, size: 20),
                    ),
                    Tab(
                      text: 'Participantes',
                      icon: Icon(Icons.people, size: 20),
                    ),
                    Tab(
                      text: 'Asistencia',
                      icon: Icon(Icons.check_circle, size: 20),
                    ),
                    Tab(
                      text: 'Actividad',
                      icon: Icon(Icons.timeline, size: 20),
                    ),
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
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              color: Colors.blue.withOpacity(0.1),
              child: Row(
                children: [
                  Icon(Icons.filter_list, size: 16, color: Colors.blue[700]),
                  const SizedBox(width: 8),
                  Text(
                    'Filtrado: ${DateFormat('dd/MM/yyyy').format(_fechaInicio!)} - ${DateFormat('dd/MM/yyyy').format(_fechaFin!)}',
                    style: TextStyle(
                      color: Colors.blue[700],
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  const Spacer(),
                  TextButton(
                    onPressed: () {
                      setState(() {
                        _fechaInicio = null;
                        _fechaFin = null;
                      });
                      _loadDashboard();
                    },
                    child: const Text('Limpiar'),
                  ),
                ],
              ),
            ),
          Expanded(
            child:
                _isLoading
                    ? const Center(child: CircularProgressIndicator())
                    : _error != null
                    ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.error_outline,
                            size: 64,
                            color: Colors.red[300],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            _error!,
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Colors.red[700]),
                          ),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            onPressed: _loadDashboard,
                            child: const Text('Reintentar'),
                          ),
                        ],
                      ),
                    )
                    : _dashboardData == null
                    ? const Center(child: Text('No hay datos disponibles'))
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
      return const Center(child: Text('No hay métricas disponibles'));
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
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
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 1.0,
                children: [
                  _buildMetricCardLaravel(
                    'Total Participantes',
                    metricas.participantesTotal.toString(),
                    Icons.people,
                    const Color(0xFF0C2B44),
                    comparativas?['participantes_total']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Aprobados',
                    (metricas.participantesPorEstado['aprobada'] ?? 0)
                        .toString(),
                    Icons.check_circle,
                    const Color(0xFF00A36C),
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Pendientes',
                    (metricas.participantesPorEstado['pendiente'] ?? 0)
                        .toString(),
                    Icons.schedule,
                    const Color(0xFF0C2B44),
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Rechazados',
                    (metricas.participantesPorEstado['rechazada'] ?? 0)
                        .toString(),
                    Icons.cancel,
                    const Color(0xFFDC3545),
                    null,
                  ),
                  _buildMetricCardLaravel(
                    'Reacciones',
                    metricas.reacciones.toString(),
                    Icons.favorite,
                    const Color(0xFFDC3545),
                    comparativas?['reacciones']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Compartidos',
                    metricas.compartidos.toString(),
                    Icons.share,
                    const Color(0xFF00A36C),
                    comparativas?['compartidos']?.crecimiento,
                  ),
                  _buildMetricCardLaravel(
                    'Voluntarios',
                    metricas.voluntarios.toString(),
                    Icons.volunteer_activism,
                    const Color(0xFFFFC107),
                    comparativas?['voluntarios']?.crecimiento,
                  ),
                  if (metricas.participantesTotal > 0)
                    _buildMetricCardLaravel(
                      'Tasa Aprobación',
                      '${((metricas.participantesPorEstado['aprobada'] ?? 0) / metricas.participantesTotal * 100).toStringAsFixed(1)}%',
                      Icons.trending_up,
                      const Color(0xFF00A36C),
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
              colors: [Colors.green, Colors.grey],
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
              colors: [Colors.green, Colors.orange, Colors.red, Colors.grey],
            ),
          const SizedBox(height: 16),

          // Métricas radar
          if (_dashboardData!.metricasRadar != null &&
              _dashboardData!.metricasRadar!.isNotEmpty)
            RadarChartWidget(
              title: 'Métricas Generales',
              subtitle: 'Vista general del rendimiento del evento',
              data: _dashboardData!.metricasRadar!,
              fillColor: Colors.teal,
            ),
        ],
      ),
    );
  }

  Widget _buildTendenciasTab() {
    final tendencias = _dashboardData!.tendencias;

    if (tendencias == null) {
      return const Center(child: Text('No hay tendencias disponibles'));
    }

    final reaccionesPorDia = tendencias['reacciones_por_dia'] as Map? ?? {};
    final compartidosPorDia = tendencias['compartidos_por_dia'] as Map? ?? {};
    final inscripcionesPorDia =
        tendencias['inscripciones_por_dia'] as Map? ?? {};

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header estilo Laravel
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF0C2B44), Color(0xFF00A36C)],
              ),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Row(
              children: [
                Icon(Icons.trending_up, color: Colors.white, size: 32),
                SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Análisis de Tendencias',
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Visualización de datos temporales y comparativas',
                        style: TextStyle(fontSize: 14, color: Colors.white70),
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
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
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
                      const Color(0xFF00A36C),
                      BarChartWidget(
                        title: '',
                        subtitle: '',
                        data: Map<String, int>.from(compartidosPorDia),
                        barColor: const Color(0xFF00A36C),
                      ),
                    ),
                  // Inscripciones por día
                  if (inscripcionesPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Inscripciones por Día',
                      Icons.calendar_today,
                      const Color(0xFF17A2B8),
                      LineChartWidget(
                        title: '',
                        subtitle: '',
                        data: Map<String, int>.from(inscripcionesPorDia),
                        lineColor: const Color(0xFF17A2B8),
                      ),
                    ),
                  // Gráfico múltiple de tendencias
                  if (reaccionesPorDia.isNotEmpty ||
                      compartidosPorDia.isNotEmpty ||
                      inscripcionesPorDia.isNotEmpty)
                    _buildChartCardLaravel(
                      'Tendencias Temporales',
                      Icons.trending_up,
                      const Color(0xFF0C2B44),
                      MultiLineChartWidget(
                        title: '',
                        subtitle: '',
                        data: [
                          if (reaccionesPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Reacciones',
                              values: Map<String, int>.from(reaccionesPorDia),
                              color: const Color(0xFFDC3545),
                            ),
                          if (compartidosPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Compartidos',
                              values: Map<String, int>.from(compartidosPorDia),
                              color: const Color(0xFF00A36C),
                            ),
                          if (inscripcionesPorDia.isNotEmpty)
                            MultiLineData(
                              label: 'Inscripciones',
                              values: Map<String, int>.from(
                                inscripcionesPorDia,
                              ),
                              color: const Color(0xFF17A2B8),
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
      padding: const EdgeInsets.all(16),
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
              barColor: Colors.blue,
            ),

          const SizedBox(height: 24),

          // Top participantes
          if (topParticipantes != null && topParticipantes.isNotEmpty) ...[
            const Text(
              'Top Participantes',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            ...topParticipantes.asMap().entries.map((entry) {
              final index = entry.key;
              final participante = entry.value;

              return Card(
                margin: const EdgeInsets.only(bottom: 8),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: _getTopColor(index),
                    child: Text(
                      '${index + 1}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  title: Text(participante.nombre),
                  trailing: Chip(
                    label: Text('${participante.totalActividades} actividades'),
                    backgroundColor: _getTopColor(index).withOpacity(0.2),
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
      return const Center(child: CircularProgressIndicator());
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
                    Colors.green,
                    Icons.check_circle,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildAsistenciaCard(
                    'No Asistieron',
                    participantesNoAsistieron.length,
                    Colors.red,
                    Icons.cancel,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildAsistenciaCard(
                    'Sin Registro',
                    participantesSinRegistro.length,
                    Colors.orange,
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
                  Colors.green,
                ),
                _buildListaParticipantes(
                  participantesNoAsistieron,
                  'No Asistieron',
                  Colors.red,
                ),
                _buildListaParticipantes(
                  participantesSinRegistro,
                  'Sin Registro de Asistencia',
                  Colors.orange,
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
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(12),
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [color.withOpacity(0.1), color.withOpacity(0.05)],
          ),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 32),
            const SizedBox(height: 8),
            Text(
              count.toString(),
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[700],
                fontWeight: FontWeight.w500,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildListaParticipantes(
    List<dynamic> participantes,
    String titulo,
    Color color,
  ) {
    if (participantes.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.people_outline, size: 64, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              'No hay participantes en esta categoría',
              style: TextStyle(color: Colors.grey[600], fontSize: 16),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
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
          duration: Duration(milliseconds: 200 + (index * 30)),
          curve: Curves.easeOut,
          builder: (context, value, child) {
            return Opacity(
              opacity: value,
              child: Transform.translate(
                offset: Offset(0, 20 * (1 - value)),
                child: child,
              ),
            );
          },
          child: Card(
            margin: const EdgeInsets.only(bottom: 12),
            elevation: 2,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
              side: BorderSide(color: color.withOpacity(0.3), width: 1),
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 8,
              ),
              leading: CircleAvatar(
                radius: 28,
                backgroundColor: color.withOpacity(0.1),
                backgroundImage:
                    fotoPerfil != null ? NetworkImage(fotoPerfil) : null,
                child:
                    fotoPerfil == null
                        ? Text(
                          nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
                          style: TextStyle(
                            color: color,
                            fontWeight: FontWeight.bold,
                            fontSize: 18,
                          ),
                        )
                        : null,
              ),
              title: Text(
                nombre,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 4),
                  Text(
                    email,
                    style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                  ),
                  if (fechaInscripcion != null) ...[
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.calendar_today,
                          size: 12,
                          color: Colors.grey[500],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          _formatFechaParticipante(fechaInscripcion),
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.grey[500],
                          ),
                        ),
                      ],
                    ),
                  ],
                  if (fechaCheckin != null && asistio) ...[
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.access_time,
                          size: 12,
                          color: Colors.green[600],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'Check-in: ${_formatFechaParticipante(fechaCheckin)}',
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.green[600],
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
              trailing: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: _getEstadoColor(estado).withOpacity(0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      estado.toUpperCase(),
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: _getEstadoColor(estado),
                      ),
                    ),
                  ),
                  if (asistio) ...[
                    const SizedBox(height: 4),
                    Icon(Icons.check_circle, color: Colors.green, size: 20),
                  ],
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Color _getEstadoColor(String estado) {
    switch (estado.toLowerCase()) {
      case 'aprobada':
      case 'aprobado':
        return Colors.green;
      case 'rechazada':
      case 'rechazado':
        return Colors.red;
      case 'pendiente':
        return Colors.orange;
      default:
        return Colors.grey;
    }
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
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          // Actividad semanal (Area Chart)
          if (actividadSemanal != null && actividadSemanal.isNotEmpty)
            AreaChartWidget(
              title: 'Actividad Semanal',
              subtitle: 'Total de interacciones por semana',
              data: actividadSemanal,
              areaColor: Colors.purple,
              borderColor: Colors.purple,
            ),

          const SizedBox(height: 24),

          // Actividad reciente (30 días)
          if (actividadReciente != null && actividadReciente.isNotEmpty) ...[
            const Text(
              'Actividad Reciente (Últimos 30 días)',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),

            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children:
                      actividadReciente.entries.take(30).map((entry) {
                        final fecha = entry.key;
                        final actividad = entry.value;

                        return Padding(
                          padding: const EdgeInsets.only(bottom: 12),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _formatFecha(fecha),
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 14,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Row(
                                children: [
                                  _buildActividadChip(
                                    'Reacciones',
                                    actividad.reacciones,
                                    Colors.red,
                                  ),
                                  const SizedBox(width: 8),
                                  _buildActividadChip(
                                    'Compartidos',
                                    actividad.compartidos,
                                    Colors.green,
                                  ),
                                  const SizedBox(width: 8),
                                  _buildActividadChip(
                                    'Inscripciones',
                                    actividad.inscripciones,
                                    Colors.blue,
                                  ),
                                ],
                              ),
                              const Divider(),
                            ],
                          ),
                        );
                      }).toList(),
                ),
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
    // Crear gradiente basado en el color
    final gradientColors = _getGradientColors(color);

    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: const Duration(milliseconds: 600),
      curve: Curves.easeOut,
      builder: (context, animValue, child) {
        return Opacity(
          opacity: animValue,
          child: Transform.scale(scale: 0.9 + (0.1 * animValue), child: child),
        );
      },
      child: MouseRegion(
        cursor: SystemMouseCursors.click,
        child: Card(
          elevation: 4,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          child: InkWell(
            onTap: () {},
            borderRadius: BorderRadius.circular(16),
            child: Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: gradientColors,
                ),
                boxShadow: [
                  BoxShadow(
                    color: color.withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              label.toUpperCase(),
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                                letterSpacing: 0.5,
                                height: 1.2,
                              ),
                            ),
                            const SizedBox(height: 12),
                            Text(
                              value,
                              style: const TextStyle(
                                fontSize: 36,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                                height: 1.0,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Icon(
                        icon,
                        size: 48,
                        color: Colors.white.withOpacity(0.2),
                      ),
                    ],
                  ),
                  if (trend != null) ...[
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            trend > 0 ? Icons.trending_up : Icons.trending_down,
                            size: 14,
                            color: Colors.white,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            '${trend > 0 ? '+' : ''}${trend.toStringAsFixed(1)}%',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  List<Color> _getGradientColors(Color baseColor) {
    // Colores específicos estilo Laravel
    if (baseColor.value == const Color(0xFF0C2B44).value) {
      return [const Color(0xFF0C2B44), const Color(0xFF0A2338)];
    } else if (baseColor.value == const Color(0xFF00A36C).value) {
      return [const Color(0xFF00A36C), const Color(0xFF008A5A)];
    } else if (baseColor.value == const Color(0xFFDC3545).value) {
      return [const Color(0xFFDC3545), const Color(0xFFC82333)];
    } else if (baseColor.value == const Color(0xFFFFC107).value) {
      return [const Color(0xFFFFC107), const Color(0xFFE0A800)];
    } else {
      // Gradiente genérico
      return [
        baseColor,
        Color.fromRGBO(
          (baseColor.red * 0.8).round(),
          (baseColor.green * 0.8).round(),
          (baseColor.blue * 0.8).round(),
          1.0,
        ),
      ];
    }
  }

  Widget _buildInfoEventoMejorado() {
    if (_evento == null) return const SizedBox.shrink();

    return Card(
      elevation: 3,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.blue.withOpacity(0.1),
              Colors.blue.withOpacity(0.05),
            ],
          ),
        ),
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.event, size: 32, color: Colors.blue),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _evento!.titulo,
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (_evento!.descripcion != null) ...[
                        const SizedBox(height: 8),
                        Text(
                          _evento!.descripcion!,
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[600],
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Wrap(
              spacing: 16,
              runSpacing: 12,
              children: [
                _buildInfoChip(
                  Icons.calendar_today,
                  _formatDate(_evento!.fechaInicio),
                  Colors.blue,
                ),
                if (_evento!.ciudad != null)
                  _buildInfoChip(
                    Icons.location_on,
                    _evento!.ciudad!,
                    Colors.green,
                  ),
                if (_evento!.estado != null)
                  _buildInfoChip(
                    Icons.info_outline,
                    _evento!.estado!,
                    Colors.orange,
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildChartCardLaravel(
    String title,
    IconData icon,
    Color accentColor,
    Widget chart,
  ) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header con gradiente estilo Laravel
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(12),
                topRight: Radius.circular(12),
              ),
              border: Border(bottom: BorderSide(color: accentColor, width: 2)),
            ),
            child: Row(
              children: [
                Icon(icon, color: accentColor, size: 20),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    title,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0C2B44),
                    ),
                  ),
                ),
              ],
            ),
          ),
          // Contenido del gráfico
          Expanded(
            child: Padding(padding: const EdgeInsets.all(16), child: chart),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoChip(IconData icon, String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.3), width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 6),
          Text(
            text,
            style: TextStyle(
              fontSize: 13,
              color: Colors.grey[700],
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoEvento() {
    if (_evento == null) return const SizedBox.shrink();

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                const Icon(Icons.event, size: 28, color: Colors.blue),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    _evento!.titulo,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.calendar_today, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text(
                  _formatDate(_evento!.fechaInicio),
                  style: TextStyle(color: Colors.grey[600]),
                ),
              ],
            ),
            if (_evento!.ciudad != null) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.location_on, size: 16, color: Colors.grey[600]),
                  const SizedBox(width: 8),
                  Text(
                    _evento!.ciudad!,
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildActividadChip(String label, int value, Color color) {
    return Chip(
      avatar: CircleAvatar(
        backgroundColor: color,
        child: Text(
          value.toString(),
          style: const TextStyle(color: Colors.white, fontSize: 10),
        ),
      ),
      label: Text(label),
      backgroundColor: color.withOpacity(0.1),
      labelStyle: TextStyle(fontSize: 11, color: color),
    );
  }

  Color _getTopColor(int index) {
    switch (index) {
      case 0:
        return Colors.amber;
      case 1:
        return Colors.grey[600]!;
      case 2:
        return Colors.brown;
      default:
        return Colors.blue;
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
