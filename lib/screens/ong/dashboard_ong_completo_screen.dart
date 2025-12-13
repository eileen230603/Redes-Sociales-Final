import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:io';
import 'package:universal_html/html.dart' as html;
import 'package:flutter/foundation.dart';
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/line_chart_widget.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/area_chart_widget.dart';
import '../../widgets/charts/grouped_bar_chart_widget.dart';
import '../../widgets/charts/radar_chart_widget.dart';
import '../../widgets/filters/advanced_filter_widget.dart';
import '../../models/dashboard/ong_dashboard_data.dart';
import '../evento_detail_screen.dart';
import '../ong/eventos_ong_screen.dart';

/// Dashboard ONG Completo
/// Implementa todas las funcionalidades del backend Laravel
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

  // Control de navegación a secciones
  final Map<String, bool> _sectionVisibility = {
    'resumen': true,
    'graficos': true,
    'rankings': true,
    'participacion': true,
    'alertas': true,
    'reportes': true,
  };

  @override
  void initState() {
    super.initState();
    _loadDashboard();
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
          final data = result['data'] as Map<String, dynamic>;
          _dashboardData = OngDashboardData.fromJson(data);
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
      print('❌ Error en DashboardOngCompletoScreen: $e');
    }
  }

  Future<void> _exportarPdf() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Exportar Dashboard PDF'),
            content: const Text(
              '¿Deseas exportar el dashboard en formato PDF?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Exportar'),
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
      final result = await ApiService.exportarDashboardOngPdf(
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
                    'download',
                    'dashboard-ong-${DateTime.now().millisecondsSinceEpoch}.pdf',
                  )
                  ..click();
            html.Url.revokeObjectUrl(url);
          } else {
            final directory = await getApplicationDocumentsDirectory();
            final file = File(
              '${directory.path}/dashboard-ong-${DateTime.now().millisecondsSinceEpoch}.pdf',
            );
            await file.writeAsBytes(pdfBytes);
            await OpenFile.open(file.path);
          }

          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('PDF exportado exitosamente'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al exportar PDF',
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

  Future<void> _exportarExcel() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Exportar Dashboard Excel'),
            content: const Text(
              '¿Deseas exportar el dashboard en formato Excel?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Exportar'),
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
      final result = await ApiService.exportarDashboardOngExcel(
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
      );

      if (!mounted) return;
      Navigator.of(context).pop();

      if (result['success'] == true) {
        final excelBytes = result['excelBytes'] as List<int>?;

        if (excelBytes != null) {
          if (kIsWeb) {
            final blob = html.Blob(
              [excelBytes],
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            final url = html.Url.createObjectUrlFromBlob(blob);
            final anchor =
                html.AnchorElement(href: url)
                  ..setAttribute(
                    'download',
                    'dashboard-ong-${DateTime.now().millisecondsSinceEpoch}.xlsx',
                  )
                  ..click();
            html.Url.revokeObjectUrl(url);
          } else {
            final directory = await getApplicationDocumentsDirectory();
            final file = File(
              '${directory.path}/dashboard-ong-${DateTime.now().millisecondsSinceEpoch}.xlsx',
            );
            await file.writeAsBytes(excelBytes);
            await OpenFile.open(file.path);
          }

          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Excel exportado exitosamente'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al exportar Excel',
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Dashboard ONG'),
        actions: [
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: _exportarPdf,
            tooltip: 'Exportar PDF',
          ),
          IconButton(
            icon: const Icon(Icons.table_chart),
            onPressed: _exportarExcel,
            tooltip: 'Exportar Excel',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadDashboard(useCache: false),
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body:
          _isLoading
              ? const Center(child: CircularProgressIndicator())
              : _error != null
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                    const SizedBox(height: 16),
                    Text(
                      _error!,
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.red[700]),
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () => _loadDashboard(useCache: false),
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _dashboardData == null
              ? const Center(child: Text('No hay datos disponibles'))
              : Column(
                children: [
                  // Filtros avanzados
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
                  // Contenido principal con secciones colapsables
                  Expanded(child: _buildDashboardContent()),
                ],
              ),
    );
  }

  Widget _buildDashboardContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: LayoutBuilder(
        builder: (context, constraints) {
          final crossAxisCount = constraints.maxWidth > 800 ? 2 : 1;
          return GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: crossAxisCount,
            crossAxisSpacing: 20,
            mainAxisSpacing: 20,
            childAspectRatio: crossAxisCount == 2 ? 1.1 : 1.3,
            children: [
              // 1. Resumen General
              _buildDashboardBlock(
                key: 'resumen',
                title: 'Resumen General',
                subtitle: 'KPIs principales y métricas clave',
                icon: Icons.dashboard,
                iconColor: const Color(0xFF0C2B44),
                gradientColors: [
                  const Color(0xFF0C2B44),
                  const Color(0xFF1A4A6B),
                ],
                previewContent: _buildResumenPreview(),
                fullContentBuilder: () => _buildResumenSection(),
              ),

              // 2. Análisis y Gráficos
              _buildDashboardBlock(
                key: 'graficos',
                title: 'Análisis y Gráficos',
                subtitle: 'Tendencias, comparativas y visualizaciones',
                icon: Icons.bar_chart,
                iconColor: const Color(0xFF00A36C),
                gradientColors: [
                  const Color(0xFF00A36C),
                  const Color(0xFF008A5A),
                ],
                previewContent: _buildGraficosPreview(),
                fullContentBuilder: () => _buildGraficosSection(),
              ),

              // 3. Rankings y Top
              _buildDashboardBlock(
                key: 'rankings',
                title: 'Rankings y Top',
                subtitle: 'Top eventos y voluntarios por engagement',
                icon: Icons.star,
                iconColor: const Color(0xFFFFC107),
                gradientColors: [
                  const Color(0xFFFFC107),
                  const Color(0xFFE0A800),
                ],
                previewContent: _buildRankingsPreview(),
                fullContentBuilder: () => _buildRankingsSection(),
              ),

              // 4. Participación y Actividad
              _buildDashboardBlock(
                key: 'participacion',
                title: 'Participación y Actividad',
                subtitle: 'Actividad reciente y listado de eventos',
                icon: Icons.people,
                iconColor: const Color(0xFF17A2B8),
                gradientColors: [
                  const Color(0xFF17A2B8),
                  const Color(0xFF138496),
                ],
                previewContent: _buildParticipacionPreview(),
                fullContentBuilder: () => _buildParticipacionSection(),
              ),

              // 5. Alertas e Insights
              if (_dashboardData!.alertas != null &&
                  _dashboardData!.alertas!.isNotEmpty)
                _buildDashboardBlock(
                  key: 'alertas',
                  title: 'Alertas e Insights',
                  subtitle: 'Notificaciones y recomendaciones',
                  icon: Icons.notifications_active,
                  iconColor: const Color(0xFFDC3545),
                  gradientColors: [
                    const Color(0xFFDC3545),
                    const Color(0xFFC82333),
                  ],
                  previewContent: _buildAlertsPreview(_dashboardData!.alertas!),
                  fullContentBuilder:
                      () => _buildAlertsSection(_dashboardData!.alertas!),
                ),

              // 6. Reportes
              _buildDashboardBlock(
                key: 'reportes',
                title: 'Reportes',
                subtitle: 'Exportar datos en PDF o Excel',
                icon: Icons.description,
                iconColor: const Color(0xFF6C757D),
                gradientColors: [
                  const Color(0xFF6C757D),
                  const Color(0xFF5A6268),
                ],
                previewContent: _buildReportesPreview(),
                fullContentBuilder: () => _buildReportesSection(),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildDashboardBlock({
    required String key,
    required String title,
    required String subtitle,
    required IconData icon,
    required Color iconColor,
    required List<Color> gradientColors,
    required Widget previewContent,
    required Widget Function() fullContentBuilder,
  }) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: Duration(milliseconds: 300 + (key.hashCode % 200)),
      curve: Curves.easeOut,
      builder: (context, animValue, child) {
        return Opacity(
          opacity: animValue,
          child: Transform.translate(
            offset: Offset(0, 30 * (1 - animValue)),
            child: Transform.scale(
              scale: 0.95 + (0.05 * animValue),
              child: child,
            ),
          ),
        );
      },
      child: MouseRegion(
        cursor: SystemMouseCursors.click,
        child: Card(
          elevation: 6,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
          ),
          child: InkWell(
            onTap: () {
              _navigateToSection(
                key,
                title,
                gradientColors,
                fullContentBuilder,
              );
            },
            borderRadius: BorderRadius.circular(24),
            child: Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: gradientColors[0].withOpacity(0.2),
                    blurRadius: 20,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header con gradiente
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: gradientColors,
                      ),
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(24),
                        topRight: Radius.circular(24),
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Icon(icon, color: Colors.white, size: 28),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                title,
                                style: const TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                  letterSpacing: -0.5,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                subtitle,
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.white.withOpacity(0.9),
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                        Icon(
                          Icons.arrow_forward_ios,
                          color: Colors.white.withOpacity(0.8),
                          size: 18,
                        ),
                      ],
                    ),
                  ),
                  // Preview del contenido
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.only(
                        bottomLeft: Radius.circular(24),
                        bottomRight: Radius.circular(24),
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        previewContent,
                        const SizedBox(height: 12),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            Text(
                              'Ver más',
                              style: TextStyle(
                                fontSize: 12,
                                color: gradientColors[0],
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            const SizedBox(width: 4),
                            Icon(
                              Icons.arrow_forward,
                              size: 14,
                              color: gradientColors[0],
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  void _navigateToSection(
    String key,
    String title,
    List<Color> gradientColors,
    Widget Function() contentBuilder,
  ) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder:
            (context) => _SectionDetailScreen(
              title: title,
              gradientColors: gradientColors,
              content: contentBuilder(),
            ),
      ),
    );
  }

  // Previews compactos para los cards
  Widget _buildResumenPreview() {
    final metricas = _dashboardData!.metricas;
    if (metricas == null) {
      return const Text(
        'No hay datos disponibles',
        style: TextStyle(fontSize: 12, color: Colors.grey),
      );
    }

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceAround,
      children: [
        _buildPreviewMetric(
          'Eventos Activos',
          metricas.eventosActivos.toString(),
          Colors.green,
        ),
        _buildPreviewMetric(
          'Total Participantes',
          metricas.totalParticipantes.toString(),
          Colors.blue,
        ),
        _buildPreviewMetric(
          'Total Reacciones',
          metricas.totalReacciones.toString(),
          Colors.red,
        ),
      ],
    );
  }

  Widget _buildPreviewMetric(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(fontSize: 10, color: Colors.grey[600]),
          textAlign: TextAlign.center,
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
        ),
      ],
    );
  }

  Widget _buildGraficosPreview() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            _buildPreviewChip('Tendencias', Icons.trending_up, Colors.blue),
            _buildPreviewChip('Comparativas', Icons.bar_chart, Colors.green),
            _buildPreviewChip('Radar', Icons.radar, Colors.orange),
          ],
        ),
        const SizedBox(height: 8),
        Text(
          'Gráficos disponibles: ${_countGraficos()}',
          style: TextStyle(fontSize: 11, color: Colors.grey[600]),
        ),
      ],
    );
  }

  Widget _buildPreviewChip(String label, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              color: color,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  int _countGraficos() {
    int count = 0;
    if (_dashboardData!.tendenciasMensuales != null &&
        _dashboardData!.tendenciasMensuales!.isNotEmpty)
      count++;
    if (_dashboardData!.distribucionEstados != null &&
        _dashboardData!.distribucionEstados!.isNotEmpty)
      count++;
    if (_dashboardData!.comparativaEventos != null &&
        _dashboardData!.comparativaEventos!.isNotEmpty)
      count++;
    if (_dashboardData!.actividadSemanal != null &&
        _dashboardData!.actividadSemanal!.isNotEmpty)
      count++;
    if (_dashboardData!.metricasRadar != null &&
        _dashboardData!.metricasRadar!.isNotEmpty)
      count++;
    return count;
  }

  Widget _buildRankingsPreview() {
    final topEventos = _dashboardData!.topEventos?.length ?? 0;
    final topVoluntarios = _dashboardData!.topVoluntarios?.length ?? 0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            _buildPreviewStat(
              'Top Eventos',
              topEventos.toString(),
              Icons.star,
              Colors.amber,
            ),
            _buildPreviewStat(
              'Top Voluntarios',
              topVoluntarios.toString(),
              Icons.people,
              Colors.blue,
            ),
          ],
        ),
        if (topEventos > 0 && _dashboardData!.topEventos != null) ...[
          const SizedBox(height: 8),
          Text(
            '🏆 ${_dashboardData!.topEventos![0].titulo}',
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ],
    );
  }

  Widget _buildPreviewStat(
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    return Column(
      children: [
        Icon(icon, size: 24, color: color),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        Text(label, style: TextStyle(fontSize: 10, color: Colors.grey[600])),
      ],
    );
  }

  Widget _buildParticipacionPreview() {
    final eventosCount = _dashboardData!.listadoEventos?.length ?? 0;
    final actividadCount = _dashboardData!.actividadReciente?.length ?? 0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            _buildPreviewStat(
              'Eventos',
              eventosCount.toString(),
              Icons.event,
              Colors.green,
            ),
            _buildPreviewStat(
              'Días Activos',
              actividadCount.toString(),
              Icons.calendar_today,
              Colors.blue,
            ),
          ],
        ),
        if (actividadCount > 0) ...[
          const SizedBox(height: 8),
          Text(
            'Última actividad: ${_getLastActivityDate()}',
            style: TextStyle(fontSize: 10, color: Colors.grey[600]),
          ),
        ],
      ],
    );
  }

  String _getLastActivityDate() {
    if (_dashboardData!.actividadReciente == null ||
        _dashboardData!.actividadReciente!.isEmpty) {
      return 'N/A';
    }
    final lastEntry = _dashboardData!.actividadReciente!.entries.first;
    try {
      final date = DateTime.parse(lastEntry.key);
      return DateFormat('dd/MM').format(date);
    } catch (e) {
      return 'N/A';
    }
  }

  Widget _buildAlertsPreview(List<Alerta> alertas) {
    final urgentes = alertas.where((a) => a.severidad == 'danger').length;
    final advertencias = alertas.where((a) => a.severidad == 'warning').length;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            if (urgentes > 0)
              _buildAlertBadge('Urgentes', urgentes.toString(), Colors.red),
            if (advertencias > 0)
              _buildAlertBadge(
                'Advertencias',
                advertencias.toString(),
                Colors.orange,
              ),
            if (urgentes == 0 && advertencias == 0)
              _buildAlertBadge('Total', alertas.length.toString(), Colors.blue),
          ],
        ),
        if (alertas.isNotEmpty) ...[
          const SizedBox(height: 8),
          Text(
            alertas[0].mensaje,
            style: const TextStyle(fontSize: 11),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ],
    );
  }

  Widget _buildAlertBadge(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(label, style: TextStyle(fontSize: 9, color: color)),
        ],
      ),
    );
  }

  Widget _buildReportesPreview() {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            _buildReportOption(Icons.picture_as_pdf, 'PDF', Colors.red),
            _buildReportOption(Icons.table_chart, 'Excel', Colors.green),
          ],
        ),
        const SizedBox(height: 8),
        Text(
          'Exporta reportes completos con gráficos',
          style: TextStyle(fontSize: 10, color: Colors.grey[600]),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  Widget _buildReportOption(IconData icon, String label, Color color) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, size: 24, color: color),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(
            fontSize: 10,
            color: color,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildResumenSection() {
    final metricas = _dashboardData!.metricas;
    if (metricas == null) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(32),
          child: Text('No hay métricas disponibles'),
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        MetricGrid(
          crossAxisCount: 2,
          metrics: [
            MetricCard(
              label: 'Eventos Activos',
              value: metricas.eventosActivos.toString(),
              icon: Icons.event_available,
              color: Colors.green,
            ),
            MetricCard(
              label: 'Eventos Inactivos',
              value: metricas.eventosInactivos.toString(),
              icon: Icons.event_busy,
              color: Colors.orange,
            ),
            MetricCard(
              label: 'Eventos Finalizados',
              value: metricas.eventosFinalizados.toString(),
              icon: Icons.event_note,
              color: Colors.grey,
            ),
            MetricCard(
              label: 'Total Reacciones',
              value: metricas.totalReacciones.toString(),
              icon: Icons.favorite,
              color: Colors.red,
            ),
            MetricCard(
              label: 'Total Compartidos',
              value: metricas.totalCompartidos.toString(),
              icon: Icons.share,
              color: Colors.purple,
            ),
            MetricCard(
              label: 'Total Voluntarios',
              value: metricas.totalVoluntarios.toString(),
              icon: Icons.volunteer_activism,
              color: Colors.blue,
            ),
            MetricCard(
              label: 'Total Participantes',
              value: metricas.totalParticipantes.toString(),
              icon: Icons.people,
              color: Colors.teal,
            ),
            MetricCard(
              label: 'Promedio/Evento',
              value:
                  (metricas.eventosActivos +
                              metricas.eventosInactivos +
                              metricas.eventosFinalizados) >
                          0
                      ? (metricas.totalParticipantes /
                              (metricas.eventosActivos +
                                  metricas.eventosInactivos +
                                  metricas.eventosFinalizados))
                          .toStringAsFixed(1)
                      : '0',
              icon: Icons.analytics,
              color: Colors.indigo,
            ),
          ],
        ),
        // Comparativas con período anterior (compacto)
        if (_dashboardData!.comparativas != null &&
            _dashboardData!.comparativas!.isNotEmpty) ...[
          const SizedBox(height: 16),
          const Text(
            'Comparativa Período Anterior',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          ..._dashboardData!.comparativas!.entries.take(3).map((entry) {
            final comparativa = entry.value;
            return Card(
              margin: const EdgeInsets.only(bottom: 8),
              child: ListTile(
                title: Text(
                  entry.key[0].toUpperCase() + entry.key.substring(1),
                ),
                subtitle: Text(
                  'Actual: ${comparativa.actual} | Anterior: ${comparativa.anterior}',
                ),
                trailing: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      comparativa.tendencia == 'up'
                          ? Icons.trending_up
                          : comparativa.tendencia == 'down'
                          ? Icons.trending_down
                          : Icons.trending_flat,
                      color:
                          comparativa.tendencia == 'up'
                              ? Colors.green
                              : comparativa.tendencia == 'down'
                              ? Colors.red
                              : Colors.grey,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      '${comparativa.crecimiento > 0 ? '+' : ''}${comparativa.crecimiento.toStringAsFixed(1)}%',
                      style: TextStyle(
                        color:
                            comparativa.tendencia == 'up'
                                ? Colors.green
                                : comparativa.tendencia == 'down'
                                ? Colors.red
                                : Colors.grey,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
            );
          }).toList(),
        ],
      ],
    );
  }

  Widget _buildGraficosSection() {
    return Column(
      children: [
        // Tendencias mensuales (compacto)
        if (_dashboardData!.tendenciasMensuales != null &&
            _dashboardData!.tendenciasMensuales!.isNotEmpty)
          SizedBox(
            height: 200,
            child: LineChartWidget(
              title: 'Tendencias Mensuales',
              subtitle: 'Participantes por mes',
              data: _dashboardData!.tendenciasMensuales!,
              lineColor: Colors.blue,
            ),
          ),
        const SizedBox(height: 12),

        // Distribución de estados (compacto)
        if (_dashboardData!.distribucionEstados != null &&
            _dashboardData!.distribucionEstados!.isNotEmpty)
          SizedBox(
            height: 200,
            child: PieChartWidget(
              title: 'Distribución de Estados',
              subtitle: 'Eventos según su estado',
              data: _dashboardData!.distribucionEstados!,
              colors: [Colors.green, Colors.orange, Colors.grey, Colors.red],
            ),
          ),
        const SizedBox(height: 12),

        // Comparativa de rendimiento entre eventos (compacto)
        if (_dashboardData!.comparativaEventos != null &&
            _dashboardData!.comparativaEventos!.isNotEmpty)
          SizedBox(
            height: 200,
            child: GroupedBarChartWidget(
              title: 'Comparativa de Rendimiento entre Eventos',
              subtitle: 'Reacciones, compartidos y participantes por evento',
              data:
                  _dashboardData!.comparativaEventos!.take(10).map((e) {
                    return GroupedBarData(
                      label:
                          e.titulo.length > 15
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
              colors: [Colors.red, Colors.green, Colors.blue],
            ),
          ),

        // Actividad semanal
        if (_dashboardData!.actividadSemanal != null &&
            _dashboardData!.actividadSemanal!.isNotEmpty)
          AreaChartWidget(
            title: 'Actividad Semanal Agregada',
            subtitle: 'Total de interacciones por semana',
            data: _dashboardData!.actividadSemanal!,
            areaColor: Colors.purple,
            borderColor: Colors.purple,
          ),
        const SizedBox(height: 16),

        // Métricas radar
        if (_dashboardData!.metricasRadar != null &&
            _dashboardData!.metricasRadar!.isNotEmpty)
          RadarChartWidget(
            title: 'Métricas Generales',
            subtitle: 'Vista general del rendimiento',
            data: _dashboardData!.metricasRadar!,
            fillColor: Colors.teal,
          ),
      ],
    );
  }

  Widget _buildTopEventosList() {
    if (_dashboardData!.topEventos == null ||
        _dashboardData!.topEventos!.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(32),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.event_busy, size: 48, color: Colors.grey[400]),
              const SizedBox(height: 8),
              Text(
                'No hay eventos disponibles',
                style: TextStyle(color: Colors.grey[600], fontSize: 14),
              ),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      itemCount:
          _dashboardData!.topEventos!.length > 5
              ? 5
              : _dashboardData!.topEventos!.length,
      itemBuilder: (context, index) {
        final evento = _dashboardData!.topEventos![index];
        return Padding(
          padding: const EdgeInsets.only(bottom: 8),
          child: _buildEventoCardCompact(evento, index),
        );
      },
    );
  }

  Widget _buildTopVoluntariosList() {
    if (_dashboardData!.topVoluntarios == null ||
        _dashboardData!.topVoluntarios!.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(32),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.people_outline, size: 48, color: Colors.grey[400]),
              const SizedBox(height: 8),
              Text(
                'No hay voluntarios disponibles',
                style: TextStyle(color: Colors.grey[600], fontSize: 14),
              ),
            ],
          ),
        ),
      );
    }

    return Column(
      children:
          _dashboardData!.topVoluntarios!.asMap().entries.map((entry) {
            final index = entry.key;
            final voluntario = entry.value;
            return TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: Duration(milliseconds: 200 + (index * 30)),
              curve: Curves.easeOut,
              builder: (context, value, child) {
                return Opacity(
                  opacity: value,
                  child: Transform.translate(
                    offset: Offset(0, 15 * (1 - value)),
                    child: child,
                  ),
                );
              },
              child: Card(
                margin: const EdgeInsets.only(bottom: 12),
                elevation: 1,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: _getRankColor(index),
                    child: Text(
                      '${index + 1}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  title: Text(
                    voluntario.nombre,
                    style: const TextStyle(fontWeight: FontWeight.bold),
                  ),
                  subtitle: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (voluntario.email != null)
                        Text(
                          voluntario.email!,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          _buildMetricChip(
                            'Eventos',
                            voluntario.eventosParticipados,
                            Colors.blue,
                          ),
                          if (voluntario.horasContribuidas != null) ...[
                            const SizedBox(width: 4),
                            _buildMetricChip(
                              'Horas',
                              voluntario.horasContribuidas!,
                              Colors.orange,
                            ),
                          ],
                        ],
                      ),
                    ],
                  ),
                  trailing: Icon(
                    Icons.volunteer_activism,
                    color: Colors.orange[700],
                  ),
                ),
              ),
            );
          }).toList(),
    );
  }

  Widget _buildEventoCardCompact(TopEvento evento, int index) {
    return Card(
      margin: EdgeInsets.zero,
      elevation: 1,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder:
                  (context) => EventoDetailScreen(eventoId: evento.eventoId),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: _getRankColor(index),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Center(
                  child: Text(
                    '${index + 1}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      evento.titulo,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.star, size: 14, color: Colors.amber),
                        const SizedBox(width: 4),
                        Text(
                          'Engagement: ${evento.engagement}',
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  _buildCompactMetric(
                    Icons.favorite,
                    evento.reacciones.toString(),
                    Colors.red,
                  ),
                  const SizedBox(width: 6),
                  _buildCompactMetric(
                    Icons.share,
                    evento.compartidos.toString(),
                    Colors.green,
                  ),
                  const SizedBox(width: 6),
                  _buildCompactMetric(
                    Icons.people,
                    evento.inscripciones.toString(),
                    Colors.blue,
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCompactMetric(IconData icon, String value, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 3),
          Text(
            value,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEventoCard(TopEvento evento, int index) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder:
                  (context) => EventoDetailScreen(eventoId: evento.eventoId),
            ),
          );
        },
        borderRadius: BorderRadius.circular(16),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.white, _getRankColor(index).withOpacity(0.03)],
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Ranking + Título
              Row(
                children: [
                  // Badge de ranking
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: _getRankColor(index),
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: [
                        BoxShadow(
                          color: _getRankColor(index).withOpacity(0.3),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Center(
                      child: Text(
                        '${index + 1}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 18,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  // Título del evento
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          evento.titulo,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        if (evento.fechaInicio != null) ...[
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              Icon(
                                Icons.calendar_today,
                                size: 14,
                                color: Colors.grey[600],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                _formatDate(evento.fechaInicio!),
                                style: TextStyle(
                                  fontSize: 13,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              // Engagement destacado
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: Colors.amber.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: Colors.amber.withOpacity(0.3),
                    width: 1,
                  ),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.star, color: Colors.amber, size: 20),
                    const SizedBox(width: 6),
                    Text(
                      'Engagement: ${evento.engagement}',
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: Colors.amber,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              // Métricas: Reacciones, Compartidos, Inscripciones
              Row(
                children: [
                  Expanded(
                    child: _buildMetricItem(
                      Icons.favorite,
                      evento.reacciones.toString(),
                      'Reacciones',
                      Colors.red,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: _buildMetricItem(
                      Icons.share,
                      evento.compartidos.toString(),
                      'Compartidos',
                      Colors.green,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: _buildMetricItem(
                      Icons.people,
                      evento.inscripciones.toString(),
                      'Inscritos',
                      Colors.blue,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              // Footer: Estado + Ubicación
              Row(
                children: [
                  // Estado del evento
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: _getEstadoColor(evento.estado).withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 8,
                          height: 8,
                          decoration: BoxDecoration(
                            color: _getEstadoColor(evento.estado),
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 6),
                        Text(
                          evento.estado?.toUpperCase() ?? 'N/A',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: _getEstadoColor(evento.estado),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const Spacer(),
                  // Ubicación (si existe)
                  if (evento.ubicacion != null)
                    Flexible(
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.location_on,
                            size: 14,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Flexible(
                            child: Text(
                              evento.ubicacion!,
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMetricItem(
    IconData icon,
    String value,
    String label,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withOpacity(0.2), width: 1),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(height: 4),
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            label,
            style: TextStyle(fontSize: 10, color: Colors.grey[600]),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildParticipacionSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Actividad reciente (compacto - últimos 7 días)
        if (_dashboardData!.actividadReciente != null &&
            _dashboardData!.actividadReciente!.isNotEmpty) ...[
          Row(
            children: [
              Icon(Icons.timeline, size: 16, color: Colors.blue[700]),
              const SizedBox(width: 6),
              const Text(
                'Actividad Reciente',
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
              ),
            ],
          ),
          const SizedBox(height: 8),
          SizedBox(
            height: 150,
            child: ListView.builder(
              shrinkWrap: true,
              itemCount:
                  _dashboardData!.actividadReciente!.entries.length > 7
                      ? 7
                      : _dashboardData!.actividadReciente!.entries.length,
              itemBuilder: (context, index) {
                final entry =
                    _dashboardData!.actividadReciente!.entries.toList()[index];
                final fecha = entry.key;
                final actividad = entry.value;
                return Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(
                          _formatFecha(fecha),
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.grey[600],
                          ),
                        ),
                      ),
                      _buildActividadChip(
                        '❤️',
                        actividad.reacciones,
                        Colors.red,
                      ),
                      const SizedBox(width: 4),
                      _buildActividadChip(
                        '🔁',
                        actividad.compartidos,
                        Colors.green,
                      ),
                      const SizedBox(width: 4),
                      _buildActividadChip(
                        '👥',
                        actividad.inscripciones,
                        Colors.blue,
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
        const SizedBox(height: 16),
        // Listado de eventos (compacto - top 5)
        if (_dashboardData!.listadoEventos != null &&
            _dashboardData!.listadoEventos!.isNotEmpty) ...[
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Icon(Icons.event, size: 16, color: Colors.green[700]),
                  const SizedBox(width: 6),
                  const Text(
                    'Eventos Recientes',
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              TextButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => const EventosOngScreen(),
                    ),
                  );
                },
                child: const Text('Ver todos', style: TextStyle(fontSize: 12)),
              ),
            ],
          ),
          const SizedBox(height: 8),
          SizedBox(
            height: 200,
            child: ListView.builder(
              shrinkWrap: true,
              itemCount:
                  _dashboardData!.listadoEventos!.length > 5
                      ? 5
                      : _dashboardData!.listadoEventos!.length,
              itemBuilder: (context, index) {
                final evento = _dashboardData!.listadoEventos![index];
                return Card(
                  margin: const EdgeInsets.only(bottom: 6),
                  elevation: 1,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: ListTile(
                    dense: true,
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 4,
                    ),
                    leading: Icon(
                      evento.tipo == 'mega_evento'
                          ? Icons.event_note
                          : Icons.event,
                      color: _getEstadoColor(evento.estado),
                      size: 20,
                    ),
                    title: Text(
                      evento.titulo,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    subtitle: Text(
                      '${evento.totalParticipantes} participantes',
                      style: TextStyle(fontSize: 11, color: Colors.grey[600]),
                    ),
                    trailing: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: _getEstadoColor(evento.estado).withOpacity(0.15),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        evento.estado ?? 'N/A',
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: _getEstadoColor(evento.estado),
                        ),
                      ),
                    ),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder:
                              (context) =>
                                  EventoDetailScreen(eventoId: evento.id),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildRankingsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Top Eventos (compacto)
        Row(
          children: [
            Icon(Icons.star, size: 18, color: Colors.amber[700]),
            const SizedBox(width: 6),
            const Text(
              'Top Eventos',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
          ],
        ),
        const SizedBox(height: 12),
        SizedBox(height: 200, child: _buildTopEventosList()),
        const SizedBox(height: 16),
        // Top Voluntarios (compacto)
        Row(
          children: [
            Icon(Icons.people, size: 18, color: Colors.blue[700]),
            const SizedBox(width: 6),
            const Text(
              'Top Voluntarios',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
          ],
        ),
        const SizedBox(height: 12),
        SizedBox(height: 200, child: _buildTopVoluntariosList()),
      ],
    );
  }

  Widget _buildReportesSection() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // PDF
        Card(
          elevation: 3,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          child: InkWell(
            onTap: _exportarPdf,
            borderRadius: BorderRadius.circular(16),
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Colors.red.withOpacity(0.1),
                    Colors.red.withOpacity(0.05),
                  ],
                ),
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.red.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Icon(
                      Icons.picture_as_pdf,
                      size: 48,
                      color: Colors.red[700],
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Exportar PDF',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Reporte completo con gráficos',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          ),
        ),
        const SizedBox(height: 16),
        // Excel
        Card(
          elevation: 3,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          child: InkWell(
            onTap: _exportarExcel,
            borderRadius: BorderRadius.circular(16),
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Colors.green.withOpacity(0.1),
                    Colors.green.withOpacity(0.05),
                  ],
                ),
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.green.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Icon(
                      Icons.table_chart,
                      size: 48,
                      color: Colors.green[700],
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Exportar Excel',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Datos en formato de hoja de cálculo',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildMetricChip(String label, int value, Color color) {
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

  Widget _buildActividadChip(String label, int value, Color color) {
    // Si el label es un emoji, mostrar solo el emoji y el valor
    final isEmoji = label.length <= 2 && label.runes.length == 1;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.3), width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (isEmoji)
            Text(label, style: const TextStyle(fontSize: 12))
          else
            Icon(Icons.circle, size: 8, color: color),
          const SizedBox(width: 4),
          Text(
            value.toString(),
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          if (!isEmoji) ...[
            const SizedBox(width: 4),
            Text(label, style: TextStyle(fontSize: 10, color: color)),
          ],
        ],
      ),
    );
  }

  Widget _buildAlertsSection(List<Alerta> alertas) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: 300,
          child: ListView.builder(
            shrinkWrap: true,
            itemCount: alertas.length > 5 ? 5 : alertas.length,
            itemBuilder: (context, index) {
              final alerta = alertas[index];
              return Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Card(
                  elevation: 2,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: BorderSide(
                      color: alerta.color.withOpacity(0.3),
                      width: 1,
                    ),
                  ),
                  color: alerta.color.withOpacity(0.05),
                  child: InkWell(
                    onTap: () {
                      if (alerta.eventoId != null) {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder:
                                (context) => EventoDetailScreen(
                                  eventoId: alerta.eventoId!,
                                ),
                          ),
                        );
                      }
                    },
                    borderRadius: BorderRadius.circular(12),
                    child: Padding(
                      padding: const EdgeInsets.all(12),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: alerta.color.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Icon(
                              alerta.icon,
                              color: alerta.color,
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  _getSeverityLabel(alerta.severidad),
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                    color: alerta.color,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  alerta.mensaje,
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: Colors.black87,
                                  ),
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
                              size: 20,
                            ),
                        ],
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
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

  Color _getRankColor(int index) {
    switch (index) {
      case 0:
        return const Color(0xFFFFB800); // Amber más suave
      case 1:
        return const Color(0xFF9E9E9E); // Gris más suave
      case 2:
        return const Color(0xFF8D6E63); // Brown más suave
      default:
        return const Color(0xFF42A5F5); // Blue más suave
    }
  }

  Color _getEstadoColor(String? estado) {
    switch (estado?.toLowerCase()) {
      case 'activo':
      case 'publicado':
        return Colors.green;
      case 'inactivo':
      case 'borrador':
        return Colors.orange;
      case 'finalizado':
        return Colors.grey;
      default:
        return Colors.blue;
    }
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy').format(date);
    } catch (e) {
      return dateStr;
    }
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

// Pantalla de detalle de sección
class _SectionDetailScreen extends StatelessWidget {
  final String title;
  final List<Color> gradientColors;
  final Widget content;

  const _SectionDetailScreen({
    required this.title,
    required this.gradientColors,
    required this.content,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          // AppBar con gradiente
          SliverAppBar(
            expandedHeight: 120,
            pinned: true,
            elevation: 0,
            backgroundColor: gradientColors[0],
            leading: IconButton(
              icon: const Icon(Icons.arrow_back, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
            flexibleSpace: FlexibleSpaceBar(
              title: Text(
                title,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: gradientColors,
                  ),
                ),
              ),
            ),
          ),
          // Contenido
          SliverToBoxAdapter(
            child: Container(padding: const EdgeInsets.all(20), child: content),
          ),
        ],
      ),
    );
  }
}
