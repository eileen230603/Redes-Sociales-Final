import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../utils/image_helper.dart';
import 'package:cached_network_image/cached_network_image.dart';

class DashboardOngScreen extends StatefulWidget {
  const DashboardOngScreen({super.key});

  @override
  State<DashboardOngScreen> createState() => _DashboardOngScreenState();
}

class _DashboardOngScreenState extends State<DashboardOngScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  Map<String, dynamic>? _estadisticasGenerales;
  List<dynamic> _estadisticasParticipantes = [];
  Map<String, dynamic>? _totalesParticipantes;
  List<dynamic> _listaParticipantes = [];
  List<dynamic> _estadisticasReacciones = [];
  int _totalReacciones = 0;
  List<dynamic> _listaReacciones = [];
  bool _isLoading = true;
  String? _error;
  int? _eventoFiltroParticipantes;
  int? _eventoFiltroReacciones;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
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

    final generalesResult = await ApiService.getEstadisticasGeneralesOng();
    final participantesResult = await ApiService.getEstadisticasParticipantes();
    final reaccionesResult = await ApiService.getEstadisticasReacciones();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (generalesResult['success'] == true) {
        _estadisticasGenerales = generalesResult.cast<String, dynamic>();
      }
      if (participantesResult['success'] == true) {
        _estadisticasParticipantes =
            participantesResult['estadisticas_por_evento'] as List? ?? [];
        final totales = participantesResult['totales'];
        _totalesParticipantes =
            totales != null ? Map<String, dynamic>.from(totales as Map) : null;
      }
      if (reaccionesResult['success'] == true) {
        _estadisticasReacciones =
            reaccionesResult['estadisticas_por_evento'] as List? ?? [];
        _totalReacciones = reaccionesResult['total_reacciones'] as int? ?? 0;
      }
      if (generalesResult['success'] != true &&
          participantesResult['success'] != true &&
          reaccionesResult['success'] != true) {
        _error =
            generalesResult['error'] as String? ??
            participantesResult['error'] as String? ??
            reaccionesResult['error'] as String? ??
            'Error al cargar datos';
      }
    });

    // Cargar listas
    await _loadListaParticipantes();
    await _loadListaReacciones();
  }

  Future<void> _loadListaParticipantes() async {
    final result = await ApiService.getListaParticipantes(
      eventoId: _eventoFiltroParticipantes,
    );
    if (!mounted) return;
    if (result['success'] == true) {
      setState(() {
        _listaParticipantes = result['participantes'] as List? ?? [];
      });
    }
  }

  Future<void> _loadListaReacciones() async {
    final result = await ApiService.getListaReacciones(
      eventoId: _eventoFiltroReacciones,
    );
    if (!mounted) return;
    if (result['success'] == true) {
      setState(() {
        _listaReacciones = result['reacciones'] as List? ?? [];
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/dashboard'),
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDatos,
            tooltip: 'Actualizar',
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          tabs: const [
            Tab(icon: Icon(Icons.people), text: 'Participantes'),
            Tab(icon: Icon(Icons.favorite), text: 'Reacciones'),
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
                    Icon(
                      Icons.error_outline,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      _error!,
                      style: TextStyle(color: Colors.grey[600]),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadDatos,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : TabBarView(
                controller: _tabController,
                children: [_buildTabParticipantes(), _buildTabReacciones()],
              ),
    );
  }

  Widget _buildTabParticipantes() {
    return RefreshIndicator(
      onRefresh: () async {
        await _loadDatos();
      },
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Cards de estadísticas generales
            if (_estadisticasGenerales != null)
              _buildCardsEstadisticasGenerales(),

            const SizedBox(height: 24),

            // Estadísticas de participantes
            _buildSeccionEstadisticasParticipantes(),

            const SizedBox(height: 24),

            // Lista de participantes
            _buildListaParticipantes(),
          ],
        ),
      ),
    );
  }

  Widget _buildTabReacciones() {
    return RefreshIndicator(
      onRefresh: () async {
        await _loadDatos();
      },
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Cards de estadísticas generales
            if (_estadisticasGenerales != null)
              _buildCardsEstadisticasGenerales(),

            const SizedBox(height: 24),

            // Estadísticas de reacciones
            _buildSeccionEstadisticasReacciones(),

            const SizedBox(height: 24),

            // Lista de reacciones
            _buildListaReacciones(),
          ],
        ),
      ),
    );
  }

  Widget _buildCardsEstadisticasGenerales() {
    final estadisticas = _estadisticasGenerales!['estadisticas'] as Map?;
    if (estadisticas == null) return const SizedBox.shrink();

    final eventos = estadisticas['eventos'] as Map?;
    final voluntarios = estadisticas['voluntarios'] as Map?;
    final reacciones = estadisticas['reacciones'] as Map?;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Resumen General',
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: _buildMetricCard(
                'Eventos Totales',
                eventos?['total']?.toString() ?? '0',
                Icons.event,
                Colors.blue,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildMetricCard(
                'Activos',
                eventos?['activos']?.toString() ?? '0',
                Icons.play_circle,
                Colors.green,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _buildMetricCard(
                'Voluntarios',
                voluntarios?['total_unicos']?.toString() ?? '0',
                Icons.people,
                Colors.orange,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildMetricCard(
                'Reacciones',
                reacciones?['total']?.toString() ?? '0',
                Icons.favorite,
                Colors.red,
              ),
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
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Icon(icon, color: color, size: 24),
                Text(
                  value,
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              title,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSeccionEstadisticasParticipantes() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Estadísticas de Participantes',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 16),
        if (_totalesParticipantes != null)
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  _buildStatRow(
                    'Total',
                    _totalesParticipantes!['total']?.toString() ?? '0',
                    Colors.blue,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Aprobados',
                    _totalesParticipantes!['aprobados']?.toString() ?? '0',
                    Colors.green,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Pendientes',
                    _totalesParticipantes!['pendientes']?.toString() ?? '0',
                    Colors.orange,
                  ),
                  const Divider(),
                  _buildStatRow(
                    'Rechazados',
                    _totalesParticipantes!['rechazados']?.toString() ?? '0',
                    Colors.red,
                  ),
                ],
              ),
            ),
          ),
        const SizedBox(height: 16),
        if (_estadisticasParticipantes.isNotEmpty) ...[
          const Text(
            'Por Evento',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 12),
          ..._estadisticasParticipantes.map(
            (est) => _buildEventoParticipantesCard(est),
          ),
        ],
      ],
    );
  }

  Widget _buildSeccionEstadisticasReacciones() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Estadísticas de Reacciones',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 16),
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                Column(
                  children: [
                    Text(
                      _totalReacciones.toString(),
                      style: const TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.bold,
                        color: Colors.red,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Total Reacciones',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                  ],
                ),
                Container(width: 1, height: 50, color: Colors.grey[300]),
                Column(
                  children: [
                    Text(
                      _estadisticasReacciones.length.toString(),
                      style: const TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.bold,
                        color: Colors.pink,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Eventos con Reacciones',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 16),
        if (_estadisticasReacciones.isNotEmpty) ...[
          const Text(
            'Por Evento',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 12),
          ..._estadisticasReacciones.map(
            (est) => _buildEventoReaccionesCard(est),
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

  Widget _buildEventoParticipantesCard(Map est) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              est['evento_titulo']?.toString() ?? 'Sin título',
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: _buildMiniStat(
                    'Total',
                    est['total']?.toString() ?? '0',
                    Colors.blue,
                  ),
                ),
                Expanded(
                  child: _buildMiniStat(
                    'Aprobados',
                    est['aprobados']?.toString() ?? '0',
                    Colors.green,
                  ),
                ),
                Expanded(
                  child: _buildMiniStat(
                    'Pendientes',
                    est['pendientes']?.toString() ?? '0',
                    Colors.orange,
                  ),
                ),
                Expanded(
                  child: _buildMiniStat(
                    'Rechazados',
                    est['rechazados']?.toString() ?? '0',
                    Colors.red,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEventoReaccionesCard(Map est) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Text(
                est['evento_titulo']?.toString() ?? 'Sin título',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            Row(
              children: [
                const Icon(Icons.favorite, color: Colors.red, size: 20),
                const SizedBox(width: 8),
                Text(
                  est['total_reacciones']?.toString() ?? '0',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMiniStat(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 4),
        Text(label, style: TextStyle(fontSize: 11, color: Colors.grey[600])),
      ],
    );
  }

  Widget _buildListaParticipantes() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Lista de Participantes',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            if (_listaParticipantes.isNotEmpty)
              Text(
                '${_listaParticipantes.length} participantes',
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
          ],
        ),
        const SizedBox(height: 16),
        if (_listaParticipantes.isEmpty)
          Card(
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Center(
                child: Column(
                  children: [
                    Icon(
                      Icons.people_outline,
                      size: 48,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay participantes',
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                  ],
                ),
              ),
            ),
          )
        else
          ..._listaParticipantes.map(
            (participante) => _buildParticipanteCard(participante),
          ),
      ],
    );
  }

  Widget _buildListaReacciones() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Lista de Reacciones',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            if (_listaReacciones.isNotEmpty)
              Text(
                '${_listaReacciones.length} reacciones',
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
          ],
        ),
        const SizedBox(height: 16),
        if (_listaReacciones.isEmpty)
          Card(
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Center(
                child: Column(
                  children: [
                    Icon(
                      Icons.favorite_border,
                      size: 48,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay reacciones',
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                  ],
                ),
              ),
            ),
          )
        else
          ..._listaReacciones.map((reaccion) => _buildReaccionCard(reaccion)),
      ],
    );
  }

  Widget _buildParticipanteCard(Map participante) {
    final fotoPerfil = participante['foto_perfil'] as String?;
    final nombre = participante['nombre']?.toString() ?? 'Sin nombre';
    final correo = participante['correo']?.toString() ?? '';
    final estado = participante['estado']?.toString() ?? 'pendiente';
    final eventoTitulo = participante['evento_titulo']?.toString() ?? '';

    Color estadoColor;
    IconData estadoIcon;
    switch (estado.toLowerCase()) {
      case 'aprobada':
        estadoColor = Colors.green;
        estadoIcon = Icons.check_circle;
        break;
      case 'rechazada':
        estadoColor = Colors.red;
        estadoIcon = Icons.cancel;
        break;
      default:
        estadoColor = Colors.orange;
        estadoIcon = Icons.pending;
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        leading: CircleAvatar(
          backgroundImage:
              fotoPerfil != null
                  ? CachedNetworkImageProvider(
                    ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                  )
                  : null,
          child:
              fotoPerfil == null
                  ? Text(
                    nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
                    style: const TextStyle(color: Colors.white),
                  )
                  : null,
          backgroundColor: Colors.blue,
        ),
        title: Text(
          nombre,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (correo.isNotEmpty) Text(correo),
            if (eventoTitulo.isNotEmpty)
              Text(
                eventoTitulo,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey[600],
                  fontStyle: FontStyle.italic,
                ),
              ),
          ],
        ),
        trailing: Chip(
          avatar: Icon(estadoIcon, size: 16, color: estadoColor),
          label: Text(
            estado.toUpperCase(),
            style: TextStyle(
              fontSize: 11,
              color: estadoColor,
              fontWeight: FontWeight.bold,
            ),
          ),
          backgroundColor: estadoColor.withOpacity(0.1),
        ),
      ),
    );
  }

  Widget _buildReaccionCard(Map reaccion) {
    final fotoPerfil = reaccion['foto_perfil'] as String?;
    final nombre = reaccion['nombre']?.toString() ?? 'Sin nombre';
    final correo = reaccion['correo']?.toString() ?? '';
    final eventoTitulo = reaccion['evento_titulo']?.toString() ?? '';
    final fechaReaccion = reaccion['fecha_reaccion']?.toString();

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        leading: CircleAvatar(
          backgroundImage:
              fotoPerfil != null
                  ? CachedNetworkImageProvider(
                    ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                  )
                  : null,
          child:
              fotoPerfil == null
                  ? Text(
                    nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
                    style: const TextStyle(color: Colors.white),
                  )
                  : null,
          backgroundColor: Colors.red,
        ),
        title: Text(
          nombre,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (correo.isNotEmpty) Text(correo),
            if (eventoTitulo.isNotEmpty)
              Text(
                eventoTitulo,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey[600],
                  fontStyle: FontStyle.italic,
                ),
              ),
            if (fechaReaccion != null)
              Text(
                _formatFecha(fechaReaccion),
                style: TextStyle(fontSize: 11, color: Colors.grey[500]),
              ),
          ],
        ),
        trailing: const Icon(Icons.favorite, color: Colors.red),
      ),
    );
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
