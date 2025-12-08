import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../evento_detail_screen.dart';
import '../../utils/image_helper.dart';

class EventosPatrocinadosScreen extends StatefulWidget {
  const EventosPatrocinadosScreen({super.key});

  @override
  State<EventosPatrocinadosScreen> createState() =>
      _EventosPatrocinadosScreenState();
}

class _EventosPatrocinadosScreenState extends State<EventosPatrocinadosScreen> {
  List<dynamic> _eventos = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadEventos();
  }

  Future<void> _loadEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getMisEventosEmpresa();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        final todosEventos = result['eventos'] as List<dynamic>? ?? [];
        // Filtrar solo eventos donde la empresa es patrocinadora
        _eventos =
            todosEventos.where((eventoData) {
              final tipoRelacion = eventoData['tipo_relacion'] as String? ?? '';
              return tipoRelacion == 'patrocinadora';
            }).toList();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/empresa/eventos'),
      appBar: AppBar(
        title: const Text('Eventos Patrocinados'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadEventos,
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
                    FilledButton.tonal(
                      onPressed: _loadEventos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _eventos.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
                    const SizedBox(height: 16),
                    Text(
                      'No tienes eventos patrocinados',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Explora eventos disponibles para patrocinar',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadEventos,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _eventos.length,
                  itemBuilder: (context, index) {
                    final eventoData = _eventos[index] as Map<String, dynamic>;
                    return _buildEventoCard(eventoData);
                  },
                ),
              ),
    );
  }

  Widget _buildEventoCard(Map<String, dynamic> eventoData) {
    final evento = eventoData['evento'] as Map<String, dynamic>?;
    if (evento == null) return const SizedBox.shrink();

    final eventoId = evento['id'] as int?;
    final titulo = evento['titulo'] as String? ?? 'Sin título';
    final descripcion = evento['descripcion'] as String?;
    final tipoEvento = evento['tipo_evento'] as String?;
    final fechaInicio = evento['fecha_inicio'] as String?;
    final ciudad = evento['ciudad'] as String?;
    final imagenes = evento['imagenes'] as List<dynamic>? ?? [];
    final imagenPrincipal = imagenes.isNotEmpty ? imagenes[0].toString() : null;
    final ong = evento['ong'] as Map<String, dynamic>?;
    final nombreOng = ong?['nombre_ong'] as String?;

    final estadoParticipacion = eventoData['estado_participacion'] as String?;
    final tipoColaboracion = eventoData['tipo_colaboracion'] as String?;
    final descripcionColaboracion =
        eventoData['descripcion_colaboracion'] as String?;
    final fechaAsignacion = eventoData['fecha_asignacion'] as String?;

    DateTime? fechaInicioDate;
    if (fechaInicio != null) {
      try {
        fechaInicioDate = DateTime.parse(fechaInicio);
      } catch (e) {
        // Ignorar error
      }
    }

    // Determinar si el evento ya pasó o está por venir
    final ahora = DateTime.now();
    final eventoPasado =
        fechaInicioDate != null && fechaInicioDate.isBefore(ahora);
    final eventoProximo =
        fechaInicioDate != null && fechaInicioDate.isAfter(ahora);

    // Color y texto del estado
    Color estadoColor;
    IconData estadoIcon;
    String estadoLabel;
    switch (estadoParticipacion?.toLowerCase() ?? '') {
      case 'confirmada':
        estadoColor = Colors.green;
        estadoIcon = Icons.check_circle;
        estadoLabel = 'Confirmada';
        break;
      case 'cancelada':
        estadoColor = Colors.red;
        estadoIcon = Icons.cancel;
        estadoLabel = 'Cancelada';
        break;
      default:
        estadoColor = Colors.orange;
        estadoIcon = Icons.pending;
        estadoLabel = 'Asignada';
    }

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      clipBehavior: Clip.antiAlias,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap:
            eventoId != null
                ? () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder:
                          (context) => EventoDetailScreen(eventoId: eventoId),
                    ),
                  ).then((_) => _loadEventos());
                }
                : null,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen principal
            if (imagenPrincipal != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(12),
                ),
                child: CachedNetworkImage(
                  imageUrl:
                      ImageHelper.buildImageUrl(imagenPrincipal) ??
                      imagenPrincipal,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder:
                      (context, url) => Container(
                        height: 200,
                        width: double.infinity,
                        color: Colors.grey[200],
                        child: const Center(child: CircularProgressIndicator()),
                      ),
                  errorWidget:
                      (context, url, error) => Container(
                        height: 200,
                        width: double.infinity,
                        color: Colors.grey[200],
                        child: Icon(
                          Icons.image_not_supported,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                      ),
                ),
              )
            else
              Container(
                height: 200,
                width: double.infinity,
                color: Colors.grey[200],
                child: Icon(Icons.event, size: 64, color: Colors.grey[400]),
              ),
            Padding(
              padding: const EdgeInsets.all(16.0),
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
                          color: Colors.purple[100],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'Patrocinadora',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.purple[800],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  // Estado de participación
                  Row(
                    children: [
                      Icon(estadoIcon, size: 16, color: estadoColor),
                      const SizedBox(width: 4),
                      Text(
                        estadoLabel,
                        style: TextStyle(
                          fontSize: 14,
                          color: estadoColor,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      const Spacer(),
                      // Indicador de evento pasado/próximo
                      if (eventoPasado)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.grey[200],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                Icons.event_busy,
                                size: 14,
                                color: Colors.grey[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                'Finalizado',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[700],
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        )
                      else if (eventoProximo)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.blue[100],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                Icons.event_available,
                                size: 14,
                                color: Colors.blue[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                'Próximo',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.blue[700],
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  if (descripcion != null && descripcion.isNotEmpty) ...[
                    Text(
                      descripcion,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[700]),
                    ),
                    const SizedBox(height: 12),
                  ],
                  if (tipoEvento != null) ...[
                    Row(
                      children: [
                        Icon(Icons.category, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          tipoEvento,
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (fechaInicioDate != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.calendar_today,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          _formatDate(fechaInicioDate),
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (ciudad != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Text(ciudad, style: TextStyle(color: Colors.grey[600])),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (nombreOng != null) ...[
                    Row(
                      children: [
                        Icon(Icons.business, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            nombreOng,
                            style: TextStyle(color: Colors.grey[600]),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (tipoColaboracion != null ||
                      descripcionColaboracion != null) ...[
                    const Divider(),
                    const SizedBox(height: 8),
                    if (tipoColaboracion != null)
                      Row(
                        children: [
                          Icon(
                            Icons.category,
                            size: 16,
                            color: Colors.purple[700],
                          ),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              'Tipo: $tipoColaboracion',
                              style: TextStyle(
                                color: Colors.purple[700],
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                        ],
                      ),
                    if (descripcionColaboracion != null) ...[
                      const SizedBox(height: 4),
                      Text(
                        descripcionColaboracion,
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[700],
                          fontStyle: FontStyle.italic,
                        ),
                      ),
                    ],
                    const SizedBox(height: 8),
                  ],
                  if (fechaAsignacion != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.access_time,
                          size: 14,
                          color: Colors.grey[500],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'Asignado: ${_formatFecha(fechaAsignacion)}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[500],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        'Ver detalles',
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
          ],
        ),
      ),
    );
  }

  String _formatFecha(String fechaStr) {
    try {
      final fecha = DateTime.parse(fechaStr);
      return '${fecha.day}/${fecha.month}/${fecha.year}';
    } catch (e) {
      return fechaStr;
    }
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
