<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the store dashboard.
     */
    public function index(): View
    {
        $store = auth('store')->user();
        $now = Carbon::now();
        $thisMonth = $now->startOfMonth();

        // Get statistics
        $stats = [
            'chat_sessions' => $store->interactions()
                ->where('created_at', '>=', $thisMonth)
                ->distinct('ip')
                ->count(),
            'leads_generated' => $store->leads()->count(),
            'qr_scans' => $store->interactions()
                ->where('created_at', '>=', $thisMonth)
                ->whereNotNull('qr_code_id')
                ->count(),
            'ai_responses' => $store->interactions()->count(),
        ];

        return view('store.dashboard', compact('stats'));
    }
}
