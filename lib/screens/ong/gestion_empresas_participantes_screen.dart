import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../widgets/app_drawer.dart';
import '../../utils/image_helper.dart';

class GestionEmpresasParticipantesScreen extends StatefulWidget {
  final int eventoId;
  final String? eventoTitulo;

  const GestionEmpresasParticipantesScreen({
    super.key,
    required this.eventoId,
    this.eventoTitulo,
  });

  @override
  State<GestionEmpresasParticipantesScreen> createState() =>
      _GestionEmpresasParticipantesScreenState();
}

class _GestionEmpresasParticipantesScreenState
    extends State<GestionEmpresasParticipantesScreen> {
  List<dynamic> _empresasParticipantes = [];
  List<dynamic> _empresasDisponibles = [];
  Set<int> _empresasSeleccionadas = {};
  bool _isLoading = true;
  bool _isLoadingEmpresas = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadEmpresasParticipantes();
    _loadEmpresasDisponibles();
  }

  Future<void> _loadEmpresasParticipantes() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEmpresasParticipantes(widget.eventoId);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _empresasParticipantes = result['empresas'] as List<dynamic>? ?? [];
      } else {
        _error =
            result['error'] as String? ??
            'Error al cargar empresas participantes';
      }
    });
  }

  Future<void> _loadEmpresasDisponibles() async {
    setState(() {
      _isLoadingEmpresas = true;
    });

    final result = await ApiService.getEmpresasDisponibles();

    if (!mounted) return;

    setState(() {
      _isLoadingEmpresas = false;
      if (result['success'] == true) {
        _empresasDisponibles = result['empresas'] as List<dynamic>? ?? [];
        // Filtrar empresas que ya están asignadas
        final empresasAsignadasIds =
            _empresasParticipantes
                .map((e) => e['empresa_id'] as int?)
                .whereType<int>()
                .toSet();
        _empresasDisponibles =
            _empresasDisponibles
                .where((e) => !empresasAsignadasIds.contains(e['id'] as int?))
                .toList();
      }
    });
  }

  Future<void> _asignarEmpresas() async {
    if (_empresasSeleccionadas.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Selecciona al menos una empresa'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Asignar Empresas'),
            content: Text(
              '¿Estás seguro de que deseas asignar ${_empresasSeleccionadas.length} empresa(s) a este evento?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, true),
                style: TextButton.styleFrom(foregroundColor: Colors.green),
                child: const Text('Asignar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.asignarEmpresasAEvento(
      widget.eventoId,
      _empresasSeleccionadas.toList(),
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Empresas asignadas correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      _empresasSeleccionadas.clear();
      await _loadEmpresasParticipantes();
      await _loadEmpresasDisponibles();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al asignar empresas',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _removerEmpresa(int empresaId, String nombreEmpresa) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Remover Empresa'),
            content: Text(
              '¿Estás seguro de que deseas remover a $nombreEmpresa de este evento?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, true),
                style: TextButton.styleFrom(foregroundColor: Colors.red),
                child: const Text('Remover'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.removerEmpresasDeEvento(widget.eventoId, [
      empresaId,
    ]);

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Empresa removida correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      await _loadEmpresasParticipantes();
      await _loadEmpresasDisponibles();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al remover empresa',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(),
      appBar: AppBar(
        title: Text(widget.eventoTitulo ?? 'Empresas Participantes'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _loadEmpresasParticipantes();
              _loadEmpresasDisponibles();
            },
            tooltip: 'Actualizar',
          ),
        ],
        bottom: TabBar(
          tabs: const [
            Tab(icon: Icon(Icons.business), text: 'Participantes'),
            Tab(icon: Icon(Icons.add_business), text: 'Asignar'),
          ],
        ),
      ),
      body:
          _isLoading && _empresasParticipantes.isEmpty
              ? const Center(child: CircularProgressIndicator())
              : _error != null && _empresasParticipantes.isEmpty
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
                    ElevatedButton(
                      onPressed: _loadEmpresasParticipantes,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : TabBarView(
                children: [_buildTabParticipantes(), _buildTabAsignar()],
              ),
    );
  }

  Widget _buildTabParticipantes() {
    return RefreshIndicator(
      onRefresh: _loadEmpresasParticipantes,
      child:
          _empresasParticipantes.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.business_outlined,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No hay empresas participantes',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Asigna empresas desde la pestaña "Asignar"',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                    ),
                  ],
                ),
              )
              : ListView.builder(
                padding: const EdgeInsets.all(8),
                itemCount: _empresasParticipantes.length,
                itemBuilder: (context, index) {
                  final empresa =
                      _empresasParticipantes[index] as Map<String, dynamic>;
                  return _buildEmpresaCard(empresa);
                },
              ),
    );
  }

  Widget _buildTabAsignar() {
    return RefreshIndicator(
      onRefresh: _loadEmpresasDisponibles,
      child: Column(
        children: [
          if (_empresasSeleccionadas.isNotEmpty)
            Container(
              padding: const EdgeInsets.all(16),
              color: Colors.blue[50],
              child: Row(
                children: [
                  Icon(Icons.info_outline, color: Colors.blue[700]),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      '${_empresasSeleccionadas.length} empresa(s) seleccionada(s)',
                      style: TextStyle(color: Colors.blue[900]),
                    ),
                  ),
                  ElevatedButton(
                    onPressed: _asignarEmpresas,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF00A36C),
                      foregroundColor: Colors.white,
                    ),
                    child: const Text('Asignar'),
                  ),
                ],
              ),
            ),
          Expanded(
            child:
                _isLoadingEmpresas
                    ? const Center(child: CircularProgressIndicator())
                    : _empresasDisponibles.isEmpty
                    ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.business_outlined,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'No hay empresas disponibles',
                            style: TextStyle(
                              fontSize: 18,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Todas las empresas ya están asignadas',
                            style: TextStyle(
                              fontSize: 14,
                              color: Colors.grey[500],
                            ),
                          ),
                        ],
                      ),
                    )
                    : ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: _empresasDisponibles.length,
                      itemBuilder: (context, index) {
                        final empresa =
                            _empresasDisponibles[index] as Map<String, dynamic>;
                        return _buildEmpresaDisponibleCard(empresa);
                      },
                    ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmpresaCard(Map<String, dynamic> empresa) {
    final empresaId = empresa['empresa_id'] as int?;
    final nombreEmpresa = empresa['nombre_empresa'] as String? ?? 'Sin nombre';
    final nit = empresa['NIT'] as String?;
    final telefono = empresa['telefono'] as String?;
    final sitioWeb = empresa['sitio_web'] as String?;
    final fotoPerfil = empresa['foto_perfil'] as String?;
    final estado = empresa['estado'] as String? ?? 'asignada';
    final tipoColaboracion = empresa['tipo_colaboracion'] as String?;
    final descripcionColaboracion =
        empresa['descripcion_colaboracion'] as String?;
    final fechaAsignacion = empresa['fecha_asignacion'] as String?;
    final fechaConfirmacion = empresa['fecha_confirmacion'] as String?;

    Color estadoColor;
    IconData estadoIcon;
    String estadoLabel;
    switch (estado.toLowerCase()) {
      case 'confirmada':
        estadoColor = Colors.green;
        estadoIcon = Icons.check_circle;
        estadoLabel = 'Confirmada';
        break;
      case 'cancelada':
        estadoColor = Colors.red;
        estadoIcon = Icons.cancel;
        estadoLabel = 'Cancelada';
        break;
      default:
        estadoColor = Colors.orange;
        estadoIcon = Icons.pending;
        estadoLabel = 'Asignada';
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundImage:
                      fotoPerfil != null
                          ? CachedNetworkImageProvider(
                            ImageHelper.buildImageUrl(fotoPerfil) ?? '',
                          )
                          : null,
                  backgroundColor: Colors.blue[100],
                  child:
                      fotoPerfil == null
                          ? Text(
                            nombreEmpresa.isNotEmpty
                                ? nombreEmpresa[0].toUpperCase()
                                : 'E',
                            style: TextStyle(
                              color: Colors.blue[800],
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          )
                          : null,
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        nombreEmpresa,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Chip(
                        avatar: Icon(estadoIcon, size: 16, color: estadoColor),
                        label: Text(
                          estadoLabel,
                          style: TextStyle(
                            fontSize: 12,
                            color: estadoColor,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        backgroundColor: estadoColor.withOpacity(0.1),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.delete, color: Colors.red),
                  onPressed:
                      empresaId != null
                          ? () => _removerEmpresa(empresaId, nombreEmpresa)
                          : null,
                  tooltip: 'Remover empresa',
                ),
              ],
            ),
            if (nit != null || telefono != null || sitioWeb != null) ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 8),
              if (nit != null) _buildInfoRow(Icons.badge, 'NIT', nit),
              if (telefono != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(Icons.phone, 'Teléfono', telefono),
              ],
              if (sitioWeb != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(Icons.language, 'Sitio Web', sitioWeb),
              ],
            ],
            if (tipoColaboracion != null ||
                descripcionColaboracion != null) ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 8),
              if (tipoColaboracion != null)
                _buildInfoRow(
                  Icons.category,
                  'Tipo de Colaboración',
                  tipoColaboracion,
                ),
              if (descripcionColaboracion != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(
                  Icons.description,
                  'Descripción',
                  descripcionColaboracion,
                ),
              ],
            ],
            if (fechaAsignacion != null || fechaConfirmacion != null) ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 8),
              if (fechaAsignacion != null)
                _buildInfoRow(
                  Icons.calendar_today,
                  'Fecha de Asignación',
                  _formatFecha(fechaAsignacion),
                ),
              if (fechaConfirmacion != null) ...[
                const SizedBox(height: 8),
                _buildInfoRow(
                  Icons.check_circle,
                  'Fecha de Confirmación',
                  _formatFecha(fechaConfirmacion),
                ),
              ],
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildEmpresaDisponibleCard(Map<String, dynamic> empresa) {
    final empresaId = empresa['id'] as int?;
    final nombre = empresa['nombre'] as String? ?? 'Sin nombre';
    final descripcion = empresa['descripcion'] as String?;
    final estaSeleccionada =
        empresaId != null && _empresasSeleccionadas.contains(empresaId);

    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      elevation: estaSeleccionada ? 4 : 2,
      color: estaSeleccionada ? Colors.blue[50] : null,
      child: CheckboxListTile(
        value: estaSeleccionada,
        onChanged:
            empresaId != null
                ? (value) {
                  setState(() {
                    if (value == true) {
                      _empresasSeleccionadas.add(empresaId);
                    } else {
                      _empresasSeleccionadas.remove(empresaId);
                    }
                  });
                }
                : null,
        title: Text(
          nombre,
          style: TextStyle(
            fontWeight: estaSeleccionada ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        subtitle:
            descripcion != null && descripcion.isNotEmpty
                ? Text(descripcion)
                : null,
        secondary: Icon(
          Icons.business,
          color: estaSeleccionada ? Colors.blue[700] : Colors.grey[600],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w500,
            color: Colors.grey[700],
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: TextStyle(fontSize: 14, color: Colors.grey[800]),
          ),
        ),
      ],
    );
  }

  String _formatFecha(String fechaStr) {
    try {
      final fecha = DateTime.parse(fechaStr);
      return '${fecha.day}/${fecha.month}/${fecha.year} ${fecha.hour.toString().padLeft(2, '0')}:${fecha.minute.toString().padLeft(2, '0')}';
    } catch (e) {
      return fechaStr;
    }
  }
}
