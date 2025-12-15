import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';

/// Botón reutilizable con 4 variantes siguiendo el sistema de diseño
/// Uso: AppButton.primary(), AppButton.secondary(), AppButton.outlined(), AppButton.text()
class AppButton extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final bool isLoading;
  final _AppButtonVariant _variant;
  final Size? minimumSize;

  const AppButton._({
    required this.label,
    required this.onPressed,
    required _AppButtonVariant variant,
    this.icon,
    this.isLoading = false,
    this.minimumSize,
  }) : _variant = variant;

  /// Botón primario (filled) - Acción principal
  factory AppButton.primary({
    required String label,
    required VoidCallback? onPressed,
    IconData? icon,
    bool isLoading = false,
    Size? minimumSize,
  }) {
    return AppButton._(
      label: label,
      onPressed: onPressed,
      variant: _AppButtonVariant.primary,
      icon: icon,
      isLoading: isLoading,
      minimumSize: minimumSize,
    );
  }

  /// Botón secundario (elevated) - Acción secundaria
  factory AppButton.secondary({
    required String label,
    required VoidCallback? onPressed,
    IconData? icon,
    bool isLoading = false,
    Size? minimumSize,
  }) {
    return AppButton._(
      label: label,
      onPressed: onPressed,
      variant: _AppButtonVariant.secondary,
      icon: icon,
      isLoading: isLoading,
      minimumSize: minimumSize,
    );
  }

  /// Botón outlined - Acción terciaria
  factory AppButton.outlined({
    required String label,
    required VoidCallback? onPressed,
    IconData? icon,
    bool isLoading = false,
    Size? minimumSize,
  }) {
    return AppButton._(
      label: label,
      onPressed: onPressed,
      variant: _AppButtonVariant.outlined,
      icon: icon,
      isLoading: isLoading,
      minimumSize: minimumSize,
    );
  }

  /// Botón text - Acción mínima/cancelar
  factory AppButton.text({
    required String label,
    required VoidCallback? onPressed,
    IconData? icon,
    bool isLoading = false,
    Size? minimumSize,
  }) {
    return AppButton._(
      label: label,
      onPressed: onPressed,
      variant: _AppButtonVariant.text,
      icon: icon,
      isLoading: isLoading,
      minimumSize: minimumSize,
    );
  }

  @override
  Widget build(BuildContext context) {
    final content = Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        if (isLoading)
          SizedBox(
            width: AppSizes.iconSm,
            height: AppSizes.iconSm,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              valueColor: AlwaysStoppedAnimation<Color>(
                _variant == _AppButtonVariant.primary
                    ? AppColors.textOnPrimary
                    : AppColors.primary,
              ),
            ),
          )
        else if (icon != null) ...[
          Icon(icon, size: AppSizes.iconSm),
          const SizedBox(width: AppSpacing.xs),
        ],
        Text(label),
      ],
    );

    return AnimatedOpacity(
      opacity: isLoading ? 0.6 : 1.0,
      duration: AppDuration.fast,
      child: _buildButton(content),
    );
  }

  Widget _buildButton(Widget content) {
    final bool enabled = onPressed != null && !isLoading;

    switch (_variant) {
      case _AppButtonVariant.primary:
        return FilledButton(
          onPressed: enabled ? onPressed : null,
          style: FilledButton.styleFrom(
            minimumSize: minimumSize ?? const Size(0, AppSizes.buttonHeightMd),
          ),
          child: content,
        );

      case _AppButtonVariant.secondary:
        return ElevatedButton(
          onPressed: enabled ? onPressed : null,
          style: ElevatedButton.styleFrom(
            minimumSize: minimumSize ?? const Size(0, AppSizes.buttonHeightMd),
          ),
          child: content,
        );

      case _AppButtonVariant.outlined:
        return OutlinedButton(
          onPressed: enabled ? onPressed : null,
          style: OutlinedButton.styleFrom(
            minimumSize: minimumSize ?? const Size(0, AppSizes.buttonHeightMd),
          ),
          child: content,
        );

      case _AppButtonVariant.text:
        return TextButton(
          onPressed: enabled ? onPressed : null,
          style: TextButton.styleFrom(
            minimumSize: minimumSize ?? const Size(0, AppSizes.buttonHeightSm),
          ),
          child: content,
        );
    }
  }
}

enum _AppButtonVariant {
  primary,
  secondary,
  outlined,
  text,
}
