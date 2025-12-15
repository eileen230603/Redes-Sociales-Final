import 'package:flutter/material.dart';
import '../../config/design_tokens.dart';

/// Estado de carga con skeleton loaders en lugar de spinners
class LoadingState extends StatefulWidget {
  final _LoadingStateType _type;

  const LoadingState._({required _LoadingStateType type}) : _type = type;

  factory LoadingState.card() {
    return const LoadingState._(type: _LoadingStateType.card);
  }

  factory LoadingState.list({int itemCount = 3}) {
    return LoadingState._(type: _LoadingStateType.list);
  }

  factory LoadingState.detail() {
    return const LoadingState._(type: _LoadingStateType.detail);
  }

  @override
  State<LoadingState> createState() => _LoadingStateState();
}

class _LoadingStateState extends State<LoadingState>
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
    switch (widget._type) {
      case _LoadingStateType.card:
        return _buildCardSkeleton();
      case _LoadingStateType.list:
        return _buildListSkeleton();
      case _LoadingStateType.detail:
        return _buildDetailSkeleton();
    }
  }

  Widget _buildCardSkeleton() {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(AppRadius.card),
          border: Border.all(color: AppColors.borderLight),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildShimmer(width: double.infinity, height: 20),
            const SizedBox(height: AppSpacing.sm),
            _buildShimmer(width: double.infinity, height: 14),
            const SizedBox(height: AppSpacing.xs),
            _buildShimmer(width: 200, height: 14),
          ],
        ),
      ),
    );
  }

  Widget _buildListSkeleton() {
    return ListView.separated(
      padding: const EdgeInsets.all(AppSpacing.md),
      itemCount: 5,
      separatorBuilder: (context, index) => const SizedBox(height: AppSpacing.sm),
      itemBuilder: (context, index) {
        return Container(
          padding: const EdgeInsets.all(AppSpacing.md),
          decoration: BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.circular(AppRadius.card),
            border: Border.all(color: AppColors.borderLight),
          ),
          child: Row(
            children: [
              _buildShimmer(width: 48, height: 48, isCircle: true),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildShimmer(width: double.infinity, height: 16),
                    const SizedBox(height: AppSpacing.xs),
                    _buildShimmer(width: 150, height: 12),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildDetailSkeleton() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildShimmer(width: double.infinity, height: 200),
          const SizedBox(height: AppSpacing.lg),
          _buildShimmer(width: 250, height: 28),
          const SizedBox(height: AppSpacing.sm),
          _buildShimmer(width: double.infinity, height: 16),
          const SizedBox(height: AppSpacing.xs),
          _buildShimmer(width: double.infinity, height: 16),
          const SizedBox(height: AppSpacing.xs),
          _buildShimmer(width: 180, height: 16),
        ],
      ),
    );
  }

  Widget _buildShimmer({
    required double width,
    required double height,
    bool isCircle = false,
  }) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) {
        return Container(
          width: width,
          height: height,
          decoration: BoxDecoration(
            color: AppColors.grey200,
            borderRadius: isCircle
                ? BorderRadius.circular(height / 2)
                : BorderRadius.circular(AppRadius.xs),
            gradient: LinearGradient(
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
              colors: [
                AppColors.grey200,
                AppColors.grey100,
                AppColors.grey200,
              ],
              stops: [
                _animation.value - 0.3,
                _animation.value,
                _animation.value + 0.3,
              ].map((e) => e.clamp(0.0, 1.0)).toList(),
            ),
          ),
        );
      },
    );
  }
}

enum _LoadingStateType {
  card,
  list,
  detail,
}
