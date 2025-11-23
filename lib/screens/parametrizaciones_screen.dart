import 'package:flutter/material.dart';
import '../widgets/app_drawer.dart';
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
        elevation: 0,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // Header
          Card(
            color: Theme.of(context).primaryColor,
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(
                        Icons.settings_applications,
                        color: Colors.white,
                        size: 32,
                      ),
                      const SizedBox(width: 12),
                      const Expanded(
                        child: Text(
                          'Gestión de Parametrizaciones',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Administra los catálogos y datos maestros del sistema',
                    style: TextStyle(color: Colors.white70, fontSize: 14),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),

          // Tipos de Evento
          _buildParametrizacionCard(
            context,
            icon: Icons.event,
            title: 'Tipos de Evento',
            description:
                'Gestiona los tipos de eventos disponibles (conferencia, taller, seminario, etc.)',
            color: Colors.blue,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const TiposEventoScreen(),
                ),
              );
            },
          ),

          // Ciudades
          _buildParametrizacionCard(
            context,
            icon: Icons.location_city,
            title: 'Ciudades',
            description: 'Administra las ciudades disponibles para los eventos',
            color: Colors.green,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const CiudadesScreen()),
              );
            },
          ),

          // Lugares
          _buildParametrizacionCard(
            context,
            icon: Icons.place,
            title: 'Lugares',
            description:
                'Gestiona los lugares específicos donde se realizan los eventos',
            color: Colors.orange,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const LugaresScreen()),
              );
            },
          ),

          // Estados de Participación
          _buildParametrizacionCard(
            context,
            icon: Icons.how_to_reg,
            title: 'Estados de Participación',
            description:
                'Configura los estados posibles de las participaciones (pendiente, aprobado, rechazado, etc.)',
            color: Colors.purple,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const EstadosParticipacionScreen(),
                ),
              );
            },
          ),

          // Tipos de Notificación
          _buildParametrizacionCard(
            context,
            icon: Icons.notifications,
            title: 'Tipos de Notificación',
            description: 'Gestiona los tipos de notificaciones del sistema',
            color: Colors.red,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const TiposNotificacionScreen(),
                ),
              );
            },
          ),

          // Estados de Evento
          _buildParametrizacionCard(
            context,
            icon: Icons.event_available,
            title: 'Estados de Evento',
            description:
                'Configura los estados de los eventos (borrador, publicado, finalizado, etc.)',
            color: Colors.teal,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const EstadosEventoScreen(),
                ),
              );
            },
          ),

          // Tipos de Usuario
          _buildParametrizacionCard(
            context,
            icon: Icons.people,
            title: 'Tipos de Usuario',
            description: 'Administra los tipos de usuarios del sistema',
            color: Colors.indigo,
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
    );
  }

  Widget _buildParametrizacionCard(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String description,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: color, size: 32),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                    ),
                  ],
                ),
              ),
              Icon(Icons.arrow_forward_ios, color: Colors.grey[400]),
            ],
          ),
        ),
      ),
    );
  }
}
