@extends('layouts.admin')

@section('title', 'Dettagli Coltivatore - ' . $grower->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🌱 {{ $grower->name }}</h1>
                <p class="mt-2 text-gray-600">Dettagli coltivatore e gestione prodotti</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.growers.edit', $grower) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    ✏️ Modifica
                </a>
                <a href="{{ route('admin.growers.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    ← Torna alla Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Prodotti</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $grower->products->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🛒
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Ordini</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $grower->orderItems->groupBy('order_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        💰
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Fatturato</h3>
                    <p class="text-2xl font-bold text-purple-600">€{{ number_format($grower->orderItems->sum('total_price'), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        🏪
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Negozi Serviti</h3>
                    <p class="text-2xl font-bold text-orange-600">{{ $grower->orderItems->pluck('store_id')->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Grower Information -->
        <div class="lg:col-span-1">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Informazioni Coltivatore</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nome</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $grower->name }}</p>
                    </div>

                    @if($grower->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $grower->email }}</p>
                        </div>
                    @endif

                    @if($grower->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telefono</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $grower->phone }}</p>
                        </div>
                    @endif

                    @if($grower->address)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Indirizzo</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $grower->address }}</p>
                        </div>
                    @endif

                    @if($grower->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Descrizione</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $grower->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Registrato il</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $grower->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ultimo aggiornamento</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $grower->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Login Information -->
            @if($grower->email)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-900 mb-2">🔑 Informazioni Accesso</h3>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><strong>Email:</strong> {{ $grower->email }}</p>
                        <p><strong>URL Accesso:</strong> <code>{{ url('/grower/login') }}</code></p>
                        <p><strong>Stato:</strong>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Attivo
                            </span>
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">🔒 Accesso Non Configurato</h3>
                    <p class="text-sm text-gray-600">
                        Nessuna email configurata per l'accesso.
                        <a href="{{ route('admin.growers.edit', $grower) }}" class="text-blue-600 hover:text-blue-500">
                            Clicca qui per configurare l'accesso.
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Right Column: Products and Orders -->
        <div class="lg:col-span-2">
            <!-- Products List -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Prodotti ({{ $grower->products->count() }})</h2>
                        <a href="{{ route('admin.products.create') }}?grower_id={{ $grower->id }}"
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                            ➕ Nuovo Prodotto
                        </a>
                    </div>
                </div>

                @if($grower->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prodotto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Codice
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prezzo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Azioni
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grower->products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            @if($product->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $product->code ?: 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">€{{ number_format($product->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.products.show', $product) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-3">Visualizza</a>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="text-yellow-600 hover:text-yellow-900">Modifica</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📦</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun prodotto</h3>
                        <p class="text-gray-600 mb-4">Questo coltivatore non ha ancora prodotti associati.</p>
                        <a href="{{ route('admin.products.create') }}?grower_id={{ $grower->id }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            ➕ Aggiungi Primo Prodotto
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Orders -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Ordini Recenti</h2>
                </div>

                @if($grower->orderItems->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ordine
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Negozio
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prodotto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantità
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Totale
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grower->orderItems->sortByDesc('created_at')->take(10) as $orderItem)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.orders.show', $orderItem->order) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ $orderItem->order->order_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $orderItem->order->store->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($orderItem->product)
                                                    {{ $orderItem->product->name }}
                                                @else
                                                    <span class="text-gray-500">Prodotto eliminato</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $orderItem->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">€{{ number_format($orderItem->total_price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $orderItem->created_at->format('d/m/Y') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($grower->orderItems->count() > 10)
                        <div class="px-6 py-4 border-t border-gray-200 text-center">
                            <a href="{{ route('admin.orders.index') }}?grower_id={{ $grower->id }}"
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                Visualizza tutti gli ordini ({{ $grower->orderItems->count() }} totali)
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">🛒</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun ordine</h3>
                        <p class="text-gray-600">Questo coltivatore non ha ancora ricevuto ordini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
