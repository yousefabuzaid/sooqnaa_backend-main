# Health Endpoint 404 Fix Guide

## Problem
The health endpoint `/api/v1/health` is returning a 404 "Page Not Found" error.

## Root Cause Analysis
The issue is likely caused by one of the following:

1. **Missing Controller Import**: The `HealthController` was not properly imported in `routes/api.php`
2. **Route Caching**: Laravel routes might be cached with old configuration
3. **Middleware Issues**: Some middleware might be blocking the route
4. **File Permissions**: The controller file might not be accessible

## Fixes Applied

### 1. Fixed Controller Import
I've added the proper import statement in `routes/api.php`:
```php
use App\Http\Controllers\Api\HealthController;
```

### 2. Updated Route Definitions
Changed from full namespace to imported class:
```php
// Before
Route::get('/health', [App\Http\Controllers\Api\HealthController::class, 'check'])

// After  
Route::get('/health', [HealthController::class, 'check'])
```

### 3. Added Cache Clearing Script
Created `docker/clear-cache.sh` to clear Laravel caches:
- Route cache
- Config cache
- Application cache
- View cache

### 4. Enhanced Health Check Script
Updated the health check to clear caches if needed and retry.

## Implementation Steps

### Step 1: Rebuild the Docker Image
```bash
docker-compose build
```

### Step 2: Restart the Container
```bash
docker-compose down
docker-compose up -d
```

### Step 3: Test the Health Endpoints

#### Test 1: Laravel Built-in Health Check
```bash
curl -v http://your-domain.com/up
```
Expected: `200 OK`

#### Test 2: Legacy Health Check
```bash
curl -v http://your-domain.com/api/health
```
Expected: JSON response with health status

#### Test 3: V1 Health Check
```bash
curl -v http://your-domain.com/api/v1/health
```
Expected: JSON response with detailed health status

### Step 4: Manual Cache Clearing (if needed)
If the endpoints still return 404, manually clear caches:
```bash
docker exec <container_name> /usr/local/bin/clear-cache.sh
```

## Available Health Endpoints

Your application now has these health check endpoints:

1. **`/up`** - Laravel built-in health check
   - Returns: `200 OK` if application is running
   - No authentication required

2. **`/api/health`** - Legacy health check
   - Returns: JSON with basic health status
   - No authentication required

3. **`/api/v1/health`** - V1 API health check
   - Returns: Detailed JSON with database, cache, and storage status
   - No authentication required

## Troubleshooting Commands

### Check if Routes are Registered
```bash
docker exec <container_name> php artisan route:list | grep health
```

### Check Laravel Logs
```bash
docker exec <container_name> tail -f /app/storage/logs/laravel.log
```

### Test Health Controller Directly
```bash
docker exec <container_name> php artisan tinker
```
Then run:
```php
app(App\Http\Controllers\Api\HealthController::class)->check();
```

### Check File Permissions
```bash
docker exec <container_name> ls -la /app/app/Http/Controllers/Api/HealthController.php
```

## Expected Health Check Response

The `/api/v1/health` endpoint should return:
```json
{
    "status": "healthy",
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "version": "1.0.0",
    "services": {
        "database": "healthy",
        "cache": "healthy", 
        "storage": "healthy",
        "environment": "production",
        "debug": false
    }
}
```

## If Still Getting 404

1. **Check nginx logs**:
   ```bash
   docker exec <container_name> tail -f /var/log/nginx/error.log
   ```

2. **Check PHP-FPM logs**:
   ```bash
   docker exec <container_name> tail -f /var/log/supervisor/php-fpm.err.log
   ```

3. **Verify nginx configuration**:
   ```bash
   docker exec <container_name> nginx -t
   ```

4. **Restart services**:
   ```bash
   docker exec <container_name> supervisorctl restart all
   ```

## Alternative Health Check URLs

If the V1 endpoint still doesn't work, you can use these alternatives:

1. **Laravel built-in**: `http://your-domain.com/up`
2. **Legacy endpoint**: `http://your-domain.com/api/health`
3. **Simple HTTP check**: Just verify the server responds

## Update Health Check Script

The health check script now tries multiple endpoints in order:
1. `/up` (Laravel built-in)
2. `/api/health` (Legacy)
3. `/api/v1/health` (V1)
4. Basic HTTP response

This ensures maximum compatibility and reliability.
