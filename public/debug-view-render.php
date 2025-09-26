<?php
require_once '../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

echo "<h1>Debug View Render Test</h1>\n";

try {
    $app = new Application(realpath(__DIR__.'/../'));
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        App\Http\Kernel::class
    );
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "<p>✅ Laravel bootstrap OK</p>\n";

    // Simula autenticazione grower
    $request = Request::create('/grower/dashboard', 'GET');
    $request->session()->put('grower_id', 34);
    
    echo "<p>✅ Request simulata</p>\n";

    // Test specifico del controller
    $controller = new \App\Http\Controllers\Grower\DashboardController();
    
    echo "<p>✅ Controller istanziato</p>\n";
    
    // Simula middleware di autenticazione
    $grower = \App\Models\Grower::find(34);
    if (!$grower) {
        echo "<p>❌ Grower 34 non trovato!</p>\n";
        exit;
    }
    
    echo "<p>✅ Grower trovato: {$grower->name}</p>\n";
    
    // Test delle query
    $totalProducts = $grower->products()->count();
    echo "<p>Total Products: {$totalProducts}</p>\n";
    
    $productsInOrders = $grower->products()
        ->whereHas('orderItems')
        ->distinct()
        ->count();
    echo "<p>Products in Orders: {$productsInOrders}</p>\n";
    
    $totalOrders = \App\Models\Order::whereHas('orderItems.product', function($query) use ($grower) {
        $query->where('grower_id', $grower->id);
    })->count();
    echo "<p>Total Orders: {$totalOrders}</p>\n";
    
    $recentProducts = $grower->products()
        ->with('orderItems')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    echo "<p>Recent Products: " . $recentProducts->count() . "</p>\n";
    
    echo "<p>✅ Tutte le query OK</p>\n";
    
    // Test della view senza rendering completo
    $viewData = [
        'grower' => $grower,
        'totalProducts' => $totalProducts,
        'productsInOrders' => $productsInOrders,
        'totalOrders' => $totalOrders,
        'recentProducts' => $recentProducts
    ];
    
    echo "<p>✅ Data preparata per la view</p>\n";
    
    // Prova il rendering
    try {
        $view = view('grower.dashboard', $viewData);
        echo "<p>✅ View istanziata</p>\n";
        
        $content = $view->render();
        echo "<p>✅ View renderizzata (length: " . strlen($content) . ")</p>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Errore nel rendering della view:</p>\n";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>\n";
        echo "<pre>" . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . "</pre>\n";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Errore generale:</p>\n";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>\n";
    echo "<pre>" . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . "</pre>\n";
}

echo "<p>Test completato: " . date('Y-m-d H:i:s') . "</p>\n";
?>