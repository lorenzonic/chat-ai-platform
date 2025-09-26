@extends('layouts.grower')

@section('title', 'Gestione Etichette Order Items')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🏷️ Gestione Etichette Order Items</h1>
        <p class="mt-2 text-gray-600">Gestisci e stampa le etichette per gli articoli dei tuoi ordini con QR code specifici</p>

        <!-- Modern Structure Notice -->
        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-green-600 text-xl">✅</span>
                </div>
                <div class="ml-3 flex-1">
                    <h4 class="text-sm font-medium text-green-800">Struttura Order Items Attiva</h4>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Stai visualizzando gli articoli importati con la nuova struttura order_items.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('grower.products.stickers.index', ['legacy' => '1']) }}"
                           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center text-sm">
                            📦 Vista Legacy Prodotti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        📋
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $orderItems->total() }}</h3>
                    <p class="text-sm text-gray-500">Order Items Totali</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        🏪
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $stores->count() }}</h3>
                    <p class="text-sm text-gray-500">Store Attivi</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $orders->count() }}</h3>
                    <p class="text-sm text-gray-500">Ordini con Items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">🔍 Filtri</h2>
            <form method="GET" action="{{ route('grower.products.stickers.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Store Filter -->
                    <div>
                        <label for="store_id" class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                        <select name="store_id" id="store_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tutti gli Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Order Filter -->
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Ordine</label>
                        <select name="order_id" id="order_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tutti gli Ordini</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                    {{ $order->order_number }} ({{ $order->created_at->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="order_date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Da</label>
                        <input type="date" name="order_date_from" id="order_date_from"
                               value="{{ request('order_date_from') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="order_date_to" class="block text-sm font-medium text-gray-700 mb-1">Data A</label>
                        <input type="date" name="order_date_to" id="order_date_to"
                               value="{{ request('order_date_to') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cerca per nome prodotto</label>
                        <input type="text" name="search" id="search"
                               value="{{ request('search') }}"
                               placeholder="Nome prodotto..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            🔍 Filtra
                        </button>
                        <a href="{{ route('grower.products.stickers.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            🗑️ Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">📋 Order Items ({{ $orderItems->total() }})</h2>
                <div class="flex space-x-3">
                    @if($orderItems->count() > 0)
                        <a href="{{ route('grower.products.stickers.bulk-print', request()->query()) }}"
                           target="_blank"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                            🖨️ Stampa Tutto
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order Item
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prodotto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantità
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Store
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ordine
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orderItems as $orderItem)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-600">{{ $orderItem->id }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            Item #{{ $orderItem->id }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $orderItem->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $orderItem->product_snapshot['name'] ?? $orderItem->product->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    SKU: {{ $orderItem->product_snapshot['sku'] ?? $orderItem->product->sku ?? 'N/A' }}
                                </div>
                                @if(isset($orderItem->product_snapshot['ean']))
                                    <div class="text-xs text-gray-400">
                                        EAN: {{ $orderItem->product_snapshot['ean'] }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($orderItem->quantity) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    € {{ number_format($orderItem->price, 2) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $orderItem->store->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $orderItem->store->slug ?? '' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $orderItem->order->order_number ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $orderItem->order->created_at->format('d/m/Y') ?? '' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $orderItem->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('grower.products.stickers.order-item', $orderItem) }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900 transition-colors px-3 py-1 rounded-lg border border-blue-200 hover:bg-blue-50">
                                        🏷️ Etichetta
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <div class="text-6xl mb-4">📦</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun Order Item Trovato</h3>
                                    <p class="text-gray-500">Non ci sono order items che corrispondono ai filtri selezionati.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('grower.products.stickers.index') }}"
                                           class="text-blue-600 hover:text-blue-500">
                                            Visualizza tutti gli order items
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orderItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orderItems->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form on select changes for better UX
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#store_id, #order_id');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
