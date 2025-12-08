import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../widgets/app_drawer.dart';

class VoluntariosOngScreen extends StatefulWidget {
  const VoluntariosOngScreen({super.key});

  @override
  State<VoluntariosOngScreen> createState() => _VoluntariosOngScreenState();
}

class _VoluntariosOngScreenState extends State<VoluntariosOngScreen> {
  List<dynamic> _voluntarios = [];
  bool _isLoading = true;
  String? _error;
  int? _ongId;

  @override
  void initState() {
    super.initState();
    _loadVoluntarios();
  }

  Future<void> _loadVoluntarios() async {
    final ongId = _ongId ?? await AuthHelper.getOngIdWithRetry();

    if (!mounted) return;

    if (ongId == null) {
      setState(() {
        _isLoading = false;
        _error =
            'No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.';
      });
      return;
    }

    setState(() {
      _ongId = ongId;
    });

    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getVoluntariosOng(ongId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _voluntarios = result['voluntarios'] as List;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar voluntarios';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/voluntarios'),
      appBar: AppBar(
        title: const Text('Voluntarios'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadVoluntarios,
            tooltip: 'Actualizar',
          ),
        ],
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
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.red[700]),
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadVoluntarios,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _voluntarios.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.people_outline,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay voluntarios registrados',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Los voluntarios aparecerán aquí cuando se inscriban a tus eventos',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadVoluntarios,
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _voluntarios.length,
                  itemBuilder: (context, index) {
                    final voluntario =
                        _voluntarios[index] as Map<String, dynamic>;
                    return _buildVoluntarioCard(voluntario);
                  },
                ),
              ),
    );
  }

  Widget _buildVoluntarioCard(Map<String, dynamic> voluntario) {
    final nombre = voluntario['nombre'] as String? ?? 'Sin nombre';
    final email = voluntario['email'] as String? ?? 'Sin email';
    final evento = voluntario['evento_titulo'] as String? ?? 'N/A';
    final asistio = voluntario['asistio'] as bool? ?? false;
    final puntos = voluntario['puntos'] as int? ?? 0;
    final fechaInscripcion = voluntario['fecha_inscripcion'] as String?;

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  backgroundColor: Theme.of(context).primaryColor,
                  child: Text(
                    nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
                    style: const TextStyle(color: Colors.white),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        nombre,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        email,
                        style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                      ),
                    ],
                  ),
                ),
                if (asistio)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.green[100],
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      'Asistió',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.green[800],
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.event, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 4),
                Expanded(
                  child: Text(
                    evento,
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.star, size: 16, color: Colors.amber),
                const SizedBox(width: 4),
                Text(
                  '$puntos puntos',
                  style: TextStyle(
                    color: Colors.grey[700],
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
            if (fechaInscripcion != null) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.calendar_today, size: 16, color: Colors.grey[600]),
                  const SizedBox(width: 4),
                  Text(
                    'Inscrito: ${_formatDate(fechaInscripcion)}',
                    style: TextStyle(color: Colors.grey[600], fontSize: 12),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  String _formatDate(String dateString) {
    try {
      final date = DateTime.parse(dateString);
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
    } catch (e) {
      return dateString;
    }
  }
}
