<?php

// Test completo del flusso di login per admin e store

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Store;

echo "=== TEST FLUSSO LOGIN COMPLETO ===\n\n";

// Test 1: Verifica presenza di account di test
echo "1. VERIFICA ACCOUNT:\n";

try {
    $admin = Admin::where('email', 'admin@test.com')->first();
    $store = Store::where('email', 'store@test.com')->first();
    
    echo "   - Admin test: " . ($admin ? "✓ Trovato (ID: {$admin->id})" : "✗ Non trovato") . "\n";
    echo "   - Store test: " . ($store ? "✓ Trovato (ID: {$store->id})" : "✗ Non trovato") . "\n";
    
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
}

// Test 2: Simula login admin
echo "\n2. SIMULAZIONE LOGIN ADMIN:\n";
try {
    if ($admin) {
        // Simula login
        Auth::guard('admin')->login($admin);
        $isLoggedIn = Auth::guard('admin')->check();
        $user = Auth::guard('admin')->user();
        
        echo "   - Login eseguito: " . ($isLoggedIn ? "✓ Successo" : "✗ Fallito") . "\n";
        echo "   - Utente autenticato: " . ($user ? "✓ {$user->email}" : "✗ Nessuno") . "\n";
        
        // Logout
        Auth::guard('admin')->logout();
        echo "   - Logout eseguito: ✓\n";
    } else {
        echo "   - ✗ Nessun admin disponibile per il test\n";
    }
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
}

// Test 3: Simula login store
echo "\n3. SIMULAZIONE LOGIN STORE:\n";
try {
    if ($store) {
        // Simula login
        Auth::guard('store')->login($store);
        $isLoggedIn = Auth::guard('store')->check();
        $user = Auth::guard('store')->user();
        
        echo "   - Login eseguito: " . ($isLoggedIn ? "✓ Successo" : "✗ Fallito") . "\n";
        echo "   - Utente autenticato: " . ($user ? "✓ {$user->email}" : "✗ Nessuno") . "\n";
        
        // Logout
        Auth::guard('store')->logout();
        echo "   - Logout eseguito: ✓\n";
    } else {
        echo "   - ✗ Nessun store disponibile per il test\n";
    }
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
}

// Test 4: Verifica guard configurations
echo "\n4. CONFIGURAZIONE GUARDS:\n";
$authConfig = config('auth');

foreach (['admin', 'store'] as $guard) {
    echo "   - Guard '{$guard}':\n";
    $guardConfig = $authConfig['guards'][$guard] ?? null;
    if ($guardConfig) {
        echo "     * Driver: {$guardConfig['driver']}\n";
        echo "     * Provider: {$guardConfig['provider']}\n";
        
        $providerConfig = $authConfig['providers'][$guardConfig['provider']] ?? null;
        if ($providerConfig) {
            echo "     * Model: {$providerConfig['model']}\n";
        }
    } else {
        echo "     * ✗ Configurazione non trovata\n";
    }
}

// Test 5: Test URL intended
echo "\n5. TEST URL INTENDED:\n";
try {
    // Simula una sessione con URL intended
    session()->put('url.intended', route('admin.dashboard'));
    $intended = session()->pull('url.intended');
    echo "   - URL intended admin: {$intended}\n";
    
    session()->put('url.intended', route('store.dashboard'));
    $intended = session()->pull('url.intended');
    echo "   - URL intended store: {$intended}\n";
    
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
}

// Test 6: Verifica middleware
echo "\n6. TEST MIDDLEWARE:\n";
try {
    $adminMiddleware = new \App\Http\Middleware\IsAdmin();
    $storeMiddleware = new \App\Http\Middleware\IsStore();
    echo "   - IsAdmin middleware: ✓ Istanziato correttamente\n";
    echo "   - IsStore middleware: ✓ Istanziato correttamente\n";
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
}

echo "\n=== FINE TEST ===\n";
