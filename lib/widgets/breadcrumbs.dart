import 'package:flutter/material.dart';

/// Widget de breadcrumbs para mostrar la ruta de navegación
class Breadcrumbs extends StatelessWidget {
  final List<BreadcrumbItem> items;
  final Color? textColor;
  final Color? separatorColor;

  const Breadcrumbs({
    super.key,
    required this.items,
    this.textColor,
    this.separatorColor,
  });

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) return const SizedBox.shrink();
    if (items.length == 1) {
      return _buildSingleItem(context, items.first);
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        border: Border(bottom: BorderSide(color: Colors.grey[300]!)),
      ),
      child: Row(
        children: [
          Icon(Icons.home, size: 16, color: textColor ?? Colors.grey[700]),
          const SizedBox(width: 8),
          Expanded(
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  for (int i = 0; i < items.length; i++) ...[
                    if (i > 0) ...[
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 8),
                        child: Icon(
                          Icons.chevron_right,
                          size: 16,
                          color: separatorColor ?? Colors.grey[500],
                        ),
                      ),
                    ],
                    _buildBreadcrumbItem(
                      context,
                      items[i],
                      i == items.length - 1,
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSingleItem(BuildContext context, BreadcrumbItem item) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        border: Border(bottom: BorderSide(color: Colors.grey[300]!)),
      ),
      child: Row(
        children: [
          Icon(Icons.home, size: 16, color: textColor ?? Colors.grey[700]),
          const SizedBox(width: 8),
          Text(
            item.label,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
              color: textColor ?? Colors.grey[700],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBreadcrumbItem(
    BuildContext context,
    BreadcrumbItem item,
    bool isLast,
  ) {
    final textStyle = TextStyle(
      fontSize: 14,
      fontWeight: isLast ? FontWeight.bold : FontWeight.normal,
      color:
          isLast
              ? (textColor ?? Theme.of(context).primaryColor)
              : (textColor ?? Colors.grey[700]),
    );

    if (item.onTap != null && !isLast) {
      return InkWell(
        onTap: item.onTap,
        child: Text(item.label, style: textStyle),
      );
    }

    return Text(item.label, style: textStyle);
  }
}

/// Item individual de breadcrumb
class BreadcrumbItem {
  final String label;
  final VoidCallback? onTap;

  BreadcrumbItem({required this.label, this.onTap});
}

