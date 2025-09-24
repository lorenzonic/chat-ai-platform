<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\OrderController;
use App\Services\PlantSitesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chatbot API routes
Route::prefix('chatbot/{store:slug}')->group(function () {
    Route::get('/info', [ChatbotController::class, 'getStoreInfo']);
    Route::post('/message', [ChatbotController::class, 'sendMessage']);
    Route::post('/track-scan', [ChatbotController::class, 'trackQrScan']);
    Route::get('/history', [ChatbotController::class, 'getChatHistory']);
});

// Store API routes (for lead collection)
Route::prefix('stores/{store:slug}')->group(function () {
    Route::post('/save-lead', [ChatbotController::class, 'saveLead']);
});

// Sites validation API
Route::post('/validate-sites', function () {
    $sitesManager = new PlantSitesManager();
    $validation = $sitesManager->validateSites();

    return response()->json($validation);
});

// Admin API routes for orders (temporaneamente senza auth per debug)
Route::prefix('admin')->group(function () {
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});
