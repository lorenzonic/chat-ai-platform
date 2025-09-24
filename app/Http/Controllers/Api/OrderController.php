<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function show(Order $order): JsonResponse
    {
        try {
            $order->load(['store', 'products']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'store_id' => $order->store_id,
                    'delivery_date' => $order->delivery_date,
                    'status' => $order->status,
                    'notes' => $order->notes,
                    'total_amount' => $order->total_amount,
                    'total_items' => $order->total_items,
                    'store' => $order->store ? [
                        'id' => $order->store->id,
                        'name' => $order->store->name,
                        'client_code' => $order->store->client_code,
                    ] : null,
                    'products' => $order->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'code' => $product->code,
                            'ean' => $product->ean,
                            'quantity' => $product->quantity,
                            'price' => $product->price,
                        ];
                    }),
                    'calculated_total_quantity' => $order->calculateTotalQuantity(),
                    'calculated_total_amount' => $order->calculateTotalAmount(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
