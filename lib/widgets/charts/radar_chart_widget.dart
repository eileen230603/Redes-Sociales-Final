import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';

class RadarChartWidget extends StatelessWidget {
  final Map<String, double> data;
  final String title;
  final Color fillColor;
  final String? subtitle;

  const RadarChartWidget({
    super.key,
    required this.data,
    required this.title,
    this.fillColor = Colors.blue,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return _buildEmptyState();
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
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (subtitle != null) ...[
              const SizedBox(height: 4),
              Text(
                subtitle!,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey[600],
                ),
              ),
            ],
            const SizedBox(height: 24),
            SizedBox(
              height: 250,
              child: RadarChart(
                RadarChartData(
                  radarShape: RadarShape.polygon,
                  tickCount: 5,
                  ticksTextStyle: TextStyle(
                    color: Colors.grey[600],
                    fontSize: 10,
                  ),
                  radarBorderData: BorderSide(
                    color: Colors.grey[300]!,
                    width: 2,
                  ),
                  gridBorderData: BorderSide(
                    color: Colors.grey[300]!,
                    width: 1,
                  ),
                  tickBorderData: BorderSide(
                    color: Colors.grey[400]!,
                    width: 1,
                  ),
                  getTitle: (index, angle) {
                    final entries = data.entries.toList();
                    if (index >= entries.length) return const RadarChartTitle(text: '');
                    
                    String label = entries[index].key;
                    // Capitalize first letter
                    label = label[0].toUpperCase() + label.substring(1);
                    
                    return RadarChartTitle(
                      text: label.length > 12 ? '${label.substring(0, 10)}...' : label,
                      angle: angle,
                    );
                  },
                  dataSets: [
                    RadarDataSet(
                      fillColor: fillColor.withOpacity(0.3),
                      borderColor: fillColor,
                      borderWidth: 2,
                      dataEntries: data.values
                          .map((value) => RadarEntry(value: value))
                          .toList(),
                    ),
                  ],
                ),
                swapAnimationDuration: const Duration(milliseconds: 400),
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
      spacing: 12,
      runSpacing: 8,
      children: data.entries.map((entry) {
        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(
            color: fillColor.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: fillColor.withOpacity(0.3)),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                entry.key[0].toUpperCase() + entry.key.substring(1),
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(width: 4),
              Text(
                '${entry.value.toStringAsFixed(1)}%',
                style: TextStyle(
                  fontSize: 11,
                  color: fillColor,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
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
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 24),
            SizedBox(
              height: 250,
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.radar, size: 48, color: Colors.grey[400]),
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
