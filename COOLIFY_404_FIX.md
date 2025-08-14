# Coolify 404 Fix Guide

## Problem
Your Laravel application is returning 404 errors in Coolify because of port configuration issues.

## Root Cause Analysis from Deployment Logs

### Issues Found:
1. **Missing Port Mapping**: `docker-compose.yml` was missing the `ports` configuration
2. **Permission Errors**: Cache clear failed due to insufficient permissions
3. **Container Configuration**: Port mismatch between nginx (8080) and docker-compose (no mapping)

## Solution Applied

### 1. Fixed Docker Compose Configuration
Updated `docker-compose.yml` to include port mapping:
```yaml
services:
  app:
    build: .
    restart: unless-stopped
    ports:
      - "8080:8080"  # Added this line
    healthcheck:
      test: ["CMD", "/usr/local/bin/healthcheck.sh"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 60s
```

### 2. Fixed Nginx Configuration
Updated `docker/nginx.conf` to listen on port 8080:
```nginx
server {
    listen 8080;  # Changed from 80 to 8080
    # ... rest of config
}
```

### 3. Fixed Dockerfile Permissions
Updated `Dockerfile` to fix permission issues:
```dockerfile
# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Create necessary directories with proper permissions
RUN mkdir -p /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views
RUN chown -R www-data:www-data /app/storage
RUN chmod -R 775 /app/storage
```

### 4. Added Health Check Endpoint
Added `/up` endpoint in `routes/web.php` for Coolify health checks:
```php
Route::get('/up', function () {
    return response('OK', 200);
});
```

## Coolify Configuration

### In Coolify Dashboard:
1. **Port**: Set to `8080`
2. **Health Check Path**: Set to `/up`
3. **Health Check Port**: Set to `8080`

### Environment Variables:
Make sure these are set in Coolify:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

## Testing

### Test Health Endpoint:
```bash
curl http://localhost:8080/up
# Should return: OK
```

### Test API Health:
```bash
curl http://localhost:8080/api/v1/health
# Should return JSON with health status
```

### Test Main Application:
```bash
curl http://localhost:8080/
# Should return Laravel welcome page
```

## Deployment Steps

1. **Commit and push** the updated files to your repository
2. **Rebuild the Docker image** in Coolify
3. **Redeploy the application**
4. **Check the logs** for any errors
5. **Test the endpoints** using the commands above

## Troubleshooting

### If still getting 404:
1. Check Coolify logs for nginx errors
2. Verify the port is set to 8080 in Coolify
3. Ensure the health check path is `/up`
4. Check if PHP-FPM is running: `ps aux | grep php-fpm`

### If health check fails:
1. Check if the application is accessible: `curl http://localhost:8080/up`
2. Check nginx error logs: `docker logs <container-name> 2>&1 | grep nginx`
3. Check PHP-FPM logs: `docker logs <container-name> 2>&1 | grep php-fpm`

### If cache clear fails:
1. Check storage permissions: `ls -la /app/storage`
2. Ensure www-data owns the directories: `chown -R www-data:www-data /app/storage`

## Available Endpoints

After fix, these endpoints should work:
- `GET /up` - Health check (returns "OK")
- `GET /api/v1/health` - Detailed health check
- `GET /api/v1/auth/*` - Authentication endpoints
- `GET /api/documentation` - API documentation
- `GET /` - Main application

## Files Modified
- `docker-compose.yml` - Added port mapping 8080:8080
- `docker/nginx.conf` - Changed port from 80 to 8080
- `Dockerfile` - Fixed permissions and changed EXPOSE to 8080
- `routes/web.php` - Added `/up` health check endpoint
- `coolify-deploy.sh` - Created deployment script
- `test-deployment.sh` - Created testing script

## Key Fix Summary
The main issue was that `docker-compose.yml` was missing the port mapping. Without this, Coolify couldn't route traffic to your application, causing 404 errors. The fix ensures:
1. Port 8080 is properly exposed
2. Permissions are correctly set
3. Health checks work properly
4. All endpoints are accessible
