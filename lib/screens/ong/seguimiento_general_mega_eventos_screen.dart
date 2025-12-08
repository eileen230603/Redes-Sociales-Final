import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import 'mega_evento_seguimiento_screen.dart';

class SeguimientoGeneralMegaEventosScreen extends StatefulWidget {
  const SeguimientoGeneralMegaEventosScreen({super.key});

  @override
  State<SeguimientoGeneralMegaEventosScreen> createState() =>
      _SeguimientoGeneralMegaEventosScreenState();
}

class _SeguimientoGeneralMegaEventosScreenState
    extends State<SeguimientoGeneralMegaEventosScreen> {
  Map<String, dynamic>? _estadisticas;
  List<dynamic> _megaEventosDetalle = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadSeguimientoGeneral();
  }

  Future<void> _loadSeguimientoGeneral() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getSeguimientoGeneralMegaEventos();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _estadisticas =
            result['estadisticas_agregadas'] as Map<String, dynamic>?;
        _megaEventosDetalle =
            result['mega_eventos_detalle'] as List<dynamic>? ?? [];
      } else {
        _error =
            result['error'] as String? ?? 'Error al cargar seguimiento general';
      }
    });
  }

  String _getEstadoLabel(String? estado) {
    switch (estado) {
      case 'planificacion':
        return 'Planificación';
      case 'activo':
        return 'Activo';
      case 'en_curso':
        return 'En Curso';
      case 'finalizado':
        return 'Finalizado';
      case 'cancelado':
        return 'Cancelado';
      default:
        return estado ?? 'Desconocido';
    }
  }

  Color _getEstadoColor(String? estado) {
    switch (estado) {
      case 'planificacion':
        return Colors.blue;
      case 'activo':
        return Colors.green;
      case 'en_curso':
        return Colors.orange;
      case 'finalizado':
        return Colors.grey;
      case 'cancelado':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(
        currentRoute: '/ong/seguimiento-general-mega-eventos',
      ),
      appBar: AppBar(
        title: const Text('Seguimiento General - Mega Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadSeguimientoGeneral,
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
                      onPressed: _loadSeguimientoGeneral,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadSeguimientoGeneral,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Estadísticas agregadas
                      if (_estadisticas != null) ...[
                        const Text(
                          'Estadísticas Generales',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 16),
                        _buildEstadisticasGrid(_estadisticas!),
                        const SizedBox(height: 24),
                      ],

                      // Lista de mega eventos
                      const Text(
                        'Mega Eventos',
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 16),

                      if (_megaEventosDetalle.isEmpty)
                        Center(
                          child: Padding(
                            padding: const EdgeInsets.all(32),
                            child: Column(
                              children: [
                                Icon(
                                  Icons.event_busy,
                                  size: 64,
                                  color: Colors.grey[400],
                                ),
                                const SizedBox(height: 16),
                                Text(
                                  'No hay mega eventos registrados',
                                  style: TextStyle(
                                    fontSize: 18,
                                    color: Colors.grey[600],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        )
                      else
                        ..._megaEventosDetalle.map((megaEvento) {
                          return _buildMegaEventoCard(
                            megaEvento as Map<String, dynamic>,
                          );
                        }).toList(),
                    ],
                  ),
                ),
              ),
    );
  }

  Widget _buildEstadisticasGrid(Map<String, dynamic> stats) {
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 16,
      mainAxisSpacing: 16,
      childAspectRatio: 1.5,
      children: [
        _buildStatCard(
          'Total Mega Eventos',
          stats['total_mega_eventos']?.toString() ?? '0',
          Icons.event,
          Colors.blue,
        ),
        _buildStatCard(
          'Total Participantes',
          stats['total_participantes']?.toString() ?? '0',
          Icons.people,
          Colors.green,
        ),
        _buildStatCard(
          'Total Reacciones',
          stats['total_reacciones']?.toString() ?? '0',
          Icons.favorite,
          Colors.red,
        ),
        _buildStatCard(
          'Total Compartidos',
          stats['total_compartidos']?.toString() ?? '0',
          Icons.share,
          Colors.orange,
        ),
        _buildStatCard(
          'Mega Eventos Activos',
          stats['mega_eventos_activos']?.toString() ?? '0',
          Icons.play_circle,
          Colors.green,
        ),
        _buildStatCard(
          'Mega Eventos Finalizados',
          stats['mega_eventos_finalizados']?.toString() ?? '0',
          Icons.check_circle,
          Colors.grey,
        ),
        _buildStatCard(
          'Promedio Participantes',
          stats['promedio_participantes_por_evento']?.toString() ?? '0',
          Icons.trending_up,
          Colors.purple,
        ),
        _buildStatCard(
          'Promedio Reacciones',
          stats['promedio_reacciones_por_evento']?.toString() ?? '0',
          Icons.trending_up,
          Colors.pink,
        ),
      ],
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
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 32, color: color),
            const SizedBox(height: 8),
            Text(
              value,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMegaEventoCard(Map<String, dynamic> megaEvento) {
    final id = megaEvento['id'] as int?;
    final titulo = megaEvento['titulo'] as String? ?? 'Sin título';
    final estado = megaEvento['estado'] as String?;
    final fechaInicio = megaEvento['fecha_inicio'] as String?;
    final fechaFin = megaEvento['fecha_fin'] as String?;
    final totalParticipantes = megaEvento['total_participantes'] as int? ?? 0;
    final totalReacciones = megaEvento['total_reacciones'] as int? ?? 0;
    final totalCompartidos = megaEvento['total_compartidos'] as int? ?? 0;

    DateTime? fechaInicioDate;
    DateTime? fechaFinDate;
    if (fechaInicio != null) {
      try {
        fechaInicioDate = DateTime.parse(fechaInicio);
      } catch (e) {
        // Ignorar error
      }
    }
    if (fechaFin != null) {
      try {
        fechaFinDate = DateTime.parse(fechaFin);
      } catch (e) {
        // Ignorar error
      }
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: InkWell(
        onTap:
            id != null
                ? () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder:
                          (context) =>
                              MegaEventoSeguimientoScreen(megaEventoId: id),
                    ),
                  ).then((_) => _loadSeguimientoGeneral());
                }
                : null,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      titulo,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: _getEstadoColor(estado).withOpacity(0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      _getEstadoLabel(estado),
                      style: TextStyle(
                        fontSize: 12,
                        color: _getEstadoColor(estado),
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    child: _buildMetricItem(
                      Icons.people,
                      'Participantes',
                      totalParticipantes.toString(),
                      Colors.green,
                    ),
                  ),
                  Expanded(
                    child: _buildMetricItem(
                      Icons.favorite,
                      'Reacciones',
                      totalReacciones.toString(),
                      Colors.red,
                    ),
                  ),
                  Expanded(
                    child: _buildMetricItem(
                      Icons.share,
                      'Compartidos',
                      totalCompartidos.toString(),
                      Colors.orange,
                    ),
                  ),
                ],
              ),
              if (fechaInicioDate != null || fechaFinDate != null) ...[
                const SizedBox(height: 12),
                const Divider(),
                const SizedBox(height: 12),
                if (fechaInicioDate != null)
                  Row(
                    children: [
                      Icon(
                        Icons.calendar_today,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Inicio: ${_formatDate(fechaInicioDate)}',
                        style: TextStyle(color: Colors.grey[600]),
                      ),
                    ],
                  ),
                if (fechaFinDate != null) ...[
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Icon(
                        Icons.calendar_today,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Fin: ${_formatDate(fechaFinDate)}',
                        style: TextStyle(color: Colors.grey[600]),
                      ),
                    ],
                  ),
                ],
              ],
              const SizedBox(height: 12),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  Text(
                    'Ver seguimiento detallado',
                    style: TextStyle(
                      color: Theme.of(context).primaryColor,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  const SizedBox(width: 4),
                  Icon(
                    Icons.arrow_forward_ios,
                    size: 16,
                    color: Theme.of(context).primaryColor,
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
    String label,
    String value,
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
        Text(label, style: TextStyle(fontSize: 12, color: Colors.grey[600])),
      ],
    );
  }

  String _formatDate(DateTime date) {
    final months = [
      'Ene',
      'Feb',
      'Mar',
      'Abr',
      'May',
      'Jun',
      'Jul',
      'Ago',
      'Sep',
      'Oct',
      'Nov',
      'Dic',
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }
}
