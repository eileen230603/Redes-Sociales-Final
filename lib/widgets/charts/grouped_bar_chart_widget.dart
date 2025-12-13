import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';

/// Datos para un grupo de barras
class GroupedBarData {
  final String label;
  final Map<String, double> values; // key: nombre de la serie, value: valor

  GroupedBarData({required this.label, required this.values});
}

/// Widget de gráfico de barras agrupadas (Grouped Bar Chart)
/// Muestra múltiples series de datos lado a lado
class GroupedBarChartWidget extends StatelessWidget {
  final List<GroupedBarData> data;
  final String title;
  final List<Color> colors;
  final bool showGrid;
  final String? subtitle;
  final List<String> seriesNames; // Nombres de las series

  const GroupedBarChartWidget({
    super.key,
    required this.data,
    required this.title,
    required this.seriesNames,
    this.colors = const [Colors.blue, Colors.green, Colors.orange, Colors.red],
    this.showGrid = true,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty || seriesNames.isEmpty) {
      return _buildEmptyState();
    }

    // Obtener máximo valor para escalar el gráfico
    double maxY = 0;
    for (final item in data) {
      for (final value in item.values.values) {
        if (value > maxY) maxY = value;
      }
    }

    // Crear datasets para cada serie
    final datasets =
        seriesNames.asMap().entries.map((entry) {
          final index = entry.key;
          final seriesName = entry.value;
          final color = colors[index % colors.length];

          return BarChartGroupData(
            x: index,
            barRods:
                data.asMap().entries.map((dataEntry) {
                  final dataIndex = dataEntry.key;
                  final dataItem = dataEntry.value;
                  final value = dataItem.values[seriesName] ?? 0.0;

                  return BarChartRodData(
                    toY: value,
                    color: color,
                    width: 20,
                    borderRadius: const BorderRadius.vertical(
                      top: Radius.circular(4),
                    ),
                  );
                }).toList(),
          );
        }).toList();

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            if (subtitle != null) ...[
              const SizedBox(height: 4),
              Text(
                subtitle!,
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
            ],
            const SizedBox(height: 24),
            SizedBox(
              height: 250,
              child: BarChart(
                BarChartData(
                  alignment: BarChartAlignment.spaceAround,
                  maxY: maxY * 1.2,
                  barTouchData: BarTouchData(
                    enabled: true,
                    touchTooltipData: BarTouchTooltipData(
                      getTooltipItem: (group, groupIndex, rod, rodIndex) {
                        final label = data[rodIndex].label;
                        final seriesName = seriesNames[groupIndex];
                        final value = rod.toY.toInt();
                        return BarTooltipItem(
                          '$label\n$seriesName: $value',
                          const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        );
                      },
                    ),
                  ),
                  titlesData: FlTitlesData(
                    show: true,
                    rightTitles: const AxisTitles(
                      sideTitles: SideTitles(showTitles: false),
                    ),
                    topTitles: const AxisTitles(
                      sideTitles: SideTitles(showTitles: false),
                    ),
                    bottomTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles: true,
                        reservedSize: 40,
                        getTitlesWidget: (value, meta) {
                          if (value.toInt() >= data.length)
                            return const Text('');
                          final label = data[value.toInt()].label;
                          return Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Text(
                              label.length > 10
                                  ? '${label.substring(0, 8)}...'
                                  : label,
                              style: TextStyle(
                                color: Colors.grey[600],
                                fontSize: 10,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          );
                        },
                      ),
                    ),
                    leftTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles: true,
                        reservedSize: 42,
                        interval: maxY > 10 ? (maxY / 5).ceilToDouble() : 1,
                        getTitlesWidget: (value, meta) {
                          return Text(
                            value.toInt().toString(),
                            style: TextStyle(
                              color: Colors.grey[600],
                              fontSize: 12,
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                  gridData: FlGridData(
                    show: showGrid,
                    drawVerticalLine: false,
                    horizontalInterval:
                        maxY > 10 ? (maxY / 5).ceilToDouble() : 1,
                    getDrawingHorizontalLine: (value) {
                      return FlLine(color: Colors.grey[300], strokeWidth: 1);
                    },
                  ),
                  borderData: FlBorderData(
                    show: true,
                    border: Border.all(color: Colors.grey[300]!),
                  ),
                  barGroups: List.generate(
                    data.length,
                    (index) => BarChartGroupData(
                      x: index,
                      groupVertically: true,
                      barRods:
                          seriesNames.asMap().entries.map((entry) {
                            final seriesIndex = entry.key;
                            final seriesName = entry.value;
                            final color = colors[seriesIndex % colors.length];
                            final value = data[index].values[seriesName] ?? 0.0;

                            return BarChartRodData(
                              toY: value,
                              color: color,
                              width: 20,
                              borderRadius: const BorderRadius.vertical(
                                top: Radius.circular(4),
                              ),
                            );
                          }).toList(),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),
            _buildLegend(),
          ],
        ),
      ),
    );
  }

  Widget _buildLegend() {
    return Wrap(
      spacing: 16,
      runSpacing: 8,
      children:
          seriesNames.asMap().entries.map((entry) {
            final index = entry.key;
            final seriesName = entry.value;
            final color = colors[index % colors.length];

            return Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 16,
                  height: 16,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
                const SizedBox(width: 6),
                Text(
                  seriesName,
                  style: TextStyle(fontSize: 12, color: Colors.grey[700]),
                ),
              ],
            );
          }).toList(),
    );
  }

  Widget _buildEmptyState() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Text(
              title,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 24),
            SizedBox(
              height: 250,
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.bar_chart, size: 48, color: Colors.grey[400]),
                    const SizedBox(height: 8),
                    Text(
                      'No hay datos disponibles',
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
