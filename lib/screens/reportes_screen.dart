import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/api_service.dart';
import '../widgets/app_drawer.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/empty_state.dart';
import '../widgets/molecules/loading_state.dart';
import '../widgets/organisms/error_view.dart';
import '../models/evento_participacion.dart';

class ReportesScreen extends StatefulWidget {
  const ReportesScreen({super.key});

  @override
  State<ReportesScreen> createState() => _ReportesScreenState();
}

class _ReportesScreenState extends State<ReportesScreen> {
  List<EventoParticipacion> _participaciones = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadDatos();
  }

  Future<void> _loadDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getMisEventos();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _participaciones =
            result['participaciones'] as List<EventoParticipacion>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar datos';
      }
    });
  }

  int get _totalEventos => _participaciones.length;
  int get _eventosAsistidos => _participaciones.where((p) => p.asistio).length;
  int get _totalPuntos => _participaciones.fold(0, (sum, p) => sum + p.puntos);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/reportes'),
      appBar: AppBar(
        title: const Text('Reportes'),
        actions: [
          IconButton(
            icon: AppIcon.md(Icons.refresh),
            onPressed: _loadDatos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body:
          _isLoading
              ? LoadingState.detail()
              : _error != null
              ? ErrorView.serverError(onRetry: _loadDatos, errorDetails: _error)
              : _totalEventos == 0
              ? EmptyState(
                  icon: Icons.bar_chart,
                  title: 'Sin datos para reportes',
                  message: 'Participa en un evento para ver estadísticas y puntos acumulados.',
                  actionLabel: 'Actualizar',
                  onAction: _loadDatos,
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Estadísticas', style: AppTypography.headlineSmall),
                      const SizedBox(height: AppSpacing.md),
                      Row(
                        children: [
                          Expanded(
                            child: _buildStatCard(
                              'Total Eventos',
                              '$_totalEventos',
                              Icons.event,
                              AppColors.info,
                            ),
                          ),
                          const SizedBox(width: AppSpacing.md),
                          Expanded(
                            child: _buildStatCard(
                              'Asistidos',
                              '$_eventosAsistidos',
                              Icons.check_circle,
                              AppColors.success,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: AppSpacing.md),
                      _buildStatCard(
                        'Puntos Totales',
                        '$_totalPuntos',
                        Icons.star,
                        AppColors.warning,
                        fullWidth: true,
                      ),
                      const SizedBox(height: AppSpacing.xl),
                      Text('Resumen de Participación', style: AppTypography.headlineSmall),
                      const SizedBox(height: AppSpacing.md),
                      AppCard(
                        child: Column(
                          children: [
                            _buildProgressBar(
                              'Eventos Asistidos',
                              _eventosAsistidos,
                              _totalEventos,
                              AppColors.success,
                            ),
                            const SizedBox(height: AppSpacing.lg),
                            _buildProgressBar(
                              'Eventos Pendientes',
                              _totalEventos - _eventosAsistidos,
                              _totalEventos,
                              AppColors.warning,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }

  Widget _buildStatCard(
    String title,
    String value,
    IconData icon,
    Color color, {
    bool fullWidth = false,
  }) {
    return AppCard(
      elevated: true,
      child: SizedBox(
        width: fullWidth ? double.infinity : null,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                AppIcon.md(icon, color: color),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: Text(title, style: AppTypography.bodySecondary),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.sm),
            Text(
              value,
              style: AppTypography.headlineMedium.copyWith(color: color),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProgressBar(String label, int value, int total, Color color) {
    final percentage = total > 0 ? (value / total) : 0.0;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label, style: AppTypography.labelLarge),
            Text(
              '$value / $total',
              style: AppTypography.labelSmall,
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.sm),
        LinearProgressIndicator(
          value: percentage,
          backgroundColor: AppColors.grey200,
          valueColor: AlwaysStoppedAnimation<Color>(color),
          minHeight: 8,
          borderRadius: BorderRadius.circular(AppRadius.full),
        ),
        const SizedBox(height: AppSpacing.xxs),
        Text(
          '${(percentage * 100).toStringAsFixed(1)}%',
          style: AppTypography.labelSmall,
        ),
      ],
    );
  }
}
