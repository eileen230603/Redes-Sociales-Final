import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/bottom_nav_bar.dart';
import '../../services/storage_service.dart';
import '../../utils/navigation_helper.dart';
import 'mega_evento_detail_screen.dart';

class MisParticipacionesMegaEventosScreen extends StatefulWidget {
  const MisParticipacionesMegaEventosScreen({super.key});

  @override
  State<MisParticipacionesMegaEventosScreen> createState() =>
      _MisParticipacionesMegaEventosScreenState();
}

class _MisParticipacionesMegaEventosScreenState
    extends State<MisParticipacionesMegaEventosScreen> {
  List<dynamic> _megaEventos = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadMisParticipaciones();
  }

  Future<void> _loadMisParticipaciones() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.misParticipacionesMegaEventos();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _megaEventos = result['mega_eventos'] as List<dynamic>? ?? [];
      } else {
        _error =
            result['error'] as String? ?? 'Error al cargar participaciones';
      }
    });
  }

  String _getEstadoBadge(String? estado) {
    switch (estado) {
      case 'aprobada':
        return 'Aprobada';
      case 'pendiente':
        return 'Pendiente';
      case 'rechazada':
        return 'Rechazada';
      default:
        return 'Pendiente';
    }
  }

  Color _getEstadoColor(String? estado) {
    switch (estado) {
      case 'aprobada':
        return Colors.green;
      case 'pendiente':
        return Colors.orange;
      case 'rechazada':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(
        currentRoute: '/mis-participaciones-mega-eventos',
      ),
      appBar: AppBar(
        title: const Text('Mis Participaciones - Mega Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadMisParticipaciones,
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
                      onPressed: _loadMisParticipaciones,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _megaEventos.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
                    const SizedBox(height: 16),
                    Text(
                      'No tienes participaciones en mega eventos',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Explora los mega eventos disponibles',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadMisParticipaciones,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _megaEventos.length,
                  itemBuilder: (context, index) {
                    final megaEvento =
                        _megaEventos[index] as Map<String, dynamic>;
                    return _buildMegaEventoCard(megaEvento);
                  },
                ),
              ),
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 2, userType: userType);
        },
      ),
    );
  }

  Widget _buildMegaEventoCard(Map<String, dynamic> megaEvento) {
    final megaEventoId = megaEvento['mega_evento_id'] as int;
    final titulo = megaEvento['titulo'] as String? ?? 'Sin título';
    final descripcion = megaEvento['descripcion'] as String? ?? '';
    final fechaInicio = megaEvento['fecha_inicio'] as String?;
    final ubicacion = megaEvento['ubicacion'] as String?;
    final categoria = megaEvento['categoria'] as String?;
    final estadoParticipacion = megaEvento['estado_participacion'] as String?;
    final imagenes = megaEvento['imagenes'] as List<dynamic>? ?? [];
    final imagenPrincipal = imagenes.isNotEmpty ? imagenes[0].toString() : null;

    DateTime? fechaInicioDate;
    if (fechaInicio != null) {
      try {
        fechaInicioDate = DateTime.parse(fechaInicio);
      } catch (e) {
        // Ignorar error de parsing
      }
    }

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            NavigationHelper.slideRightRoute(
              MegaEventoDetailScreen(megaEventoId: megaEventoId),
            ),
          ).then((_) => _loadMisParticipaciones());
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen principal
            if (imagenPrincipal != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(4),
                ),
                child: Image.network(
                  imagenPrincipal,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder:
                      (context, error, stackTrace) => Container(
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
                          color: _getEstadoColor(
                            estadoParticipacion,
                          ).withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          _getEstadoBadge(estadoParticipacion),
                          style: TextStyle(
                            fontSize: 12,
                            color: _getEstadoColor(estadoParticipacion),
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  if (descripcion.isNotEmpty) ...[
                    Text(
                      descripcion,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (categoria != null) ...[
                    Row(
                      children: [
                        Icon(Icons.category, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          categoria,
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
                  if (ubicacion != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            ubicacion,
                            style: TextStyle(color: Colors.grey[600]),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Botones de QR y copiar ticket
                      Row(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.qr_code),
                            onPressed:
                                () => _mostrarTicketQR(context, megaEvento),
                            tooltip: 'Ver QR del ticket',
                            color: Theme.of(context).primaryColor,
                          ),
                          IconButton(
                            icon: const Icon(Icons.copy),
                            onPressed: () => _copiarTicket(context, megaEvento),
                            tooltip: 'Copiar código del ticket',
                            color: Theme.of(context).primaryColor,
                          ),
                        ],
                      ),
                      // Ver detalles
                      Row(
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

  String _getTicketCode(Map<String, dynamic> megaEvento) {
    final megaEventoId = megaEvento['mega_evento_id'] as int;
    final participacionId = megaEvento['participacion_id'] as int? ?? 0;
    // Generar código único basado en el ID de la participación o mega evento
    return 'MEG-${megaEventoId}-${participacionId > 0 ? participacionId : megaEventoId}';
  }

  void _mostrarTicketQR(BuildContext context, Map<String, dynamic> megaEvento) {
    final ticketCode = _getTicketCode(megaEvento);
    final titulo = megaEvento['titulo'] as String? ?? 'Mega Evento';

    showDialog(
      context: context,
      builder:
          (context) => Dialog(
            child: Container(
              constraints: const BoxConstraints(maxWidth: 400, maxHeight: 600),
              padding: const EdgeInsets.all(24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          'Ticket - $titulo',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close),
                        onPressed: () => Navigator.of(context).pop(),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey[300]!),
                    ),
                    child: QrImageView(
                      data: ticketCode,
                      version: QrVersions.auto,
                      size: 250.0,
                      backgroundColor: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.grey[100],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            'Código: $ticketCode',
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.copy, size: 20),
                          onPressed: () {
                            Clipboard.setData(ClipboardData(text: ticketCode));
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text('Código copiado al portapapeles'),
                                duration: Duration(seconds: 2),
                              ),
                            );
                          },
                          tooltip: 'Copiar código',
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () => Navigator.of(context).pop(),
                      child: const Text('Cerrar'),
                    ),
                  ),
                ],
              ),
            ),
          ),
    );
  }

  void _copiarTicket(BuildContext context, Map<String, dynamic> megaEvento) {
    final ticketCode = _getTicketCode(megaEvento);
    Clipboard.setData(ClipboardData(text: ticketCode));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Código del ticket copiado al portapapeles'),
        backgroundColor: Colors.green,
      ),
    );
  }
}
