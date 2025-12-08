import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/bottom_nav_bar.dart';
import '../../services/storage_service.dart';
import '../../utils/image_helper.dart';
import '../../utils/navigation_helper.dart';
import '../evento_detail_screen.dart';

class MisEventosColaboradoraScreen extends StatefulWidget {
  const MisEventosColaboradoraScreen({super.key});

  @override
  State<MisEventosColaboradoraScreen> createState() =>
      _MisEventosColaboradoraScreenState();
}

class _MisEventosColaboradoraScreenState
    extends State<MisEventosColaboradoraScreen> {
  List<dynamic> _eventos = [];
  bool _isLoading = true;
  String? _error;
  String _filtroTipo = 'todos'; // 'todos', 'colaboradora', 'patrocinadora'
  String _filtroEstado =
      'todos'; // 'todos', 'asignada', 'confirmada', 'cancelada'

  @override
  void initState() {
    super.initState();
    _loadMisEventos();
  }

  Future<void> _loadMisEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getMisEventosEmpresa();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _eventos = result['eventos'] as List<dynamic>? ?? [];
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  List<dynamic> get _eventosFiltrados {
    return _eventos.where((evento) {
      final tipoRelacion = evento['tipo_relacion'] as String? ?? '';
      final estadoParticipacion =
          evento['estado_participacion'] as String? ?? '';

      // Filtro por tipo
      if (_filtroTipo != 'todos') {
        if (_filtroTipo == 'colaboradora' && tipoRelacion != 'colaboradora') {
          return false;
        }
        if (_filtroTipo == 'patrocinadora' && tipoRelacion != 'patrocinadora') {
          return false;
        }
      }

      // Filtro por estado
      if (_filtroEstado != 'todos') {
        if (estadoParticipacion != _filtroEstado) {
          return false;
        }
      }

      return true;
    }).toList();
  }

  Future<void> _confirmarParticipacion(int eventoId) async {
    final tipoController = TextEditingController();
    final descripcionController = TextEditingController();

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Confirmar Participación'),
            content: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text(
                    'Confirma tu participación en este evento. Puedes agregar información sobre tu colaboración:',
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: tipoController,
                    decoration: const InputDecoration(
                      labelText: 'Tipo de Colaboración',
                      hintText: 'Ej: Recursos, Logística, Financiera',
                      border: OutlineInputBorder(),
                    ),
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: descripcionController,
                    decoration: const InputDecoration(
                      labelText: 'Descripción de Colaboración',
                      hintText: 'Describe cómo colaborarás...',
                      border: OutlineInputBorder(),
                    ),
                    maxLines: 3,
                  ),
                ],
              ),
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.pop(context, true),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF00A36C),
                  foregroundColor: Colors.white,
                ),
                child: const Text('Confirmar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.confirmarParticipacionEmpresa(
      eventoId,
      tipoColaboracion:
          tipoController.text.trim().isEmpty
              ? null
              : tipoController.text.trim(),
      descripcionColaboracion:
          descripcionController.text.trim().isEmpty
              ? null
              : descripcionController.text.trim(),
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Participación confirmada correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      await _loadMisEventos();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al confirmar participación',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(
        currentRoute: '/empresa/mis-eventos-colaboradora',
      ),
      appBar: AppBar(
        title: const Text('Mis Eventos - Colaboradora'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadMisEventos,
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
                      style: TextStyle(color: Colors.red[700]),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadMisEventos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : Column(
                children: [
                  // Filtros
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.grey[100],
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: DropdownButtonFormField<String>(
                                value: _filtroTipo,
                                decoration: const InputDecoration(
                                  labelText: 'Tipo',
                                  border: OutlineInputBorder(),
                                  contentPadding: EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 8,
                                  ),
                                ),
                                items: const [
                                  DropdownMenuItem(
                                    value: 'todos',
                                    child: Text('Todos'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'colaboradora',
                                    child: Text('Colaboradora'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'patrocinadora',
                                    child: Text('Patrocinadora'),
                                  ),
                                ],
                                onChanged: (value) {
                                  if (value != null) {
                                    setState(() {
                                      _filtroTipo = value;
                                    });
                                  }
                                },
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: DropdownButtonFormField<String>(
                                value: _filtroEstado,
                                decoration: const InputDecoration(
                                  labelText: 'Estado',
                                  border: OutlineInputBorder(),
                                  contentPadding: EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 8,
                                  ),
                                ),
                                items: const [
                                  DropdownMenuItem(
                                    value: 'todos',
                                    child: Text('Todos'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'asignada',
                                    child: Text('Asignada'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'confirmada',
                                    child: Text('Confirmada'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'cancelada',
                                    child: Text('Cancelada'),
                                  ),
                                ],
                                onChanged: (value) {
                                  if (value != null) {
                                    setState(() {
                                      _filtroEstado = value;
                                    });
                                  }
                                },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  // Lista de eventos
                  Expanded(
                    child:
                        _eventosFiltrados.isEmpty
                            ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.event_busy,
                                    size: 64,
                                    color: Colors.grey[400],
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    'No tienes eventos asignados',
                                    style: TextStyle(
                                      fontSize: 18,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Las ONGs te asignarán eventos para colaborar',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[500],
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                ],
                              ),
                            )
                            : RefreshIndicator(
                              onRefresh: _loadMisEventos,
                              child: ListView.builder(
                                padding: const EdgeInsets.all(8),
                                itemCount: _eventosFiltrados.length,
                                itemBuilder: (context, index) {
                                  final eventoData =
                                      _eventosFiltrados[index]
                                          as Map<String, dynamic>;
                                  return _buildEventoCard(eventoData);
                                },
                              ),
                            ),
                  ),
                ],
              ),
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 1, userType: userType);
        },
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

    final tipoRelacion =
        eventoData['tipo_relacion'] as String? ?? 'colaboradora';
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
      child: InkWell(
        onTap:
            eventoId != null
                ? () {
                  Navigator.push(
                    context,
                    NavigationHelper.slideRightRoute(
                      EventoDetailScreen(eventoId: eventoId),
                    ),
                  ).then((_) => _loadMisEventos());
                }
                : null,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen principal
            if (imagenPrincipal != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(4),
                ),
                child: CachedNetworkImage(
                  imageUrl:
                      ImageHelper.buildImageUrl(imagenPrincipal) ??
                      imagenPrincipal,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorWidget:
                      (context, url, error) => Container(
                        height: 200,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported, size: 48),
                      ),
                ),
              ),
            Padding(
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
                          color:
                              tipoRelacion == 'patrocinadora'
                                  ? Colors.purple.withOpacity(0.2)
                                  : Colors.blue.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          tipoRelacion == 'patrocinadora'
                              ? 'Patrocinadora'
                              : 'Colaboradora',
                          style: TextStyle(
                            fontSize: 12,
                            color:
                                tipoRelacion == 'patrocinadora'
                                    ? Colors.purple[700]
                                    : Colors.blue[700],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
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
                    ],
                  ),
                  const SizedBox(height: 8),
                  if (descripcion != null && descripcion.isNotEmpty) ...[
                    Text(
                      descripcion,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
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
                    const SizedBox(height: 4),
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
                    const SizedBox(height: 4),
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
                    const SizedBox(height: 4),
                  ],
                  if (nombreOng != null) ...[
                    Row(
                      children: [
                        Icon(Icons.business, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          nombreOng,
                          style: TextStyle(color: Colors.grey[600]),
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
                            color: Colors.blue[700],
                          ),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              'Tipo: $tipoColaboracion',
                              style: TextStyle(
                                color: Colors.blue[700],
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
                  // Botón de confirmar si está asignada
                  if (estadoParticipacion == 'asignada' ||
                      estadoParticipacion == null) ...[
                    const SizedBox(height: 8),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed:
                            eventoId != null
                                ? () => _confirmarParticipacion(eventoId)
                                : null,
                        icon: const Icon(Icons.check_circle),
                        label: const Text('Confirmar Participación'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF00A36C),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                      ),
                    ),
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

  String _formatFecha(String fechaStr) {
    try {
      final fecha = DateTime.parse(fechaStr);
      return '${fecha.day}/${fecha.month}/${fecha.year}';
    } catch (e) {
      return fechaStr;
    }
  }
}
