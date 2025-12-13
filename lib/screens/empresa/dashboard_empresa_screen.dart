import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/metrics/metric_card.dart';
import '../../widgets/charts/pie_chart_widget.dart';
import '../../widgets/charts/bar_chart_widget.dart';

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
        title: const Text('Dashboard Empresa'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDatos,
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
                      onPressed: _loadDatos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _datos == null
              ? const Center(child: Text('No hay datos disponibles'))
              : SingleChildScrollView(
                child: Column(
                  children: [
                    // 1. Resumen General
                    _buildCollapsibleSection(
                      key: 'resumen',
                      title: '📊 Resumen General',
                      subtitle: 'KPIs principales y métricas de patrocinios',
                      icon: Icons.dashboard,
                      child: _buildResumenSection(),
                    ),
                    // 2. Impacto Social
                    _buildCollapsibleSection(
                      key: 'impacto',
                      title: '📈 Impacto Social',
                      subtitle: 'Análisis de alcance y categorías',
                      icon: Icons.trending_up,
                      child: _buildImpactoSection(),
                    ),
                    // 3. Eventos Patrocinados
                    _buildCollapsibleSection(
                      key: 'eventos',
                      title: '🎯 Eventos Patrocinados',
                      subtitle: 'Listado completo de eventos',
                      icon: Icons.event,
                      child: _buildEventosSection(),
                    ),
                    const SizedBox(height: 24),
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

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: ExpansionTile(
        key: ValueKey(key),
        initiallyExpanded: isExpanded,
        onExpansionChanged: (expanded) {
          setState(() {
            _expandedSections[key] = expanded;
          });
        },
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Theme.of(context).primaryColor.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: Theme.of(context).primaryColor),
        ),
        title: Text(
          title,
          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        subtitle: Text(
          subtitle,
          style: TextStyle(fontSize: 13, color: Colors.grey[600]),
        ),
        children: [Padding(padding: const EdgeInsets.all(16), child: child)],
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
              color: Colors.blue,
            ),
            MetricCard(
              label: 'Total Participantes',
              value: totalParticipantes.toString(),
              icon: Icons.people,
              color: Colors.green,
            ),
            MetricCard(
              label: 'Total Reacciones',
              value: totalReacciones.toString(),
              icon: Icons.favorite,
              color: Colors.red,
            ),
            MetricCard(
              label: 'Total Compartidos',
              value: totalCompartidos.toString(),
              icon: Icons.share,
              color: Colors.purple,
            ),
            MetricCard(
              label: 'Promedio Participantes',
              value:
                  totalEventos > 0
                      ? (totalParticipantes / totalEventos).toStringAsFixed(1)
                      : '0',
              icon: Icons.analytics,
              color: Colors.orange,
            ),
            MetricCard(
              label: 'Alcance Total',
              value:
                  (totalParticipantes + totalReacciones + totalCompartidos)
                      .toString(),
              icon: Icons.trending_up,
              color: Colors.teal,
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildImpactoSection() {
    final eventosPatrocinados = _datos?['eventos_patrocinados'] as List? ?? [];

    if (eventosPatrocinados.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(32),
        child: Center(child: Text('No hay datos de impacto disponibles')),
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
          barColor: Colors.green,
        ),
        const SizedBox(height: 16),
        Card(
          elevation: 2,
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Icon(Icons.stars, color: Colors.amber[700]),
                    const SizedBox(width: 8),
                    const Text(
                      'Impacto Social',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(
                  'Tu empresa ha contribuido al impacto social patrocinando '
                  '${eventosPatrocinados.length} eventos, llegando a '
                  '${participantesPorCategoria.values.fold(0, (a, b) => a + b)} '
                  'participantes en diferentes categorías de eventos.',
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.grey[700],
                    height: 1.5,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildEventosSection() {
    final eventosPatrocinados = _datos?['eventos_patrocinados'] as List? ?? [];

    if (eventosPatrocinados.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(32),
        child: Center(child: Text('No has patrocinado ningún evento aún')),
      );
    }

    return Column(
      children:
          eventosPatrocinados.map((evento) {
            return Card(
              margin: const EdgeInsets.only(bottom: 12),
              elevation: 1,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              child: ListTile(
                leading: CircleAvatar(
                  backgroundColor: Colors.blue[100],
                  child: const Icon(Icons.event, color: Colors.blue),
                ),
                title: Text(
                  evento['titulo'] ?? 'Sin título',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                subtitle: Text(
                  '${evento['total_participantes'] ?? 0} participantes • '
                  '${evento['total_reacciones'] ?? 0} reacciones',
                ),
                trailing: Chip(
                  label: Text(evento['estado'] ?? 'Activo'),
                  backgroundColor: _getEstadoColor(evento['estado']),
                ),
              ),
            );
          }).toList(),
    );
  }

  Color _getEstadoColor(String? estado) {
    switch (estado?.toLowerCase()) {
      case 'activo':
      case 'publicado':
        return Colors.green.withOpacity(0.2);
      case 'finalizado':
        return Colors.blue.withOpacity(0.2);
      case 'cancelado':
        return Colors.red.withOpacity(0.2);
      default:
        return Colors.grey.withOpacity(0.2);
    }
  }
}
