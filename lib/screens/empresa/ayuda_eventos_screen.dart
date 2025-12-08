import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';
import '../../models/evento.dart';
import '../evento_detail_screen.dart';
import '../../utils/image_helper.dart';

class AyudaEventosScreen extends StatefulWidget {
  const AyudaEventosScreen({super.key});

  @override
  State<AyudaEventosScreen> createState() => _AyudaEventosScreenState();
}

class _AyudaEventosScreenState extends State<AyudaEventosScreen> {
  List<Evento> _eventos = [];
  Set<int> _eventosPatrocinados =
      {}; // IDs de eventos que la empresa ya patrocina
  bool _isLoading = true;
  String? _error;
  int? _empresaId;

  @override
  void initState() {
    super.initState();
    _initializeData();
  }

  Future<void> _initializeData() async {
    // Cargar primero el ID de la empresa
    await _loadEmpresaId();
    // Luego cargar los eventos y los eventos patrocinados
    await Future.wait([_loadEventos(), _loadEventosPatrocinados()]);
  }

  Future<void> _loadEmpresaId() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _empresaId = userData?['entity_id'] as int?;
    });
  }

  Future<void> _loadEventos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getEventosPublicados();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        final todosEventos = result['eventos'] as List<Evento>;
        // Mostrar todos los eventos (ya no filtrar)
        _eventos = todosEventos;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar eventos';
      }
    });
  }

  // Cargar los eventos donde la empresa es patrocinadora
  Future<void> _loadEventosPatrocinados() async {
    if (_empresaId == null) return;

    final result = await ApiService.getMisEventosEmpresa();
    if (result['success'] == true) {
      final eventos = result['eventos'] as List<dynamic>? ?? [];
      final eventosPatrocinados =
          eventos
              .where((e) => e['tipo_relacion'] == 'patrocinadora')
              .map((e) => e['evento']?['id'] as int?)
              .where((id) => id != null)
              .cast<int>()
              .toSet();

      setState(() {
        _eventosPatrocinados = eventosPatrocinados;
      });
    }
  }

  // Verificar si la empresa ya está patrocinando un evento
  bool _estaPatrocinando(Evento evento) {
    if (_empresaId == null) {
      return false;
    }

    // PRIMERO: Verificar directamente en el campo patrocinadores del evento
    if (evento.patrocinadores != null && evento.patrocinadores!.isNotEmpty) {
      final patrocinadores = evento.patrocinadores!;
      final empresaIdStr = _empresaId.toString();
      final empresaIdInt = _empresaId;

      // Verificar de múltiples formas para asegurar compatibilidad
      final estaEnPatrocinadores = patrocinadores.any((p) {
        if (p == null) return false;

        // Comparar como string (más común)
        if (p.toString().trim() == empresaIdStr) {
          return true;
        }

        // Comparar como int
        if (p == empresaIdInt) {
          return true;
        }

        // Intentar convertir a int y comparar
        try {
          final pInt = int.tryParse(p.toString().trim());
          if (pInt != null && pInt == empresaIdInt) {
            return true;
          }
        } catch (e) {
          // Ignorar errores de conversión
        }

        return false;
      });

      if (estaEnPatrocinadores) {
        return true;
      }
    }

    // SEGUNDO: Verificar en el Set de eventos patrocinados como respaldo
    if (_eventosPatrocinados.contains(evento.id)) {
      return true;
    }

    return false;
  }

  Future<void> _patrocinarEvento(Evento evento) async {
    if (_empresaId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: No se pudo identificar la empresa'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Patrocinar Evento'),
            content: Text('¿Deseas patrocinar el evento "${evento.titulo}"?'),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Patrocinar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result = await ApiService.patrocinarEvento(
      eventoId: evento.id,
      empresaId: _empresaId!,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      // ACTUALIZAR INMEDIATAMENTE el evento local agregando el ID de la empresa a patrocinadores
      setState(() {
        final index = _eventos.indexWhere((e) => e.id == evento.id);
        if (index != -1) {
          final eventoActualizado = _eventos[index];
          final patrocinadoresActuales = eventoActualizado.patrocinadores ?? [];
          if (!patrocinadoresActuales.contains(_empresaId)) {
            final nuevosPatrocinadores = [
              ...patrocinadoresActuales,
              _empresaId,
            ];
            _eventos[index] = Evento(
              id: eventoActualizado.id,
              ongId: eventoActualizado.ongId,
              titulo: eventoActualizado.titulo,
              descripcion: eventoActualizado.descripcion,
              tipoEvento: eventoActualizado.tipoEvento,
              fechaInicio: eventoActualizado.fechaInicio,
              fechaFin: eventoActualizado.fechaFin,
              fechaLimiteInscripcion: eventoActualizado.fechaLimiteInscripcion,
              capacidadMaxima: eventoActualizado.capacidadMaxima,
              estado: eventoActualizado.estado,
              ciudad: eventoActualizado.ciudad,
              direccion: eventoActualizado.direccion,
              lat: eventoActualizado.lat,
              lng: eventoActualizado.lng,
              inscripcionAbierta: eventoActualizado.inscripcionAbierta,
              patrocinadores: nuevosPatrocinadores,
              invitados: eventoActualizado.invitados,
              imagenes: eventoActualizado.imagenes,
              auspiciadores: eventoActualizado.auspiciadores,
              createdAt: eventoActualizado.createdAt,
              updatedAt: eventoActualizado.updatedAt,
            );
          }
        }
        // También agregar al Set
        _eventosPatrocinados.add(evento.id);
      });

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Evento patrocinado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );

      // Recargar eventos y eventos patrocinados en segundo plano para sincronizar
      Future.wait([_loadEventos(), _loadEventosPatrocinados()]);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al patrocinar evento',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/empresa/eventos/disponibles'),
      appBar: AppBar(
        title: const Text('Ayuda a Eventos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () async {
              await Future.wait([_loadEventos(), _loadEventosPatrocinados()]);
            },
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
                    FilledButton.tonal(
                      onPressed: () async {
                        await Future.wait([
                          _loadEventos(),
                          _loadEventosPatrocinados(),
                        ]);
                      },
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _eventos.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.event_busy, size: 64, color: Colors.grey[400]),
                    const SizedBox(height: 16),
                    Text(
                      'No hay eventos disponibles',
                      style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'No se encontraron eventos para mostrar',
                      style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: () async {
                  await Future.wait([
                    _loadEventos(),
                    _loadEventosPatrocinados(),
                  ]);
                },
                child: ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: _eventos.length,
                  itemBuilder: (context, index) {
                    final evento = _eventos[index];
                    return _buildEventoCard(evento);
                  },
                ),
              ),
    );
  }

  Widget _buildEventoCard(Evento evento) {
    // Obtener la primera imagen usando el helper
    final imagenUrl = ImageHelper.getFirstImageUrl(evento.imagenes);

    // Verificar si está patrocinando (se calcula aquí para asegurar que se actualice)
    final estaPatrocinando = _estaPatrocinando(evento);

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      elevation: 2,
      clipBehavior: Clip.antiAlias,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          InkWell(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => EventoDetailScreen(eventoId: evento.id),
                ),
              ).then((_) => _loadEventos());
            },
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (imagenUrl != null)
                  CachedNetworkImage(
                    imageUrl: imagenUrl,
                    height: 200,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    placeholder:
                        (context, url) => Container(
                          height: 200,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: const Center(
                            child: CircularProgressIndicator(),
                          ),
                        ),
                    errorWidget:
                        (context, url, error) => Container(
                          height: 200,
                          width: double.infinity,
                          color: Colors.grey[200],
                          child: Icon(
                            Icons.image_not_supported,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                        ),
                  )
                else
                  Container(
                    height: 200,
                    width: double.infinity,
                    color: Colors.grey[200],
                    child: Icon(Icons.event, size: 64, color: Colors.grey[400]),
                  ),
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        evento.titulo,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Icon(
                            Icons.category,
                            size: 16,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            evento.tipoEvento,
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(
                            Icons.calendar_today,
                            size: 16,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            _formatDate(evento.fechaInicio),
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ],
                      ),
                      if (evento.ciudad != null) ...[
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(
                              Icons.location_on,
                              size: 16,
                              color: Colors.grey[600],
                            ),
                            const SizedBox(width: 4),
                            Text(
                              evento.ciudad!,
                              style: TextStyle(color: Colors.grey[600]),
                            ),
                          ],
                        ),
                      ],
                      if (evento.descripcion != null &&
                          evento.descripcion!.isNotEmpty) ...[
                        const SizedBox(height: 12),
                        Text(
                          evento.descripcion!,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(color: Colors.grey[700]),
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: SizedBox(
              width: double.infinity,
              child:
                  estaPatrocinando
                      ? Container(
                        decoration: BoxDecoration(
                          color: Colors.green[600],
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: FilledButton.icon(
                          onPressed:
                              null, // Deshabilitado cuando ya está patrocinando
                          icon: const Icon(Icons.check_circle, size: 20),
                          label: const Text(
                            'Está Patrocinando',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          style: FilledButton.styleFrom(
                            backgroundColor: Colors.green[600],
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            disabledBackgroundColor: Colors.green[600],
                            disabledForegroundColor: Colors.white,
                            elevation: 0,
                          ),
                        ),
                      )
                      : FilledButton.icon(
                        onPressed: () => _patrocinarEvento(evento),
                        icon: const Icon(Icons.favorite, size: 20),
                        label: const Text(
                          'Patrocinar Evento',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        style: FilledButton.styleFrom(
                          backgroundColor: Theme.of(context).primaryColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          elevation: 2,
                        ),
                      ),
            ),
          ),
        ],
      ),
    );
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
}
