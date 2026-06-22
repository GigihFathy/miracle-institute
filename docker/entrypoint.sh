#!/bin/sh
set -e

APP_KEY_FILE="/var/www/html/storage/docker/app.key"

mkdir -p \
    /var/www/html/storage/docker \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/images/thumbnail

if [ -z "$(ls -A /var/www/html/public/images/thumbnail 2>/dev/null)" ] && [ -d /opt/app-defaults/thumbnails ]; then
    cp -a /opt/app-defaults/thumbnails/. /var/www/html/public/images/thumbnail/ 2>/dev/null || true
fi

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/images/thumbnail

chmod -R 775 /var/www/html/public/images/thumbnail

if [ -z "${APP_KEY:-}" ]; then
    if [ ! -f "$APP_KEY_FILE" ]; then
        php artisan key:generate --show > "$APP_KEY_FILE"
    fi

    export APP_KEY="$(cat "$APP_KEY_FILE")"
fi

if [ ! -L /var/www/html/public/storage ] && [ ! -e /var/www/html/public/storage ]; then
    php artisan storage:link
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
