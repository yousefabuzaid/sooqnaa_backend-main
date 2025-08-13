# Coolify Deployment Guide - Docker Compose

## Overview
This guide will help you deploy your Laravel application to Coolify using Docker Compose with full control over the nginx configuration.

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

### 2. Docker Setup Created
- **Dockerfile**: Complete Laravel setup with PHP 8.2, nginx, and Node.js
- **docker-compose.yml**: Multi-service setup with Laravel app and MySQL
- **docker/nginx.conf**: Proper nginx configuration for Laravel
- **docker/supervisord.conf**: Process management for nginx and PHP-FPM
- **.dockerignore**: Optimized build context

## Coolify Configuration

### Build Pack
Use **Docker Compose**

### Pre-deployment Command
```bash
php artisan migrate --force
```

### Post-deployment Command
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

## Environment Variables to Set in Coolify

Copy all variables from `env-clean.txt` to your Coolify environment variables section. Make sure to:

1. Update `APP_URL` to match your actual domain
2. Update `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` to match your MySQL database
3. Update `MAIL_USERNAME` and `MAIL_PASSWORD` to match your email credentials
4. Update `L5_SWAGGER_CONST_HOST` to match your domain

## Docker Compose Benefits

✅ **Full Control**: Complete control over nginx configuration  
✅ **No Conflicts**: No more nginx configuration conflicts  
✅ **Predictable**: Same environment every time  
✅ **Debugging**: Easy to debug and troubleshoot  
✅ **Customization**: Can customize any part of the setup  

## Troubleshooting

### If you see environment variable warnings:
1. Make sure you've committed all the configuration changes
2. Clear the application cache after deployment
3. Check that all environment variables are properly set in Coolify

### If the application doesn't start:
1. Check the Docker logs in Coolify
2. Verify the Dockerfile and docker-compose.yml are correct
3. Ensure all environment variables are set

### Database connection issues:
1. Verify your MySQL database is accessible from the Coolify server
2. Check that the database credentials are correct
3. Ensure the database exists and migrations can run

## Benefits of This Setup

1. **No nginx conflicts**: Docker Compose handles nginx configuration properly
2. **Full control**: You control every aspect of the deployment
3. **Predictable**: Same setup every time
4. **Easy debugging**: Clear logs and error messages
5. **Scalable**: Easy to add more services if needed
