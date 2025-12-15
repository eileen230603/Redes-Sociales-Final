import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

class LineChartWidget extends StatelessWidget {
  final Map<String, int> data;
  final String title;
  final Color lineColor;
  final bool showDots;
  final bool showGrid;
  final String? subtitle;

  const LineChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.lineColor = AppColors.primary,
    this.showDots = true,
    this.showGrid = true,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
    }

    final sortedEntries = data.entries.toList()
      ..sort((a, b) => a.key.compareTo(b.key));

    final spots = sortedEntries
        .asMap()
        .entries
        .map((e) => FlSpot(e.key.toDouble(), e.value.value.toDouble()))
        .toList();

    final maxY = sortedEntries.map((e) => e.value).reduce((a, b) => a > b ? a : b).toDouble();
    final minY = sortedEntries.map((e) => e.value).reduce((a, b) => a < b ? a : b).toDouble();

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
            height: 200,
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
                      interval: spots.length > 10 ? 2 : 1,
                      getTitlesWidget: (value, meta) {
                        if (value.toInt() >= sortedEntries.length) {
                          return const SizedBox.shrink();
                        }
                        final date = sortedEntries[value.toInt()].key;
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
                      interval: (maxY - minY) > 5 ? (maxY - minY) / 5 : 1,
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
                maxX: (spots.length - 1).toDouble(),
                minY: minY > 0 ? 0 : minY,
                maxY: maxY * 1.1,
                lineBarsData: [
                  LineChartBarData(
                    spots: spots,
                    isCurved: true,
                    color: lineColor,
                    barWidth: 3,
                    isStrokeCapRound: true,
                    dotData: FlDotData(
                      show: showDots,
                      getDotPainter: (spot, percent, barData, index) {
                        return FlDotCirclePainter(
                          radius: 4,
                          color: lineColor,
                          strokeWidth: 2,
                          strokeColor: AppColors.white,
                        );
                      },
                    ),
                    belowBarData: BarAreaData(
                      show: true,
                      color: lineColor.withOpacity(0.12),
                    ),
                  ),
                ],
                lineTouchData: LineTouchData(
                  touchTooltipData: LineTouchTooltipData(
                    getTooltipItems: (touchedSpots) {
                      return touchedSpots.map((spot) {
                        final date = sortedEntries[spot.x.toInt()].key;
                        String formattedDate;
                        try {
                          final parsedDate = DateTime.parse(date);
                          formattedDate =
                              DateFormat('MMM dd').format(parsedDate);
                        } catch (_) {
                          formattedDate = date;
                        }
                        return LineTooltipItem(
                          '$formattedDate\n${spot.y.toInt()}',
                          AppTypography.labelSmall.copyWith(
                            color: AppColors.white,
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
            height: 200,
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
