#!/bin/bash

# Step 1: Remove conflicting nginx files
echo "Step 1: Removing conflicting nginx files"
rm -f /app/nginx.conf
rm -f /etc/nginx/conf.d/*.conf

# Step 2: Create nginx configuration
echo "Step 2: Creating nginx configuration"
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

# Step 3: Test nginx configuration
echo "Step 3: Testing nginx configuration"
nginx -t

# Step 4: Reload nginx
echo "Step 4: Reloading nginx"
nginx -s reload

# Step 5: Clear Laravel caches
echo "Step 5: Clearing Laravel caches"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Post-deployment completed successfully"
