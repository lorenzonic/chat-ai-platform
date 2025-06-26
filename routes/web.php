<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
