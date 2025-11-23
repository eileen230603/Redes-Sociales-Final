import 'dart:typed_data';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb, defaultTargetPlatform;
import 'package:flutter/foundation.dart' show TargetPlatform;
import 'package:image_picker/image_picker.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:http/http.dart' as http;
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/app_drawer.dart';

class CrearEventoScreen extends StatefulWidget {
  const CrearEventoScreen({super.key});

  @override
  State<CrearEventoScreen> createState() => _CrearEventoScreenState();
}

class _CrearEventoScreenState extends State<CrearEventoScreen> {
  final _formKey = GlobalKey<FormState>();
  final _tituloController = TextEditingController();
  final _descripcionController = TextEditingController();
  final _tipoEventoController = TextEditingController();
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
  final ImagePicker _imagePicker = ImagePicker();
  List<XFile> _imagenesSeleccionadas = [];

  // Patrocinadores e invitados
  List<Map<String, dynamic>> _empresasDisponibles = [];
  List<Map<String, dynamic>> _invitadosDisponibles = [];
  Set<int> _patrocinadoresSeleccionados = {};
  Set<int> _invitadosSeleccionados = {};
  bool _isLoadingEmpresas = false;
  bool _isLoadingInvitados = false;

  @override
  void initState() {
    super.initState();
    _loadOngId();
    _loadEmpresasDisponibles();
    _loadInvitadosDisponibles();
  }

  Future<void> _loadOngId() async {
    final userData = await StorageService.getUserData();
    setState(() {
      _ongId = userData?['entity_id'] as int?;
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
            (result['empresas'] as List)
                .map((e) => e as Map<String, dynamic>)
                .toList();
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
            (result['invitados'] as List)
                .map((e) => e as Map<String, dynamic>)
                .toList();
      }
    });
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
            address?['state'] ??
            '';

        setState(() {
          _direccionSeleccionada = displayName;
          _direccionController.text = displayName;
          if (ciudad.isNotEmpty && _ciudadController.text.isEmpty) {
            _ciudadController.text = ciudad;
          }
        });
      }
    } catch (e) {
      // Si falla la geocodificación, solo usar coordenadas
      final coordenadasTexto =
          'Lat: ${lat.toStringAsFixed(7)}, Lng: ${lng.toStringAsFixed(7)}';
      setState(() {
        _direccionSeleccionada = coordenadasTexto;
        _direccionController.text = coordenadasTexto;
      });
    }
  }

  // Función helper para detectar si estamos en desktop (no web, no móvil)
  bool _isDesktop() {
    if (kIsWeb) return false; // Web usa image_picker
    // Usar defaultTargetPlatform que es más seguro que Platform
    return defaultTargetPlatform == TargetPlatform.windows ||
        defaultTargetPlatform == TargetPlatform.linux ||
        defaultTargetPlatform == TargetPlatform.macOS;
  }

  Future<void> _selectFechaInicio() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
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
      initialDate: _fechaInicio!.add(const Duration(days: 1)),
      firstDate: _fechaInicio!,
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
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
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: _fechaInicio!,
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
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

  Future<void> _crearEvento() async {
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

    setState(() {
      _isLoading = true;
    });

    final eventoData = <String, dynamic>{
      'ong_id': _ongId,
      'titulo': _tituloController.text.trim(),
      'descripcion':
          _descripcionController.text.trim().isEmpty
              ? null
              : _descripcionController.text.trim(),
      'tipo_evento': _tipoEventoController.text.trim(),
      'fecha_inicio': _fechaInicio!.toIso8601String(),
      'fecha_fin': _fechaFin?.toIso8601String(),
      'fecha_limite_inscripcion': _fechaLimiteInscripcion?.toIso8601String(),
      'capacidad_maxima':
          _capacidadController.text.trim().isEmpty
              ? null
              : int.tryParse(_capacidadController.text.trim()),
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
      'patrocinadores': _patrocinadoresSeleccionados.toList(),
      'invitados': _invitadosSeleccionados.toList(),
      'auspiciadores': [],
      // No incluir 'imagenes' aquí, se envía como archivos en multipart
    };

    final result = await ApiService.crearEvento(
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
            result['message'] as String? ?? 'Evento creado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      Navigator.pop(context, true);
    } else {
      final errorMessage =
          result['error'] as String? ?? 'Error al crear evento';
      final errors = result['errors'];

      // Mostrar error detallado
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(errorMessage),
          backgroundColor: Colors.red,
          duration: const Duration(seconds: 5),
        ),
      );

      // Si hay errores de validación, mostrarlos en consola
      if (errors != null) {
        print('Errores de validación: $errors');
      }
    }
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _descripcionController.dispose();
    _tipoEventoController.dispose();
    _ciudadController.dispose();
    _direccionController.dispose();
    _capacidadController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos/crear'),
      appBar: AppBar(title: const Text('Crear Evento')),
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
                ),
                maxLines: 3,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _tipoEventoController,
                decoration: const InputDecoration(
                  labelText: 'Tipo de evento *',
                  border: OutlineInputBorder(),
                  hintText: 'Ej: Cultura, Educación, Salud, Ambiente',
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
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
                'Empresas Colaboradoras',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
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
                'Invitados',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
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
              // Vista previa de imágenes seleccionadas
              if (_imagenesSeleccionadas.isNotEmpty)
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
                                  setState(() {
                                    _imagenesSeleccionadas.removeAt(index);
                                  });
                                },
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
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
              if (_imagenesSeleccionadas.isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  '${_imagenesSeleccionadas.length} imagen(es) seleccionada(s)',
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
                  hintText: 'Número de participantes',
                ),
                keyboardType: TextInputType.number,
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
                  onPressed: _isLoading ? null : _crearEvento,
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child:
                      _isLoading
                          ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                          : const Text('Crear Evento'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _seleccionarImagenGaleria() async {
    try {
      XFile? selectedFile;

      // Usar file_picker solo para desktop (Windows/Linux/macOS)
      if (_isDesktop()) {
        final result = await FilePicker.platform.pickFiles(
          type: FileType.image,
          allowMultiple: false,
        );

        if (result != null && result.files.isNotEmpty) {
          final pickedFile = result.files.single;
          // En desktop, path está disponible - convertir a XFile
          if (pickedFile.path != null && pickedFile.path!.isNotEmpty) {
            selectedFile = XFile(pickedFile.path!);
          }
        }
      } else {
        // Usar image_picker para móviles (Android/iOS) y web
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

      // En desktop no hay cámara, usar file_picker como alternativa
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
        // Usar image_picker para móviles (Android/iOS) y web
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
}
