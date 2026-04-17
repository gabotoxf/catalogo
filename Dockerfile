# =============================================================================
# Stage 1: Frontend build
# =============================================================================
FROM node:20-slim AS frontend-build

WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# =============================================================================
# Stage 2: PHP-FPM — Laravel application
# =============================================================================
FROM php:8.2-fpm-alpine AS php-fpm

# Install system dependencies and PHP extensions required by Laravel
RUN apk add --no-cache \
        libpng-dev \
        oniguruma-dev \
        libxml2-dev \
        libzip-dev \
        icu-dev \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy backend source and install PHP dependencies
COPY backend/ ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set correct ownership and permissions for Laravel writable directories
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

# =============================================================================
# Stage 3: Nginx — final production image
# =============================================================================
FROM nginx:alpine

# Install supervisor to manage both PHP-FPM and Nginx processes
RUN apk add --no-cache supervisor

WORKDIR /var/www/html

# Copy the entire PHP-FPM runtime (binary, extensions, config) from Stage 2
COPY --from=php-fpm /usr/local /usr/local
COPY --from=php-fpm /usr/lib /usr/lib
COPY --from=php-fpm /etc/php* /etc/php/

# Copy the full Laravel application (including vendor/) from the php-fpm stage
COPY --from=php-fpm /var/www/html /var/www/html

# Copy Vue.js frontend dist into Laravel's public directory
COPY --from=frontend-build /app/frontend/dist/ /var/www/html/public/

# Fix ownership so nginx/php-fpm can read and write where needed
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx site configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# PHP-FPM pool configuration (Unix socket, runs as nginx user)
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 80

CMD ["/start.sh"]
