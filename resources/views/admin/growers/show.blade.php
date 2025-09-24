@extends('layouts.admin')

@section('title', 'Fornitore: ' . $grower->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $grower->name }}</h1>
                <p class="mt-2 text-gray-600">Codice fornitore: <span class="font-semibold">{{ $grower->code }}</span></p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.growers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna alla Lista
                </a>
                <a href="{{ route('admin.growers.edit', $grower) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Modifica
                </a>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grower Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informazioni Fornitore</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $grower->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Codice</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $grower->code }}
                                </span>
                            </dd>
                        </div>
                        @if($grower->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $grower->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $grower->email }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($grower->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefono</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $grower->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $grower->phone }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($grower->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Indirizzo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $grower->address }}</dd>
                        </div>
                        @endif
                        @if($grower->city || $grower->country)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Città/Paese</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($grower->city){{ $grower->city }}@endif
                                @if($grower->city && $grower->country), @endif
                                @if($grower->country){{ $grower->country }}@endif
                            </dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stato</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $grower->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $grower->is_active ? 'Attivo' : 'Inattivo' }}
                                </span>
                            </dd>
                        </div>
                        @if($grower->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Note</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $grower->notes }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Statistics -->
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistiche</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Totale Prodotti</span>
                            <span class="text-sm font-medium text-gray-900">{{ $grower->products->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Valore Medio</span>
                            <span class="text-sm font-medium text-gray-900">
                                €{{ $grower->products->avg('price') ? number_format($grower->products->avg('price'), 2) : '0.00' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Valore Totale</span>
                            <span class="text-sm font-medium text-gray-900">
                                €{{ number_format($grower->products->sum(function($product) { return $product->price * $product->quantity; }), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Quantità Totale</span>
                            <span class="text-sm font-medium text-gray-900">{{ $grower->products->sum('quantity') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Azioni Rapide</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <form method="POST" action="{{ route('admin.growers.toggle-status', $grower) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 {{ $grower->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                <i class="fas fa-{{ $grower->is_active ? 'pause' : 'play' }} mr-2"></i>
                                {{ $grower->is_active ? 'Disattiva' : 'Attiva' }} Fornitore
                            </button>
                        </form>

                        @if($grower->products->count() == 0)
                        <form method="POST" action="{{ route('admin.growers.destroy', $grower) }}" class="w-full" onsubmit="return confirm('Sei sicuro di voler eliminare questo fornitore?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                                <i class="fas fa-trash mr-2"></i>
                                Elimina Fornitore
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Prodotti ({{ $grower->products->count() }})</h3>
            </div>
            <div class="p-6">
                @if($grower->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codici</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantità</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grower->products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                @if($product->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($product->description, 60) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($product->code)
                                                <div><strong>Codice:</strong> {{ $product->code }}</div>
                                            @endif
                                            @if($product->ean)
                                                <div><strong>EAN:</strong> {{ $product->ean }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->store->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            €{{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->category)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    {{ $product->category }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-boxes text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun prodotto</h3>
                        <p class="text-gray-500">Questo fornitore non ha ancora prodotti associati.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
