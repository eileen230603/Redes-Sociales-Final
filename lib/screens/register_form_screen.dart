import 'package:flutter/material.dart';
import '../config/design_tokens.dart';
import '../config/typography_system.dart';
import '../services/api_service.dart';
import '../widgets/atoms/app_button.dart';
import '../widgets/atoms/app_icon.dart';
import '../widgets/molecules/app_card.dart';
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
        _showSnackBar('Los campos Nombres y Apellidos son requeridos', AppColors.warning);
        return;
      }
    } else if (widget.tipoUsuario == 'ONG') {
      if (_nombreOngController.text.trim().isEmpty) {
        _showSnackBar('El nombre de la ONG es requerido', AppColors.warning);
        return;
      }
    } else if (widget.tipoUsuario == 'Empresa') {
      if (_nombreEmpresaController.text.trim().isEmpty) {
        _showSnackBar('El nombre de la Empresa es requerido', AppColors.warning);
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
        _showSnackBar('¡Registro exitoso!', AppColors.success);

        await Future.delayed(const Duration(milliseconds: 500));
        if (!mounted) return;

        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (context) => const HomeScreen()),
          (route) => false,
        );
      } else {
        _showSnackBar(response.error ?? 'Error al registrar', AppColors.error);
      }
    } catch (e) {
      if (!mounted) return;
      _showSnackBar('Error: ${e.toString()}', AppColors.error);
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  void _showSnackBar(String message, Color backgroundColor) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: backgroundColor,
        duration: const Duration(seconds: 3),
      ),
    );
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

  Color _getPrimaryColor() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        return AppColors.accent;
      case 'Empresa':
        return AppColors.info;
      case 'Integrante externo':
        return AppColors.primary;
      default:
        return AppColors.primary;
    }
  }

  IconData _getIcon() {
    switch (widget.tipoUsuario) {
      case 'ONG':
        return Icons.volunteer_activism;
      case 'Empresa':
        return Icons.business_center;
      case 'Integrante externo':
        return Icons.person_pin_circle;
      default:
        return Icons.person;
    }
  }

  Widget _buildFormField({
    required TextEditingController controller,
    required String label,
    String? Function(String?)? validator,
    TextInputType? keyboardType,
    bool obscureText = false,
    Widget? suffixIcon,
    bool readOnly = false,
    VoidCallback? onTap,
    int maxLines = 1,
  }) {
    return TextFormField(
      controller: controller,
      decoration: InputDecoration(
        labelText: label,
        suffixIcon: suffixIcon,
      ),
      validator: validator,
      keyboardType: keyboardType,
      obscureText: obscureText,
      readOnly: readOnly,
      onTap: onTap,
      maxLines: maxLines,
    );
  }

  Widget _buildFormIntegranteExterno() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildFormField(
          controller: _nombreUsuarioController,
          label: 'Nombre de usuario',
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _emailController,
          label: 'Correo electrónico',
          keyboardType: TextInputType.emailAddress,
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _passwordController,
          label: 'Contraseña',
          obscureText: _obscurePassword,
          suffixIcon: IconButton(
            icon: AppIcon.sm(
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
        const SizedBox(height: AppSpacing.md),
        Row(
          children: [
            Expanded(
              child: _buildFormField(
                controller: _nombresController,
                label: 'Nombres',
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Requerido';
                  }
                  return null;
                },
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: _buildFormField(
                controller: _apellidosController,
                label: 'Apellidos',
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _telefonoExternoController,
          label: 'Teléfono',
          keyboardType: TextInputType.phone,
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _direccionExternoController,
          label: 'Dirección',
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _fechaNacimientoController,
          label: 'Fecha de nacimiento',
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _descripcionExternoController,
          label: 'Descripción (opcional)',
          maxLines: 3,
        ),
      ],
    );
  }

  Widget _buildFormONG() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildFormField(
          controller: _nombreUsuarioController,
          label: 'Nombre de usuario',
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _emailController,
          label: 'Correo electrónico',
          keyboardType: TextInputType.emailAddress,
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _passwordController,
          label: 'Contraseña',
          obscureText: _obscurePassword,
          suffixIcon: IconButton(
            icon: AppIcon.sm(
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _nombreOngController,
          label: 'Nombre de la ONG',
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: AppSpacing.md),
        Row(
          children: [
            Expanded(
              child: _buildFormField(
                controller: _nitOngController,
                label: 'NIT',
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: _buildFormField(
                controller: _telefonoOngController,
                label: 'Teléfono',
                keyboardType: TextInputType.phone,
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _direccionOngController,
          label: 'Dirección',
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _sitioWebOngController,
          label: 'Sitio web (opcional)',
          keyboardType: TextInputType.url,
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _descripcionOngController,
          label: 'Descripción (opcional)',
          maxLines: 3,
        ),
      ],
    );
  }

  Widget _buildFormEmpresa() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildFormField(
          controller: _nombreUsuarioController,
          label: 'Nombre de usuario',
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _emailController,
          label: 'Correo electrónico',
          keyboardType: TextInputType.emailAddress,
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _passwordController,
          label: 'Contraseña',
          obscureText: _obscurePassword,
          suffixIcon: IconButton(
            icon: AppIcon.sm(
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
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _nombreEmpresaController,
          label: 'Nombre de la Empresa',
          validator: (value) {
            if (value == null || value.trim().isEmpty) {
              return 'Campo requerido';
            }
            return null;
          },
        ),
        const SizedBox(height: AppSpacing.md),
        Row(
          children: [
            Expanded(
              child: _buildFormField(
                controller: _nitEmpresaController,
                label: 'NIT',
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: _buildFormField(
                controller: _telefonoEmpresaController,
                label: 'Teléfono',
                keyboardType: TextInputType.phone,
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _direccionEmpresaController,
          label: 'Dirección',
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _sitioWebEmpresaController,
          label: 'Sitio web (opcional)',
          keyboardType: TextInputType.url,
        ),
        const SizedBox(height: AppSpacing.md),
        _buildFormField(
          controller: _descripcionEmpresaController,
          label: 'Descripción (opcional)',
          maxLines: 3,
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_getTitle()),
        leading: IconButton(
          icon: AppIcon.md(Icons.arrow_back_ios_new),
          onPressed: () => Navigator.of(context).pop(),
          tooltip: 'Volver',
        ),
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 600),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Header con icono
                  Container(
                    padding: const EdgeInsets.all(AppSpacing.lg),
                    decoration: BoxDecoration(
                      color: _getPrimaryColor().withOpacity(0.1),
                      borderRadius: BorderRadius.circular(AppRadius.lg),
                    ),
                    child: Column(
                      children: [
                        Container(
                          width: AppSizes.avatarLg,
                          height: AppSizes.avatarLg,
                          decoration: BoxDecoration(
                            color: _getPrimaryColor(),
                            borderRadius: BorderRadius.circular(AppRadius.md),
                          ),
                          child: Icon(
                            _getIcon(),
                            size: AppSizes.iconLg,
                            color: AppColors.textOnPrimary,
                          ),
                        ),
                        const SizedBox(height: AppSpacing.md),
                        Text(
                          _getTitle(),
                          style: AppTypography.headlineSmall.copyWith(
                            color: _getPrimaryColor(),
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: AppSpacing.xs),
                        Text(
                          'Completa el formulario para unirte a UNI2',
                          style: AppTypography.bodySecondary,
                          textAlign: TextAlign.center,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  // Form card
                  AppCard(
                    elevated: true,
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          if (widget.tipoUsuario == 'Integrante externo')
                            _buildFormIntegranteExterno()
                          else if (widget.tipoUsuario == 'ONG')
                            _buildFormONG()
                          else if (widget.tipoUsuario == 'Empresa')
                            _buildFormEmpresa(),
                          const SizedBox(height: AppSpacing.lg),
                          AppButton.primary(
                            label: widget.tipoUsuario == 'ONG'
                                ? 'Registrar ONG'
                                : widget.tipoUsuario == 'Empresa'
                                    ? 'Registrar Empresa'
                                    : 'Registrar Usuario',
                            icon: Icons.app_registration,
                            onPressed: _isLoading ? null : _handleRegister,
                            isLoading: _isLoading,
                          ),
                          const SizedBox(height: AppSpacing.md),
                          AppButton.text(
                            label: '¿Ya tienes cuenta? Inicia sesión',
                            icon: Icons.login,
                            onPressed: () {
                              Navigator.of(context).popUntil((route) => route.isFirst);
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
