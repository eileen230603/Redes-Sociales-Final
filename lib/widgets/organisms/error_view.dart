import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../atoms/app_button.dart';

/// Vista de error completa con icono, mensaje y acciones
class ErrorView extends StatelessWidget {
  final String title;
  final String message;
  final String? actionLabel;
  final VoidCallback? onRetry;
  final IconData? icon;
  final bool showDetails;
  final String? errorDetails;

  const ErrorView({
    super.key,
    required this.title,
    required this.message,
    this.actionLabel,
    this.onRetry,
    this.icon,
    this.showDetails = false,
    this.errorDetails,
  });

  factory ErrorView.network({
    VoidCallback? onRetry,
  }) {
    return ErrorView(
      icon: Icons.wifi_off,
      title: 'Error de conexión',
      message: 'No se pudo conectar al servidor. Por favor, verifica tu conexión a internet.',
      actionLabel: 'Reintentar',
      onRetry: onRetry,
    );
  }

  factory ErrorView.serverError({
    VoidCallback? onRetry,
    String? errorDetails,
  }) {
    return ErrorView(
      icon: Icons.error_outline,
      title: 'Error del servidor',
      message: 'Ocurrió un error en el servidor. Por favor, intenta nuevamente.',
      actionLabel: 'Reintentar',
      onRetry: onRetry,
      showDetails: errorDetails != null,
      errorDetails: errorDetails,
    );
  }

  factory ErrorView.notFound() {
    return const ErrorView(
      icon: Icons.search_off,
      title: 'No encontrado',
      message: 'El contenido que buscas no existe o ha sido eliminado.',
    );
  }

  factory ErrorView.unauthorized({
    VoidCallback? onRetry,
  }) {
    return ErrorView(
      icon: Icons.lock_outline,
      title: 'Acceso denegado',
      message: 'No tienes permisos para acceder a este contenido.',
      actionLabel: 'Volver',
      onRetry: onRetry,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.xl),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: AppDuration.normal,
              curve: AppCurves.emphasizedDecelerate,
              builder: (context, value, child) {
                return Transform.scale(
                  scale: 0.5 + (value * 0.5),
                  child: Opacity(
                    opacity: value,
                    child: child,
                  ),
                );
              },
              child: Container(
                padding: const EdgeInsets.all(AppSpacing.lg),
                decoration: BoxDecoration(
                  color: AppColors.errorLight,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  icon ?? Icons.error_outline,
                  size: AppSizes.iconXl,
                  color: AppColors.error,
                ),
              ),
            ),
            const SizedBox(height: AppSpacing.lg),
            Text(
              title,
              style: AppTypography.headlineSmall,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: AppSpacing.sm),
            Text(
              message,
              style: AppTypography.bodySecondary,
              textAlign: TextAlign.center,
            ),
            if (showDetails && errorDetails != null) ...[
              const SizedBox(height: AppSpacing.md),
              Container(
                padding: const EdgeInsets.all(AppSpacing.sm),
                decoration: BoxDecoration(
                  color: AppColors.grey100,
                  borderRadius: BorderRadius.circular(AppRadius.sm),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Detalles técnicos:',
                      style: AppTypography.labelMedium,
                    ),
                    const SizedBox(height: AppSpacing.xxs),
                    Text(
                      errorDetails!,
                      style: AppTypography.bodySmall.copyWith(
                        fontFamily: 'monospace',
                      ),
                    ),
                  ],
                ),
              ),
            ],
            if (actionLabel != null && onRetry != null) ...[
              const SizedBox(height: AppSpacing.lg),
              AppButton.primary(
                label: actionLabel!,
                onPressed: onRetry,
                icon: Icons.refresh,
              ),
            ],
          ],
        ),
      ),
    );
  }
}
