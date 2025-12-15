import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/api_service.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/app_list_tile.dart';
import 'home_screen.dart';
import 'register_form_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
    });

    try {
      final response = await ApiService.login(
        email: _emailController.text.trim(),
        password: _passwordController.text,
      );

      if (!mounted) return;

      if (response.success) {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const HomeScreen()),
        );
        return;
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response.error ?? 'Error al iniciar sesión'),
          backgroundColor: AppColors.error,
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
          backgroundColor: AppColors.error,
        ),
      );
    } finally {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _goToRegister(String tipoUsuario) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (context) => RegisterFormScreen(tipoUsuario: tipoUsuario),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 460),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const SizedBox(height: AppSpacing.lg),
                  const _Header(),
                  const SizedBox(height: AppSpacing.lg),
                  AppCard(
                    elevated: true,
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Text('Inicia sesión', style: AppTypography.headlineSmall),
                          const SizedBox(height: AppSpacing.sm),
                          Text(
                            'Ingresa tus credenciales para continuar.',
                            style: AppTypography.bodySecondary,
                          ),
                          const SizedBox(height: AppSpacing.lg),
                          TextFormField(
                            controller: _emailController,
                            keyboardType: TextInputType.emailAddress,
                            decoration: const InputDecoration(
                              labelText: 'Correo electrónico',
                              prefixIcon: Icon(Icons.email_outlined),
                            ),
                            validator: (value) {
                              if (value == null || value.trim().isEmpty) {
                                return 'Por favor ingresa tu correo';
                              }
                              if (!value.contains('@')) {
                                return 'Ingresa un correo válido';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: AppSpacing.md),
                          TextFormField(
                            controller: _passwordController,
                            obscureText: _obscurePassword,
                            decoration: InputDecoration(
                              labelText: 'Contraseña',
                              prefixIcon: const Icon(Icons.lock_outline),
                              suffixIcon: IconButton(
                                icon: AppIcon.sm(
                                  _obscurePassword
                                      ? Icons.visibility_outlined
                                      : Icons.visibility_off_outlined,
                                ),
                                onPressed: () {
                                  setState(() {
                                    _obscurePassword = !_obscurePassword;
                                  });
                                },
                              ),
                            ),
                            validator: (value) {
                              if (value == null || value.isEmpty) {
                                return 'Por favor ingresa tu contraseña';
                              }
                              if (value.length < 6) {
                                return 'La contraseña debe tener al menos 6 caracteres';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: AppSpacing.lg),
                          AppButton.primary(
                            label: 'Iniciar sesión',
                            onPressed: _isLoading ? null : _handleLogin,
                            icon: Icons.login,
                            isLoading: _isLoading,
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  Text(
                    '¿No tienes cuenta?',
                    style: AppTypography.titleMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Regístrate como:',
                    style: AppTypography.bodySecondary,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: AppSpacing.md),
                  AppCard(
                    child: Column(
                      children: [
                        AppListTile(
                          leading: AppIcon.md(Icons.person, color: AppColors.primary),
                          title: 'Integrante externo',
                          subtitle: 'Participa en eventos y acumula puntos.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister('Integrante externo'),
                          showDivider: true,
                        ),
                        AppListTile(
                          leading: AppIcon.md(Icons.favorite, color: AppColors.accent),
                          title: 'ONG',
                          subtitle: 'Crea y gestiona eventos para tu comunidad.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister('ONG'),
                          showDivider: true,
                        ),
                        AppListTile(
                          leading: AppIcon.md(Icons.business, color: AppColors.info),
                          title: 'Empresa',
                          subtitle: 'Patrocina y colabora con eventos.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister('Empresa'),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  Text(
                    '© 2025 UNI2. Conectando comunidades, transformando vidas.',
                    textAlign: TextAlign.center,
                    style: AppTypography.labelSmall,
                  ),
                  const SizedBox(height: AppSpacing.md),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _Header extends StatelessWidget {
  const _Header();

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Container(
          width: AppSizes.avatarLg,
          height: AppSizes.avatarLg,
          decoration: BoxDecoration(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(AppRadius.md),
          ),
          child: const Center(
            child: Icon(Icons.groups_2, color: AppColors.textOnPrimary),
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        Text('UNI2', style: AppTypography.headlineMedium),
        const SizedBox(height: AppSpacing.xxs),
        Text(
          'Red social solidaria',
          style: AppTypography.bodySecondary,
          textAlign: TextAlign.center,
        ),
      ],
    );
  }
}
