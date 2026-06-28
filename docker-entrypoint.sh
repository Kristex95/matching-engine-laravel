#!/bin/sh
set -e

echo "Installing dependencies..."
composer install

echo "Generating unique app key..."
php artisan key:generate

echo "Running migrations..."
php artisan migrate

echo "Clearing cache..."
php artisan config:clear

echo "Starting Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf

echo "Starting Reverb..."
php artisan reverb:start --port=8081
