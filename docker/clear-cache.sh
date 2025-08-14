#!/bin/bash

# Script to clear Laravel caches and test routes
echo "Clearing Laravel caches..."

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear

# Clear application cache
php artisan cache:clear

# Clear view cache
php artisan view:clear

# List all routes to verify health endpoints are registered
echo "Listing all routes..."
php artisan route:list | grep -i health

echo "Cache clearing completed."
