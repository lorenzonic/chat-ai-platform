<?php

use App\Http\Controllers\Store\Auth\LoginController;
use App\Http\Controllers\Store\DashboardController;
use App\Http\Controllers\Store\ChatbotController;
use App\Http\Controllers\Store\KnowledgeController;
use App\Http\Controllers\Store\ProfileController;
use App\Http\Controllers\Store\NewsletterController;
use App\Http\Controllers\Store\AnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store Routes
|--------------------------------------------------------------------------
*/

// Store Authentication Routes
Route::prefix('store')->name('store.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:store')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated store routes
    Route::middleware('isStore')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('chatbot/settings', [ChatbotController::class, 'edit'])->name('chatbot.edit');
        Route::put('chatbot/settings', [ChatbotController::class, 'update'])->name('chatbot.update');

        // Knowledge Base routes
        Route::resource('knowledge', KnowledgeController::class);

        // Newsletter & Lead routes
        Route::get('newsletters/leads', [NewsletterController::class, 'leads'])->name('newsletters.leads');
        Route::post('newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('newsletters.send');
        Route::get('newsletters/{newsletter}/preview', [NewsletterController::class, 'preview'])->name('newsletters.preview');
        Route::resource('newsletters', NewsletterController::class);

        // Analytics routes
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics-data', [AnalyticsController::class, 'getAnalyticsData'])->name('analytics.data');
        Route::get('analytics/export', [AnalyticsController::class, 'exportCsv'])->name('analytics.export');
        Route::get('analytics/test-data', [AnalyticsController::class, 'testData'])->name('analytics.test');
        Route::get('analytics/debug', function() {
            $store = auth()->guard('store')->user();
            return view('store.analytics.debug', compact('store'));
        })->name('analytics.debug');

        // Profile routes
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});

// Debug route (no auth required for testing)
Route::get('debug/geographic', [AnalyticsController::class, 'debugGeographic'])->name('debug.geographic');

// GS1 Digital Link Route - Must be before generic store route
// Format: /{store:slug}/01/{gtin}?question=...&ref=...
Route::get('/{store:slug}/01/{gtin}', function(\App\Models\Store $store, string $gtin) {
    // Validate GTIN format (EAN-13 = 13 digits)
    if (!preg_match('/^\d{13}$/', $gtin)) {
        abort(404, 'Invalid GTIN format');
    }

    // If a tracking ref is present, record a QrScan immediately so scans are counted
    try {
        $ref = request('ref');
        if ($ref) {
            $qrCode = \App\Models\QrCode::where('ref_code', $ref)
                ->where('store_id', $store->id)
                ->first();

            if ($qrCode) {
                $ua = request()->userAgent();
                $deviceType = 'desktop';
                if (preg_match('/Mobile|Android|iPhone|iPad/', $ua)) {
                    $deviceType = preg_match('/iPad/', $ua) ? 'tablet' : 'mobile';
                }

                \App\Models\QrScan::create([
                    'store_id' => $store->id,
                    'qr_code_id' => $qrCode->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => $ua,
                    'referer' => request()->header('referer'),
                    'device_type' => $deviceType,
                ]);
            }
        }
    } catch (\Exception $e) {
        // Don't block the user if analytics fails; log and continue
        \Log::warning('GS1 scan tracking failed', ['error' => $e->getMessage(), 'store_id' => $store->id, 'gtin' => $gtin]);
    }

    // Pass the GTIN to the chatbot view for potential product-specific handling
    return view('store.frontend.chatbot-vue', [
        'store' => $store,
        'gtin' => $gtin,
        'gs1_digital_link' => true
    ]);
})->name('store.chatbot.gs1');

// Store Frontend Routes (for chatbot)
Route::get('/{store:slug}', function(\App\Models\Store $store) {
    // Support tracking for legacy/non-GS1 QR links that include a ref query param
    try {
        $ref = request('ref');
        if ($ref) {
            $qrCode = \App\Models\QrCode::where('ref_code', $ref)
                ->where('store_id', $store->id)
                ->first();

            if ($qrCode) {
                $ua = request()->userAgent();
                $deviceType = 'desktop';
                if (preg_match('/Mobile|Android|iPhone|iPad/', $ua)) {
                    $deviceType = preg_match('/iPad/', $ua) ? 'tablet' : 'mobile';
                }

                \App\Models\QrScan::create([
                    'store_id' => $store->id,
                    'qr_code_id' => $qrCode->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => $ua,
                    'referer' => request()->header('referer'),
                    'device_type' => $deviceType,
                ]);
            }
        }
    } catch (\Exception $e) {
        \Log::warning('Scan tracking failed', ['error' => $e->getMessage(), 'store_id' => $store->id]);
    }

    return view('store.frontend.chatbot-vue', compact('store'));
})->name('store.chatbot');
