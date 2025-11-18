import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/evento.dart';
import '../models/evento_participacion.dart';
import '../widgets/app_drawer.dart';

class EventoDetailScreen extends StatefulWidget {
  final int eventoId;

  const EventoDetailScreen({super.key, required this.eventoId});

  @override
  State<EventoDetailScreen> createState() => _EventoDetailScreenState();
}

class _EventoDetailScreenState extends State<EventoDetailScreen> {
  Evento? _evento;
  bool _isLoading = true;
  String? _error;
  bool _isInscrito = false;
  bool _isCheckingInscripcion = false;
  bool _isProcessing = false;

  @override
  void initState() {
    super.initState();
    _loadEvento();
    _checkInscripcion();
  }

  Future<void> _loadEvento() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventoDetalle(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _evento = result['evento'] as Evento;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar evento';
      }
    });
  }

  Future<void> _checkInscripcion() async {
    setState(() {
      _isCheckingInscripcion = true;
    });

    final result = await ApiService.getMisEventos();

    if (!mounted) return;

    setState(() {
      _isCheckingInscripcion = false;
      if (result['success'] == true) {
        final participaciones =
            result['participaciones'] as List<EventoParticipacion>;
        _isInscrito = participaciones.any((p) => p.eventoId == widget.eventoId);
      }
    });
  }

  Future<void> _inscribirse() async {
    if (_evento == null || _isProcessing) return;

    setState(() {
      _isProcessing = true;
    });

    final result = await ApiService.inscribirEnEvento(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isProcessing = false;
    });

    if (result['success'] == true) {
      setState(() {
        _isInscrito = true;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] as String? ?? 'Inscripción exitosa'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al inscribirse'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _cancelarInscripcion() async {
    if (_evento == null || _isProcessing) return;

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Cancelar inscripción'),
            content: const Text(
              '¿Estás seguro de que deseas cancelar tu inscripción?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('No'),
              ),
              TextButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Sí, cancelar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    setState(() {
      _isProcessing = true;
    });

    final result = await ApiService.cancelarInscripcion(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isProcessing = false;
    });

    if (result['success'] == true) {
      setState(() {
        _isInscrito = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Inscripción cancelada',
          ),
          backgroundColor: Colors.orange,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al cancelar'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(title: const Text('Detalle del Evento')),
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
                      onPressed: _loadEvento,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _evento == null
              ? const Center(child: Text('Evento no encontrado'))
              : SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Título
                    Text(
                      _evento!.titulo,
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Estado e inscripción
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color:
                                _evento!.estado == 'publicado'
                                    ? Colors.green[100]
                                    : Colors.grey[300],
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Text(
                            _evento!.estado.toUpperCase(),
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  _evento!.estado == 'publicado'
                                      ? Colors.green[800]
                                      : Colors.grey[800],
                            ),
                          ),
                        ),
                        if (_isInscrito) ...[
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 6,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.blue[100],
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: const Text(
                              'INSCRITO',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.blue,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 24),

                    // Información básica
                    _buildInfoRow(
                      Icons.category,
                      'Tipo de evento',
                      _evento!.tipoEvento,
                    ),
                    const SizedBox(height: 12),
                    _buildInfoRow(
                      Icons.calendar_today,
                      'Fecha de inicio',
                      _formatDateTime(_evento!.fechaInicio),
                    ),
                    if (_evento!.fechaFin != null) ...[
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.calendar_today,
                        'Fecha de fin',
                        _formatDateTime(_evento!.fechaFin!),
                      ),
                    ],
                    if (_evento!.fechaLimiteInscripcion != null) ...[
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.event_available,
                        'Límite de inscripción',
                        _formatDateTime(_evento!.fechaLimiteInscripcion!),
                      ),
                    ],
                    if (_evento!.ciudad != null) ...[
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.location_on,
                        'Ciudad',
                        _evento!.ciudad!,
                      ),
                    ],
                    if (_evento!.direccion != null) ...[
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.place,
                        'Dirección',
                        _evento!.direccion!,
                      ),
                    ],
                    if (_evento!.capacidadMaxima != null) ...[
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.people,
                        'Capacidad máxima',
                        '${_evento!.capacidadMaxima} personas',
                      ),
                    ],

                    // Descripción
                    if (_evento!.descripcion != null &&
                        _evento!.descripcion!.isNotEmpty) ...[
                      const SizedBox(height: 24),
                      const Text(
                        'Descripción',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        _evento!.descripcion!,
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.grey[700],
                          height: 1.5,
                        ),
                      ),
                    ],

                    // Botones de acción
                    const SizedBox(height: 32),
                    if (_isCheckingInscripcion)
                      const Center(child: CircularProgressIndicator())
                    else if (_isInscrito)
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed:
                              _isProcessing ? null : _cancelarInscripcion,
                          icon: const Icon(Icons.cancel),
                          label:
                              _isProcessing
                                  ? const Text('Cancelando...')
                                  : const Text('Cancelar Inscripción'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.orange,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                        ),
                      )
                    else if (_evento!.puedeInscribirse)
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: _isProcessing ? null : _inscribirse,
                          icon: const Icon(Icons.check_circle),
                          label:
                              _isProcessing
                                  ? const Text('Inscribiendo...')
                                  : const Text('Inscribirse al Evento'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Theme.of(context).primaryColor,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                        ),
                      )
                    else
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: null,
                          child: const Text('Inscripciones Cerradas'),
                        ),
                      ),
                  ],
                ),
              ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: Colors.grey[600]),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
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
}
