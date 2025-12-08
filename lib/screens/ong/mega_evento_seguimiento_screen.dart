import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:io';
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
import '../../models/mega_evento.dart';
import 'mega_evento_detail_screen.dart';

class MegaEventoSeguimientoScreen extends StatefulWidget {
  final int megaEventoId;

  const MegaEventoSeguimientoScreen({super.key, required this.megaEventoId});

  @override
  State<MegaEventoSeguimientoScreen> createState() =>
      _MegaEventoSeguimientoScreenState();
}

class _MegaEventoSeguimientoScreenState
    extends State<MegaEventoSeguimientoScreen> {
  Map<String, dynamic>? _seguimiento;
  MegaEvento? _megaEvento;
  bool _isLoading = true;
  String? _error;
  List<dynamic> _participantes = [];
  List<dynamic> _historial = [];
  bool _isLoadingParticipantes = false;
  bool _isLoadingHistorial = false;

  @override
  void initState() {
    super.initState();
    _loadSeguimiento();
    _loadParticipantes();
    _loadHistorial();
  }

  Future<void> _loadSeguimiento() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getSeguimientoMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _seguimiento = result['seguimiento'] as Map<String, dynamic>?;
        if (_seguimiento != null && _seguimiento!['mega_evento'] != null) {
          _megaEvento = MegaEvento.fromJson(
            _seguimiento!['mega_evento'] as Map<String, dynamic>,
          );
        }
      } else {
        _error = result['error'] as String? ?? 'Error al cargar seguimiento';
      }
    });
  }

  Future<void> _loadParticipantes() async {
    setState(() {
      _isLoadingParticipantes = true;
    });

    final result = await ApiService.getParticipantesMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      _isLoadingParticipantes = false;
      if (result['success'] == true) {
        _participantes = result['participantes'] as List<dynamic>? ?? [];
      }
    });
  }

  Future<void> _loadHistorial() async {
    setState(() {
      _isLoadingHistorial = true;
    });

    final result = await ApiService.getHistorialMegaEvento(widget.megaEventoId);

    if (!mounted) return;

    setState(() {
      _isLoadingHistorial = false;
      if (result['success'] == true) {
        _historial = result['historial'] as List<dynamic>? ?? [];
      }
    });
  }

  Future<void> _exportarExcel() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Exportar Reporte Excel'),
            content: const Text(
              '¿Deseas exportar el seguimiento del mega evento en formato Excel?',
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
      final result = await ApiService.exportarExcelMegaEvento(
        widget.megaEventoId,
      );

      if (!mounted) return;
      Navigator.of(context).pop(); // Cerrar indicador de carga

      if (result['success'] == true) {
        final data = result['data'] as Uint8List?;
        if (data != null) {
          final directory = await getApplicationDocumentsDirectory();
          final file = File(
            '${directory.path}/seguimiento_mega_evento_${widget.megaEventoId}_${DateTime.now().millisecondsSinceEpoch}.xlsx',
          );
          await file.writeAsBytes(data);

          await OpenFile.open(file.path);

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
      Navigator.of(context).pop(); // Cerrar indicador de carga
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
        title: const Text('Seguimiento del Mega Evento'),
        actions: [
          IconButton(
            icon: const Icon(Icons.file_download),
            onPressed: _exportarExcel,
            tooltip: 'Exportar Excel',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _loadSeguimiento();
              _loadParticipantes();
              _loadHistorial();
            },
            tooltip: 'Actualizar',
          ),
        ],
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
                label: 'Mega Eventos',
                onTap: () => Navigator.pop(context),
              ),
              BreadcrumbItem(
                label: _megaEvento?.titulo ?? 'Seguimiento',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder:
                          (context) => MegaEventoDetailScreen(
                            megaEventoId: widget.megaEventoId,
                          ),
                    ),
                  );
                },
              ),
              BreadcrumbItem(label: 'Seguimiento'),
            ],
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
                            style: const TextStyle(color: Colors.red),
                            textAlign: TextAlign.center,
                          ),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            onPressed: _loadSeguimiento,
                            child: const Text('Reintentar'),
                          ),
                        ],
                      ),
                    )
                    : _seguimiento == null
                    ? const Center(child: Text('No hay datos de seguimiento'))
                    : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Header con información del mega evento
                          if (_megaEvento != null) ...[
                            Card(
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _megaEvento!.titulo,
                                      style: const TextStyle(
                                        fontSize: 24,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    Text(
                                      '${_formatDateTime(_megaEvento!.fechaInicio)} - ${_formatDateTime(_megaEvento!.fechaFin)}',
                                      style: TextStyle(color: Colors.grey[600]),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),
                          ],

                          // Métricas principales
                          const Text(
                            'Métricas Principales',
                            style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 16),

                          _buildMetricasGrid(),

                          const SizedBox(height: 24),

                          // Métricas de interacción
                          const Text(
                            'Métricas de Interacción',
                            style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 16),

                          _buildMetricasInteraccion(),

                          const SizedBox(height: 24),

                          // Tabla de participantes
                          const Text(
                            'Participantes',
                            style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 16),

                          _buildTablaParticipantes(),

                          const SizedBox(height: 24),

                          // Historial
                          const Text(
                            'Historial de Cambios',
                            style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 16),

                          _buildHistorial(),
                        ],
                      ),
                    ),
          ),
        ],
      ),
    );
  }

  Widget _buildMetricasGrid() {
    final estadisticas = _seguimiento?['estadisticas'] as Map<String, dynamic>?;
    if (estadisticas == null) {
      return const SizedBox.shrink();
    }

    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 16,
      crossAxisSpacing: 16,
      childAspectRatio: 1.5,
      children: [
        _buildMetricaCard(
          'Total Participantes',
          '${estadisticas['total_participantes'] ?? 0}',
          Icons.people,
          Colors.blue,
        ),
        _buildMetricaCard(
          'Participantes Aprobados',
          '${estadisticas['participantes_aprobados'] ?? 0}',
          Icons.check_circle,
          Colors.green,
        ),
        _buildMetricaCard(
          'Tasa de Confirmación',
          '${estadisticas['tasa_confirmacion'] ?? 0}%',
          Icons.percent,
          Colors.orange,
        ),
        _buildMetricaCard(
          'Capacidad',
          estadisticas['porcentaje_capacidad'] != null
              ? '${estadisticas['porcentaje_capacidad']}%'
              : '-',
          Icons.pie_chart,
          Colors.purple,
        ),
      ],
    );
  }

  Widget _buildMetricaCard(
    String titulo,
    String valor,
    IconData icon,
    Color color,
  ) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [color, color.withOpacity(0.7)],
          ),
          borderRadius: BorderRadius.circular(12),
        ),
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white, size: 32),
            const SizedBox(height: 8),
            Text(
              valor,
              style: const TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              titulo,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 12,
                color: Colors.white,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMetricasInteraccion() {
    final estadisticas = _seguimiento?['estadisticas'] as Map<String, dynamic>?;
    final interaccion =
        estadisticas?['interaccion_social'] as Map<String, dynamic>?;
    if (interaccion == null) {
      return const SizedBox.shrink();
    }

    return Row(
      children: [
        Expanded(
          child: _buildMetricaInteraccionCard(
            'Total Reacciones',
            '${interaccion['total_reacciones'] ?? 0}',
            Icons.favorite,
            Colors.pink,
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: _buildMetricaInteraccionCard(
            'Total Compartidos',
            '${interaccion['total_compartidos'] ?? 0}',
            Icons.share,
            Colors.orange,
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: _buildMetricaInteraccionCard(
            'Total Participaciones',
            '${interaccion['total_participaciones'] ?? 0}',
            Icons.person_add,
            Colors.green,
          ),
        ),
      ],
    );
  }

  Widget _buildMetricaInteraccionCard(
    String titulo,
    String valor,
    IconData icon,
    Color color,
  ) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: color, width: 2),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 8),
            Text(
              valor,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              titulo,
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTablaParticipantes() {
    if (_isLoadingParticipantes) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_participantes.isEmpty) {
      return const Center(child: Text('No hay participantes registrados'));
    }

    return Card(
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: DataTable(
          columns: const [
            DataColumn(label: Text('Participante')),
            DataColumn(label: Text('Email')),
            DataColumn(label: Text('Teléfono')),
            DataColumn(label: Text('Estado')),
            DataColumn(label: Text('Fecha Registro')),
          ],
          rows:
              _participantes.map((participante) {
                final p = participante as Map<String, dynamic>;
                return DataRow(
                  cells: [
                    DataCell(
                      Text(
                        p['nombre'] as String? ??
                            p['user_name'] as String? ??
                            'Usuario',
                      ),
                    ),
                    DataCell(Text(p['email'] as String? ?? '-')),
                    DataCell(Text(p['telefono'] as String? ?? '-')),
                    DataCell(
                      _buildEstadoBadge(p['estado'] as String? ?? 'pendiente'),
                    ),
                    DataCell(
                      Text(
                        p['fecha_registro'] != null
                            ? _formatDate(DateTime.parse(p['fecha_registro']))
                            : '-',
                      ),
                    ),
                  ],
                );
              }).toList(),
        ),
      ),
    );
  }

  Widget _buildEstadoBadge(String estado) {
    Color color;
    switch (estado) {
      case 'aprobada':
        color = Colors.green;
        break;
      case 'pendiente':
        color = Colors.orange;
        break;
      case 'rechazada':
        color = Colors.red;
        break;
      default:
        color = Colors.grey;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color),
      ),
      child: Text(
        estado.toUpperCase(),
        style: TextStyle(
          color: color,
          fontSize: 10,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _buildHistorial() {
    if (_isLoadingHistorial) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_historial.isEmpty) {
      return const Center(child: Text('No hay historial de cambios'));
    }

    return Card(
      child: ListView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _historial.length,
        itemBuilder: (context, index) {
          final item = _historial[index] as Map<String, dynamic>;
          return ListTile(
            leading: const Icon(Icons.history),
            title: Text(item['accion'] as String? ?? 'Cambio'),
            subtitle: Text(item['descripcion'] as String? ?? ''),
            trailing: Text(
              item['fecha'] != null
                  ? _formatDate(DateTime.parse(item['fecha']))
                  : '-',
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          );
        },
      ),
    );
  }

  String _formatDateTime(DateTime date) {
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
    return '${date.day} de ${months[date.month - 1]} de ${date.year}, '
        '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
  }

  String _formatDate(DateTime date) {
    return '${date.day}/${date.month}/${date.year}';
  }
}
