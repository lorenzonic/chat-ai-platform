<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\DetectQrFormat;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// QR Code Optimization System - Short URL with Intelligent Redirect
// Pattern: /{short_code}/01/{gtin14}?r={ref}
Route::get('/{shortCode}/01/{gtin14}', function () {
    // Gestito dal middleware DetectQrFormat
    return response('QR Redirect Handler', 200);
})
->middleware(DetectQrFormat::class)
->where([
    'shortCode' => '[a-z]\d+',      // es: f6, v22, b21
    'gtin14' => '\d{14}',            // 14 cifre GTIN
])
->name('qr.short.redirect');

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin redirect
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

// Database connection test route
Route::get('/test-db-connection', function () {
    try {
        $connection = DB::connection();
        $pdo = $connection->getPdo();

        $result = [
            'status' => 'success',
            'connection' => 'OK',
            'driver' => $connection->getDriverName(),
            'database' => $connection->getDatabaseName(),
            'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'timestamp' => now(),
            'tables' => []
        ];

        // Check tables
        $tables = DB::select('SHOW TABLES');
        $result['tables_count'] = count($tables);

        // Check required tables
        $required = ['users', 'stores', 'conversations', 'interactions'];
        foreach ($required as $table) {
            $exists = Schema::hasTable($table);
            $count = $exists ? DB::table($table)->count() : 0;
            $result['tables'][$table] = [
                'exists' => $exists,
                'records' => $count
            ];
        }

        return response()->json($result, 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'timestamp' => now()
        ], 500);
    }
});

// Test route for analytics data
Route::get('/test-analytics', function () {
    $store = \App\Models\Store::first();
    if (!$store) {
        return response()->json(['error' => 'No store found']);
    }

    return response()->json([
        'store' => $store->name,
        'interactions' => $store->interactions()->count(),
        'leads' => $store->leads()->count(),
        'unique_visitors' => $store->interactions()->distinct('ip')->count(),
    ]);
})->name('test.analytics');

// Test route for conversation history
Route::get('/test-conversation/{sessionId}', function ($sessionId) {
    $store = \App\Models\Store::first();
    if (!$store) {
        return response()->json(['error' => 'No store found']);
    }

    $conversationHistory = \App\Models\ChatLog::where('session_id', $sessionId)
        ->where('store_id', $store->id)
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json([
        'session_id' => $sessionId,
        'store' => $store->name,
        'conversation_count' => $conversationHistory->count(),
        'history' => $conversationHistory->map(function($chat) {
            return [
                'timestamp' => $chat->created_at->format('Y-m-d H:i:s'),
                'user_message' => $chat->user_message,
                'ai_response' => $chat->ai_response,
            ];
        })
    ]);
})->name('test.conversation');

require __DIR__.'/auth.php';

// Debug route for trends
Route::get('/debug-trends', function () {
    $googleTrendsService = app(\App\Services\Trends\GoogleTrendsService::class);

    $data = $googleTrendsService->getTrends(30);

    return response()->json([
        'success' => true,
        'data' => $data,
        'has_keywords' => isset($data['keywords']),
        'keywords_count' => isset($data['keywords']) ? count($data['keywords']) : 0,
        'first_keyword' => isset($data['keywords'][0]) ? $data['keywords'][0] : null,
    ]);
});

// Test route for Python scraping
Route::get('/test-python-scraping', function () {
    $ecommerceService = app(\App\Services\Trends\EcommerceDataService::class);
    $ecommerceData = $ecommerceService->getEcommerceData(30, [], 'simulation');

    return response()->json([
        'success' => true,
        'data' => $ecommerceData,
        'message' => 'Python scraping test completed'
    ]);
});

// Include demo routes
if (file_exists(__DIR__.'/demo.php')) {
    require __DIR__.'/demo.php';
}

// QR EAN/GTIN redirect route
Route::get('/qr/{ean_code}', [\App\Http\Controllers\QrRedirectController::class, 'redirect'])->name('qr.redirect');

// QR Code short URLs con redirect intelligente (GS1 Digital Link format)
// Pattern: /{short_code}/01/{gtin14}?r={ref}
Route::middleware([\App\Http\Middleware\DetectQrFormat::class])
    ->group(function () {
        Route::get('/{shortCode}/01/{gtin14}', function () {
            // Gestito dal middleware DetectQrFormat
            return response()->json(['error' => 'Middleware should handle this'], 500);
        })->where([
            'shortCode' => '[a-z]\d+',
            'gtin14' => '\d{14}',
        ])->name('qr.short');
    });
