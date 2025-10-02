@extends('layouts.admin')

@section('title', 'Modifica Order Item')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">✏️ Modifica Order Item #{{ $orderItem->id }}</h1>
                    <p class="mt-2 text-gray-600">Modifica i dettagli dell'articolo dell'ordine</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.order-items.show', $orderItem) }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        👁️ Visualizza
                    </a>
                    <a href="{{ route('admin.order-items.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        ← Torna alla Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow">
            <form method="POST" action="{{ route('admin.order-items.update', $orderItem) }}">
                @csrf
                @method('PUT')

                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">📋 Dati Order Item</h2>
                </div>

                <div class="p-6 space-y-6">

                    <!-- Basic Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                Quantità <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   value="{{ old('quantity', $orderItem->quantity) }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Prezzo Unitario (€) <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="price"
                                   name="price"
                                   value="{{ old('price', $orderItem->price) }}"
                                   min="0"
                                   step="0.01"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Total Price Display -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Totale Calcolato:</span>
                            <span id="calculated-total" class="text-lg font-bold text-green-600">
                                € {{ number_format($orderItem->quantity * $orderItem->price, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Relationships (Read-only info) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-gray-200">

                        <!-- Store -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Store</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $orderItem->store->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600">ID: {{ $orderItem->store_id }}</p>
                        </div>

                        <!-- Grower -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Grower</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $orderItem->grower->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600">ID: {{ $orderItem->grower_id }}</p>
                        </div>

                        <!-- Order -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Ordine</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $orderItem->order->order_number ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600">ID: {{ $orderItem->order_id }}</p>
                        </div>

                    </div>

                    <!-- Product Info (Editable) -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">🏷️ Informazioni Prodotto</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Product Name (Editable) -->
                            <div>
                                <label for="product_name" class="block text-sm font-medium text-gray-700">
                                    Nome Prodotto
                                </label>
                                <input type="text"
                                       id="product_name"
                                       name="product_name"
                                       value="{{ old('product_name', $orderItem->product_snapshot['name'] ?? $orderItem->product->name ?? '') }}"
                                       maxlength="255"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('product_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- EAN Code (Editable) -->
                            <div>
                                <label for="product_ean" class="block text-sm font-medium text-gray-700">
                                    Codice EAN
                                </label>
                                <input type="text"
                                       id="product_ean"
                                       name="product_ean"
                                       value="{{ old('product_ean', $orderItem->product_snapshot['ean'] ?? $orderItem->product->ean ?? '') }}"
                                       maxlength="20"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('product_ean')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU (Read-only) -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">SKU</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $orderItem->product_snapshot['sku'] ?? $orderItem->product->sku ?? 'N/A' }}
                                </p>
                            </div>

                            <!-- Product ID (Read-only) -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">Product ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $orderItem->product_id ?? 'N/A' }}</p>
                            </div>

                        </div>
                    </div>

                    <!-- Notes section -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Attenzione</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>La modifica di quantità e prezzo influenzerà i calcoli dell'ordine. I dati del prodotto (snapshot) non possono essere modificati per preservare l'integrità storica.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.order-items.show', $orderItem) }}"
                           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Annulla
                        </a>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            💾 Salva Modifiche
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('price');
    const totalElement = document.getElementById('calculated-total');

    function updateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        totalElement.textContent = '€ ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    quantityInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);
});
</script>
@endsection
