import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

/// Datos para una línea en el gráfico múltiple
class MultiLineData {
  final String label;
  final Map<String, int> values; // key: fecha, value: cantidad
  final Color color;

  MultiLineData({
    required this.label,
    required this.values,
    required this.color,
  });
}

/// Widget de gráfico de líneas múltiples (Multi Line Chart)
/// Muestra múltiples series de datos como líneas superpuestas
class MultiLineChartWidget extends StatelessWidget {
  final List<MultiLineData> data;
  final String title;
  final bool showDots;
  final bool showGrid;
  final String? subtitle;

  const MultiLineChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.showDots = true,
    this.showGrid = true,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
    }

    // Obtener todas las fechas únicas y ordenarlas
    final allDates = <String>{};
    for (final lineData in data) {
      allDates.addAll(lineData.values.keys);
    }
    final sortedDates = allDates.toList()..sort();

    // Crear spots para cada línea
    final lineBarsData =
        data.map((lineData) {
          final spots =
              sortedDates.asMap().entries.map((entry) {
                final index = entry.key;
                final date = entry.value;
                final value = lineData.values[date] ?? 0;
                return FlSpot(index.toDouble(), value.toDouble());
              }).toList();

          return LineChartBarData(
            spots: spots,
            isCurved: true,
            color: lineData.color,
            barWidth: 3,
            isStrokeCapRound: true,
            dotData: FlDotData(
              show: showDots,
              getDotPainter: (spot, percent, barData, index) {
                return FlDotCirclePainter(
                  radius: 4,
                  color: lineData.color,
                  strokeWidth: 2,
                  strokeColor: AppColors.white,
                );
              },
            ),
            belowBarData: BarAreaData(
              show: true,
              color: lineData.color.withOpacity(0.12),
            ),
          );
        }).toList();

    // Calcular maxY
    double maxY = 0;
    for (final lineData in data) {
      for (final value in lineData.values.values) {
        if (value > maxY) maxY = value.toDouble();
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
            child: LineChart(
              LineChartData(
                gridData: FlGridData(
                  show: showGrid,
                  drawVerticalLine: true,
                  horizontalInterval: 1,
                  verticalInterval: 1,
                  getDrawingHorizontalLine: (value) {
                    return const FlLine(
                      color: AppColors.borderLight,
                      strokeWidth: 1,
                    );
                  },
                  getDrawingVerticalLine: (value) {
                    return const FlLine(
                      color: AppColors.borderLight,
                      strokeWidth: 1,
                    );
                  },
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
                      reservedSize: 30,
                      interval: sortedDates.length > 10 ? 2 : 1,
                      getTitlesWidget: (value, meta) {
                        if (value.toInt() >= sortedDates.length) {
                          return const SizedBox.shrink();
                        }
                        final date = sortedDates[value.toInt()];
                        try {
                          final parsedDate = DateTime.parse(date);
                          return Padding(
                            padding: const EdgeInsets.only(top: AppSpacing.xs),
                            child: Text(
                              DateFormat('MM/dd').format(parsedDate),
                              style: AppTypography.labelSmall,
                            ),
                          );
                        } catch (_) {
                          return Text(
                            date.length > 5 ? date.substring(0, 5) : date,
                            style: AppTypography.labelSmall,
                          );
                        }
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      interval: maxY > 5 ? (maxY / 5) : 1,
                      reservedSize: 42,
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
                  border: Border.all(color: AppColors.borderLight),
                ),
                minX: 0,
                maxX: (sortedDates.length - 1).toDouble(),
                minY: 0,
                maxY: maxY * 1.1,
                lineBarsData: lineBarsData,
                lineTouchData: LineTouchData(
                  touchTooltipData: LineTouchTooltipData(
                    getTooltipItems: (touchedSpots) {
                      return touchedSpots.map((spot) {
                        final date = sortedDates[spot.x.toInt()];
                        String formattedDate;
                        try {
                          final parsedDate = DateTime.parse(date);
                          formattedDate =
                              DateFormat('MMM dd').format(parsedDate);
                        } catch (_) {
                          formattedDate = date;
                        }
                        final lineData = data[spot.barIndex];
                        return LineTooltipItem(
                          '${lineData.label}\n$formattedDate: ${spot.y.toInt()}',
                          AppTypography.labelSmall.copyWith(
                            color: lineData.color,
                            fontWeight: FontWeight.w700,
                          ),
                        );
                      }).toList();
                    },
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
          data.map((lineData) {
            return Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 16,
                  height: 3,
                  decoration: BoxDecoration(
                    color: lineData.color,
                    borderRadius: BorderRadius.circular(AppRadius.xs),
                  ),
                ),
                const SizedBox(width: AppSpacing.xs),
                Text(
                  lineData.label,
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
                  AppIcon.lg(Icons.show_chart, color: AppColors.textTertiary),
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
