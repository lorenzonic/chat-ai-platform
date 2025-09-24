@extends('layouts.grower')

@section('title', 'Etichette Order Items')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with legacy toggle -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🏷️ Etichette Order Items</h1>
                <p class="text-gray-600">Gestisci le etichette per i tuoi order items (nuova struttura)</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('grower.products.stickers.bulk-print', request()->all()) }}"
                   class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    🖨️ Stampa Bulk
                </a>
                <a href="{{ route('grower.products.stickers.index', ['legacy' => 1] + request()->all()) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    📦 Vista Legacy
                </a>
                <a href="{{ route('grower.products.stickers.index') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    🔄 Aggiorna
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Totale Items</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $orderItems->total() }}</p>
                    </div>
                    <span class="text-3xl">📦</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Negozi</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stores->count() }}</p>
                    </div>
                    <span class="text-3xl">🏪</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-600 text-sm font-medium">Ordini</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $orders->count() }}</p>
                    </div>
                    <span class="text-3xl">📋</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 text-sm font-medium">Valore Totale</p>
                        <p class="text-2xl font-bold text-orange-900">
                            €{{ number_format((float)$orderItems->sum(function($item) { return (float)$item->total_price; }), 2, ',', '.') }}
                        </p>
                    </div>
                    <span class="text-3xl">💰</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Filtri</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Store Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Negozio</label>
                    <select name="store_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tutti i negozi</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordine</label>
                    <select name="order_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tutti gli ordini</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                {{ $order->order_number }}
                                @if($order->delivery_date)
                                    ({{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Da</label>
                    <input type="date" name="order_date_from" value="{{ request('order_date_from') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data A</label>
                    <input type="date" name="order_date_to" value="{{ request('order_date_to') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cerca prodotto</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nome prodotto..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Submit -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex-1 transition-colors">
                        🔍 Filtra
                    </button>
                    <a href="{{ route('grower.products.stickers.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                        🧹
                    </a>
                </div>
            </form>
        </div>

        <!-- Order Items Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📦 Order Items ({{ $orderItems->total() }} risultati)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negozio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtà</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Totale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orderItems as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                Codice: {{ $item->product->code ?? 'N/A' }}
                                            </div>
                                            @if($item->ean)
                                                <div class="text-xs text-gray-400">EAN: {{ $item->ean }}</div>
                                            @endif
                                            @if($item->sku && $item->sku !== $item->product->code)
                                                <div class="text-xs text-blue-600">SKU: {{ $item->sku }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->order->order_number }}</div>
                                    @if($item->order->delivery_date)
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->order->delivery_date)->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->store->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">€{{ number_format((float)$item->unit_price, 2, ',', '.') }}</div>
                                    @if($item->prezzo_rivendita && $item->prezzo_rivendita != $item->unit_price)
                                        <div class="text-xs text-green-600">Rivendita: €{{ number_format((float)$item->prezzo_rivendita, 2, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">€{{ number_format((float)$item->total_price, 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('grower.order-items.label', $item) }}"
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                                            🏷️ Etichetta
                                        </a>
                                        <a href="{{ route('grower.products.stickers.show', $item->product) }}"
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            👁️ Dettagli
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <div class="text-4xl mb-4">📦</div>
                                        <p class="text-lg font-medium">Nessun order item trovato</p>
                                        <p class="text-sm mt-2">Prova a modificare i filtri o verifica che ci siano ordini importati</p>
                                        <div class="mt-4">
                                            <a href="{{ route('grower.products.stickers.index', ['legacy' => 1]) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                Passa alla vista legacy →
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
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $orderItems->links() }}
                </div>
            @endif
        </div>

        <!-- Info Panel -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-blue-600 text-xl">ℹ️</span>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Informazioni Order Items</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>• Questa vista mostra gli order items dalla nuova struttura importata</p>
                        <p>• Ogni riga rappresenta un prodotto specifico all'interno di un ordine</p>
                        <p>• I prezzi e le quantità sono specifici per questo order item</p>
                        <p>• Le etichette includono informazioni complete su ordine, negozio e prodotto</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
