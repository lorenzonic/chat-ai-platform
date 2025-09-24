@extends('layouts.grower')

@section('title', 'Order Details - Grower Portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
                        <p class="text-gray-500">{{ $order->order_number ?? 'No order number' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                                   {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                      ($order->status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                        <div class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Store Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Store Information</h3>
                        <div class="space-y-2">
                            <div><strong>Name:</strong> {{ $order->store->name }}</div>
                            <div><strong>Email:</strong> {{ $order->store->email }}</div>
                            @if($order->store->phone)
                                <div><strong>Phone:</strong> {{ $order->store->phone }}</div>
                            @endif
                            @if($order->store->address)
                                <div><strong>Address:</strong> {{ $order->store->address }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Grower Information -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Your Company</h3>
                        <div class="space-y-2">
                            <div><strong>Company:</strong> {{ $grower->company_name }}</div>
                            <div><strong>Contact:</strong> {{ $grower->contact_name }}</div>
                            <div><strong>Email:</strong> {{ $grower->email }}</div>
                            @if($grower->phone)
                                <div><strong>Phone:</strong> {{ $grower->phone }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                @if($order->notes)
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Order Notes</h3>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                @endif

                <!-- Your Products in this Order -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Your Products in this Order</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        EAN
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->ean ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->quantity ?? 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        €{{ number_format($product->price ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        €{{ number_format(($product->price ?? 0) * ($product->quantity ?? 1), 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                        Total for your products:
                                    </td>
                                    <td class="px-6 py-3 text-sm font-bold text-gray-900">
                                        €{{ number_format($order->products->sum(function($product) { return ($product->price ?? 0) * ($product->quantity ?? 1); }), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('grower.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        ← Back to Orders
                    </a>

                    <div class="space-x-2">
                        @if($order->status !== 'completed')
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="alert('Feature coming soon!')">
                                Update Status
                            </button>
                        @endif
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="window.print()">
                            Print Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
