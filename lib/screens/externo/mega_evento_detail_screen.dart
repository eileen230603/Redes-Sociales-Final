import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../models/mega_evento.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../utils/image_helper.dart';
import '../../config/api_config.dart';
import 'package:cached_network_image/cached_network_image.dart';

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
  bool _participando = false;
  bool _isProcessingParticipacion = false;
  int _totalCompartidos = 0;
  bool _isLoadingCompartidos = false;

  @override
  void initState() {
    super.initState();
    _loadMegaEvento();
    _checkReaccion();
    _checkParticipacion();
    _loadTotalCompartidos();
    // Asegurar que el total de reacciones se carga
    _loadTotalReacciones();
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
    final isAuthenticated = await AuthHelper.isAuthenticated();

    if (isAuthenticated) {
      // Para usuarios autenticados, usar verificar que ya devuelve el total
      final result = await ApiService.verificarReaccionMegaEvento(
        widget.megaEventoId,
      );
      if (mounted) {
        setState(() {
          if (result['success'] == true) {
            _reaccionado = result['reaccionado'] as bool? ?? false;
            // El backend devuelve total_reacciones en la respuesta de verificar
            final total = result['total_reacciones'] as int?;
            if (total != null) {
              _totalReacciones = total;
            }
          }
        });

        // Si no se obtuvo el total o falló, cargarlo por separado
        if (result['total_reacciones'] == null || result['success'] != true) {
          await _loadTotalReacciones();
        }
      }
    } else {
      // Para usuarios no autenticados, obtener el total por separado
      await _loadTotalReacciones();
    }
  }

  Future<void> _checkParticipacion() async {
    final isAuthenticated = await AuthHelper.isAuthenticated();

    if (!isAuthenticated) {
      return;
    }

    final result = await ApiService.verificarParticipacionMegaEvento(
      widget.megaEventoId,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _participando = result['participa'] as bool? ?? false;
      });
    }
  }

  Future<void> _toggleReaccion() async {
    if (_isProcessingReaccion) return;

    setState(() {
      _isProcessingReaccion = true;
    });

    final isAuthenticated = await AuthHelper.isAuthenticated();

    final result =
        isAuthenticated
            ? await ApiService.toggleReaccionMegaEvento(widget.megaEventoId)
            : await ApiService.reaccionarMegaEventoPublico(widget.megaEventoId);

    if (!mounted) return;

    if (result['success'] == true) {
      // Actualizar estado de reacción
      final reaccionado = result['reaccionado'] as bool? ?? false;
      final totalReacciones = result['total_reacciones'] as int?;

      setState(() {
        _isProcessingReaccion = false;
        _reaccionado = reaccionado;

        // Actualizar total de reacciones directamente desde la respuesta
        if (totalReacciones != null) {
          _totalReacciones = totalReacciones;
        } else {
          // Si no viene en la respuesta, recargar el total
          _loadTotalReacciones();
        }
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              reaccionado ? '¡Reacción agregada! ❤️' : 'Reacción eliminada',
            ),
            backgroundColor: reaccionado ? Colors.red : Colors.grey,
            duration: const Duration(seconds: 2),
          ),
        );
      }
    } else {
      setState(() {
        _isProcessingReaccion = false;
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al procesar reacción',
            ),
            backgroundColor: Colors.red,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  Future<void> _loadTotalReacciones() async {
    try {
      final totalResult = await ApiService.getTotalReaccionesMegaEvento(
        widget.megaEventoId,
      );
      if (mounted) {
        setState(() {
          if (totalResult['success'] == true) {
            final total = totalResult['total_reacciones'] as int?;
            _totalReacciones = total ?? 0;
            print('✅ Total de reacciones cargado: $_totalReacciones');
          } else {
            print(
              '❌ Error al cargar total de reacciones: ${totalResult['error']}',
            );
          }
        });
      }
    } catch (e) {
      print('❌ Excepción al cargar total de reacciones: $e');
    }
  }

  Future<void> _toggleParticipacion() async {
    if (_isProcessingParticipacion) return;

    setState(() {
      _isProcessingParticipacion = true;
    });

    final result = await ApiService.participarMegaEvento(widget.megaEventoId);

    if (!mounted) return;

    setState(() {
      _isProcessingParticipacion = false;
      if (result['success'] == true) {
        _participando = !_participando;
      }
    });

    if (result['success'] == true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            _participando
                ? '¡Te has inscrito al mega evento!'
                : 'Inscripción cancelada',
          ),
          backgroundColor: _participando ? Colors.green : Colors.orange,
          duration: const Duration(seconds: 2),
        ),
      );
      _checkParticipacion(); // Verificar estado real
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar participación',
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

    // Mostrar modal de compartir (igual que en Laravel)
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detalle del Mega Evento')),
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
                    // Imagen principal
                    if (_megaEvento!.imagenes != null &&
                        _megaEvento!.imagenes!.isNotEmpty)
                      CachedNetworkImage(
                        imageUrl:
                            ImageHelper.buildImageUrl(
                              _megaEvento!.imagenes!.first.toString(),
                            ) ??
                            '',
                        height: 250,
                        width: double.infinity,
                        fit: BoxFit.cover,
                        placeholder:
                            (context, url) => Container(
                              height: 250,
                              color: Colors.grey[300],
                              child: const Center(
                                child: CircularProgressIndicator(),
                              ),
                            ),
                        errorWidget:
                            (context, url, error) => Container(
                              height: 250,
                              color: Colors.grey[300],
                              child: const Icon(Icons.image_not_supported),
                            ),
                      ),

                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Título
                          Text(
                            _megaEvento!.titulo,
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),

                          const SizedBox(height: 8),

                          // Badges
                          Wrap(
                            spacing: 8,
                            children: [
                              if (_megaEvento!.categoria != null)
                                Chip(
                                  label: Text(_megaEvento!.categoria!),
                                  backgroundColor: Colors.blue[100],
                                ),
                              Chip(
                                label: Text(_megaEvento!.estado),
                                backgroundColor: Colors.grey[200],
                              ),
                              if (_megaEvento!.esPublico)
                                const Chip(
                                  label: Text('Público'),
                                  backgroundColor: Colors.green,
                                  labelStyle: TextStyle(color: Colors.white),
                                ),
                            ],
                          ),

                          const SizedBox(height: 16),

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
                            Text(_megaEvento!.descripcion!),
                            const SizedBox(height: 16),
                          ],

                          // Información del evento
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

                          const SizedBox(height: 24),

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
                                    backgroundColor: Colors.blue,
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),

                          const SizedBox(height: 16),

                          // Botón de participar
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed:
                                  _isProcessingParticipacion
                                      ? null
                                      : _toggleParticipacion,
                              icon: Icon(
                                _participando
                                    ? Icons.check_circle
                                    : Icons.person_add,
                                color: Colors.white,
                              ),
                              label: Text(
                                _participando
                                    ? 'Ya estás participando'
                                    : 'Participar en este Mega Evento',
                                style: const TextStyle(color: Colors.white),
                              ),
                              style: ElevatedButton.styleFrom(
                                backgroundColor:
                                    _participando
                                        ? Colors.green
                                        : Colors.orange,
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                              ),
                            ),
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
      final isAuthenticated = await AuthHelper.isAuthenticated();

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
    final isAuthenticated = await AuthHelper.isAuthenticated();

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
