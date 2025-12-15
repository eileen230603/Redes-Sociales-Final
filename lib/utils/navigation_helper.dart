import 'package:flutter/material.dart';
import '../config/design_tokens.dart';

/// Helper para navegación con animaciones personalizadas
class NavigationHelper {
  /// Navegación con animación de deslizamiento desde la derecha
  static Route<T> slideRightRoute<T extends Object?>(
    Widget page, {
    RouteSettings? settings,
  }) {
    return PageRouteBuilder<T>(
      settings: settings,
      transitionDuration: AppDuration.pageTransition,
      reverseTransitionDuration: AppDuration.pageTransition,
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        const begin = Offset(1.0, 0.0);
        const end = Offset.zero;
        const curve = AppCurves.standard;

        var tween = Tween(
          begin: begin,
          end: end,
        ).chain(CurveTween(curve: curve));

        return SlideTransition(position: animation.drive(tween), child: child);
      },
    );
  }

  /// Navegación con animación de deslizamiento desde abajo
  static Route<T> slideUpRoute<T extends Object?>(
    Widget page, {
    RouteSettings? settings,
  }) {
    return PageRouteBuilder<T>(
      settings: settings,
      transitionDuration: AppDuration.pageTransition,
      reverseTransitionDuration: AppDuration.pageTransition,
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        const begin = Offset(0.0, 1.0);
        const end = Offset.zero;
        const curve = AppCurves.decelerate;

        var tween = Tween(
          begin: begin,
          end: end,
        ).chain(CurveTween(curve: curve));

        return SlideTransition(position: animation.drive(tween), child: child);
      },
    );
  }

  /// Navegación con animación de fade (desvanecimiento)
  static Route<T> fadeRoute<T extends Object?>(
    Widget page, {
    RouteSettings? settings,
  }) {
    return PageRouteBuilder<T>(
      settings: settings,
      transitionDuration: AppDuration.pageTransition,
      reverseTransitionDuration: AppDuration.pageTransition,
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        return FadeTransition(
          opacity: CurvedAnimation(parent: animation, curve: AppCurves.standard),
          child: child,
        );
      },
    );
  }

  /// Navegación con animación de escala
  static Route<T> scaleRoute<T extends Object?>(
    Widget page, {
    RouteSettings? settings,
  }) {
    return PageRouteBuilder<T>(
      settings: settings,
      transitionDuration: AppDuration.pageTransition,
      reverseTransitionDuration: AppDuration.pageTransition,
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        return ScaleTransition(
          scale: Tween<double>(begin: 0.0, end: 1.0).animate(
            CurvedAnimation(parent: animation, curve: AppCurves.emphasizedDecelerate),
          ),
          child: child,
        );
      },
    );
  }

  /// Navegación con animación de rotación y escala
  static Route<T> rotationRoute<T extends Object?>(
    Widget page, {
    RouteSettings? settings,
  }) {
    return PageRouteBuilder<T>(
      settings: settings,
      transitionDuration: AppDuration.pageTransition,
      reverseTransitionDuration: AppDuration.pageTransition,
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        return RotationTransition(
          turns: Tween<double>(begin: 0.0, end: 1.0).animate(
            CurvedAnimation(parent: animation, curve: AppCurves.standard),
          ),
          child: ScaleTransition(
            scale: Tween<double>(begin: 0.0, end: 1.0).animate(
              CurvedAnimation(parent: animation, curve: AppCurves.emphasizedDecelerate),
            ),
            child: child,
          ),
        );
      },
    );
  }
}
