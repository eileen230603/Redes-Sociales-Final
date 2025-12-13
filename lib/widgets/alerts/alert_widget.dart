import 'package:flutter/material.dart';
import '../../models/dashboard/ong_dashboard_data.dart';

/// Widget para mostrar alertas del dashboard
class AlertWidget extends StatelessWidget {
  final Alerta alerta;
  final VoidCallback? onTap;

  const AlertWidget({super.key, required this.alerta, this.onTap});

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      color: alerta.color.withOpacity(0.1),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: alerta.color.withOpacity(0.3), width: 1),
      ),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: alerta.color.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(alerta.icon, color: alerta.color, size: 20),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _getSeverityLabel(alerta.severidad),
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: alerta.color,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      alerta.mensaje,
                      style: const TextStyle(
                        fontSize: 14,
                        color: Colors.black87,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(Icons.chevron_right, color: alerta.color),
            ],
          ),
        ),
      ),
    );
  }

  String _getSeverityLabel(String severidad) {
    switch (severidad) {
      case 'danger':
        return 'URGENTE';
      case 'warning':
        return 'ADVERTENCIA';
      case 'info':
        return 'INFORMACIÓN';
      default:
        return 'ALERTA';
    }
  }
}

/// Widget para mostrar lista de alertas
class AlertsListWidget extends StatelessWidget {
  final List<Alerta> alertas;
  final Function(int? eventoId)? onAlertTap;

  const AlertsListWidget({super.key, required this.alertas, this.onAlertTap});

  @override
  Widget build(BuildContext context) {
    if (alertas.isEmpty) {
      return const SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Row(
            children: [
              Icon(Icons.notifications_active, color: Colors.orange[700]),
              const SizedBox(width: 8),
              Text(
                'Alertas (${alertas.length})',
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
        ...alertas.map((alerta) {
          return AlertWidget(
            alerta: alerta,
            onTap: () {
              if (onAlertTap != null && alerta.eventoId != null) {
                onAlertTap!(alerta.eventoId);
              }
            },
          );
        }),
      ],
    );
  }
}
