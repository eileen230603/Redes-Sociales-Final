import 'package:flutter/material.dart';
import '../services/parametrizacion_service.dart';
import '../models/parametro.dart';
import '../widgets/app_drawer.dart';

class ConfiguracionScreen extends StatefulWidget {
  const ConfiguracionScreen({super.key});

  @override
  State<ConfiguracionScreen> createState() => _ConfiguracionScreenState();
}

class _ConfiguracionScreenState extends State<ConfiguracionScreen> {
  List<Parametro> _parametros = [];
  List<String> _categorias = [];
  List<String> _grupos = [];
  String? _categoriaSeleccionada;
  String? _grupoSeleccionado;
  bool _isLoading = true;
  String? _error;
  String _busqueda = '';

  @override
  void initState() {
    super.initState();
    _cargarDatos();
  }

  Future<void> _cargarDatos() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    // Cargar categorías
    final categoriasResult = await ParametrizacionService.getCategorias();
    if (categoriasResult['success'] == true) {
      setState(() {
        _categorias =
            (categoriasResult['categorias'] as List)
                .map((c) => c.toString())
                .toList();
      });
    }

    // Cargar grupos
    final gruposResult = await ParametrizacionService.getGrupos();
    if (gruposResult['success'] == true) {
      setState(() {
        _grupos =
            (gruposResult['grupos'] as List).map((g) => g.toString()).toList();
      });
    }

    // Cargar parámetros
    await _cargarParametros();
  }

  Future<void> _cargarParametros() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await ParametrizacionService.getParametros(
      categoria: _categoriaSeleccionada,
      grupo: _grupoSeleccionado,
      buscar: _busqueda.isEmpty ? null : _busqueda,
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
      if (result['success'] == true) {
        _parametros = result['parametros'] as List<Parametro>;
      } else {
        _error = result['error'] as String? ?? 'Error al cargar parámetros';
        _parametros = [];
      }
    });
  }

  Future<void> _actualizarParametro(
    Parametro parametro,
    dynamic nuevoValor,
  ) async {
    // Usar el endpoint específico para actualizar solo el valor
    final result = await ParametrizacionService.actualizarValorParametro(
      parametro.id,
      nuevoValor,
    );

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Parámetro actualizado',
          ),
          backgroundColor: Colors.green,
        ),
      );
      await _cargarParametros();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al actualizar'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _crearParametro() async {
    final result = await Navigator.push<Map<String, dynamic>>(
      context,
      MaterialPageRoute(builder: (context) => _FormularioParametroScreen()),
    );

    if (result != null && result['success'] == true) {
      await _cargarParametros();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Parámetro creado correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
    }
  }

  Future<void> _editarParametro(Parametro parametro) async {
    final result = await Navigator.push<Map<String, dynamic>>(
      context,
      MaterialPageRoute(
        builder: (context) => _FormularioParametroScreen(parametro: parametro),
      ),
    );

    if (result != null && result['success'] == true) {
      await _cargarParametros();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ??
                'Parámetro actualizado correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
    }
  }

  Future<void> _eliminarParametro(Parametro parametro) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Eliminar Parámetro'),
            content: Text(
              '¿Estás seguro de que deseas eliminar el parámetro "${parametro.nombre}"?',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, true),
                style: TextButton.styleFrom(foregroundColor: Colors.red),
                child: const Text('Eliminar'),
              ),
            ],
          ),
    );

    if (confirm != true) return;

    final result = await ParametrizacionService.eliminarParametro(parametro.id);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] as String? ?? 'Parámetro eliminado correctamente',
          ),
          backgroundColor: Colors.green,
        ),
      );
      await _cargarParametros();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['error'] as String? ?? 'Error al eliminar'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _buscarPorCodigo() async {
    final codigoController = TextEditingController();
    final result = await showDialog<String>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Buscar por Código'),
            content: TextField(
              controller: codigoController,
              decoration: const InputDecoration(
                labelText: 'Código del parámetro',
                hintText: 'Ej: MAX_EVENTOS',
                border: OutlineInputBorder(),
              ),
              autofocus: true,
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Cancelar'),
              ),
              ElevatedButton(
                onPressed:
                    () => Navigator.pop(context, codigoController.text.trim()),
                child: const Text('Buscar'),
              ),
            ],
          ),
    );

    if (result != null && result.isNotEmpty) {
      final parametroResult =
          await ParametrizacionService.getParametroPorCodigo(result);
      if (parametroResult['success'] == true) {
        final parametro = parametroResult['parametro'] as Parametro;
        // Mostrar detalles del parámetro encontrado
        await _editarParametro(parametro);
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              parametroResult['error'] as String? ?? 'Parámetro no encontrado',
            ),
            backgroundColor: Colors.orange,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/configuracion'),
      appBar: AppBar(
        title: const Text('Configuración del Sistema'),
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: _buscarPorCodigo,
            tooltip: 'Buscar por código',
          ),
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: _crearParametro,
            tooltip: 'Crear parámetro',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _cargarDatos,
            tooltip: 'Actualizar',
          ),
        ],
      ),
      body: Column(
        children: [
          // Filtros y búsqueda
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.grey[100],
            child: Column(
              children: [
                // Búsqueda
                TextField(
                  decoration: InputDecoration(
                    hintText: 'Buscar por código, nombre o descripción...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon:
                        _busqueda.isNotEmpty
                            ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                setState(() {
                                  _busqueda = '';
                                });
                                _cargarParametros();
                              },
                            )
                            : null,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    filled: true,
                    fillColor: Colors.white,
                  ),
                  onChanged: (value) {
                    setState(() {
                      _busqueda = value;
                    });
                  },
                  onSubmitted: (_) => _cargarParametros(),
                ),
                const SizedBox(height: 12),
                // Filtros por categoría y grupo
                Row(
                  children: [
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: _categoriaSeleccionada,
                        decoration: const InputDecoration(
                          labelText: 'Categoría',
                          border: OutlineInputBorder(),
                          contentPadding: EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 8,
                          ),
                        ),
                        hint: const Text('Todas las categorías'),
                        items: [
                          const DropdownMenuItem<String>(
                            value: null,
                            child: Text('Todas las categorías'),
                          ),
                          ..._categorias.map(
                            (cat) => DropdownMenuItem<String>(
                              value: cat,
                              child: Text(cat),
                            ),
                          ),
                        ],
                        onChanged: (value) {
                          setState(() {
                            _categoriaSeleccionada = value;
                          });
                          _cargarParametros();
                        },
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: _grupoSeleccionado,
                        decoration: const InputDecoration(
                          labelText: 'Grupo',
                          border: OutlineInputBorder(),
                          contentPadding: EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 8,
                          ),
                        ),
                        hint: const Text('Todos los grupos'),
                        items: [
                          const DropdownMenuItem<String>(
                            value: null,
                            child: Text('Todos los grupos'),
                          ),
                          ..._grupos.map(
                            (grupo) => DropdownMenuItem<String>(
                              value: grupo,
                              child: Text(grupo),
                            ),
                          ),
                        ],
                        onChanged: (value) {
                          setState(() {
                            _grupoSeleccionado = value;
                          });
                          _cargarParametros();
                        },
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          // Lista de parámetros
          Expanded(
            child:
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
                            color: Colors.red[300],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            _error!,
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Colors.red[700]),
                          ),
                          const SizedBox(height: 16),
                          FilledButton.tonal(
                            onPressed: _cargarParametros,
                            child: const Text('Reintentar'),
                          ),
                        ],
                      ),
                    )
                    : _parametros.isEmpty
                    ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.settings,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'No hay parámetros disponibles',
                            style: TextStyle(
                              fontSize: 18,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ),
                    )
                    : RefreshIndicator(
                      onRefresh: _cargarParametros,
                      child: ListView.builder(
                        padding: const EdgeInsets.all(8),
                        itemCount: _parametros.length,
                        itemBuilder: (context, index) {
                          final parametro = _parametros[index];
                          return _buildParametroCard(parametro);
                        },
                      ),
                    ),
          ),
        ],
      ),
    );
  }

  Widget _buildParametroCard(Parametro parametro) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      elevation: 2,
      child: ExpansionTile(
        leading: Icon(
          _getIconForTipo(parametro.tipo),
          color: Theme.of(context).primaryColor,
        ),
        title: Text(
          parametro.nombre,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Código: ${parametro.codigo}'),
            if (parametro.categoria.isNotEmpty)
              Text('Categoría: ${parametro.categoria}'),
            if (parametro.grupo != null && parametro.grupo!.isNotEmpty)
              Text('Grupo: ${parametro.grupo}'),
            if (parametro.descripcion != null)
              Text(
                parametro.descripcion!,
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
          ],
        ),
        trailing: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            IconButton(
              icon: const Icon(Icons.edit, size: 20),
              onPressed: () => _editarParametro(parametro),
              tooltip: 'Editar',
            ),
            IconButton(
              icon: const Icon(Icons.delete, size: 20, color: Colors.red),
              onPressed: () => _eliminarParametro(parametro),
              tooltip: 'Eliminar',
            ),
          ],
        ),
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Valor actual
                Row(
                  children: [
                    const Text(
                      'Valor actual: ',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Expanded(
                      child: Text(
                        parametro.valor ??
                            parametro.valorDefecto ??
                            'Sin valor',
                        style: TextStyle(
                          color:
                              parametro.valor != null
                                  ? Colors.green[700]
                                  : Colors.grey[600],
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                // Campo de edición si es editable
                if (parametro.editable)
                  _buildEditorCampo(parametro)
                else
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.lock, size: 16),
                        SizedBox(width: 8),
                        Text(
                          'Este parámetro no es editable',
                          style: TextStyle(fontStyle: FontStyle.italic),
                        ),
                      ],
                    ),
                  ),
                if (parametro.ayuda != null) ...[
                  const SizedBox(height: 12),
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.blue[50],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(
                          Icons.info_outline,
                          size: 16,
                          color: Colors.blue[700],
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            parametro.ayuda!,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.blue[900],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEditorCampo(Parametro parametro) {
    final valorActual = parametro.valorFormateado ?? parametro.valorDefecto;

    switch (parametro.tipo) {
      case 'booleano':
        final boolValue =
            valorActual == true || valorActual == '1' || valorActual == 'true';
        return SwitchListTile(
          title: const Text('Activar/Desactivar'),
          value: boolValue,
          onChanged: (value) {
            _actualizarParametro(parametro, value);
          },
        );

      case 'numero':
        final numValue =
            valorActual is num
                ? valorActual.toDouble()
                : double.tryParse(valorActual.toString()) ?? 0.0;
        return TextField(
          decoration: const InputDecoration(
            labelText: 'Valor numérico',
            border: OutlineInputBorder(),
          ),
          keyboardType: TextInputType.number,
          controller: TextEditingController(text: numValue.toString()),
          onSubmitted: (value) {
            final numVal = double.tryParse(value);
            if (numVal != null) {
              _actualizarParametro(parametro, numVal);
            }
          },
        );

      case 'select':
        return DropdownButtonFormField<String>(
          decoration: const InputDecoration(
            labelText: 'Seleccionar opción',
            border: OutlineInputBorder(),
          ),
          value: valorActual?.toString(),
          items:
              (parametro.opciones ?? [])
                  .map(
                    (op) => DropdownMenuItem<String>(
                      value: op.toString(),
                      child: Text(op.toString()),
                    ),
                  )
                  .toList(),
          onChanged: (value) {
            if (value != null) {
              _actualizarParametro(parametro, value);
            }
          },
        );

      default: // texto, json, fecha
        return TextField(
          decoration: const InputDecoration(
            labelText: 'Valor',
            border: OutlineInputBorder(),
          ),
          controller: TextEditingController(
            text: valorActual?.toString() ?? '',
          ),
          maxLines: parametro.tipo == 'json' ? 5 : 1,
          onSubmitted: (value) {
            _actualizarParametro(parametro, value);
          },
        );
    }
  }

  IconData _getIconForTipo(String tipo) {
    switch (tipo) {
      case 'texto':
        return Icons.text_fields;
      case 'numero':
        return Icons.numbers;
      case 'booleano':
        return Icons.toggle_on;
      case 'json':
        return Icons.code;
      case 'fecha':
        return Icons.calendar_today;
      case 'select':
        return Icons.list;
      default:
        return Icons.settings;
    }
  }
}

// Pantalla de formulario para crear/editar parámetros
class _FormularioParametroScreen extends StatefulWidget {
  final Parametro? parametro;

  const _FormularioParametroScreen({this.parametro});

  @override
  State<_FormularioParametroScreen> createState() =>
      _FormularioParametroScreenState();
}

class _FormularioParametroScreenState
    extends State<_FormularioParametroScreen> {
  final _formKey = GlobalKey<FormState>();
  final _codigoController = TextEditingController();
  final _nombreController = TextEditingController();
  final _descripcionController = TextEditingController();
  final _categoriaController = TextEditingController();
  final _valorController = TextEditingController();
  final _valorDefectoController = TextEditingController();
  final _grupoController = TextEditingController();
  final _ordenController = TextEditingController();
  final _validacionController = TextEditingController();
  final _ayudaController = TextEditingController();

  String _tipoSeleccionado = 'texto';
  bool _editable = true;
  bool _visible = true;
  bool _requerido = false;
  bool _isLoading = false;
  List<String> _categorias = [];
  List<String> _tipos = [
    'texto',
    'numero',
    'booleano',
    'json',
    'fecha',
    'select',
  ];

  @override
  void initState() {
    super.initState();
    _cargarCategorias();
    if (widget.parametro != null) {
      _cargarDatosParametro();
    }
  }

  Future<void> _cargarCategorias() async {
    final result = await ParametrizacionService.getCategorias();
    if (result['success'] == true) {
      setState(() {
        _categorias =
            (result['categorias'] as List).map((c) => c.toString()).toList();
      });
    }
  }

  void _cargarDatosParametro() {
    final p = widget.parametro!;
    _codigoController.text = p.codigo;
    _nombreController.text = p.nombre;
    _descripcionController.text = p.descripcion ?? '';
    _categoriaController.text = p.categoria;
    _tipoSeleccionado = p.tipo;
    _valorController.text = p.valor ?? '';
    _valorDefectoController.text = p.valorDefecto ?? '';
    _grupoController.text = p.grupo ?? '';
    _ordenController.text = p.orden.toString();
    _validacionController.text = p.validacion ?? '';
    _ayudaController.text = p.ayuda ?? '';
    _editable = p.editable;
    _visible = p.visible;
    _requerido = p.requerido;
  }

  Future<void> _guardar() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
    });

    final parametroData = {
      'codigo': _codigoController.text.trim(),
      'nombre': _nombreController.text.trim(),
      'descripcion':
          _descripcionController.text.trim().isEmpty
              ? null
              : _descripcionController.text.trim(),
      'categoria': _categoriaController.text.trim(),
      'tipo': _tipoSeleccionado,
      'valor':
          _valorController.text.trim().isEmpty
              ? null
              : _valorController.text.trim(),
      'valor_defecto':
          _valorDefectoController.text.trim().isEmpty
              ? null
              : _valorDefectoController.text.trim(),
      'grupo':
          _grupoController.text.trim().isEmpty
              ? null
              : _grupoController.text.trim(),
      'orden': int.tryParse(_ordenController.text.trim()) ?? 0,
      'editable': _editable,
      'visible': _visible,
      'requerido': _requerido,
      'validacion':
          _validacionController.text.trim().isEmpty
              ? null
              : _validacionController.text.trim(),
      'ayuda':
          _ayudaController.text.trim().isEmpty
              ? null
              : _ayudaController.text.trim(),
    };

    Map<String, dynamic> result;
    if (widget.parametro != null) {
      result = await ParametrizacionService.actualizarParametro(
        widget.parametro!.id,
        parametroData,
      );
    } else {
      result = await ParametrizacionService.crearParametro(parametroData);
    }

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true) {
      Navigator.pop(context, result);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['error'] as String? ?? 'Error al guardar parámetro',
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.parametro != null ? 'Editar Parámetro' : 'Crear Parámetro',
        ),
      ),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            TextFormField(
              controller: _codigoController,
              decoration: const InputDecoration(
                labelText: 'Código *',
                hintText: 'Ej: MAX_EVENTOS',
                border: OutlineInputBorder(),
              ),
              enabled: widget.parametro == null, // No se puede editar el código
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'El código es requerido';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _nombreController,
              decoration: const InputDecoration(
                labelText: 'Nombre *',
                border: OutlineInputBorder(),
              ),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'El nombre es requerido';
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
              maxLines: 2,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _categoriaController,
              decoration: InputDecoration(
                labelText: 'Categoría *',
                border: const OutlineInputBorder(),
                suffixIcon:
                    _categorias.isNotEmpty
                        ? PopupMenuButton<String>(
                          icon: const Icon(Icons.arrow_drop_down),
                          onSelected: (value) {
                            setState(() {
                              _categoriaController.text = value;
                            });
                          },
                          itemBuilder:
                              (context) =>
                                  _categorias
                                      .map(
                                        (cat) => PopupMenuItem(
                                          value: cat,
                                          child: Text(cat),
                                        ),
                                      )
                                      .toList(),
                        )
                        : null,
              ),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'La categoría es requerida';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: _tipoSeleccionado,
              decoration: const InputDecoration(
                labelText: 'Tipo *',
                border: OutlineInputBorder(),
              ),
              items:
                  _tipos
                      .map(
                        (tipo) =>
                            DropdownMenuItem(value: tipo, child: Text(tipo)),
                      )
                      .toList(),
              onChanged: (value) {
                if (value != null) {
                  setState(() {
                    _tipoSeleccionado = value;
                  });
                }
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _valorController,
              decoration: const InputDecoration(
                labelText: 'Valor',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _valorDefectoController,
              decoration: const InputDecoration(
                labelText: 'Valor por Defecto',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _grupoController,
              decoration: const InputDecoration(
                labelText: 'Grupo',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _ordenController,
              decoration: const InputDecoration(
                labelText: 'Orden',
                border: OutlineInputBorder(),
              ),
              keyboardType: TextInputType.number,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _validacionController,
              decoration: const InputDecoration(
                labelText: 'Validación',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _ayudaController,
              decoration: const InputDecoration(
                labelText: 'Ayuda',
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 16),
            SwitchListTile(
              title: const Text('Editable'),
              value: _editable,
              onChanged: (value) {
                setState(() {
                  _editable = value;
                });
              },
            ),
            SwitchListTile(
              title: const Text('Visible'),
              value: _visible,
              onChanged: (value) {
                setState(() {
                  _visible = value;
                });
              },
            ),
            SwitchListTile(
              title: const Text('Requerido'),
              value: _requerido,
              onChanged: (value) {
                setState(() {
                  _requerido = value;
                });
              },
            ),
            const SizedBox(height: 24),
            FilledButton(
              onPressed: _isLoading ? null : _guardar,
              style: FilledButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 16),
                backgroundColor: const Color(0xFF00A36C),
                foregroundColor: Colors.white,
              ),
              child:
                  _isLoading
                      ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                      : Text(widget.parametro != null ? 'Actualizar' : 'Crear'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _codigoController.dispose();
    _nombreController.dispose();
    _descripcionController.dispose();
    _categoriaController.dispose();
    _valorController.dispose();
    _valorDefectoController.dispose();
    _grupoController.dispose();
    _ordenController.dispose();
    _validacionController.dispose();
    _ayudaController.dispose();
    super.dispose();
  }
}
