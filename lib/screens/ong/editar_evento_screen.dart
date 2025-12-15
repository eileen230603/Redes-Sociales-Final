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
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../services/auth_helper.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/evento.dart';
import '../../models/tipo_evento.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';
import '../../widgets/organisms/skeleton_loader.dart';
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
    _initializeData();
  }

  Future<void> _initializeData() async {
    // Cargar ONG ID primero
    await _loadOngId();
    // Luego cargar el resto de datos en paralelo
    _loadEvento();
    _loadEmpresasDisponibles();
    _loadInvitadosDisponibles();
    _loadTiposEvento();
  }

  Future<void> _loadOngId() async {
    final ongId = await AuthHelper.getOngIdWithRetry();
    if (mounted) {
      setState(() {
        _ongId = ongId;
      });
    }
  }

  Future<void> _loadEvento() async {
    setState(() {
      _isLoadingEvento = true;
      _error = null;
    });

    try {
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
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = 'Error al cargar evento: ${e.toString()}';
        _isLoadingEvento = false;
      });
    }
  }

  // Helper para parsear ID que puede venir como int o String
  int? _parseId(dynamic value) {
    if (value == null) return null;
    if (value is int) return value;
    if (value is String) {
      final parsed = int.tryParse(value);
      return parsed;
    }
    if (value is num) return value.toInt();
    return null;
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
                .map((p) {
                  if (p is int) return p;
                  if (p is Map && p['id'] != null) {
                    return _parseId(p['id']);
                  }
                  return _parseId(p);
                })
                .whereType<int>()
                .toSet();
      }

      if (evento.invitados != null && evento.invitados is List) {
        final invitados = evento.invitados as List;
        _invitadosSeleccionados =
            invitados
                .map((i) {
                  if (i is int) return i;
                  if (i is Map && i['id'] != null) {
                    return _parseId(i['id']);
                  }
                  return _parseId(i);
                })
                .whereType<int>()
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
      
      if (!mounted) return;

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
      
      if (!mounted) return;
      
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
      firstDate: DateTime(2020), // Permitir fechas pasadas para edición
      lastDate: DateTime.now().add(const Duration(days: 730)), // 2 años futuro
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
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 730)),
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

    final lastDate = _fechaInicio!.add(const Duration(days: 1));
    var initialDate = _fechaLimiteInscripcion ?? DateTime.now();

    // Validar que initialDate esté dentro del rango permitido
    if (initialDate.isAfter(lastDate)) {
      initialDate = lastDate;
    }
    if (initialDate.isBefore(DateTime(2020))) {
      initialDate = DateTime(2020);
    }

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: DateTime(2020),
      lastDate: lastDate,
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
              backgroundColor: AppColors.warning,
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
          backgroundColor: AppColors.error,
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
              backgroundColor: AppColors.warning,
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
          backgroundColor: AppColors.error,
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

    // Asegurar que el ONG ID esté cargado antes de actualizar
    if (_ongId == null) {
      setState(() {
        _isLoading = true;
      });
      final ongId = await AuthHelper.getOngIdWithRetry();
      if (!mounted) return;
      setState(() {
        _ongId = ongId;
        _isLoading = false;
      });

        if (_ongId == null) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'Error: No se pudo identificar la ONG. Por favor, cierra sesión y vuelve a iniciar sesión.',
              ),
              backgroundColor: AppColors.error,
            ),
          );
          return;
        }
      }

    if (_fechaInicio == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona la fecha de inicio'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    if (_tipoEventoSeleccionado == null || _tipoEventoSeleccionado!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona un tipo de evento'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    final descripcionTexto = _descripcionController.text.trim();
    if (descripcionTexto.isNotEmpty && descripcionTexto.length < 10) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('La descripción debe tener al menos 10 caracteres'),
          backgroundColor: AppColors.error,
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
      'patrocinadores': _patrocinadoresSeleccionados.toList(),
      'invitados': _invitadosSeleccionados.toList(),
      // Incluir imágenes existentes que no se eliminaron
      if (_imagenesExistentes.isNotEmpty)
        'imagenes_existentes': _imagenesExistentes,
    };

    try {
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
            backgroundColor: AppColors.success,
            duration: AppDuration.slow,
          ),
        );
        Navigator.pop(context, true);
      } else {
        final errorMessage =
            result['error'] as String? ?? 'Error al actualizar evento';
        final errors = result['errors'];

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(errorMessage, style: AppTypography.bodyMedium),
            backgroundColor: AppColors.error,
            duration: AppDuration.slow,
            action: SnackBarAction(
              label: 'Ver detalles',
              textColor: AppColors.textOnPrimary,
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
                              style: AppTypography.bodySmall,
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
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error inesperado: ${e.toString()}'),
          backgroundColor: AppColors.error,
          duration: AppDuration.slow,
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
        body: SkeletonLoader.eventDetail(),
      );
    }

    if (_error != null) {
      return Scaffold(
        drawer: const AppDrawer(),
        appBar: AppBar(title: const Text('Editar Evento')),
        body: ErrorView.serverError(onRetry: _loadEvento, errorDetails: _error),
      );
    }

    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos'),
      appBar: AppBar(title: const Text('Editar Evento')),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(AppSpacing.md),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Información Básica', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _tituloController,
                decoration: const InputDecoration(labelText: 'Título del evento *'),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'El título es requerido';
                  }
                  return null;
                },
              ),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _descripcionController,
                decoration: const InputDecoration(
                  labelText: 'Descripción',
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
              const SizedBox(height: AppSpacing.md),
              if (_isLoadingTiposEvento)
                Padding(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: LoadingState.card(),
                )
              else
                DropdownButtonFormField<String>(
                  value: _tipoEventoSeleccionado,
                  decoration: const InputDecoration(
                    labelText: 'Tipo de evento *',
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
              const SizedBox(height: AppSpacing.lg),
              Text('Fechas', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.md),
              AppCard(
                padding: EdgeInsets.zero,
                child: AppListTile(
                  leading: AppIcon.sm(Icons.calendar_today),
                  title: 'Fecha de inicio *',
                  subtitle:
                      _fechaInicio == null
                          ? 'Seleccionar fecha'
                          : _formatDateTime(_fechaInicio!),
                  trailing: AppIcon.sm(Icons.chevron_right),
                  onTap: _selectFechaInicio,
                ),
              ),
              const SizedBox(height: AppSpacing.sm),
              AppCard(
                padding: EdgeInsets.zero,
                child: AppListTile(
                  leading: AppIcon.sm(Icons.calendar_today),
                  title: 'Fecha de finalización',
                  subtitle:
                      _fechaFin == null
                          ? 'Seleccionar fecha (opcional)'
                          : _formatDateTime(_fechaFin!),
                  trailing: AppIcon.sm(Icons.chevron_right),
                  onTap: _selectFechaFin,
                ),
              ),
              const SizedBox(height: AppSpacing.sm),
              AppCard(
                padding: EdgeInsets.zero,
                child: AppListTile(
                  leading: AppIcon.sm(Icons.calendar_today),
                  title: 'Límite para inscribirse',
                  subtitle:
                      _fechaLimiteInscripcion == null
                          ? 'Seleccionar fecha (opcional)'
                          : _formatDateTime(_fechaLimiteInscripcion!),
                  trailing: AppIcon.sm(Icons.chevron_right),
                  onTap: _selectFechaLimiteInscripcion,
                ),
              ),
              const SizedBox(height: AppSpacing.lg),
              Text('Ubicación', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _ciudadController,
                decoration: const InputDecoration(labelText: 'Ciudad'),
              ),
              const SizedBox(height: AppSpacing.md),
              Text(
                'Selecciona la ubicación en el mapa',
                style: AppTypography.titleSmall,
              ),
              const SizedBox(height: AppSpacing.xs),
              SizedBox(
                height: 300,
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(AppRadius.card),
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
                              width: 48,
                              height: 48,
                              child: AppIcon.xl(
                                Icons.location_on,
                                color: AppColors.error,
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _direccionController,
                decoration: InputDecoration(
                  labelText: 'Dirección seleccionada',
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
                const SizedBox(height: AppSpacing.xs),
                Text(
                  'Coordenadas: ${_selectedLocation!.latitude.toStringAsFixed(7)}, ${_selectedLocation!.longitude.toStringAsFixed(7)}',
                  style: AppTypography.bodySmall.copyWith(
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
              const SizedBox(height: AppSpacing.lg),
              Text(
                'Empresas Colaboradoras (Opcional)',
                style: AppTypography.headlineSmall,
              ),
              const SizedBox(height: AppSpacing.xs),
              Text(
                'Puedes seleccionar empresas que patrocinarán este evento',
                style: AppTypography.bodySecondary,
              ),
              const SizedBox(height: AppSpacing.md),
              if (_isLoadingEmpresas)
                SizedBox(height: 180, child: LoadingState.list())
              else if (_empresasDisponibles.isEmpty)
                const EmptyState(
                  icon: Icons.business_outlined,
                  title: 'Sin empresas disponibles',
                  message: 'No se encontraron empresas para seleccionar.',
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
              const SizedBox(height: AppSpacing.lg),
              Text('Invitados (Opcional)', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.xs),
              Text(
                'Puedes seleccionar invitados especiales para este evento',
                style: AppTypography.bodySecondary,
              ),
              const SizedBox(height: AppSpacing.md),
              if (_isLoadingInvitados)
                SizedBox(height: 180, child: LoadingState.list())
              else if (_invitadosDisponibles.isEmpty)
                const EmptyState(
                  icon: Icons.person_outline,
                  title: 'Sin invitados disponibles',
                  message: 'No se encontraron invitados para seleccionar.',
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
              const SizedBox(height: AppSpacing.lg),
              Text('Imágenes del Evento', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.md),
              // Imágenes existentes
              if (_imagenesExistentes.isNotEmpty) ...[
                Text('Imágenes existentes', style: AppTypography.titleMedium),
                const SizedBox(height: AppSpacing.xs),
                SizedBox(
                  height: 120,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesExistentes.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(right: AppSpacing.sm),
                            child: SizedBox(
                              width: 120,
                              height: 120,
                              child: AppCard(
                                padding: EdgeInsets.zero,
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(AppRadius.card),
                                  child: CachedNetworkImage(
                                    imageUrl: _imagenesExistentes[index],
                                    fit: BoxFit.cover,
                                    placeholder: (context, url) {
                                      return Container(color: AppColors.grey100);
                                    },
                                    errorWidget: (context, url, error) {
                                      return Container(
                                        color: AppColors.grey100,
                                        child: Center(
                                          child: AppIcon.lg(
                                            Icons.image_not_supported,
                                            color: AppColors.textTertiary,
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                ),
                              ),
                            ),
                          ),
                          Positioned(
                            top: 4,
                            right: 4,
                            child: InkWell(
                              onTap: () => _eliminarImagenExistente(index),
                              borderRadius: BorderRadius.circular(AppRadius.full),
                              child: Container(
                                width: 28,
                                height: 28,
                                decoration: BoxDecoration(
                                  color: AppColors.error,
                                  shape: BoxShape.circle,
                                  boxShadow: AppElevation.cardShadow,
                                ),
                                child: const Center(
                                  child: Icon(
                                    Icons.close,
                                    size: AppSizes.iconXs,
                                    color: AppColors.white,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
              ],
              // Vista previa de imágenes seleccionadas nuevas
              if (_imagenesSeleccionadas.isNotEmpty) ...[
                Text('Nuevas imágenes', style: AppTypography.titleMedium),
                const SizedBox(height: AppSpacing.xs),
                SizedBox(
                  height: 120,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _imagenesSeleccionadas.length,
                    itemBuilder: (context, index) {
                      return Stack(
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(right: AppSpacing.sm),
                            child: SizedBox(
                              width: 120,
                              height: 120,
                              child: AppCard(
                                padding: EdgeInsets.zero,
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(AppRadius.card),
                                  child: FutureBuilder<Uint8List>(
                                    future: _imagenesSeleccionadas[index].readAsBytes(),
                                    builder: (context, snapshot) {
                                      if (snapshot.hasData) {
                                        return Image.memory(
                                          snapshot.data!,
                                          fit: BoxFit.cover,
                                        );
                                      }
                                      return Container(color: AppColors.grey100);
                                    },
                                  ),
                                ),
                              ),
                            ),
                          ),
                          Positioned(
                            top: 4,
                            right: 12,
                            child: InkWell(
                              onTap: () => _eliminarImagenSeleccionada(index),
                              borderRadius: BorderRadius.circular(AppRadius.full),
                              child: Container(
                                width: 24,
                                height: 24,
                                decoration: BoxDecoration(
                                  color: AppColors.error,
                                  shape: BoxShape.circle,
                                  boxShadow: AppElevation.cardShadow,
                                ),
                                child: const Center(
                                  child: Icon(
                                    Icons.close,
                                    size: AppSizes.iconXs,
                                    color: AppColors.white,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
              ],
              // Botones para seleccionar imágenes
              Row(
                children: [
                  Expanded(
                    child: AppButton.outlined(
                      label: 'Desde Galería',
                      icon: Icons.photo_library_outlined,
                      onPressed: _seleccionarImagenGaleria,
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: AppButton.outlined(
                      label: _isDesktop() ? 'Seleccionar Archivo' : 'Tomar Foto',
                      icon: Icons.camera_alt_outlined,
                      onPressed: _seleccionarImagenCamara,
                    ),
                  ),
                ],
              ),
              if (_imagenesSeleccionadas.isNotEmpty ||
                  _imagenesExistentes.isNotEmpty) ...[
                const SizedBox(height: AppSpacing.xs),
                Text(
                  '${_imagenesExistentes.length} existente(s), ${_imagenesSeleccionadas.length} nueva(s)',
                  style: AppTypography.bodySmall.copyWith(
                    color: AppColors.textSecondary,
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ],
              const SizedBox(height: AppSpacing.lg),
              Text('Configuración', style: AppTypography.headlineSmall),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _capacidadController,
                decoration: const InputDecoration(
                  labelText: 'Capacidad máxima',
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
              const SizedBox(height: AppSpacing.md),
              DropdownButtonFormField<String>(
                value: _estado,
                decoration: const InputDecoration(
                  labelText: 'Estado',
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
              const SizedBox(height: AppSpacing.md),
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
              const SizedBox(height: AppSpacing.xl),
              SizedBox(
                width: double.infinity,
                child: AppButton.primary(
                  label: 'Actualizar Evento',
                  icon: Icons.save_outlined,
                  onPressed: _isLoading ? null : _actualizarEvento,
                  isLoading: _isLoading,
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
