<?php

// Endpoint per creare account admin e store
header('Content-Type: application/json');

try {
    // Carica Laravel completamente
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Inizializza il kernel HTTP
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Crea una request fittizia per inizializzare Laravel
    $request = Illuminate\Http\Request::capture();
    $app->instance('request', $request);

    // Bootstrap dell'applicazione
    $kernel->bootstrap();

    // Test connessione database
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
    } catch (Exception $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }

    // Crea admin
    $adminEmail = 'admin@chataiplatform.com';
    $adminPassword = 'admin123';

    $admin = \App\Models\User::where('email', $adminEmail)->first();
    if (!$admin) {
        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => $adminEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $adminCreated = true;
    } else {
        $adminCreated = false;
    }

    // Crea store direttamente (non user)
    $storeEmail = 'store@test.com';
    $storePassword = 'store123';

    $store = \App\Models\Store::where('email', $storeEmail)->first();
    if (!$store) {
        $store = \App\Models\Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'email' => $storeEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($storePassword),
            'description' => 'Negozio di test per la demo',
            'phone' => '+39 333 123 4567',
            'address' => 'Via Test 123, Milano',
            'website' => 'https://teststore.com',
            'is_active' => true,
            'chat_enabled' => true,
            'assistant_name' => 'Assistant',
            'chat_context' => 'Assistente virtuale per Test Store',
            'chat_opening_message' => 'Ciao! Come posso aiutarti oggi?',
            'chat_theme_color' => '#3b82f6',
        ]);

        $storeCreated = true;
    } else {
        $storeCreated = false;
    }

    $response = [
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'accounts' => [
            'admin' => [
                'email' => $adminEmail,
                'password' => $adminPassword,
                'created' => $adminCreated,
                'login_url' => 'https://web-production-9c70.up.railway.app/admin/login'
            ],
            'store' => [
                'email' => $storeEmail,
                'password' => $storePassword,
                'created' => $storeCreated,
                'store_id' => $store ? $store->id : null,
                'login_url' => 'https://web-production-9c70.up.railway.app/store/login'
            ]
        ],
        'instructions' => [
            'Admin: Usa admin@chataiplatform.com / admin123',
            'Store: Usa store@test.com / store123',
            'Puoi eliminare questo file dopo aver creato gli account'
        ]
    ];

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
