import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';

/// Icono estandarizado con tamaños y colores del sistema de diseño
class AppIcon extends StatelessWidget {
  final IconData icon;
  final double? size;
  final Color? color;

  const AppIcon._({
    required this.icon,
    this.size,
    this.color,
  });

  factory AppIcon.xs(
    IconData icon, {
    Color? color,
  }) {
    return AppIcon._(
      icon: icon,
      size: AppSizes.iconXs,
      color: color,
    );
  }

  factory AppIcon.sm(
    IconData icon, {
    Color? color,
  }) {
    return AppIcon._(
      icon: icon,
      size: AppSizes.iconSm,
      color: color,
    );
  }

  factory AppIcon.md(
    IconData icon, {
    Color? color,
  }) {
    return AppIcon._(
      icon: icon,
      size: AppSizes.iconMd,
      color: color,
    );
  }

  factory AppIcon.lg(
    IconData icon, {
    Color? color,
  }) {
    return AppIcon._(
      icon: icon,
      size: AppSizes.iconLg,
      color: color,
    );
  }

  factory AppIcon.xl(
    IconData icon, {
    Color? color,
  }) {
    return AppIcon._(
      icon: icon,
      size: AppSizes.iconXl,
      color: color,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Icon(
      icon,
      size: size,
      color: color ?? AppColors.textSecondary,
    );
  }
}
