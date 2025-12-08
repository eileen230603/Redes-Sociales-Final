import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:io';
import 'package:path_provider/path_provider.dart';
import 'package:open_file/open_file.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
import '../../models/evento.dart';
import '../evento_detail_screen.dart';

class DashboardEventoScreen extends StatefulWidget {
  final int eventoId;
  final String? eventoTitulo;

  const DashboardEventoScreen({
    super.key,
    required this.eventoId,
    this.eventoTitulo,
  });

  @override
  State<DashboardEventoScreen> createState() => _DashboardEventoScreenState();
}

class _DashboardEventoScreenState extends State<DashboardEventoScreen> {
  Map<String, dynamic>? _dashboardData;
  bool _isLoading = true;
  String? _error;
  Evento? _evento;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getDashboardEvento(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _dashboardData = result;
        if (result['evento'] != null) {
          _evento = Evento.fromJson(result['evento'] as Map<String, dynamic>);
        }
      } else {
        _error = result['error'] as String? ?? 'Error al cargar dashboard';
      }
    });
  }

  Future<void> _descargarPdf() async {
    // Mostrar diálogo de confirmación
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

    // Mostrar indicador de carga
    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );

    try {
      final result = await ApiService.descargarDashboardEventoPdf(
        widget.eventoId,
      );

      if (!mounted) return;
      Navigator.of(context).pop(); // Cerrar indicador de carga

      if (result['success'] == true) {
        // Guardar el PDF
        final pdfBytes = result['pdfBytes'] as List<int>?;
        if (pdfBytes != null) {
          final directory = await getApplicationDocumentsDirectory();
          final file = File(
            '${directory.path}/dashboard_evento_${widget.eventoId}_${DateTime.now().millisecondsSinceEpoch}.pdf',
          );
          await file.writeAsBytes(pdfBytes);

          // Abrir el archivo
          await OpenFile.open(file.path);

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
        title: const Text('Dashboard del Evento'),
        actions: [
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDashboard,
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
                    : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Información del evento
                          if (_evento != null) _buildInfoEvento(),

                          const SizedBox(height: 24),

                          // Estadísticas generales
                          if (_dashboardData!['estadisticas'] != null)
                            _buildEstadisticas(),

                          const SizedBox(height: 24),

                          // Participantes
                          if (_dashboardData!['participantes'] != null)
                            _buildParticipantes(),

                          const SizedBox(height: 24),

                          // Reacciones
                          if (_dashboardData!['reacciones'] != null)
                            _buildReacciones(),

                          const SizedBox(height: 24),

                          // Compartidos
                          _buildCompartidos(),
                        ],
                      ),
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
                const Icon(Icons.event, size: 24, color: Colors.blue),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    _evento!.titulo,
                    style: const TextStyle(
                      fontSize: 20,
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

  Widget _buildEstadisticas() {
    final estadisticas = _dashboardData!['estadisticas'] as Map?;
    if (estadisticas == null) return const SizedBox.shrink();

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Estadísticas',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            Wrap(
              spacing: 16,
              runSpacing: 16,
              children: [
                _buildMetricaCard(
                  'Total Participantes',
                  estadisticas['total_participantes']?.toString() ?? '0',
                  Icons.people,
                  Colors.blue,
                ),
                _buildMetricaCard(
                  'Aprobados',
                  estadisticas['aprobados']?.toString() ?? '0',
                  Icons.check_circle,
                  Colors.green,
                ),
                _buildMetricaCard(
                  'Pendientes',
                  estadisticas['pendientes']?.toString() ?? '0',
                  Icons.pending,
                  Colors.orange,
                ),
                _buildMetricaCard(
                  'Rechazados',
                  estadisticas['rechazados']?.toString() ?? '0',
                  Icons.cancel,
                  Colors.red,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMetricaCard(
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    return Container(
      width: (MediaQuery.of(context).size.width - 64) / 2,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 32),
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
            label,
            style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildParticipantes() {
    final participantes = _dashboardData!['participantes'] as List?;
    if (participantes == null || participantes.isEmpty) {
      return Card(
        elevation: 2,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            children: [
              const Text(
                'Participantes',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              Text(
                'No hay participantes registrados',
                style: TextStyle(color: Colors.grey[600]),
              ),
            ],
          ),
        ),
      );
    }

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Participantes',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                Text(
                  '${participantes.length}',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Colors.blue,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ...participantes.take(5).map((p) => _buildParticipanteItem(p)),
            if (participantes.length > 5)
              Padding(
                padding: const EdgeInsets.only(top: 8),
                child: Text(
                  'Y ${participantes.length - 5} más...',
                  style: TextStyle(
                    color: Colors.grey[600],
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildParticipanteItem(dynamic participante) {
    final nombre = participante['nombre']?.toString() ?? 'Sin nombre';
    final estado = participante['estado']?.toString() ?? 'pendiente';
    final correo = participante['correo']?.toString();

    Color estadoColor;
    IconData estadoIcon;
    switch (estado.toLowerCase()) {
      case 'aprobada':
        estadoColor = Colors.green;
        estadoIcon = Icons.check_circle;
        break;
      case 'rechazada':
        estadoColor = Colors.red;
        estadoIcon = Icons.cancel;
        break;
      default:
        estadoColor = Colors.orange;
        estadoIcon = Icons.pending;
    }

    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: estadoColor.withOpacity(0.2),
            child: Icon(estadoIcon, color: estadoColor, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  nombre,
                  style: const TextStyle(fontWeight: FontWeight.w500),
                ),
                if (correo != null && correo.isNotEmpty)
                  Text(
                    correo,
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                  ),
              ],
            ),
          ),
          Chip(
            label: Text(estado.toUpperCase()),
            backgroundColor: estadoColor.withOpacity(0.1),
            labelStyle: TextStyle(
              fontSize: 10,
              color: estadoColor,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReacciones() {
    final reacciones = _dashboardData!['reacciones'] as Map?;
    if (reacciones == null) return const SizedBox.shrink();

    final total = reacciones['total'] as int? ?? 0;

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.favorite, color: Colors.red, size: 32),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Reacciones',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      '$total reacciones',
                      style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                    ),
                  ],
                ),
              ],
            ),
            Text(
              '$total',
              style: const TextStyle(
                fontSize: 32,
                fontWeight: FontWeight.bold,
                color: Colors.red,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCompartidos() {
    final totalCompartidos = _dashboardData!['compartidos'] as int? ?? 0;

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.share, color: Colors.blue, size: 32),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Compartidos',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      '$totalCompartidos veces compartido',
                      style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                    ),
                  ],
                ),
              ],
            ),
            Text(
              '$totalCompartidos',
              style: const TextStyle(
                fontSize: 32,
                fontWeight: FontWeight.bold,
                color: Colors.blue,
              ),
            ),
          ],
        ),
      ),
    );
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
}
