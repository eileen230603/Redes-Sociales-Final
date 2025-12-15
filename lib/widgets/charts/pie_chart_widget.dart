import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

class PieChartWidget extends StatefulWidget {
  final Map<String, int> data;
  final String title;
  final List<Color>? colors;
  final String? subtitle;
  final bool showLegend;
  final bool showPercentage;

  const PieChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.colors,
    this.subtitle,
    this.showLegend = true,
    this.showPercentage = true,
  });

  @override
  State<PieChartWidget> createState() => _PieChartWidgetState();
}

class _PieChartWidgetState extends State<PieChartWidget> {
  int touchedIndex = -1;

  @override
  Widget build(BuildContext context) {
    if (widget.data.isEmpty) {
      return _buildEmptyState();
    }

    final total = widget.data.values.reduce((a, b) => a + b);
    final colorList = widget.colors ??
        [
          AppColors.primary,
          AppColors.accent,
          AppColors.warning,
          AppColors.info,
          AppColors.error,
          AppColors.primaryLight,
          AppColors.accentLight,
          AppColors.grey600,
        ];

    return AppCard(
      elevated: true,
      padding: const EdgeInsets.all(AppSpacing.lg),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(widget.title, style: AppTypography.titleLarge),
          if (widget.subtitle != null) ...[
            const SizedBox(height: AppSpacing.xxs),
            Text(widget.subtitle!, style: AppTypography.bodySecondary),
          ],
          const SizedBox(height: AppSpacing.lg),
          SizedBox(
            height: 200,
            child: Row(
              children: [
                Expanded(
                  flex: 3,
                  child: PieChart(
                    PieChartData(
                      pieTouchData: PieTouchData(
                        touchCallback: (FlTouchEvent event, pieTouchResponse) {
                          setState(() {
                            if (!event.isInterestedForInteractions ||
                                pieTouchResponse == null ||
                                pieTouchResponse.touchedSection == null) {
                              touchedIndex = -1;
                              return;
                            }
                            touchedIndex = pieTouchResponse
                                .touchedSection!
                                .touchedSectionIndex;
                          });
                        },
                      ),
                      borderData: FlBorderData(show: false),
                      sectionsSpace: 2,
                      centerSpaceRadius: 40,
                      sections: _buildSections(total, colorList),
                    ),
                  ),
                ),
                if (widget.showLegend) ...[
                  const SizedBox(width: AppSpacing.lg),
                  Expanded(
                    flex: 2,
                    child: _buildLegend(colorList),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  List<PieChartSectionData> _buildSections(int total, List<Color> colorList) {
    final entries = widget.data.entries.toList();
    
    return entries.asMap().entries.map((entry) {
      final index = entry.key;
      final data = entry.value;
      final isTouched = index == touchedIndex;
      final fontSize = isTouched ? 16.0 : 12.0;
      final radius = isTouched ? 65.0 : 55.0;

      final percentage = (data.value / total * 100);
      final color = colorList[index % colorList.length];

      return PieChartSectionData(
        color: color,
        value: data.value.toDouble(),
        title: widget.showPercentage ? '${percentage.toStringAsFixed(1)}%' : '',
        radius: radius,
        titleStyle: AppTypography.labelSmall.copyWith(
          fontSize: fontSize,
          fontWeight: FontWeight.w700,
          color: AppColors.white,
          shadows: [
            Shadow(
              color: AppColors.black.withOpacity(0.45),
              blurRadius: 2,
            ),
          ],
        ),
      );
    }).toList();
  }

  Widget _buildLegend(List<Color> colorList) {
    final entries = widget.data.entries.toList();
    final total = widget.data.values.reduce((a, b) => a + b);

    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: entries.asMap().entries.map((entry) {
        final index = entry.key;
        final data = entry.value;
        final color = colorList[index % colorList.length];
        final percentage = (data.value / total * 100);

        return Padding(
          padding: const EdgeInsets.only(bottom: 8.0),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 16,
                height: 16,
                decoration: BoxDecoration(
                  color: color,
                  shape: BoxShape.circle,
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      data.key.length > 15
                          ? '${data.key.substring(0, 12)}...'
                          : data.key,
                      style: AppTypography.labelSmall.copyWith(
                        fontWeight: FontWeight.w600,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    Text(
                      '${data.value} (${percentage.toStringAsFixed(1)}%)',
                      style: AppTypography.labelSmall,
                    ),
                  ],
                ),
              ),
            ],
          ),
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
          Text(widget.title, style: AppTypography.titleLarge),
          const SizedBox(height: AppSpacing.lg),
          SizedBox(
            height: 200,
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  AppIcon.lg(Icons.pie_chart, color: AppColors.textTertiary),
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
