import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

/// Widget de filtros avanzados para dashboards
class AdvancedFilterWidget extends StatefulWidget {
  final DateTime? fechaInicio;
  final DateTime? fechaFin;
  final String? estadoEvento;
  final String? tipoParticipacion;
  final String? busquedaEvento;
  final Function({
    DateTime? fechaInicio,
    DateTime? fechaFin,
    String? estadoEvento,
    String? tipoParticipacion,
    String? busquedaEvento,
  })
  onApply;
  final VoidCallback onClear;

  const AdvancedFilterWidget({
    super.key,
    this.fechaInicio,
    this.fechaFin,
    this.estadoEvento,
    this.tipoParticipacion,
    this.busquedaEvento,
    required this.onApply,
    required this.onClear,
  });

  @override
  State<AdvancedFilterWidget> createState() => _AdvancedFilterWidgetState();
}

class _AdvancedFilterWidgetState extends State<AdvancedFilterWidget> {
  late DateTime? _fechaInicio;
  late DateTime? _fechaFin;
  late String? _estadoEvento;
  late String? _tipoParticipacion;
  late TextEditingController _busquedaController;

  final List<String> _estadosEvento = [
    'todos',
    'activo',
    'inactivo',
    'finalizado',
  ];

  final List<String> _tiposParticipacion = [
    'todos',
    'voluntario',
    'asistente',
    'colaborador',
  ];

  @override
  void initState() {
    super.initState();
    _fechaInicio = widget.fechaInicio;
    _fechaFin = widget.fechaFin;
    _estadoEvento = widget.estadoEvento ?? 'todos';
    _tipoParticipacion = widget.tipoParticipacion ?? 'todos';
    _busquedaController = TextEditingController(text: widget.busquedaEvento);
  }

  @override
  void dispose() {
    _busquedaController.dispose();
    super.dispose();
  }

  Future<void> _selectDateRange() async {
    final DateTimeRange? picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDateRange:
          _fechaInicio != null && _fechaFin != null
              ? DateTimeRange(start: _fechaInicio!, end: _fechaFin!)
              : null,
    );

    if (picked != null) {
      setState(() {
        _fechaInicio = picked.start;
        _fechaFin = picked.end;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.all(16),
      child: ExpansionTile(
        title: const Row(
          children: [
            Icon(Icons.filter_list, size: 20),
            SizedBox(width: 8),
            Text('Filtros Avanzados'),
          ],
        ),
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Rango de fechas
                _buildDateRangeField(),
                const SizedBox(height: 16),

                // Estado del evento
                _buildEstadoEventoField(),
                const SizedBox(height: 16),

                // Tipo de participación
                _buildTipoParticipacionField(),
                const SizedBox(height: 16),

                // Búsqueda
                _buildBusquedaField(),
                const SizedBox(height: 24),

                // Botones
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    TextButton(
                      onPressed: () {
                        setState(() {
                          _fechaInicio = null;
                          _fechaFin = null;
                          _estadoEvento = 'todos';
                          _tipoParticipacion = 'todos';
                          _busquedaController.clear();
                        });
                        widget.onClear();
                      },
                      child: const Text('Limpiar'),
                    ),
                    const SizedBox(width: 8),
                    ElevatedButton(
                      onPressed: () {
                        widget.onApply(
                          fechaInicio: _fechaInicio,
                          fechaFin: _fechaFin,
                          estadoEvento:
                              _estadoEvento == 'todos' ? null : _estadoEvento,
                          tipoParticipacion:
                              _tipoParticipacion == 'todos'
                                  ? null
                                  : _tipoParticipacion,
                          busquedaEvento:
                              _busquedaController.text.isEmpty
                                  ? null
                                  : _busquedaController.text,
                        );
                      },
                      child: const Text('Aplicar Filtros'),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDateRangeField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Rango de Fechas',
          style: TextStyle(fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 8),
        InkWell(
          onTap: _selectDateRange,
          child: Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              border: Border.all(color: Colors.grey[300]!),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              children: [
                const Icon(Icons.calendar_today, size: 20),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    _fechaInicio != null && _fechaFin != null
                        ? '${DateFormat('dd/MM/yyyy').format(_fechaInicio!)} - ${DateFormat('dd/MM/yyyy').format(_fechaFin!)}'
                        : 'Seleccionar rango de fechas',
                    style: TextStyle(
                      color:
                          _fechaInicio != null && _fechaFin != null
                              ? Colors.black
                              : Colors.grey[600],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildEstadoEventoField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Estado del Evento',
          style: TextStyle(fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: _estadoEvento,
          decoration: InputDecoration(
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 8,
            ),
          ),
          items:
              _estadosEvento.map((estado) {
                return DropdownMenuItem(
                  value: estado,
                  child: Text(estado[0].toUpperCase() + estado.substring(1)),
                );
              }).toList(),
          onChanged: (value) {
            setState(() {
              _estadoEvento = value;
            });
          },
        ),
      ],
    );
  }

  Widget _buildTipoParticipacionField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Tipo de Participación',
          style: TextStyle(fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: _tipoParticipacion,
          decoration: InputDecoration(
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 8,
            ),
          ),
          items:
              _tiposParticipacion.map((tipo) {
                return DropdownMenuItem(
                  value: tipo,
                  child: Text(tipo[0].toUpperCase() + tipo.substring(1)),
                );
              }).toList(),
          onChanged: (value) {
            setState(() {
              _tipoParticipacion = value;
            });
          },
        ),
      ],
    );
  }

  Widget _buildBusquedaField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Buscar Evento',
          style: TextStyle(fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 8),
        TextField(
          controller: _busquedaController,
          decoration: InputDecoration(
            hintText: 'Ingrese nombre del evento',
            prefixIcon: const Icon(Icons.search),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 8,
            ),
          ),
        ),
      ],
    );
  }
}
