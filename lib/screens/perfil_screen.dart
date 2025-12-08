import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';
import '../widgets/app_drawer.dart';
import '../utils/image_helper.dart';
import 'package:cached_network_image/cached_network_image.dart';

class PerfilScreen extends StatefulWidget {
  const PerfilScreen({super.key});

  @override
  State<PerfilScreen> createState() => _PerfilScreenState();
}

class _PerfilScreenState extends State<PerfilScreen> {
  Map<String, dynamic>? _perfilData;
  bool _isLoading = true;
  bool _isEditing = false;
  bool _isSaving = false;
  String? _error;
  XFile? _fotoPerfilSeleccionada;
  String? _fotoPerfilUrl;

  // Controladores para formulario
  final _formKey = GlobalKey<FormState>();
  final _nombreUsuarioController = TextEditingController();
  final _correoController = TextEditingController();
  final _contrasenaActualController = TextEditingController();
  final _nuevaContrasenaController = TextEditingController();
  final _confirmarContrasenaController = TextEditingController();

  // Controladores específicos por tipo
  final _nombreOngController = TextEditingController();
  final _nitOngController = TextEditingController();
  final _telefonoOngController = TextEditingController();
  final _direccionOngController = TextEditingController();
  final _sitioWebOngController = TextEditingController();
  final _descripcionOngController = TextEditingController();

  final _nombreEmpresaController = TextEditingController();
  final _nitEmpresaController = TextEditingController();
  final _telefonoEmpresaController = TextEditingController();
  final _direccionEmpresaController = TextEditingController();
  final _sitioWebEmpresaController = TextEditingController();
  final _descripcionEmpresaController = TextEditingController();

  final _nombresExternoController = TextEditingController();
  final _apellidosExternoController = TextEditingController();
  final _emailExternoController = TextEditingController();
  final _telefonoExternoController = TextEditingController();
  final _descripcionExternoController = TextEditingController();
  DateTime? _fechaNacimientoExterno;

  @override
  void initState() {
    super.initState();
    _loadPerfil();
  }

  @override
  void dispose() {
    _nombreUsuarioController.dispose();
    _correoController.dispose();
    _contrasenaActualController.dispose();
    _nuevaContrasenaController.dispose();
    _confirmarContrasenaController.dispose();
    _nombreOngController.dispose();
    _nitOngController.dispose();
    _telefonoOngController.dispose();
    _direccionOngController.dispose();
    _sitioWebOngController.dispose();
    _descripcionOngController.dispose();
    _nombreEmpresaController.dispose();
    _nitEmpresaController.dispose();
    _telefonoEmpresaController.dispose();
    _direccionEmpresaController.dispose();
    _sitioWebEmpresaController.dispose();
    _descripcionEmpresaController.dispose();
    _nombresExternoController.dispose();
    _apellidosExternoController.dispose();
    _emailExternoController.dispose();
    _telefonoExternoController.dispose();
    _descripcionExternoController.dispose();
    super.dispose();
  }

  Future<void> _loadPerfil() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ApiService.getPerfil();

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _perfilData = result['data'] as Map<String, dynamic>;
        _cargarDatosEnFormulario();
      } else {
        _error = result['error'] as String? ?? 'Error al cargar perfil';
      }
    });
  }

  void _cargarDatosEnFormulario() {
    if (_perfilData == null) return;

    _nombreUsuarioController.text = _perfilData!['nombre_usuario'] ?? '';
    _correoController.text = _perfilData!['correo_electronico'] ?? '';
    _fotoPerfilUrl = _perfilData!['foto_perfil'] as String?;

    final tipoUsuario = _perfilData!['tipo_usuario'] as String?;

    if (tipoUsuario == 'ONG' && _perfilData!.containsKey('ong')) {
      final ong = _perfilData!['ong'] as Map<String, dynamic>;
      _nombreOngController.text = ong['nombre_ong'] ?? '';
      _nitOngController.text = ong['NIT'] ?? '';
      _telefonoOngController.text = ong['telefono'] ?? '';
      _direccionOngController.text = ong['direccion'] ?? '';
      _sitioWebOngController.text = ong['sitio_web'] ?? '';
      _descripcionOngController.text = ong['descripcion'] ?? '';
      _fotoPerfilUrl = ong['foto_perfil'] as String? ?? _fotoPerfilUrl;
    } else if (tipoUsuario == 'Empresa' &&
        _perfilData!.containsKey('empresa')) {
      final empresa = _perfilData!['empresa'] as Map<String, dynamic>;
      _nombreEmpresaController.text = empresa['nombre_empresa'] ?? '';
      _nitEmpresaController.text = empresa['NIT'] ?? '';
      _telefonoEmpresaController.text = empresa['telefono'] ?? '';
      _direccionEmpresaController.text = empresa['direccion'] ?? '';
      _sitioWebEmpresaController.text = empresa['sitio_web'] ?? '';
      _descripcionEmpresaController.text = empresa['descripcion'] ?? '';
      _fotoPerfilUrl = empresa['foto_perfil'] as String? ?? _fotoPerfilUrl;
    } else if (tipoUsuario == 'Integrante externo' &&
        _perfilData!.containsKey('integrante_externo')) {
      final externo =
          _perfilData!['integrante_externo'] as Map<String, dynamic>;
      _nombresExternoController.text = externo['nombres'] ?? '';
      _apellidosExternoController.text = externo['apellidos'] ?? '';
      _emailExternoController.text = externo['email'] ?? '';
      _telefonoExternoController.text = externo['phone_number'] ?? '';
      _descripcionExternoController.text = externo['descripcion'] ?? '';
      if (externo['fecha_nacimiento'] != null) {
        _fechaNacimientoExterno = DateTime.parse(externo['fecha_nacimiento']);
      }
      _fotoPerfilUrl = externo['foto_perfil'] as String? ?? _fotoPerfilUrl;
    }
  }

  Future<void> _seleccionarFoto() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(
      source: ImageSource.gallery,
      maxWidth: 1024,
      maxHeight: 1024,
      imageQuality: 85,
    );

    if (pickedFile != null) {
      setState(() {
        _fotoPerfilSeleccionada = pickedFile;
      });
    }
  }

  Future<void> _guardarPerfil() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isSaving = true;
    });

    final perfilData = <String, dynamic>{
      'nombre_usuario': _nombreUsuarioController.text.trim(),
      'correo_electronico': _correoController.text.trim(),
    };

    // Agregar cambio de contraseña si se proporcionó
    if (_contrasenaActualController.text.isNotEmpty ||
        _nuevaContrasenaController.text.isNotEmpty) {
      perfilData['contrasena_actual'] = _contrasenaActualController.text;
      perfilData['nueva_contrasena'] = _nuevaContrasenaController.text;
    }

    final tipoUsuario = _perfilData!['tipo_usuario'] as String?;

    if (tipoUsuario == 'ONG') {
      perfilData['nombre_ong'] = _nombreOngController.text.trim();
      perfilData['NIT'] = _nitOngController.text.trim();
      perfilData['telefono'] = _telefonoOngController.text.trim();
      perfilData['direccion'] = _direccionOngController.text.trim();
      perfilData['sitio_web'] = _sitioWebOngController.text.trim();
      perfilData['descripcion'] = _descripcionOngController.text.trim();
    } else if (tipoUsuario == 'Empresa') {
      perfilData['nombre_empresa'] = _nombreEmpresaController.text.trim();
      perfilData['NIT'] = _nitEmpresaController.text.trim();
      perfilData['telefono'] = _telefonoEmpresaController.text.trim();
      perfilData['direccion'] = _direccionEmpresaController.text.trim();
      perfilData['sitio_web'] = _sitioWebEmpresaController.text.trim();
      perfilData['descripcion'] = _descripcionEmpresaController.text.trim();
    } else if (tipoUsuario == 'Integrante externo') {
      perfilData['nombres'] = _nombresExternoController.text.trim();
      perfilData['apellidos'] = _apellidosExternoController.text.trim();
      perfilData['email'] = _emailExternoController.text.trim();
      perfilData['phone_number'] = _telefonoExternoController.text.trim();
      perfilData['descripcion'] = _descripcionExternoController.text.trim();
      if (_fechaNacimientoExterno != null) {
        perfilData['fecha_nacimiento'] =
            _fechaNacimientoExterno!.toIso8601String();
      }
    }

    final result = await ApiService.actualizarPerfil(
      perfilData,
      fotoPerfil: _fotoPerfilSeleccionada,
    );

    if (!mounted) return;

    setState(() {
      _isSaving = false;
    });

    if (result['success'] == true) {
      if (result['foto_perfil'] != null) {
        setState(() {
          _fotoPerfilUrl = result['foto_perfil'] as String?;
          _fotoPerfilSeleccionada = null;
        });
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Perfil actualizado exitosamente',
          ),
          backgroundColor: Colors.green,
        ),
      );

      setState(() {
        _isEditing = false;
      });

      // Recargar perfil
      await _loadPerfil();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al actualizar perfil',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/perfil'),
      appBar: AppBar(
        title: const Text('Mi Perfil'),
        actions: [
          if (!_isEditing)
            IconButton(
              icon: const Icon(Icons.edit),
              onPressed: () {
                setState(() {
                  _isEditing = true;
                });
              },
              tooltip: 'Editar',
            )
          else
            IconButton(
              icon: const Icon(Icons.close),
              onPressed: () {
                setState(() {
                  _isEditing = false;
                  _fotoPerfilSeleccionada = null;
                  _contrasenaActualController.clear();
                  _nuevaContrasenaController.clear();
                  _confirmarContrasenaController.clear();
                  _cargarDatosEnFormulario();
                });
              },
              tooltip: 'Cancelar',
            ),
          if (_isEditing)
            IconButton(
              icon:
                  _isSaving
                      ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                      : const Icon(Icons.save),
              onPressed: _isSaving ? null : _guardarPerfil,
              tooltip: 'Guardar',
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
                      onPressed: _loadPerfil,
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              )
              : RefreshIndicator(
                onRefresh: _loadPerfil,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Foto de perfil
                        _buildFotoPerfil(),

                        const SizedBox(height: 24),

                        // Información básica
                        _buildSeccionBasica(),

                        const SizedBox(height: 24),

                        // Información específica según tipo
                        _buildSeccionEspecifica(),

                        const SizedBox(height: 24),

                        // Cambio de contraseña
                        if (_isEditing) _buildSeccionContrasena(),
                      ],
                    ),
                  ),
                ),
              ),
    );
  }

  Widget _buildFotoPerfil() {
    final fotoActual =
        _fotoPerfilSeleccionada != null
            ? _fotoPerfilSeleccionada!.path
            : (_fotoPerfilUrl != null
                ? ImageHelper.buildImageUrl(_fotoPerfilUrl)
                : null);

    return Center(
      child: Stack(
        children: [
          CircleAvatar(
            radius: 60,
            backgroundImage:
                fotoActual != null
                    ? (_fotoPerfilSeleccionada != null
                        ? FileImage(File(fotoActual))
                        : CachedNetworkImageProvider(fotoActual))
                    : null,
            child:
                fotoActual == null
                    ? const Icon(Icons.person, size: 60, color: Colors.white)
                    : null,
            backgroundColor: Colors.blue,
          ),
          if (_isEditing)
            Positioned(
              bottom: 0,
              right: 0,
              child: CircleAvatar(
                radius: 20,
                backgroundColor: Colors.blue,
                child: IconButton(
                  icon: const Icon(
                    Icons.camera_alt,
                    color: Colors.white,
                    size: 20,
                  ),
                  onPressed: _seleccionarFoto,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSeccionBasica() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Información Básica',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nombreUsuarioController,
              decoration: const InputDecoration(
                labelText: 'Nombre de usuario',
                prefixIcon: Icon(Icons.person),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'El nombre de usuario es requerido';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _correoController,
              decoration: const InputDecoration(
                labelText: 'Correo electrónico',
                prefixIcon: Icon(Icons.email),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.emailAddress,
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'El correo electrónico es requerido';
                }
                if (!value.contains('@')) {
                  return 'Ingresa un correo válido';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              initialValue: _perfilData?['tipo_usuario'] ?? '',
              decoration: const InputDecoration(
                labelText: 'Tipo de usuario',
                prefixIcon: Icon(Icons.badge),
                border: OutlineInputBorder(),
              ),
              enabled: false,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSeccionEspecifica() {
    final tipoUsuario = _perfilData?['tipo_usuario'] as String?;

    if (tipoUsuario == 'ONG') {
      return _buildSeccionOng();
    } else if (tipoUsuario == 'Empresa') {
      return _buildSeccionEmpresa();
    } else if (tipoUsuario == 'Integrante externo') {
      return _buildSeccionExterno();
    }

    return const SizedBox.shrink();
  }

  Widget _buildSeccionOng() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Información de la ONG',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nombreOngController,
              decoration: const InputDecoration(
                labelText: 'Nombre de la ONG',
                prefixIcon: Icon(Icons.business),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nitOngController,
              decoration: const InputDecoration(
                labelText: 'NIT',
                prefixIcon: Icon(Icons.numbers),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _telefonoOngController,
              decoration: const InputDecoration(
                labelText: 'Teléfono',
                prefixIcon: Icon(Icons.phone),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _direccionOngController,
              decoration: const InputDecoration(
                labelText: 'Dirección',
                prefixIcon: Icon(Icons.location_on),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              maxLines: 2,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _sitioWebOngController,
              decoration: const InputDecoration(
                labelText: 'Sitio web',
                prefixIcon: Icon(Icons.language),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.url,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _descripcionOngController,
              decoration: const InputDecoration(
                labelText: 'Descripción',
                prefixIcon: Icon(Icons.description),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              maxLines: 4,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSeccionEmpresa() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Información de la Empresa',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nombreEmpresaController,
              decoration: const InputDecoration(
                labelText: 'Nombre de la empresa',
                prefixIcon: Icon(Icons.business),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nitEmpresaController,
              decoration: const InputDecoration(
                labelText: 'NIT',
                prefixIcon: Icon(Icons.numbers),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _telefonoEmpresaController,
              decoration: const InputDecoration(
                labelText: 'Teléfono',
                prefixIcon: Icon(Icons.phone),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _direccionEmpresaController,
              decoration: const InputDecoration(
                labelText: 'Dirección',
                prefixIcon: Icon(Icons.location_on),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              maxLines: 2,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _sitioWebEmpresaController,
              decoration: const InputDecoration(
                labelText: 'Sitio web',
                prefixIcon: Icon(Icons.language),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.url,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _descripcionEmpresaController,
              decoration: const InputDecoration(
                labelText: 'Descripción',
                prefixIcon: Icon(Icons.description),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              maxLines: 4,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSeccionExterno() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Información Personal',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nombresExternoController,
              decoration: const InputDecoration(
                labelText: 'Nombres',
                prefixIcon: Icon(Icons.person),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _apellidosExternoController,
              decoration: const InputDecoration(
                labelText: 'Apellidos',
                prefixIcon: Icon(Icons.person_outline),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _emailExternoController,
              decoration: const InputDecoration(
                labelText: 'Email',
                prefixIcon: Icon(Icons.email),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.emailAddress,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _telefonoExternoController,
              decoration: const InputDecoration(
                labelText: 'Teléfono',
                prefixIcon: Icon(Icons.phone),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: _isEditing ? _seleccionarFechaNacimiento : null,
              child: InputDecorator(
                decoration: const InputDecoration(
                  labelText: 'Fecha de nacimiento',
                  prefixIcon: Icon(Icons.calendar_today),
                  border: OutlineInputBorder(),
                ),
                child: Text(
                  _fechaNacimientoExterno != null
                      ? '${_fechaNacimientoExterno!.day}/${_fechaNacimientoExterno!.month}/${_fechaNacimientoExterno!.year}'
                      : 'Seleccionar fecha',
                  style: TextStyle(
                    color: _isEditing ? Colors.black87 : Colors.grey[600],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _descripcionExternoController,
              decoration: const InputDecoration(
                labelText: 'Descripción',
                prefixIcon: Icon(Icons.description),
                border: OutlineInputBorder(),
              ),
              enabled: _isEditing,
              maxLines: 4,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSeccionContrasena() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Cambiar Contraseña',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _contrasenaActualController,
              decoration: const InputDecoration(
                labelText: 'Contraseña actual',
                prefixIcon: Icon(Icons.lock),
                border: OutlineInputBorder(),
              ),
              obscureText: true,
              validator: (value) {
                if (_nuevaContrasenaController.text.isNotEmpty &&
                    (value == null || value.isEmpty)) {
                  return 'La contraseña actual es requerida';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nuevaContrasenaController,
              decoration: const InputDecoration(
                labelText: 'Nueva contraseña',
                prefixIcon: Icon(Icons.lock_outline),
                border: OutlineInputBorder(),
              ),
              obscureText: true,
              validator: (value) {
                if (_contrasenaActualController.text.isNotEmpty &&
                    (value == null || value.length < 6)) {
                  return 'La nueva contraseña debe tener al menos 6 caracteres';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _confirmarContrasenaController,
              decoration: const InputDecoration(
                labelText: 'Confirmar nueva contraseña',
                prefixIcon: Icon(Icons.lock_outline),
                border: OutlineInputBorder(),
              ),
              obscureText: true,
              validator: (value) {
                if (_nuevaContrasenaController.text.isNotEmpty &&
                    value != _nuevaContrasenaController.text) {
                  return 'Las contraseñas no coinciden';
                }
                return null;
              },
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _seleccionarFechaNacimiento() async {
    final fecha = await showDatePicker(
      context: context,
      initialDate:
          _fechaNacimientoExterno ??
          DateTime.now().subtract(const Duration(days: 365 * 18)),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );

    if (fecha != null) {
      setState(() {
        _fechaNacimientoExterno = fecha;
      });
    }
  }
}
