import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_icon.dart';
import '../molecules/app_card.dart';
import '../molecules/loading_state.dart';

class MetricCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;
  final String? subtitle;
  final double? trend; // Porcentaje de crecimiento
  final VoidCallback? onTap;
  final bool isLoading;

  const MetricCard({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
    this.subtitle,
    this.trend,
    this.onTap,
    this.isLoading = false,
  });

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return LoadingState.card();
    }

    return AppCard(
      onTap: onTap,
      elevated: true,
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(AppSpacing.sm),
                decoration: BoxDecoration(
                  color: AppColors.grey100,
                  borderRadius: BorderRadius.circular(AppRadius.sm),
                ),
                child: AppIcon.md(icon, color: color),
              ),
              const Spacer(),
              if (trend != null) _buildTrendIndicator(),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            value,
            style: AppTypography.headlineSmall.copyWith(color: color),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: AppSpacing.xxs),
          Text(
            label,
            style: AppTypography.labelMedium,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          if (subtitle != null) ...[
            const SizedBox(height: AppSpacing.xxs),
            Text(
              subtitle!,
              style: AppTypography.labelSmall,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildTrendIndicator() {
    final isPositive = trend! >= 0;
    final trendColor = isPositive ? AppColors.successDark : AppColors.errorDark;
    final trendBackground = isPositive
        ? AppColors.successLight
        : AppColors.errorLight;
    final trendIcon = isPositive ? Icons.trending_up : Icons.trending_down;

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.xs,
        vertical: AppSpacing.xxs,
      ),
      decoration: BoxDecoration(
        color: trendBackground,
        borderRadius: BorderRadius.circular(AppRadius.xs),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          AppIcon.sm(trendIcon, color: trendColor),
          const SizedBox(width: AppSpacing.xxxs),
          Text(
            '${trend!.abs().toStringAsFixed(0)}%',
            style: AppTypography.labelSmall.copyWith(color: trendColor),
          ),
        ],
      ),
    );
  }
}

class MetricGrid extends StatelessWidget {
  final List<MetricCard> metrics;
  final int crossAxisCount;

  const MetricGrid({
    super.key,
    required this.metrics,
    this.crossAxisCount = 2,
  });

  @override
  Widget build(BuildContext context) {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        // Cambiado de 1.1 a 1.0 para dar un poco más de altura relativa al ancho
        // Si sigue fallando en pantallas muy angostas, bajar a 0.9
        childAspectRatio: 0.95,
        crossAxisSpacing: AppSpacing.sm,
        mainAxisSpacing: AppSpacing.sm,
      ),
      itemCount: metrics.length,
      itemBuilder: (context, index) => metrics[index],
    );
  }
}
