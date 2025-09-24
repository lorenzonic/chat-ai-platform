<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testOrder($id)
    {
        // Test molto semplice senza middleware
        $order = Order::with(['store', 'products'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'store_name' => $order->store->name ?? 'N/A',
            'products_count' => $order->products->count(),
            'message' => 'Test successful - Order loaded correctly'
        ]);
    }

    public function testPage($id)
    {
        $order = Order::with(['store', 'products'])->findOrFail($id);

        return view('admin.orders.test-order', compact('order'));
    }
}
