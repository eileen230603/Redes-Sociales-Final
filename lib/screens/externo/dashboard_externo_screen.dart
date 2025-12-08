import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/bottom_nav_bar.dart';
import '../../services/storage_service.dart';
import '../../utils/navigation_helper.dart';
import '../evento_detail_screen.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../utils/image_helper.dart';

class DashboardExternoScreen extends StatefulWidget {
  const DashboardExternoScreen({super.key});

  @override
  State<DashboardExternoScreen> createState() => _DashboardExternoScreenState();
}

class _DashboardExternoScreenState extends State<DashboardExternoScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  Map<String, dynamic>? _estadisticas;
  Map<String, dynamic>? _graficas;
  Map<String, dynamic>? _usuario;
  List<dynamic> _eventosInscritos = [];
  List<dynamic> _eventosAsistidos = [];
  List<dynamic> _reacciones = [];
  List<dynamic> _compartidos = [];
  List<dynamic> _eventosDisponibles = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadDatos();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final estadisticasResult =
        await ApiService.getEstadisticasGeneralesExterno();
    final detallesResult = await ApiService.getDatosDetalladosExterno();
    final eventosDisponiblesResult =
        await ApiService.getEventosDisponiblesExterno();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (estadisticasResult['success'] == true) {
        _estadisticas =
            estadisticasResult['estadisticas'] as Map<String, dynamic>?;
        _graficas = estadisticasResult['graficas'] as Map<String, dynamic>?;
        _usuario = estadisticasResult['usuario'] as Map<String, dynamic>?;
      }
      if (detallesResult['success'] == true) {
        _eventosInscritos =
            detallesResult['eventos_inscritos'] as List<dynamic>? ?? [];
        _eventosAsistidos =
            detallesResult['eventos_asistidos'] as List<dynamic>? ?? [];
        _reacciones = detallesResult['reacciones'] as List<dynamic>? ?? [];
        _compartidos = detallesResult['compartidos'] as List<dynamic>? ?? [];
      }
      if (eventosDisponiblesResult['success'] == true) {
        _eventosDisponibles =
            eventosDisponiblesResult['eventos'] as List<dynamic>? ?? [];
      }
      if (estadisticasResult['success'] != true &&
          detallesResult['success'] != true) {
        _error =
            estadisticasResult['error'] as String? ??
            detallesResult['error'] as String? ??
            'Error al cargar datos';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/externo/dashboard'),
      appBar: AppBar(
        title: const Text('Mi Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDatos,
            tooltip: 'Actualizar',
          ),
          IconButton(
            icon: const Icon(Icons.picture_as_pdf),
            onPressed: _descargarPdf,
            tooltip: 'Descargar PDF',
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          tabs: const [
            Tab(icon: Icon(Icons.dashboard), text: 'Resumen'),
            Tab(icon: Icon(Icons.event), text: 'Mis Eventos'),
            Tab(icon: Icon(Icons.favorite), text: 'Reacciones'),
            Tab(icon: Icon(Icons.explore), text: 'Disponibles'),
          ],
        ),
      ),
      body:
          _isLoading
              ? const Center(child: CircularProgressIndicator())
              : _error != null
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                    const SizedBox(height: 16),
                    Text(
                      _error!,
                      style: TextStyle(color: Colors.red[700]),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    FilledButton.tonal(
                      onPressed: _loadDatos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : TabBarView(
                controller: _tabController,
                children: [
                  _buildTabResumen(),
                  _buildTabMisEventos(),
                  _buildTabReacciones(),
                  _buildTabDisponibles(),
                ],
              ),
      bottomNavigationBar: FutureBuilder<Map<String, dynamic>?>(
        future: StorageService.getUserData(),
        builder: (context, snapshot) {
          final userType = snapshot.data?['user_type'] as String?;
          return BottomNavBar(currentIndex: 0, userType: userType);
        },
      ),
    );
  }

  Widget _buildTabResumen() {
    return RefreshIndicator(
      onRefresh: _loadDatos,
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Información del usuario
            if (_usuario != null) _buildUsuarioCard(),

            const SizedBox(height: 24),

            // Estadísticas principales
            if (_estadisticas != null) _buildEstadisticasCards(),

            const SizedBox(height: 24),

            // Gráficas
            if (_graficas != null) _buildGraficasSection(),

            const SizedBox(height: 24),

            // Resumen de impacto
            _buildResumenImpacto(),
          ],
        ),
      ),
    );
  }

  Widget _buildUsuarioCard() {
    final nombre = _usuario!['nombre'] as String? ?? 'Usuario';
    final fotoPerfil = _usuario!['foto_perfil'] as String?;

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              radius: 30,
              backgroundImage:
                  fotoPerfil != null
                      ? CachedNetworkImageProvider(
                        ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                      )
                      : null,
              child:
                  fotoPerfil == null
                      ? Text(
                        nombre.isNotEmpty ? nombre[0].toUpperCase() : 'U',
                        style: const TextStyle(fontSize: 24),
                      )
                      : null,
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Bienvenido,',
                    style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                  ),
                  Text(
                    nombre,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
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

  Widget _buildEstadisticasCards() {
    final stats = _estadisticas!;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Estadísticas Generales',
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 16),
        GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          childAspectRatio: 1.3,
          children: [
            _buildMetricCard(
              'Eventos Inscritos',
              stats['total_eventos_inscritos']?.toString() ?? '0',
              Icons.event,
              Colors.blue,
            ),
            _buildMetricCard(
              'Eventos Asistidos',
              stats['total_eventos_asistidos']?.toString() ?? '0',
              Icons.check_circle,
              Colors.green,
            ),
            _buildMetricCard(
              'Mega Eventos',
              stats['total_mega_eventos_inscritos']?.toString() ?? '0',
              Icons.festival,
              Colors.purple,
            ),
            _buildMetricCard(
              'Reacciones',
              stats['total_reacciones']?.toString() ?? '0',
              Icons.favorite,
              Colors.red,
            ),
            _buildMetricCard(
              'Compartidos',
              stats['total_compartidos']?.toString() ?? '0',
              Icons.share,
              Colors.orange,
            ),
            _buildMetricCard(
              'Horas Acumuladas',
              stats['horas_acumuladas']?.toString() ?? '0',
              Icons.access_time,
              Colors.teal,
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildMetricCard(
    String title,
    String value,
    IconData icon,
    Color color,
  ) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 32, color: color),
            const SizedBox(height: 8),
            Text(
              value,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildGraficasSection() {
    final graficas = _graficas!;
    final estadoParticipaciones =
        graficas['estado_participaciones'] as Map<String, dynamic>?;
    final tipoEventos = graficas['tipo_eventos'] as Map<String, dynamic>?;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Distribución',
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 16),
        if (estadoParticipaciones != null) ...[
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Estado de Participaciones',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 12),
                  _buildStatRow(
                    'Activos',
                    estadoParticipaciones['activos']?.toString() ?? '0',
                    Colors.green,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Finalizados',
                    estadoParticipaciones['finalizados']?.toString() ?? '0',
                    Colors.grey,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Pendientes',
                    estadoParticipaciones['pendientes']?.toString() ?? '0',
                    Colors.orange,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Cancelados',
                    estadoParticipaciones['cancelados']?.toString() ?? '0',
                    Colors.red,
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
        ],
        if (tipoEventos != null && tipoEventos.isNotEmpty) ...[
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Tipos de Eventos',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 12),
                  ...tipoEventos.entries.map((entry) {
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(entry.key),
                          Text(
                            entry.value.toString(),
                            style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    );
                  }).toList(),
                ],
              ),
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildStatRow(String label, String value, Color color) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 14)),
        Text(
          value,
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
      ],
    );
  }

  Widget _buildResumenImpacto() {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Resumen de Impacto',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            if (_estadisticas != null) ...[
              _buildImpactoItem(
                Icons.event_available,
                'Total de Participaciones',
                '${_estadisticas!['total_eventos_inscritos'] ?? 0} eventos',
                Colors.blue,
              ),
              const SizedBox(height: 12),
              _buildImpactoItem(
                Icons.check_circle,
                'Asistencias Confirmadas',
                '${_estadisticas!['total_eventos_asistidos'] ?? 0} eventos',
                Colors.green,
              ),
              const SizedBox(height: 12),
              _buildImpactoItem(
                Icons.favorite,
                'Interacciones Sociales',
                '${(_estadisticas!['total_reacciones'] ?? 0) + (_estadisticas!['total_compartidos'] ?? 0)} interacciones',
                Colors.pink,
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildImpactoItem(
    IconData icon,
    String label,
    String value,
    Color color,
  ) {
    return Row(
      children: [
        Icon(icon, color: color, size: 24),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
              Text(
                value,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: color,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTabMisEventos() {
    return RefreshIndicator(
      onRefresh: _loadDatos,
      child: DefaultTabController(
        length: 2,
        child: Column(
          children: [
            TabBar(
              tabs: const [Tab(text: 'Inscritos'), Tab(text: 'Asistidos')],
            ),
            Expanded(
              child: TabBarView(
                children: [
                  _buildListaEventos(_eventosInscritos, 'Inscritos'),
                  _buildListaEventos(_eventosAsistidos, 'Asistidos'),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildListaEventos(List<dynamic> eventos, String tipo) {
    if (eventos.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              'No hay eventos $tipo.toLowerCase()',
              style: TextStyle(fontSize: 18, color: Colors.grey[600]),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(8),
      itemCount: eventos.length,
      itemBuilder: (context, index) {
        final evento = eventos[index] as Map<String, dynamic>;
        return _buildEventoCard(evento);
      },
    );
  }

  Widget _buildEventoCard(Map<String, dynamic> evento) {
    final eventoId = evento['evento_id'] as int?;
    final titulo = evento['titulo'] as String? ?? 'Sin título';
    final tipoEvento = evento['tipo_evento'] as String?;
    final fechaInicio = evento['fecha_inicio'] as String?;
    final ciudad = evento['ciudad'] as String?;
    final organizador = evento['organizador'] as String?;
    final imagen = evento['imagen'] as String?;
    final puntos = evento['puntos'] as int? ?? 0;

    DateTime? fechaInicioDate;
    if (fechaInicio != null) {
      try {
        fechaInicioDate = DateTime.parse(fechaInicio);
      } catch (e) {
        // Ignorar error
      }
    }

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      child: InkWell(
        onTap:
            eventoId != null
                ? () {
                  Navigator.push(
                    context,
                    NavigationHelper.slideRightRoute(
                      EventoDetailScreen(eventoId: eventoId),
                    ),
                  ).then((_) => _loadDatos());
                }
                : null,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (imagen != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(4),
                ),
                child: CachedNetworkImage(
                  imageUrl: ImageHelper.buildImageUrl(imagen) ?? imagen,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorWidget:
                      (context, url, error) => Container(
                        height: 200,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported, size: 48),
                      ),
                ),
              ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    titulo,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  if (tipoEvento != null) ...[
                    Row(
                      children: [
                        Icon(Icons.category, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          tipoEvento,
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                  ],
                  if (fechaInicioDate != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.calendar_today,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          _formatDate(fechaInicioDate),
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                  ],
                  if (ciudad != null) ...[
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 16,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Text(ciudad, style: TextStyle(color: Colors.grey[600])),
                      ],
                    ),
                    const SizedBox(height: 4),
                  ],
                  if (organizador != null) ...[
                    Row(
                      children: [
                        Icon(Icons.business, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          organizador,
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  if (puntos > 0) ...[
                    Row(
                      children: [
                        Icon(Icons.star, size: 16, color: Colors.amber[700]),
                        const SizedBox(width: 4),
                        Text(
                          '$puntos puntos',
                          style: TextStyle(
                            color: Colors.amber[700],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        'Ver detalles',
                        style: TextStyle(
                          color: Theme.of(context).primaryColor,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Icon(
                        Icons.arrow_forward_ios,
                        size: 16,
                        color: Theme.of(context).primaryColor,
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTabReacciones() {
    return RefreshIndicator(
      onRefresh: _loadDatos,
      child: DefaultTabController(
        length: 2,
        child: Column(
          children: [
            TabBar(
              tabs: const [Tab(text: 'Reacciones'), Tab(text: 'Compartidos')],
            ),
            Expanded(
              child: TabBarView(
                children: [_buildListaReacciones(), _buildListaCompartidos()],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildListaReacciones() {
    if (_reacciones.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.favorite_border, size: 64, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              'No has reaccionado a ningún evento',
              style: TextStyle(fontSize: 18, color: Colors.grey[600]),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(8),
      itemCount: _reacciones.length,
      itemBuilder: (context, index) {
        final reaccion = _reacciones[index] as Map<String, dynamic>;
        return _buildReaccionCard(reaccion);
      },
    );
  }

  Widget _buildListaCompartidos() {
    if (_compartidos.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.share, size: 64, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              'No has compartido ningún evento',
              style: TextStyle(fontSize: 18, color: Colors.grey[600]),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(8),
      itemCount: _compartidos.length,
      itemBuilder: (context, index) {
        final compartido = _compartidos[index] as Map<String, dynamic>;
        return _buildCompartidoCard(compartido);
      },
    );
  }

  Widget _buildReaccionCard(Map<String, dynamic> reaccion) {
    final eventoId = reaccion['evento_id'] as int?;
    final eventoTitulo =
        reaccion['evento_titulo'] as String? ?? 'Evento eliminado';
    final fechaReaccion = reaccion['fecha_reaccion'] as String?;

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 4),
      child: ListTile(
        leading: const Icon(Icons.favorite, color: Colors.red),
        title: Text(
          eventoTitulo,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle:
            fechaReaccion != null ? Text(_formatFecha(fechaReaccion)) : null,
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
        onTap:
            eventoId != null
                ? () {
                  Navigator.push(
                    context,
                    NavigationHelper.slideRightRoute(
                      EventoDetailScreen(eventoId: eventoId),
                    ),
                  );
                }
                : null,
      ),
    );
  }

  Widget _buildCompartidoCard(Map<String, dynamic> compartido) {
    final eventoId = compartido['evento_id'] as int?;
    final eventoTitulo =
        compartido['evento_titulo'] as String? ?? 'Evento eliminado';
    final metodo = compartido['metodo'] as String?;
    final fechaCompartido = compartido['fecha_compartido'] as String?;

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 4),
      child: ListTile(
        leading: Icon(
          metodo == 'whatsapp'
              ? Icons.chat
              : metodo == 'facebook'
              ? Icons.facebook
              : Icons.share,
          color: Colors.blue,
        ),
        title: Text(
          eventoTitulo,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (metodo != null) Text('Compartido por: $metodo'),
            if (fechaCompartido != null) Text(_formatFecha(fechaCompartido)),
          ],
        ),
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
        onTap:
            eventoId != null
                ? () {
                  Navigator.push(
                    context,
                    NavigationHelper.slideRightRoute(
                      EventoDetailScreen(eventoId: eventoId),
                    ),
                  );
                }
                : null,
      ),
    );
  }

  Widget _buildTabDisponibles() {
    return RefreshIndicator(
      onRefresh: _loadDatos,
      child:
          _eventosDisponibles.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.event_available,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay eventos disponibles',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Ya estás participando en todos los eventos disponibles',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              )
              : ListView.builder(
                padding: const EdgeInsets.all(8),
                itemCount: _eventosDisponibles.length,
                itemBuilder: (context, index) {
                  final evento =
                      _eventosDisponibles[index] as Map<String, dynamic>;
                  return _buildEventoCard(evento);
                },
              ),
    );
  }

  Future<void> _descargarPdf() async {
    final result = await ApiService.descargarPdfCompletoExterno();

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('PDF generado correctamente'),
          backgroundColor: Colors.green,
        ),
      );
      // Aquí se podría implementar la descarga del PDF
      // usando packages como path_provider y open_file
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al generar PDF'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  String _formatDate(DateTime date) {
    final months = [
      'Ene',
      'Feb',
      'Mar',
      'Abr',
      'May',
      'Jun',
      'Jul',
      'Ago',
      'Sep',
      'Oct',
      'Nov',
      'Dic',
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }

  String _formatFecha(String fechaStr) {
    try {
      final fecha = DateTime.parse(fechaStr);
      final ahora = DateTime.now();
      final diferencia = ahora.difference(fecha);

      if (diferencia.inDays == 0) {
        if (diferencia.inHours == 0) {
          if (diferencia.inMinutes == 0) {
            return 'Hace unos momentos';
          }
          return 'Hace ${diferencia.inMinutes} min';
        }
        return 'Hace ${diferencia.inHours} h';
      } else if (diferencia.inDays == 1) {
        return 'Ayer';
      } else if (diferencia.inDays < 7) {
        return 'Hace ${diferencia.inDays} días';
      } else {
        return '${fecha.day}/${fecha.month}/${fecha.year}';
      }
    } catch (e) {
      return fechaStr;
    }
  }
}
