import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';

/// Datos para una línea en el gráfico múltiple
class MultiLineData {
  final String label;
  final Map<String, int> values; // key: fecha, value: cantidad
  final Color color;

  MultiLineData({
    required this.label,
    required this.values,
    required this.color,
  });
}

/// Widget de gráfico de líneas múltiples (Multi Line Chart)
/// Muestra múltiples series de datos como líneas superpuestas
class MultiLineChartWidget extends StatelessWidget {
  final List<MultiLineData> data;
  final String title;
  final bool showDots;
  final bool showGrid;
  final String? subtitle;

  const MultiLineChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.showDots = true,
    this.showGrid = true,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
    }

    // Obtener todas las fechas únicas y ordenarlas
    final allDates = <String>{};
    for (final lineData in data) {
      allDates.addAll(lineData.values.keys);
    }
    final sortedDates = allDates.toList()..sort();

    // Crear spots para cada línea
    final lineBarsData =
        data.map((lineData) {
          final spots =
              sortedDates.asMap().entries.map((entry) {
                final index = entry.key;
                final date = entry.value;
                final value = lineData.values[date] ?? 0;
                return FlSpot(index.toDouble(), value.toDouble());
              }).toList();

          return LineChartBarData(
            spots: spots,
            isCurved: true,
            color: lineData.color,
            barWidth: 3,
            isStrokeCapRound: true,
            dotData: FlDotData(
              show: showDots,
              getDotPainter: (spot, percent, barData, index) {
                return FlDotCirclePainter(
                  radius: 4,
                  color: lineData.color,
                  strokeWidth: 2,
                  strokeColor: Colors.white,
                );
              },
            ),
            belowBarData: BarAreaData(
              show: true,
              color: lineData.color.withOpacity(0.1),
            ),
          );
        }).toList();

    // Calcular maxY
    double maxY = 0;
    for (final lineData in data) {
      for (final value in lineData.values.values) {
        if (value > maxY) maxY = value.toDouble();
      }
    }

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
              child: LineChart(
                LineChartData(
                  gridData: FlGridData(
                    show: showGrid,
                    drawVerticalLine: true,
                    horizontalInterval: 1,
                    verticalInterval: 1,
                    getDrawingHorizontalLine: (value) {
                      return FlLine(color: Colors.grey[300], strokeWidth: 1);
                    },
                    getDrawingVerticalLine: (value) {
                      return FlLine(color: Colors.grey[300], strokeWidth: 1);
                    },
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
                        reservedSize: 30,
                        interval: sortedDates.length > 10 ? 2 : 1,
                        getTitlesWidget: (value, meta) {
                          if (value.toInt() >= sortedDates.length)
                            return const Text('');
                          final date = sortedDates[value.toInt()];
                          try {
                            final parsedDate = DateTime.parse(date);
                            return Padding(
                              padding: const EdgeInsets.only(top: 8.0),
                              child: Text(
                                DateFormat('MM/dd').format(parsedDate),
                                style: TextStyle(
                                  color: Colors.grey[600],
                                  fontSize: 10,
                                ),
                              ),
                            );
                          } catch (e) {
                            return Text(
                              date.length > 5 ? date.substring(0, 5) : date,
                              style: TextStyle(
                                color: Colors.grey[600],
                                fontSize: 10,
                              ),
                            );
                          }
                        },
                      ),
                    ),
                    leftTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles: true,
                        interval: maxY > 5 ? (maxY / 5) : 1,
                        reservedSize: 42,
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
                  borderData: FlBorderData(
                    show: true,
                    border: Border.all(color: Colors.grey[300]!),
                  ),
                  minX: 0,
                  maxX: (sortedDates.length - 1).toDouble(),
                  minY: 0,
                  maxY: maxY * 1.1,
                  lineBarsData: lineBarsData,
                  lineTouchData: LineTouchData(
                    touchTooltipData: LineTouchTooltipData(
                      getTooltipItems: (touchedSpots) {
                        return touchedSpots.map((spot) {
                          final date = sortedDates[spot.x.toInt()];
                          String formattedDate;
                          try {
                            final parsedDate = DateTime.parse(date);
                            formattedDate = DateFormat(
                              'MMM dd',
                            ).format(parsedDate);
                          } catch (e) {
                            formattedDate = date;
                          }
                          final lineData = data[spot.barIndex];
                          return LineTooltipItem(
                            '${lineData.label}\n$formattedDate: ${spot.y.toInt()}',
                            TextStyle(
                              color: lineData.color,
                              fontWeight: FontWeight.bold,
                            ),
                          );
                        }).toList();
                      },
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
          data.map((lineData) {
            return Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 16,
                  height: 3,
                  decoration: BoxDecoration(
                    color: lineData.color,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                const SizedBox(width: 6),
                Text(
                  lineData.label,
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
                    Icon(Icons.show_chart, size: 48, color: Colors.grey[400]),
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
