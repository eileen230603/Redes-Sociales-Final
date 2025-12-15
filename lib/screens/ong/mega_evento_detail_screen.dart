import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../models/mega_evento.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
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
          backgroundColor: _reaccionado ? AppColors.error : AppColors.grey600,
          duration: AppDuration.slow,
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar reacción',
          ),
          backgroundColor: AppColors.error,
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
          backgroundColor: AppColors.warning,
        ),
      );
      return;
    }

    _mostrarModalCompartir();
  }

  void _mostrarModalCompartir() {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppColors.black.withOpacity(0),
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
          AppIcon.sm(Icons.favorite, color: AppColors.error),
          const SizedBox(width: AppSpacing.xs),
          Text(
            '${_usuariosQueReaccionaron.length} Reacciones',
            style: AppTypography.titleMedium,
          ),
        ],
      ),
      content: SizedBox(
        width: double.maxFinite,
        child:
            _isLoadingUsuariosReaccion
                ? SizedBox(height: 240, child: LoadingState.list())
                : _usuariosQueReaccionaron.isEmpty
                ? Text('No hay reacciones aún', style: AppTypography.bodySecondary)
                : ListView.builder(
                  shrinkWrap: true,
                  itemCount: _usuariosQueReaccionaron.length,
                  itemBuilder: (context, index) {
                    final usuario =
                        _usuariosQueReaccionaron[index] as Map<String, dynamic>;
                    final nombre =
                        usuario['nombre'] as String? ??
                        usuario['user_name'] as String? ??
                        'Usuario';
                    final email = usuario['email'] as String?;
                    final initials = _initialsFromName(nombre);

                    return AppListTile(
                      leading: AppAvatar.sm(
                        initials: initials,
                        backgroundColor: AppColors.grey200,
                        foregroundColor: AppColors.textSecondary,
                      ),
                      title: nombre,
                      subtitle: (email == null || email.isEmpty) ? null : email,
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

  String _initialsFromName(String name) {
    final parts =
        name
            .trim()
            .split(RegExp(r'\s+'))
            .where((p) => p.isNotEmpty)
            .toList();
    if (parts.isEmpty) return '';
    final first = parts[0].substring(0, 1);
    if (parts.length > 1) return (first + parts[1].substring(0, 1)).toUpperCase();
    if (parts[0].length > 1) return (first + parts[0].substring(1, 2)).toUpperCase();
    return first.toUpperCase();
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
              ? SkeletonLoader.eventDetail()
              : _error != null
              ? ErrorView.serverError(onRetry: _loadMegaEvento, errorDetails: _error)
              : _megaEvento == null
              ? ErrorView.notFound()
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
                              AppColors.primary.withOpacity(0.3),
                              AppColors.accent.withOpacity(0.6),
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
                                    color: AppColors.grey100,
                                  ),
                              errorWidget:
                                  (context, url, error) => Container(
                                    color: AppColors.grey100,
                                    child: Center(
                                      child: AppIcon.lg(
                                        Icons.image_not_supported,
                                        color: AppColors.textTertiary,
                                      ),
                                    ),
                                  ),
                            ),
                            Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  begin: Alignment.topCenter,
                                  end: Alignment.bottomCenter,
                                  colors: [
                                    AppColors.primary.withOpacity(0.3),
                                    AppColors.accent.withOpacity(0.6),
                                  ],
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              left: 0,
                              right: 0,
                              child: Container(
                                padding: const EdgeInsets.all(AppSpacing.lg),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _megaEvento!.titulo,
                                      style: AppTypography.headlineMedium.copyWith(
                                        color: AppColors.textOnPrimary,
                                      ),
                                    ),
                                    const SizedBox(height: AppSpacing.sm),
                                    Wrap(
                                      spacing: AppSpacing.sm,
                                      runSpacing: AppSpacing.xs,
                                      children: [
                                        if (_megaEvento!.categoria != null)
                                          AppBadge.neutral(
                                            label: _megaEvento!.categoria!,
                                            icon: Icons.category_outlined,
                                          ),
                                        _buildEstadoBadge(_megaEvento!.estado),
                                        if (_megaEvento!.esPublico)
                                          AppBadge.primary(
                                            label: 'Público',
                                            icon: Icons.public,
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
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Botones de acción
                          Row(
                            children: [
                              Expanded(
                                child: AppButton.secondary(
                                  label: '$_totalReacciones',
                                  icon:
                                      _reaccionado
                                          ? Icons.favorite
                                          : Icons.favorite_border,
                                  onPressed:
                                      _isProcessingReaccion
                                          ? null
                                          : _toggleReaccion,
                                  isLoading: _isProcessingReaccion,
                                ),
                              ),
                              const SizedBox(width: AppSpacing.sm),
                              Expanded(
                                child: AppButton.secondary(
                                  onPressed:
                                      _isLoadingCompartidos
                                          ? null
                                          : _compartirMegaEvento,
                                  icon: Icons.share_outlined,
                                  label:
                                      _isLoadingCompartidos
                                          ? '...'
                                          : '$_totalCompartidos',
                                ),
                              ),
                            ],
                          ),

                          const SizedBox(height: AppSpacing.md),

                          Row(
                            children: [
                              Expanded(
                                child: AppButton.primary(
                                  label: 'Seguimiento',
                                  icon: Icons.track_changes,
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
                                ),
                              ),
                              const SizedBox(width: AppSpacing.sm),
                              Expanded(
                                child: AppButton.outlined(
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
                                  icon: Icons.edit,
                                  label: 'Editar',
                                ),
                              ),
                            ],
                          ),

                          const SizedBox(height: AppSpacing.lg),

                          // Descripción
                          if (_megaEvento!.descripcion != null &&
                              _megaEvento!.descripcion!.isNotEmpty) ...[
                            Text('Descripción', style: AppTypography.titleMedium),
                            const SizedBox(height: AppSpacing.xs),
                            Text(
                              _megaEvento!.descripcion!,
                              style: AppTypography.bodyLarge,
                            ),
                            const SizedBox(height: AppSpacing.lg),
                          ],

                          // Información del mega evento
                          Text(
                            'Información del Mega Evento',
                            style: AppTypography.titleMedium,
                          ),
                          const SizedBox(height: AppSpacing.sm),

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
                            const SizedBox(height: AppSpacing.lg),
                            Text(
                              'Ubicación en el Mapa',
                              style: AppTypography.titleMedium,
                            ),
                            const SizedBox(height: AppSpacing.sm),
                            AppCard(
                              padding: EdgeInsets.zero,
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(
                                  AppRadius.card,
                                ),
                                child: SizedBox(
                                  height: 300,
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
                                            child: AppIcon.xl(
                                              Icons.location_on,
                                              color: AppColors.error,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ],

                          // Galería de imágenes
                          if (_megaEvento!.imagenes != null &&
                              _megaEvento!.imagenes!.length > 1) ...[
                            const SizedBox(height: AppSpacing.lg),
                            Text(
                              'Galería de Imágenes',
                              style: AppTypography.titleMedium,
                            ),
                            const SizedBox(height: AppSpacing.sm),
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

                                  return Padding(
                                    padding: const EdgeInsets.only(
                                      right: AppSpacing.sm,
                                    ),
                                    child: SizedBox(
                                      width: 300,
                                      child: AppCard(
                                        elevated: true,
                                        padding: EdgeInsets.zero,
                                        child: ClipRRect(
                                          borderRadius: BorderRadius.circular(
                                            AppRadius.card,
                                          ),
                                          child: CachedNetworkImage(
                                            imageUrl: imageUrl,
                                            fit: BoxFit.cover,
                                            placeholder: (context, url) {
                                              return Container(
                                                color: AppColors.grey100,
                                              );
                                            },
                                            errorWidget: (context, url, error) {
                                              return Container(
                                                color: AppColors.grey100,
                                                child: Center(
                                                  child: AppIcon.lg(
                                                    Icons.image_not_supported,
                                                    color:
                                                        AppColors.textTertiary,
                                                  ),
                                                ),
                                              );
                                            },
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
                            const SizedBox(height: AppSpacing.lg),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  'Reacciones',
                                  style: AppTypography.titleMedium,
                                ),
                                AppButton.text(
                                  onPressed: _cargarUsuariosQueReaccionaron,
                                  icon: Icons.people_outline,
                                  label: 'Ver',
                                ),
                              ],
                            ),
                            const SizedBox(height: AppSpacing.xs),
                            Text(
                              'Usuarios que han marcado este mega evento como favorito con un corazón.',
                              style: AppTypography.bodySecondary,
                            ),
                          ],

                          // Información adicional
                          const SizedBox(height: AppSpacing.lg),
                          Text(
                            'Información Adicional',
                            style: AppTypography.titleMedium,
                          ),
                          const SizedBox(height: AppSpacing.sm),

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
      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          AppIcon.sm(icon, color: AppColors.textSecondary),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: AppTypography.labelMedium,
                ),
                const SizedBox(height: AppSpacing.xxs),
                Text(
                  value,
                  style: AppTypography.bodyLarge,
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

  Widget _buildEstadoBadge(String estado) {
    final label = _getEstadoText(estado);
    switch (estado) {
      case 'activo':
        return AppBadge.success(
          label: label,
          icon: Icons.check_circle_outline,
        );
      case 'en_curso':
        return AppBadge.info(
          label: label,
          icon: Icons.timelapse,
        );
      case 'finalizado':
        return AppBadge.neutral(
          label: label,
          icon: Icons.flag_outlined,
        );
      case 'cancelado':
        return AppBadge.error(
          label: label,
          icon: Icons.cancel_outlined,
        );
      case 'planificacion':
      default:
        return AppBadge.neutral(
          label: label,
          icon: Icons.schedule,
        );
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
          backgroundColor: AppColors.success,
          duration: AppDuration.slow,
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al copiar: ${e.toString()}'),
          backgroundColor: AppColors.error,
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
        color: AppColors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(AppRadius.modal)),
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
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: const BoxDecoration(
                border: Border(
                  bottom: BorderSide(color: AppColors.borderLight, width: 1),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Compartir', style: AppTypography.titleLarge),
                  IconButton(
                    icon: AppIcon.md(Icons.close),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),

            // Contenido
            Padding(
              padding: const EdgeInsets.all(AppSpacing.xl),
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
                          backgroundColor: AppColors.grey100,
                          foregroundColor: AppColors.textPrimary,
                          onTap: _copiarEnlace,
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Código QR
                      Expanded(
                        child: _buildOpcionCompartir(
                          icon: Icons.qr_code,
                          label: 'Código QR',
                          backgroundColor: AppColors.primary,
                          foregroundColor: AppColors.textOnPrimary,
                          onTap: _mostrarCodigoQR,
                        ),
                      ),
                    ],
                  ),

                  // Contenedor para el QR
                  if (_mostrarQR) ...[
                    const SizedBox(height: 24),
                    AppCard(
                      elevated: true,
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        children: [
                          QrImageView(
                            data: widget.megaEventoUrl,
                            version: QrVersions.auto,
                            size: 250.0,
                            backgroundColor: AppColors.white,
                          ),
                          const SizedBox(height: 12),
                          Text(
                            'Escanea este código para acceder al mega evento',
                            textAlign: TextAlign.center,
                            style: AppTypography.bodySecondary,
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
    required Color backgroundColor,
    required Color foregroundColor,
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
              color: backgroundColor,
              borderRadius: BorderRadius.circular(16),
              boxShadow: AppElevation.cardShadow,
            ),
            child: Icon(icon, size: 32, color: foregroundColor),
          ),
          const SizedBox(height: 12),
          Text(
            label,
            style: AppTypography.labelLarge,
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}
