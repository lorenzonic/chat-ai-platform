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

// Store Frontend Routes (for chatbot)
Route::get('/{store:slug}', function(\App\Models\Store $store) {
    return view('store.frontend.chatbot-vue', compact('store'));
})->name('store.chatbot');
