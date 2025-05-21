#!/bin/bash

cd /var/www

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-interaction --optimize-autoloader --ignore-platform-req=ext-intl

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
RETRY_COUNT=0
MAX_RETRIES=30
RETRY_INTERVAL=5

until mysqladmin ping -h mysql -u sql -psql --silent || [ $RETRY_COUNT -eq $MAX_RETRIES ]; do
  echo "Waiting for MySQL connection... (attempt $((RETRY_COUNT+1))/$MAX_RETRIES)"
  sleep $RETRY_INTERVAL
  RETRY_COUNT=$((RETRY_COUNT+1))
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
  echo "Failed to connect to MySQL after $MAX_RETRIES attempts"
  exit 1
fi

echo "MySQL is ready!"

# Run migrations and seeders
echo "Running database migrations..."
php artisan migrate --force
echo "Running database seeders..."
php artisan db:seed --force

# Generate application key if not exists
php artisan key:generate --no-interaction --force

# Create storage link
php artisan storage:link || true

# Clear cache
echo "Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set proper permissions
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

echo "Laravel application is ready!"

# Start PHP-FPM
php-fpm