#!/bin/sh
# Production startup script for the PHP-FPM + Nginx container.
# Runs once: migrations → config/route cache → starts both daemons.
set -e

APP_DIR="/var/www/html"
PHP_FPM_CONF="/etc/php82/php-fpm.conf"
NGINX_CONF="/etc/nginx/conf.d/default.conf"

# ---------------------------------------------------------------------------
# 1. Resolve the listening port (Railway injects $PORT; default to 80)
# ---------------------------------------------------------------------------
PORT="${PORT:-80}"
echo "[start] Configuring Nginx to listen on port ${PORT}"
sed -i "s/\${PORT:-80}/${PORT}/g" "${NGINX_CONF}"

# ---------------------------------------------------------------------------
# 2. Run Laravel bootstrap commands
# ---------------------------------------------------------------------------
cd "${APP_DIR}"

echo "[start] Running database migrations..."
php artisan migrate --force

echo "[start] Caching Laravel configuration..."
php artisan config:cache

echo "[start] Caching Laravel routes..."
php artisan route:cache

# ---------------------------------------------------------------------------
# 3. Start PHP-FPM in the background
# ---------------------------------------------------------------------------
echo "[start] Starting PHP-FPM..."
php-fpm -F -R -y "${PHP_FPM_CONF}" &
PHP_FPM_PID=$!

# Give PHP-FPM a moment to create the socket before Nginx tries to connect
sleep 1

# ---------------------------------------------------------------------------
# 4. Start Nginx in the foreground (keeps the container alive)
# ---------------------------------------------------------------------------
echo "[start] Starting Nginx on port ${PORT}..."
exec nginx -g "daemon off;"
