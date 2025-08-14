#!/bin/bash

# Simple route test script
echo "=== SIMPLE ROUTE TEST ==="

# Test 1: Basic Laravel functionality
echo "1. Testing basic Laravel:"
php artisan --version
echo ""

# Test 2: Check if routes file is loaded
echo "2. Testing routes file:"
php -r "
try {
    require_once '/app/vendor/autoload.php';
    \$app = require_once '/app/bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo 'Laravel bootstrapped successfully\n';
    
    // Test if we can access the router
    \$router = app('router');
    echo 'Router accessible\n';
    
    // List all routes
    \$routes = \$router->getRoutes();
    echo 'Total routes: ' . count(\$routes) . '\n';
    
    // Look for health routes
    foreach (\$routes as \$route) {
        if (strpos(\$route->uri(), 'health') !== false) {
            echo 'Found health route: ' . \$route->uri() . ' -> ' . \$route->getActionName() . '\n';
        }
    }
    
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"
echo ""

# Test 3: Simple HTTP test
echo "3. Testing HTTP endpoints:"
echo "Testing /up:"
curl -v http://localhost/up 2>&1 | head -10
echo ""

echo "Testing /api/health:"
curl -v http://localhost/api/health 2>&1 | head -10
echo ""

echo "Testing /api/v1/health:"
curl -v http://localhost/api/v1/health 2>&1 | head -10
echo ""

echo "=== TEST COMPLETE ==="
