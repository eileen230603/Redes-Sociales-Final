import 'package:flutter/material.dart';
import 'design_tokens.dart';

/// Sistema de Tipografía
/// Jerarquía tipográfica consistente y escalable para toda la aplicación

class AppTypography {
  AppTypography._();

  // ============================================================================
  // DISPLAY - Títulos muy grandes (marketing, hero sections)
  // ============================================================================

  static const TextStyle displayLarge = TextStyle(
    fontSize: 57,
    height: 1.12,
    letterSpacing: -0.25,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
  );

  static const TextStyle displayMedium = TextStyle(
    fontSize: 45,
    height: 1.16,
    letterSpacing: 0,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
  );

  static const TextStyle displaySmall = TextStyle(
    fontSize: 36,
    height: 1.22,
    letterSpacing: 0,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  // ============================================================================
  // HEADLINE - Títulos de secciones principales
  // ============================================================================

  static const TextStyle headlineLarge = TextStyle(
    fontSize: 32,
    height: 1.25,
    letterSpacing: 0,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  static const TextStyle headlineMedium = TextStyle(
    fontSize: 28,
    height: 1.29,
    letterSpacing: 0,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  static const TextStyle headlineSmall = TextStyle(
    fontSize: 24,
    height: 1.33,
    letterSpacing: 0,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  // ============================================================================
  // TITLE - Títulos de cards, modales, secciones menores
  // ============================================================================

  static const TextStyle titleLarge = TextStyle(
    fontSize: 22,
    height: 1.27,
    letterSpacing: 0,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
  );

  static const TextStyle titleMedium = TextStyle(
    fontSize: 18,
    height: 1.33,
    letterSpacing: 0.15,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
  );

  static const TextStyle titleSmall = TextStyle(
    fontSize: 16,
    height: 1.43,
    letterSpacing: 0.1,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
  );

  // ============================================================================
  // BODY - Texto principal de contenido
  // ============================================================================

  static const TextStyle bodyLarge = TextStyle(
    fontSize: 16,
    height: 1.5,
    letterSpacing: 0.15,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
  );

  static const TextStyle bodyMedium = TextStyle(
    fontSize: 14,
    height: 1.43,
    letterSpacing: 0.25,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
  );

  static const TextStyle bodySmall = TextStyle(
    fontSize: 12,
    height: 1.33,
    letterSpacing: 0.4,
    fontWeight: FontWeight.w400,
    color: AppColors.textSecondary,
  );

  // ============================================================================
  // LABEL - Etiquetas, botones, tabs
  // ============================================================================

  static const TextStyle labelLarge = TextStyle(
    fontSize: 14,
    height: 1.43,
    letterSpacing: 0.1,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
  );

  static const TextStyle labelMedium = TextStyle(
    fontSize: 12,
    height: 1.33,
    letterSpacing: 0.5,
    fontWeight: FontWeight.w500,
    color: AppColors.textSecondary,
  );

  static const TextStyle labelSmall = TextStyle(
    fontSize: 11,
    height: 1.45,
    letterSpacing: 0.5,
    fontWeight: FontWeight.w500,
    color: AppColors.textTertiary,
  );

  // ============================================================================
  // VARIANTES SEMÁNTICAS - Colores específicos por contexto
  // ============================================================================

  // Textos secundarios
  static TextStyle get bodySecondary => bodyMedium.copyWith(
    color: AppColors.textSecondary,
  );

  static TextStyle get bodyTertiary => bodySmall.copyWith(
    color: AppColors.textTertiary,
  );

  // Textos sobre fondos de color
  static TextStyle get bodyOnPrimary => bodyMedium.copyWith(
    color: AppColors.textOnPrimary,
  );

  static TextStyle get titleOnPrimary => titleMedium.copyWith(
    color: AppColors.textOnPrimary,
  );

  // Textos de estado
  static TextStyle get bodySuccess => bodyMedium.copyWith(
    color: AppColors.success,
  );

  static TextStyle get bodyError => bodyMedium.copyWith(
    color: AppColors.error,
  );

  static TextStyle get bodyWarning => bodyMedium.copyWith(
    color: AppColors.warning,
  );

  static TextStyle get bodyInfo => bodyMedium.copyWith(
    color: AppColors.info,
  );

  // Labels especiales
  static TextStyle get labelButton => labelLarge.copyWith(
    fontWeight: FontWeight.w600,
    letterSpacing: 0.5,
  );

  static TextStyle get labelChip => labelSmall.copyWith(
    fontWeight: FontWeight.w600,
  );

  // Metadata (fechas, contadores, etc.)
  static TextStyle get metadata => labelSmall.copyWith(
    color: AppColors.textTertiary,
  );

  // Helpers
  static TextStyle get helperText => labelSmall.copyWith(
    color: AppColors.textSecondary,
  );

  // Links
  static TextStyle get link => bodyMedium.copyWith(
    color: AppColors.primary,
    decoration: TextDecoration.underline,
  );

  // ============================================================================
  // UTILITARIOS - Modificadores rápidos
  // ============================================================================

  static TextStyle bold(TextStyle style) => style.copyWith(
    fontWeight: FontWeight.w700,
  );

  static TextStyle semiBold(TextStyle style) => style.copyWith(
    fontWeight: FontWeight.w600,
  );

  static TextStyle medium(TextStyle style) => style.copyWith(
    fontWeight: FontWeight.w500,
  );

  static TextStyle withColor(TextStyle style, Color color) => style.copyWith(
    color: color,
  );

  static TextStyle uppercase(TextStyle style) => style.copyWith(
    letterSpacing: 1.2,
  );

  // ============================================================================
  // TEXT THEME COMPLETO PARA MATERIAL
  // ============================================================================

  static TextTheme get textTheme => const TextTheme(
    displayLarge: displayLarge,
    displayMedium: displayMedium,
    displaySmall: displaySmall,
    headlineLarge: headlineLarge,
    headlineMedium: headlineMedium,
    headlineSmall: headlineSmall,
    titleLarge: titleLarge,
    titleMedium: titleMedium,
    titleSmall: titleSmall,
    bodyLarge: bodyLarge,
    bodyMedium: bodyMedium,
    bodySmall: bodySmall,
    labelLarge: labelLarge,
    labelMedium: labelMedium,
    labelSmall: labelSmall,
  );
}
