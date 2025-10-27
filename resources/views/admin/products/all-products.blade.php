@extends('layouts.admin')

@section('title', 'All Products - Gestione Completa Prodotti')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🌿 All Products</h1>
                <p class="mt-2 text-gray-600">Gestione completa di tutti i prodotti nel sistema</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.all-products.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    <span class="mr-2">➕</span> Nuovo Prodotto
                </a>
                <a href="{{ route('admin.import.products') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    <span class="mr-2">📂</span> Import CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        🌿
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Totale Prodotti</h3>
                    <p class="text-2xl font-bold text-emerald-600">{{ $products->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        �
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Produttori Attivi</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $growers->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        🌱
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Con Produttore</h3>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $products->whereNotNull('grower_id')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Stock Totale</h3>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $products->sum('quantity') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.all-products.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cerca Prodotto</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nome, codice, EAN..."
                           class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 w-64">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produttore</label>
                    <select name="grower" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tutti i produttori</option>
                        @foreach($growers as $grower)
                            <option value="{{ $grower->id }}" {{ request('grower') == $grower->id ? 'selected' : '' }}>
                                {{ $grower->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stato Stock</label>
                    <select name="stock_status" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tutti</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Stock Basso</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Esaurito</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium">
                        🔍 Filtra
                    </button>
                    @if(request()->hasAny(['search', 'grower', 'stock_status']))
                        <a href="{{ route('admin.all-products.index') }}"
                           class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium">
                            🗑️ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Elenco Prodotti</h2>
                <div class="text-sm text-gray-500">
                    Mostrando {{ $products->firstItem() }}-{{ $products->lastItem() }} di {{ $products->total() }} prodotti
                </div>
            </div>

            @if($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produttore</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codici</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                                    <span class="text-emerald-600 font-medium">🌿</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    @if($product->height)
                                                        Altezza: {{ $product->height }}cm
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($product->grower)
                                            <div class="text-sm text-gray-900">{{ $product->grower->name }}</div>

                                        @else
                                            <span class="text-gray-400">Nessun produttore</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($product->vaso)
                                            <div class="text-sm font-medium text-gray-900">{{ $product->vaso }}</div>
                                            <div class="text-xs text-gray-500">cm</div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->quantity ?? 0 }}</div>
                                        <div class="text-xs">
                                            @if(($product->quantity ?? 0) > 50)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Stock OK
                                                </span>
                                            @elseif(($product->quantity ?? 0) > 10)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Stock Basso
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Esaurito
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($product->price)
                                            <div class="text-sm font-medium text-gray-900">€{{ number_format($product->price, 2) }}</div>
                                        @else
                                            <span class="text-gray-400">Non definito</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-600">
                                            @if($product->code)
                                                <div><strong>Code:</strong> {{ $product->code }}</div>
                                            @endif
                                            @if($product->ean)
                                                <div><strong>EAN:</strong> {{ $product->ean }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.all-products.show', $product) }}"
                                               class="text-emerald-600 hover:text-emerald-900">👁️</a>
                                            <a href="{{ route('admin.all-products.edit', $product) }}"
                                               class="text-blue-600 hover:text-blue-900">✏️</a>
                                            <form action="{{ route('admin.all-products.destroy', $product) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Sei sicuro di voler eliminare questo prodotto?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-4xl mb-4">🌿</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Nessun prodotto trovato</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request()->hasAny(['search', 'store', 'stock_status']))
                            Nessun prodotto corrisponde ai filtri applicati.
                        @else
                            Non ci sono ancora prodotti nel sistema.
                        @endif
                    </p>
                    <div class="space-x-3">
                        <a href="{{ route('admin.all-products.create') }}"
                           class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                            <span class="mr-2">➕</span> Crea Primo Prodotto
                        </a>
                        @if(request()->hasAny(['search', 'store', 'stock_status']))
                            <a href="{{ route('admin.all-products.index') }}"
                               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                                🗑️ Rimuovi Filtri
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">🚀 Azioni Rapide</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.import.products') }}"
                   class="block p-4 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">📂</span>
                        <div>
                            <h3 class="font-medium">Import Prodotti</h3>
                            <p class="text-sm text-gray-600">Carica prodotti da file CSV/Excel</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="block p-4 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">📦</span>
                        <div>
                            <h3 class="font-medium">Gestisci Ordini</h3>
                            <p class="text-sm text-gray-600">Visualizza ordini correlati</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="block p-4 border border-orange-200 rounded-lg hover:bg-orange-50 transition-colors">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">🏷️</span>
                        <div>
                            <h3 class="font-medium">Etichette Prodotti</h3>
                            <p class="text-sm text-gray-600">Stampa etichette con QR code</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
