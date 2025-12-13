import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../models/mega_evento.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../utils/image_helper.dart';
import '../../config/api_config.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'mega_evento_seguimiento_screen.dart';
import 'editar_mega_evento_screen.dart';

class MegaEventoDetailScreen extends StatefulWidget {
  final int megaEventoId;

  const MegaEventoDetailScreen({super.key, required this.megaEventoId});

  @override
  State<MegaEventoDetailScreen> createState() => _MegaEventoDetailScreenState();
}

class _MegaEventoDetailScreenState extends State<MegaEventoDetailScreen> {
  MegaEvento? _megaEvento;
  bool _isLoading = true;
  String? _error;
  bool _reaccionado = false;
  int _totalReacciones = 0;
  bool _isProcessingReaccion = false;
  List<dynamic> _usuariosQueReaccionaron = [];
  bool _isLoadingUsuariosReaccion = false;
  int _totalCompartidos = 0;
  bool _isLoadingCompartidos = false;

  @override
  void initState() {
    super.initState();
    _loadMegaEvento();
    _checkReaccion();
    _loadTotalCompartidos();
  }

  Future<void> _loadMegaEvento() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getMegaEventoDetalle(widget.megaEventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _megaEvento = MegaEvento.fromJson(
          result['mega_evento'] as Map<String, dynamic>,
        );
        _checkReaccion();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar mega evento';
      }
    });
  }

  Future<void> _checkReaccion() async {
    final result = await ApiService.verificarReaccionMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      if (result['success'] == true) {
        _reaccionado = result['reaccionado'] as bool? ?? false;
      }
    });

    final totalResult = await ApiService.getTotalReaccionesMegaEvento(
      widget.megaEventoId,
    );
    if (totalResult['success'] == true && mounted) {
      setState(() {
        _totalReacciones = totalResult['total_reacciones'] as int? ?? 0;
      });
    }
  }

  Future<void> _toggleReaccion() async {
    if (_isProcessingReaccion) return;

    setState(() {
      _isProcessingReaccion = true;
    });

    final result = await ApiService.toggleReaccionMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      _isProcessingReaccion = false;
      if (result['success'] == true) {
        _reaccionado = result['reaccionado'] as bool? ?? false;
        _totalReacciones = result['total_reacciones'] as int? ?? 0;
      }
    });

    if (result['success'] == true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            _reaccionado ? '¡Reacción agregada! ❤️' : 'Reacción eliminada',
          ),
          backgroundColor: _reaccionado ? Colors.red : Colors.grey,
          duration: const Duration(seconds: 2),
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar reacción',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _loadTotalCompartidos() async {
    setState(() {
      _isLoadingCompartidos = true;
    });

    final result = await ApiService.getTotalCompartidosMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      _isLoadingCompartidos = false;
      if (result['success'] == true) {
        _totalCompartidos = result['total_compartidos'] as int? ?? 0;
      }
    });
  }

  Future<void> _compartirMegaEvento() async {
    if (_megaEvento == null) return;

    // Verificar si el mega evento está finalizado
    final megaEventoFinalizado = _megaEvento!.estaFinalizado;

    if (megaEventoFinalizado) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Este mega evento fue finalizado. Ya no se puede compartir.',
          ),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    _mostrarModalCompartir();
  }

  void _mostrarModalCompartir() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder:
          (context) => _ModalCompartirMegaEvento(
            megaEventoId: widget.megaEventoId,
            megaEventoTitulo: _megaEvento?.titulo ?? 'Mega Evento',
            megaEventoUrl: _getMegaEventoUrl(),
            onCompartido: () {
              _loadTotalCompartidos();
            },
          ),
    );
  }

  String _getMegaEventoUrl() {
    final baseUrl = ApiConfig.baseUrl.replaceAll('/api', '');
    return '$baseUrl/mega-evento/${widget.megaEventoId}/qr';
  }

  Future<void> _cargarUsuariosQueReaccionaron() async {
    setState(() {
      _isLoadingUsuariosReaccion = true;
    });

    final result = await ApiService.usuariosQueReaccionaronMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    setState(() {
      _isLoadingUsuariosReaccion = false;
      if (result['success'] == true) {
        _usuariosQueReaccionaron = result['usuarios'] as List? ?? [];
      }
    });

    if (result['success'] == true && mounted) {
      showDialog(
        context: context,
        builder: (context) => _buildDialogUsuariosReaccion(),
      );
    }
  }

  Widget _buildDialogUsuariosReaccion() {
    return AlertDialog(
      title: Row(
        children: [
          const Icon(Icons.favorite, color: Colors.red),
          const SizedBox(width: 8),
          Text('${_usuariosQueReaccionaron.length} Reacciones'),
        ],
      ),
      content: SizedBox(
        width: double.maxFinite,
        child:
            _isLoadingUsuariosReaccion
                ? const Center(child: CircularProgressIndicator())
                : _usuariosQueReaccionaron.isEmpty
                ? const Text('No hay reacciones aún')
                : ListView.builder(
                  shrinkWrap: true,
                  itemCount: _usuariosQueReaccionaron.length,
                  itemBuilder: (context, index) {
                    final usuario =
                        _usuariosQueReaccionaron[index] as Map<String, dynamic>;
                    return ListTile(
                      leading: const CircleAvatar(child: Icon(Icons.person)),
                      title: Text(
                        usuario['nombre'] as String? ??
                            usuario['user_name'] as String? ??
                            'Usuario',
                      ),
                      subtitle: Text(usuario['email'] as String? ?? ''),
                    );
                  },
                ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Cerrar'),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: const Text('Detalle del Mega Evento'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () async {
              final result = await Navigator.push(
                context,
                MaterialPageRoute(
                  builder:
                      (context) => EditarMegaEventoScreen(
                        megaEventoId: widget.megaEventoId,
                      ),
                ),
              );

              if (result == true) {
                _loadMegaEvento();
              }
            },
            tooltip: 'Editar',
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
                      style: const TextStyle(color: Colors.red),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadMegaEvento,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _megaEvento == null
              ? const Center(child: Text('No se encontró el mega evento'))
              : SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Banner con imagen principal
                    if (_megaEvento!.imagenes != null &&
                        _megaEvento!.imagenes!.isNotEmpty)
                      Container(
                        height: 300,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              const Color(0xFF0C2B44).withOpacity(0.3),
                              const Color(0xFF00A36C).withOpacity(0.6),
                            ],
                          ),
                        ),
                        child: Stack(
                          children: [
                            CachedNetworkImage(
                              imageUrl:
                                  ImageHelper.buildImageUrl(
                                    _megaEvento!.imagenes!.first.toString(),
                                  ) ??
                                  '',
                              width: double.infinity,
                              height: double.infinity,
                              fit: BoxFit.cover,
                              placeholder:
                                  (context, url) => Container(
                                    color: Colors.grey[300],
                                    child: const Center(
                                      child: CircularProgressIndicator(),
                                    ),
                                  ),
                              errorWidget:
                                  (context, url, error) => Container(
                                    color: Colors.grey[300],
                                    child: const Icon(
                                      Icons.image_not_supported,
                                      size: 48,
                                      color: Colors.grey,
                                    ),
                                  ),
                            ),
                            Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  begin: Alignment.topCenter,
                                  end: Alignment.bottomCenter,
                                  colors: [
                                    const Color(0xFF0C2B44).withOpacity(0.3),
                                    const Color(0xFF00A36C).withOpacity(0.6),
                                  ],
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              left: 0,
                              right: 0,
                              child: Container(
                                padding: const EdgeInsets.all(24),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _megaEvento!.titulo,
                                      style: const TextStyle(
                                        fontSize: 28,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white,
                                        shadows: [
                                          Shadow(
                                            offset: Offset(2, 2),
                                            blurRadius: 4,
                                            color: Colors.black45,
                                          ),
                                        ],
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    Wrap(
                                      spacing: 8,
                                      children: [
                                        if (_megaEvento!.categoria != null)
                                          Chip(
                                            label: Text(
                                              _megaEvento!.categoria!,
                                              style: const TextStyle(
                                                color: Colors.white,
                                                fontSize: 12,
                                              ),
                                            ),
                                            backgroundColor: const Color(
                                              0xFF00A36C,
                                            ),
                                          ),
                                        Chip(
                                          label: Text(
                                            _getEstadoText(_megaEvento!.estado),
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontSize: 12,
                                            ),
                                          ),
                                          backgroundColor: _getEstadoColor(
                                            _megaEvento!.estado,
                                          ),
                                        ),
                                        if (_megaEvento!.esPublico)
                                          const Chip(
                                            label: Text(
                                              'Público',
                                              style: TextStyle(
                                                color: Colors.white,
                                                fontSize: 12,
                                              ),
                                            ),
                                            backgroundColor: Color(0xFF0C2B44),
                                          ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Botones de acción
                          Row(
                            children: [
                              Expanded(
                                child: ElevatedButton.icon(
                                  onPressed:
                                      _isProcessingReaccion
                                          ? null
                                          : _toggleReaccion,
                                  icon: Icon(
                                    _reaccionado
                                        ? Icons.favorite
                                        : Icons.favorite_border,
                                    color: Colors.white,
                                  ),
                                  label: Text(
                                    '$_totalReacciones',
                                    style: const TextStyle(color: Colors.white),
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.red,
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: ElevatedButton.icon(
                                  onPressed:
                                      _isLoadingCompartidos
                                          ? null
                                          : _compartirMegaEvento,
                                  icon: const Icon(
                                    Icons.share,
                                    color: Colors.white,
                                  ),
                                  label: Text(
                                    _isLoadingCompartidos
                                        ? '...'
                                        : '$_totalCompartidos',
                                    style: const TextStyle(color: Colors.white),
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF0C2B44),
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),

                          const SizedBox(height: 16),

                          Row(
                            children: [
                              Expanded(
                                child: ElevatedButton.icon(
                                  onPressed: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                        builder:
                                            (context) =>
                                                MegaEventoSeguimientoScreen(
                                                  megaEventoId:
                                                      widget.megaEventoId,
                                                ),
                                      ),
                                    );
                                  },
                                  icon: const Icon(
                                    Icons.track_changes,
                                    color: Colors.white,
                                  ),
                                  label: const Text(
                                    'Seguimiento',
                                    style: TextStyle(color: Colors.white),
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF0C2B44),
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: OutlinedButton.icon(
                                  onPressed: () async {
                                    final result = await Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                        builder:
                                            (context) => EditarMegaEventoScreen(
                                              megaEventoId: widget.megaEventoId,
                                            ),
                                      ),
                                    );
                                    if (result == true) {
                                      _loadMegaEvento();
                                    }
                                  },
                                  icon: const Icon(Icons.edit),
                                  label: const Text('Editar'),
                                  style: OutlinedButton.styleFrom(
                                    foregroundColor: const Color(0xFF00A36C),
                                    side: const BorderSide(
                                      color: Color(0xFF00A36C),
                                    ),
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),

                          const SizedBox(height: 24),

                          // Descripción
                          if (_megaEvento!.descripcion != null &&
                              _megaEvento!.descripcion!.isNotEmpty) ...[
                            const Text(
                              'Descripción',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              _megaEvento!.descripcion!,
                              style: const TextStyle(fontSize: 16),
                            ),
                            const SizedBox(height: 24),
                          ],

                          // Información del mega evento
                          const Text(
                            'Información del Mega Evento',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 12),

                          _buildInfoRow(
                            Icons.calendar_today,
                            'Fecha de Inicio',
                            _formatDateTime(_megaEvento!.fechaInicio),
                          ),
                          _buildInfoRow(
                            Icons.calendar_today,
                            'Fecha de Fin',
                            _formatDateTime(_megaEvento!.fechaFin),
                          ),

                          if (_megaEvento!.ubicacion != null &&
                              _megaEvento!.ubicacion!.isNotEmpty)
                            _buildInfoRow(
                              Icons.location_on,
                              'Ubicación',
                              _megaEvento!.ubicacion!,
                            ),

                          if (_megaEvento!.capacidadMaxima != null)
                            _buildInfoRow(
                              Icons.people,
                              'Capacidad Máxima',
                              '${_megaEvento!.capacidadMaxima} personas',
                            ),

                          // Mapa si hay coordenadas
                          if (_megaEvento!.lat != null &&
                              _megaEvento!.lng != null) ...[
                            const SizedBox(height: 24),
                            const Text(
                              'Ubicación en el Mapa',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            Container(
                              height: 300,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: Colors.grey[300]!),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(12),
                                child: FlutterMap(
                                  options: MapOptions(
                                    initialCenter: LatLng(
                                      _megaEvento!.lat!,
                                      _megaEvento!.lng!,
                                    ),
                                    initialZoom: 15.0,
                                  ),
                                  children: [
                                    TileLayer(
                                      urlTemplate:
                                          'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                      userAgentPackageName: 'com.example.app',
                                    ),
                                    MarkerLayer(
                                      markers: [
                                        Marker(
                                          point: LatLng(
                                            _megaEvento!.lat!,
                                            _megaEvento!.lng!,
                                          ),
                                          width: 80,
                                          height: 80,
                                          child: const Icon(
                                            Icons.location_on,
                                            color: Colors.red,
                                            size: 48,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],

                          // Galería de imágenes
                          if (_megaEvento!.imagenes != null &&
                              _megaEvento!.imagenes!.length > 1) ...[
                            const SizedBox(height: 24),
                            const Text(
                              'Galería de Imágenes',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            SizedBox(
                              height: 200,
                              child: ListView.builder(
                                scrollDirection: Axis.horizontal,
                                itemCount: _megaEvento!.imagenes!.length,
                                itemBuilder: (context, index) {
                                  final imgPath =
                                      _megaEvento!.imagenes![index]
                                          ?.toString()
                                          .trim();
                                  if (imgPath == null || imgPath.isEmpty) {
                                    return const SizedBox.shrink();
                                  }

                                  final imageUrl = ImageHelper.buildImageUrl(
                                    imgPath,
                                  );
                                  if (imageUrl == null) {
                                    return const SizedBox.shrink();
                                  }

                                  return Container(
                                    width: 300,
                                    margin: const EdgeInsets.only(right: 12),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(12),
                                      boxShadow: [
                                        BoxShadow(
                                          color: Colors.black.withOpacity(0.1),
                                          blurRadius: 4,
                                          offset: const Offset(0, 2),
                                        ),
                                      ],
                                    ),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(12),
                                      child: CachedNetworkImage(
                                        imageUrl: imageUrl,
                                        fit: BoxFit.cover,
                                        placeholder:
                                            (context, url) => Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                child:
                                                    CircularProgressIndicator(),
                                              ),
                                            ),
                                        errorWidget:
                                            (context, url, error) => Container(
                                              color: Colors.grey[200],
                                              child: const Icon(
                                                Icons.image_not_supported,
                                                size: 48,
                                                color: Colors.grey,
                                              ),
                                            ),
                                      ),
                                    ),
                                  );
                                },
                              ),
                            ),
                          ],

                          // Reacciones
                          if (_totalReacciones > 0) ...[
                            const SizedBox(height: 24),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                const Text(
                                  'Reacciones y Favoritos',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                TextButton.icon(
                                  onPressed: _cargarUsuariosQueReaccionaron,
                                  icon: const Icon(Icons.refresh, size: 18),
                                  label: const Text('Actualizar'),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Usuarios que han marcado este mega evento como favorito con un corazón.',
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],

                          // Información adicional
                          const SizedBox(height: 24),
                          const Text(
                            'Información Adicional',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 12),

                          if (_megaEvento!.ongPrincipal != null)
                            _buildInfoRow(
                              Icons.business,
                              'ONG Organizadora',
                              _megaEvento!.ongPrincipal!['nombre_ong']
                                      as String? ??
                                  '-',
                            ),

                          if (_megaEvento!.fechaCreacion != null)
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Fecha de Creación',
                              _formatDateTime(_megaEvento!.fechaCreacion!),
                            ),

                          if (_megaEvento!.fechaActualizacion != null)
                            _buildInfoRow(
                              Icons.update,
                              'Última Actualización',
                              _formatDateTime(_megaEvento!.fechaActualizacion!),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
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
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
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

  String _getEstadoText(String estado) {
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
        return estado;
    }
  }

  Color _getEstadoColor(String estado) {
    switch (estado) {
      case 'planificacion':
        return Colors.grey;
      case 'activo':
        return const Color(0xFF00A36C);
      case 'en_curso':
        return const Color(0xFF0C2B44);
      case 'finalizado':
        return Colors.blue;
      case 'cancelado':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }
}

// Modal de compartir para mega eventos (igual que en Laravel)
class _ModalCompartirMegaEvento extends StatefulWidget {
  final int megaEventoId;
  final String megaEventoTitulo;
  final String megaEventoUrl;
  final VoidCallback onCompartido;

  const _ModalCompartirMegaEvento({
    required this.megaEventoId,
    required this.megaEventoTitulo,
    required this.megaEventoUrl,
    required this.onCompartido,
  });

  @override
  State<_ModalCompartirMegaEvento> createState() =>
      _ModalCompartirMegaEventoState();
}

class _ModalCompartirMegaEventoState extends State<_ModalCompartirMegaEvento> {
  bool _mostrarQR = false;

  Future<void> _copiarEnlace() async {
    try {
      await Clipboard.setData(ClipboardData(text: widget.megaEventoUrl));

      // Registrar compartido en backend con método 'link'
      final userData = await StorageService.getUserData();
      final isAuthenticated = userData != null;

      if (isAuthenticated) {
        await ApiService.compartirMegaEvento(
          widget.megaEventoId,
          metodo: 'link',
        );
      } else {
        await ApiService.compartirMegaEventoPublico(
          widget.megaEventoId,
          metodo: 'link',
        );
      }

      widget.onCompartido();

      if (!mounted) return;
      Navigator.of(context).pop(); // Cerrar modal
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('¡Enlace copiado al portapapeles!'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 2),
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al copiar: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _mostrarCodigoQR() async {
    // Registrar compartido en backend con método 'qr'
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (isAuthenticated) {
      await ApiService.compartirMegaEvento(widget.megaEventoId, metodo: 'qr');
    } else {
      await ApiService.compartirMegaEventoPublico(
        widget.megaEventoId,
        metodo: 'qr',
      );
    }

    widget.onCompartido();

    if (!mounted) return;
    setState(() {
      _mostrarQR = true;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Padding(
        padding: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                border: Border(
                  bottom: BorderSide(color: Color(0xFFF5F5F5), width: 1),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Compartir',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0C2B44),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),

            // Contenido
            Padding(
              padding: const EdgeInsets.all(32),
              child: Column(
                children: [
                  // Opciones de compartir
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      // Copiar enlace
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.link,
                          label: 'Copiar enlace',
                          color: const Color(0xFFF5F5F5),
                          iconColor: const Color(0xFF0C2B44),
                          onTap: _copiarEnlace,
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Código QR
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.qr_code,
                          label: 'Código QR',
                          color: const Color(0xFF0C2B44),
                          iconColor: Colors.white,
                          onTap: _mostrarCodigoQR,
                        ),
                      ),
                    ],
                  ),

                  // Contenedor para el QR
                  if (_mostrarQR) ...[
                    const SizedBox(height: 24),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          QrImageView(
                            data: widget.megaEventoUrl,
                            version: QrVersions.auto,
                            size: 250.0,
                            backgroundColor: Colors.white,
                          ),
                          const SizedBox(height: 12),
                          const Text(
                            'Escanea este código para acceder al mega evento',
                            textAlign: TextAlign.center,
                            style: TextStyle(fontSize: 14, color: Colors.grey),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOpcionCompartir({
    required IconData icon,
    required String label,
    required Color color,
    required Color iconColor,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: color,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Icon(icon, size: 32, color: iconColor),
          ),
          const SizedBox(height: 12),
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Color(0xFF333333),
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}
