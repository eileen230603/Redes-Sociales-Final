import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/notificacion.dart';
import '../../widgets/app_drawer.dart';
import '../evento_detail_screen.dart';

class NotificacionesEmpresaScreen extends StatefulWidget {
  const NotificacionesEmpresaScreen({super.key});

  @override
  State<NotificacionesEmpresaScreen> createState() =>
      _NotificacionesEmpresaScreenState();
}

class _NotificacionesEmpresaScreenState
    extends State<NotificacionesEmpresaScreen> {
  List<Notificacion> _notificaciones = [];
  int _noLeidas = 0;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadNotificaciones();
  }

  Future<void> _loadNotificaciones() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getNotificacionesEmpresa();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _notificaciones = result['notificaciones'] as List<Notificacion>;
        _noLeidas = result['no_leidas'] as int? ?? 0;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar notificaciones';
        _notificaciones = [];
      }
    });
  }

  Future<void> _marcarComoLeida(Notificacion notificacion) async {
    if (notificacion.leida) return;

    final result = await ApiService.marcarNotificacionLeidaEmpresa(
      notificacion.id,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        final index = _notificaciones.indexWhere(
          (n) => n.id == notificacion.id,
        );
        if (index != -1) {
          _notificaciones[index] = notificacion.copyWith(leida: true);
          if (_noLeidas > 0) {
            _noLeidas--;
          }
        }
      });
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al marcar notificación',
            ),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _marcarTodasLeidas() async {
    if (_noLeidas == 0) return;

    final result = await ApiService.marcarTodasLeidasEmpresa();

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _notificaciones =
            _notificaciones.map((n) => n.copyWith(leida: true)).toList();
        _noLeidas = 0;
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Todas las notificaciones marcadas como leídas'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['error'] as String? ?? 'Error al marcar notificaciones',
            ),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _navegarAEvento(int? eventoId) {
    if (eventoId == null) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => EventoDetailScreen(eventoId: eventoId),
      ),
    ).then((_) {
      // Recargar notificaciones al volver
      _loadNotificaciones();
    });
  }

  IconData _getIconoTipo(String tipo) {
    switch (tipo.toLowerCase()) {
      case 'empresa_asignada':
        return Icons.business;
      case 'empresa_confirmada':
        return Icons.check_circle;
      default:
        return Icons.notifications;
    }
  }

  Color _getColorTipo(String tipo) {
    switch (tipo.toLowerCase()) {
      case 'empresa_asignada':
        return Colors.blue;
      case 'empresa_confirmada':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/empresa/notificaciones'),
      appBar: AppBar(
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Flexible(
              child: Text('Notificaciones', overflow: TextOverflow.ellipsis),
            ),
            if (_noLeidas > 0) ...[
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.red,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  _noLeidas.toString(),
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ],
          ],
        ),
        actions: [
          if (_noLeidas > 0)
            TextButton.icon(
              onPressed: _marcarTodasLeidas,
              icon: const Icon(Icons.done_all, size: 18),
              label: const Text('Marcar todas', style: TextStyle(fontSize: 12)),
              style: TextButton.styleFrom(foregroundColor: Colors.white),
            ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadNotificaciones,
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
                      onPressed: _loadNotificaciones,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : _notificaciones.isEmpty
              ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.notifications_none,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'No tienes notificaciones',
                      style: TextStyle(color: Colors.grey[600], fontSize: 16),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadNotificaciones,
                child: ListView.builder(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  itemCount: _notificaciones.length,
                  itemBuilder: (context, index) {
                    final notificacion = _notificaciones[index];
                    return _buildNotificacionItem(notificacion);
                  },
                ),
              ),
    );
  }

  Widget _buildNotificacionItem(Notificacion notificacion) {
    final colorTipo = _getColorTipo(notificacion.tipo);
    final iconoTipo = _getIconoTipo(notificacion.tipo);

    return InkWell(
      onTap: () {
        _marcarComoLeida(notificacion);
        if (notificacion.eventoId != null) {
          _navegarAEvento(notificacion.eventoId);
        }
      },
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        decoration: BoxDecoration(
          color:
              notificacion.leida ? Colors.white : colorTipo.withOpacity(0.05),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color:
                notificacion.leida
                    ? Colors.grey[200]!
                    : colorTipo.withOpacity(0.3),
            width: 1,
          ),
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Icono
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: colorTipo.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(24),
                ),
                child: Icon(iconoTipo, color: colorTipo, size: 24),
              ),
              const SizedBox(width: 12),
              // Contenido
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            notificacion.titulo,
                            style: TextStyle(
                              fontWeight:
                                  notificacion.leida
                                      ? FontWeight.normal
                                      : FontWeight.bold,
                              fontSize: 15,
                              color: Colors.grey[800],
                            ),
                          ),
                        ),
                        if (!notificacion.leida)
                          Container(
                            width: 8,
                            height: 8,
                            decoration: BoxDecoration(
                              color: colorTipo,
                              shape: BoxShape.circle,
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      notificacion.mensaje,
                      style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    if (notificacion.eventoTitulo != null) ...[
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(Icons.event, size: 14, color: Colors.grey[500]),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              notificacion.eventoTitulo!,
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[500],
                                fontStyle: FontStyle.italic,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                    const SizedBox(height: 8),
                    Text(
                      _formatFecha(notificacion.fecha),
                      style: TextStyle(fontSize: 12, color: Colors.grey[400]),
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

  String _formatFecha(DateTime fecha) {
    final ahora = DateTime.now();
    final diferencia = ahora.difference(fecha);

    if (diferencia.inDays == 0) {
      if (diferencia.inHours == 0) {
        if (diferencia.inMinutes == 0) {
          return 'Hace unos momentos';
        }
        return 'Hace ${diferencia.inMinutes} minuto${diferencia.inMinutes > 1 ? 's' : ''}';
      }
      return 'Hace ${diferencia.inHours} hora${diferencia.inHours > 1 ? 's' : ''}';
    } else if (diferencia.inDays == 1) {
      return 'Ayer';
    } else if (diferencia.inDays < 7) {
      return 'Hace ${diferencia.inDays} días';
    } else {
      return '${fecha.day}/${fecha.month}/${fecha.year}';
    }
  }
}
