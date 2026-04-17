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

# Install PHP-FPM and all extensions needed by Laravel (Alpine packages)
RUN apk add --no-cache \
        php82 \
        php82-pdo \
        php82-pdo_mysql \
        php82-mbstring \
        php82-exif \
        php82-pcntl \
        php82-bcmath \
        php82-gd \
        php82-zip \
        php82-intl \
        php82-tokenizer \
        php82-xml \
        php82-xmlwriter \
        php82-xmlreader \
        php82-dom \
        php82-fileinfo \
        php82-openssl \
        php82-session \
        php82-ctype \
        php82-curl \
        php82-phar \
        php82-iconv \
        php82-sodium \
        php82-fpm \
    && ln -sf /usr/bin/php82 /usr/local/bin/php \
    && ln -sf /usr/sbin/php-fpm82 /usr/local/sbin/php-fpm

WORKDIR /var/www/html

# Copy the full Laravel application (including vendor/) from the php-fpm stage
COPY --from=php-fpm /var/www/html /var/www/html

# Copy Vue.js frontend dist into Laravel's public directory
COPY --from=frontend-build /app/frontend/dist/ /var/www/html/public/

# Fix ownership so nginx/php-fpm can read and write where needed
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx site configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# PHP-FPM pool configuration (Unix socket, runs as nginx user)
COPY docker/php-fpm.conf /etc/php82/php-fpm.d/www.conf

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 80

CMD ["/start.sh"]
