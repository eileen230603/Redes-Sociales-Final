import 'dart:typed_data';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter/foundation.dart' show kIsWeb, defaultTargetPlatform;
import 'package:flutter/foundation.dart' show TargetPlatform;
import 'package:image_picker/image_picker.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:http/http.dart' as http;
import 'package:cached_network_image/cached_network_image.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/evento.dart';
import '../../models/tipo_evento.dart';
import '../../widgets/app_drawer.dart';
import '../../utils/image_helper.dart';

class EditarEventoScreen extends StatefulWidget {
  final int eventoId;

  const EditarEventoScreen({super.key, required this.eventoId});

  @override
  State<EditarEventoScreen> createState() => _EditarEventoScreenState();
}

class _EditarEventoScreenState extends State<EditarEventoScreen> {
  final _formKey = GlobalKey<FormState>();
  final _tituloController = TextEditingController();
  final _descripcionController = TextEditingController();
  final _ciudadController = TextEditingController();
  final _direccionController = TextEditingController();
  final _capacidadController = TextEditingController();

  // Mapa
  final MapController _mapController = MapController();
  LatLng? _selectedLocation;
  String _direccionSeleccionada = '';

  DateTime? _fechaInicio;
  DateTime? _fechaFin;
  DateTime? _fechaLimiteInscripcion;
  String _estado = 'borrador';
  bool _inscripcionAbierta = true;
  int? _ongId;
  bool _isLoading = false;
  bool _isLoadingEvento = true;
  String? _error;
  final ImagePicker _imagePicker = ImagePicker();
  List<XFile> _imagenesSeleccionadas = [];
  List<String> _imagenesExistentes = []; // URLs de imágenes existentes

  // Patrocinadores e invitados
  List<Map<String, dynamic>> _empresasDisponibles = [];
  List<Map<String, dynamic>> _invitadosDisponibles = [];
  Set<int> _patrocinadoresSeleccionados = {};
  Set<int> _invitadosSeleccionados = {};
  bool _isLoadingEmpresas = false;
  bool _isLoadingInvitados = false;

  // Tipos de evento
  List<TipoEvento> _tiposEvento = [];
  bool _isLoadingTiposEvento = false;
  String? _tipoEventoSeleccionado;

  // Tipos de evento por defecto
  static const List<String> _tiposEventoPorDefecto = [
    'conferencia',
    'taller',
    'seminario',
    'voluntariado',
    'cultural',
    'deportivo',
    'otro',
  ];

  @override
  void initState() {
    super.initState();
    _loadOngId();
    _loadEvento();
    _loadEmpresasDisponibles();
    _loadInvitadosDisponibles();
    _loadTiposEvento();
  }

  Future<void> _loadOngId() async {
    final ongId = await AuthHelper.getOngIdWithRetry();
    setState(() {
      _ongId = ongId;
    });
  }

  Future<void> _loadEvento() async {
    setState(() {
      _isLoadingEvento = true;
      _error = null;
    });

    final result = await ApiService.getEventoDetalle(widget.eventoId);

    if (!mounted) return;

    if (result['success'] == true) {
      final evento = result['evento'] as Evento;
      _cargarDatosEvento(evento);
    } else {
      setState(() {
        _error = result['error'] as String? ?? 'Error al cargar evento';
        _isLoadingEvento = false;
      });
    }
  }

  void _cargarDatosEvento(Evento evento) {
    setState(() {
      _tituloController.text = evento.titulo;
      _descripcionController.text = evento.descripcion ?? '';
      _ciudadController.text = evento.ciudad ?? '';
      _direccionController.text = evento.direccion ?? '';
      _capacidadController.text = evento.capacidadMaxima?.toString() ?? '';
      _estado = evento.estado;
      _inscripcionAbierta = evento.inscripcionAbierta;
      _fechaInicio = evento.fechaInicio;
      _fechaFin = evento.fechaFin;
      _fechaLimiteInscripcion = evento.fechaLimiteInscripcion;
      _tipoEventoSeleccionado = evento.tipoEvento;

      // Cargar imágenes existentes
      if (evento.imagenes != null && evento.imagenes is List) {
        _imagenesExistentes =
            (evento.imagenes as List)
                .map((img) => ImageHelper.buildImageUrl(img.toString()))
                .whereType<String>()
                .toList();
      }

      // Cargar patrocinadores e invitados
      if (evento.patrocinadores != null && evento.patrocinadores is List) {
        final patrocinadores = evento.patrocinadores as List;
        _patrocinadoresSeleccionados =
            patrocinadores
                .where((p) => p is int || (p is Map && p['id'] != null))
                .map((p) => p is int ? p : (p as Map)['id'] as int)
                .toSet();
      }

      if (evento.invitados != null && evento.invitados is List) {
        final invitados = evento.invitados as List;
        _invitadosSeleccionados =
            invitados
                .where((i) => i is int || (i is Map && i['id'] != null))
                .map((i) => i is int ? i : (i as Map)['id'] as int)
                .toSet();
      }

      // Cargar ubicación
      if (evento.lat != null && evento.lng != null) {
        _selectedLocation = LatLng(evento.lat!, evento.lng!);
        _direccionSeleccionada = evento.direccion ?? '';
      }

      _isLoadingEvento = false;
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
        _empresasDisponibles =
            (result['empresas'] as List).cast<Map<String, dynamic>>();
      }
    });
  }

  Future<void> _loadInvitadosDisponibles() async {
    setState(() {
      _isLoadingInvitados = true;
    });
    final result = await ApiService.getInvitadosDisponibles();
    if (!mounted) return;
    setState(() {
      _isLoadingInvitados = false;
      if (result['success'] == true) {
        _invitadosDisponibles =
            (result['invitados'] as List).cast<Map<String, dynamic>>();
      }
    });
  }

  Future<void> _loadTiposEvento() async {
    setState(() {
      _isLoadingTiposEvento = true;
    });

    try {
      final result = await ParametrizacionService.getTiposEvento(activo: true);

      if (!mounted) return;

      setState(() {
        _isLoadingTiposEvento = false;
        if (result['success'] == true) {
          final tiposCargados =
              (result['tipos'] as List)
                  .map((t) => TipoEvento.fromJson(t as Map<String, dynamic>))
                  .toList();
          if (tiposCargados.isNotEmpty) {
            _tiposEvento = tiposCargados;
          } else {
            _usarTiposPorDefecto();
          }
        } else {
          _usarTiposPorDefecto();
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoadingTiposEvento = false;
        _usarTiposPorDefecto();
      });
    }
  }

  void _usarTiposPorDefecto() {
    _tiposEvento =
        _tiposEventoPorDefecto.asMap().entries.map((entry) {
          final index = entry.key;
          final nombre = entry.value;
          final nombreMostrar = nombre[0].toUpperCase() + nombre.substring(1);
          return TipoEvento(
            id: index + 1,
            codigo: nombre,
            nombre: nombreMostrar,
            orden: index,
            activo: true,
          );
        }).toList();
  }

  Future<void> _reverseGeocode(double lat, double lng) async {
    try {
      final response = await http.get(
        Uri.parse(
          'https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lng&format=json',
        ),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body) as Map<String, dynamic>;
        final displayName = data['display_name'] as String? ?? '';
        final address = data['address'] as Map<String, dynamic>?;

        final ciudad =
            address?['city'] ??
            address?['town'] ??
            address?['village'] ??
            address?['municipality'] ??
            address?['county'] ??
            address?['state'] ??
            '';

        setState(() {
          _direccionSeleccionada = displayName;
          _direccionController.text = displayName;
          if (ciudad.isNotEmpty && _ciudadController.text.trim().isEmpty) {
            _ciudadController.text = ciudad;
          }
        });
      }
    } catch (e) {
      final coordenadasTexto =
          'Lat: ${lat.toStringAsFixed(7)}, Lng: ${lng.toStringAsFixed(7)}';
      setState(() {
        _direccionSeleccionada = coordenadasTexto;
        _direccionController.text = coordenadasTexto;
      });
    }
  }

  bool _isDesktop() {
    if (kIsWeb) return false;
    return defaultTargetPlatform == TargetPlatform.windows ||
        defaultTargetPlatform == TargetPlatform.linux ||
        defaultTargetPlatform == TargetPlatform.macOS;
  }

  Future<void> _selectFechaInicio() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _fechaInicio ?? DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.fromDateTime(_fechaInicio ?? DateTime.now()),
      );
      if (time != null) {
        setState(() {
          _fechaInicio = DateTime(
            picked.year,
            picked.month,
            picked.day,
            time.hour,
            time.minute,
          );
        });
      }
    }
  }

  Future<void> _selectFechaFin() async {
    if (_fechaInicio == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Primero selecciona la fecha de inicio')),
      );
      return;
    }

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _fechaFin ?? _fechaInicio!.add(const Duration(days: 1)),
      firstDate: _fechaInicio!,
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.fromDateTime(_fechaFin ?? DateTime.now()),
      );
      if (time != null) {
        setState(() {
          _fechaFin = DateTime(
            picked.year,
            picked.month,
            picked.day,
            time.hour,
            time.minute,
          );
        });
      }
    }
  }

  Future<void> _selectFechaLimiteInscripcion() async {
    if (_fechaInicio == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Primero selecciona la fecha de inicio')),
      );
      return;
    }

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _fechaLimiteInscripcion ?? DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: _fechaInicio!,
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime:
            _fechaLimiteInscripcion != null
                ? TimeOfDay.fromDateTime(_fechaLimiteInscripcion!)
                : TimeOfDay.now(),
      );
      if (time != null) {
        setState(() {
          _fechaLimiteInscripcion = DateTime(
            picked.year,
            picked.month,
            picked.day,
            time.hour,
            time.minute,
          );
        });
      }
    }
  }

  Future<void> _seleccionarImagenGaleria() async {
    try {
      XFile? selectedFile;

      if (_isDesktop()) {
        final result = await FilePicker.platform.pickFiles(
          type: FileType.image,
          allowMultiple: false,
        );

        if (result != null && result.files.isNotEmpty) {
          final pickedFile = result.files.single;
          if (pickedFile.path != null && pickedFile.path!.isNotEmpty) {
            selectedFile = XFile(pickedFile.path!);
          }
        }
      } else {
        selectedFile = await _imagePicker.pickImage(
          source: ImageSource.gallery,
          imageQuality: 85,
          maxWidth: 1920,
          maxHeight: 1920,
        );
      }

      if (selectedFile != null) {
        final bytes = await selectedFile.readAsBytes();
        final fileSize = bytes.length;
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (fileSize > maxSize) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'La imagen es muy grande. Por favor selecciona una imagen menor a 5MB',
              ),
              backgroundColor: Colors.orange,
            ),
          );
          return;
        }

        setState(() {
          _imagenesSeleccionadas.add(selectedFile!);
        });
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al seleccionar imagen: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _seleccionarImagenCamara() async {
    try {
      XFile? selectedFile;

      if (_isDesktop()) {
        final result = await FilePicker.platform.pickFiles(
          type: FileType.image,
          allowMultiple: false,
        );

        if (result != null && result.files.isNotEmpty) {
          final pickedFile = result.files.single;
          if (pickedFile.path != null && pickedFile.path!.isNotEmpty) {
            selectedFile = XFile(pickedFile.path!);
          }
        }
      } else {
        selectedFile = await _imagePicker.pickImage(
          source: kIsWeb ? ImageSource.gallery : ImageSource.camera,
          imageQuality: 85,
          maxWidth: 1920,
          maxHeight: 1920,
        );
      }

      if (selectedFile != null) {
        final bytes = await selectedFile.readAsBytes();
        final fileSize = bytes.length;
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (fileSize > maxSize) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'La imagen es muy grande. Por favor selecciona una imagen menor a 5MB',
              ),
              backgroundColor: Colors.orange,
            ),
          );
          return;
        }

        setState(() {
          _imagenesSeleccionadas.add(selectedFile!);
        });
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al tomar foto: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  String _formatDateTime(DateTime date) {
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
    final day = date.day.toString().padLeft(2, '0');
    final month = months[date.month - 1];
    final year = date.year;
    final hour = date.hour.toString().padLeft(2, '0');
    final minute = date.minute.toString().padLeft(2, '0');
    return '$day/$month/$year $hour:$minute';
  }

  void _eliminarImagenSeleccionada(int index) {
    setState(() {
      _imagenesSeleccionadas.removeAt(index);
    });
  }

  void _eliminarImagenExistente(int index) {
    setState(() {
      _imagenesExistentes.removeAt(index);
    });
  }

  Future<void> _actualizarEvento() async {
    if (!_formKey.currentState!.validate()) return;

    if (_ongId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: No se pudo identificar la ONG'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_fechaInicio == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona la fecha de inicio'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_tipoEventoSeleccionado == null || _tipoEventoSeleccionado!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona un tipo de evento'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final descripcionTexto = _descripcionController.text.trim();
    if (descripcionTexto.isNotEmpty && descripcionTexto.length < 10) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('La descripción debe tener al menos 10 caracteres'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    // Preparar datos del evento
    final eventoData = <String, dynamic>{
      'ong_id': _ongId,
      'titulo': _tituloController.text.trim(),
      'descripcion': descripcionTexto.isEmpty ? null : descripcionTexto,
      'tipo_evento': _tipoEventoSeleccionado,
      'fecha_inicio': _fechaInicio!.toIso8601String(),
      'fecha_fin': _fechaFin?.toIso8601String(),
      'fecha_limite_inscripcion': _fechaLimiteInscripcion?.toIso8601String(),
      'capacidad_maxima':
          _capacidadController.text.trim().isEmpty
              ? null
              : int.tryParse(
                _capacidadController.text.trim().replaceAll(
                  RegExp(r'[,.]'),
                  '',
                ),
              ),
      'estado': _estado,
      'ciudad':
          _ciudadController.text.trim().isEmpty
              ? null
              : _ciudadController.text.trim(),
      'direccion':
          _direccionSeleccionada.isNotEmpty
              ? _direccionSeleccionada
              : (_direccionController.text.trim().isEmpty
                  ? null
                  : _direccionController.text.trim()),
      'lat': _selectedLocation?.latitude,
      'lng': _selectedLocation?.longitude,
      'inscripcion_abierta': _inscripcionAbierta,
      if (_patrocinadoresSeleccionados.isNotEmpty)
        'patrocinadores': _patrocinadoresSeleccionados.toList(),
      if (_invitadosSeleccionados.isNotEmpty)
        'invitados': _invitadosSeleccionados.toList(),
      // Incluir imágenes existentes que no se eliminaron
      if (_imagenesExistentes.isNotEmpty)
        'imagenes_existentes': _imagenesExistentes,
    };

    final result = await ApiService.actualizarEvento(
      widget.eventoId,
      eventoData,
      imagenes:
          _imagenesSeleccionadas.isNotEmpty ? _imagenesSeleccionadas : null,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Evento actualizado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      Navigator.pop(context, true);
    } else {
      final errorMessage =
          result['error'] as String? ?? 'Error al actualizar evento';
      final errors = result['errors'];

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(errorMessage, style: const TextStyle(fontSize: 14)),
          backgroundColor: Colors.red,
          duration: const Duration(seconds: 6),
          action: SnackBarAction(
            label: 'Ver detalles',
            textColor: Colors.white,
            onPressed: () {
              if (errors != null) {
                showDialog(
                  context: context,
                  builder:
                      (context) => AlertDialog(
                        title: const Text('Errores de validación'),
                        content: SingleChildScrollView(
                          child: Text(
                            errors.toString(),
                            style: const TextStyle(fontSize: 12),
                          ),
                        ),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(context),
                            child: const Text('Cerrar'),
                          ),
                        ],
                      ),
                );
              }
            },
          ),
        ),
      );
    }
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _descripcionController.dispose();
    _ciudadController.dispose();
    _direccionController.dispose();
    _capacidadController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoadingEvento) {
      return Scaffold(
        drawer: const AppDrawer(),
        appBar: AppBar(title: const Text('Editar Evento')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    if (_error != null) {
      return Scaffold(
        drawer: const AppDrawer(),
        appBar: AppBar(title: const Text('Editar Evento')),
        body: Center(
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
                onPressed: _loadEvento,
                child: const Text('Reintentar'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos'),
      appBar: AppBar(title: const Text('Editar Evento')),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Información Básica',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _tituloController,
                decoration: const InputDecoration(
                  labelText: 'Título del evento *',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'El título es requerido';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _descripcionController,
                decoration: const InputDecoration(
                  labelText: 'Descripción',
                  border: OutlineInputBorder(),
                  hintText:
                      'Descripción del evento (mínimo 10 caracteres si se completa)',
                ),
                maxLines: 3,
                validator: (value) {
                  if (value != null && value.trim().isNotEmpty) {
                    if (value.trim().length < 10) {
                      return 'La descripción debe tener al menos 10 caracteres';
                    }
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              if (_isLoadingTiposEvento)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(16.0),
                    child: CircularProgressIndicator(),
                  ),
                )
              else
                DropdownButtonFormField<String>(
                  value: _tipoEventoSeleccionado,
                  decoration: const InputDecoration(
                    labelText: 'Tipo de evento *',
                    border: OutlineInputBorder(),
                    hintText: 'Selecciona un tipo de evento',
                    prefixIcon: Icon(Icons.category),
                  ),
                  items:
                      _tiposEvento.map((tipo) {
                        return DropdownMenuItem<String>(
                          value: tipo.codigo,
                          child: Text(tipo.nombre),
                        );
                      }).toList(),
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _tipoEventoSeleccionado = value;
                      });
                    }
                  },
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'El tipo de evento es requerido';
                    }
                    return null;
                  },
                ),
              const SizedBox(height: 24),
              const Text(
                'Fechas',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              ListTile(
                title: const Text('Fecha de inicio *'),
                subtitle: Text(
                  _fechaInicio == null
                      ? 'Seleccionar fecha'
                      : _formatDateTime(_fechaInicio!),
                ),
                trailing: const Icon(Icons.calendar_today),
                onTap: _selectFechaInicio,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(color: Colors.grey[300]!),
                ),
              ),
              const SizedBox(height: 8),
              ListTile(
                title: const Text('Fecha de finalización'),
                subtitle: Text(
                  _fechaFin == null
                      ? 'Seleccionar fecha (opcional)'
                      : _formatDateTime(_fechaFin!),
                ),
                trailing: const Icon(Icons.calendar_today),
                onTap: _selectFechaFin,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(color: Colors.grey[300]!),
                ),
              ),
              const SizedBox(height: 8),
              ListTile(
                title: const Text('Límite para inscribirse'),
                subtitle: Text(
                  _fechaLimiteInscripcion == null
                      ? 'Seleccionar fecha (opcional)'
                      : _formatDateTime(_fechaLimiteInscripcion!),
                ),
                trailing: const Icon(Icons.calendar_today),
                onTap: _selectFechaLimiteInscripcion,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(color: Colors.grey[300]!),
                ),
              ),
              const SizedBox(height: 24),
              const Text(
                'Ubicación',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _ciudadController,
                decoration: const InputDecoration(
                  labelText: 'Ciudad',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'Selecciona la ubicación en el mapa',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500),
              ),
              const SizedBox(height: 8),
              SizedBox(
                height: 300,
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: FlutterMap(
                    mapController: _mapController,
                    options: MapOptions(
                      initialCenter:
                          _selectedLocation ?? const LatLng(-16.5, -68.15),
                      initialZoom: 13.0,
                      onTap: (tapPosition, point) async {
                        setState(() {
                          _selectedLocation = point;
                        });
                        await _reverseGeocode(point.latitude, point.longitude);
                      },
                    ),
                    children: [
                      TileLayer(
                        urlTemplate:
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        subdomains: const ['a', 'b', 'c'],
                        userAgentPackageName:
                            'com.example.redes_sociales_mobile',
                      ),
                      if (_selectedLocation != null)
                        MarkerLayer(
                          markers: [
                            Marker(
                              point: _selectedLocation!,
                              width: 40,
                              height: 40,
                              child: const Icon(
                                Icons.location_on,
                                color: Colors.red,
                                size: 40,
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _direccionController,
                decoration: InputDecoration(
                  labelText: 'Dirección seleccionada',
                  border: const OutlineInputBorder(),
                  hintText:
                      _direccionSeleccionada.isEmpty
                          ? 'Haz clic en el mapa para seleccionar'
                          : _direccionSeleccionada,
                ),
                readOnly: true,
                onTap: () {
                  if (_selectedLocation != null) {
                    _direccionController.text = _direccionSeleccionada;
                  }
                },
              ),
              if (_selectedLocation != null) ...[
                const SizedBox(height: 8),
                Text(
                  'Coordenadas: ${_selectedLocation!.latitude.toStringAsFixed(7)}, ${_selectedLocation!.longitude.toStringAsFixed(7)}',
                  style: TextStyle(color: Colors.grey[600], fontSize: 12),
                ),
              ],
              const SizedBox(height: 24),
              const Text(
                'Empresas Colaboradoras (Opcional)',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Text(
                'Puedes seleccionar empresas que patrocinarán este evento',
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
              const SizedBox(height: 16),
              if (_isLoadingEmpresas)
                const Center(child: CircularProgressIndicator())
              else if (_empresasDisponibles.isEmpty)
                const Text(
                  'No hay empresas disponibles',
                  style: TextStyle(color: Colors.grey),
                )
              else
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children:
                      _empresasDisponibles.map((empresa) {
                        final empresaId = empresa['id'] as int;
                        final empresaNombre =
                            empresa['nombre'] as String? ?? 'Sin nombre';
                        final isSelected = _patrocinadoresSeleccionados
                            .contains(empresaId);
                        return FilterChip(
                          label: Text(empresaNombre),
                          selected: isSelected,
                          onSelected: (selected) {
                            setState(() {
                              if (selected) {
                                _patrocinadoresSeleccionados.add(empresaId);
                              } else {
                                _patrocinadoresSeleccionados.remove(empresaId);
                              }
                            });
                          },
                        );
                      }).toList(),
                ),
              const SizedBox(height: 24),
              const Text(
                'Invitados (Opcional)',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Text(
                'Puedes seleccionar invitados especiales para este evento',
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
              const SizedBox(height: 16),
              if (_isLoadingInvitados)
                const Center(child: CircularProgressIndicator())
              else if (_invitadosDisponibles.isEmpty)
                const Text(
                  'No hay invitados disponibles',
                  style: TextStyle(color: Colors.grey),
                )
              else
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children:
                      _invitadosDisponibles.map((invitado) {
                        final invitadoId = invitado['id'] as int;
                        final invitadoNombre =
                            invitado['nombre'] as String? ?? 'Sin nombre';
                        final isSelected = _invitadosSeleccionados.contains(
                          invitadoId,
                        );
                        return FilterChip(
                          label: Text(invitadoNombre),
                          selected: isSelected,
                          onSelected: (selected) {
                            setState(() {
                              if (selected) {
                                _invitadosSeleccionados.add(invitadoId);
                              } else {
                                _invitadosSeleccionados.remove(invitadoId);
                              }
                            });
                          },
                        );
                      }).toList(),
                ),
              const SizedBox(height: 24),
              const Text(
                'Imágenes del Evento',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              // Imágenes existentes
              if (_imagenesExistentes.isNotEmpty) ...[
                const Text(
                  'Imágenes existentes:',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  height: 120,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesExistentes.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Container(
                            margin: const EdgeInsets.only(right: 8),
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: Colors.grey[300]!),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: CachedNetworkImage(
                                imageUrl: _imagenesExistentes[index],
                                fit: BoxFit.cover,
                                placeholder:
                                    (context, url) => Container(
                                      color: Colors.grey[200],
                                      child: const Center(
                                        child: CircularProgressIndicator(),
                                      ),
                                    ),
                                errorWidget:
                                    (context, url, error) => Container(
                                      color: Colors.grey[200],
                                      child: const Icon(
                                        Icons.image_not_supported,
                                      ),
                                    ),
                              ),
                            ),
                          ),
                          Positioned(
                            top: 4,
                            right: 4,
                            child: CircleAvatar(
                              radius: 14,
                              backgroundColor: Colors.red,
                              child: IconButton(
                                icon: const Icon(Icons.close, size: 16),
                                color: Colors.white,
                                onPressed:
                                    () => _eliminarImagenExistente(index),
                                padding: EdgeInsets.zero,
                                constraints: const BoxConstraints(),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
                const SizedBox(height: 16),
              ],
              // Vista previa de imágenes seleccionadas nuevas
              if (_imagenesSeleccionadas.isNotEmpty) ...[
                const Text(
                  'Nuevas imágenes:',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  height: 120,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesSeleccionadas.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Container(
                            margin: const EdgeInsets.only(right: 8),
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: Colors.grey[300]!),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: FutureBuilder<Uint8List>(
                                future:
                                    _imagenesSeleccionadas[index].readAsBytes(),
                                builder: (context, snapshot) {
                                  if (snapshot.hasData) {
                                    return Image.memory(
                                      snapshot.data!,
                                      fit: BoxFit.cover,
                                    );
                                  }
                                  return const Center(
                                    child: CircularProgressIndicator(),
                                  );
                                },
                              ),
                            ),
                          ),
                          Positioned(
                            top: 4,
                            right: 12,
                            child: CircleAvatar(
                              radius: 12,
                              backgroundColor: Colors.red,
                              child: IconButton(
                                padding: EdgeInsets.zero,
                                iconSize: 16,
                                icon: const Icon(
                                  Icons.close,
                                  color: Colors.white,
                                ),
                                onPressed: () {
                                  _eliminarImagenSeleccionada(index);
                                },
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
                const SizedBox(height: 16),
              ],
              // Botones para seleccionar imágenes
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _seleccionarImagenGaleria,
                      icon: const Icon(Icons.photo_library),
                      label: const Text('Desde Galería'),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _seleccionarImagenCamara,
                      icon: const Icon(Icons.camera_alt),
                      label: Text(
                        _isDesktop() ? 'Seleccionar Archivo' : 'Tomar Foto',
                      ),
                    ),
                  ),
                ],
              ),
              if (_imagenesSeleccionadas.isNotEmpty ||
                  _imagenesExistentes.isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  '${_imagenesExistentes.length} existente(s), ${_imagenesSeleccionadas.length} nueva(s)',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ],
              const SizedBox(height: 24),
              const Text(
                'Configuración',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _capacidadController,
                decoration: const InputDecoration(
                  labelText: 'Capacidad máxima',
                  border: OutlineInputBorder(),
                  hintText: 'Ej: 1,000 o 1000',
                ),
                keyboardType: const TextInputType.numberWithOptions(
                  decimal: false,
                  signed: false,
                ),
                inputFormatters: [
                  _NumericWithSeparatorFormatter(),
                  LengthLimitingTextInputFormatter(15),
                ],
                validator: (value) {
                  if (value != null && value.trim().isNotEmpty) {
                    final numeroTexto = value.trim().replaceAll(
                      RegExp(r'[,.]'),
                      '',
                    );
                    final numero = int.tryParse(numeroTexto);
                    if (numero == null) {
                      return 'Ingrese un número válido';
                    }
                    if (numero <= 0) {
                      return 'La capacidad debe ser mayor a 0';
                    }
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<String>(
                value: _estado,
                decoration: const InputDecoration(
                  labelText: 'Estado',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(value: 'borrador', child: Text('Borrador')),
                  DropdownMenuItem(
                    value: 'publicado',
                    child: Text('Publicado'),
                  ),
                  DropdownMenuItem(
                    value: 'cancelado',
                    child: Text('Cancelado'),
                  ),
                ],
                onChanged: (value) {
                  if (value != null) {
                    setState(() {
                      _estado = value;
                    });
                  }
                },
              ),
              const SizedBox(height: 16),
              SwitchListTile(
                title: const Text('Inscripción abierta'),
                subtitle: const Text('Permitir que los usuarios se inscriban'),
                value: _inscripcionAbierta,
                onChanged: (value) {
                  setState(() {
                    _inscripcionAbierta = value;
                  });
                },
              ),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _actualizarEvento,
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    backgroundColor: const Color(0xFF00A36C),
                  ),
                  child:
                      _isLoading
                          ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.white,
                              ),
                            ),
                          )
                          : const Text(
                            'Actualizar Evento',
                            style: TextStyle(fontSize: 16),
                          ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Formatter personalizado que permite números con separadores de miles
class _NumericWithSeparatorFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    if (newValue.text.isEmpty) {
      return newValue;
    }

    final regex = RegExp(r'^[0-9,.]*$');

    if (!regex.hasMatch(newValue.text)) {
      return oldValue;
    }

    final text = newValue.text;

    if (text.contains(',') && text.contains('.')) {
      return oldValue;
    }

    if (text.startsWith(',') || text.startsWith('.')) {
      return oldValue;
    }

    if (text.contains(',,') ||
        text.contains('..') ||
        text.contains(',.') ||
        text.contains('.,')) {
      return oldValue;
    }

    return newValue;
  }
}
