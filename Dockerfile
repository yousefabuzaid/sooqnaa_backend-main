FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy package.json and install Node.js dependencies
COPY package.json package-lock.json* ./
RUN npm install

# Copy existing application directory contents
COPY . .

# Build frontend assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy health check scripts
COPY docker/healthcheck.sh /usr/local/bin/healthcheck.sh
COPY docker/healthcheck-simple.sh /usr/local/bin/healthcheck-simple.sh
COPY docker/clear-cache.sh /usr/local/bin/clear-cache.sh
COPY docker/debug-routes.sh /usr/local/bin/debug-routes.sh
COPY docker/test-simple-route.sh /usr/local/bin/test-simple-route.sh
COPY docker/nginx-debug.sh /usr/local/bin/nginx-debug.sh
RUN chmod +x /usr/local/bin/healthcheck.sh /usr/local/bin/healthcheck-simple.sh /usr/local/bin/clear-cache.sh /usr/local/bin/debug-routes.sh /usr/local/bin/test-simple-route.sh /usr/local/bin/nginx-debug.sh

# Create necessary directories
RUN mkdir -p /var/log/supervisor

# Expose port 80
EXPOSE 80

# Add health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
