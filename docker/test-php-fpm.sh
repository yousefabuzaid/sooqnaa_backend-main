#!/bin/bash

# Test PHP-FPM functionality
echo "=== PHP-FPM TESTING ==="
echo "Timestamp: $(date)"
echo ""

# Check PHP-FPM status
echo "1. PHP-FPM process status:"
ps aux | grep php-fpm
echo ""

# Check supervisor status
echo "2. Supervisor status:"
supervisorctl status
echo ""

# Check if PHP-FPM is listening
echo "3. PHP-FPM listening status:"
netstat -tlnp | grep :9000 || echo "No PHP-FPM listening on port 9000"
echo ""

# Test PHP-FPM directly
echo "4. Testing PHP-FPM directly:"
echo "<?php echo 'PHP-FPM is working'; ?>" | php-cgi 2>/dev/null || echo "PHP-FPM direct test failed"
echo ""

# Test PHP from command line
echo "5. Testing PHP from command line:"
php -v
echo ""

# Check PHP configuration
echo "6. PHP configuration:"
php --ini
echo ""

# Test if Laravel can bootstrap
echo "7. Testing Laravel bootstrap:"
cd /app && php artisan --version
echo ""

# Check file permissions
echo "8. File permissions:"
ls -la /app/public/index.php
ls -la /app/storage/
echo ""

# Test simple PHP file
echo "9. Creating and testing simple PHP file:"
cat > /tmp/test.php << 'EOF'
<?php
echo "PHP is working\n";
echo "Current directory: " . getcwd() . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
?>
EOF

php /tmp/test.php
echo ""

# Test nginx to PHP-FPM communication
echo "10. Testing nginx to PHP-FPM communication:"
cat > /app/public/test.php << 'EOF'
<?php
echo "Nginx to PHP-FPM communication is working\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
?>
EOF

curl -s http://localhost/test.php || echo "Failed to access test.php"
echo ""

echo "=== PHP-FPM TESTING COMPLETE ==="
