# Coolify 404 Fix Guide

## Problem
Your Laravel application is returning 404 errors in Coolify because of port configuration issues.

## Root Cause
- Nginx is running on port 8080 (as shown in your `cat /proc/net/tcp` output)
- But nginx configuration was set to listen on port 80
- Coolify expects the application on port 8080

## Solution Applied

### 1. Fixed Nginx Configuration
Updated `docker/nginx.conf` to listen on port 8080:
```nginx
server {
    listen 8080;  # Changed from 80 to 8080
    # ... rest of config
}
```

### 2. Updated Dockerfile
Changed exposed port from 80 to 8080:
```dockerfile
EXPOSE 8080  # Changed from 80 to 8080
```

### 3. Added Health Check Endpoint
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

1. **Rebuild the Docker image** in Coolify
2. **Redeploy the application**
3. **Check the logs** for any errors
4. **Test the endpoints** using the commands above

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

## Available Endpoints

After fix, these endpoints should work:
- `GET /up` - Health check (returns "OK")
- `GET /api/v1/health` - Detailed health check
- `GET /api/v1/auth/*` - Authentication endpoints
- `GET /api/documentation` - API documentation
- `GET /` - Main application

## Files Modified
- `docker/nginx.conf` - Changed port from 80 to 8080
- `Dockerfile` - Changed EXPOSE from 80 to 8080
- `routes/web.php` - Added `/up` health check endpoint
- `coolify-deploy.sh` - Created deployment script
