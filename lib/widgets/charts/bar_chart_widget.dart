import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

class BarChartWidget extends StatelessWidget {
  final Map<String, int> data;
  final String title;
  final Color barColor;
  final String? subtitle;
  final bool horizontal;

  const BarChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.barColor = AppColors.primary,
    this.subtitle,
    this.horizontal = false,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
    }

    final sortedEntries = data.entries.toList();
    final maxValue = sortedEntries.map((e) => e.value).reduce((a, b) => a > b ? a : b).toDouble();

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
                maxY: maxValue * 1.2,
                barTouchData: BarTouchData(
                  enabled: true,
                  touchTooltipData: BarTouchTooltipData(
                    getTooltipItem: (group, groupIndex, rod, rodIndex) {
                      final label = sortedEntries[groupIndex].key;
                      return BarTooltipItem(
                        '$label\n${rod.toY.toInt()}',
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
                        if (value.toInt() >= sortedEntries.length) {
                          return const SizedBox.shrink();
                        }
                        final label = sortedEntries[value.toInt()].key;
                        return Padding(
                          padding: const EdgeInsets.only(top: AppSpacing.xs),
                          child: Transform.rotate(
                            angle: sortedEntries.length > 5 ? -0.5 : 0,
                            child: Text(
                              label.length > 10
                                  ? '${label.substring(0, 8)}...'
                                  : label,
                              style: AppTypography.labelSmall,
                              textAlign: TextAlign.center,
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 40,
                      interval: maxValue > 10 ? maxValue / 5 : 2,
                      getTitlesWidget: (value, meta) {
                        return Text(
                          value.toInt().toString(),
                          style: AppTypography.labelSmall,
                        );
                      },
                    ),
                  ),
                ),
                borderData: FlBorderData(
                  show: true,
                  border: const Border(
                    left: BorderSide(color: AppColors.borderLight),
                    bottom: BorderSide(color: AppColors.borderLight),
                  ),
                ),
                gridData: FlGridData(
                  show: true,
                  drawVerticalLine: false,
                  horizontalInterval: maxValue > 10 ? maxValue / 5 : 2,
                  getDrawingHorizontalLine: (value) {
                    return const FlLine(
                      color: AppColors.borderLight,
                      strokeWidth: 1,
                    );
                  },
                ),
                barGroups: sortedEntries.asMap().entries.map((entry) {
                  return BarChartGroupData(
                    x: entry.key,
                    barRods: [
                      BarChartRodData(
                        toY: entry.value.value.toDouble(),
                        color: barColor,
                        width: 20,
                        borderRadius: const BorderRadius.only(
                          topLeft: Radius.circular(AppRadius.xs),
                          topRight: Radius.circular(AppRadius.xs),
                        ),
                        backDrawRodData: BackgroundBarChartRodData(
                          show: true,
                          toY: maxValue * 1.2,
                          color: barColor.withOpacity(0.12),
                        ),
                      ),
                    ],
                  );
                }).toList(),
              ),
            ),
          ),
        ],
      ),
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
