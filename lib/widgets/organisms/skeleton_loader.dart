import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';

/// Skeleton loader reutilizable para diferentes layouts
class SkeletonLoader extends StatefulWidget {
  final Widget child;

  const SkeletonLoader({
    super.key,
    required this.child,
  });

  /// Skeleton para dashboard con métricas
  factory SkeletonLoader.dashboard() {
    return SkeletonLoader(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            _SkeletonBox(width: 200, height: 28),
            const SizedBox(height: AppSpacing.lg),

            // Métricas
            Row(
              children: [
                Expanded(
                  child: _SkeletonBox(height: 100),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: _SkeletonBox(height: 100),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.md),
            Row(
              children: [
                Expanded(
                  child: _SkeletonBox(height: 100),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: _SkeletonBox(height: 100),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),

            // Gráfico
            _SkeletonBox(width: double.infinity, height: 250),
          ],
        ),
      ),
    );
  }

  /// Skeleton para lista de cards
  factory SkeletonLoader.cardList({int itemCount = 5}) {
    return SkeletonLoader(
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: itemCount,
        separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.md),
        itemBuilder: (context, index) {
          return _SkeletonBox(height: 120);
        },
      ),
    );
  }

  /// Skeleton para detalle de evento
  factory SkeletonLoader.eventDetail() {
    return SkeletonLoader(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Imagen
            _SkeletonBox(width: double.infinity, height: 200),
            const SizedBox(height: AppSpacing.lg),

            // Título
            _SkeletonBox(width: 250, height: 28),
            const SizedBox(height: AppSpacing.sm),

            // Fecha y ubicación
            _SkeletonBox(width: 180, height: 16),
            const SizedBox(height: AppSpacing.xs),
            _SkeletonBox(width: 200, height: 16),
            const SizedBox(height: AppSpacing.lg),

            // Descripción
            _SkeletonBox(width: double.infinity, height: 16),
            const SizedBox(height: AppSpacing.xs),
            _SkeletonBox(width: double.infinity, height: 16),
            const SizedBox(height: AppSpacing.xs),
            _SkeletonBox(width: double.infinity, height: 16),
            const SizedBox(height: AppSpacing.xs),
            _SkeletonBox(width: 180, height: 16),
          ],
        ),
      ),
    );
  }

  @override
  State<SkeletonLoader> createState() => _SkeletonLoaderState();
}

class _SkeletonLoaderState extends State<SkeletonLoader>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: AppDuration.shimmer,
      vsync: this,
    )..repeat();
    _animation = Tween<double>(begin: -1.0, end: 2.0).animate(
      CurvedAnimation(parent: _controller, curve: AppCurves.standard),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return _SkeletonContext(
      animation: _animation,
      child: widget.child,
    );
  }
}

class _SkeletonContext extends InheritedWidget {
  final Animation<double> animation;

  const _SkeletonContext({
    required this.animation,
    required super.child,
  });

  static _SkeletonContext? of(BuildContext context) {
    return context.dependOnInheritedWidgetOfExactType<_SkeletonContext>();
  }

  @override
  bool updateShouldNotify(_SkeletonContext oldWidget) {
    return animation != oldWidget.animation;
  }
}

class _SkeletonBox extends StatelessWidget {
  final double? width;
  final double height;

  const _SkeletonBox({
    this.width,
    required this.height,
  });

  @override
  Widget build(BuildContext context) {
    final skeletonContext = _SkeletonContext.of(context);

    return AnimatedBuilder(
      animation: skeletonContext?.animation ?? const AlwaysStoppedAnimation(0),
      builder: (context, child) {
        final animationValue = skeletonContext?.animation.value ?? 0.0;

        return Container(
          width: width,
          height: height,
          decoration: BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.circular(AppRadius.card),
            border: Border.all(color: AppColors.borderLight),
            gradient: LinearGradient(
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
              colors: [
                AppColors.grey200,
                AppColors.grey100,
                AppColors.grey200,
              ],
              stops: [
                (animationValue - 0.3).clamp(0.0, 1.0),
                animationValue.clamp(0.0, 1.0),
                (animationValue + 0.3).clamp(0.0, 1.0),
              ],
            ),
          ),
        );
      },
    );
  }
}
