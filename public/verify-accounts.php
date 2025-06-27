<?php

// Verifica account creati
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

    $results = [];

    // Verifica admin nella tabella admins
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM admins WHERE email = 'admin@chataiplatform.com'");
    $stmt->execute();
    $admin = $stmt->fetch();
    $results['admin_found'] = $admin ? 'YES' : 'NO';
    $results['admin_data'] = $admin;

    // Conta tutti gli admin
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins");
    $stmt->execute();
    $adminCount = $stmt->fetch();
    $results['total_admins'] = $adminCount['count'];

    // Verifica store nella tabella stores
    $stmt = $pdo->prepare("SELECT id, name, email, slug, created_at FROM stores WHERE email = 'store@test.com'");
    $stmt->execute();
    $store = $stmt->fetch();
    $results['store_found'] = $store ? 'YES' : 'NO';
    $results['store_data'] = $store;

    // Conta tutti gli store
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM stores");
    $stmt->execute();
    $storeCount = $stmt->fetch();
    $results['total_stores'] = $storeCount['count'];

    // Lista tutte le tabelle per conferma
    $stmt = $pdo->prepare("SHOW TABLES");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $results['available_tables'] = $tables;

    echo json_encode($results, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database connection failed',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
