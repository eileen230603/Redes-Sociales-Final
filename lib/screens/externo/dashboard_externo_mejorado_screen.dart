import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'dart:io';
import 'package:universal_html/html.dart' as html;
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/line_chart_widget.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/bar_chart_widget.dart';
import '../../widgets/charts/multi_line_chart_widget.dart';
import '../../services/cache_service.dart';
import '../../models/evento.dart';
import '../evento_detail_screen.dart';

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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Mi Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(useCache: false),
            tooltip: 'Actualizar',
          ),
        ],
        bottom:
            !_isLoading && _error == null
                ? TabBar(
                  controller: _tabController,
                  tabs: const [
                    Tab(text: 'Resumen', icon: Icon(Icons.dashboard, size: 20)),
                    Tab(text: 'Mis Eventos', icon: Icon(Icons.event, size: 20)),
                    Tab(
                      text: 'Actividad',
                      icon: Icon(Icons.timeline, size: 20),
                    ),
                  ],
                )
                : null,
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
                      onPressed: _loadData,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
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
      return const Center(child: Text('No hay estadísticas disponibles'));
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
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Información del usuario
          if (_usuario != null) _buildUserInfo(),
          const SizedBox(height: 24),

          // Métricas principales
          const Text(
            'Mis Estadísticas',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 16),

          MetricGrid(
            crossAxisCount: 2,
            metrics: [
              MetricCard(
                label: 'Eventos Inscritos',
                value: eventosInscritos.toString(),
                icon: Icons.event_available,
                color: Colors.blue,
              ),
              MetricCard(
                label: 'Eventos Asistidos',
                value: eventosAsistidos.toString(),
                icon: Icons.check_circle,
                color: Colors.green,
              ),
              MetricCard(
                label: 'Reacciones',
                value: reaccionesTotales.toString(),
                icon: Icons.favorite,
                color: Colors.red,
              ),
              MetricCard(
                label: 'Compartidos',
                value: compartidosTotales.toString(),
                icon: Icons.share,
                color: Colors.purple,
              ),
              MetricCard(
                label: 'Mega Eventos',
                value: megaEventosInscritos.toString(),
                icon: Icons.event_note,
                color: Colors.purple,
              ),
              MetricCard(
                label: 'Horas Acumuladas',
                value: horasAcumuladas.toString(),
                icon: Icons.access_time,
                color: Colors.indigo,
              ),
              MetricCard(
                label: 'Tasa Asistencia',
                value:
                    eventosInscritos > 0
                        ? '${((eventosAsistidos / eventosInscritos) * 100).toStringAsFixed(0)}%'
                        : '0%',
                icon: Icons.analytics,
                color: Colors.teal,
              ),
            ],
          ),

          const SizedBox(height: 24),

          // Tasa de asistencia
          Card(
            elevation: 2,
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  const Text(
                    'Tasa de Asistencia',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
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
                          backgroundColor: Colors.grey[300],
                          valueColor: const AlwaysStoppedAnimation<Color>(
                            Colors.green,
                          ),
                        ),
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              eventosInscritos > 0
                                  ? '${((eventosAsistidos / eventosInscritos) * 100).toStringAsFixed(1)}%'
                                  : '0%',
                              style: const TextStyle(
                                fontSize: 32,
                                fontWeight: FontWeight.bold,
                                color: Colors.green,
                              ),
                            ),
                            const Text(
                              'Asistencia',
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    '$eventosAsistidos de $eventosInscritos eventos asistidos',
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEventosTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
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
              colors: [Colors.green, Colors.orange, Colors.blue, Colors.grey],
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
                  color: Colors.blue,
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
                  color: Colors.green,
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
            const Text(
              'Mis Eventos Favoritos',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            ...(_graficas!['eventos_interacciones'] as List).take(5).map((e) {
              final evento = e as Map;
              return Card(
                margin: const EdgeInsets.only(bottom: 8),
                child: ListTile(
                  leading: const Icon(Icons.favorite, color: Colors.red),
                  title: Text(evento['titulo'] ?? 'Evento'),
                  subtitle: Row(
                    children: [
                      Chip(
                        label: Text('${evento['reacciones'] ?? 0} reacciones'),
                        backgroundColor: Colors.red.withOpacity(0.1),
                      ),
                      const SizedBox(width: 4),
                      Chip(
                        label: Text(
                          '${evento['compartidos'] ?? 0} compartidos',
                        ),
                        backgroundColor: Colors.green.withOpacity(0.1),
                      ),
                    ],
                  ),
                  trailing: Text(
                    '${evento['total'] ?? 0}',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.teal,
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
      return const Center(child: Text('No hay datos de actividad'));
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          // Reacciones por mes
          if (_graficas!['reacciones_por_mes'] != null &&
              (_graficas!['reacciones_por_mes'] as Map).isNotEmpty)
            LineChartWidget(
              title: 'Mis Reacciones por Mes',
              subtitle: 'Interacciones mensuales con eventos',
              data: Map<String, int>.from(_graficas!['reacciones_por_mes']),
              lineColor: Colors.red,
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
              barColor: Colors.blue,
            ),
            const SizedBox(height: 16),
          ],

          // Ubicaciones
          if (_graficas!['ubicaciones'] != null &&
              (_graficas!['ubicaciones'] is List &&
                  (_graficas!['ubicaciones'] as List).isNotEmpty)) ...[
            const Text(
              'Ciudades donde he Participado',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            ...(_graficas!['ubicaciones'] as List).map((ubicacion) {
              final u = ubicacion as Map;
              return Card(
                margin: const EdgeInsets.only(bottom: 8),
                child: ListTile(
                  leading: const Icon(Icons.location_on, color: Colors.red),
                  title: Text(u['ciudad'] ?? 'Ciudad desconocida'),
                  trailing: Chip(
                    label: Text('${u['cantidad'] ?? 0} eventos'),
                    backgroundColor: Colors.blue.withOpacity(0.1),
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

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              radius: 30,
              backgroundColor: Colors.blue[100],
              child: Text(
                (_usuario!['nombres']?[0] ?? 'U').toUpperCase(),
                style: const TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: Colors.blue,
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${_usuario!['nombres'] ?? ''} ${_usuario!['apellidos'] ?? ''}',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _usuario!['correo_electronico'] ?? '',
                    style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
