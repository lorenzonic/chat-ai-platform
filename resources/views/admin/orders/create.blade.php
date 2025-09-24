@extends('layouts.admin')

@section('title', 'Crea Nuovo Ordine')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">➕ Crea Nuovo Ordine</h1>
                <p class="mt-2 text-gray-600">Crea un nuovo ordine manualmente</p>
            </div>
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                ← Torna agli Ordini
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <!-- Store Selection -->
            <div>
                <label for="store_id" class="block text-sm font-medium text-gray-700 mb-2">
                    🏪 Store *
                </label>
                <select name="store_id" id="store_id" required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('store_id') border-red-300 @enderror">
                    <option value="">Seleziona un store...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }} ({{ $store->email }})
                        </option>
                    @endforeach
                </select>
                @error('store_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Client Information Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="client" class="block text-sm font-medium text-gray-700 mb-2">
                        👤 Cliente
                    </label>
                    <input type="text" name="client" id="client" value="{{ old('client') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('client') border-red-300 @enderror"
                           placeholder="Nome del cliente">
                    @error('client')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        📞 Telefono
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror"
                           placeholder="Numero di telefono">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Codes Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="cc" class="block text-sm font-medium text-gray-700 mb-2">
                        🏷️ CC
                    </label>
                    <input type="text" name="cc" id="cc" value="{{ old('cc') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('cc') border-red-300 @enderror"
                           placeholder="Codice CC">
                    @error('cc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pia" class="block text-sm font-medium text-gray-700 mb-2">
                        📋 PIA
                    </label>
                    <input type="text" name="pia" id="pia" value="{{ old('pia') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('pia') border-red-300 @enderror"
                           placeholder="Codice PIA">
                    @error('pia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pro" class="block text-sm font-medium text-gray-700 mb-2">
                        📄 PRO
                    </label>
                    <input type="text" name="pro" id="pro" value="{{ old('pro') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('pro') border-red-300 @enderror"
                           placeholder="Codice PRO">
                    @error('pro')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Transport Information Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="transport" class="block text-sm font-medium text-gray-700 mb-2">
                        🚚 Trasporto
                    </label>
                    <input type="text" name="transport" id="transport" value="{{ old('transport') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('transport') border-red-300 @enderror"
                           placeholder="Modalità di trasporto">
                    @error('transport')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transport_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        💰 Costo Trasporto
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">€</span>
                        <input type="number" name="transport_cost" id="transport_cost" value="{{ old('transport_cost') }}"
                               step="0.01" min="0"
                               class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('transport_cost') border-red-300 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('transport_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Delivery Date -->
            <div>
                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Data di Consegna
                </label>
                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('delivery_date') border-red-300 @enderror">
                @error('delivery_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Note
                </label>
                <textarea name="notes" id="notes" rows="4"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror"
                          placeholder="Note aggiuntive per l'ordine...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.orders.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annulla
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    ✅ Crea Ordine
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <div class="w-5 h-5 text-blue-400">ℹ️</div>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Informazioni sulla Creazione Ordine
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Il numero ordine verrà generato automaticamente</li>
                        <li>Solo il campo "Store" è obbligatorio</li>
                        <li>Dopo la creazione potrai aggiungere prodotti tramite import o manualmente</li>
                        <li>Tutti i campi possono essere modificati successivamente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
