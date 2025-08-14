<?php

// Simple test script to verify health endpoint
echo "Testing health endpoints...\n\n";

// Test 1: Laravel built-in health check
echo "1. Testing Laravel built-in health check (/up):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/up");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response: " . substr($response, 0, 100) . "\n\n";

// Test 2: Legacy health check
echo "2. Testing legacy health check (/api/health):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/api/health");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response: " . substr($response, 0, 100) . "\n\n";

// Test 3: V1 health check
echo "3. Testing V1 health check (/api/v1/health):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/api/v1/health");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response: " . substr($response, 0, 100) . "\n\n";

echo "Test completed.\n";
