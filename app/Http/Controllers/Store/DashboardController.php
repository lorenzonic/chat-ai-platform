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
            'chat_sessions' => $store->chatLogs()
                ->where('created_at', '>=', $thisMonth)
                ->distinct('session_id')
                ->count('session_id'),
            'leads_generated' => $store->leads()->count(),
            'qr_scans' => $store->qrCodes()
                ->withCount(['scans' => function($query) use ($thisMonth) {
                    $query->where('created_at', '>=', $thisMonth);
                }])
                ->get()
                ->sum('scans_count'),
            'ai_responses' => $store->chatLogs()->count(),
        ];

        return view('store.dashboard', compact('stats'));
    }
}
