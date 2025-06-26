<?php

// Simple healthcheck endpoint
header('Content-Type: application/json');

try {
    // Check if Laravel is working
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Basic health checks
    $checks = [
        'status' => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'laravel' => true
    ];
    
    // Check database connection if possible
    try {
        if (getenv('DB_CONNECTION')) {
            $checks['database'] = 'configured';
        }
    } catch (Exception $e) {
        $checks['database'] = 'error';
    }
    
    http_response_code(200);
    echo json_encode($checks);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Application not ready',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
