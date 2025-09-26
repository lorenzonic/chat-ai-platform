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

        // Check if grower is authenticated
        if (!$grower) {
            return redirect()->route('grower.login')
                ->with('error', 'Devi effettuare il login per accedere alla dashboard.');
        }

        try {
            // Statistiche per il dashboard con controlli sicuri
            $totalProducts = Product::where('grower_id', $grower->id)->count() ?? 0;

            $totalOrders = Order::whereHas('orderItems.product', function($query) use ($grower) {
                $query->where('grower_id', $grower->id);
            })->count() ?? 0;

            $productsInOrders = $grower->products()
                ->whereHas('orderItems')
                ->count() ?? 0;

            $recentProducts = Product::where('grower_id', $grower->id)
                ->latest()
                ->take(5)
                ->get() ?? collect();

            $recentOrders = Order::whereHas('orderItems.product', function($query) use ($grower) {
                $query->where('grower_id', $grower->id);
            })
            ->with(['store', 'orderItems.product' => function($query) use ($grower) {
                $query->where('grower_id', $grower->id);
            }])
            ->latest()
            ->take(5)
            ->get() ?? collect();

            $lowStockProducts = Product::where('grower_id', $grower->id)
                ->where('quantity', '<=', 10)
                ->where('quantity', '>', 0)
                ->count() ?? 0;

            $outOfStockProducts = Product::where('grower_id', $grower->id)
                ->where('quantity', 0)
                ->count() ?? 0;
        } catch (\Exception $e) {
            // Fallback values in case of any database issues
            $totalProducts = 0;
            $totalOrders = 0;
            $productsInOrders = 0;
            $recentProducts = collect();
            $recentOrders = collect();
            $lowStockProducts = 0;
            $outOfStockProducts = 0;
        }

        return view('grower.dashboard', compact(
            'grower',
            'totalProducts',
            'totalOrders',
            'productsInOrders',
            'recentProducts',
            'recentOrders',
            'lowStockProducts',
            'outOfStockProducts'
        ));
    }
}
