import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/app_drawer.dart';
import '../models/evento_participacion.dart';

class ReportesScreen extends StatefulWidget {
  const ReportesScreen({super.key});

  @override
  State<ReportesScreen> createState() => _ReportesScreenState();
}

class _ReportesScreenState extends State<ReportesScreen> {
  List<EventoParticipacion> _participaciones = [];
  bool _isLoading = true;
  String? _error;

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

    final result = await ApiService.getMisEventos();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _participaciones =
            result['participaciones'] as List<EventoParticipacion>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar datos';
      }
    });
  }

  int get _totalEventos => _participaciones.length;
  int get _eventosAsistidos => _participaciones.where((p) => p.asistio).length;
  int get _totalPuntos => _participaciones.fold(0, (sum, p) => sum + p.puntos);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/reportes'),
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
                    // Estadísticas generales
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
                            'Total Eventos',
                            '$_totalEventos',
                            Icons.event,
                            Colors.blue,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildStatCard(
                            'Asistidos',
                            '$_eventosAsistidos',
                            Icons.check_circle,
                            Colors.green,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _buildStatCard(
                      'Puntos Totales',
                      '$_totalPuntos',
                      Icons.star,
                      Colors.amber,
                      fullWidth: true,
                    ),
                    const SizedBox(height: 32),
                    // Gráfico de participación
                    const Text(
                      'Resumen de Participación',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: [
                            if (_totalEventos > 0) ...[
                              _buildProgressBar(
                                'Eventos Asistidos',
                                _eventosAsistidos,
                                _totalEventos,
                                Colors.green,
                              ),
                              const SizedBox(height: 16),
                              _buildProgressBar(
                                'Eventos Pendientes',
                                _totalEventos - _eventosAsistidos,
                                _totalEventos,
                                Colors.orange,
                              ),
                            ] else
                              const Padding(
                                padding: EdgeInsets.all(24.0),
                                child: Text(
                                  'No hay datos para mostrar',
                                  style: TextStyle(color: Colors.grey),
                                ),
                              ),
                          ],
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
    Color color, {
    bool fullWidth = false,
  }) {
    return Card(
      elevation: 2,
      child: Container(
        width: fullWidth ? double.infinity : null,
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
