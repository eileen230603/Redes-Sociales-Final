# Configuración de URL Pública

Para que los QR codes y enlaces compartidos funcionen desde otros dispositivos en la misma red, agrega esta variable a tu archivo `.env`:

```env
PUBLIC_APP_URL=http://10.26.0.215:8000
```

## Instrucciones:

1. Abre tu archivo `.env` en la raíz del proyecto
2. Agrega la línea: `PUBLIC_APP_URL=http://10.26.0.215:8000`
3. Reemplaza `10.26.0.215` con la IP de tu computadora en la red local
4. Guarda el archivo
5. Reinicia el servidor Laravel con: `php artisan serve --host=0.0.0.0 --port=8000`

## Nota importante:

- Si no defines `PUBLIC_APP_URL` en el `.env`, se usará el valor por defecto: `http://10.26.0.215:8000`
- Para encontrar tu IP local en Windows: `ipconfig` (busca "IPv4 Address")
- Para encontrar tu IP local en Linux/Mac: `ifconfig` o `ip addr`

