@extends('layouts.grower')

@section('title', 'Gestione Etichette')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏷️ Gestione Etichette</h1>
                <p class="mt-2 text-gray-600">Visualizza e stampa etichette per i tuoi prodotti ordinati</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('grower.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    🏠 Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🏷️
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Etichette Totali</h3>
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
                    <h3 class="text-lg font-medium text-gray-900">Ordini Attivi</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $orderItems->groupBy('order_id')->count() }}</p>
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
                    <h3 class="text-lg font-medium text-gray-900">Negozi</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $orderItems->groupBy('store_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        💰
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Valore Totale</h3>
                    <p class="text-2xl font-bold text-orange-600">€{{ number_format((float)$orderItems->sum('total_price'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Filtri</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('grower.order-items.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search by Product -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cerca Prodotto</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nome prodotto..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filter by Store -->
                <div>
                    <label for="store_id" class="block text-sm font-medium text-gray-700 mb-2">Negozio</label>
                    <select id="store_id"
                            name="store_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tutti i negozi</option>
                        @foreach($orderItems->groupBy('store_id') as $storeId => $items)
                            @if($items->first()->store)
                                <option value="{{ $storeId }}" {{ request('store_id') == $storeId ? 'selected' : '' }}>
                                    {{ $items->first()->store->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Order -->
                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Ordine</label>
                    <select id="order_id"
                            name="order_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tutti gli ordini</option>
                        @foreach($orderItems->groupBy('order_id') as $orderId => $items)
                            @if($items->first()->order)
                                <option value="{{ $orderId }}" {{ request('order_id') == $orderId ? 'selected' : '' }}>
                                    {{ $items->first()->order->order_number }} - {{ $items->first()->order->store->name ?? 'N/A' }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150">
                        🔍 Filtra
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['search', 'store_id', 'order_id']))
                <div class="mt-4">
                    <a href="{{ route('grower.order-items.index') }}"
                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        ✖️ Rimuovi Filtri
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Prodotti da Etichettare ({{ $orderItems->total() }})</h2>
                @if($orderItems->count() > 0)
                    <button onclick="printAllLabels()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150">
                        🖨️ Stampa Tutte le Etichette
                    </button>
                @endif
            </div>
        </div>

        @if($orderItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prodotto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ordine
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Negozio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantità
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prezzo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Ordine
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orderItems as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                           value="{{ $item->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="text-green-600 font-medium text-sm">🌱</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->name ?? 'Prodotto eliminato' }}
                                            </div>
                                            @if($item->product && $item->product->code)
                                                <div class="text-sm text-gray-500">Codice: {{ $item->product->code }}</div>
                                            @endif
                                            @if($item->ean)
                                                <div class="text-xs text-gray-400">EAN: {{ $item->ean }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <a href="{{ route('grower.orders.show', $item->order) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $item->order->order_number }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $item->order->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->store->name ?? 'N/A' }}</div>
                                    @if($item->store && $item->store->city)
                                        <div class="text-sm text-gray-500">{{ $item->store->city }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->quantity }}</div>
                                    @if($item->unit_price && $item->unit_price > 0)
                                        <div class="text-xs text-gray-500">€{{ number_format((float)$item->unit_price, 2) }} cad.</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">€{{ number_format((float)$item->total_price, 2) }}</div>
                                    @if($item->prezzo_rivendita && $item->prezzo_rivendita > 0)
                                        <div class="text-xs text-green-600">Rivendita: €{{ number_format((float)$item->prezzo_rivendita, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('grower.order-items.label', $item) }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                            🏷️ Etichetta
                                        </a>
                                        <button onclick="printSingleLabel({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                            🖨️ Stampa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orderItems->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🏷️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna etichetta da stampare</h3>
                <p class="text-gray-600 mb-4">
                    @if(request()->hasAny(['search', 'store_id', 'order_id']))
                        Nessun risultato per i filtri selezionati.
                    @else
                        Non ci sono ancora prodotti ordinati che richiedono etichette.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'store_id', 'order_id']))
                    <a href="{{ route('grower.order-items.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Rimuovi Filtri
                    </a>
                @else
                    <a href="{{ route('grower.products.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        📦 Gestisci Prodotti
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
// Select All Functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Individual checkbox change
document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        const selectAll = document.getElementById('select-all');

        selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
    });
});

// Print Functions
function printSingleLabel(itemId) {
    window.open(`/grower/order-items/${itemId}/label`, '_blank');
}

function printAllLabels() {
    const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkedCheckboxes.length === 0) {
        alert('Seleziona almeno un\'etichetta da stampare.');
        return;
    }

    checkedCheckboxes.forEach(checkbox => {
        const itemId = checkbox.value;
        setTimeout(() => {
            window.open(`/grower/order-items/${itemId}/label`, '_blank');
        }, 200); // Small delay between opens
    });
}
</script>
@endsection
