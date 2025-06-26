<?php

// Creazione account con SQL diretto
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
    $results = [];
    
    // Crea admin
    $adminEmail = 'admin@chataiplatform.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', $adminEmail, $adminPassword, 'admin', $now, $now, $now]);
        $results['admin'] = 'CREATED';
    } else {
        $results['admin'] = 'EXISTS';
    }
    
    // Crea store user
    $storeEmail = 'store@test.com';
    $storePassword = password_hash('store123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$storeEmail]);
    $storeUser = $stmt->fetch();
    
    if (!$storeUser) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Test Store', $storeEmail, $storePassword, 'store', $now, $now, $now]);
        $storeUserId = $pdo->lastInsertId();
        $results['store_user'] = 'CREATED';
        
        // Crea store
        $stmt = $pdo->prepare("INSERT INTO stores (user_id, name, description, phone, email, address, website, category, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $storeUserId,
            'Test Store',
            'Negozio di test per la demo',
            '+39 333 123 4567',
            $storeEmail,
            'Via Test 123, Milano',
            'https://teststore.com',
            'retail',
            1,
            $now,
            $now
        ]);
        $results['store'] = 'CREATED';
    } else {
        $results['store_user'] = 'EXISTS';
        $results['store'] = 'EXISTS';
    }
    
    echo json_encode([
        'status' => 'success',
        'timestamp' => $now,
        'results' => $results,
        'accounts' => [
            'admin' => [
                'email' => $adminEmail,
                'password' => 'admin123',
                'url' => 'https://web-production-9c70.up.railway.app/admin/login'
            ],
            'store' => [
                'email' => $storeEmail,
                'password' => 'store123',
                'url' => 'https://web-production-9c70.up.railway.app/store/login'
            ]
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
