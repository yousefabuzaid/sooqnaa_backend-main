#!/bin/bash
set -eu

echo "== Cleaning up nginx configuration =="
# Remove any existing conf.d files that might cause conflicts
rm -f /etc/nginx/conf.d/*.conf 2>/dev/null || true

echo "== Writing clean nginx configuration =="
cat > /nginx.conf << 'EOF'
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

echo "== Testing nginx configuration =="
nginx -t && nginx -s reload || { 
    echo "Nginx test failed, showing configuration:"
    cat /nginx.conf
    exit 0
}

echo "== Clearing Laravel caches =="
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "== Post-deployment completed successfully =="
