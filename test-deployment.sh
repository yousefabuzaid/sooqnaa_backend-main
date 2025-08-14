#!/bin/bash

# Test script for Coolify deployment verification
echo "🔍 Testing Coolify deployment..."

# Test 1: Check if the application is accessible
echo "📡 Testing basic connectivity..."
curl -f -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:8080/up || echo "❌ Basic connectivity failed"

# Test 2: Test health endpoint
echo "🏥 Testing health endpoint..."
curl -f -s -o /dev/null -w "Health Check Status: %{http_code}\n" http://localhost:8080/up || echo "❌ Health check failed"

# Test 3: Test API health endpoint
echo "🔬 Testing API health endpoint..."
curl -f -s -o /dev/null -w "API Health Status: %{http_code}\n" http://localhost:8080/api/v1/health || echo "❌ API health check failed"

# Test 4: Test main application
echo "🌐 Testing main application..."
curl -f -s -o /dev/null -w "Main App Status: %{http_code}\n" http://localhost:8080/ || echo "❌ Main application failed"

# Test 5: Test API documentation
echo "📚 Testing API documentation..."
curl -f -s -o /dev/null -w "API Docs Status: %{http_code}\n" http://localhost:8080/api/documentation || echo "❌ API documentation failed"

echo "✅ Testing completed!"
