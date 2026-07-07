#!/bin/sh
set -e

echo "==> Démarrage SmartTransport API"

if [ -n "$RENDER_EXTERNAL_URL" ] && [ -z "$APP_URL" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi

if [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    export DB_URL="$DATABASE_URL"
fi

echo "==> Attente de la base de données..."
attempt=0
until php artisan migrate --force --no-interaction 2>/dev/null; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 15 ]; then
        echo "==> Échec : la base de données n'est pas accessible."
        exit 1
    fi
    echo "==> Nouvelle tentative dans 3s... ($attempt/15)"
    sleep 3
done

if [ "$SEED_DATABASE" = "true" ]; then
    echo "==> Seeding de la base de données..."
    php artisan db:seed --force --no-interaction
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Serveur prêt sur le port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
