import 'package:flutter/material.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'services/storage_service.dart';
import 'services/cache_service.dart';
import 'config/app_theme.dart';
import 'config/design_tokens.dart';
import 'config/typography_system.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Limpiar caché corrupto de versiones anteriores al iniciar
  // Esto asegura que datos antiguos no causen problemas
  try {
    await CacheService.clearAllCache();
    print('✅ Caché de dashboard limpiado al iniciar app');
  } catch (e) {
    print('⚠️ Error limpiando caché al iniciar: $e');
  }

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Redes Sociales',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const AuthWrapper(),
    );
  }
}

class AuthWrapper extends StatefulWidget {
  const AuthWrapper({super.key});

  @override
  State<AuthWrapper> createState() => _AuthWrapperState();
}

class _AuthWrapperState extends State<AuthWrapper> {
  bool _isLoading = true;
  bool _isLoggedIn = false;

  @override
  void initState() {
    super.initState();
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    // Delay artificial para mostrar el Splash Screen
    await Future.delayed(const Duration(seconds: 3));
    
    final loggedIn = await StorageService.isLoggedIn();
    if (mounted) {
      setState(() {
        _isLoggedIn = loggedIn;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedSwitcher(
      duration: AppDuration.slow,
      switchInCurve: AppCurves.standard,
      switchOutCurve: AppCurves.standard,
      transitionBuilder: (Widget child, Animation<double> animation) {
        return FadeTransition(opacity: animation, child: child);
      },
      child: _isLoading
          ? _buildSplashScreen()
          : (_isLoggedIn ? const HomeScreen() : const LoginScreen()),
    );
  }

  Widget _buildSplashScreen() {
    return Scaffold(
      key: const ValueKey('splash'),
      backgroundColor: AppColors.primary,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo simple con icono
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: AppDuration.slow,
              curve: AppCurves.emphasizedDecelerate,
              builder: (context, value, child) {
                return Transform.scale(
                  scale: value,
                  child: child,
                );
              },
              child: Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppColors.white.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.volunteer_activism,
                  size: 80,
                  color: AppColors.accent,
                ),
              ),
            ),
            const SizedBox(height: 24),
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: AppDuration.slow,
              curve: AppCurves.decelerate,
              builder: (context, value, child) {
                return Opacity(
                  opacity: value,
                  child: Transform.translate(
                    offset: Offset(0, 20 * (1 - value)),
                    child: child,
                  ),
                );
              },
              child: Column(
                children: [
                  Text(
                    'Redes Sociales',
                    style: AppTypography.headlineMedium.copyWith(
                      color: AppColors.textOnPrimary,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Conectando causas',
                    style: AppTypography.bodyMedium.copyWith(
                      color: AppColors.textOnPrimary.withOpacity(0.7),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
