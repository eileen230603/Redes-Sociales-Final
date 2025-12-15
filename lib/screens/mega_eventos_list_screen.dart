import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../models/mega_evento.dart';
import '../widgets/app_drawer.dart';
import '../widgets/atoms/app_badge.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
import '../utils/image_helper.dart';
import 'externo/mega_evento_detail_screen.dart';

class MegaEventosListScreen extends StatefulWidget {
  const MegaEventosListScreen({super.key});

  @override
  State<MegaEventosListScreen> createState() => _MegaEventosListScreenState();
}

class _MegaEventosListScreenState extends State<MegaEventosListScreen> {
  List<MegaEvento> _megaEventos = [];
  bool _isLoading = true;
  String? _error;
  String _filtroCategoria = 'todos';
  final TextEditingController _buscarController = TextEditingController();
  // Mapa para almacenar estado de reacciones por mega evento
  Map<int, bool> _megaEventosReaccionados = {}; // megaEventoId -> reaccionado
  Map<int, int> _totalReaccionesPorMegaEvento = {}; // megaEventoId -> total
  Map<int, bool> _megaEventosParticipando = {}; // megaEventoId -> participando

  @override
  void initState() {
    super.initState();
    _loadMegaEventos();
  }

  @override
  void dispose() {
    _buscarController.dispose();
    super.dispose();
  }

  Future<void> _loadMegaEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final result = await ApiService.getMegaEventosPublicos(
        categoria: _filtroCategoria != 'todos' ? _filtroCategoria : null,
        buscar:
            _buscarController.text.isNotEmpty ? _buscarController.text : null,
      );

      if (!mounted) return;

      setState(() {
        _isLoading = false;
        if (result['success'] == true) {
          final megaEventosData = result['mega_eventos'] as List<dynamic>;
          _megaEventos =
              megaEventosData
                  .map((e) => MegaEvento.fromJson(e as Map<String, dynamic>))
                  .toList();
          // Verificar reacciones y participación para cada mega evento
          _verificarReacciones();
          _verificarParticipaciones();
        } else {
          _error = result['error'] as String? ?? 'Error al cargar mega eventos';
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
        _error = 'Error de conexión: ${e.toString()}';
      });
    }
  }

  Future<void> _verificarReacciones() async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      // Para usuarios no autenticados, solo cargar totales
      for (final megaEvento in _megaEventos) {
        final totalResult = await ApiService.getTotalReaccionesMegaEvento(
          megaEvento.megaEventoId,
        );
        if (totalResult['success'] == true) {
          _totalReaccionesPorMegaEvento[megaEvento.megaEventoId] =
              totalResult['total_reacciones'] as int? ?? 0;
        }
      }
      if (mounted) setState(() {});
      return;
    }

    Map<int, bool> megaEventosReaccionadosMap = {};
    Map<int, int> totalReaccionesMap = {};

    for (final megaEvento in _megaEventos) {
      final reaccionResult = await ApiService.verificarReaccionMegaEvento(
        megaEvento.megaEventoId,
      );
      if (reaccionResult['success'] == true) {
        megaEventosReaccionadosMap[megaEvento.megaEventoId] =
            reaccionResult['reaccionado'] as bool? ?? false;
      }

      final totalResult = await ApiService.getTotalReaccionesMegaEvento(
        megaEvento.megaEventoId,
      );
      if (totalResult['success'] == true) {
        totalReaccionesMap[megaEvento.megaEventoId] =
            totalResult['total_reacciones'] as int? ?? 0;
      }
    }

    if (!mounted) return;

    setState(() {
      _megaEventosReaccionados = megaEventosReaccionadosMap;
      _totalReaccionesPorMegaEvento = totalReaccionesMap;
    });
  }

  Future<void> _verificarParticipaciones() async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      return;
    }

    Map<int, bool> megaEventosParticipandoMap = {};

    for (final megaEvento in _megaEventos) {
      final participacionResult =
          await ApiService.verificarParticipacionMegaEvento(
            megaEvento.megaEventoId,
          );
      if (participacionResult['success'] == true) {
        megaEventosParticipandoMap[megaEvento.megaEventoId] =
            participacionResult['participa'] as bool? ?? false;
      }
    }

    if (!mounted) return;

    setState(() {
      _megaEventosParticipando = megaEventosParticipandoMap;
    });
  }

  Future<void> _toggleReaccionEnCard(int megaEventoId) async {
    final userData = await StorageService.getUserData();
    final isAuthenticated = userData != null;

    if (!isAuthenticated) {
      // Para usuarios no autenticados, usar reacción pública
      final result = await ApiService.reaccionarMegaEventoPublico(megaEventoId);
      if (result['success'] == true && mounted) {
        // Recargar total de reacciones
        final totalResult = await ApiService.getTotalReaccionesMegaEvento(
          megaEventoId,
        );
        if (totalResult['success'] == true && mounted) {
          setState(() {
            _totalReaccionesPorMegaEvento[megaEventoId] =
                totalResult['total_reacciones'] as int? ?? 0;
          });
        }
      }
      return;
    }

    final result = await ApiService.toggleReaccionMegaEvento(megaEventoId);

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _megaEventosReaccionados[megaEventoId] =
            result['reaccionado'] as bool? ?? false;
      });

      // Actualizar total de reacciones
      final totalResult = await ApiService.getTotalReaccionesMegaEvento(
        megaEventoId,
      );
      if (totalResult['success'] == true && mounted) {
        setState(() {
          _totalReaccionesPorMegaEvento[megaEventoId] =
              totalResult['total_reacciones'] as int? ?? 0;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/mega-eventos'),
      appBar: AppBar(
        title: const Text('Mega Eventos Disponibles'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadMegaEventos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 900),
          child: _buildBody(),
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return LoadingState.list();
    }

    if (_error != null) {
      return ErrorView.serverError(onRetry: _loadMegaEventos, errorDetails: _error);
    }

    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(AppSpacing.md),
          color: AppColors.grey100,
          child: Column(
            children: [
              TextField(
                controller: _buscarController,
                decoration: InputDecoration(
                  hintText: 'Buscar mega eventos...',
                  prefixIcon: AppIcon.md(Icons.search),
                  suffixIcon: _buscarController.text.isNotEmpty
                      ? IconButton(
                          icon: AppIcon.md(Icons.clear),
                          onPressed: () {
                            _buscarController.clear();
                            _loadMegaEventos();
                          },
                        )
                      : null,
                  filled: true,
                  fillColor: AppColors.white,
                ),
                onSubmitted: (_) => _loadMegaEventos(),
              ),
              const SizedBox(height: AppSpacing.sm),
              DropdownButtonFormField<String>(
                value: _filtroCategoria,
                decoration: const InputDecoration(
                  labelText: 'Categoría',
                  filled: true,
                  fillColor: AppColors.white,
                ),
                items: const [
                  DropdownMenuItem(value: 'todos', child: Text('Todas las categorías')),
                  DropdownMenuItem(value: 'social', child: Text('Social')),
                  DropdownMenuItem(value: 'cultural', child: Text('Cultural')),
                  DropdownMenuItem(value: 'deportivo', child: Text('Deportivo')),
                  DropdownMenuItem(value: 'educativo', child: Text('Educativo')),
                  DropdownMenuItem(value: 'benefico', child: Text('Benéfico')),
                  DropdownMenuItem(value: 'ambiental', child: Text('Ambiental')),
                  DropdownMenuItem(value: 'otro', child: Text('Otro')),
                ],
                onChanged: (value) {
                  setState(() {
                    _filtroCategoria = value ?? 'todos';
                  });
                  _loadMegaEventos();
                },
              ),
            ],
          ),
        ),
        Expanded(
          child: _megaEventos.isEmpty
              ? EmptyState(
                  icon: Icons.event_busy,
                  title: 'No hay mega eventos disponibles',
                  message: 'Prueba con otra categoría o vuelve a intentarlo.',
                  actionLabel: 'Actualizar',
                  onAction: _loadMegaEventos,
                )
              : RefreshIndicator(
                  onRefresh: _loadMegaEventos,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    itemCount: _megaEventos.length,
                    separatorBuilder: (context, index) =>
                        const SizedBox(height: AppSpacing.md),
                    itemBuilder: (context, index) {
                      final megaEvento = _megaEventos[index];
                      return _buildMegaEventoCard(megaEvento);
                    },
                  ),
                ),
        ),
      ],
    );
  }

  Widget _buildMegaEventoCard(MegaEvento megaEvento) {
    final estaReaccionado =
        _megaEventosReaccionados[megaEvento.megaEventoId] ?? false;
    final totalReacciones =
        _totalReaccionesPorMegaEvento[megaEvento.megaEventoId] ?? 0;
    final estaParticipando =
        _megaEventosParticipando[megaEvento.megaEventoId] ?? false;

    final imagenUrl =
        (megaEvento.imagenes != null && megaEvento.imagenes!.isNotEmpty)
            ? (ImageHelper.buildImageUrl(megaEvento.imagenes!.first.toString()) ?? '')
            : null;

    return AppCard(
      elevated: true,
      padding: EdgeInsets.zero,
      onTap: () async {
        final result = await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MegaEventoDetailScreen(
              megaEventoId: megaEvento.megaEventoId,
            ),
          ),
        );
        if (result == true) {
          _loadMegaEventos();
        }
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (imagenUrl != null && imagenUrl.isNotEmpty)
            ClipRRect(
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(AppRadius.card),
              ),
              child: CachedNetworkImage(
                imageUrl: imagenUrl,
                height: 200,
                width: double.infinity,
                fit: BoxFit.cover,
                placeholder: (context, url) => Container(
                  height: 200,
                  color: AppColors.grey100,
                ),
                errorWidget: (context, url, error) => Container(
                  height: 200,
                  color: AppColors.grey100,
                  child: Center(
                    child: AppIcon.lg(
                      Icons.image_not_supported,
                      color: AppColors.textTertiary,
                    ),
                  ),
                ),
              ),
            )
          else
            Container(
              height: 200,
              width: double.infinity,
              color: AppColors.grey100,
              child: Center(
                child: AppIcon.xl(Icons.event, color: AppColors.textTertiary),
              ),
            ),
          Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        megaEvento.titulo,
                        style: AppTypography.titleLarge,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    if (megaEvento.categoria != null)
                      AppBadge.info(
                        label: megaEvento.categoria!,
                        icon: Icons.category,
                      ),
                  ],
                ),
                const SizedBox(height: AppSpacing.sm),
                if (estaParticipando)
                  AppBadge.success(
                    label: 'Ya estás participando',
                    icon: Icons.check_circle,
                  ),
                if (megaEvento.descripcion != null &&
                    megaEvento.descripcion!.isNotEmpty) ...[
                  const SizedBox(height: AppSpacing.sm),
                  Text(
                    megaEvento.descripcion!,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: AppTypography.bodySecondary,
                  ),
                ],
                const SizedBox(height: AppSpacing.md),
                Row(
                  children: [
                    AppIcon.xs(Icons.calendar_today, color: AppColors.textSecondary),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Text(
                        '${_formatDate(megaEvento.fechaInicio)} - ${_formatDate(megaEvento.fechaFin)}',
                        style: AppTypography.bodySmall,
                      ),
                    ),
                  ],
                ),
                if (megaEvento.ubicacion != null &&
                    megaEvento.ubicacion!.isNotEmpty) ...[
                  const SizedBox(height: AppSpacing.sm),
                  Row(
                    children: [
                      AppIcon.xs(Icons.location_on, color: AppColors.textSecondary),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: Text(
                          megaEvento.ubicacion!,
                          style: AppTypography.bodySmall,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
                const SizedBox(height: AppSpacing.md),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    TextButton.icon(
                      onPressed: () => _toggleReaccionEnCard(megaEvento.megaEventoId),
                      icon: AppIcon.sm(
                        estaReaccionado ? Icons.favorite : Icons.favorite_border,
                        color: estaReaccionado ? AppColors.error : AppColors.textSecondary,
                      ),
                      label: Text(
                        '$totalReacciones',
                        style: AppTypography.labelMedium.copyWith(
                          color: estaReaccionado ? AppColors.error : AppColors.textSecondary,
                        ),
                      ),
                    ),
                    AppButton.primary(
                      label: 'Ver más',
                      icon: Icons.arrow_forward,
                      onPressed: () async {
                        final result = await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => MegaEventoDetailScreen(
                              megaEventoId: megaEvento.megaEventoId,
                            ),
                          ),
                        );
                        if (result == true) {
                          _loadMegaEventos();
                        }
                      },
                      minimumSize: const Size(0, AppSizes.buttonHeightSm),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
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
}
