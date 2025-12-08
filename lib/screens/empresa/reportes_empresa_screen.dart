import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';

class ReportesEmpresaScreen extends StatefulWidget {
  const ReportesEmpresaScreen({super.key});

  @override
  State<ReportesEmpresaScreen> createState() => _ReportesEmpresaScreenState();
}

class _ReportesEmpresaScreenState extends State<ReportesEmpresaScreen> {
  List<Evento> _eventosPatrocinados = [];
  bool _isLoading = true;
  String? _error;
  int? _empresaId;

  @override
  void initState() {
    super.initState();
    _loadEmpresaId();
    _loadDatos();
  }

  Future<void> _loadEmpresaId() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _empresaId = userData?['entity_id'] as int?;
    });
  }

  Future<void> _loadDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventosPublicados();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        final todosEventos = result['eventos'] as List<Evento>;
        // Filtrar eventos donde esta empresa es patrocinadora
        _eventosPatrocinados =
            todosEventos.where((evento) {
              if (_empresaId == null || evento.patrocinadores == null) {
                return false;
              }
              final patrocinadores = evento.patrocinadores as List;
              return patrocinadores.any(
                (p) => p.toString() == _empresaId.toString() || p == _empresaId,
              );
            }).toList();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar datos';
      }
    });
  }

  int get _totalEventos => _eventosPatrocinados.length;
  int get _eventosActivos =>
      _eventosPatrocinados
          .where(
            (e) =>
                e.estado == 'publicado' &&
                e.fechaInicio.isAfter(DateTime.now()),
          )
          .length;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/empresa/reportes'),
      appBar: AppBar(
        title: const Text('Reportes'),
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
              : SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Estadísticas',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: _buildStatCard(
                            'Total Patrocinios',
                            '$_totalEventos',
                            Icons.event_available,
                            Colors.blue,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildStatCard(
                            'Eventos Activos',
                            '$_eventosActivos',
                            Icons.event_available,
                            Colors.green,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 32),
                    const Text(
                      'Resumen de Patrocinios',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 16),
                    if (_totalEventos > 0)
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            children: [
                              _buildProgressBar(
                                'Eventos Activos',
                                _eventosActivos,
                                _totalEventos,
                                Colors.green,
                              ),
                              const SizedBox(height: 16),
                              _buildProgressBar(
                                'Eventos Finalizados',
                                _totalEventos - _eventosActivos,
                                _totalEventos,
                                Colors.orange,
                              ),
                            ],
                          ),
                        ),
                      )
                    else
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(24.0),
                          child: Text(
                            'No hay datos para mostrar',
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ),
                      ),
                  ],
                ),
              ),
    );
  }

  Widget _buildStatCard(
    String title,
    String value,
    IconData icon,
    Color color,
  ) {
    return Card(
      elevation: 2,
      child: Container(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: color, size: 24),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    title,
                    style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              value,
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProgressBar(String label, int value, int total, Color color) {
    final percentage = total > 0 ? (value / total) : 0.0;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label, style: const TextStyle(fontWeight: FontWeight.w500)),
            Text(
              '$value / $total',
              style: TextStyle(color: Colors.grey[600], fontSize: 12),
            ),
          ],
        ),
        const SizedBox(height: 8),
        LinearProgressIndicator(
          value: percentage,
          backgroundColor: Colors.grey[200],
          valueColor: AlwaysStoppedAnimation<Color>(color),
          minHeight: 8,
        ),
        const SizedBox(height: 4),
        Text(
          '${(percentage * 100).toStringAsFixed(1)}%',
          style: TextStyle(fontSize: 12, color: Colors.grey[600]),
        ),
      ],
    );
  }
}
