import 'package:flutter/material.dart';
import 'register_form_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  static const Map<String, List<Color>> _gradients = {
    'ONG': [Color(0xFF34D399), Color(0xFF10B981)],
    'Empresa': [Color(0xFF60A5FA), Color(0xFF2563EB)],
    'Integrante externo': [Color(0xFFFBBF24), Color(0xFFF97316)],
  };

  Widget _buildTipoUsuarioCard({
    required String tipo,
    required IconData icon,
    required String description,
  }) {
    final colors = _gradients[tipo]!;

    return InkWell(
      borderRadius: BorderRadius.circular(20),
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (context) => RegisterFormScreen(tipoUsuario: tipo),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          gradient: LinearGradient(
            colors: colors,
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(
              color: colors.last.withOpacity(0.25),
              blurRadius: 25,
              offset: const Offset(0, 12),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(icon, color: Colors.white, size: 28),
            ),
            const SizedBox(width: 18),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    tipo,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    description,
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.85),
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios, color: Colors.white, size: 18),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF3883D3), Color(0xFF21BFC4), Color(0xFF36C974)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Stack(
          children: [
            Positioned(
              top: -40,
              right: -60,
              child: Container(
                width: 200,
                height: 200,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(200),
                ),
              ),
            ),
            Positioned(
              bottom: -60,
              left: -30,
              child: Container(
                width: 250,
                height: 250,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(250),
                ),
              ),
            ),
            SafeArea(
              child: Center(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Container(
                    constraints: const BoxConstraints(maxWidth: 520),
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.95),
                      borderRadius: BorderRadius.circular(28),
                      border: Border.all(color: Colors.white.withOpacity(0.2)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.12),
                          blurRadius: 30,
                          offset: const Offset(0, 18),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Row(
                          children: [
                            IconButton(
                              icon: const Icon(Icons.arrow_back_ios_new),
                              color: Colors.grey[600],
                              onPressed: () => Navigator.of(context).pop(),
                            ),
                            const SizedBox(width: 8),
                            const Expanded(
                              child: Text(
                                'Elige el tipo de cuenta',
                                style: TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.black87,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ),
                            const SizedBox(width: 48), // balance back button
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Selecciona cómo deseas participar en UNI2 y completa el registro en el siguiente paso.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                        const SizedBox(height: 28),
                        _buildTipoUsuarioCard(
                          tipo: 'Integrante externo',
                          icon: Icons.person_pin_circle,
                          description:
                              'Únete como voluntario y participa en eventos solidarios.',
                        ),
                        const SizedBox(height: 16),
                        _buildTipoUsuarioCard(
                          tipo: 'ONG',
                          icon: Icons.volunteer_activism,
                          description:
                              'Organiza proyectos, recibe apoyo y gestiona tus campañas.',
                        ),
                        const SizedBox(height: 16),
                        _buildTipoUsuarioCard(
                          tipo: 'Empresa',
                          icon: Icons.business_center,
                          description:
                              'Patrocina eventos, impulsa acciones RSE y mide tu impacto.',
                        ),
                        const SizedBox(height: 28),
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(),
                          child: const Text(
                            '¿Ya tienes cuenta? Inicia sesión',
                            style: TextStyle(
                              color: Color(0xFF3883D3),
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
