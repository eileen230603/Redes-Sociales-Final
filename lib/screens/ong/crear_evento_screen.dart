import 'dart:typed_data';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart'; // Agregar este import para FilteringTextInputFormatter
import 'package:flutter/foundation.dart' show kIsWeb, defaultTargetPlatform;
import 'package:flutter/foundation.dart' show TargetPlatform;
import 'package:image_picker/image_picker.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:http/http.dart' as http;
import '../../config/design_tokens.dart';
import '../../config/typography_system.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../services/auth_helper.dart';
import '../../services/parametrizacion_service.dart';
import '../../models/tipo_evento.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/atoms/app_button.dart';
import '../../widgets/atoms/app_icon.dart';
import '../../widgets/molecules/app_card.dart';
import '../../widgets/molecules/app_list_tile.dart';
import '../../widgets/molecules/empty_state.dart';
import '../../widgets/molecules/loading_state.dart';
import '../../widgets/organisms/error_view.dart';

class CrearEventoScreen extends StatefulWidget {
  const CrearEventoScreen({super.key});

  @override
  State<CrearEventoScreen> createState() => _CrearEventoScreenState();
}

class _CrearEventoScreenState extends State<CrearEventoScreen> {
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
  final ImagePicker _imagePicker = ImagePicker();
  List<XFile> _imagenesSeleccionadas = [];

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

  // Tipos de evento por defecto (iguales a Laravel - valores exactos del select)
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
    _loadEmpresasDisponibles();
    _loadInvitadosDisponibles();
    _loadTiposEvento(); // Agregar esta línea
  }

  Future<void> _loadOngId() async {
    // Usar AuthHelper para obtener ONG ID con validación y reintento
    final ongId = await AuthHelper.getOngIdWithRetry();
    setState(() {
      _ongId = ongId;
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

  Future<void> _loadTiposEvento() async {
    setState(() {
      _isLoadingTiposEvento = true;
    });

    try {
      print('🔄 Cargando tipos de evento desde API...');
      final result = await ParametrizacionService.getTiposEvento(activo: true);

      if (!mounted) return;

      print('📥 Respuesta recibida: success=${result['success']}');

      setState(() {
        _isLoadingTiposEvento = false;
        if (result['success'] == true) {
          final tiposCargados = result['tipos'] as List<TipoEvento>;
          if (tiposCargados.isNotEmpty) {
            _tiposEvento = tiposCargados;
            print(
              '✅ Tipos de evento cargados desde API: ${_tiposEvento.length}',
            );
            for (var tipo in _tiposEvento) {
              print('   - ${tipo.nombre}');
            }
          } else {
            // Si no hay tipos en la API, usar los por defecto
            print('⚠️ No hay tipos en la API, usando tipos por defecto');
            _usarTiposPorDefecto();
          }
        } else {
          // Si hay error (tabla no existe, etc.), usar tipos por defecto
          print('❌ Error al cargar tipos de evento: ${result['error']}');
          print('⚠️ Usando tipos por defecto (iguales a Laravel)');
          _usarTiposPorDefecto();
        }
      });
    } catch (e, stackTrace) {
      if (!mounted) return;
      print('❌ Excepción al cargar tipos de evento: $e');
      print('📚 Stack trace: $stackTrace');
      print('⚠️ Usando tipos por defecto debido a error');
      setState(() {
        _isLoadingTiposEvento = false;
        _usarTiposPorDefecto();
      });
    }
  }

  // Método helper para usar tipos por defecto (iguales a Laravel)
  void _usarTiposPorDefecto() {
    _tiposEvento =
        _tiposEventoPorDefecto.asMap().entries.map((entry) {
          final index = entry.key;
          final nombre = entry.value;
          // Capitalizar primera letra para mostrar
          final nombreMostrar = nombre[0].toUpperCase() + nombre.substring(1);
          return TipoEvento(
            id: index + 1,
            codigo: nombre,
            nombre: nombreMostrar, // Mostrar con primera letra mayúscula
            orden: index,
            activo: true,
          );
        }).toList();
    print(
      '✅ Tipos por defecto cargados (iguales a Laravel): ${_tiposEvento.length}',
    );
    for (var tipo in _tiposEvento) {
      print('   - ${tipo.nombre} (código: ${tipo.codigo})');
    }
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

        // Obtener ciudad de diferentes campos posibles
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
          // Solo actualizar ciudad si el campo está vacío (no sobrescribir si ya tiene valor)
          if (ciudad.isNotEmpty && _ciudadController.text.trim().isEmpty) {
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
          backgroundColor: AppColors.error,
        ),
      );
      return;
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

    // Validar tipo de evento
    if (_tipoEventoSeleccionado == null || _tipoEventoSeleccionado!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona un tipo de evento'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    // Validar descripción si se llenó
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
      'tipo_evento': _tipoEventoSeleccionado, // Usar el valor seleccionado
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
      // Solo incluir patrocinadores si hay selección
      if (_patrocinadoresSeleccionados.isNotEmpty)
        'patrocinadores': _patrocinadoresSeleccionados.toList(),
      // Solo incluir invitados si hay selección
      if (_invitadosSeleccionados.isNotEmpty)
        'invitados': _invitadosSeleccionados.toList(),
      // NO incluir auspiciadores - Laravel lo manejará como array vacío por defecto
    };

    print('📤 Datos del evento a enviar:');
    print('   Título: ${eventoData['titulo']}');
    print('   Tipo evento: ${eventoData['tipo_evento']}');
    print('   Fecha inicio: ${eventoData['fecha_inicio']}');
    print('   Estado: ${eventoData['estado']}');
    print('   Inscripción abierta: ${eventoData['inscripcion_abierta']}');
    print(
      '   Patrocinadores: ${eventoData.containsKey('patrocinadores') ? eventoData['patrocinadores'] : 'No incluido'}',
    );
    print(
      '   Invitados: ${eventoData.containsKey('invitados') ? eventoData['invitados'] : 'No incluido'}',
    );
    print(
      '   Auspiciadores: No incluido (Laravel usará array vacío por defecto)',
    );

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
          backgroundColor: AppColors.success,
          duration: AppDuration.slow,
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

      // Si hay errores de validación, mostrarlos en consola
      if (errors != null) {
        print('❌ Errores de validación: $errors');
      }
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
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/ong/eventos/crear'),
      appBar: AppBar(title: const Text('Crear Evento')),
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
                  // Si se llena la descripción, debe tener al menos 10 caracteres
                  if (value != null && value.trim().isNotEmpty) {
                    if (value.trim().length < 10) {
                      return 'La descripción debe tener al menos 10 caracteres';
                    }
                  }
                  return null;
                },
              ),
              const SizedBox(height: AppSpacing.md),
              // Selector de tipo de evento
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
                          value:
                              tipo.codigo, // Usar el código (valor real) como value
                          child: Text(
                            tipo.nombre,
                          ), // Mostrar el nombre formateado
                        );
                      }).toList(),
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _tipoEventoSeleccionado = value; // Guardar el código
                      });
                      print('✅ Tipo de evento seleccionado: $value');
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
                // Campo editable - se puede llenar manualmente o desde el mapa si está vacío
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
                              onTap: () {
                                setState(() {
                                  _imagenesSeleccionadas.removeAt(index);
                                });
                              },
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
              if (_imagenesSeleccionadas.isNotEmpty) ...[
                const SizedBox(height: AppSpacing.xs),
                Text(
                  '${_imagenesSeleccionadas.length} imagen(es) seleccionada(s)',
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
                  // Formatter que permite números con separadores de miles (coma o punto)
                  _NumericWithSeparatorFormatter(),
                  LengthLimitingTextInputFormatter(
                    15,
                  ), // Aumentar límite para números grandes
                ],
                validator: (value) {
                  if (value != null && value.trim().isNotEmpty) {
                    // Remover separadores de miles antes de parsear
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
                  label: 'Crear Evento',
                  icon: Icons.check_circle_outline,
                  onPressed: _isLoading ? null : _crearEvento,
                  isLoading: _isLoading,
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
}

// Formatter personalizado que permite números con separadores de miles
class _NumericWithSeparatorFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    // Si el nuevo valor está vacío, permitirlo
    if (newValue.text.isEmpty) {
      return newValue;
    }

    // Permitir solo dígitos, comas y puntos
    final regex = RegExp(r'^[0-9,.]*$');

    if (!regex.hasMatch(newValue.text)) {
      // Si contiene caracteres no permitidos, devolver el valor anterior
      return oldValue;
    }

    // Validar formato: solo permitir comas o puntos como separadores de miles
    final text = newValue.text;

    // No permitir comas y puntos juntos
    if (text.contains(',') && text.contains('.')) {
      return oldValue;
    }

    // No permitir separadores al inicio
    if (text.startsWith(',') || text.startsWith('.')) {
      return oldValue;
    }

    // No permitir separadores consecutivos
    if (text.contains(',,') ||
        text.contains('..') ||
        text.contains(',.') ||
        text.contains('.,')) {
      return oldValue;
    }

    return newValue;
  }
}
