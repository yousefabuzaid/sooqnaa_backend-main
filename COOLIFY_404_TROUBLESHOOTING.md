# Coolify 404 Troubleshooting Guide

## Problem
The health endpoint `/api/v1/health` is still returning 404 after fixes.

## Immediate Steps to Take

### Step 1: Rebuild in Coolify
1. Go to your Coolify dashboard
2. Navigate to your application
3. Click **"Build & Deploy"** to rebuild with the latest fixes
4. Wait for the build to complete

### Step 2: Access Container Terminal
Once the new build is deployed, you need to access the container terminal:

**Option A: Through Coolify Dashboard**
- Go to your application in Coolify
- Look for "Terminal" or "Console" option
- Access the container shell

**Option B: Through SSH (if available)**
```bash
ssh your-server
docker exec -it <container_name> bash
```

### Step 3: Run Debugging Scripts

Once inside the container, run these commands:

```bash
# Run comprehensive debugging
/usr/local/bin/debug-routes.sh

# Run simple route test
/usr/local/bin/test-simple-route.sh

# Clear all caches manually
/usr/local/bin/clear-cache.sh
```

### Step 4: Check Specific Issues

#### A. Check if Routes are Registered
```bash
php artisan route:list | grep health
```

**Expected Output:**
```
GET|HEAD  api/health ................ health.legacy › HealthController@check
GET|HEAD  api/v1/health ............. health › HealthController@check
```

#### B. Check Laravel Logs
```bash
tail -f /app/storage/logs/laravel.log
```

#### C. Test Controller Directly
```bash
php artisan tinker
```
Then run:
```php
app(App\Http\Controllers\Api\HealthController::class)->check();
```

#### D. Check File Permissions
```bash
ls -la /app/app/Http/Controllers/Api/HealthController.php
ls -la /app/routes/api.php
```

## Common Issues and Solutions

### Issue 1: Routes Not Loading
**Symptoms:** `php artisan route:list` shows no health routes

**Solutions:**
1. Clear route cache:
   ```bash
   php artisan route:clear
   ```

2. Check for syntax errors:
   ```bash
   php -l /app/routes/api.php
   ```

3. Verify controller exists:
   ```bash
   ls -la /app/app/Http/Controllers/Api/HealthController.php
   ```

### Issue 2: Controller Not Found
**Symptoms:** "Class not found" errors

**Solutions:**
1. Clear autoload cache:
   ```bash
   composer dump-autoload
   ```

2. Check namespace:
   ```bash
   grep -n "namespace" /app/app/Http/Controllers/Api/HealthController.php
   ```

### Issue 3: Nginx Not Routing to PHP
**Symptoms:** 404 from nginx, not Laravel

**Solutions:**
1. Check nginx configuration:
   ```bash
   nginx -t
   cat /etc/nginx/conf.d/default.conf
   ```

2. Check PHP-FPM status:
   ```bash
   supervisorctl status
   ```

### Issue 4: Environment Issues
**Symptoms:** Laravel not bootstrapping properly

**Solutions:**
1. Check environment:
   ```bash
   php artisan env
   ```

2. Check if .env file exists:
   ```bash
   ls -la /app/.env
   ```

## Alternative Health Check Solutions

If the V1 endpoint still doesn't work, use these alternatives:

### Option 1: Use Laravel Built-in Health Check
```bash
curl http://your-domain.com/up
```

### Option 2: Use Legacy Health Check
```bash
curl http://your-domain.com/api/health
```

### Option 3: Update Health Check Script
Modify the health check to use working endpoints:

```bash
# Edit the health check script
nano /usr/local/bin/healthcheck.sh
```

Change the order to try `/up` first, then `/api/health`.

## Coolify-Specific Debugging

### Check Coolify Logs
1. Go to your application in Coolify
2. Check "Logs" section
3. Look for any error messages during build or deployment

### Check Container Status
```bash
# From your server
docker ps
docker logs <container_name>
```

### Check Resource Usage
```bash
# Inside container
top
df -h
free -m
```

## Emergency Fixes

### Fix 1: Create Simple Health Route
If nothing else works, create a simple health route:

```bash
# Add this to routes/api.php
Route::get('/health-simple', function() {
    return response()->json(['status' => 'ok']);
});
```

### Fix 2: Use Basic Health Check
Update your docker-compose.yml to use a basic health check:

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost/up"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 60s
```

### Fix 3: Disable Health Check Temporarily
If health checks are causing deployment issues:

```yaml
# Comment out healthcheck section temporarily
# healthcheck:
#   test: ["CMD", "/usr/local/bin/healthcheck.sh"]
#   interval: 30s
#   timeout: 10s
#   retries: 3
#   start_period: 60s
```

## Next Steps

1. **Run the debugging scripts** to identify the exact issue
2. **Check the output** and look for error messages
3. **Apply the appropriate fix** based on the error
4. **Test the endpoints** after fixing
5. **Update health check configuration** if needed

## Contact Support

If none of these solutions work:
1. Collect the output from debugging scripts
2. Check Coolify logs
3. Contact Coolify support with the error details

The debugging scripts will help identify exactly what's causing the 404 error.
