import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/app_list_tile.dart';
import 'login_screen.dart';
import 'register_screen.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _fade;
  late final Animation<Offset> _slide;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: AppDuration.pageTransition,
    )..forward();
    _fade = CurvedAnimation(parent: _controller, curve: AppCurves.decelerate);
    _slide = Tween<Offset>(
      begin: const Offset(0, 0.04),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _controller, curve: AppCurves.decelerate));
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _goToLogin() {
    Navigator.of(context).push(
      MaterialPageRoute(builder: (context) => const LoginScreen()),
    );
  }

  void _goToRegister() {
    Navigator.of(context).push(
      MaterialPageRoute(builder: (context) => const RegisterScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('UNI2'),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: AppSpacing.xs),
            child: AppButton.text(
              label: 'Iniciar sesión',
              icon: Icons.login,
              onPressed: _goToLogin,
              minimumSize: const Size(0, AppSizes.buttonHeightSm),
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: FadeTransition(
          opacity: _fade,
          child: SlideTransition(
            position: _slide,
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 900),
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Red social solidaria', style: AppTypography.headlineLarge),
                      const SizedBox(height: AppSpacing.sm),
                      Text(
                        'Conecta ONGs, empresas y voluntarios para impulsar causas con impacto real.',
                        style: AppTypography.bodySecondary,
                      ),
                      const SizedBox(height: AppSpacing.lg),
                      Wrap(
                        spacing: AppSpacing.sm,
                        runSpacing: AppSpacing.sm,
                        children: [
                          AppButton.primary(
                            label: 'Crear cuenta',
                            icon: Icons.person_add,
                            onPressed: _goToRegister,
                            minimumSize: const Size(220, AppSizes.buttonHeightMd),
                          ),
                          AppButton.secondary(
                            label: 'Explorar e iniciar sesión',
                            icon: Icons.explore,
                            onPressed: _goToLogin,
                            minimumSize: const Size(260, AppSizes.buttonHeightMd),
                          ),
                        ],
                      ),
                      const SizedBox(height: AppSpacing.xl),
                      _Features(),
                      const SizedBox(height: AppSpacing.xl),
                      AppCard(
                        elevated: true,
                        backgroundColor: AppColors.primary,
                        child: Row(
                          children: [
                            Container(
                              width: AppSizes.avatarLg,
                              height: AppSizes.avatarLg,
                              decoration: BoxDecoration(
                                color: AppColors.white.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(AppRadius.md),
                              ),
                              child: const Center(
                                child: Icon(
                                  Icons.volunteer_activism,
                                  color: AppColors.textOnPrimary,
                                ),
                              ),
                            ),
                            const SizedBox(width: AppSpacing.md),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    '¿Listo para participar?',
                                    style: AppTypography.titleLarge.copyWith(
                                      color: AppColors.textOnPrimary,
                                    ),
                                  ),
                                  const SizedBox(height: AppSpacing.xxs),
                                  Text(
                                    'Regístrate y comienza a colaborar hoy.',
                                    style: AppTypography.bodyMedium.copyWith(
                                      color: AppColors.textOnPrimary.withOpacity(0.8),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: AppSpacing.lg),
                      Text(
                        '© 2025 UNI2. Conectando comunidades, transformando vidas.',
                        style: AppTypography.labelSmall,
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _Features extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Qué puedes hacer', style: AppTypography.titleLarge),
          const SizedBox(height: AppSpacing.sm),
          AppListTile(
            leading: AppIcon.md(Icons.event, color: AppColors.primary),
            title: 'Participar en eventos',
            subtitle: 'Encuentra eventos publicados y suma puntos por tu impacto.',
            showDivider: true,
          ),
          AppListTile(
            leading: AppIcon.md(Icons.favorite, color: AppColors.accent),
            title: 'Gestionar campañas',
            subtitle: 'Si eres ONG, crea y administra eventos y mega eventos.',
            showDivider: true,
          ),
          AppListTile(
            leading: AppIcon.md(Icons.business, color: AppColors.info),
            title: 'Patrocinar y colaborar',
            subtitle: 'Si eres empresa, apoya causas y mide tu participación.',
          ),
        ],
      ),
    );
  }
}
