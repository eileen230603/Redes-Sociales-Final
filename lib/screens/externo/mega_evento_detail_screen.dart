import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../models/mega_evento.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../utils/image_helper.dart';
import '../../config/api_config.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
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
            backgroundColor: reaccionado ? AppColors.error : AppColors.grey600,
            duration: AppDuration.slow,
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
            backgroundColor: AppColors.error,
            duration: AppDuration.slow,
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
            debugPrint('✅ Total de reacciones cargado: $_totalReacciones');
          } else {
            debugPrint(
              '❌ Error al cargar total de reacciones: ${totalResult['error']}',
            );
          }
        });
      }
    } catch (e) {
      debugPrint('❌ Excepción al cargar total de reacciones: $e');
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
          backgroundColor: _participando ? AppColors.success : AppColors.warning,
          duration: AppDuration.slow,
        ),
      );
      _checkParticipacion(); // Verificar estado real
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al procesar participación',
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

    // Mostrar modal de compartir (igual que en Laravel)
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
        return AppBadge.success(label: label, icon: Icons.check_circle_outline);
      case 'en_curso':
        return AppBadge.info(label: label, icon: Icons.timelapse);
      case 'finalizado':
        return AppBadge.neutral(label: label, icon: Icons.flag_outlined);
      case 'cancelado':
        return AppBadge.error(label: label, icon: Icons.cancel_outlined);
      case 'planificacion':
      default:
        return AppBadge.neutral(label: label, icon: Icons.schedule);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detalle del Mega Evento')),
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
                    // Imagen principal
                    if (_megaEvento!.imagenes != null &&
                        _megaEvento!.imagenes!.isNotEmpty)
                      AppCard(
                        padding: EdgeInsets.zero,
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(AppRadius.card),
                          child: SizedBox(
                            height: 250,
                            width: double.infinity,
                            child: CachedNetworkImage(
                              imageUrl:
                                  ImageHelper.buildImageUrl(
                                    _megaEvento!.imagenes!.first.toString(),
                                  ) ??
                                  '',
                              fit: BoxFit.cover,
                              placeholder: (context, url) {
                                return Container(color: AppColors.grey100);
                              },
                              errorWidget: (context, url, error) {
                                return Container(
                                  color: AppColors.grey100,
                                  child: Center(
                                    child: AppIcon.lg(
                                      Icons.image_not_supported,
                                      color: AppColors.textTertiary,
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
                        ),
                      ),

                    Padding(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Título
                          Text(
                            _megaEvento!.titulo,
                            style: AppTypography.headlineSmall,
                          ),

                          const SizedBox(height: AppSpacing.xs),

                          // Badges
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

                          const SizedBox(height: AppSpacing.md),

                          // Descripción
                          if (_megaEvento!.descripcion != null &&
                              _megaEvento!.descripcion!.isNotEmpty) ...[
                            Text('Descripción', style: AppTypography.titleMedium),
                            const SizedBox(height: AppSpacing.xs),
                            Text(_megaEvento!.descripcion!, style: AppTypography.bodyLarge),
                            const SizedBox(height: AppSpacing.md),
                          ],

                          // Información del evento
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

                          const SizedBox(height: AppSpacing.lg),

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

                          // Botón de participar
                          SizedBox(
                            width: double.infinity,
                            child:
                                _participando
                                    ? AppButton.outlined(
                                      label: 'Cancelar participación',
                                      icon: Icons.cancel_outlined,
                                      onPressed:
                                          _isProcessingParticipacion
                                              ? null
                                              : _toggleParticipacion,
                                      isLoading: _isProcessingParticipacion,
                                    )
                                    : AppButton.primary(
                                      label: 'Participar en este Mega Evento',
                                      icon: Icons.person_add_alt_1,
                                      onPressed:
                                          _isProcessingParticipacion
                                              ? null
                                              : _toggleParticipacion,
                                      isLoading: _isProcessingParticipacion,
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
