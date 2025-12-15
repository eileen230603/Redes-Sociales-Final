import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../widgets/app_drawer.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
import '../widgets/molecules/app_list_tile.dart';
import 'parametrizaciones/tipos_evento_screen.dart';
import 'parametrizaciones/ciudades_screen.dart';
import 'parametrizaciones/lugares_screen.dart';
import 'parametrizaciones/estados_participacion_screen.dart';
import 'parametrizaciones/tipos_notificacion_screen.dart';
import 'parametrizaciones/estados_evento_screen.dart';
import 'parametrizaciones/tipos_usuario_screen.dart';

class ParametrizacionesScreen extends StatelessWidget {
  const ParametrizacionesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/parametrizaciones'),
      appBar: AppBar(
        title: const Text('Parametrizaciones del Sistema'),
      ),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 900),
          child: ListView(
            padding: const EdgeInsets.all(AppSpacing.md),
            children: [
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
                          Icons.settings_applications,
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
                            'Gestión de Parametrizaciones',
                            style: AppTypography.headlineSmall.copyWith(
                              color: AppColors.textOnPrimary,
                            ),
                          ),
                          const SizedBox(height: AppSpacing.xxs),
                          Text(
                            'Administra los catálogos y datos maestros del sistema.',
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
              AppCard(
                child: Column(
                  children: [
                    _ParamItem(
                      icon: Icons.event,
                      title: 'Tipos de Evento',
                      subtitle:
                          'Gestiona los tipos de eventos disponibles (conferencia, taller, seminario, etc.).',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TiposEventoScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.location_city,
                      title: 'Ciudades',
                      subtitle: 'Administra las ciudades disponibles para los eventos.',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const CiudadesScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.place,
                      title: 'Lugares',
                      subtitle:
                          'Gestiona los lugares específicos donde se realizan los eventos.',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const LugaresScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.how_to_reg,
                      title: 'Estados de Participación',
                      subtitle:
                          'Configura los estados posibles de las participaciones (pendiente, aprobado, rechazado, etc.).',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const EstadosParticipacionScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.notifications,
                      title: 'Tipos de Notificación',
                      subtitle: 'Gestiona los tipos de notificaciones del sistema.',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TiposNotificacionScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.event_available,
                      title: 'Estados de Evento',
                      subtitle:
                          'Configura los estados de los eventos (borrador, publicado, finalizado, etc.).',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const EstadosEventoScreen(),
                          ),
                        );
                      },
                      showDivider: true,
                    ),
                    _ParamItem(
                      icon: Icons.people,
                      title: 'Tipos de Usuario',
                      subtitle: 'Administra los tipos de usuarios del sistema.',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TiposUsuarioScreen(),
                          ),
                        );
                      },
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ParamItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;
  final bool showDivider;

  const _ParamItem({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
    this.showDivider = false,
  });

  @override
  Widget build(BuildContext context) {
    return AppListTile(
      leading: Container(
        width: AppSizes.avatarMd,
        height: AppSizes.avatarMd,
        decoration: BoxDecoration(
          color: AppColors.grey100,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.borderLight),
        ),
        child: Center(
          child: AppIcon.md(icon, color: AppColors.primary),
        ),
      ),
      title: title,
      subtitle: subtitle,
      trailing: AppIcon.sm(Icons.chevron_right),
      onTap: onTap,
      showDivider: showDivider,
    );
  }
}
