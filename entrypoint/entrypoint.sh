#!/bin/sh

set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "Checking Database"

until php -r '
try {
    new PDO(
        "mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT") . ";dbname=" . getenv("DB_DATABASE"),
        getenv("DB_USERNAME"),
        getenv("DB_PASSWORD")
    );
    echo "DB Healthy\n";
} catch(Exception $e) {
    exit(1);
}
'; do
    echo "Database is unavailable - Sleeping"
    sleep 2
done


if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

php artisan optimize:clear || true

php artisan config:clear

echo "Running migrations"
php artisan migrate --force

echo "Starting server..."
exec php-fpm -F