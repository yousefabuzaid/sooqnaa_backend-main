#!/bin/bash
rm -f /app/nginx.conf /etc/nginx/conf.d/*.conf 2>/dev/null || true && cat > /etc/nginx/conf.d/default.conf << 'EOF'
server {
    listen 80;
    server_name _;
    root /app/public;
    index index.php index.html;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; fastcgi_pass 127.0.0.1:9000; fastcgi_index index.php; }
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|webp)$ { try_files $uri =404; expires max; access_log off; }
}
EOF
nginx -t && nginx -s reload && php artisan config:clear && php artisan route:clear && php artisan view:clear
