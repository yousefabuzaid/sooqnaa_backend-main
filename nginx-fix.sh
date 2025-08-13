#!/bin/bash
set -eu

echo "== Comprehensive nginx configuration fix =="

# Remove any existing nginx configuration files that might cause conflicts
echo "== Removing conflicting nginx configurations =="
rm -f /app/nginx.conf 2>/dev/null || true
rm -f /etc/nginx/conf.d/*.conf 2>/dev/null || true
rm -f /etc/nginx/sites-enabled/* 2>/dev/null || true
rm -f /etc/nginx/sites-available/* 2>/dev/null || true

# Create the correct nginx configuration
echo "== Creating clean nginx configuration =="
cat > /etc/nginx/conf.d/default.conf << 'EOF'
server {
    listen 80;
    server_name _;
    root /app/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
    }

    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|webp)$ {
        try_files $uri =404;
        expires max;
        access_log off;
    }
}
EOF

# Also create a backup of the main nginx.conf if it exists
if [ -f /etc/nginx/nginx.conf ]; then
    echo "== Backing up main nginx.conf =="
    cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup
fi

# Test nginx configuration
echo "== Testing nginx configuration =="
nginx -t && nginx -s reload || { 
    echo "Nginx test failed, showing configuration:"
    echo "=== /etc/nginx/conf.d/default.conf ==="
    cat /etc/nginx/conf.d/default.conf
    echo "=== /etc/nginx/nginx.conf ==="
    cat /etc/nginx/nginx.conf
    exit 0
}

echo "== Nginx configuration fixed successfully =="
