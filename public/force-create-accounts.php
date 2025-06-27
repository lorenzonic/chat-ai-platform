<?php

// Versione che forza sempre la creazione degli account
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

    // ELIMINA E RICREA ADMIN
    $adminEmail = 'admin@chataiplatform.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);

    // Elimina admin esistente
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);

    // Crea nuovo admin
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Admin', $adminEmail, $adminPassword, 'admin', $now, $now, $now]);
    $results['admin'] = 'FORCE_CREATED';

    // ELIMINA E RICREA STORE
    $storeEmail = 'store@test.com';
    $storePassword = password_hash('store123', PASSWORD_DEFAULT);

    // Elimina store esistente
    $stmt = $pdo->prepare("DELETE FROM stores WHERE email = ?");
    $stmt->execute([$storeEmail]);

    // Crea nuovo store
    $stmt = $pdo->prepare("INSERT INTO stores (name, slug, email, password, description, phone, address, website, is_active, chat_enabled, assistant_name, chat_context, chat_opening_message, chat_theme_color, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test Store',
        'test-store',
        $storeEmail,
        $storePassword,
        'Negozio di test per la demo',
        '+39 333 123 4567',
        'Via Test 123, Milano',
        'https://teststore.com',
        1,
        1,
        'Assistant',
        'Assistente virtuale per Test Store',
        'Ciao! Come posso aiutarti oggi?',
        '#3b82f6',
        $now,
        $now
    ]);
    $results['store'] = 'FORCE_CREATED';

    // Verifica che siano stati creati
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $adminCount = $stmt->fetch()['count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM stores WHERE email = ?");
    $stmt->execute([$storeEmail]);
    $storeCount = $stmt->fetch()['count'];

    echo json_encode([
        'status' => 'success',
        'timestamp' => $now,
        'results' => $results,
        'verification' => [
            'admin_in_db' => $adminCount,
            'store_in_db' => $storeCount
        ],
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
