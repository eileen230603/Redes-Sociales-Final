import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/breadcrumbs.dart';
import '../../models/evento.dart';
import '../../widgets/atoms/app_avatar.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
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
                style: TextButton.styleFrom(foregroundColor: AppColors.success),
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
          backgroundColor: AppColors.success,
          duration: AppDuration.slow,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al aprobar participación',
          ),
          backgroundColor: AppColors.error,
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
                style: TextButton.styleFrom(foregroundColor: AppColors.error),
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
          backgroundColor: AppColors.warning,
          duration: AppDuration.slow,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al rechazar participación',
          ),
          backgroundColor: AppColors.error,
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
            backgroundColor: AppColors.warning,
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
          backgroundColor: AppColors.success,
          duration: AppDuration.slow,
        ),
      );
      _loadParticipantes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al actualizar asistencia',
          ),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  Color _getColorEstado(String estado) {
    switch (estado.toLowerCase()) {
      case 'aprobada':
        return AppColors.success;
      case 'rechazada':
        return AppColors.error;
      case 'pendiente':
      default:
        return AppColors.warning;
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
                    ? SkeletonLoader.cardList()
                    : _error != null
                    ? ErrorView.serverError(
                      onRetry: _loadParticipantes,
                      errorDetails: _error,
                    )
                    : Column(
                      children: [
                        // Información del evento
                        Padding(
                          padding: const EdgeInsets.all(AppSpacing.md),
                          child: AppCard(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    AppIcon.md(Icons.event),
                                    const SizedBox(width: AppSpacing.sm),
                                    Expanded(
                                      child: Text(
                                        eventoTitulo,
                                        style: AppTypography.titleLarge,
                                      ),
                                    ),
                                  ],
                                ),
                                if (_evento != null) ...[
                                  const SizedBox(height: AppSpacing.xs),
                                  Text(
                                    'Total de participantes: ${_participantes.length}',
                                    style: AppTypography.bodySecondary,
                                  ),
                                ],
                              ],
                            ),
                          ),
                        ),

                        // Filtros
                        Padding(
                          padding: const EdgeInsets.symmetric(
                            horizontal: AppSpacing.md,
                          ),
                          child: AppCard(
                            child: Row(
                              children: [
                                AppIcon.sm(Icons.filter_list),
                                const SizedBox(width: AppSpacing.sm),
                                Text(
                                  'Filtrar por estado:',
                                  style: AppTypography.titleSmall,
                                ),
                                const SizedBox(width: AppSpacing.sm),
                                Expanded(
                                  child: SingleChildScrollView(
                                    scrollDirection: Axis.horizontal,
                                    child: Row(
                                      children: [
                                        _buildChipFiltro(null, 'Todos'),
                                        const SizedBox(width: AppSpacing.sm),
                                        ..._estados.entries.map(
                                          (entry) => Padding(
                                            padding: const EdgeInsets.only(
                                              right: AppSpacing.sm,
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
                        ),
                        const SizedBox(height: AppSpacing.sm),

                        // Lista de participantes
                        Expanded(
                          child:
                              _participantesFiltrados.isEmpty
                                  ? EmptyState(
                                    icon: Icons.people_outline,
                                    title: 'Sin participantes',
                                    message:
                                        _filtroEstado == null
                                            ? 'No hay participantes registrados.'
                                            : 'No hay participantes con estado "${_estados[_filtroEstado] ?? _filtroEstado}".',
                                  )
                                  : RefreshIndicator(
                                    onRefresh: _loadParticipantes,
                                    child: ListView.builder(
                                      padding: const EdgeInsets.all(AppSpacing.md),
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
      selectedColor: AppColors.primary.withOpacity(0.12),
      checkmarkColor: AppColors.primary,
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

    final estadoLabel = _estados[estado.toLowerCase()] ?? estado;

    final avatarUrl =
        (fotoPerfil == null || fotoPerfil.trim().isEmpty)
            ? null
            : ImageHelper.buildImageUrl(fotoPerfil);

    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
      child: AppCard(
        elevated: true,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                AppAvatar.lg(
                  imageUrl: avatarUrl,
                  initials: _initialsFromName(nombre),
                  backgroundColor: AppColors.grey200,
                  foregroundColor: AppColors.textSecondary,
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(nombre, style: AppTypography.titleMedium),
                      if (correo.isNotEmpty) ...[
                        const SizedBox(height: AppSpacing.xxs),
                        Text(correo, style: AppTypography.bodySecondary),
                      ],
                      const SizedBox(height: AppSpacing.sm),
                      Wrap(
                        spacing: AppSpacing.sm,
                        runSpacing: AppSpacing.xs,
                        children: [
                          _badgeForEstado(estado.toLowerCase(), estadoLabel),
                          if (esNoRegistrado)
                            AppBadge.warning(
                              label: 'No registrado',
                              icon: Icons.person_off_outlined,
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),

            if (telefono != null || direccion != null || fechaInscripcion != null) ...[
              const SizedBox(height: AppSpacing.md),
              const Divider(height: 1),
              const SizedBox(height: AppSpacing.md),
              if (telefono != null) _buildInfoRow(Icons.phone, 'Teléfono', telefono),
              if (direccion != null) ...[
                const SizedBox(height: AppSpacing.sm),
                _buildInfoRow(Icons.location_on, 'Dirección', direccion),
              ],
              if (fechaInscripcion != null) ...[
                const SizedBox(height: AppSpacing.sm),
                _buildInfoRow(
                  Icons.calendar_today,
                  'Fecha de inscripción',
                  fechaInscripcion,
                ),
              ],
            ],

            const SizedBox(height: AppSpacing.md),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    AppIcon.sm(Icons.how_to_reg),
                    const SizedBox(width: AppSpacing.sm),
                    Text('Asistencia', style: AppTypography.titleSmall),
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

            if (estado.toLowerCase() == 'pendiente') ...[
              const SizedBox(height: AppSpacing.md),
              const Divider(height: 1),
              const SizedBox(height: AppSpacing.md),
              Row(
                children: [
                  Expanded(
                    child: AppButton.outlined(
                      label: 'Rechazar',
                      icon: Icons.cancel_outlined,
                      onPressed: () => _rechazarParticipacion(
                        participacionId,
                        nombre,
                        esNoRegistrado: esNoRegistrado,
                      ),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: AppButton.primary(
                      label: 'Aprobar',
                      icon: Icons.check_circle_outline,
                      onPressed: () => _aprobarParticipacion(
                        participacionId,
                        nombre,
                        esNoRegistrado: esNoRegistrado,
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

  AppBadge _badgeForEstado(String estadoKey, String estadoLabel) {
    switch (estadoKey) {
      case 'aprobada':
        return AppBadge.success(label: estadoLabel, icon: Icons.check_circle_outline);
      case 'rechazada':
        return AppBadge.error(label: estadoLabel, icon: Icons.cancel_outlined);
      case 'pendiente':
      default:
        return AppBadge.warning(label: estadoLabel, icon: Icons.pending_outlined);
    }
  }

  String _initialsFromName(String name) {
    final parts =
        name
            .trim()
            .split(RegExp(r'\s+'))
            .where((p) => p.isNotEmpty)
            .toList();
    if (parts.isEmpty) return '?';
    final first = parts[0].substring(0, 1);
    if (parts.length > 1) return (first + parts[1].substring(0, 1)).toUpperCase();
    if (parts[0].length > 1) return (first + parts[0].substring(1, 2)).toUpperCase();
    return first.toUpperCase();
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        AppIcon.xs(icon, color: AppColors.textSecondary),
        const SizedBox(width: AppSpacing.sm),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTypography.labelMedium),
              const SizedBox(height: AppSpacing.xxs),
              Text(value, style: AppTypography.bodyLarge),
            ],
          ),
        ),
      ],
    );
  }
}
