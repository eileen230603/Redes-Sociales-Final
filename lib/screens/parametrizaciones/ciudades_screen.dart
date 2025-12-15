import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/ciudad.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_badge.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';

class CiudadesScreen extends StatefulWidget {
  const CiudadesScreen({super.key});

  @override
  State<CiudadesScreen> createState() => _CiudadesScreenState();
}

class _CiudadesScreenState extends State<CiudadesScreen> {
  List<Ciudad> _ciudades = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _cargarCiudades();
  }

  Future<void> _cargarCiudades() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ParametrizacionService.getCiudades();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _ciudades = result['ciudades'] as List<Ciudad>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar ciudades';
        _ciudades = [];
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(
        title: const Text('Ciudades'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _cargarCiudades,
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
      return ErrorView.serverError(onRetry: _cargarCiudades, errorDetails: _error);
    }

    if (_ciudades.isEmpty) {
      return EmptyState(
        icon: Icons.location_city,
        title: 'No hay ciudades disponibles',
        message: 'Intenta actualizar para volver a cargar la lista.',
        actionLabel: 'Actualizar',
        onAction: _cargarCiudades,
      );
    }

    return RefreshIndicator(
      onRefresh: _cargarCiudades,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _ciudades.length,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.sm),
        itemBuilder: (context, index) {
          final ciudad = _ciudades[index];
          return _buildCiudadCard(ciudad);
        },
      ),
    );
  }

  Widget _buildCiudadCard(Ciudad ciudad) {
    return AppCard(
      elevated: true,
      child: Row(
        children: [
          Container(
            width: AppSizes.avatarMd,
            height: AppSizes.avatarMd,
            decoration: BoxDecoration(
              color: AppColors.successLight,
              shape: BoxShape.circle,
              border: Border.all(color: AppColors.borderLight),
            ),
            child: Center(
              child: AppIcon.md(Icons.location_city, color: AppColors.successDark),
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(ciudad.nombre),
                const SizedBox(height: AppSpacing.xxs),
                Text(
                  ciudad.nombreCompleto,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        color: AppColors.textSecondary,
                      ),
                ),
              ],
            ),
          ),
          if (ciudad.activo) AppBadge.success(label: 'Activa', icon: Icons.check_circle),
        ],
      ),
    );
  }
}
