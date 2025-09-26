@extends('layouts.admin')

@section('title', 'Gestione Order Items')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Gestione Order Items</h1>
                    <p class="mt-2 text-gray-600">Visualizza e gestisci tutti gli articoli degli ordini importati</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.orders.index') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        📦 Gestione Ordini
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                {{ session('info') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            📋
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ number_format($stats['total_items']) }}</h3>
                        <p class="text-sm text-gray-500">Order Items</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            📦
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ number_format($stats['total_orders']) }}</h3>
                        <p class="text-sm text-gray-500">Ordini con Items</p>
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
                        <h3 class="text-lg font-medium text-gray-900">{{ number_format($stats['total_stores']) }}</h3>
                        <p class="text-sm text-gray-500">Store Coinvolti</p>
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
                        <h3 class="text-lg font-medium text-gray-900">{{ number_format($stats['total_growers']) }}</h3>
                        <p class="text-sm text-gray-500">Grower Attivi</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            📊
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ number_format($stats['total_quantity']) }}</h3>
                        <p class="text-sm text-gray-500">Quantità Totale</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            💰
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">€{{ number_format($stats['total_value'], 2) }}</h3>
                        <p class="text-sm text-gray-500">Valore Totale</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">🔍 Filtri</h2>
                <form method="GET" action="{{ route('admin.order-items.index') }}" class="space-y-4">
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

                        <!-- Grower Filter -->
                        <div>
                            <label for="grower_id" class="block text-sm font-medium text-gray-700 mb-1">Grower</label>
                            <select name="grower_id" id="grower_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tutti i Grower</option>
                                @foreach($growers as $grower)
                                    <option value="{{ $grower->id }}" {{ request('grower_id') == $grower->id ? 'selected' : '' }}>
                                        {{ $grower->name }}
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

                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cerca Prodotto</label>
                            <input type="text" name="search" id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nome prodotto..."
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date From -->
                        <div>
                            <label for="order_date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Ordine Da</label>
                            <input type="date" name="order_date_from" id="order_date_from"
                                   value="{{ request('order_date_from') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="order_date_to" class="block text-sm font-medium text-gray-700 mb-1">Data Ordine A</label>
                            <input type="date" name="order_date_to" id="order_date_to"
                                   value="{{ request('order_date_to') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            🔍 Filtra
                        </button>
                        <a href="{{ route('admin.order-items.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            🗑️ Reset
                        </a>
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
                            <button onclick="toggleBulkActions()"
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                ⚙️ Azioni Bulk
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Bulk Actions Form (initially hidden) -->
                <div id="bulk-actions" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                    <form method="POST" action="{{ route('admin.order-items.bulk-action') }}" onsubmit="return confirmBulkAction()">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <select name="action" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleziona azione...</option>
                                <option value="delete">🗑️ Elimina selezionati</option>
                                <option value="export">📥 Esporta selezionati</option>
                            </select>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                Esegui
                            </button>
                            <button type="button" onclick="selectAll()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Seleziona Tutto
                            </button>
                            <button type="button" onclick="selectNone()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                Deseleziona Tutto
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-header" onchange="toggleSelectAll(this)" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order Item
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prodotto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantità & Prezzo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Store
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grower
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ordine
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
                                    <input type="checkbox" name="selected_items[]" value="{{ $orderItem->id }}"
                                           class="rounded border-gray-300 item-checkbox">
                                </td>

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
                                        Qty: {{ number_format($orderItem->quantity) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        € {{ number_format($orderItem->price, 2) }} cad.
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Tot: € {{ number_format($orderItem->quantity * $orderItem->price, 2) }}
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
                                        {{ $orderItem->grower->name ?? 'N/A' }}
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

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.order-items.show', $orderItem) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors px-3 py-1 rounded-lg border border-blue-200 hover:bg-blue-50">
                                            👁️ Dettagli
                                        </a>
                                        <a href="{{ route('admin.order-items.edit', $orderItem) }}"
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors px-3 py-1 rounded-lg border border-yellow-200 hover:bg-yellow-50">
                                            ✏️ Modifica
                                        </a>
                                        <form method="POST" action="{{ route('admin.order-items.destroy', $orderItem) }}"
                                              class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo order item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors px-3 py-1 rounded-lg border border-red-200 hover:bg-red-50">
                                                🗑️ Elimina
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <div class="text-6xl mb-4">📦</div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun Order Item Trovato</h3>
                                        <p class="text-gray-500">Non ci sono order items che corrispondono ai filtri selezionati.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('admin.order-items.index') }}"
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
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form on select changes for better UX
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#store_id, #grower_id, #order_id');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    bulkActions.classList.toggle('hidden');
}

function toggleSelectAll(checkbox) {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => cb.checked = checkbox.checked);
}

function selectAll() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => cb.checked = true);
    document.getElementById('select-all-header').checked = true;
}

function selectNone() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => cb.checked = false);
    document.getElementById('select-all-header').checked = false;
}

function confirmBulkAction() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked').length;
    if (selectedItems === 0) {
        alert('Seleziona almeno un order item per continuare.');
        return false;
    }

    const action = document.querySelector('select[name="action"]').value;
    if (!action) {
        alert('Seleziona un\'azione per continuare.');
        return false;
    }

    if (action === 'delete') {
        return confirm(`Sei sicuro di voler eliminare ${selectedItems} order items selezionati?`);
    }

    return true;
}
</script>
@endpush
