<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TestAnalyticsController extends Controller
{
    public function test(Request $request)
    {
        $stores = Store::orderBy('name')->get();
        $stats = [
            'total_stores' => Store::count(),
            'active_stores' => Store::where('is_active', true)->count(),
            'premium_stores' => Store::where('is_premium', true)->count(),
        ];

        return view('admin.analytics.test', compact('stores', 'stats'));
    }
}
