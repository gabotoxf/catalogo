# --- Etapa 1: Frontend ---
FROM node:20-slim AS frontend-build
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# --- Etapa 2: Backend ---
FROM php:8.2-fpm

# Instalar nginx y dependencias
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copiar backend y frontend
COPY backend/ .
COPY --from=frontend-build /app/frontend/dist/ ./public/

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx
RUN echo 'server { \n\
    listen 80; \n\
    root /var/www/html/public; \n\
    index index.php; \n\
    location / { \n\
        try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_index index.php; \n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
        include fastcgi_params; \n\
    } \n\
}' > /etc/nginx/sites-available/default

ENV APP_ENV=production
ENV APP_DEBUG=false

# Script de inicio
RUN printf '#!/bin/bash\nset -e\n\
cd /var/www/html\n\
php artisan migrate --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php-fpm -D\n\
sed -i "s/listen 80/listen ${PORT:-80}/g" /etc/nginx/sites-available/default\n\
nginx -g "daemon off;"\n\
' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]