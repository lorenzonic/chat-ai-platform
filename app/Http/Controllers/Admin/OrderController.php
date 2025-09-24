<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $orders = Order::with(['store', 'products'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('admin.orders.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'client' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:100',
            'pia' => 'nullable|string|max:100',
            'pro' => 'nullable|string|max:100',
            'transport' => 'nullable|string|max:255',
            'transport_cost' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        // Generate unique order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'store_id' => $request->store_id,
            'order_number' => $orderNumber,
            'client' => $request->client,
            'cc' => $request->cc,
            'pia' => $request->pia,
            'pro' => $request->pro,
            'transport' => $request->transport,
            'transport_cost' => $request->transport_cost,
            'delivery_date' => $request->delivery_date,
            'notes' => $request->notes,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.orders.show', $order)
                        ->with('success', 'Ordine creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $order->load(['store', 'orderItems.product', 'orderItems.grower']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
