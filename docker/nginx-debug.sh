#!/bin/bash

# Nginx debugging script
echo "=== NGINX DEBUGGING ==="
echo "Timestamp: $(date)"
echo ""

# Check nginx configuration
echo "1. Testing nginx configuration:"
nginx -t
echo ""

# Check nginx configuration file
echo "2. Current nginx configuration:"
cat /etc/nginx/conf.d/default.conf
echo ""

# Check if nginx is running
echo "3. Nginx process status:"
ps aux | grep nginx
echo ""

# Check nginx error logs
echo "4. Recent nginx error logs:"
tail -n 20 /var/log/nginx/error.log 2>/dev/null || echo "No nginx error log found"
echo ""

# Check nginx access logs
echo "5. Recent nginx access logs:"
tail -n 10 /var/log/nginx/access.log 2>/dev/null || echo "No nginx access log found"
echo ""

# Check PHP-FPM status
echo "6. PHP-FPM status:"
supervisorctl status
echo ""

# Check if PHP-FPM is listening
echo "7. PHP-FPM socket/port status:"
netstat -tlnp | grep :9000 || echo "No PHP-FPM listening on port 9000"
ls -la /var/run/php-fpm.sock 2>/dev/null || echo "No PHP-FPM socket found"
echo ""

# Test PHP-FPM directly
echo "8. Testing PHP-FPM directly:"
echo "<?php echo 'PHP-FPM is working'; ?>" | php-cgi 2>/dev/null || echo "PHP-FPM test failed"
echo ""

# Check file permissions
echo "9. File permissions:"
ls -la /app/public/
ls -la /app/public/index.php
echo ""

# Test if nginx can access the files
echo "10. Testing file accessibility:"
nginx -t
echo ""

echo "=== NGINX DEBUGGING COMPLETE ==="
