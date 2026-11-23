#!/bin/sh
set -eu

: "${APP_KEY:?Set APP_KEY to a Laravel base64 application key in Render.}"
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:${PORT:-10000}}}"
sed -i "s/^Listen .*/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf
php artisan config:cache --no-interaction
php artisan migrate --force --no-interaction
php artisan view:cache --no-interaction
chown -R www-data:www-data storage bootstrap/cache
exec docker-php-entrypoint "$@"
