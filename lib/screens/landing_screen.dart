import 'package:flutter/material.dart';
import 'login_screen.dart';
import 'register_screen.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  // Colores de marca
  // Azul Marino (primario) - #0C2B44
  static const Color brandProfundo = Color(0xFF0C2B44);
  // Verde Esmeralda (acento) - #00A36C
  static const Color brandCyan = Color(0xFF00A36C);
  // Usamos el mismo Verde Esmeralda como tercer tono para mantener la coherencia
  static const Color brandVerde = Color(0xFF00A36C);
  static const Color yellow300 = Color(0xFFFDE047);

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );

    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeOut),
    );

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.4),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeOut),
    );

    _animationController.forward();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Navbar
            _buildNavbar(),

            // Hero Section
            _buildHeroSection(),

            // Scroll Indicator
            _buildScrollIndicator(),

            // Estadísticas
            _buildEstadisticas(),

            // Sobre el Proyecto
            _buildSobreProyecto(),

            // Cómo Funciona
            _buildComoFunciona(),

            // Servicios
            _buildServicios(),

            // Beneficios
            _buildBeneficios(),

            // Call to Action Final
            _buildCallToActionFinal(),

            // Footer
            _buildFooter(),
          ],
        ),
      ),
    );
  }

  Widget _buildNavbar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.95),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: SafeArea(
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            // Logo
            Row(
              children: [
                // Aquí puedes agregar la imagen del logo si la tienes
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [brandProfundo, brandCyan],
                    ),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(
                    Icons.people,
                    color: Colors.white,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 12),
                ShaderMask(
                  shaderCallback:
                      (bounds) => LinearGradient(
                        colors: [brandProfundo, brandCyan],
                      ).createShader(bounds),
                  child: const Text(
                    'UNI2',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              ],
            ),

            // Botones de navegación (en móvil solo mostramos los botones principales)
            Row(
              children: [
                TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const LoginScreen(),
                      ),
                    );
                  },
                  child: const Text(
                    'Iniciar Sesión',
                    style: TextStyle(
                      color: Colors.grey,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                FilledButton.tonal(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const RegisterScreen(),
                      ),
                    );
                  },
                  style: FilledButton.styleFrom(
                    backgroundColor: brandProfundo,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 20,
                      vertical: 12,
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Registrarse',
                    style: TextStyle(fontWeight: FontWeight.w600),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeroSection() {
    return Container(
      height: MediaQuery.of(context).size.height - 100,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [brandProfundo, brandCyan, brandVerde],
        ),
      ),
      child: Stack(
        children: [
          // Elementos de fondo animados
          Positioned(
            top: MediaQuery.of(context).size.height * 0.25,
            left: MediaQuery.of(context).size.width * 0.25,
            child: Container(
              width: 200,
              height: 200,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.white.withOpacity(0.2),
                    blurRadius: 100,
                    spreadRadius: 50,
                  ),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: MediaQuery.of(context).size.height * 0.25,
            right: MediaQuery.of(context).size.width * 0.25,
            child: Container(
              width: 160,
              height: 160,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.white.withOpacity(0.2),
                    blurRadius: 100,
                    spreadRadius: 50,
                  ),
                ],
              ),
            ),
          ),
          Positioned(
            top: MediaQuery.of(context).size.height * 0.5,
            left: MediaQuery.of(context).size.width * 0.5,
            child: Transform.translate(
              offset: const Offset(-80, -80),
              child: Container(
                width: 128,
                height: 128,
                decoration: BoxDecoration(
                  color: yellow300.withOpacity(0.2),
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: yellow300.withOpacity(0.3),
                      blurRadius: 80,
                      spreadRadius: 40,
                    ),
                  ],
                ),
              ),
            ),
          ),

          // Contenido principal
          FadeTransition(
            opacity: _fadeAnimation,
            child: SlideTransition(
              position: _slideAnimation,
              child: Center(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Badge "Plataforma de Impacto Social"
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: Colors.white.withOpacity(0.3),
                            width: 1,
                          ),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: const [
                            Icon(
                              Icons.rocket_launch,
                              color: Colors.white,
                              size: 16,
                            ),
                            SizedBox(width: 8),
                            Text(
                              'Plataforma de Impacto Social',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 24),

                      // Título principal
                      LayoutBuilder(
                        builder: (context, constraints) {
                          final fontSize =
                              constraints.maxWidth > 600 ? 56.0 : 42.0;
                          return RichText(
                            textAlign: TextAlign.center,
                            text: TextSpan(
                              style: TextStyle(
                                fontSize: fontSize,
                                fontWeight: FontWeight.w900,
                                color: Colors.white,
                                height: 1.2,
                                letterSpacing: -0.5,
                              ),
                              children: [
                                const TextSpan(
                                  text: 'Conectando Comunidades,\n',
                                ),
                                TextSpan(
                                  text: 'Transformando Vidas',
                                  style: TextStyle(
                                    color: yellow300,
                                    shadows: [
                                      Shadow(
                                        color: Colors.black.withOpacity(0.3),
                                        blurRadius: 8,
                                        offset: const Offset(0, 2),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          );
                        },
                      ),

                      const SizedBox(height: 24),

                      // Descripción
                      LayoutBuilder(
                        builder: (context, constraints) {
                          final fontSize =
                              constraints.maxWidth > 600 ? 22.0 : 18.0;
                          return Padding(
                            padding: EdgeInsets.symmetric(
                              horizontal: constraints.maxWidth > 600 ? 32 : 16,
                            ),
                            child: RichText(
                              textAlign: TextAlign.center,
                              text: TextSpan(
                                style: TextStyle(
                                  fontSize: fontSize,
                                  color: Colors.white.withOpacity(0.95),
                                  height: 1.6,
                                  fontWeight: FontWeight.w300,
                                ),
                                children: [
                                  const TextSpan(
                                    text:
                                        'La plataforma que une ONGs, empresas y voluntarios para crear eventos que generan ',
                                  ),
                                  TextSpan(
                                    text: 'impacto real',
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const TextSpan(text: ' y '),
                                  TextSpan(
                                    text: 'medible',
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const TextSpan(
                                    text: ' en nuestras comunidades.',
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),

                      const SizedBox(height: 48),

                      // Botones de acción
                      Column(
                        children: [
                          // Botón "Iniciar Sesión"
                          Container(
                            width: double.infinity,
                            constraints: const BoxConstraints(maxWidth: 400),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              boxShadow: [
                                BoxShadow(
                                  color: brandProfundo.withOpacity(0.3),
                                  blurRadius: 20,
                                  spreadRadius: 2,
                                ),
                              ],
                            ),
                            child: Material(
                              color: Colors.transparent,
                              child: InkWell(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => const LoginScreen(),
                                    ),
                                  );
                                },
                                borderRadius: BorderRadius.circular(16),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 40,
                                    vertical: 20,
                                  ),
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: const [
                                      Icon(
                                        Icons.login,
                                        color: brandProfundo,
                                        size: 20,
                                      ),
                                      SizedBox(width: 8),
                                      Text(
                                        'Iniciar Sesión',
                                        style: TextStyle(
                                          color: brandProfundo,
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ),

                          const SizedBox(height: 16),

                          // Botón "Crear Cuenta Gratis"
                          Container(
                            width: double.infinity,
                            constraints: const BoxConstraints(maxWidth: 400),
                            decoration: BoxDecoration(
                              color: yellow300,
                              borderRadius: BorderRadius.circular(16),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.1),
                                  blurRadius: 20,
                                  spreadRadius: 2,
                                ),
                              ],
                            ),
                            child: Material(
                              color: Colors.transparent,
                              child: InkWell(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder:
                                          (context) => const RegisterScreen(),
                                    ),
                                  );
                                },
                                borderRadius: BorderRadius.circular(16),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 40,
                                    vertical: 20,
                                  ),
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: const [
                                      Icon(
                                        Icons.person_add,
                                        color: Colors.black87,
                                        size: 20,
                                      ),
                                      SizedBox(width: 8),
                                      Text(
                                        'Crear Cuenta Gratis',
                                        style: TextStyle(
                                          color: Colors.black87,
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
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
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildScrollIndicator() {
    return Container(
      padding: const EdgeInsets.only(bottom: 8),
      child: TweenAnimationBuilder<double>(
        tween: Tween(begin: 0.0, end: 1.0),
        duration: const Duration(milliseconds: 1500),
        curve: Curves.easeInOut,
        builder: (context, value, child) {
          return Transform.translate(
            offset: Offset(0, 10 * (1 - value)),
            child: Opacity(
              opacity: value,
              child: const Icon(
                Icons.keyboard_arrow_down,
                color: Colors.white70,
                size: 32,
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildEstadisticas() {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 48),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(bottom: BorderSide(color: Color(0xFFF3F4F6), width: 1)),
      ),
      child: LayoutBuilder(
        builder: (context, constraints) {
          final isTablet = constraints.maxWidth > 600;
          return isTablet
              ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildEstadisticaItem(
                    Icons.event_available,
                    'Eventos Exitosos',
                    'Más de 500 eventos comunitarios gestionados exitosamente',
                    [brandProfundo, brandCyan],
                  ),
                  _buildEstadisticaItem(
                    Icons.people,
                    'Comunidad Activa',
                    'Más de 2,500 voluntarios comprometidos con el cambio',
                    [brandCyan, brandVerde],
                  ),
                  _buildEstadisticaItem(
                    Icons.emoji_events,
                    'Impacto Medible',
                    'Sistema de métricas para evaluar el impacto real',
                    [brandVerde, brandProfundo],
                  ),
                ],
              )
              : Column(
                children: [
                  _buildEstadisticaItem(
                    Icons.event_available,
                    'Eventos Exitosos',
                    'Más de 500 eventos comunitarios gestionados exitosamente',
                    [brandProfundo, brandCyan],
                  ),
                  const SizedBox(height: 32),
                  _buildEstadisticaItem(
                    Icons.people,
                    'Comunidad Activa',
                    'Más de 2,500 voluntarios comprometidos con el cambio',
                    [brandCyan, brandVerde],
                  ),
                  const SizedBox(height: 32),
                  _buildEstadisticaItem(
                    Icons.emoji_events,
                    'Impacto Medible',
                    'Sistema de métricas para evaluar el impacto real',
                    [brandVerde, brandProfundo],
                  ),
                ],
              );
        },
      ),
    );
  }

  Widget _buildEstadisticaItem(
    IconData icon,
    String title,
    String description,
    List<Color> gradientColors,
  ) {
    return Expanded(
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: gradientColors,
              ),
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: gradientColors[0].withOpacity(0.3),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Icon(icon, color: Colors.white, size: 36),
          ),
          const SizedBox(height: 16),
          Text(
            title,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Text(
              description,
              style: const TextStyle(fontSize: 14, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSobreProyecto() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Colors.white, const Color(0xFFF9F9F9)],
        ),
      ),
      child: LayoutBuilder(
        builder: (context, constraints) {
          final isTablet = constraints.maxWidth > 800;
          return isTablet
              ? Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(child: _buildSobreProyectoTexto()),
                  const SizedBox(width: 48),
                  Expanded(child: _buildSobreProyectoTarjeta()),
                ],
              )
              : Column(
                children: [
                  _buildSobreProyectoTexto(),
                  const SizedBox(height: 48),
                  _buildSobreProyectoTarjeta(),
                ],
              );
        },
      ),
    );
  }

  Widget _buildSobreProyectoTexto() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: brandProfundo.withOpacity(0.1),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Text(
            'Sobre UNI2',
            style: TextStyle(
              color: brandProfundo,
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
        const SizedBox(height: 16),
        RichText(
          text: TextSpan(
            style: const TextStyle(
              fontSize: 36,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
              height: 1.2,
            ),
            children: [
              const TextSpan(text: '¿Qué es '),
              TextSpan(
                text: 'UNI2',
                style: TextStyle(
                  foreground:
                      Paint()
                        ..shader = LinearGradient(
                          colors: [brandProfundo, brandCyan],
                        ).createShader(const Rect.fromLTWH(0, 0, 200, 70)),
                ),
              ),
              const TextSpan(text: '?'),
            ],
          ),
        ),
        const SizedBox(height: 24),
        const Text(
          'UNI2 es una plataforma innovadora diseñada para conectar organizaciones sin fines de lucro (ONGs), empresas comprometidas con la responsabilidad social y voluntarios apasionados por generar cambios positivos en sus comunidades.',
          style: TextStyle(fontSize: 16, color: Colors.grey, height: 1.6),
        ),
        const SizedBox(height: 16),
        const Text(
          'Facilitamos la creación, gestión y participación en eventos comunitarios y megaeventos, permitiendo que cada actor del ecosistema social pueda contribuir de manera efectiva y medible al bienestar de nuestras comunidades.',
          style: TextStyle(fontSize: 16, color: Colors.grey, height: 1.6),
        ),
        const SizedBox(height: 32),
        Wrap(
          spacing: 12,
          runSpacing: 12,
          children: [
            _buildFeatureChip(
              Icons.check_circle,
              'Gestión Simplificada',
              brandProfundo,
            ),
            _buildFeatureChip(Icons.trending_up, 'Impacto Medible', brandCyan),
            _buildFeatureChip(Icons.people, 'Comunidad Activa', brandVerde),
            _buildFeatureChip(
              Icons.shield,
              'Seguro y Confiable',
              Colors.orange,
            ),
          ],
        ),
        const SizedBox(height: 32),
        FilledButton(
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => const RegisterScreen()),
            );
          },
          style: FilledButton.styleFrom(
            backgroundColor: brandProfundo,
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          child: const Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Comenzar Ahora',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
              ),
              SizedBox(width: 8),
              Icon(Icons.arrow_forward, size: 20),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildFeatureChip(IconData icon, String text, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 12),
          Text(
            text,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              color: Colors.black87,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSobreProyectoTarjeta() {
    return Container(
      transform: Matrix4.rotationZ(0.1),
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Column(
          children: [
            Row(
              children: [
                Expanded(
                  child: _buildTipoUsuarioCard(
                    Icons.handshake,
                    'ONGs',
                    'Organizaciones',
                    [brandProfundo, brandCyan],
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildTipoUsuarioCard(
                    Icons.business,
                    'Empresas',
                    'RSE',
                    [brandCyan, brandVerde],
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildTipoUsuarioCard(
                    Icons.people,
                    'Voluntarios',
                    'Comunidad',
                    [brandVerde, brandProfundo],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.red.shade400, Colors.pink.shade500],
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                children: [
                  const Icon(Icons.favorite, color: Colors.white, size: 48),
                  const SizedBox(height: 16),
                  const Text(
                    'Impacto Real',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Transformando comunidades juntos',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.white.withOpacity(0.9),
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

  Widget _buildTipoUsuarioCard(
    IconData icon,
    String title,
    String subtitle,
    List<Color> gradientColors,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: gradientColors,
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          Icon(icon, color: Colors.white, size: 32),
          const SizedBox(height: 12),
          Text(
            title,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: TextStyle(
              fontSize: 12,
              color: Colors.white.withOpacity(0.9),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildComoFunciona() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
      color: Colors.white,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: brandCyan.withOpacity(0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              'Proceso Simple',
              style: TextStyle(
                color: brandCyan,
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(height: 12),
          RichText(
            textAlign: TextAlign.center,
            text: TextSpan(
              style: const TextStyle(
                fontSize: 36,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
              children: [
                const TextSpan(text: '¿Cómo '),
                TextSpan(
                  text: 'Funciona',
                  style: TextStyle(
                    foreground:
                        Paint()
                          ..shader = LinearGradient(
                            colors: [brandProfundo, brandCyan],
                          ).createShader(const Rect.fromLTWH(0, 0, 200, 70)),
                  ),
                ),
                const TextSpan(text: '?'),
              ],
            ),
          ),
          const SizedBox(height: 12),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 32),
            child: Text(
              'En solo 4 pasos simples, puedes comenzar a generar impacto en tu comunidad',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 18, color: Colors.grey),
            ),
          ),
          const SizedBox(height: 32),
          LayoutBuilder(
            builder: (context, constraints) {
              final isTablet = constraints.maxWidth > 800;
              return isTablet
                  ? Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildPasoCard(
                        1,
                        'Regístrate',
                        'Crea tu cuenta como ONG, Empresa o Voluntario en menos de 2 minutos',
                        [brandProfundo, brandCyan],
                      ),
                      _buildPasoCard(
                        2,
                        'Explora Eventos',
                        'Descubre eventos cercanos a ti o crea nuevos eventos para tu organización',
                        [brandCyan, brandVerde],
                      ),
                      _buildPasoCard(
                        3,
                        'Participa',
                        'Inscríbete en eventos, gestiona participantes y genera impacto real',
                        [brandVerde, brandProfundo],
                      ),
                      _buildPasoCard(
                        4,
                        'Mide el Impacto',
                        'Accede a reportes detallados y certificados de participación',
                        [yellow300, Colors.orange],
                      ),
                    ],
                  )
                  : Column(
                    children: [
                      _buildPasoCard(
                        1,
                        'Regístrate',
                        'Crea tu cuenta como ONG, Empresa o Voluntario en menos de 2 minutos',
                        [brandProfundo, brandCyan],
                      ),
                      const SizedBox(height: 32),
                      _buildPasoCard(
                        2,
                        'Explora Eventos',
                        'Descubre eventos cercanos a ti o crea nuevos eventos para tu organización',
                        [brandCyan, brandVerde],
                      ),
                      const SizedBox(height: 32),
                      _buildPasoCard(
                        3,
                        'Participa',
                        'Inscríbete en eventos, gestiona participantes y genera impacto real',
                        [brandVerde, brandProfundo],
                      ),
                      const SizedBox(height: 32),
                      _buildPasoCard(
                        4,
                        'Mide el Impacto',
                        'Accede a reportes detallados y certificados de participación',
                        [yellow300, Colors.orange],
                      ),
                    ],
                  );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildPasoCard(
    int numero,
    String titulo,
    String descripcion,
    List<Color> gradientColors,
  ) {
    return Expanded(
      child: Column(
        children: [
          Stack(
            alignment: Alignment.center,
            children: [
              Container(
                width: 96,
                height: 96,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: gradientColors,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: gradientColors[0].withOpacity(0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                transform: Matrix4.rotationZ(0.1),
              ),
              Container(
                width: 96,
                height: 96,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: gradientColors,
                  ),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Center(
                  child: Text(
                    '$numero',
                    style: const TextStyle(
                      fontSize: 36,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          Text(
            titulo,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Text(
              descripcion,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 14,
                color: Colors.grey,
                height: 1.5,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildServicios() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Colors.white, const Color(0xFFF9F9F9)],
        ),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: brandVerde.withOpacity(0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              'Nuestras Herramientas',
              style: TextStyle(
                color: brandVerde,
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(height: 12),
          RichText(
            textAlign: TextAlign.center,
            text: TextSpan(
              style: const TextStyle(
                fontSize: 36,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
              children: [
                const TextSpan(text: 'Servicios '),
                TextSpan(
                  text: 'Completos',
                  style: TextStyle(
                    foreground:
                        Paint()
                          ..shader = LinearGradient(
                            colors: [brandProfundo, brandCyan],
                          ).createShader(const Rect.fromLTWH(0, 0, 200, 70)),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 32),
            child: Text(
              'Herramientas poderosas para gestionar eventos, conectar voluntarios y generar impacto social medible',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 18, color: Colors.grey),
            ),
          ),
          const SizedBox(height: 32),
          Wrap(
            spacing: 24,
            runSpacing: 24,
            alignment: WrapAlignment.center,
            children: [
              _buildServicioCard(
                Icons.calendar_today,
                'Gestión de Eventos',
                'Crea y gestiona eventos comunitarios y megaeventos de forma sencilla. Controla fechas, ubicaciones, capacidad y participantes en tiempo real.',
                [
                  'Calendario integrado',
                  'Gestión de capacidad',
                  'Notificaciones automáticas',
                ],
                [brandProfundo, brandCyan],
              ),
              _buildServicioCard(
                Icons.favorite,
                'Participación Voluntaria',
                'Sistema de inscripción intuitivo que permite a los voluntarios encontrar eventos, inscribirse fácilmente y hacer seguimiento de su participación.',
                [
                  'Búsqueda avanzada',
                  'Inscripción en un clic',
                  'Historial completo',
                ],
                [brandCyan, brandVerde],
              ),
              _buildServicioCard(
                Icons.favorite_border,
                'Reacciones a Eventos',
                'Los usuarios pueden marcar eventos como favoritos, permitiendo que las organizaciones conozcan qué eventos generan más interés en la comunidad.',
                [
                  'Sistema de favoritos',
                  'Métricas de interés',
                  'Recomendaciones',
                ],
                [brandVerde, brandProfundo],
              ),
              _buildServicioCard(
                Icons.trending_up,
                'Panel de ONG',
                'Dashboard completo para ONGs con estadísticas de eventos, gestión de voluntarios, reportes detallados y herramientas de análisis de impacto.',
                [
                  'Dashboard interactivo',
                  'Reportes en tiempo real',
                  'Análisis de impacto',
                ],
                [brandProfundo, brandVerde],
              ),
              _buildServicioCard(
                Icons.verified,
                'Certificados para Voluntarios',
                'Sistema de certificación que reconoce y valida la participación de voluntarios en eventos, generando documentos oficiales de su contribución.',
                [
                  'Certificados digitales',
                  'Validación automática',
                  'Descarga en PDF',
                ],
                [brandCyan, brandProfundo],
              ),
              _buildServicioCard(
                Icons.pie_chart,
                'Dashboard de Participación',
                'Visualiza estadísticas completas de participación, reacciones y tendencias. Herramientas de análisis para optimizar tus eventos y maximizar el impacto.',
                [
                  'Gráficos interactivos',
                  'Tendencias y patrones',
                  'Exportación de datos',
                ],
                [brandVerde, brandCyan],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildServicioCard(
    IconData icon,
    String titulo,
    String descripcion,
    List<String> features,
    List<Color> gradientColors,
  ) {
    return SizedBox(
      width: 350,
      child: Container(
        padding: const EdgeInsets.all(32),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: gradientColors,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: gradientColors[0].withOpacity(0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Icon(icon, color: Colors.white, size: 36),
            ),
            const SizedBox(height: 24),
            Text(
              titulo,
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 16),
            Text(
              descripcion,
              style: const TextStyle(
                fontSize: 14,
                color: Colors.grey,
                height: 1.6,
              ),
            ),
            const SizedBox(height: 16),
            ...features.map(
              (feature) => Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Row(
                  children: [
                    Icon(Icons.check, color: gradientColors[0], size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        feature,
                        style: const TextStyle(
                          fontSize: 14,
                          color: Colors.grey,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBeneficios() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
      color: Colors.white,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: yellow300.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              'Ventajas',
              style: TextStyle(
                color: Colors.orange.shade700,
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(height: 12),
          RichText(
            textAlign: TextAlign.center,
            text: TextSpan(
              style: const TextStyle(
                fontSize: 36,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
              children: [
                const TextSpan(text: '¿Por qué elegir '),
                TextSpan(
                  text: 'UNI2',
                  style: TextStyle(
                    foreground:
                        Paint()
                          ..shader = LinearGradient(
                            colors: [brandProfundo, brandCyan],
                          ).createShader(const Rect.fromLTWH(0, 0, 200, 70)),
                  ),
                ),
                const TextSpan(text: '?'),
              ],
            ),
          ),
          const SizedBox(height: 32),
          Wrap(
            spacing: 24,
            runSpacing: 24,
            alignment: WrapAlignment.center,
            children: [
              _buildBeneficioCard(
                Icons.flash_on,
                'Rápido y Eficiente',
                'Gestiona eventos en minutos, no en horas. Interfaz intuitiva diseñada para ahorrar tiempo.',
                [brandProfundo, brandCyan],
              ),
              _buildBeneficioCard(
                Icons.phone_android,
                'Totalmente Responsivo',
                'Accede desde cualquier dispositivo. Funciona perfectamente en móviles, tablets y computadoras.',
                [brandCyan, brandVerde],
              ),
              _buildBeneficioCard(
                Icons.shield,
                'Seguro y Confiable',
                'Tus datos están protegidos con los más altos estándares de seguridad y privacidad.',
                [brandVerde, brandProfundo],
              ),
              _buildBeneficioCard(
                Icons.headset_mic,
                'Soporte Dedicado',
                'Equipo de soporte disponible para ayudarte en cada paso del proceso.',
                [Colors.orange, Colors.deepOrange],
              ),
              _buildBeneficioCard(
                Icons.bar_chart,
                'Analytics Avanzados',
                'Métricas detalladas y reportes personalizados para medir el impacto real de tus eventos.',
                [Colors.purple, Colors.pink],
              ),
              _buildBeneficioCard(
                Icons.card_giftcard,
                'Gratis para Voluntarios',
                'Los voluntarios pueden usar todas las funcionalidades sin costo alguno.',
                [Colors.red, Colors.pink],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildBeneficioCard(
    IconData icon,
    String titulo,
    String descripcion,
    List<Color> gradientColors,
  ) {
    return SizedBox(
      width: 300,
      child: Container(
        padding: const EdgeInsets.all(32),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              gradientColors[0].withOpacity(0.05),
              gradientColors[1].withOpacity(0.05),
            ],
          ),
          borderRadius: BorderRadius.circular(16),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: gradientColors,
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(icon, color: Colors.white, size: 28),
            ),
            const SizedBox(height: 24),
            Text(
              titulo,
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              descripcion,
              style: const TextStyle(
                fontSize: 14,
                color: Colors.grey,
                height: 1.5,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCallToActionFinal() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 64, horizontal: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [brandProfundo, brandCyan, brandVerde],
        ),
      ),
      child: Stack(
        children: [
          // Elementos de fondo
          Positioned(
            top: 50,
            left: 50,
            child: Container(
              width: 200,
              height: 200,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.white.withOpacity(0.2),
                    blurRadius: 100,
                    spreadRadius: 50,
                  ),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: 50,
            right: 50,
            child: Container(
              width: 160,
              height: 160,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.white.withOpacity(0.2),
                    blurRadius: 100,
                    spreadRadius: 50,
                  ),
                ],
              ),
            ),
          ),
          // Contenido
          Center(
            child: Column(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 8,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: Colors.white.withOpacity(0.3),
                      width: 1,
                    ),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.star, color: Colors.white, size: 16),
                      SizedBox(width: 8),
                      Text(
                        'Únete a la Comunidad',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                RichText(
                  textAlign: TextAlign.center,
                  text: TextSpan(
                    style: const TextStyle(
                      fontSize: 42,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                      height: 1.2,
                    ),
                    children: [
                      const TextSpan(text: 'Únete y empieza a\n'),
                      TextSpan(
                        text: 'transformar comunidades',
                        style: TextStyle(
                          color: yellow300,
                          shadows: [
                            Shadow(
                              color: Colors.black26,
                              blurRadius: 8,
                              offset: Offset(0, 2),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 32),
                  child: Text(
                    'Sé parte del cambio. Ya seas una ONG, una empresa o un voluntario, tu participación marca la diferencia en nuestras comunidades.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.white,
                      height: 1.6,
                    ),
                  ),
                ),
                const SizedBox(height: 32),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    FilledButton.tonal(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const RegisterScreen(),
                          ),
                        );
                      },
                      style: FilledButton.styleFrom(
                        backgroundColor: Colors.white,
                        foregroundColor: brandProfundo,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 40,
                          vertical: 20,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.person_add, size: 20),
                          SizedBox(width: 8),
                          Text(
                            'Crear Cuenta Gratis',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    FilledButton(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const LoginScreen(),
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: yellow300,
                        foregroundColor: Colors.black87,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 40,
                          vertical: 20,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.login, size: 20),
                          SizedBox(width: 8),
                          Text(
                            'Iniciar Sesión',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 64, horizontal: 24),
      color: const Color(0xFF1F2937),
      child: Column(
        children: [
          LayoutBuilder(
            builder: (context, constraints) {
              final isTablet = constraints.maxWidth > 800;
              return isTablet
                  ? Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildFooterColumn(
                        'UNI2',
                        'Conectando comunidades, transformando vidas. La plataforma que une esfuerzos para generar impacto social real y medible.',
                        true,
                      ),
                      _buildFooterColumn(
                        'Plataforma',
                        null,
                        false,
                        links: [
                          'Sobre el Proyecto',
                          'Cómo Funciona',
                          'Servicios',
                          'Beneficios',
                        ],
                      ),
                      _buildFooterColumn(
                        'Recursos',
                        null,
                        false,
                        links: [
                          'Iniciar Sesión',
                          'Registro ONG',
                          'Registro Empresa',
                          'Registro Voluntario',
                        ],
                      ),
                      _buildFooterColumn(
                        'Contacto',
                        null,
                        false,
                        contact: true,
                      ),
                    ],
                  )
                  : Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildFooterColumn(
                        'UNI2',
                        'Conectando comunidades, transformando vidas. La plataforma que une esfuerzos para generar impacto social real y medible.',
                        true,
                      ),
                      const SizedBox(height: 32),
                      _buildFooterColumn(
                        'Plataforma',
                        null,
                        false,
                        links: [
                          'Sobre el Proyecto',
                          'Cómo Funciona',
                          'Servicios',
                          'Beneficios',
                        ],
                      ),
                      const SizedBox(height: 32),
                      _buildFooterColumn(
                        'Recursos',
                        null,
                        false,
                        links: [
                          'Iniciar Sesión',
                          'Registro ONG',
                          'Registro Empresa',
                          'Registro Voluntario',
                        ],
                      ),
                      const SizedBox(height: 32),
                      _buildFooterColumn(
                        'Contacto',
                        null,
                        false,
                        contact: true,
                      ),
                    ],
                  );
            },
          ),
          const SizedBox(height: 32),
          const Divider(color: Color(0xFF374151)),
          const SizedBox(height: 32),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                '© 2024 UNI2. Todos los derechos reservados.',
                style: TextStyle(color: Colors.grey, fontSize: 12),
              ),
              Wrap(
                spacing: 24,
                children: [
                  TextButton(
                    onPressed: () {},
                    child: const Text(
                      'Política de Privacidad',
                      style: TextStyle(color: Colors.grey, fontSize: 12),
                    ),
                  ),
                  TextButton(
                    onPressed: () {},
                    child: const Text(
                      'Términos de Servicio',
                      style: TextStyle(color: Colors.grey, fontSize: 12),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFooterColumn(
    String title,
    String? description,
    bool isMain, {
    List<String>? links,
    bool contact = false,
  }) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (isMain) ...[
            Row(
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [brandProfundo, brandCyan],
                    ),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(
                    Icons.people,
                    color: Colors.white,
                    size: 24,
                  ),
                ),
                const SizedBox(width: 12),
                ShaderMask(
                  shaderCallback:
                      (bounds) => LinearGradient(
                        colors: [brandProfundo, brandCyan],
                      ).createShader(bounds),
                  child: const Text(
                    'UNI2',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            if (description != null)
              Text(
                description,
                style: const TextStyle(
                  color: Colors.grey,
                  fontSize: 12,
                  height: 1.5,
                ),
              ),
            const SizedBox(height: 16),
            Row(
              children: [
                _buildSocialIcon(Icons.facebook, brandProfundo),
                const SizedBox(width: 12),
                _buildSocialIcon(Icons.chat, brandCyan),
                const SizedBox(width: 12),
                _buildSocialIcon(Icons.camera_alt, brandVerde),
                const SizedBox(width: 12),
                _buildSocialIcon(Icons.business, brandProfundo),
              ],
            ),
          ] else ...[
            Text(
              title,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 16),
            if (links != null)
              ...links.map(
                (link) => Padding(
                  padding: const EdgeInsets.only(bottom: 12),
                  child: TextButton(
                    onPressed: () {},
                    style: TextButton.styleFrom(
                      padding: EdgeInsets.zero,
                      alignment: Alignment.centerLeft,
                    ),
                    child: Text(
                      link,
                      style: const TextStyle(color: Colors.grey, fontSize: 14),
                    ),
                  ),
                ),
              ),
            if (contact) ...[
              _buildContactItem(Icons.email, 'contacto@uni2.com'),
              const SizedBox(height: 12),
              _buildContactItem(Icons.phone, '+57 (1) 234 5678'),
              const SizedBox(height: 12),
              _buildContactItem(Icons.location_on, 'Bogotá, Colombia'),
            ],
          ],
        ],
      ),
    );
  }

  Widget _buildSocialIcon(IconData icon, Color color) {
    return Container(
      width: 40,
      height: 40,
      decoration: BoxDecoration(
        color: const Color(0xFF374151),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Icon(icon, color: color, size: 20),
    );
  }

  Widget _buildContactItem(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, color: brandCyan, size: 16),
        const SizedBox(width: 12),
        Text(text, style: const TextStyle(color: Colors.grey, fontSize: 14)),
      ],
    );
  }
}
