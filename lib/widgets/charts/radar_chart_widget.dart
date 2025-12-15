import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';

class RadarChartWidget extends StatelessWidget {
  final Map<String, double> data;
  final String title;
  final Color fillColor;
  final String? subtitle;

  const RadarChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.fillColor = AppColors.primary,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
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
            child: RadarChart(
              RadarChartData(
                radarShape: RadarShape.polygon,
                tickCount: 5,
                ticksTextStyle: AppTypography.labelSmall,
                radarBorderData: const BorderSide(
                  color: AppColors.borderLight,
                  width: 2,
                ),
                gridBorderData: const BorderSide(
                  color: AppColors.borderLight,
                  width: 1,
                ),
                tickBorderData: const BorderSide(
                  color: AppColors.borderMedium,
                  width: 1,
                ),
                getTitle: (index, angle) {
                  final entries = data.entries.toList();
                  if (index >= entries.length) {
                    return const RadarChartTitle(text: '');
                  }

                  String label = entries[index].key;
                  label = label[0].toUpperCase() + label.substring(1);

                  return RadarChartTitle(
                    text: label.length > 12
                        ? '${label.substring(0, 10)}...'
                        : label,
                    angle: angle,
                  );
                },
                dataSets: [
                  RadarDataSet(
                    fillColor: fillColor.withOpacity(0.25),
                    borderColor: fillColor,
                    borderWidth: 2,
                    dataEntries:
                        data.values
                            .map((value) => RadarEntry(value: value))
                            .toList(),
                  ),
                ],
              ),
              swapAnimationDuration: AppDuration.normal,
              swapAnimationCurve: AppCurves.standard,
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
      spacing: AppSpacing.sm,
      runSpacing: AppSpacing.sm,
      children: data.entries.map((entry) {
        return Container(
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.sm,
            vertical: AppSpacing.xs,
          ),
          decoration: BoxDecoration(
            color: fillColor.withOpacity(0.12),
            borderRadius: BorderRadius.circular(AppRadius.sm),
            border: Border.all(color: fillColor.withOpacity(0.24)),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                entry.key[0].toUpperCase() + entry.key.substring(1),
                style: AppTypography.labelSmall,
              ),
              const SizedBox(width: AppSpacing.xxs),
              Text(
                '${entry.value.toStringAsFixed(1)}%',
                style: AppTypography.labelSmall.copyWith(
                  color: fillColor,
                  fontWeight: FontWeight.w700,
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
          Text(title, style: AppTypography.titleLarge),
          const SizedBox(height: AppSpacing.lg),
          SizedBox(
            height: 250,
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  AppIcon.lg(Icons.radar, color: AppColors.textTertiary),
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
