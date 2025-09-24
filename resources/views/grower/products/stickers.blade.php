@extends('layouts.grower')

@section('title', 'Gestione Etichette Prodotti')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🏷️ Gestione Etichette Prodotti</h1>
        <p class="mt-2 text-gray-600">Gestisci e stampa le etichette per i tuoi prodotti con QR code specifici per ordine</p>

        <!-- Legacy Notice -->
        @php
            $hasOrderItems = \App\Models\OrderItem::where('grower_id', auth('grower')->id())->exists();
        @endphp

        @if($hasOrderItems)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="text-blue-600 text-xl">ℹ️</span>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium text-blue-800">Nuova Struttura Order Items Disponibile</h4>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>È disponibile una nuova versione che mostra i dettagli specifici degli order items importati.</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('grower.products.stickers.index') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center text-sm">
                                🆕 Passa alla Nuova Vista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if(request()->hasAny(['search', 'store_id', 'order_id']))
                            Prodotti Filtrati
                        @else
                            I Tuoi Prodotti
                        @endif
                    </h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $products->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        🛒
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Ordini Coinvolti</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $products->unique('order_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        🏪
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Store Coinvolti</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $products->unique('store_id')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Order Info -->
    @if($selectedOrder)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🛒
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900">
                        Filtrato per Ordine: {{ $selectedOrder->order_number }}
                    </h3>
                    <p class="text-blue-700">
                        Store: {{ $selectedOrder->store->name ?? 'N/A' }} |
                        Prodotti in questo ordine: {{ $products->total() }}
                    </p>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('grower.products.stickers.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                        ✕ Rimuovi Filtro
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">🔍 Filtri di Ricerca</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('grower.products.stickers.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search by Name -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Cerca per Nome
                        </label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nome prodotto..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Filter by Store -->
                    <div>
                        <label for="store_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Filtra per Store
                        </label>
                        <select id="store_id"
                                name="store_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Tutti i tuoi Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter by Order ID -->
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Filtra per Ordine
                        </label>
                        <input type="text"
                               id="order_id"
                               name="order_id"
                               value="{{ request('order_id') }}"
                               placeholder="ID Ordine..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="order_date_from" class="block text-sm font-medium text-gray-700 mb-1">
                            Data Da
                        </label>
                        <input type="date"
                               id="order_date_from"
                               name="order_date_from"
                               value="{{ request('order_date_from') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="order_date_to" class="block text-sm font-medium text-gray-700 mb-1">
                            Data A
                        </label>
                        <input type="date"
                               id="order_date_to"
                               name="order_date_to"
                               value="{{ request('order_date_to') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center space-x-4 pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        🔍 Applica Filtri
                    </button>

                    <a href="{{ route('grower.products.stickers.bulk-print', array_merge(['legacy' => 1], request()->all())) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        🖨️ Stampa Bulk
                    </a>

                    <a href="{{ route('grower.products.stickers.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        🔄 Reset Filtri
                    </a>

                    @if(request()->hasAny(['search', 'store_id', 'order_id', 'order_date_from', 'order_date_to']))
                        <span class="text-sm text-gray-500">
                            Filtri attivi:
                            @if(request('search')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Nome: {{ request('search') }}</span> @endif
                            @if(request('store_id')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Store</span> @endif
                            @if(request('order_id')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ordine</span> @endif
                            @if(request('order_date_from')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Da: {{ request('order_date_from') }}</span> @endif
                            @if(request('order_date_to')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">A: {{ request('order_date_to') }}</span> @endif
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Lista Prodotti</h2>
        </div>

        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prodotto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ordine
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Store
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prezzo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Consegna
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </div>
                                    @if($product->variety)
                                        <div class="text-sm text-gray-500 ml-2">
                                            ({{ $product->variety }})
                                        </div>
                                    @endif
                                </div>
                                @if($product->ean)
                                    <div class="text-xs text-gray-400">EAN: {{ $product->ean }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->order)
                                        <a href="{{ route('grower.products.stickers.index', ['order_id' => $product->order->id]) }}"
                                           class="text-blue-700 hover:text-blue-900 hover:underline">
                                            {{ $product->order->order_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    Cliente: {{ $product->client ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->store)
                                        <a href="{{ route('grower.products.stickers.index', ['store_id' => $product->store->id]) }}"
                                           class="text-indigo-700 hover:text-indigo-900 hover:underline">
                                            {{ $product->store->name }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    €{{ number_format((float) $product->price, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $product->delivery_date ? date('d/m/Y', strtotime($product->delivery_date)) : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('grower.products.stickers.show', $product) }}"
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    🏷️ Stampa Etichetta
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📦</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun prodotto trovato</h3>
                <p class="text-gray-600">Non ci sono prodotti disponibili per la stampa etichette.</p>
                @if(request()->hasAny(['search', 'store_id', 'order_id']))
                    <div class="mt-4">
                        <a href="{{ route('grower.products.stickers.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            🔄 Rimuovi tutti i filtri
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
