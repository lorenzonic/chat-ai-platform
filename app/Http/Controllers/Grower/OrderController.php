<?php

namespace App\Http\Controllers\Grower;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders for the authenticated grower.
     */
    public function index()
    {
        $grower = auth('grower')->user();

        // Get orders that have order items from this grower
        $orders = Order::whereHas('orderItems', function ($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })
        ->with(['store', 'orderItems' => function ($query) use ($grower) {
            $query->where('grower_id', $grower->id)->with('product');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('grower.orders.index', compact('orders', 'grower'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $grower = auth('grower')->user();

        // Ensure the order has order items from this grower
        if (!$order->orderItems()->where('grower_id', $grower->id)->exists()) {
            abort(404);
        }

        $order->load(['store', 'orderItems' => function ($query) use ($grower) {
            $query->where('grower_id', $grower->id)->with('product');
        }]);

        return view('grower.orders.show', compact('order', 'grower'));
    }
}
