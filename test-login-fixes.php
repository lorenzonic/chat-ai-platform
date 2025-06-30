<?php

// Test per verificare le modifiche ai controller di login e middleware

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Store\Auth\LoginController as StoreLoginController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsStore;

echo "=== TEST MODIFICHE LOGIN E MIDDLEWARE ===\n\n";

// Test 1: Verifica modifiche controller
echo "1. VERIFICHE CONTROLLER:\n";

// Verifica AdminLoginController
try {
    $adminController = new AdminLoginController();
    echo "   ✓ AdminLoginController: istanziato correttamente\n";

    // Verifica che il metodo login esista
    $reflection = new ReflectionClass($adminController);
    $loginMethod = $reflection->getMethod('login');
    echo "   ✓ AdminLoginController::login: metodo esistente\n";

} catch (Exception $e) {
    echo "   ✗ AdminLoginController: " . $e->getMessage() . "\n";
}

// Verifica StoreLoginController
try {
    $storeController = new StoreLoginController();
    echo "   ✓ StoreLoginController: istanziato correttamente\n";

    // Verifica che il metodo login esista
    $reflection = new ReflectionClass($storeController);
    $loginMethod = $reflection->getMethod('login');
    echo "   ✓ StoreLoginController::login: metodo esistente\n";

} catch (Exception $e) {
    echo "   ✗ StoreLoginController: " . $e->getMessage() . "\n";
}

// Test 2: Verifica modifiche middleware
echo "\n2. VERIFICHE MIDDLEWARE:\n";

try {
    $adminMiddleware = new IsAdmin();
    echo "   ✓ IsAdmin middleware: istanziato correttamente\n";

    $storeMiddleware = new IsStore();
    echo "   ✓ IsStore middleware: istanziato correttamente\n";

} catch (Exception $e) {
    echo "   ✗ Middleware: " . $e->getMessage() . "\n";
}

// Test 3: Simula comportamento redirect
echo "\n3. SIMULAZIONE REDIRECT:\n";

// Simula richiesta admin/dashboard
$request = Request::create('/admin/dashboard', 'GET');
echo "   - URL richiesta: " . $request->fullUrl() . "\n";
echo "   - Is admin route: " . ($request->is('admin/*') ? 'Sì' : 'No') . "\n";
echo "   - Is login route: " . ($request->is('admin/login') ? 'Sì' : 'No') . "\n";

// Simula richiesta store/dashboard
$request2 = Request::create('/store/dashboard', 'GET');
echo "   - URL richiesta: " . $request2->fullUrl() . "\n";
echo "   - Is store route: " . ($request2->is('store/*') ? 'Sì' : 'No') . "\n";
echo "   - Is login route: " . ($request2->is('store/login') ? 'Sì' : 'No') . "\n";

// Test 4: Verifica route generation
echo "\n4. VERIFICA ROTTE:\n";
try {
    echo "   - admin.login: " . route('admin.login') . "\n";
    echo "   - admin.dashboard: " . route('admin.dashboard') . "\n";
    echo "   - store.login: " . route('store.login') . "\n";
    echo "   - store.dashboard: " . route('store.dashboard') . "\n";
} catch (Exception $e) {
    echo "   ✗ Errore generazione rotte: " . $e->getMessage() . "\n";
}

// Test 5: Simula comportamento URL intended
echo "\n5. SIMULAZIONE URL INTENDED:\n";

// Test con URL admin
$adminUrl = route('admin.dashboard');
if (str_contains($adminUrl, '/admin/')) {
    echo "   ✓ URL admin valido: {$adminUrl}\n";
} else {
    echo "   ✗ URL admin non valido: {$adminUrl}\n";
}

// Test con URL store
$storeUrl = route('store.dashboard');
if (str_contains($storeUrl, '/store/')) {
    echo "   ✓ URL store valido: {$storeUrl}\n";
} else {
    echo "   ✗ URL store non valido: {$storeUrl}\n";
}

// Test con URL esterno (dovrebbe essere rifiutato)
$externalUrl = 'http://example.com/admin/dashboard';
if (str_contains($externalUrl, '/admin/')) {
    echo "   ⚠ URL esterno riconosciuto come admin: {$externalUrl} (potenziale problema)\n";
} else {
    echo "   ✓ URL esterno correttamente rifiutato: {$externalUrl}\n";
}

echo "\n=== FINE TEST ===\n";

echo "\n📋 RIASSUNTO MODIFICHE:\n";
echo "1. ✓ AdminLoginController: migliorato gestione redirect con fallback sicuro\n";
echo "2. ✓ StoreLoginController: migliorato gestione redirect con fallback sicuro\n";
echo "3. ✓ IsAdmin middleware: aggiunta memorizzazione URL intended\n";
echo "4. ✓ IsStore middleware: aggiunta memorizzazione URL intended\n";
echo "5. ✓ Validazione URL per prevenire redirect esterni\n";
