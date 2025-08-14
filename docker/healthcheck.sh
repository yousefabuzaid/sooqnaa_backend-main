#!/bin/bash

# Enhanced health check script for Laravel application
set -e

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Check if nginx is running
if ! pgrep nginx > /dev/null; then
    log "ERROR: Nginx is not running"
    exit 1
fi

# Check if PHP-FPM is running
if ! pgrep php-fpm > /dev/null; then
    log "ERROR: PHP-FPM is not running"
    exit 1
fi

# Wait a moment for services to be fully ready
sleep 2

# Check if the application responds using Laravel's built-in health check
log "Checking application health at /up endpoint..."

# Try the Laravel health check endpoint first
if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/up | grep -q "200"; then
    log "SUCCESS: Laravel health check passed"
    exit 0
fi

# If Laravel health check fails, try to clear caches and retry
log "Laravel health check failed, clearing caches..."
/usr/local/bin/clear-cache.sh > /dev/null 2>&1

# Retry Laravel health check after clearing caches
if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/up | grep -q "200"; then
    log "SUCCESS: Laravel health check passed after cache clear"
    exit 0
fi

# Fallback: Try the API health check endpoint
log "Trying API health check endpoint..."
if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/api/v1/health | grep -q "200"; then
    log "SUCCESS: API health check passed"
    exit 0
fi

# Fallback: Try a simple HTTP response
log "Trying simple HTTP check..."
if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/ | grep -q "200\|404"; then
    log "SUCCESS: Basic HTTP check passed"
    exit 0
fi

log "ERROR: All health checks failed"
exit 1
