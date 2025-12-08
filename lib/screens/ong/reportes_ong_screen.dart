import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';

class ReportesOngScreen extends StatefulWidget {
  const ReportesOngScreen({super.key});

  @override
  State<ReportesOngScreen> createState() => _ReportesOngScreenState();
}

class _ReportesOngScreenState extends State<ReportesOngScreen> {
  List<Evento> _eventos = [];
  List<dynamic> _voluntarios = [];
  bool _isLoading = true;
  String? _error;
  int? _ongId;

  @override
  void initState() {
    super.initState();
    _loadDatos();
  }

  Future<void> _loadDatos() async {
    final ongId = _ongId ?? await AuthHelper.getOngIdWithRetry();

    if (!mounted) return;

    if (ongId == null) {
      setState(() {
        _isLoading = false;
        _error =
            'No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.';
      });
      return;
    }

    setState(() {
      _ongId = ongId;
    });

    setState(() {
      _isLoading = true;
      _error = null;
    });

    final eventosResult = await ApiService.getEventosOng(ongId);
    final voluntariosResult = await ApiService.getVoluntariosOng(ongId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (eventosResult['success'] == true) {
        _eventos = eventosResult['eventos'] as List<Evento>;
      }
      if (voluntariosResult['success'] == true) {
        _voluntarios = voluntariosResult['voluntarios'] as List;
      }
      if (eventosResult['success'] != true &&
          voluntariosResult['success'] != true) {
        _error =
            eventosResult['error'] as String? ??
            voluntariosResult['error'] as String? ??
            'Error al cargar datos';
      }
    });
  }

  int get _totalEventos => _eventos.length;
  int get _eventosPublicados =>
      _eventos.where((e) => e.estado == 'publicado').length;
  int get _eventosActivos =>
      _eventos
          .where(
            (e) =>
                e.estado == 'publicado' &&
                e.fechaInicio.isAfter(DateTime.now()),
          )
          .length;
  int get _totalVoluntarios => _voluntarios.length;
  int get _voluntariosAsistieron =>
      _voluntarios.where((v) => (v as Map)['asistio'] == true).length;
  int get _totalPuntos => _voluntarios.fold(
    0,
    (sum, v) => sum + ((v as Map)['puntos'] as int? ?? 0),
  );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/reportes'),
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
                            'Total Eventos',
                            '$_totalEventos',
                            Icons.event,
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
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: _buildStatCard(
                            'Total Voluntarios',
                            '$_totalVoluntarios',
                            Icons.people,
                            Colors.purple,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildStatCard(
                            'Total Puntos',
                            '$_totalPuntos',
                            Icons.star,
                            Colors.amber,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 32),
                    const Text(
                      'Resumen de Eventos',
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
                                'Eventos Publicados',
                                _eventosPublicados,
                                _totalEventos,
                                Colors.green,
                              ),
                              const SizedBox(height: 16),
                              _buildProgressBar(
                                'Eventos Activos',
                                _eventosActivos,
                                _totalEventos,
                                Colors.blue,
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
                            'No hay eventos para mostrar',
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ),
                      ),
                    const SizedBox(height: 32),
                    const Text(
                      'Resumen de Voluntarios',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 16),
                    if (_totalVoluntarios > 0)
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            children: [
                              _buildProgressBar(
                                'Voluntarios que Asistieron',
                                _voluntariosAsistieron,
                                _totalVoluntarios,
                                Colors.green,
                              ),
                              const SizedBox(height: 16),
                              _buildProgressBar(
                                'Voluntarios Inscritos',
                                _totalVoluntarios - _voluntariosAsistieron,
                                _totalVoluntarios,
                                Colors.blue,
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
                            'No hay voluntarios para mostrar',
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
