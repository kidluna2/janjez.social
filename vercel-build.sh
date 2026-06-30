#!/bin/bash

# Vercel build script for Laravel + Vue + Vite application

set -e

echo "==> Installing Composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "==> Installing Node.js dependencies..."
npm ci --silent

echo "==> Building frontend assets..."
npm run build

echo "==> Creating required Laravel directories..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "==> Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build complete!"
