# --- Etapa 1: Frontend ---
FROM node:20-slim AS frontend-build
WORKDIR /app/frontend

# Mismo origen por defecto: el monolito sirve API + frontend desde un solo
# dominio, así no hace falta conocer la URL de Render en tiempo de build.
# Se pueden sobreescribir con --build-arg si se separa el frontend.
ARG VITE_API_BASE_URL=/api
ARG VITE_IMAGE_BASE_URL=/assets/img/Productos
ARG VITE_CAT_IMAGE_BASE_URL=/assets/img/Categorias

ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
ENV VITE_IMAGE_BASE_URL=$VITE_IMAGE_BASE_URL
ENV VITE_CAT_IMAGE_BASE_URL=$VITE_CAT_IMAGE_BASE_URL

COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# --- Etapa 2: Backend ---
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY backend/ .
COPY --from=frontend-build /app/frontend/dist/ ./public/
RUN mkdir -p public/assets/img/Productos public/assets/img/Categorias \
    storage/framework/sessions storage/framework/views storage/framework/cache \
    storage/logs bootstrap/cache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Config ya preparada para $PORT (Render/Railway lo inyectan, default 80)
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh

RUN rm -f /etc/nginx/sites-enabled/default \
    && chmod +x /start.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache public/assets/img

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 80

CMD ["/start.sh"]
