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
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../widgets/app_drawer.dart';
import '../../models/mega_evento.dart';
import 'package:cached_network_image/cached_network_image.dart';

class EditarMegaEventoScreen extends StatefulWidget {
  final int megaEventoId;

  const EditarMegaEventoScreen({super.key, required this.megaEventoId});

  @override
  State<EditarMegaEventoScreen> createState() => _EditarMegaEventoScreenState();
}

class _EditarMegaEventoScreenState extends State<EditarMegaEventoScreen> {
  final _formKey = GlobalKey<FormState>();
  final _tituloController = TextEditingController();
  final _descripcionController = TextEditingController();
  final _ubicacionController = TextEditingController();
  final _capacidadController = TextEditingController();

  // Mapa
  final MapController _mapController = MapController();
  LatLng? _selectedLocation;

  DateTime? _fechaInicio;
  DateTime? _fechaFin;
  String _estado = 'planificacion';
  String _categoria = 'social';
  bool _esPublico = true;
  bool _activo = true;
  int? _ongId;
  bool _isLoading = false;
  bool _isLoadingEvento = true;
  String? _error;
  final ImagePicker _imagePicker = ImagePicker();
  List<XFile> _imagenesSeleccionadas = [];
  List<String> _imagenesUrls = [];
  List<String> _imagenesExistentes = []; // URLs de imágenes existentes

  // Categorías disponibles
  static const List<Map<String, dynamic>> _categorias = [
    {'value': 'social', 'label': 'Social'},
    {'value': 'cultural', 'label': 'Cultural'},
    {'value': 'deportivo', 'label': 'Deportivo'},
    {'value': 'educativo', 'label': 'Educativo'},
    {'value': 'benefico', 'label': 'Benéfico'},
    {'value': 'ambiental', 'label': 'Ambiental'},
    {'value': 'otro', 'label': 'Otro'},
  ];

  // Estados disponibles
  static const List<Map<String, dynamic>> _estados = [
    {'value': 'planificacion', 'label': 'Planificación'},
    {'value': 'activo', 'label': 'Activo'},
    {'value': 'en_curso', 'label': 'En Curso'},
    {'value': 'finalizado', 'label': 'Finalizado'},
    {'value': 'cancelado', 'label': 'Cancelado'},
  ];

  @override
  void initState() {
    super.initState();
    _loadOngId();
    _loadMegaEvento();
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _descripcionController.dispose();
    _ubicacionController.dispose();
    _capacidadController.dispose();
    super.dispose();
  }

  Future<void> _loadOngId() async {
    final ongId = await AuthHelper.getOngIdWithRetry();
    setState(() {
      _ongId = ongId;
    });
  }

  Future<void> _loadMegaEvento() async {
    setState(() {
      _isLoadingEvento = true;
      _error = null;
    });

    final result = await ApiService.getMegaEventoDetalle(widget.megaEventoId);

    if (!mounted) return;

    setState(() {
      _isLoadingEvento = false;
      if (result['success'] == true) {
        final megaEvento = MegaEvento.fromJson(
          result['mega_evento'] as Map<String, dynamic>,
        );

        // Cargar datos en los controladores
        _tituloController.text = megaEvento.titulo;
        _descripcionController.text = megaEvento.descripcion ?? '';
        _ubicacionController.text = megaEvento.ubicacion ?? '';
        _capacidadController.text =
            megaEvento.capacidadMaxima?.toString() ?? '';
        _fechaInicio = megaEvento.fechaInicio;
        _fechaFin = megaEvento.fechaFin;
        _estado = megaEvento.estado;
        _categoria = megaEvento.categoria ?? 'social';
        _esPublico = megaEvento.esPublico;
        _activo = megaEvento.activo;

        if (megaEvento.lat != null && megaEvento.lng != null) {
          _selectedLocation = LatLng(megaEvento.lat!, megaEvento.lng!);
          _mapController.move(_selectedLocation!, 13.0);
        }

        // Cargar imágenes existentes
        if (megaEvento.imagenes != null && megaEvento.imagenes!.isNotEmpty) {
          _imagenesExistentes =
              megaEvento.imagenes!
                  .map((img) => img.toString())
                  .where((img) => img.isNotEmpty)
                  .toList();
        }
      } else {
        _error = result['error'] as String? ?? 'Error al cargar mega evento';
      }
    });
  }

  Future<void> _onMapTap(TapPosition position, LatLng point) async {
    setState(() {
      _selectedLocation = point;
    });

    try {
      final response = await http.get(
        Uri.parse(
          'https://nominatim.openstreetmap.org/reverse?lat=${point.latitude}&lon=${point.longitude}&format=json',
        ),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body) as Map<String, dynamic>;
        final displayName = data['display_name'] as String? ?? '';

        setState(() {
          if (_ubicacionController.text.trim().isEmpty) {
            _ubicacionController.text = displayName;
          }
        });
      }
    } catch (e) {
      setState(() {
        final coordenadasTexto =
            'Lat: ${point.latitude.toStringAsFixed(7)}, Lng: ${point.longitude.toStringAsFixed(7)}';
        if (_ubicacionController.text.trim().isEmpty) {
          _ubicacionController.text = coordenadasTexto;
        }
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
        initialTime:
            _fechaInicio != null
                ? TimeOfDay.fromDateTime(_fechaInicio!)
                : TimeOfDay.now(),
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
        initialTime:
            _fechaFin != null
                ? TimeOfDay.fromDateTime(_fechaFin!)
                : TimeOfDay.now(),
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

  Future<void> _seleccionarImagenes() async {
    if (_isDesktop()) {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.image,
        allowMultiple: true,
      );

      if (result != null && result.files.isNotEmpty) {
        final xFiles =
            result.files
                .map((file) {
                  if (file.bytes != null) {
                    return XFile.fromData(
                      file.bytes!,
                      mimeType: file.extension,
                      name: file.name,
                    );
                  }
                  return null;
                })
                .whereType<XFile>()
                .toList();

        setState(() {
          _imagenesSeleccionadas.addAll(xFiles);
        });
      }
    } else {
      final List<XFile> images = await _imagePicker.pickMultiImage();
      if (images.isNotEmpty) {
        setState(() {
          _imagenesSeleccionadas.addAll(images);
        });
      }
    }
  }

  void _eliminarImagen(int index) {
    setState(() {
      _imagenesSeleccionadas.removeAt(index);
    });
  }

  void _eliminarImagenExistente(int index) async {
    final imagenUrl = _imagenesExistentes[index];

    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Eliminar imagen'),
            content: const Text(
              '¿Estás seguro de que deseas eliminar esta imagen?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, true),
                child: const Text(
                  'Eliminar',
                  style: TextStyle(color: Colors.red),
                ),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result = await ApiService.eliminarImagenMegaEvento(
      widget.megaEventoId,
      imagenUrl,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _imagenesExistentes.removeAt(index);
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Imagen eliminada correctamente'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al eliminar imagen',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _agregarImagenUrl() {
    showDialog(
      context: context,
      builder: (context) {
        final urlController = TextEditingController();
        return AlertDialog(
          title: const Text('Agregar imagen por URL'),
          content: TextField(
            controller: urlController,
            decoration: const InputDecoration(
              hintText: 'https://ejemplo.com/imagen.jpg',
              labelText: 'URL de la imagen',
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cancelar'),
            ),
            TextButton(
              onPressed: () {
                final url = urlController.text.trim();
                if (url.isNotEmpty) {
                  setState(() {
                    _imagenesUrls.add(url);
                  });
                  Navigator.pop(context);
                }
              },
              child: const Text('Agregar'),
            ),
          ],
        );
      },
    );
  }

  void _eliminarImagenUrl(int index) {
    setState(() {
      _imagenesUrls.removeAt(index);
    });
  }

  Future<void> _actualizarMegaEvento() async {
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

    if (_fechaInicio == null || _fechaFin == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona las fechas'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_fechaFin!.isBefore(_fechaInicio!)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'La fecha de fin debe ser posterior a la fecha de inicio',
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    // Convertir imágenes a MultipartFile
    List<http.MultipartFile>? imagenesFiles;
    if (_imagenesSeleccionadas.isNotEmpty) {
      imagenesFiles = [];
      for (var imagen in _imagenesSeleccionadas) {
        try {
          final bytes = await imagen.readAsBytes();
          final multipartFile = http.MultipartFile.fromBytes(
            'imagenes[]',
            bytes,
            filename: imagen.name,
          );
          imagenesFiles.add(multipartFile);
        } catch (e) {
          print('Error al leer imagen ${imagen.name}: $e');
        }
      }
    }

    final result = await ApiService.actualizarMegaEvento(
      id: widget.megaEventoId,
      titulo: _tituloController.text.trim(),
      descripcion:
          _descripcionController.text.trim().isEmpty
              ? null
              : _descripcionController.text.trim(),
      fechaInicio: _fechaInicio!,
      fechaFin: _fechaFin!,
      ubicacion:
          _ubicacionController.text.trim().isEmpty
              ? null
              : _ubicacionController.text.trim(),
      lat: _selectedLocation?.latitude,
      lng: _selectedLocation?.longitude,
      categoria: _categoria,
      estado: _estado,
      capacidadMaxima:
          _capacidadController.text.trim().isEmpty
              ? null
              : int.tryParse(_capacidadController.text.trim()),
      esPublico: _esPublico,
      activo: _activo,
      imagenesUrls: _imagenesUrls.isNotEmpty ? _imagenesUrls : null,
      imagenesJson: _imagenesExistentes.isNotEmpty ? _imagenesExistentes : null,
      imagenesFiles: imagenesFiles,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Mega evento actualizado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      Navigator.pop(context, true);
    } else {
      final errorMessage =
          result['error'] as String? ?? 'Error al actualizar mega evento';
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(errorMessage),
          backgroundColor: Colors.red,
          duration: const Duration(seconds: 6),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoadingEvento) {
      return Scaffold(
        appBar: AppBar(title: const Text('Editar Mega Evento')),
        drawer: const AppDrawer(),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    if (_error != null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Editar Mega Evento')),
        drawer: const AppDrawer(),
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
                onPressed: _loadMegaEvento,
                child: const Text('Reintentar'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(title: const Text('Editar Mega Evento')),
      drawer: const AppDrawer(),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Información básica
              const Text(
                'Información Básica',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),

              Row(
                children: [
                  Expanded(
                    flex: 2,
                    child: TextFormField(
                      controller: _tituloController,
                      decoration: const InputDecoration(
                        labelText: 'Título del mega evento *',
                        border: OutlineInputBorder(),
                        hintText: 'Ej: Festival de Verano 2025',
                      ),
                      maxLength: 200,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'El título es requerido';
                        }
                        return null;
                      },
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _categoria,
                      decoration: const InputDecoration(
                        labelText: 'Categoría',
                        border: OutlineInputBorder(),
                      ),
                      items:
                          _categorias.map((cat) {
                            return DropdownMenuItem<String>(
                              value: cat['value'] as String,
                              child: Text(cat['label'] as String),
                            );
                          }).toList(),
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _categoria = value;
                          });
                        }
                      },
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 16),

              TextFormField(
                controller: _descripcionController,
                decoration: const InputDecoration(
                  labelText: 'Descripción',
                  border: OutlineInputBorder(),
                  hintText: 'Describe el mega evento...',
                ),
                maxLines: 4,
              ),

              const SizedBox(height: 24),

              // Fechas
              const Text(
                'Fechas del Evento',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),

              Row(
                children: [
                  Expanded(
                    child: InkWell(
                      onTap: _selectFechaInicio,
                      child: InputDecorator(
                        decoration: const InputDecoration(
                          labelText: 'Fecha y Hora de Inicio *',
                          border: OutlineInputBorder(),
                        ),
                        child: Text(
                          _fechaInicio != null
                              ? _formatDateTime(_fechaInicio!)
                              : 'Seleccionar fecha',
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: InkWell(
                      onTap: _selectFechaFin,
                      child: InputDecorator(
                        decoration: const InputDecoration(
                          labelText: 'Fecha y Hora de Fin *',
                          border: OutlineInputBorder(),
                        ),
                        child: Text(
                          _fechaFin != null
                              ? _formatDateTime(_fechaFin!)
                              : 'Seleccionar fecha',
                        ),
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 24),

              // Ubicación y capacidad
              const Text(
                'Ubicación y Capacidad',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),

              // Mapa
              Container(
                height: 300,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey[300]!),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: FlutterMap(
                    mapController: _mapController,
                    options: MapOptions(
                      initialCenter:
                          _selectedLocation ?? const LatLng(-16.5, -68.15),
                      initialZoom: _selectedLocation != null ? 13.0 : 13.0,
                      onTap: _onMapTap,
                    ),
                    children: [
                      TileLayer(
                        urlTemplate:
                            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                        userAgentPackageName: 'com.example.app',
                      ),
                      if (_selectedLocation != null)
                        MarkerLayer(
                          markers: [
                            Marker(
                              point: _selectedLocation!,
                              width: 80,
                              height: 80,
                              child: const Icon(
                                Icons.location_on,
                                color: Colors.red,
                                size: 48,
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
                controller: _ubicacionController,
                decoration: const InputDecoration(
                  labelText: 'Ubicación (texto)',
                  border: OutlineInputBorder(),
                  hintText: 'Ej: Parque Central, La Paz',
                ),
                maxLength: 500,
              ),

              const SizedBox(height: 16),

              TextFormField(
                controller: _capacidadController,
                decoration: const InputDecoration(
                  labelText: 'Capacidad máxima',
                  border: OutlineInputBorder(),
                  hintText: 'Ej: 1000',
                ),
                keyboardType: TextInputType.number,
                inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              ),

              const SizedBox(height: 24),

              // Configuración
              const Text(
                'Configuración',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),

              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _estado,
                      decoration: const InputDecoration(
                        labelText: 'Estado',
                        border: OutlineInputBorder(),
                      ),
                      items:
                          _estados.map((estado) {
                            return DropdownMenuItem<String>(
                              value: estado['value'] as String,
                              child: Text(estado['label'] as String),
                            );
                          }).toList(),
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _estado = value;
                          });
                        }
                      },
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: DropdownButtonFormField<bool>(
                      value: _esPublico,
                      decoration: const InputDecoration(
                        labelText: 'Visibilidad',
                        border: OutlineInputBorder(),
                      ),
                      items: const [
                        DropdownMenuItem(value: true, child: Text('Público')),
                        DropdownMenuItem(value: false, child: Text('Privado')),
                      ],
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _esPublico = value;
                          });
                        }
                      },
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: DropdownButtonFormField<bool>(
                      value: _activo,
                      decoration: const InputDecoration(
                        labelText: 'Estado de Actividad',
                        border: OutlineInputBorder(),
                      ),
                      items: const [
                        DropdownMenuItem(value: true, child: Text('Activo')),
                        DropdownMenuItem(value: false, child: Text('Inactivo')),
                      ],
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _activo = value;
                          });
                        }
                      },
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 24),

              // Imágenes
              const Text(
                'Imágenes Promocionales',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),

              // Imágenes existentes
              if (_imagenesExistentes.isNotEmpty) ...[
                const Text(
                  'Imágenes existentes:',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  height: 150,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesExistentes.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Container(
                            width: 150,
                            height: 150,
                            margin: const EdgeInsets.only(right: 8),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(
                                color: const Color(0xFF00A36C),
                              ),
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

              // Subir archivos
              ElevatedButton.icon(
                onPressed: _seleccionarImagenes,
                icon: const Icon(Icons.upload_file),
                label: const Text('Subir imágenes desde archivo'),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
              ),

              const SizedBox(height: 8),

              // Agregar por URL
              OutlinedButton.icon(
                onPressed: _agregarImagenUrl,
                icon: const Icon(Icons.link),
                label: const Text('Agregar imagen por URL'),
                style: OutlinedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
              ),

              const SizedBox(height: 16),

              // Preview de imágenes desde archivo
              if (_imagenesSeleccionadas.isNotEmpty) ...[
                const Text(
                  'Imágenes seleccionadas:',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  height: 150,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesSeleccionadas.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Container(
                            width: 150,
                            height: 150,
                            margin: const EdgeInsets.only(right: 8),
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
                            right: 4,
                            child: CircleAvatar(
                              radius: 14,
                              backgroundColor: Colors.red,
                              child: IconButton(
                                icon: const Icon(Icons.close, size: 16),
                                color: Colors.white,
                                onPressed: () => _eliminarImagen(index),
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

              // Preview de imágenes por URL
              if (_imagenesUrls.isNotEmpty) ...[
                const Text(
                  'Imágenes por URL:',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  height: 150,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesUrls.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Container(
                            width: 150,
                            height: 150,
                            margin: const EdgeInsets.only(right: 8),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(
                                color: const Color(0xFF00A36C),
                              ),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: CachedNetworkImage(
                                imageUrl: _imagenesUrls[index],
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
                                onPressed: () => _eliminarImagenUrl(index),
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

              const SizedBox(height: 24),

              // Botones
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed:
                          _isLoading ? null : () => Navigator.pop(context),
                      child: const Text('Cancelar'),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _actualizarMegaEvento,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF00A36C),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                      ),
                      child:
                          _isLoading
                              ? const CircularProgressIndicator(
                                color: Colors.white,
                              )
                              : const Text('Actualizar Mega Evento'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _formatDateTime(DateTime date) {
    final months = [
      'Enero',
      'Febrero',
      'Marzo',
      'Abril',
      'Mayo',
      'Junio',
      'Julio',
      'Agosto',
      'Septiembre',
      'Octubre',
      'Noviembre',
      'Diciembre',
    ];
    return '${date.day} de ${months[date.month - 1]} de ${date.year}, '
        '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
  }
}
