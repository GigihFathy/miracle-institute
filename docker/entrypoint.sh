#!/bin/sh
set -e

APP_KEY_FILE="/var/www/html/storage/docker/app.key"

if [ -z "${APP_KEY:-}" ]; then
    mkdir -p "$(dirname "$APP_KEY_FILE")"

    if [ ! -f "$APP_KEY_FILE" ]; then
        php artisan key:generate --show > "$APP_KEY_FILE"
    fi

    export APP_KEY="$(cat "$APP_KEY_FILE")"
fi

exec php artisan serve --host=0.0.0.0 --port=8000
