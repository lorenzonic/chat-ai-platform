@extends('layouts.admin')

@section('title', 'Gestione Etichette Prodotti')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🏷️ Gestione Etichette Prodotti</h1>
        <p class="mt-2 text-gray-600">Gestisci e stampa le etichette per i prodotti con QR code specifici per ordine</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if(request()->hasAny(['search', 'store_id', 'grower_id', 'order_id']))
                            Order Items Filtrati
                        @else
                            Totale Order Items
                        @endif
                    </h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $orderItems->total() }}</p>
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
                    <p class="text-2xl font-bold text-green-600">{{ $orderItems->unique('order_id')->count() }}</p>
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
                    <p class="text-2xl font-bold text-purple-600">{{ $orderItems->unique('store_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        🌱
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Fornitori</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $orderItems->unique('grower_id')->count() }}</p>
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
                        Order Items in questo ordine: {{ $orderItems->total() }}
                    </p>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.products.index') }}"
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
            <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <option value="">Tutti gli Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter by Grower -->
                    <div>
                        <label for="grower_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Filtra per Fornitore
                        </label>
                        <select id="grower_id"
                                name="grower_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Tutti i Fornitori</option>
                            @foreach($growers as $grower)
                                <option value="{{ $grower->id }}" {{ request('grower_id') == $grower->id ? 'selected' : '' }}>
                                    {{ $grower->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Order ID (hidden if already filtered) -->
                    @if(request('order_id'))
                        <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                    @endif
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center space-x-4 pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        🔍 Applica Filtri
                    </button>

                    <a href="{{ route('admin.products.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        🔄 Reset Filtri
                    </a>

                    @if(request()->hasAny(['search', 'store_id', 'grower_id', 'order_id']))
                        <span class="text-sm text-gray-500">
                            Filtri attivi:
                            @if(request('search')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Nome: {{ request('search') }}</span> @endif
                            @if(request('store_id')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Store</span> @endif
                            @if(request('grower_id')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Coltivatore</span> @endif
                            @if(request('order_id')) <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ordine</span> @endif
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Lista Order Items per Etichette</h2>
        </div>

        @if($orderItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prodotto
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fornitore
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
                                data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orderItems as $orderItem)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $orderItem->product_snapshot['name'] ?? ($orderItem->product->name ?? 'N/A') }}
                                    </div>
                                    @if($orderItem->product_snapshot['variety'] ?? ($orderItem->product->variety ?? null))
                                        <div class="text-sm text-gray-500 ml-2">
                                            ({{ $orderItem->product_snapshot['variety'] ?? $orderItem->product->variety }})
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400">
                                    Qty: {{ $orderItem->quantity }}
                                    @if($orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? null))
                                        | EAN: {{ $orderItem->product_snapshot['ean'] ?? $orderItem->product->ean }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-green-600 font-medium text-xs">
                                                🌱
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.products.index', ['grower_id' => $orderItem->grower->id ?? '']) }}"
                                               class="text-green-700 hover:text-green-900 hover:underline">
                                                {{ $orderItem->grower->name ?? 'N/A' }}
                                            </a>
                                        </div>
                                        @if($orderItem->grower && $orderItem->grower->phone)
                                            <div class="text-xs text-gray-500">
                                                {{ $orderItem->grower->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <a href="{{ route('admin.products.index', ['order_id' => $orderItem->order_id]) }}"
                                       class="text-blue-700 hover:text-blue-900 hover:underline">
                                        {{ $orderItem->order->order_number ?? 'N/A' }}
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Cliente: {{ $orderItem->order->client ?? ($orderItem->store->name ?? 'N/A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <a href="{{ route('admin.products.index', ['store_id' => $orderItem->store->id ?? '']) }}"
                                       class="text-indigo-700 hover:text-indigo-900 hover:underline">
                                        {{ $orderItem->store->name ?? 'N/A' }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    €{{ number_format((float) $orderItem->prezzo_rivendita, 2, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Tot: €{{ number_format((float) ($orderItem->price * $orderItem->quantity), 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $orderItem->order->delivery_date ? date('d/m/Y', strtotime($orderItem->order->delivery_date)) : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.products.show', $orderItem) }}"
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    🏷️ Stampa Etichetta
                                </a>
                            </td>
                        </tr>
                        @endforeach
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orderItems->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📦</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun order item trovato</h3>
                <p class="text-gray-600">Non ci sono order items disponibili per la stampa etichette.</p>
            </div>
        @endif
    </div>
</div>
@endsection
