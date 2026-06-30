#!/bin/bash

set -e

echo "==> Verifying required directories exist..."
mkdir -p public/build
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "==> Installing Composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "==> Installing Node.js dependencies..."
npm ci --silent

echo "==> Building frontend assets..."
npm run build

echo "==> Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build complete!"
