<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Store;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items
     */
    public function index(Request $request): View
    {
        $query = OrderItem::with(['product', 'order', 'store', 'grower']);

        // Filter by order ID
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by grower
        if ($request->filled('grower_id')) {
            $query->where('grower_id', $request->grower_id);
        }

        // Filter by order date
        if ($request->filled('order_date_from')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->order_date_from);
            });
        }

        if ($request->filled('order_date_to')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->order_date_to);
            });
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhere('product_snapshot->name', 'like', '%' . $request->search . '%');
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->withQueryString();

        // Get options for filters
        $stores = Store::whereHas('orderItems')->orderBy('name')->get();
        $growers = Grower::whereHas('orderItems')->orderBy('name')->get();
        $orders = Order::whereHas('orderItems')->orderBy('created_at', 'desc')->get();

        // Get statistics
        $stats = [
            'total_items' => OrderItem::count(),
            'total_orders' => Order::whereHas('orderItems')->count(),
            'total_stores' => Store::whereHas('orderItems')->count(),
            'total_growers' => Grower::whereHas('orderItems')->count(),
            'total_quantity' => OrderItem::sum('quantity'),
            'total_value' => OrderItem::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
        ];

        return view('admin.order-items.index', compact(
            'orderItems',
            'stores',
            'growers',
            'orders',
            'stats'
        ));
    }

    /**
     * Display the specified order item
     */
    public function show(Request $request, OrderItem $orderItem): View
    {
        $orderItem->load(['product', 'order', 'store', 'grower']);

        return view('admin.order-items.show', compact('orderItem'));
    }

    /**
     * Show the form for editing the specified order item
     */
    public function edit(OrderItem $orderItem): View
    {
        $orderItem->load(['product', 'order', 'store', 'grower']);

        $stores = Store::orderBy('name')->get();
        $growers = Grower::orderBy('name')->get();
        $orders = Order::orderBy('created_at', 'desc')->get();

        return view('admin.order-items.edit', compact('orderItem', 'stores', 'growers', 'orders'));
    }

    /**
     * Update the specified order item in storage
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'store_id' => 'required|exists:stores,id',
            'grower_id' => 'required|exists:growers,id',
        ]);

        $orderItem->update([
            'quantity' => $request->quantity,
            'price' => $request->price,
            'store_id' => $request->store_id,
            'grower_id' => $request->grower_id,
        ]);

        return redirect()->route('admin.order-items.index')
                        ->with('success', 'Order item aggiornato con successo!');
    }

    /**
     * Remove the specified order item from storage
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return redirect()->route('admin.order-items.index')
                        ->with('success', 'Order item eliminato con successo!');
    }

    /**
     * Bulk operations on order items
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:order_items,id'
        ]);

        $orderItems = OrderItem::whereIn('id', $request->selected_items);

        switch ($request->action) {
            case 'delete':
                $count = $orderItems->count();
                $orderItems->delete();
                return redirect()->route('admin.order-items.index')
                                ->with('success', "Eliminati {$count} order items con successo!");

            case 'export':
                // TODO: Implement CSV export
                return redirect()->route('admin.order-items.index')
                                ->with('info', 'Funzione di export in sviluppo');
        }

        return redirect()->route('admin.order-items.index');
    }
}
