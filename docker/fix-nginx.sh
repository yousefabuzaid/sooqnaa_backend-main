#!/bin/bash

# Fix nginx configuration conflicts
echo "=== FIXING NGINX CONFIGURATION ==="
echo "Timestamp: $(date)"
echo ""

# Check for multiple nginx config files
echo "1. Checking for multiple nginx configuration files:"
find /etc/nginx -name "*.conf" -type f
echo ""

# Check the main nginx.conf
echo "2. Main nginx.conf content:"
cat /etc/nginx/nginx.conf
echo ""

# Check all sites-enabled
echo "3. Sites-enabled directory:"
ls -la /etc/nginx/sites-enabled/ 2>/dev/null || echo "No sites-enabled directory"
echo ""

# Check all conf.d files
echo "4. Conf.d directory contents:"
ls -la /etc/nginx/conf.d/
echo ""

# Remove conflicting configurations
echo "5. Removing conflicting configurations:"
rm -f /etc/nginx/sites-enabled/default 2>/dev/null
rm -f /etc/nginx/conf.d/default.conf 2>/dev/null
echo "Removed default configurations"
echo ""

# Create clean nginx configuration
echo "6. Creating clean nginx configuration:"
cat > /etc/nginx/conf.d/app.conf << 'EOF'
server {
    listen 80 default_server;
    server_name _;
    root /app/public;
    index index.php index.html;

    # Add error and access logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Handle Laravel health check endpoint
    location = /up {
        try_files /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
    }

    # Handle all other requests
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle PHP files
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        
        # Add timeout settings
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
    }

    # Handle static files
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|webp)$ {
        try_files $uri =404;
        expires max;
        access_log off;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
EOF

echo "Created clean nginx configuration"
echo ""

# Test nginx configuration
echo "7. Testing nginx configuration:"
nginx -t
echo ""

# Restart nginx
echo "8. Restarting nginx:"
supervisorctl restart nginx
echo ""

# Check nginx status
echo "9. Nginx status:"
supervisorctl status nginx
echo ""

# Test the endpoints
echo "10. Testing endpoints:"
echo "Testing /up:"
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/up
echo "Testing /api/health:"
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/api/health
echo "Testing /api/v1/health:"
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" http://localhost/api/v1/health
echo ""

echo "=== NGINX FIX COMPLETE ==="
