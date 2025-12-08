import 'package:flutter/material.dart';

/// Paleta de colores compartida entre web (Laravel) y mobile (Flutter).
/// Basada en `PALETA_COLORES.md` del proyecto Laravel.
class AppColors {
  // Primario Azul Marino
  static const Color primary = Color(0xFF0C2B44);

  // Acento Verde Esmeralda
  static const Color accent = Color(0xFF00A36C);

  // Neutral Base Blanco Puro
  static const Color white = Color(0xFFFFFFFF);

  // Neutral Oscuro Gris Carbón
  static const Color greyDark = Color(0xFF333333);

  // Soporte Gris Suave
  static const Color greySoft = Color(0xFFF5F5F5);
}

class AppTheme {
  /// Tema claro principal de la aplicación, alineado con la paleta de Laravel.
  static ThemeData get lightTheme {
    // Material 3: esquema de color moderno basado en semilla
    final baseScheme = ColorScheme.fromSeed(
      seedColor: AppColors.primary,
      brightness: Brightness.light,
    );

    final colorScheme = baseScheme.copyWith(
      primary: AppColors.primary,
      secondary: AppColors.accent,
      background: AppColors.white,
      surface: AppColors.white,
    );

    return ThemeData(
      useMaterial3: true,
      colorScheme: colorScheme,
      scaffoldBackgroundColor: AppColors.white,

      // Tipografía Material 3
      textTheme: Typography.material2021().black.apply(
        bodyColor: AppColors.greyDark,
        displayColor: AppColors.greyDark,
      ),

      // AppBar moderno
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.white,
        foregroundColor: AppColors.greyDark,
        elevation: 0,
        centerTitle: false,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: const TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.w600,
        ).copyWith(color: AppColors.greyDark),
      ),

      // Buttons Material 3
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: AppColors.white,
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(foregroundColor: AppColors.primary),
      ),

      // Campos de texto tipo "filled"
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.greySoft,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.transparent),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.transparent),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: AppColors.accent, width: 2),
        ),
      ),

      // Cards modernas
      cardTheme: CardTheme(
        color: AppColors.white,
        elevation: 1,
        shadowColor: Colors.black.withOpacity(0.06),
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      ),

      // Drawer y NavigationDrawer modernos
      drawerTheme: DrawerThemeData(
        backgroundColor: AppColors.white,
        surfaceTintColor: Colors.transparent,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.horizontal(right: Radius.circular(24)),
        ),
        elevation: 0,
      ),
      navigationDrawerTheme: NavigationDrawerThemeData(
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        indicatorColor: AppColors.accent.withOpacity(0.12),
        indicatorShape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
      ),

      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: AppColors.white,
        surfaceTintColor: Colors.transparent,
        indicatorColor: AppColors.accent.withOpacity(0.12),
        elevation: 3,
        labelTextStyle: MaterialStateProperty.all<TextStyle>(
          const TextStyle(fontSize: 12, fontWeight: FontWeight.w500),
        ),
      ),

      iconButtonTheme: IconButtonThemeData(
        style: IconButton.styleFrom(
          padding: const EdgeInsets.all(8),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
      ),
    );
  }
}
