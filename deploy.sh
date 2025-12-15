#!/bin/bash

# ==============================================================================
# KIT DE DESPLIEGUE AUTOMÁTICO - REDES SOCIALES (LARAVEL)
# Rama objetivo: mobile (Contiene fixes críticos de imágenes y dashboards)
# ==============================================================================

set -e # Detener script si hay error

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[$(date +'%H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

# 1. Solicitar IP Pública
echo -e "${YELLOW}Ingrese la IP Pública o Dominio de este servidor (ej: 192.168.1.50):${NC}"
read SERVER_IP

if [ -z "$SERVER_IP" ]; then
    error "La IP es obligatoria para configurar APP_URL."
fi

APP_URL="http://$SERVER_IP:8000"
log "Configurando despliegue para: $APP_URL"

# 2. Actualizar sistema e instalar dependencias
log "Actualizando sistema e instalando dependencias..."
sudo apt-get update
sudo apt-get install -y git zip unzip curl supervisor postgresql postgresql-contrib php-cli php-curl php-pgsql php-xml php-mbstring php-zip

# Instalar Composer si no existe
if ! command -v composer &> /dev/null; then
    log "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# 3. Preparar directorio
TARGET_DIR="/var/www/Redes-Sociales-Final"
REPO_URL="https://github.com/eileen230603/Redes-Sociales-Final.git"
BRANCH="mobile"

if [ -d "$TARGET_DIR" ]; then
    log "El directorio $TARGET_DIR ya existe. Actualizando..."
    cd $TARGET_DIR
    sudo git fetch origin
    sudo git checkout $BRANCH
    sudo git pull origin $BRANCH
else
    log "Clonando repositorio en $TARGET_DIR..."
    sudo git clone -b $BRANCH $REPO_URL $TARGET_DIR
    cd $TARGET_DIR
fi

# 4. Instalar dependencias de PHP
log "Instalando dependencias de Composer..."
sudo composer install --no-dev --optimize-autoloader --no-interaction

# 5. Configurar .env
log "Configurando entorno (.env)..."
if [ ! -f .env ]; then
    sudo cp .env.example .env
fi

# Generar Key si no existe
if ! grep -q "APP_KEY=base64" .env; then
    sudo php artisan key:generate
fi

# Configurar variables críticas usando sed
sudo sed -i "s|APP_ENV=local|APP_ENV=production|" .env
sudo sed -i "s|APP_DEBUG=true|APP_DEBUG=false|" .env
sudo sed -i "s|APP_URL=http://localhost|APP_URL=$APP_URL|" .env
sudo sed -i "s|DB_CONNECTION=mysql|DB_CONNECTION=pgsql|" .env
sudo sed -i "s|DB_HOST=127.0.0.1|DB_HOST=127.0.0.1|" .env
sudo sed -i "s|DB_PORT=3306|DB_PORT=5432|" .env
sudo sed -i "s|DB_DATABASE=laravel|DB_DATABASE=redes_sociales|" .env
sudo sed -i "s|DB_USERNAME=root|DB_USERNAME=redes_user|" .env
sudo sed -i "s|DB_PASSWORD=|DB_PASSWORD=secure_password|" .env

# 6. Configurar Base de Datos PostgreSQL
log "Configurando PostgreSQL..."
sudo -u postgres psql -c "CREATE DATABASE redes_sociales;" || log "Base de datos ya existe (ignorar error)"
sudo -u postgres psql -c "CREATE USER redes_user WITH PASSWORD 'secure_password';" || log "Usuario ya existe (ignorar error)"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE redes_sociales TO redes_user;"

# 7. Migraciones y Storage
log "Ejecutando migraciones..."
sudo php artisan migrate --force

log "Configurando Storage..."
sudo php artisan storage:link || true

# Asegurar permisos (CRÍTICO PARA IMÁGENES)
log "Ajustando permisos..."
sudo chown -R www-data:www-data $TARGET_DIR
sudo chmod -R 775 $TARGET_DIR/storage
sudo chmod -R 775 $TARGET_DIR/bootstrap/cache
# Permisos explícitos para la carpeta de imágenes pública
sudo mkdir -p $TARGET_DIR/storage/app/public
sudo chmod -R 775 $TARGET_DIR/storage/app/public

# 8. Configurar Supervisor
log "Configurando Supervisor..."
cat <<EOF | sudo tee /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-serve]
process_name=%(program_name)s
command=php $TARGET_DIR/artisan serve --host=0.0.0.0 --port=8000
autostart=true
autorestart=true
user=root
redirect_stderr=true
stdout_logfile=$TARGET_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart laravel-serve

# 9. Verificación Final
log "Verificando despliegue..."
sleep 5 # Esperar a que levante

HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}\n" http://localhost:8000/api/mega-eventos/publicos)

if [ "$HTTP_STATUS" == "200" ]; then
    log "✅ Backend desplegado exitosamente en: $APP_URL"
    echo -e "${GREEN}Endpoints probados:${NC}"
    echo -e "  - API: $APP_URL/api/mega-eventos/publicos (Status: $HTTP_STATUS)"
    echo -e "  - Storage: $APP_URL/storage/"
else
    error "❌ El backend no respondió correctamente (Status: $HTTP_STATUS). Revisa los logs en storage/logs/worker.log"
fi
