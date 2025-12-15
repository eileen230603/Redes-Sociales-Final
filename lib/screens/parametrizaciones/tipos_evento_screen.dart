import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/tipo_evento.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';

class TiposEventoScreen extends StatefulWidget {
  const TiposEventoScreen({super.key});

  @override
  State<TiposEventoScreen> createState() => _TiposEventoScreenState();
}

class _TiposEventoScreenState extends State<TiposEventoScreen> {
  List<TipoEvento> _tipos = [];
  bool _isLoading = true;
  String? _error;
  bool _soloActivos = true;

  @override
  void initState() {
    super.initState();
    _cargarTipos();
  }

  Future<void> _cargarTipos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ParametrizacionService.getTiposEvento(
      activo: _soloActivos ? true : null,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _tipos = result['tipos'] as List<TipoEvento>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar tipos';
        _tipos = [];
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(
        title: const Text('Tipos de Evento'),
        actions: [
          Row(
            children: [
              Text('Solo activos', style: AppTypography.labelSmall),
              Switch(
                value: _soloActivos,
                onChanged: (value) {
                  setState(() {
                    _soloActivos = value;
                  });
                  _cargarTipos();
                },
              ),
            ],
          ),
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _cargarTipos,
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
      return ErrorView.serverError(onRetry: _cargarTipos, errorDetails: _error);
    }

    if (_tipos.isEmpty) {
      return EmptyState(
        icon: Icons.event_busy,
        title: 'No hay tipos de evento disponibles',
        message: 'Intenta cambiar el filtro o actualizar la lista.',
        actionLabel: 'Actualizar',
        onAction: _cargarTipos,
      );
    }

    return RefreshIndicator(
      onRefresh: _cargarTipos,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _tipos.length,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.sm),
        itemBuilder: (context, index) {
          final tipo = _tipos[index];
          return _buildTipoCard(tipo);
        },
      ),
    );
  }

  Widget _buildTipoCard(TipoEvento tipo) {
    final color = _parseColor(tipo.color);
    return AppCard(
      elevated: true,
      onTap: () => _mostrarDetallesTipo(tipo),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: AppSizes.avatarMd,
            height: AppSizes.avatarMd,
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              borderRadius: BorderRadius.circular(AppRadius.full),
              border: Border.all(color: AppColors.borderLight),
            ),
            child: Center(
              child: AppIcon.md(_getIconFromString(tipo.icono), color: color),
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        tipo.nombre,
                        style: AppTypography.titleSmall,
                      ),
                    ),
                    tipo.activo
                        ? AppBadge.success(label: 'Activo', icon: Icons.check_circle)
                        : AppBadge.neutral(label: 'Inactivo', icon: Icons.pause_circle),
                  ],
                ),
                const SizedBox(height: AppSpacing.xxs),
                if (tipo.descripcion != null && tipo.descripcion!.isNotEmpty) ...[
                  Text(tipo.descripcion!, style: AppTypography.bodySecondary),
                  const SizedBox(height: AppSpacing.xxs),
                ],
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.xxs,
                  children: [
                    AppBadge.neutral(label: 'Código: ${tipo.codigo}', icon: Icons.code),
                    AppBadge.neutral(label: 'Orden: ${tipo.orden}', icon: Icons.sort),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          AppIcon.sm(Icons.chevron_right),
        ],
      ),
    );
  }

  void _mostrarDetallesTipo(TipoEvento tipo) {
    showDialog(
      context: context,
      builder:
          (context) => AlertDialog(
            title: Text(tipo.nombre),
            content: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  _buildInfoRow('Código', tipo.codigo),
                  if (tipo.descripcion != null)
                    _buildInfoRow('Descripción', tipo.descripcion!),
                  _buildInfoRow('Icono', tipo.icono ?? 'Sin icono'),
                  _buildInfoRow('Color', tipo.color ?? 'Sin color'),
                  _buildInfoRow('Orden', tipo.orden.toString()),
                  _buildInfoRow('Estado', tipo.activo ? 'Activo' : 'Inactivo'),
                ],
              ),
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Cerrar'),
              ),
            ],
          ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: AppSpacing.xxs),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              '$label:',
              style: AppTypography.labelLarge,
            ),
          ),
          Expanded(child: Text(value, style: AppTypography.bodyMedium)),
        ],
      ),
    );
  }

  Color _parseColor(String? colorString) {
    final value = colorString?.trim().toLowerCase();
    if (value == null || value.isEmpty) return AppColors.primary;

    switch (value) {
      case 'blue':
        return AppColors.info;
      case 'green':
        return AppColors.success;
      case 'red':
        return AppColors.error;
      case 'orange':
        return AppColors.warning;
      case 'purple':
        return AppColors.primaryLight;
      default:
        return AppColors.primary;
    }
  }

  IconData _getIconFromString(String? iconString) {
    if (iconString == null) return Icons.event;
    // Mapeo básico de iconos comunes
    switch (iconString.toLowerCase()) {
      case 'conference':
      case 'conferencia':
        return Icons.business_center;
      case 'taller':
      case 'workshop':
        return Icons.build;
      case 'seminario':
      case 'seminar':
        return Icons.school;
      case 'voluntariado':
      case 'volunteer':
        return Icons.volunteer_activism;
      case 'cultural':
        return Icons.palette;
      case 'deportivo':
      case 'sport':
        return Icons.sports_soccer;
      default:
        return Icons.event;
    }
  }
}
