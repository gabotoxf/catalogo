# --- Etapa 1: Construcción del Frontend ---
FROM node:20-slim AS frontend-build
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
# Construir los archivos de producción
RUN npm run build

# --- Etapa 2: Backend y Servidor Web ---
FROM php:8.2-apache

# Instalación de dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Habilitar mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del backend
COPY backend/ ./

# Copiar el build del frontend a la carpeta pública de Laravel
COPY --from=frontend-build /app/frontend/dist/ ./public/

# Instalación de Composer y dependencias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Configurar el DocumentRoot de Apache para que apunte a la carpeta public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar Apache para escuchar en el puerto dinámico ($PORT), por defecto 80
RUN sed -i 's/80/${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Ajustar permisos para carpetas de almacenamiento y caché de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Variables de entorno para producción
ENV APP_ENV=production
ENV APP_DEBUG=false

# Crear script de inicio que corre migraciones y luego inicia Apache
RUN printf '#!/bin/bash\nset -e\ncd /var/www/html\nphp artisan migrate --force\nphp artisan config:cache\nphp artisan route:cache\napache2-foreground' > /start.sh && chmod +x /start.sh

# Exponer puerto
EXPOSE 80

# Usar script de inicio
CMD ["/start.sh"]