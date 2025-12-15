#!/bin/bash

# ==============================================================================
# KIT DE DESPLIEGUE FINAL - CONECTANDOEVENTOS.WEBSITE
# Rama: mobile (Fixes de imágenes y UI)
# Servidor: Nginx + PHP-FPM (Producción Real)
# ==============================================================================

set -e # Detener si hay error

# Variables de Configuración
DOMAIN="conectandoeventos.website"
APP_URL="http://$DOMAIN"
REPO_URL="https://github.com/eileen230603/Redes-Sociales-Final.git"
BRANCH="mobile"
TARGET_DIR="/var/www/Redes-Sociales-Final"
DB_NAME="redes_sociales"
DB_USER="redes_user"
DB_PASS="secure_password" # Cambiar si se desea

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

log() { echo -e "${GREEN}[$(date +'%H:%M:%S')] $1${NC}"; }
error() { echo -e "${RED}[ERROR] $1${NC}"; exit 1; }

log "Iniciando despliegue para: $DOMAIN"

# 1. Preparar Sistema y Dependencias (Nginx + PHP-FPM)
log "Actualizando sistema e instalando stack LEMP..."
sudo apt-get update
sudo apt-get install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php # Asegurar PHP reciente
sudo apt-get update
sudo apt-get install -y nginx git zip unzip curl postgresql postgresql-contrib \
    php8.2-fpm php8.2-cli php8.2-curl php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-zip php8.2-gd

# Instalar Composer
if ! command -v composer &> /dev/null; then
    log "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# 2. Clonar/Actualizar Repositorio (Rama mobile)
if [ -d "$TARGET_DIR" ]; then
    log "Actualizando repositorio..."
    cd $TARGET_DIR
    sudo git fetch origin
    sudo git checkout $BRANCH
    sudo git pull origin $BRANCH
else
    log "Clonando repositorio..."
    sudo git clone -b $BRANCH $REPO_URL $TARGET_DIR
    cd $TARGET_DIR
fi

# 3. Instalar Dependencias Laravel
log "Ejecutando Composer Install..."
sudo composer install --no-dev --optimize-autoloader --no-interaction

# 4. Configurar Entorno (.env)
log "Configurando .env..."
if [ ! -f .env ]; then
    sudo cp .env.example .env
fi

# Ajustes con sed para producción
sudo sed -i "s|APP_ENV=local|APP_ENV=production|" .env
sudo sed -i "s|APP_DEBUG=true|APP_DEBUG=false|" .env
sudo sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
sudo sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=pgsql|" .env
sudo sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|" .env
sudo sed -i "s|DB_PORT=.*|DB_PORT=5432|" .env
sudo sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
sudo sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
sudo sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env

# Generar Key si falta
if ! grep -q "APP_KEY=base64" .env; then
    sudo php artisan key:generate
fi

# 5. Base de Datos
log "Configurando PostgreSQL..."
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;" || true
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';" || true
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;" || true
# Permisos esquema public (necesario en PG 15+)
sudo -u postgres psql -d $DB_NAME -c "GRANT ALL ON SCHEMA public TO $DB_USER;" || true

log "Ejecutando migraciones..."
sudo php artisan migrate --force

# 6. Storage y Permisos (CRÍTICO)
log "Configurando Storage y Permisos..."
sudo php artisan storage:link || true

# Asegurar estructura de carpetas
sudo mkdir -p storage/app/public
sudo mkdir -p storage/framework/{sessions,views,cache}
sudo mkdir -p bootstrap/cache

# Permisos recursivos para Nginx (www-data)
sudo chown -R www-data:www-data $TARGET_DIR
sudo find $TARGET_DIR -type f -exec chmod 644 {} \;
sudo find $TARGET_DIR -type d -exec chmod 755 {} \;
sudo chmod -R 775 $TARGET_DIR/storage
sudo chmod -R 775 $TARGET_DIR/bootstrap/cache

# 7. Configurar Nginx
log "Configurando Nginx..."
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
FPM_SOCK="/run/php/php$PHP_VERSION-fpm.sock"

cat <<EOF | sudo tee /etc/nginx/sites-available/$DOMAIN
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $TARGET_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    # Configuración para PHP
    location ~ \.php$ {
        fastcgi_pass unix:$FPM_SOCK;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Configuración especial para imágenes (CORS para Flutter Web)
    location /storage/ {
        add_header 'Access-Control-Allow-Origin' '*';
        add_header 'Access-Control-Allow-Methods' 'GET, OPTIONS';
        try_files \$uri \$uri/ =404;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Activar sitio
sudo ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx

# 8. Verificación Final
log "Esperando reinicio de servicios..."
sleep 5

log "Verificando endpoints..."

# Verificar API
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $APP_URL/api/mega-eventos/publicos)
if [ "$HTTP_CODE" == "200" ]; then
    log "✅ API accesible correctamente ($HTTP_CODE)"
else
    log "⚠️ Advertencia: API retornó $HTTP_CODE (Puede ser normal si no hay datos, pero verifica logs)"
fi

# Verificar Storage (debería dar 404 si no hay archivo, pero 200/404 es mejor que 403/500)
STORAGE_CODE=$(curl -s -o /dev/null -w "%{http_code}" $APP_URL/storage/test.txt)
if [ "$STORAGE_CODE" != "403" ] && [ "$STORAGE_CODE" != "500" ]; then
    log "✅ Storage configurado (Código $STORAGE_CODE - Permisos OK)"
else
    error "❌ Error en Storage: Retornó $STORAGE_CODE"
fi

echo -e "${GREEN}====================================================${NC}"
echo -e "${GREEN}🚀 DESPLIEGUE COMPLETADO EXITOSAMENTE${NC}"
echo -e "URL Backend: ${YELLOW}$APP_URL${NC}"
echo -e "Rama: ${YELLOW}$BRANCH${NC}"
echo -e "Servidor Web: ${YELLOW}Nginx${NC}"
echo -e "${GREEN}====================================================${NC}"
