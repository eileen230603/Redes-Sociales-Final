#!/bin/bash

# ==============================================================================
# KIT DE DESPLIEGUE DOCKER - CONECTANDOEVENTOS.WEBSITE
# ==============================================================================

set -e

# Configuración
DOMAIN="conectandoeventos.website"
REPO_URL="https://github.com/eileen230603/Redes-Sociales-Final.git"
BRANCH="mobile"
TARGET_DIR="~/Redes-Sociales-Docker"

# Colores
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}[1] Preparando directorio...${NC}"
mkdir -p $TARGET_DIR
cd $TARGET_DIR

# Clonar o actualizar
if [ -d ".git" ]; then
    echo -e "${GREEN}[2] Actualizando código...${NC}"
    git fetch origin
    git checkout $BRANCH
    git pull origin $BRANCH
else
    echo -e "${GREEN}[2] Clonando repositorio...${NC}"
    git clone -b $BRANCH $REPO_URL .
fi

echo -e "${GREEN}[2.5] Copiando configuración Docker...${NC}"
# Copiar archivos de configuración desde el directorio padre (donde está el script)
# Asumimos que el script se ejecuta desde ~ o donde están los archivos subidos
cp ../Dockerfile .
cp ../docker-compose.yml .
cp -r ../nginx .

# Generar .env si no existe (para Docker Compose)
if [ ! -f .env ]; then
    cp .env.example .env
    # Generar key temporalmente para ponerla en el .env
    # En producción real, esto debería manejarse mejor, pero para este script rápido:
    sed -i "s|APP_KEY=|APP_KEY=base64:$(openssl rand -base64 32)|" .env
fi

echo -e "${GREEN}[3] Levantando contenedores...${NC}"
# Construir y levantar
docker compose up -d --build

echo -e "${GREEN}[4] Ejecutando tareas post-deploy...${NC}"
# Instalar dependencias dentro del contenedor (por si acaso el build falló en algo)
docker compose exec -T app composer install --no-dev --optimize-autoloader

# Generar key si no se pasó correctamente
docker compose exec -T app php artisan key:generate --force

# Migraciones
docker compose exec -T app php artisan migrate --force

# Storage Link
docker compose exec -T app php artisan storage:link

# Permisos
docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache
docker compose exec -T app chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}====================================================${NC}"
echo -e "${GREEN}🚀 DESPLIEGUE DOCKER COMPLETADO${NC}"
echo -e "La aplicación está corriendo en el puerto: 8095"
echo -e "Ahora debes configurar el Nginx Proxy Manager para apuntar:"
echo -e "Dominio: $DOMAIN"
echo -e "Forward Host: redes-sociales-nginx (o la IP interna de Docker)"
echo -e "Forward Port: 80"
echo -e "${GREEN}====================================================${NC}"
