<?php

// Debug completo del database e creazione account
header('Content-Type: application/json');

try {
    // Connessione database diretta
    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
    $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
    $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    $now = date('Y-m-d H:i:s');
    $debug = [];
    
    // 1. Controlla quali tabelle esistono
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $debug['tables'] = $tables;
    
    // 2. Controlla struttura tabella users (se esiste)
    if (in_array('users', $tables)) {
        $stmt = $pdo->query('DESCRIBE users');
        $usersColumns = $stmt->fetchAll();
        $debug['users_structure'] = $usersColumns;
        
        // Conta record attuali
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
        $debug['users_count'] = $stmt->fetch()['count'];
    } else {
        $debug['users_structure'] = 'TABLE_NOT_EXISTS';
    }
    
    // 3. Controlla struttura tabella stores (se esiste)
    if (in_array('stores', $tables)) {
        $stmt = $pdo->query('DESCRIBE stores');
        $storesColumns = $stmt->fetchAll();
        $debug['stores_structure'] = $storesColumns;
        
        // Conta record attuali
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM stores');
        $debug['stores_count'] = $stmt->fetch()['count'];
    } else {
        $debug['stores_structure'] = 'TABLE_NOT_EXISTS';
    }
    
    // 4. Se le tabelle esistono, prova a creare gli account
    $results = [];
    
    if (in_array('users', $tables)) {
        try {
            $adminEmail = 'admin@chataiplatform.com';
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            
            // Prova inserimento diretto
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = ?");
            $stmt->execute(['Admin', $adminEmail, $adminPassword, $now, $now, $now]);
            $results['admin'] = 'INSERTED_OR_UPDATED';
            
        } catch (Exception $e) {
            $results['admin'] = 'ERROR: ' . $e->getMessage();
        }
    } else {
        $results['admin'] = 'TABLE_USERS_NOT_EXISTS';
    }
    
    if (in_array('stores', $tables)) {
        try {
            $storeEmail = 'store@test.com';
            $storePassword = password_hash('store123', PASSWORD_DEFAULT);
            
            // Prova inserimento diretto
            $stmt = $pdo->prepare("INSERT INTO stores (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = ?");
            $stmt->execute(['Test Store', $storeEmail, $storePassword, $now, $now, $now]);
            $results['store'] = 'INSERTED_OR_UPDATED';
            
        } catch (Exception $e) {
            $results['store'] = 'ERROR: ' . $e->getMessage();
        }
    } else {
        $results['store'] = 'TABLE_STORES_NOT_EXISTS';
    }
    
    // 5. Verifica finale
    if (in_array('users', $tables)) {
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
        $debug['users_count_after'] = $stmt->fetch()['count'];
    }
    
    if (in_array('stores', $tables)) {
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM stores');
        $debug['stores_count_after'] = $stmt->fetch()['count'];
    }
    
    echo json_encode([
        'status' => 'debug_complete',
        'timestamp' => $now,
        'database_info' => [
            'host' => $host,
            'database' => $database,
            'connected' => true
        ],
        'debug' => $debug,
        'results' => $results,
        'next_steps' => [
            'Se le tabelle non esistono, esegui le migrazioni',
            'Se esistono ma struttura è diversa, controlla le colonne',
            'Se tutto ok, prova login con gli account creati'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
