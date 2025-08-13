#!/bin/bash

echo "=== Laravel Docker Compose Deployment Script ==="

# Stop any existing containers
echo "Stopping existing containers..."
docker-compose down --remove-orphans 2>/dev/null || true

# Remove any containers using port 80
echo "Cleaning up port 80..."
docker ps -q --filter "publish=80" | xargs -r docker stop 2>/dev/null || true
docker ps -aq --filter "publish=80" | xargs -r docker rm 2>/dev/null || true

# Build and start the new container
echo "Building and starting new container..."
docker-compose up --build -d

# Wait for container to be ready
echo "Waiting for container to be ready..."
sleep 10

# Run Laravel setup commands
echo "Running Laravel setup..."
docker-compose exec -T app php artisan config:clear || true
docker-compose exec -T app php artisan route:clear || true
docker-compose exec -T app php artisan view:clear || true
docker-compose exec -T app php artisan cache:clear || true

echo "Deployment completed successfully!"
