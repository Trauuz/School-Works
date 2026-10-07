#!/bin/sh
set -eu

render_port="${PORT:-10000}"

if [ -n "${APP_BASE_URL:-}" ]; then
    base_url="${APP_BASE_URL}"
elif [ -n "${RENDER_EXTERNAL_HOSTNAME:-}" ]; then
    base_url="https://${RENDER_EXTERNAL_HOSTNAME}"
else
    base_url="http://localhost:${render_port}"
fi

case "$base_url" in
    */) ;;
    *) base_url="${base_url}/" ;;
esac

cat > /var/www/html/.env <<EOF
CI_ENVIRONMENT = production
app.baseURL = '${base_url}'
EOF

sed -ri "s/^Listen [0-9]+$/Listen ${render_port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${render_port}>/" /etc/apache2/sites-available/000-default.conf

chown -R www-data:www-data /var/www/html/writable

php spark migrate --all

exec "$@"
