import 'package:flutter/material.dart';

/// Sistema de Design Tokens
/// Tokens semánticos para construir un sistema de diseño escalable y consistente

// ============================================================================
// COLORES
// ============================================================================

class AppColors {
  AppColors._(); // Constructor privado para evitar instanciación

  // ─── Colores Principales ───────────────────────────────────────────────────
  static const Color primary = Color(0xFF0C2B44); // Azul Marino
  static const Color primaryLight = Color(0xFF1A4568);
  static const Color primaryDark = Color(0xFF081D2E);

  static const Color accent = Color(0xFF00A36C); // Verde Esmeralda
  static const Color accentLight = Color(0xFF33B584);
  static const Color accentDark = Color(0xFF008257);

  // ─── Neutrales ──────────────────────────────────────────────────────────────
  static const Color white = Color(0xFFFFFFFF);
  static const Color black = Color(0xFF000000);

  static const Color grey50 = Color(0xFFFAFAFA);
  static const Color grey100 = Color(0xFFF5F5F5);
  static const Color grey200 = Color(0xFFEEEEEE);
  static const Color grey300 = Color(0xFFE0E0E0);
  static const Color grey400 = Color(0xFFBDBDBD);
  static const Color grey500 = Color(0xFF9E9E9E);
  static const Color grey600 = Color(0xFF757575);
  static const Color grey700 = Color(0xFF616161);
  static const Color grey800 = Color(0xFF424242);
  static const Color grey900 = Color(0xFF333333);

  // ─── Semánticos ─────────────────────────────────────────────────────────────
  static const Color success = Color(0xFF00A36C);
  static const Color successLight = Color(0xFFE8F5F0);
  static const Color successDark = Color(0xFF008257);

  static const Color warning = Color(0xFFF59E0B);
  static const Color warningLight = Color(0xFFFEF3E2);
  static const Color warningDark = Color(0xFFD97706);

  static const Color error = Color(0xFFEF4444);
  static const Color errorLight = Color(0xFFFEE2E2);
  static const Color errorDark = Color(0xFFDC2626);

  static const Color info = Color(0xFF3B82F6);
  static const Color infoLight = Color(0xFFDCEAFE);
  static const Color infoDark = Color(0xFF2563EB);

  // ─── Backgrounds ────────────────────────────────────────────────────────────
  static const Color backgroundPrimary = white;
  static const Color backgroundSecondary = grey50;
  static const Color backgroundTertiary = grey100;

  // ─── Borders ────────────────────────────────────────────────────────────────
  static const Color borderLight = grey200;
  static const Color borderMedium = grey300;
  static const Color borderDark = grey400;

  // ─── Text ───────────────────────────────────────────────────────────────────
  static const Color textPrimary = grey900;
  static const Color textSecondary = grey600;
  static const Color textTertiary = grey500;
  static const Color textDisabled = grey400;
  static const Color textOnPrimary = white;
  static const Color textOnAccent = white;

  // ─── Overlays ───────────────────────────────────────────────────────────────
  static Color get scrim => black.withOpacity(0.32);
  static Color get overlay => black.withOpacity(0.08);
}

// ============================================================================
// ESPACIADO
// ============================================================================

class AppSpacing {
  AppSpacing._();

  static const double xxxs = 2.0;
  static const double xxs = 4.0;
  static const double xs = 8.0;
  static const double sm = 12.0;
  static const double md = 16.0;
  static const double lg = 24.0;
  static const double xl = 32.0;
  static const double xxl = 48.0;
  static const double xxxl = 64.0;

  // Espaciado específico para componentes
  static const double cardPadding = md;
  static const double listItemPadding = md;
  static const double sectionSpacing = lg;
  static const double screenPadding = md;
}

// ============================================================================
// BORDES Y RADIOS
// ============================================================================

class AppRadius {
  AppRadius._();

  static const double none = 0.0;
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 12.0;
  static const double lg = 16.0;
  static const double xl = 20.0;
  static const double xxl = 24.0;
  static const double full = 999.0;

  // Radios específicos para componentes
  static const double button = md;
  static const double card = lg;
  static const double input = md;
  static const double modal = xl;
  static const double avatar = full;
}

// ============================================================================
// ELEVACIONES Y SOMBRAS
// ============================================================================

class AppElevation {
  AppElevation._();

  static const double none = 0;
  static const double xs = 1;
  static const double sm = 2;
  static const double md = 4;
  static const double lg = 8;
  static const double xl = 12;
  static const double xxl = 16;

  // Sombras específicas
  static List<BoxShadow> get cardShadow => [
    BoxShadow(
      color: AppColors.black.withOpacity(0.04),
      blurRadius: 8,
      offset: const Offset(0, 2),
    ),
    BoxShadow(
      color: AppColors.black.withOpacity(0.02),
      blurRadius: 4,
      offset: const Offset(0, 1),
    ),
  ];

  static List<BoxShadow> get elevatedCardShadow => [
    BoxShadow(
      color: AppColors.black.withOpacity(0.08),
      blurRadius: 16,
      offset: const Offset(0, 4),
    ),
    BoxShadow(
      color: AppColors.black.withOpacity(0.04),
      blurRadius: 8,
      offset: const Offset(0, 2),
    ),
  ];

  static List<BoxShadow> get buttonShadow => [
    BoxShadow(
      color: AppColors.black.withOpacity(0.1),
      blurRadius: 8,
      offset: const Offset(0, 2),
    ),
  ];
}

// ============================================================================
// DURACIONES DE ANIMACIONES
// ============================================================================

class AppDuration {
  AppDuration._();

  static const Duration instant = Duration(milliseconds: 100);
  static const Duration fast = Duration(milliseconds: 200);
  static const Duration normal = Duration(milliseconds: 300);
  static const Duration slow = Duration(milliseconds: 500);

  // Duraciones específicas
  static const Duration buttonPress = fast;
  static const Duration pageTransition = normal;
  static const Duration modalAppear = normal;
  static const Duration shimmer = Duration(milliseconds: 1500);
}

// ============================================================================
// CURVAS DE ANIMACIÓN
// ============================================================================

class AppCurves {
  AppCurves._();

  static const Curve standard = Curves.easeInOut;
  static const Curve decelerate = Curves.easeOut;
  static const Curve accelerate = Curves.easeIn;
  static const Curve sharp = Curves.linear;

  // Curvas Material
  static const Curve emphasized = Curves.easeInOutCubic;
  static const Curve emphasizedDecelerate = Curves.easeOutCubic;
  static const Curve emphasizedAccelerate = Curves.easeInCubic;
}

// ============================================================================
// TAMAÑOS ESPECÍFICOS
// ============================================================================

class AppSizes {
  AppSizes._();

  // Icons
  static const double iconXs = 16.0;
  static const double iconSm = 20.0;
  static const double iconMd = 24.0;
  static const double iconLg = 32.0;
  static const double iconXl = 48.0;

  // Buttons
  static const double buttonHeightSm = 36.0;
  static const double buttonHeightMd = 44.0;
  static const double buttonHeightLg = 52.0;

  // Avatars
  static const double avatarXs = 24.0;
  static const double avatarSm = 32.0;
  static const double avatarMd = 40.0;
  static const double avatarLg = 56.0;
  static const double avatarXl = 80.0;

  // Cards
  static const double cardMinHeight = 100.0;
  static const double cardImageHeight = 200.0;

  // App Bar
  static const double appBarHeight = 56.0;
  static const double toolbarHeight = 56.0;

  // Bottom Navigation
  static const double bottomNavHeight = 60.0;

  // Inputs
  static const double inputHeight = 48.0;
  static const double inputHeightSm = 40.0;
}

// ============================================================================
// BREAKPOINTS RESPONSIVE
// ============================================================================

class AppBreakpoints {
  AppBreakpoints._();

  static const double mobile = 480;
  static const double tablet = 768;
  static const double desktop = 1024;
  static const double wide = 1440;
}
