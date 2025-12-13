import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class DateRangeFilterWidget extends StatefulWidget {
  final DateTime? startDate;
  final DateTime? endDate;
  final Function(DateTime?, DateTime?) onDateRangeSelected;
  final Color? primaryColor;

  const DateRangeFilterWidget({
    super.key,
    this.startDate,
    this.endDate,
    required this.onDateRangeSelected,
    this.primaryColor,
  });

  @override
  State<DateRangeFilterWidget> createState() => _DateRangeFilterWidgetState();
}

class _DateRangeFilterWidgetState extends State<DateRangeFilterWidget> {
  DateTime? _startDate;
  DateTime? _endDate;

  @override
  void initState() {
    super.initState();
    _startDate = widget.startDate;
    _endDate = widget.endDate;
  }

  Future<void> _selectDateRange() async {
    final DateTimeRange? picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDateRange: _startDate != null && _endDate != null
          ? DateTimeRange(start: _startDate!, end: _endDate!)
          : null,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: widget.primaryColor ?? Theme.of(context).primaryColor,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        _startDate = picked.start;
        _endDate = picked.end;
      });
      widget.onDateRangeSelected(_startDate, _endDate);
    }
  }

  void _clearFilter() {
    setState(() {
      _startDate = null;
      _endDate = null;
    });
    widget.onDateRangeSelected(null, null);
  }

  String _getDateRangeText() {
    if (_startDate == null || _endDate == null) {
      return 'Seleccionar rango de fechas';
    }
    return '${DateFormat('dd/MM/yyyy').format(_startDate!)} - ${DateFormat('dd/MM/yyyy').format(_endDate!)}';
  }

  @override
  Widget build(BuildContext context) {
    final hasFilter = _startDate != null && _endDate != null;

    return Card(
      elevation: 1,
      child: InkWell(
        onTap: _selectDateRange,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Row(
            children: [
              Icon(
                Icons.date_range,
                color: hasFilter
                    ? widget.primaryColor ?? Theme.of(context).primaryColor
                    : Colors.grey[600],
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  _getDateRangeText(),
                  style: TextStyle(
                    color: hasFilter ? Colors.black87 : Colors.grey[600],
                    fontWeight: hasFilter ? FontWeight.w500 : FontWeight.normal,
                  ),
                ),
              ),
              if (hasFilter)
                IconButton(
                  icon: const Icon(Icons.clear, size: 20),
                  onPressed: _clearFilter,
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                  tooltip: 'Limpiar filtro',
                ),
            ],
          ),
        ),
      ),
    );
  }
}

class QuickDateRangeSelector extends StatelessWidget {
  final Function(DateTime, DateTime) onRangeSelected;

  const QuickDateRangeSelector({
    super.key,
    required this.onRangeSelected,
  });

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 8,
      runSpacing: 8,
      children: [
        _buildQuickButton(context, 'Última semana', 7),
        _buildQuickButton(context, 'Último mes', 30),
        _buildQuickButton(context, 'Último trimestre', 90),
        _buildQuickButton(context, 'Último año', 365),
      ],
    );
  }

  Widget _buildQuickButton(BuildContext context, String label, int days) {
    return OutlinedButton(
      onPressed: () {
        final end = DateTime.now();
        final start = end.subtract(Duration(days: days));
        onRangeSelected(start, end);
      },
      style: OutlinedButton.styleFrom(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
      child: Text(
        label,
        style: const TextStyle(fontSize: 12),
      ),
    );
  }
}
