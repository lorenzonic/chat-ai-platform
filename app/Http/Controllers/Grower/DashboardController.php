<?php

namespace App\Http\Controllers\Grower;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $grower = Auth::guard('grower')->user();

        // Statistiche per il dashboard
        $totalProducts = Product::where('grower_id', $grower->id)->count();

        $totalOrders = Order::whereHas('products', function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })->count();

        $recentProducts = Product::where('grower_id', $grower->id)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::whereHas('products', function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })
        ->with(['store', 'products' => function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        }])
        ->latest()
        ->take(5)
        ->get();

        $lowStockProducts = Product::where('grower_id', $grower->id)
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();

        $outOfStockProducts = Product::where('grower_id', $grower->id)
            ->where('quantity', 0)
            ->count();

        return view('grower.dashboard', compact(
            'grower',
            'totalProducts',
            'totalOrders',
            'recentProducts',
            'recentOrders',
            'lowStockProducts',
            'outOfStockProducts'
        ));
    }
}
