import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
import '../../models/evento.dart';
import '../../utils/image_helper.dart';

class GestionParticipantesScreen extends StatefulWidget {
  final int eventoId;
  final String? eventoTitulo;

  const GestionParticipantesScreen({
    super.key,
    required this.eventoId,
    this.eventoTitulo,
  });

  @override
  State<GestionParticipantesScreen> createState() =>
      _GestionParticipantesScreenState();
}

class _GestionParticipantesScreenState
    extends State<GestionParticipantesScreen> {
  List<dynamic> _participantes = [];
  List<dynamic> _participantesFiltrados = [];
  bool _isLoading = true;
  String? _error;
  String? _filtroEstado;
  Evento? _evento;

  final Map<String, String> _estados = {
    'pendiente': 'Pendiente',
    'aprobada': 'Aprobada',
    'rechazada': 'Rechazada',
  };

  @override
  void initState() {
    super.initState();
    _loadEvento();
    _loadParticipantes();
  }

  Future<void> _loadEvento() async {
    final result = await ApiService.getEventoDetalle(widget.eventoId);
    if (result['success'] == true && mounted) {
      setState(() {
        _evento = result['evento'] as Evento;
      });
    }
  }

  Future<void> _loadParticipantes() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getParticipantesEvento(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _participantes = result['participantes'] as List? ?? [];
        _aplicarFiltro();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar participantes';
      }
    });
  }

  void _aplicarFiltro() {
    if (_filtroEstado == null || _filtroEstado!.isEmpty) {
      _participantesFiltrados = List.from(_participantes);
    } else {
      _participantesFiltrados =
          _participantes
              .where(
                (p) =>
                    (p['estado']?.toString().toLowerCase() ?? '') ==
                    _filtroEstado!.toLowerCase(),
              )
              .toList();
    }
  }

  void _cambiarFiltro(String? estado) {
    setState(() {
      _filtroEstado = estado;
      _aplicarFiltro();
    });
  }

  Future<void> _aprobarParticipacion(
    int participacionId,
    String nombre, {
    bool esNoRegistrado = false,
  }) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Aprobar Participación'),
            content: Text(
              '¿Estás seguro de que deseas aprobar la participación de $nombre?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.of(context).pop(true),
                style: TextButton.styleFrom(foregroundColor: Colors.green),
                child: const Text('Aprobar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result =
        esNoRegistrado
            ? await ApiService.aprobarParticipacionNoRegistrada(participacionId)
            : await ApiService.aprobarParticipacion(participacionId);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Participación aprobada exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al aprobar participación',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _rechazarParticipacion(
    int participacionId,
    String nombre, {
    bool esNoRegistrado = false,
  }) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Rechazar Participación'),
            content: Text(
              '¿Estás seguro de que deseas rechazar la participación de $nombre?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.of(context).pop(true),
                style: TextButton.styleFrom(foregroundColor: Colors.red),
                child: const Text('Rechazar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result =
        esNoRegistrado
            ? await ApiService.rechazarParticipacionNoRegistrada(
              participacionId,
            )
            : await ApiService.rechazarParticipacion(participacionId);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Participación rechazada exitosamente',
          ),
          backgroundColor: Colors.orange,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al rechazar participación',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _toggleAsistencia({
    required int participacionId,
    required String nombre,
    required bool esNoRegistrado,
    required bool nuevoValor,
  }) async {
    // Validar que la participación esté aprobada antes de marcar asistencia
    final participante = _participantes.firstWhere(
      (p) => (p as Map)['id'] == participacionId,
      orElse: () => null,
    );

    if (participante is Map) {
      final estadoActual =
          (participante['estado']?.toString().toLowerCase() ?? 'pendiente');
      if (estadoActual != 'aprobada') {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Solo puedes registrar asistencia de participantes aprobados.',
            ),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Registrar asistencia'),
            content: Text(
              nuevoValor
                  ? '¿Marcar a $nombre como ASISTIÓ al evento?'
                  : '¿Marcar a $nombre como NO asistió al evento?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: Text(
                  nuevoValor ? 'Marcar asistió' : 'Marcar no asistió',
                ),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result =
        esNoRegistrado
            ? await ApiService.marcarAsistenciaParticipacionNoRegistrada(
              participacionId,
              asistio: nuevoValor,
            )
            : await ApiService.marcarAsistenciaParticipacion(
              participacionId,
              asistio: nuevoValor,
            );

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Asistencia actualizada correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al actualizar asistencia',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Color _getColorEstado(String estado) {
    switch (estado.toLowerCase()) {
      case 'aprobada':
        return Colors.green;
      case 'rechazada':
        return Colors.red;
      case 'pendiente':
      default:
        return Colors.orange;
    }
  }

  IconData _getIconEstado(String estado) {
    switch (estado.toLowerCase()) {
      case 'aprobada':
        return Icons.check_circle;
      case 'rechazada':
        return Icons.cancel;
      case 'pendiente':
      default:
        return Icons.pending;
    }
  }

  @override
  Widget build(BuildContext context) {
    final eventoTitulo =
        widget.eventoTitulo ?? _evento?.titulo ?? 'Evento #${widget.eventoId}';

    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos'),
      appBar: AppBar(
        title: const Text('Gestión de Participantes'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadParticipantes,
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
              BreadcrumbItem(label: 'Participantes'),
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
                            onPressed: _loadParticipantes,
                            child: const Text('Reintentar'),
                          ),
                        ],
                      ),
                    )
                    : Column(
                      children: [
                        // Información del evento
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(16),
                          color: Theme.of(
                            context,
                          ).primaryColor.withOpacity(0.1),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.event, color: Colors.blue),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      eventoTitulo,
                                      style: const TextStyle(
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              if (_evento != null) ...[
                                const SizedBox(height: 8),
                                Text(
                                  'Total de participantes: ${_participantes.length}',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: Colors.grey[700],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),

                        // Filtros
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 16,
                            vertical: 12,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.grey[100],
                            border: Border(
                              bottom: BorderSide(color: Colors.grey[300]!),
                            ),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.filter_list, size: 20),
                              const SizedBox(width: 8),
                              const Text(
                                'Filtrar por estado:',
                                style: TextStyle(fontWeight: FontWeight.w500),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: SingleChildScrollView(
                                  scrollDirection: Axis.horizontal,
                                  child: Row(
                                    children: [
                                      _buildChipFiltro(null, 'Todos'),
                                      const SizedBox(width: 8),
                                      ..._estados.entries.map(
                                        (entry) => Padding(
                                          padding: const EdgeInsets.only(
                                            right: 8,
                                          ),
                                          child: _buildChipFiltro(
                                            entry.key,
                                            entry.value,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                        // Lista de participantes
                        Expanded(
                          child:
                              _participantesFiltrados.isEmpty
                                  ? Center(
                                    child: Column(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Icon(
                                          Icons.people_outline,
                                          size: 64,
                                          color: Colors.grey[400],
                                        ),
                                        const SizedBox(height: 16),
                                        Text(
                                          _filtroEstado == null
                                              ? 'No hay participantes registrados'
                                              : 'No hay participantes con estado "${_estados[_filtroEstado] ?? _filtroEstado}"',
                                          style: TextStyle(
                                            fontSize: 16,
                                            color: Colors.grey[600],
                                          ),
                                          textAlign: TextAlign.center,
                                        ),
                                      ],
                                    ),
                                  )
                                  : RefreshIndicator(
                                    onRefresh: _loadParticipantes,
                                    child: ListView.builder(
                                      padding: const EdgeInsets.all(16),
                                      itemCount: _participantesFiltrados.length,
                                      itemBuilder: (context, index) {
                                        final participante =
                                            _participantesFiltrados[index]
                                                as Map;
                                        return _buildParticipanteCard(
                                          participante,
                                        );
                                      },
                                    ),
                                  ),
                        ),
                      ],
                    ),
          ),
        ],
      ),
    );
  }

  Widget _buildChipFiltro(String? estado, String label) {
    final isSelected = _filtroEstado == estado;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        if (selected) {
          _cambiarFiltro(estado);
        } else {
          _cambiarFiltro(null);
        }
      },
      selectedColor: Theme.of(context).primaryColor.withOpacity(0.2),
      checkmarkColor: Theme.of(context).primaryColor,
    );
  }

  Widget _buildParticipanteCard(Map participante) {
    final participacionId = participante['id'] as int? ?? 0;
    final nombre = participante['nombre']?.toString() ?? 'Sin nombre';
    final correo = participante['correo']?.toString() ?? '';
    final fotoPerfil = participante['foto_perfil'] as String?;
    final estado = participante['estado']?.toString() ?? 'pendiente';
    final fechaInscripcion = participante['fecha_inscripcion']?.toString();
    final telefono = participante['telefono']?.toString();
    final direccion = participante['direccion']?.toString();
    final asistio = participante['asistio'] == true;

    // Detectar si es participante no registrado
    final esNoRegistrado =
        participante['es_no_registrado'] == true ||
        participante['no_registrado'] == true ||
        participante['tipo']?.toString().toLowerCase() == 'no_registrado';

    final estadoColor = _getColorEstado(estado);
    final estadoIcon = _getIconEstado(estado);
    final estadoLabel = _estados[estado.toLowerCase()] ?? estado;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header con foto y nombre
            Row(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundImage:
                      fotoPerfil != null
                          ? CachedNetworkImageProvider(
                            ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                          )
                          : null,
                  backgroundColor: Colors.blue[100],
                  child:
                      fotoPerfil == null
                          ? Text(
                            nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
                            style: TextStyle(
                              color: Colors.blue[800],
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          )
                          : null,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        nombre,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (correo.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        Text(
                          correo,
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
                // Badge de estado
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: estadoColor.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: estadoColor, width: 1.5),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(estadoIcon, size: 16, color: estadoColor),
                          const SizedBox(width: 4),
                          Text(
                            estadoLabel,
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: estadoColor,
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (esNoRegistrado) ...[
                      const SizedBox(height: 4),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.orange.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.orange, width: 1),
                        ),
                        child: const Text(
                          'NO REGISTRADO',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: Colors.orange,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ),

            // Información adicional
            if (telefono != null ||
                direccion != null ||
                fechaInscripcion != null) ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 8),
              if (telefono != null)
                _buildInfoRow(Icons.phone, 'Teléfono', telefono),
              if (direccion != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(Icons.location_on, 'Dirección', direccion),
              ],
              if (fechaInscripcion != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(
                  Icons.calendar_today,
                  'Fecha de inscripción',
                  fechaInscripcion,
                ),
              ],
            ],

            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.how_to_reg, size: 18),
                    const SizedBox(width: 8),
                    const Text(
                      'Asistencia',
                      style: TextStyle(fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
                Switch(
                  value: asistio,
                  onChanged: (value) {
                    _toggleAsistencia(
                      participacionId: participacionId,
                      nombre: nombre,
                      esNoRegistrado: esNoRegistrado,
                      nuevoValor: value,
                    );
                  },
                ),
              ],
            ),

            // Botones de acción (solo para pendientes)
            if (estado.toLowerCase() == 'pendiente') ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed:
                          () => _rechazarParticipacion(
                            participacionId,
                            nombre,
                            esNoRegistrado: esNoRegistrado,
                          ),
                      icon: const Icon(Icons.cancel, size: 18),
                      label: const Text('Rechazar'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.red,
                        side: const BorderSide(color: Colors.red),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed:
                          () => _aprobarParticipacion(
                            participacionId,
                            nombre,
                            esNoRegistrado: esNoRegistrado,
                          ),
                      icon: const Icon(Icons.check_circle, size: 18),
                      label: const Text('Aprobar'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
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
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
