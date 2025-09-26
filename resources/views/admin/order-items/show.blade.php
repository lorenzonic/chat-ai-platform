@extends('layouts.admin')

@section('title', 'Dettagli Order Item')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Order Item #{{ $orderItem->id }}</h1>
                    <p class="mt-2 text-gray-600">Dettagli completi dell'articolo dell'ordine</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.order-items.edit', $orderItem) }}"
                       class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        ✏️ Modifica
                    </a>
                    <a href="{{ route('admin.order-items.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        ← Torna alla Lista
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Order Item Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">📋 Dettagli Order Item</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID</label>
                        <p class="mt-1 text-sm text-gray-900">#{{ $orderItem->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantità</label>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ number_format($orderItem->quantity) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prezzo Unitario</label>
                        <p class="mt-1 text-lg font-bold text-green-600">€ {{ number_format($orderItem->price, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Totale</label>
                        <p class="mt-1 text-xl font-bold text-green-700">€ {{ number_format($orderItem->quantity * $orderItem->price, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Creazione</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $orderItem->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ultimo Aggiornamento</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $orderItem->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">🏷️ Dettagli Prodotto</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome Prodotto</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $orderItem->product_snapshot['name'] ?? $orderItem->product->name ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $orderItem->product_snapshot['sku'] ?? $orderItem->product->sku ?? 'N/A' }}
                        </p>
                    </div>

                    @if(isset($orderItem->product_snapshot['ean']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700">EAN</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $orderItem->product_snapshot['ean'] }}</p>
                        </div>
                    @endif

                    @if(isset($orderItem->product_snapshot['description']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descrizione</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $orderItem->product_snapshot['description'] }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product ID</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($orderItem->product)
                                <a href="#" class="text-blue-600 hover:text-blue-800">
                                    #{{ $orderItem->product->id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Store Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">🏪 Dettagli Store</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome Store</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $orderItem->store->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $orderItem->store->slug ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Store ID</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($orderItem->store)
                                <a href="#" class="text-blue-600 hover:text-blue-800">
                                    #{{ $orderItem->store->id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Grower Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">🌱 Dettagli Grower</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome Grower</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $orderItem->grower->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $orderItem->grower->email ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Grower ID</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($orderItem->grower)
                                <a href="#" class="text-blue-600 hover:text-blue-800">
                                    #{{ $orderItem->grower->id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Order Details -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">📦 Dettagli Ordine</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Numero Ordine</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $orderItem->order->order_number ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $orderItem->order->client ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Ordine</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $orderItem->order->created_at->format('d/m/Y H:i') ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order ID</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($orderItem->order)
                                <a href="{{ route('admin.orders.show', $orderItem->order) }}" class="text-blue-600 hover:text-blue-800">
                                    #{{ $orderItem->order->id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Snapshot (if available) -->
        @if($orderItem->product_snapshot)
            <div class="mt-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">📸 Product Snapshot</h2>
                    <p class="text-sm text-gray-600">Dati del prodotto al momento dell'ordine</p>
                </div>
                <div class="p-6">
                    <pre class="text-xs text-gray-600 bg-gray-50 p-4 rounded-lg overflow-x-auto">{{ json_encode($orderItem->product_snapshot, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <form method="POST" action="{{ route('admin.order-items.destroy', $orderItem) }}"
                  onsubmit="return confirm('Sei sicuro di voler eliminare questo order item?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    🗑️ Elimina Order Item
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
