#!/bin/bash

# Simple health check script - alternative version
# This script performs basic checks without complex HTTP requests

# Check if nginx is running
if ! pgrep nginx > /dev/null; then
    echo "Nginx is not running"
    exit 1
fi

# Check if PHP-FPM is running
if ! pgrep php-fpm > /dev/null; then
    echo "PHP-FPM is not running"
    exit 1
fi

# Check if the application directory exists and is accessible
if [ ! -d "/app" ]; then
    echo "Application directory not found"
    exit 1
fi

# Check if the public directory exists
if [ ! -d "/app/public" ]; then
    echo "Public directory not found"
    exit 1
fi

# Check if index.php exists
if [ ! -f "/app/public/index.php" ]; then
    echo "index.php not found"
    exit 1
fi

# Simple HTTP check - just verify the server responds
if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/ > /dev/null 2>&1; then
    echo "Application is healthy"
    exit 0
else
    echo "Application is not responding"
    exit 1
fi
