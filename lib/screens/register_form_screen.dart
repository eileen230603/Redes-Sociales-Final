import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'home_screen.dart';

class RegisterFormScreen extends StatefulWidget {
  final String tipoUsuario;

  const RegisterFormScreen({super.key, required this.tipoUsuario});

  @override
  State<RegisterFormScreen> createState() => _RegisterFormScreenState();
}

class _RegisterFormScreenState extends State<RegisterFormScreen> {
  final _formKey = GlobalKey<FormState>();
  bool _isLoading = false;
  bool _obscurePassword = true;

  // Controllers comunes
  final _nombreUsuarioController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  // Controllers - Integrante externo
  final _nombresController = TextEditingController();
  final _apellidosController = TextEditingController();
  final _telefonoExternoController = TextEditingController();
  final _direccionExternoController = TextEditingController();
  final _fechaNacimientoController = TextEditingController();
  final _descripcionExternoController = TextEditingController();

  // Controllers - ONG
  final _nombreOngController = TextEditingController();
  final _nitOngController = TextEditingController();
  final _telefonoOngController = TextEditingController();
  final _sitioWebOngController = TextEditingController();
  final _direccionOngController = TextEditingController();
  final _descripcionOngController = TextEditingController();

  // Controllers - Empresa
  final _nombreEmpresaController = TextEditingController();
  final _nitEmpresaController = TextEditingController();
  final _telefonoEmpresaController = TextEditingController();
  final _direccionEmpresaController = TextEditingController();
  final _sitioWebEmpresaController = TextEditingController();
  final _descripcionEmpresaController = TextEditingController();

  @override
  void dispose() {
    _nombreUsuarioController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _nombresController.dispose();
    _apellidosController.dispose();
    _telefonoExternoController.dispose();
    _direccionExternoController.dispose();
    _fechaNacimientoController.dispose();
    _descripcionExternoController.dispose();
    _nombreOngController.dispose();
    _nitOngController.dispose();
    _telefonoOngController.dispose();
    _sitioWebOngController.dispose();
    _direccionOngController.dispose();
    _descripcionOngController.dispose();
    _nombreEmpresaController.dispose();
    _nitEmpresaController.dispose();
    _telefonoEmpresaController.dispose();
    _direccionEmpresaController.dispose();
    _sitioWebEmpresaController.dispose();
    _descripcionEmpresaController.dispose();
    super.dispose();
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    // Validar campos específicos según tipo
    if (widget.tipoUsuario == 'Integrante externo') {
      if (_nombresController.text.trim().isEmpty ||
          _apellidosController.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Los campos Nombres y Apellidos son requeridos'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    } else if (widget.tipoUsuario == 'ONG') {
      if (_nombreOngController.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('El nombre de la ONG es requerido'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    } else if (widget.tipoUsuario == 'Empresa') {
      if (_nombreEmpresaController.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('El nombre de la Empresa es requerido'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    setState(() {
      _isLoading = true;
    });

    try {
      String? nombresValue;
      String? apellidosValue;
      String? nombreOngValue;
      String? nombreEmpresaValue;

      if (widget.tipoUsuario == 'Integrante externo') {
        nombresValue = _nombresController.text.trim();
        apellidosValue = _apellidosController.text.trim();
      } else if (widget.tipoUsuario == 'ONG') {
        nombreOngValue = _nombreOngController.text.trim();
      } else if (widget.tipoUsuario == 'Empresa') {
        nombreEmpresaValue = _nombreEmpresaController.text.trim();
      }

      final response = await ApiService.register(
        tipoUsuario: widget.tipoUsuario,
        nombreUsuario: _nombreUsuarioController.text.trim(),
        correoElectronico: _emailController.text.trim(),
        contrasena: _passwordController.text,
        nombres: nombresValue,
        apellidos: apellidosValue,
        nombreOng: nombreOngValue,
        nombreEmpresa: nombreEmpresaValue,
        nit:
            widget.tipoUsuario == 'ONG'
                ? (_nitOngController.text.trim().isEmpty
                    ? null
                    : _nitOngController.text.trim())
                : (widget.tipoUsuario == 'Empresa'
                    ? (_nitEmpresaController.text.trim().isEmpty
                        ? null
                        : _nitEmpresaController.text.trim())
                    : null),
        telefono:
            widget.tipoUsuario == 'Integrante externo'
                ? (_telefonoExternoController.text.trim().isEmpty
                    ? null
                    : _telefonoExternoController.text.trim())
                : (widget.tipoUsuario == 'ONG'
                    ? (_telefonoOngController.text.trim().isEmpty
                        ? null
                        : _telefonoOngController.text.trim())
                    : (_telefonoEmpresaController.text.trim().isEmpty
                        ? null
                        : _telefonoEmpresaController.text.trim())),
        direccion:
            widget.tipoUsuario == 'Integrante externo'
                ? (_direccionExternoController.text.trim().isEmpty
                    ? null
                    : _direccionExternoController.text.trim())
                : (widget.tipoUsuario == 'ONG'
                    ? (_direccionOngController.text.trim().isEmpty
                        ? null
                        : _direccionOngController.text.trim())
                    : (_direccionEmpresaController.text.trim().isEmpty
                        ? null
                        : _direccionEmpresaController.text.trim())),
        sitioWeb:
            widget.tipoUsuario == 'ONG'
                ? (_sitioWebOngController.text.trim().isEmpty
                    ? null
                    : _sitioWebOngController.text.trim())
                : (widget.tipoUsuario == 'Empresa'
                    ? (_sitioWebEmpresaController.text.trim().isEmpty
                        ? null
                        : _sitioWebEmpresaController.text.trim())
                    : null),
        descripcion:
            widget.tipoUsuario == 'Integrante externo'
                ? (_descripcionExternoController.text.trim().isEmpty
                    ? null
                    : _descripcionExternoController.text.trim())
                : (widget.tipoUsuario == 'ONG'
                    ? (_descripcionOngController.text.trim().isEmpty
                        ? null
                        : _descripcionOngController.text.trim())
                    : (_descripcionEmpresaController.text.trim().isEmpty
                        ? null
                        : _descripcionEmpresaController.text.trim())),
        fechaNacimiento:
            _fechaNacimientoController.text.trim().isEmpty
                ? null
                : _fechaNacimientoController.text.trim(),
      );

      if (!mounted) return;

      if (response.success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('¡Registro exitoso!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );

        await Future.delayed(const Duration(milliseconds: 500));
        if (!mounted) return;

        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (context) => const HomeScreen()),
          (route) => false,
        );
      } else {
        final errorMessage = response.error ?? 'Error al registrar';
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(errorMessage, style: const TextStyle(fontSize: 14)),
            backgroundColor: Colors.red,
            duration: const Duration(seconds: 5),
            action: SnackBarAction(
              label: 'Cerrar',
              textColor: Colors.white,
              onPressed: () {},
            ),
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  String _getTitle() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        return 'Registro de ONG';
      case 'Empresa':
        return 'Registro de Empresa';
      case 'Integrante externo':
        return 'Registro de Integrante Externo';
      default:
        return 'Registro';
    }
  }

  // Gradientes de fondo exactos de Laravel (Tailwind colors)
  LinearGradient _getBackgroundGradient() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        // from-green-600 via-teal-500 to-cyan-400
        return const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFF16A34A), // green-600
            Color(0xFF14B8A6), // teal-500
            Color(0xFF22D3EE), // cyan-400
          ],
        );
      case 'Empresa':
        // from-blue-600 via-cyan-500 to-green-400
        return const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFF2563EB), // blue-600
            Color(0xFF06B6D4), // cyan-500
            Color(0xFF4ADE80), // green-400
          ],
        );
      case 'Integrante externo':
        // from-cyan-600 via-sky-500 to-blue-400
        return const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFF0891B2), // cyan-600
            Color(0xFF0EA5E9), // sky-500
            Color(0xFF60A5FA), // blue-400
          ],
        );
      default:
        return const LinearGradient(
          colors: [Color(0xFF2563EB), Color(0xFF06B6D4)],
        );
    }
  }

  // Colores del título y botón (exactos de Laravel)
  Color _getPrimaryColor() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        return const Color(0xFF15803D); // green-700
      case 'Empresa':
        return const Color(0xFF1D4ED8); // blue-700
      case 'Integrante externo':
        return const Color(0xFF0E7490); // cyan-700
      default:
        return Colors.blue;
    }
  }

  Color _getButtonColor() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        return const Color(0xFF16A34A); // green-600
      case 'Empresa':
        return const Color(0xFF2563EB); // blue-600
      case 'Integrante externo':
        return const Color(0xFF0891B2); // cyan-600
      default:
        return Colors.blue;
    }
  }

  Widget _buildFormIntegranteExterno() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // Nombre de usuario (ancho completo)
        TextFormField(
          controller: _nombreUsuarioController,
          decoration: InputDecoration(
            labelText: 'Nombre de usuario',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Correo (ancho completo)
        TextFormField(
          controller: _emailController,
          keyboardType: TextInputType.emailAddress,
          decoration: InputDecoration(
            labelText: 'Correo',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (!value.trim().contains('@')) {
              return 'Correo inválido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Contraseña (ancho completo)
        TextFormField(
          controller: _passwordController,
          obscureText: _obscurePassword,
          decoration: InputDecoration(
            labelText: 'Contraseña',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            suffixIcon: IconButton(
              icon: Icon(
                _obscurePassword
                    ? Icons.visibility_outlined
                    : Icons.visibility_off_outlined,
              ),
              onPressed: () {
                setState(() {
                  _obscurePassword = !_obscurePassword;
                });
              },
            ),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (value.trim().length < 6) {
              return 'Mínimo 6 caracteres';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Nombres y Apellidos (lado a lado)
        Row(
          children: [
            Expanded(
              child: TextFormField(
                controller: _nombresController,
                decoration: InputDecoration(
                  labelText: 'Nombres',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Requerido';
                  }
                  return null;
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                controller: _apellidosController,
                decoration: InputDecoration(
                  labelText: 'Apellidos',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Requerido';
                  }
                  return null;
                },
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        // Teléfono (ancho completo)
        TextFormField(
          controller: _telefonoExternoController,
          keyboardType: TextInputType.phone,
          decoration: InputDecoration(
            labelText: 'Teléfono',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
        const SizedBox(height: 16),
        // Dirección (ancho completo)
        TextFormField(
          controller: _direccionExternoController,
          decoration: InputDecoration(
            labelText: 'Dirección',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
        const SizedBox(height: 16),
        // Fecha de nacimiento (ancho completo)
        TextFormField(
          controller: _fechaNacimientoController,
          decoration: InputDecoration(
            labelText: 'Fecha de nacimiento',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          readOnly: true,
          onTap: () async {
            final date = await showDatePicker(
              context: context,
              initialDate: DateTime.now(),
              firstDate: DateTime(1900),
              lastDate: DateTime.now(),
            );
            if (date != null) {
              _fechaNacimientoController.text =
                  '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
            }
          },
        ),
        const SizedBox(height: 16),
        // Descripción (ancho completo)
        TextFormField(
          controller: _descripcionExternoController,
          maxLines: 2,
          decoration: InputDecoration(
            labelText: 'Descripción',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
      ],
    );
  }

  Widget _buildFormONG() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // Nombre de usuario (ancho completo)
        TextFormField(
          controller: _nombreUsuarioController,
          decoration: InputDecoration(
            labelText: 'Nombre de usuario',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Correo (ancho completo)
        TextFormField(
          controller: _emailController,
          keyboardType: TextInputType.emailAddress,
          decoration: InputDecoration(
            labelText: 'Correo',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (!value.trim().contains('@')) {
              return 'Correo inválido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Contraseña (ancho completo)
        TextFormField(
          controller: _passwordController,
          obscureText: _obscurePassword,
          decoration: InputDecoration(
            labelText: 'Contraseña',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            suffixIcon: IconButton(
              icon: Icon(
                _obscurePassword
                    ? Icons.visibility_outlined
                    : Icons.visibility_off_outlined,
              ),
              onPressed: () {
                setState(() {
                  _obscurePassword = !_obscurePassword;
                });
              },
            ),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (value.trim().length < 6) {
              return 'Mínimo 6 caracteres';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Nombre de la ONG y NIT (lado a lado)
        Row(
          children: [
            Expanded(
              child: TextFormField(
                controller: _nombreOngController,
                decoration: InputDecoration(
                  labelText: 'Nombre de la ONG',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Requerido';
                  }
                  return null;
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                controller: _nitOngController,
                decoration: InputDecoration(
                  labelText: 'NIT',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        // Teléfono y Sitio web (lado a lado)
        Row(
          children: [
            Expanded(
              child: TextFormField(
                controller: _telefonoOngController,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  labelText: 'Teléfono',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                controller: _sitioWebOngController,
                keyboardType: TextInputType.url,
                decoration: InputDecoration(
                  labelText: 'Sitio web',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        // Dirección (ancho completo)
        TextFormField(
          controller: _direccionOngController,
          decoration: InputDecoration(
            labelText: 'Dirección',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
        const SizedBox(height: 16),
        // Descripción (ancho completo)
        TextFormField(
          controller: _descripcionOngController,
          maxLines: 2,
          decoration: InputDecoration(
            labelText: 'Descripción',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
      ],
    );
  }

  Widget _buildFormEmpresa() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // Nombre de usuario (ancho completo)
        TextFormField(
          controller: _nombreUsuarioController,
          decoration: InputDecoration(
            labelText: 'Nombre de usuario',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Correo (ancho completo)
        TextFormField(
          controller: _emailController,
          keyboardType: TextInputType.emailAddress,
          decoration: InputDecoration(
            labelText: 'Correo',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (!value.trim().contains('@')) {
              return 'Correo inválido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Contraseña (ancho completo)
        TextFormField(
          controller: _passwordController,
          obscureText: _obscurePassword,
          decoration: InputDecoration(
            labelText: 'Contraseña',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            suffixIcon: IconButton(
              icon: Icon(
                _obscurePassword
                    ? Icons.visibility_outlined
                    : Icons.visibility_off_outlined,
              ),
              onPressed: () {
                setState(() {
                  _obscurePassword = !_obscurePassword;
                });
              },
            ),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            if (value.trim().length < 6) {
              return 'Mínimo 6 caracteres';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // Nombre de la Empresa (ancho completo)
        TextFormField(
          controller: _nombreEmpresaController,
          decoration: InputDecoration(
            labelText: 'Nombre de la Empresa',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: 16),
        // NIT y Teléfono (lado a lado)
        Row(
          children: [
            Expanded(
              child: TextFormField(
                controller: _nitEmpresaController,
                decoration: InputDecoration(
                  labelText: 'NIT',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                controller: _telefonoEmpresaController,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  labelText: 'Teléfono',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        // Dirección (ancho completo)
        TextFormField(
          controller: _direccionEmpresaController,
          decoration: InputDecoration(
            labelText: 'Dirección',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          ),
        ),
        const SizedBox(height: 16),
        // Sitio Web y Descripción (lado a lado)
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: TextFormField(
                controller: _sitioWebEmpresaController,
                keyboardType: TextInputType.url,
                decoration: InputDecoration(
                  labelText: 'Sitio Web',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                controller: _descripcionEmpresaController,
                maxLines: 2,
                decoration: InputDecoration(
                  labelText: 'Descripción',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: BoxDecoration(gradient: _getBackgroundGradient()),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Container(
                constraints: const BoxConstraints(maxWidth: 768),
                padding: const EdgeInsets.all(32.0),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Título
                      Text(
                        _getTitle(),
                        style: TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.bold,
                          color: _getPrimaryColor(),
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 24),
                      // Formulario según tipo
                      if (widget.tipoUsuario == 'Integrante externo')
                        _buildFormIntegranteExterno()
                      else if (widget.tipoUsuario == 'ONG')
                        _buildFormONG()
                      else if (widget.tipoUsuario == 'Empresa')
                        _buildFormEmpresa(),
                      const SizedBox(height: 24),
                      // Botón de registro
                      ElevatedButton(
                        onPressed: _isLoading ? null : _handleRegister,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: _getButtonColor(),
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
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
                                : Text(
                                  widget.tipoUsuario == 'ONG'
                                      ? 'Registrar ONG'
                                      : widget.tipoUsuario == 'Empresa'
                                      ? 'Registrar Empresa'
                                      : 'Registrar Usuario',
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                    color: Colors.white,
                                  ),
                                ),
                      ),
                      const SizedBox(height: 16),
                      // Enlace a login
                      Center(
                        child: TextButton(
                          onPressed: () {
                            Navigator.of(
                              context,
                            ).popUntil((route) => route.isFirst);
                          },
                          child: Text(
                            '¿Ya tienes cuenta? Inicia sesión',
                            style: TextStyle(
                              fontSize: 14,
                              color: _getPrimaryColor(),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
