<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger Documentation Route
Route::get('/api/documentation', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'API documentation not found'], 404);
    }
    
    $docs = json_decode(file_get_contents($filePath), true);
    
    // Create a simple HTML page to display the Swagger UI
    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sooqnaa API Documentation</title>
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
        <style>
            html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
            *, *:before, *:after { box-sizing: inherit; }
            body { margin:0; background: #fafafa; }
        </style>
    </head>
    <body>
        <div id="swagger-ui"></div>
        <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
        <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-standalone-preset.js"></script>
        <script>
            window.onload = function() {
                const ui = SwaggerUIBundle({
                    url: "' . url('/api/docs') . '",
                    dom_id: "#swagger-ui",
                    deepLinking: true,
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    plugins: [
                        SwaggerUIBundle.plugins.DownloadUrl
                    ],
                    layout: "StandaloneLayout"
                });
            };
        </script>
    </body>
    </html>';
    
    return response($html, 200, ['Content-Type' => 'text/html']);
})->name('l5-swagger.api');

Route::get('/api/docs', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'API documentation not found'], 404);
    }
    
    return response()->json(json_decode(file_get_contents($filePath)));
})->name('l5-swagger.docs');
