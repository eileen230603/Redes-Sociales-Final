import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';

/// Badge para indicadores de estado, contadores, etiquetas
class AppBadge extends StatelessWidget {
  final String label;
  final Color? backgroundColor;
  final Color? textColor;
  final _AppBadgeVariant _variant;
  final IconData? icon;

  const AppBadge._({
    required this.label,
    this.backgroundColor,
    this.textColor,
    required _AppBadgeVariant variant,
    this.icon,
  }) : _variant = variant;

  factory AppBadge.primary({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.primary,
      icon: icon,
    );
  }

  factory AppBadge.success({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.success,
      icon: icon,
    );
  }

  factory AppBadge.warning({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.warning,
      icon: icon,
    );
  }

  factory AppBadge.error({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.error,
      icon: icon,
    );
  }

  factory AppBadge.info({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.info,
      icon: icon,
    );
  }

  factory AppBadge.neutral({
    required String label,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      variant: _AppBadgeVariant.neutral,
      icon: icon,
    );
  }

  factory AppBadge.custom({
    required String label,
    required Color backgroundColor,
    required Color textColor,
    IconData? icon,
  }) {
    return AppBadge._(
      label: label,
      backgroundColor: backgroundColor,
      textColor: textColor,
      variant: _AppBadgeVariant.custom,
      icon: icon,
    );
  }

  @override
  Widget build(BuildContext context) {
    final colors = _getColors();

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xxs,
      ),
      decoration: BoxDecoration(
        color: colors.background,
        borderRadius: BorderRadius.circular(AppRadius.full),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(
              icon,
              size: AppSizes.iconXs,
              color: colors.text,
            ),
            const SizedBox(width: AppSpacing.xxs),
          ],
          Text(
            label,
            style: AppTypography.labelChip.copyWith(
              color: colors.text,
            ),
          ),
        ],
      ),
    );
  }

  _BadgeColors _getColors() {
    switch (_variant) {
      case _AppBadgeVariant.primary:
        return _BadgeColors(
          background: AppColors.primaryLight,
          text: AppColors.textOnPrimary,
        );
      case _AppBadgeVariant.success:
        return _BadgeColors(
          background: AppColors.successLight,
          text: AppColors.successDark,
        );
      case _AppBadgeVariant.warning:
        return _BadgeColors(
          background: AppColors.warningLight,
          text: AppColors.warningDark,
        );
      case _AppBadgeVariant.error:
        return _BadgeColors(
          background: AppColors.errorLight,
          text: AppColors.errorDark,
        );
      case _AppBadgeVariant.info:
        return _BadgeColors(
          background: AppColors.infoLight,
          text: AppColors.infoDark,
        );
      case _AppBadgeVariant.neutral:
        return _BadgeColors(
          background: AppColors.grey200,
          text: AppColors.textPrimary,
        );
      case _AppBadgeVariant.custom:
        return _BadgeColors(
          background: backgroundColor!,
          text: textColor!,
        );
    }
  }
}

enum _AppBadgeVariant {
  primary,
  success,
  warning,
  error,
  info,
  neutral,
  custom,
}

class _BadgeColors {
  final Color background;
  final Color text;

  _BadgeColors({required this.background, required this.text});
}
