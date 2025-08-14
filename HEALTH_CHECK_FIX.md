# Health Check Fix Guide

## Problem
Your Docker container is showing as "unhealthy" because the health check script is not working properly.

## Root Cause
The original health check script was trying to access `http://localhost/up` but may have been failing due to:
1. Services not being fully ready when the check runs
2. Incorrect endpoint or response handling
3. Missing proper error handling and logging

## Solutions

### Solution 1: Enhanced Health Check Script (Recommended)
I've updated the main health check script (`docker/healthcheck.sh`) with:
- Better error handling and logging
- Multiple fallback checks
- Proper timing with a small delay
- Uses Laravel's built-in `/up` endpoint first, then falls back to API health check

### Solution 2: Simple Health Check Script
I've created an alternative script (`docker/healthcheck-simple.sh`) that:
- Performs basic service checks only
- Doesn't rely on complex HTTP endpoints
- Checks if files and directories exist
- Simple HTTP response verification

### Solution 3: Docker Compose Health Check Options

#### Option A: Use the Enhanced Script
```yaml
healthcheck:
  test: ["CMD", "/usr/local/bin/healthcheck.sh"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 60s
```

#### Option B: Use the Simple Script
```yaml
healthcheck:
  test: ["CMD", "/usr/local/bin/healthcheck-simple.sh"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 60s
```

#### Option C: Use PHP Direct Check
```yaml
healthcheck:
  test: ["CMD", "php", "-r", "echo 'OK';"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 30s
```

#### Option D: Use Curl Direct Check
```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost/up"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 60s
```

## Implementation Steps

1. **Rebuild the Docker image** to include the updated health check scripts:
   ```bash
   docker-compose build
   ```

2. **Update your docker-compose.yml** to use one of the health check options above.

3. **Restart the container**:
   ```bash
   docker-compose down
   docker-compose up -d
   ```

4. **Monitor the health check logs**:
   ```bash
   docker-compose logs -f app
   ```

## Testing Health Checks

You can test the health checks manually:

```bash
# Test the enhanced script
docker exec <container_name> /usr/local/bin/healthcheck.sh

# Test the simple script
docker exec <container_name> /usr/local/bin/healthcheck-simple.sh

# Test direct PHP
docker exec <container_name> php -r "echo 'OK';"

# Test direct curl
docker exec <container_name> curl -f http://localhost/up
```

## Laravel Health Endpoints

Your Laravel application has these health check endpoints:
- `/up` - Built-in Laravel health check (returns 200 OK)
- `/api/v1/health` - Custom health check with detailed status

## Troubleshooting

If health checks still fail:

1. **Check container logs**:
   ```bash
   docker-compose logs app
   ```

2. **Check if services are running**:
   ```bash
   docker exec <container_name> ps aux
   ```

3. **Check nginx and PHP-FPM status**:
   ```bash
   docker exec <container_name> supervisorctl status
   ```

4. **Test HTTP endpoints manually**:
   ```bash
   docker exec <container_name> curl -v http://localhost/up
   docker exec <container_name> curl -v http://localhost/api/v1/health
   ```

## Recommended Configuration

For production, I recommend using **Solution 1** (Enhanced Health Check Script) with the following docker-compose configuration:

```yaml
healthcheck:
  test: ["CMD", "/usr/local/bin/healthcheck.sh"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 60s
```

This provides comprehensive health checking while being robust and informative.
