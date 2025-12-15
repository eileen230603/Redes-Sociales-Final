import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

/// Datos para un grupo de barras
class GroupedBarData {
  final String label;
  final Map<String, double> values; // key: nombre de la serie, value: valor

  GroupedBarData({required this.label, required this.values});
}

/// Widget de gráfico de barras agrupadas (Grouped Bar Chart)
/// Muestra múltiples series de datos lado a lado
class GroupedBarChartWidget extends StatelessWidget {
  final List<GroupedBarData> data;
  final String title;
  final List<Color> colors;
  final bool showGrid;
  final String? subtitle;
  final List<String> seriesNames; // Nombres de las series

  const GroupedBarChartWidget({
    super.key,
    required this.data,
    required this.title,
    required this.seriesNames,
    this.colors = const [
      AppColors.primary,
      AppColors.accent,
      AppColors.warning,
      AppColors.error,
    ],
    this.showGrid = true,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty || seriesNames.isEmpty) {
      return _buildEmptyState();
    }

    // Obtener máximo valor para escalar el gráfico
    double maxY = 0;
    for (final item in data) {
      for (final value in item.values.values) {
        if (value > maxY) {
          maxY = value;
        }
      }
    }

    return AppCard(
      elevated: true,
      padding: const EdgeInsets.all(AppSpacing.lg),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: AppTypography.titleLarge),
          if (subtitle != null) ...[
            const SizedBox(height: AppSpacing.xxs),
            Text(subtitle!, style: AppTypography.bodySecondary),
          ],
          const SizedBox(height: AppSpacing.lg),
          SizedBox(
            height: 250,
            child: BarChart(
              BarChartData(
                alignment: BarChartAlignment.spaceAround,
                maxY: maxY * 1.2,
                barTouchData: BarTouchData(
                  enabled: true,
                  touchTooltipData: BarTouchTooltipData(
                    getTooltipItem: (group, groupIndex, rod, rodIndex) {
                      final label = data[rodIndex].label;
                      final seriesName = seriesNames[groupIndex];
                      final value = rod.toY.toInt();
                      return BarTooltipItem(
                        '$label\n$seriesName: $value',
                        AppTypography.labelSmall.copyWith(
                          color: AppColors.white,
                          fontWeight: FontWeight.w700,
                        ),
                      );
                    },
                  ),
                ),
                titlesData: FlTitlesData(
                  show: true,
                  rightTitles: const AxisTitles(
                    sideTitles: SideTitles(showTitles: false),
                  ),
                  topTitles: const AxisTitles(
                    sideTitles: SideTitles(showTitles: false),
                  ),
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 40,
                      getTitlesWidget: (value, meta) {
                        if (value.toInt() >= data.length) {
                          return const SizedBox.shrink();
                        }
                        final label = data[value.toInt()].label;
                        return Padding(
                          padding: const EdgeInsets.only(top: AppSpacing.xs),
                          child: Text(
                            label.length > 10
                                ? '${label.substring(0, 8)}...'
                                : label,
                            style: AppTypography.labelSmall,
                            textAlign: TextAlign.center,
                          ),
                        );
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 42,
                      interval: maxY > 10 ? (maxY / 5).ceilToDouble() : 1,
                      getTitlesWidget: (value, meta) {
                        return Text(
                          value.toInt().toString(),
                          style: AppTypography.labelSmall,
                        );
                      },
                    ),
                  ),
                ),
                gridData: FlGridData(
                  show: showGrid,
                  drawVerticalLine: false,
                  horizontalInterval: maxY > 10 ? (maxY / 5).ceilToDouble() : 1,
                  getDrawingHorizontalLine: (value) {
                    return const FlLine(
                      color: AppColors.borderLight,
                      strokeWidth: 1,
                    );
                  },
                ),
                borderData: FlBorderData(
                  show: true,
                  border: Border.all(color: AppColors.borderLight),
                ),
                barGroups: List.generate(
                  data.length,
                  (index) => BarChartGroupData(
                    x: index,
                    groupVertically: true,
                    barRods: seriesNames.asMap().entries.map((entry) {
                      final seriesIndex = entry.key;
                      final seriesName = entry.value;
                      final color = colors[seriesIndex % colors.length];
                      final value = data[index].values[seriesName] ?? 0.0;

                      return BarChartRodData(
                        toY: value,
                        color: color,
                        width: 20,
                        borderRadius: const BorderRadius.vertical(
                          top: Radius.circular(AppRadius.xs),
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.md),
          _buildLegend(),
        ],
      ),
    );
  }

  Widget _buildLegend() {
    return Wrap(
      spacing: AppSpacing.md,
      runSpacing: AppSpacing.sm,
      children:
          seriesNames.asMap().entries.map((entry) {
            final index = entry.key;
            final seriesName = entry.value;
            final color = colors[index % colors.length];

            return Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 16,
                  height: 16,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(AppRadius.xs),
                  ),
                ),
                const SizedBox(width: AppSpacing.xs),
                Text(
                  seriesName,
                  style: AppTypography.labelSmall,
                ),
              ],
            );
          }).toList(),
    );
  }

  Widget _buildEmptyState() {
    return AppCard(
      elevated: true,
      padding: const EdgeInsets.all(AppSpacing.lg),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: AppTypography.titleLarge),
          const SizedBox(height: AppSpacing.lg),
          SizedBox(
            height: 250,
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  AppIcon.lg(Icons.bar_chart, color: AppColors.textTertiary),
                  const SizedBox(height: AppSpacing.xs),
                  Text('No hay datos disponibles', style: AppTypography.bodySecondary),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
