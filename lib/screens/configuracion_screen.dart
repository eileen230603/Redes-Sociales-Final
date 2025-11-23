import 'dart:convert';
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
  String? _categoriaSeleccionada;
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
    // Convertir valor según tipo
    String valorString;
    if (parametro.tipo == 'booleano') {
      valorString = nuevoValor == true ? '1' : '0';
    } else if (parametro.tipo == 'json' && nuevoValor is Map) {
      valorString = jsonEncode(nuevoValor);
    } else {
      valorString = nuevoValor.toString();
    }

    final result = await ParametrizacionService.actualizarParametro(
      parametro.id,
      {'valor': valorString},
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: const AppDrawer(currentRoute: '/configuracion'),
      appBar: AppBar(
        title: const Text('Configuración del Sistema'),
        elevation: 0,
        actions: [
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
                    hintText: 'Buscar parámetros...',
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
                    // Debounce podría agregarse aquí
                  },
                  onSubmitted: (_) => _cargarParametros(),
                ),
                const SizedBox(height: 12),
                // Filtro por categoría
                Row(
                  children: [
                    const Text(
                      'Categoría: ',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Expanded(
                      child: DropdownButton<String>(
                        value: _categoriaSeleccionada,
                        hint: const Text('Todas las categorías'),
                        isExpanded: true,
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
                          ElevatedButton(
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
                    : ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: _parametros.length,
                      itemBuilder: (context, index) {
                        final parametro = _parametros[index];
                        return _buildParametroCard(parametro);
                      },
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
            if (parametro.descripcion != null)
              Text(
                parametro.descripcion!,
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
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
