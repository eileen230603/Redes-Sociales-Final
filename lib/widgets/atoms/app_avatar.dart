import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';

/// Avatar con tamaños estandarizados y soporte para imagen/iniciales/icono
class AppAvatar extends StatelessWidget {
  final String? imageUrl;
  final String? initials;
  final IconData? icon;
  final Color? backgroundColor;
  final Color? foregroundColor;
  final double size;

  const AppAvatar._({
    this.imageUrl,
    this.initials,
    this.icon,
    this.backgroundColor,
    this.foregroundColor,
    required this.size,
  });

  factory AppAvatar.xs({
    String? imageUrl,
    String? initials,
    IconData? icon,
    Color? backgroundColor,
    Color? foregroundColor,
  }) {
    return AppAvatar._(
      imageUrl: imageUrl,
      initials: initials,
      icon: icon,
      backgroundColor: backgroundColor,
      foregroundColor: foregroundColor,
      size: AppSizes.avatarXs,
    );
  }

  factory AppAvatar.sm({
    String? imageUrl,
    String? initials,
    IconData? icon,
    Color? backgroundColor,
    Color? foregroundColor,
  }) {
    return AppAvatar._(
      imageUrl: imageUrl,
      initials: initials,
      icon: icon,
      backgroundColor: backgroundColor,
      foregroundColor: foregroundColor,
      size: AppSizes.avatarSm,
    );
  }

  factory AppAvatar.md({
    String? imageUrl,
    String? initials,
    IconData? icon,
    Color? backgroundColor,
    Color? foregroundColor,
  }) {
    return AppAvatar._(
      imageUrl: imageUrl,
      initials: initials,
      icon: icon,
      backgroundColor: backgroundColor,
      foregroundColor: foregroundColor,
      size: AppSizes.avatarMd,
    );
  }

  factory AppAvatar.lg({
    String? imageUrl,
    String? initials,
    IconData? icon,
    Color? backgroundColor,
    Color? foregroundColor,
  }) {
    return AppAvatar._(
      imageUrl: imageUrl,
      initials: initials,
      icon: icon,
      backgroundColor: backgroundColor,
      foregroundColor: foregroundColor,
      size: AppSizes.avatarLg,
    );
  }

  factory AppAvatar.xl({
    String? imageUrl,
    String? initials,
    IconData? icon,
    Color? backgroundColor,
    Color? foregroundColor,
  }) {
    return AppAvatar._(
      imageUrl: imageUrl,
      initials: initials,
      icon: icon,
      backgroundColor: backgroundColor,
      foregroundColor: foregroundColor,
      size: AppSizes.avatarXl,
    );
  }

  @override
  Widget build(BuildContext context) {
    final bgColor = backgroundColor ?? AppColors.grey300;
    final fgColor = foregroundColor ?? AppColors.textOnPrimary;

    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: bgColor,
        shape: BoxShape.circle,
        border: Border.all(
          color: AppColors.borderLight,
          width: 1,
        ),
      ),
      child: ClipOval(
        child: _buildContent(fgColor),
      ),
    );
  }

  Widget _buildContent(Color fgColor) {
    // Prioridad: imagen > iniciales > icono > placeholder
    if (imageUrl != null && imageUrl!.isNotEmpty) {
      return Image.network(
        imageUrl!,
        fit: BoxFit.cover,
        errorBuilder: (context, error, stackTrace) {
          return _buildFallback(fgColor);
        },
      );
    }

    return _buildFallback(fgColor);
  }

  Widget _buildFallback(Color fgColor) {
    if (initials != null && initials!.isNotEmpty) {
      return Center(
        child: Text(
          initials!.toUpperCase(),
          style: AppTypography.labelMedium.copyWith(
            color: fgColor,
            fontSize: size * 0.4,
            fontWeight: FontWeight.w600,
          ),
        ),
      );
    }

    return Icon(
      icon ?? Icons.person,
      color: fgColor,
      size: size * 0.6,
    );
  }
}
