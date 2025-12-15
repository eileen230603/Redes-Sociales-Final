import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'design_tokens.dart';
import 'typography_system.dart';

/// Tema principal de la aplicación
/// Sistema de diseño unificado, moderno y profesional

class AppTheme {
  AppTheme._();

  /// Tema claro principal - Diseño limpio, minimalista y profesional
  static ThemeData get lightTheme {
    final colorScheme = ColorScheme.light(
      primary: AppColors.primary,
      primaryContainer: AppColors.primaryLight,
      secondary: AppColors.accent,
      secondaryContainer: AppColors.accentLight,
      error: AppColors.error,
      errorContainer: AppColors.errorLight,
      background: AppColors.backgroundPrimary,
      surface: AppColors.white,
      surfaceVariant: AppColors.grey100,
      outline: AppColors.borderLight,
      outlineVariant: AppColors.borderMedium,
      shadow: AppColors.black.withOpacity(0.1),
      scrim: AppColors.scrim,
      inverseSurface: AppColors.grey900,
      onPrimary: AppColors.textOnPrimary,
      onSecondary: AppColors.textOnAccent,
      onError: AppColors.white,
      onBackground: AppColors.textPrimary,
      onSurface: AppColors.textPrimary,
      onSurfaceVariant: AppColors.textSecondary,
    );

    return ThemeData(
      useMaterial3: true,
      colorScheme: colorScheme,
      scaffoldBackgroundColor: AppColors.backgroundPrimary,
      brightness: Brightness.light,

      // ────────────────────────────────────────────────────────────────────────
      // TIPOGRAFÍA
      // ────────────────────────────────────────────────────────────────────────
      textTheme: AppTypography.textTheme,

      // ────────────────────────────────────────────────────────────────────────
      // APP BAR
      // ────────────────────────────────────────────────────────────────────────
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.white,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        centerTitle: false,
        scrolledUnderElevation: 0,
        surfaceTintColor: Colors.transparent,
        systemOverlayStyle: SystemUiOverlayStyle.dark,
        titleTextStyle: AppTypography.titleLarge,
        titleSpacing: AppSpacing.md,
        toolbarHeight: AppSizes.appBarHeight,
        iconTheme: const IconThemeData(
          color: AppColors.textPrimary,
          size: AppSizes.iconMd,
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // BOTONES
      // ────────────────────────────────────────────────────────────────────────

      // Filled Button (Primary)
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: AppColors.textOnPrimary,
          disabledBackgroundColor: AppColors.grey300,
          disabledForegroundColor: AppColors.textDisabled,
          elevation: AppElevation.xs,
          shadowColor: AppColors.black.withOpacity(0.1),
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.lg,
            vertical: AppSpacing.sm,
          ),
          minimumSize: const Size(0, AppSizes.buttonHeightMd),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button),
          ),
          textStyle: AppTypography.labelButton,
        ),
      ),

      // Elevated Button
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.white,
          foregroundColor: AppColors.primary,
          elevation: AppElevation.sm,
          shadowColor: AppColors.black.withOpacity(0.08),
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.lg,
            vertical: AppSpacing.sm,
          ),
          minimumSize: const Size(0, AppSizes.buttonHeightMd),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button),
            side: BorderSide(color: AppColors.borderLight, width: 1),
          ),
          textStyle: AppTypography.labelButton,
        ),
      ),

      // Outlined Button
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: AppColors.primary,
          side: BorderSide(color: AppColors.borderMedium, width: 1.5),
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.lg,
            vertical: AppSpacing.sm,
          ),
          minimumSize: const Size(0, AppSizes.buttonHeightMd),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button),
          ),
          textStyle: AppTypography.labelButton,
        ),
      ),

      // Text Button
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.primary,
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.md,
            vertical: AppSpacing.xs,
          ),
          minimumSize: const Size(0, AppSizes.buttonHeightSm),
          textStyle: AppTypography.labelButton,
        ),
      ),

      // Icon Button
      iconButtonTheme: IconButtonThemeData(
        style: IconButton.styleFrom(
          foregroundColor: AppColors.textPrimary,
          iconSize: AppSizes.iconMd,
          padding: const EdgeInsets.all(AppSpacing.xs),
          minimumSize: const Size(AppSizes.buttonHeightSm, AppSizes.buttonHeightSm),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.sm),
          ),
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // INPUTS
      // ────────────────────────────────────────────────────────────────────────
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.grey50,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.md,
          vertical: AppSpacing.sm,
        ),

        // Borders
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide(color: AppColors.accent, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide(color: AppColors.error, width: 1),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide(color: AppColors.error, width: 2),
        ),
        disabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.input),
          borderSide: BorderSide.none,
        ),

        // Text Styles
        labelStyle: AppTypography.labelMedium,
        floatingLabelStyle: AppTypography.labelMedium.copyWith(
          color: AppColors.accent,
        ),
        hintStyle: AppTypography.bodyMedium.copyWith(
          color: AppColors.textTertiary,
        ),
        helperStyle: AppTypography.helperText,
        errorStyle: AppTypography.labelSmall.copyWith(
          color: AppColors.error,
        ),

        // Icons
        prefixIconColor: AppColors.textSecondary,
        suffixIconColor: AppColors.textSecondary,
      ),

      // ────────────────────────────────────────────────────────────────────────
      // CARDS
      // ────────────────────────────────────────────────────────────────────────
      cardTheme: CardThemeData(
        color: AppColors.white,
        elevation: 0,
        shadowColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.card),
          side: BorderSide(color: AppColors.borderLight, width: 1),
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // DIALOGS & MODALS
      // ────────────────────────────────────────────────────────────────────────
      dialogTheme: DialogThemeData(
        backgroundColor: AppColors.white,
        elevation: AppElevation.xl,
        shadowColor: AppColors.black.withOpacity(0.1),
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.modal),
        ),
        titleTextStyle: AppTypography.headlineSmall,
        contentTextStyle: AppTypography.bodyMedium,
      ),

      bottomSheetTheme: BottomSheetThemeData(
        backgroundColor: AppColors.white,
        elevation: AppElevation.xl,
        modalBackgroundColor: AppColors.white,
        modalElevation: AppElevation.xl,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(
            top: Radius.circular(AppRadius.modal),
          ),
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // NAVIGATION
      // ────────────────────────────────────────────────────────────────────────
      drawerTheme: DrawerThemeData(
        backgroundColor: AppColors.white,
        elevation: AppElevation.lg,
        surfaceTintColor: Colors.transparent,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.horizontal(
            right: Radius.circular(AppRadius.xxl),
          ),
        ),
      ),

      navigationDrawerTheme: NavigationDrawerThemeData(
        backgroundColor: AppColors.white,
        surfaceTintColor: Colors.transparent,
        indicatorColor: AppColors.accentLight,
        indicatorShape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.lg),
        ),
        labelTextStyle: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppTypography.labelLarge.copyWith(color: AppColors.accent);
          }
          return AppTypography.labelLarge.copyWith(color: AppColors.textSecondary);
        }),
      ),

      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: AppColors.white,
        surfaceTintColor: Colors.transparent,
        indicatorColor: AppColors.accentLight,
        elevation: AppElevation.sm,
        height: AppSizes.bottomNavHeight,
        labelTextStyle: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppTypography.labelMedium.copyWith(color: AppColors.accent);
          }
          return AppTypography.labelMedium;
        }),
        iconTheme: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return IconThemeData(color: AppColors.accent, size: AppSizes.iconMd);
          }
          return IconThemeData(color: AppColors.textSecondary, size: AppSizes.iconMd);
        }),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // CHIPS
      // ────────────────────────────────────────────────────────────────────────
      chipTheme: ChipThemeData(
        backgroundColor: AppColors.grey100,
        deleteIconColor: AppColors.textSecondary,
        disabledColor: AppColors.grey200,
        selectedColor: AppColors.accentLight,
        secondarySelectedColor: AppColors.primaryLight,
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.sm,
          vertical: AppSpacing.xxs,
        ),
        labelStyle: AppTypography.labelChip,
        secondaryLabelStyle: AppTypography.labelChip,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.full),
        ),
        elevation: 0,
        pressElevation: 0,
      ),

      // ────────────────────────────────────────────────────────────────────────
      // DIVIDER
      // ────────────────────────────────────────────────────────────────────────
      dividerTheme: const DividerThemeData(
        color: AppColors.borderLight,
        thickness: 1,
        space: AppSpacing.md,
      ),

      // ────────────────────────────────────────────────────────────────────────
      // PROGRESS INDICATORS
      // ────────────────────────────────────────────────────────────────────────
      progressIndicatorTheme: const ProgressIndicatorThemeData(
        color: AppColors.accent,
        linearTrackColor: AppColors.grey200,
        circularTrackColor: AppColors.grey200,
      ),

      // ────────────────────────────────────────────────────────────────────────
      // SNACKBAR
      // ────────────────────────────────────────────────────────────────────────
      snackBarTheme: SnackBarThemeData(
        backgroundColor: AppColors.grey900,
        contentTextStyle: AppTypography.bodyMedium.copyWith(
          color: AppColors.white,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.sm),
        ),
        behavior: SnackBarBehavior.floating,
        elevation: AppElevation.md,
      ),

      // ────────────────────────────────────────────────────────────────────────
      // LIST TILES
      // ────────────────────────────────────────────────────────────────────────
      listTileTheme: ListTileThemeData(
        contentPadding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.md,
          vertical: AppSpacing.xs,
        ),
        minLeadingWidth: AppSizes.iconLg,
        iconColor: AppColors.textSecondary,
        textColor: AppColors.textPrimary,
        titleTextStyle: AppTypography.titleSmall,
        subtitleTextStyle: AppTypography.bodySecondary,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.sm),
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // SWITCHES & CHECKBOXES
      // ────────────────────────────────────────────────────────────────────────
      switchTheme: SwitchThemeData(
        thumbColor: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppColors.accent;
          }
          return AppColors.grey400;
        }),
        trackColor: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppColors.accentLight;
          }
          return AppColors.grey300;
        }),
      ),

      checkboxTheme: CheckboxThemeData(
        fillColor: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppColors.accent;
          }
          return Colors.transparent;
        }),
        checkColor: MaterialStateProperty.all(AppColors.white),
        side: BorderSide(color: AppColors.borderMedium, width: 2),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.xs),
        ),
      ),

      radioTheme: RadioThemeData(
        fillColor: MaterialStateProperty.resolveWith((states) {
          if (states.contains(MaterialState.selected)) {
            return AppColors.accent;
          }
          return AppColors.borderMedium;
        }),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // TOOLTIPS
      // ────────────────────────────────────────────────────────────────────────
      tooltipTheme: TooltipThemeData(
        decoration: BoxDecoration(
          color: AppColors.grey900,
          borderRadius: BorderRadius.circular(AppRadius.xs),
        ),
        textStyle: AppTypography.labelSmall.copyWith(
          color: AppColors.white,
        ),
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.sm,
          vertical: AppSpacing.xxs,
        ),
        waitDuration: const Duration(milliseconds: 500),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // FLOATING ACTION BUTTON
      // ────────────────────────────────────────────────────────────────────────
      floatingActionButtonTheme: FloatingActionButtonThemeData(
        backgroundColor: AppColors.accent,
        foregroundColor: AppColors.textOnAccent,
        elevation: AppElevation.md,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.lg),
        ),
      ),

      // ────────────────────────────────────────────────────────────────────────
      // PAGE TRANSITIONS
      // ────────────────────────────────────────────────────────────────────────
      pageTransitionsTheme: const PageTransitionsTheme(
        builders: {
          TargetPlatform.android: CupertinoPageTransitionsBuilder(),
          TargetPlatform.iOS: CupertinoPageTransitionsBuilder(),
          TargetPlatform.linux: FadeUpwardsPageTransitionsBuilder(),
          TargetPlatform.macOS: CupertinoPageTransitionsBuilder(),
          TargetPlatform.windows: FadeUpwardsPageTransitionsBuilder(),
        },
      ),
    );
  }
}
