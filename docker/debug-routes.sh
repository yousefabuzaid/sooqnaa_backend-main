#!/bin/bash

# Comprehensive debugging script for route issues
echo "=== LARAVEL ROUTE DEBUGGING ==="
echo "Timestamp: $(date)"
echo ""

# Check if we're in the right directory
echo "1. Current working directory:"
pwd
echo ""

# Check if Laravel files exist
echo "2. Checking Laravel files:"
ls -la /app/public/index.php
ls -la /app/routes/api.php
ls -la /app/app/Http/Controllers/Api/HealthController.php
echo ""

# Check Laravel version and basic functionality
echo "3. Laravel version and basic info:"
php artisan --version
php artisan env
echo ""

# List all routes
echo "4. All registered routes:"
php artisan route:list
echo ""

# Check specifically for health routes
echo "5. Health-related routes:"
php artisan route:list | grep -i health
echo ""

# Test route caching
echo "6. Route cache status:"
php artisan route:cache --help > /dev/null 2>&1 && echo "Route caching is available" || echo "Route caching not available"
echo ""

# Clear all caches
echo "7. Clearing all caches:"
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "Caches cleared"
echo ""

# Check route list again after clearing
echo "8. Routes after cache clear:"
php artisan route:list | grep -i health
echo ""

# Test the controller directly
echo "9. Testing HealthController directly:"
php -r "
try {
    require_once '/app/vendor/autoload.php';
    \$app = require_once '/app/bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    \$controller = new App\Http\Controllers\Api\HealthController();
    \$response = \$controller->check();
    echo 'Controller test: SUCCESS - ' . \$response->getStatusCode() . '\n';
} catch (Exception \$e) {
    echo 'Controller test: FAILED - ' . \$e->getMessage() . '\n';
}
"
echo ""

# Check nginx configuration
echo "10. Nginx configuration:"
nginx -t
echo ""

# Check if nginx is serving requests
echo "11. Testing nginx locally:"
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/up
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/api/health
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/api/v1/health
echo ""

# Check PHP-FPM status
echo "12. PHP-FPM status:"
supervisorctl status
echo ""

# Check logs
echo "13. Recent Laravel logs:"
tail -n 20 /app/storage/logs/laravel.log 2>/dev/null || echo "No Laravel logs found"
echo ""

echo "=== DEBUGGING COMPLETE ==="
