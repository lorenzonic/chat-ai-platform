@extends('layouts.grower')

@section('title', 'My Products - Grower Portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">I Miei Prodotti</h1>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-500">
                            {{ $grower->company_name }}
                        </div>
                        <a href="{{ route('grower.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Aggiungi Nuovo Prodotto
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800">Riepilogo Prodotti</h3>
                    <p class="text-blue-600">Totale prodotti nel mio catalogo: {{ $products->total() }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dettagli Prodotto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Codice EAN
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prezzo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantità
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Creato
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($product->description, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->ean)
                                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $product->ean }}</span>
                                    @else
                                        <span class="text-gray-400 text-sm">No EAN</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($product->price)
                                        €{{ number_format($product->price, 2) }}
                                    @else
                                        <span class="text-gray-400">No price</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($product->quantity !== null)
                                        {{ $product->quantity }}
                                        @if($product->quantity == 0)
                                            <span class="text-red-500 text-xs">(Out of stock)</span>
                                        @elseif($product->quantity < 10)
                                            <span class="text-yellow-500 text-xs">(Low stock)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400">{{ $product->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('grower.products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                        <a href="{{ route('grower.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('grower.products.destroy', $product) }}" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4.5m8-4.5v10l-8 4.5m0-9v9m0-9L4 7m8 4.5v9m8-4.5l-8 4.5"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-2">nessun prodotto trovato</p>
                                        <p class="text-gray-500 mb-4">Inizia aggiungendo il tuo primo prodotto al catalogo.</p>
                                        <a href="{{ route('grower.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                            Aggiungi il Primo Prodotto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif

                <!-- Quick Stats -->
                @if($products->count() > 0)
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800">Totale Prodotti</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $products->total() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-800">Prodotti con EAN</h4>
                            <p class="text-2xl font-bold text-green-600">{{ $products->where('ean', '!=', null)->count() }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800">Prodotti con Prezzo</h4>
                            <p class="text-2xl font-bold text-purple-600">{{ $products->where('price', '!=', null)->count() }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
