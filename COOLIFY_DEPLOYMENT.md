# Coolify Deployment Guide

## Overview
This guide will help you deploy your Laravel application to Coolify with the cleaned up configuration that removes all unused environment variables.

## Changes Made

### 1. Configuration Files Cleaned
- **config/cache.php**: Removed Redis, Memcached, and DynamoDB cache drivers
- **config/queue.php**: Removed SQS and Redis queue drivers  
- **config/session.php**: Changed default to file driver
- **config/filesystems.php**: Removed S3 disk configuration
- **config/services.php**: Removed AWS SES, Slack, Postmark, and Resend configurations
- **config/logging.php**: Removed Slack and Papertrail logging channels
- **config/mail.php**: Removed Postmark and Resend mailers
- **config/database.php**: Removed Redis configuration

### 2. Environment Variables
Use the cleaned environment variables from `env-clean.txt`. This includes only the variables you actually need:

#### Core Application
```
APP_NAME=SooqnaaBackend
APP_ENV=production
APP_KEY=base64:YIBt2e4lIGh/H072lawSH7sgLo3e7CRGe0j7navgQ/4=
APP_DEBUG=false
APP_URL=https://your-domain.com
```

#### Database (MySQL)
```
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=sooqnaa_db
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

#### Cache & Sessions
```
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

#### Mail (SMTP)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## Coolify Configuration

### Build Pack
Use **Nixpacks**

### Pre-deployment Command
```bash
php artisan migrate --force
```

### Post-deployment Command
```bash
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
```

## Environment Variables to Set in Coolify

Copy all variables from `env-clean.txt` to your Coolify environment variables section. Make sure to:

1. Update `APP_URL` to match your actual domain
2. Update `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` to match your MySQL database
3. Update `MAIL_USERNAME` and `MAIL_PASSWORD` to match your email credentials
4. Update `L5_SWAGGER_CONST_HOST` to match your domain

## Troubleshooting

### If you still see environment variable warnings:
1. Make sure you've committed all the configuration changes
2. Clear the application cache after deployment
3. Check that all environment variables are properly set in Coolify

### If nginx still fails:
1. The post-deployment script should handle this automatically
2. Check the deployment logs for any nginx errors
3. The script will show the nginx configuration if it fails

### Database connection issues:
1. Verify your MySQL database is accessible from the Coolify server
2. Check that the database credentials are correct
3. Ensure the database exists and migrations can run

## Benefits of This Cleanup

1. **Reduced warnings**: No more environment variable warnings for unused services
2. **Faster startup**: Less configuration to load
3. **Cleaner logs**: Fewer irrelevant warnings in your application logs
4. **Better security**: No unused service configurations that could be exploited
5. **Easier maintenance**: Only the services you actually use are configured
