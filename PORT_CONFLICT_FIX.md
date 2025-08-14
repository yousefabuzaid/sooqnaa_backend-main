# Port Conflict Fix Guide

## Problem
Your deployment is failing with this error:
```
Error response from daemon: driver failed programming external connectivity on endpoint: Bind for 0.0.0.0:8080 failed: port is already allocated
```

## Root Cause
Port 8080 is already being used by another container or service on your Coolify server.

## Solutions

### Solution 1: Use Port 3000 (Recommended)
I've updated your `docker-compose.yml` to use port 3000 instead:

```yaml
ports:
  - "3000:8080"
```

This maps external port 3000 to internal port 8080.

### Solution 2: Dynamic Port Configuration
I've also made the configuration flexible:

```yaml
ports:
  - "${PORT:-3000}:8080"
```

This allows you to set a custom port via environment variable.

## Coolify Configuration

### In Coolify Dashboard:
1. **Port**: Set to `3000` (or your preferred port)
2. **Health Check Path**: Set to `/up`
3. **Health Check Port**: Set to `3000` (or your preferred port)

### Environment Variables:
Add this to your Coolify environment variables:
```
PORT=3000
```

## Testing

### Test with new port:
```bash
curl http://localhost:3000/up
curl http://localhost:3000/api/v1/health
curl http://localhost:3000/
```

## Alternative Ports to Try

If port 3000 is also busy, try these ports:
- `3001`
- `3002`
- `8000`
- `8001`
- `9000`
- `9001`

## How to Check Available Ports

### On your Coolify server:
```bash
# Check what's using port 8080
netstat -tulpn | grep :8080

# Check what's using port 3000
netstat -tulpn | grep :3000

# List all listening ports
netstat -tulpn | grep LISTEN
```

### In Docker:
```bash
# List all containers and their port mappings
docker ps -a

# Check port usage
docker port <container-name>
```

## Quick Fix Steps

1. **Update Coolify Configuration**:
   - Set Port to `3000`
   - Set Health Check Port to `3000`

2. **Redeploy** your application

3. **Test the endpoints**:
   ```bash
   curl http://your-domain:3000/up
   ```

## If Still Having Issues

### Option 1: Let Coolify Auto-Assign Port
Remove the port mapping entirely and let Coolify assign a random port:

```yaml
services:
  app:
    build: .
    restart: unless-stopped
    # Remove the ports section entirely
    healthcheck:
      test: ["CMD", "/usr/local/bin/healthcheck.sh"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 60s
```

### Option 2: Use Host Networking
Use host networking to avoid port conflicts:

```yaml
services:
  app:
    build: .
    restart: unless-stopped
    network_mode: "host"
    healthcheck:
      test: ["CMD", "/usr/local/bin/healthcheck.sh"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 60s
```

## Files Modified
- `docker-compose.yml` - Changed port mapping to use port 3000
- `test-deployment.sh` - Updated to test port 3000

## Next Steps
1. Commit and push these changes
2. Update Coolify configuration to use port 3000
3. Redeploy the application
4. Test the endpoints
