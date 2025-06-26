<?php

// Test connessione database
header('Content-Type: application/json');

try {
    // Test connessione diretta
    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? '';
    $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
    $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';

    $response = [
        'database_test' => 'running',
        'timestamp' => date('Y-m-d H:i:s'),
        'config_check' => [
            'DB_HOST' => $host ? 'SET' : 'MISSING',
            'DB_DATABASE' => $database ? 'SET' : 'MISSING',
            'DB_USERNAME' => $username ? 'SET' : 'MISSING',
            'DB_PASSWORD' => $password ? 'SET' : 'MISSING',
            'DB_PORT' => $port
        ]
    ];

    // Test connessione MySQL
    if ($host && $database && $username) {
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5
            ]);

            // Test query semplice
            $stmt = $pdo->query('SELECT 1 as test, NOW() as server_time');
            $result = $stmt->fetch();

            $response['connection'] = 'SUCCESS';
            $response['server_time'] = $result['server_time'];

            // Test tabelle esistenti
            $stmt = $pdo->query('SHOW TABLES');
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $response['tables_count'] = count($tables);
            $response['tables'] = array_slice($tables, 0, 10); // Prime 10 tabelle

            // Test se esistono le tabelle principali
            $required_tables = ['users', 'stores', 'conversations', 'interactions'];
            $response['required_tables'] = [];
            foreach ($required_tables as $table) {
                $response['required_tables'][$table] = in_array($table, $tables) ? 'EXISTS' : 'MISSING';
            }

        } catch (PDOException $e) {
            $response['connection'] = 'FAILED';
            $response['error'] = $e->getMessage();
            $response['error_code'] = $e->getCode();
        }
    } else {
        $response['connection'] = 'MISSING_CONFIG';
        $response['message'] = 'Database configuration incomplete';
    }

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
