#!/bin/bash

# Coolify Deployment Script for Sooqnaa Backend
set -e

echo "🚀 Starting Coolify deployment..."

# Clear any existing caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate API documentation
echo "📚 Generating API documentation..."
php artisan l5-swagger:generate

# Test the health endpoint
echo "🏥 Testing health endpoint..."
curl -f http://localhost:8080/up || echo "Health check failed, but continuing..."

echo "✅ Deployment completed successfully!"
echo "🌐 Application should be available on port 8080"
echo "🏥 Health check endpoint: http://localhost:8080/up"
echo "📚 API Documentation: http://localhost:8080/api/documentation"
