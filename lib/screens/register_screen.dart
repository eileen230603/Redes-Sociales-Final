import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/app_list_tile.dart';
import 'register_form_screen.dart';

class RegisterScreen extends StatelessWidget {
  const RegisterScreen({super.key});

  void _goToRegister(BuildContext context, String tipoUsuario) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (context) => RegisterFormScreen(tipoUsuario: tipoUsuario),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Registro'),
        leading: IconButton(
          icon: AppIcon.md(Icons.arrow_back_ios_new),
          onPressed: () => Navigator.of(context).pop(),
          tooltip: 'Volver',
        ),
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 520),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text('Elige el tipo de cuenta', style: AppTypography.headlineSmall),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Selecciona cómo deseas participar en UNI2 y completa el registro.',
                    style: AppTypography.bodySecondary,
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  AppCard(
                    child: Column(
                      children: [
                        AppListTile(
                          leading: AppIcon.md(Icons.person_pin_circle, color: AppColors.primary),
                          title: 'Integrante externo',
                          subtitle: 'Únete como voluntario y participa en eventos solidarios.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister(context, 'Integrante externo'),
                          showDivider: true,
                        ),
                        AppListTile(
                          leading: AppIcon.md(Icons.volunteer_activism, color: AppColors.accent),
                          title: 'ONG',
                          subtitle: 'Organiza proyectos, recibe apoyo y gestiona tus campañas.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister(context, 'ONG'),
                          showDivider: true,
                        ),
                        AppListTile(
                          leading: AppIcon.md(Icons.business_center, color: AppColors.info),
                          title: 'Empresa',
                          subtitle: 'Patrocina eventos, impulsa acciones RSE y mide tu impacto.',
                          trailing: AppIcon.sm(Icons.chevron_right),
                          onTap: () => _goToRegister(context, 'Empresa'),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  Center(
                    child: AppButton.text(
                      label: '¿Ya tienes cuenta? Inicia sesión',
                      icon: Icons.login,
                      onPressed: () => Navigator.of(context).pop(),
                      minimumSize: const Size(0, AppSizes.buttonHeightSm),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
