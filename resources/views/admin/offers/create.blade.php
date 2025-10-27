@extends('layouts.admin')

@section('title', 'Nuova Offerta')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nuova Offerta</h1>
        <a href="{{ route('admin.offers.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            ← Torna alle Offerte
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.offers.store') }}" class="bg-white shadow-sm rounded-lg p-6">
        @csrf

        <!-- Basic Information -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Generali</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="title">
                        Titolo Offerta *
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('title') }}"
                           required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                        Descrizione
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Descrizione dettagliata dell'offerta...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="grower_id">
                        Produttore (Opzionale)
                    </label>
                    <select id="grower_id"
                            name="grower_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Tutti i produttori</option>
                        @foreach($growers as $grower)
                            <option value="{{ $grower->id }}" {{ old('grower_id') == $grower->id ? 'selected' : '' }}>
                                {{ $grower->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                        Codice Promozionale
                    </label>
                    <input type="text"
                           id="code"
                           name="code"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('code') }}"
                           placeholder="Es: ESTATE2025">
                </div>
            </div>
        </div>

        <!-- Offer Type and Value -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tipo di Offerta</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo di Sconto *</label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="type" value="percentage" class="mr-3" {{ old('type') == 'percentage' ? 'checked' : '' }} required>
                            <span class="text-sm">Percentuale di sconto</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="fixed_amount" class="mr-3" {{ old('type') == 'fixed_amount' ? 'checked' : '' }} required>
                            <span class="text-sm">Importo fisso di sconto</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="buy_x_get_y" class="mr-3" {{ old('type') == 'buy_x_get_y' ? 'checked' : '' }} required>
                            <span class="text-sm">Compra X, prendi Y gratis</span>
                        </label>
                    </div>
                </div>

                <!-- Percentage/Fixed Amount Fields -->
                <div id="discount-value-field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="discount_value">
                        Valore Sconto
                    </label>
                    <div class="flex">
                        <input type="number"
                               id="discount_value"
                               name="discount_value"
                               step="0.01"
                               min="0"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               value="{{ old('discount_value') }}">
                        <span id="discount-unit" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 rounded-r-md text-sm text-gray-500">
                            %
                        </span>
                    </div>
                </div>

                <!-- Buy X Get Y Fields -->
                <div id="buy-x-get-y-fields" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="buy_quantity">
                            Quantità da Comprare
                        </label>
                        <input type="number"
                               id="buy_quantity"
                               name="buy_quantity"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               value="{{ old('buy_quantity') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="get_quantity">
                            Quantità Gratuita
                        </label>
                        <input type="number"
                               id="get_quantity"
                               name="get_quantity"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               value="{{ old('get_quantity') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Conditions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Condizioni</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="minimum_amount">
                        Importo Minimo (€)
                    </label>
                    <input type="number"
                           id="minimum_amount"
                           name="minimum_amount"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('minimum_amount') }}"
                           placeholder="Es: 50.00">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="usage_limit">
                        Limite Utilizzi
                    </label>
                    <input type="number"
                           id="usage_limit"
                           name="usage_limit"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('usage_limit') }}"
                           placeholder="Lascia vuoto per illimitato">
                </div>
            </div>
        </div>

        <!-- Period -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Periodo di Validità</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="start_date">
                        Data Inizio *
                    </label>
                    <input type="datetime-local"
                           id="start_date"
                           name="start_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('start_date') }}"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="end_date">
                        Data Fine *
                    </label>
                    <input type="datetime-local"
                           id="end_date"
                           name="end_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           value="{{ old('end_date') }}"
                           required>
                </div>
            </div>
        </div>

        <!-- Product/Category Restrictions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Restrizioni (Opzionale)</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prodotti Specifici</label>
                    <select name="applicable_products[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" size="5">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ in_array($product->id, old('applicable_products', [])) ? 'selected' : '' }}>
                                {{ $product->name }} - {{ $product->grower->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tieni premuto Ctrl/Cmd per selezionare più prodotti. Lascia vuoto per applicare a tutti i prodotti.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categorie Specifiche</label>
                    <select name="applicable_categories[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" size="4">
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ in_array($category, old('applicable_categories', [])) ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tieni premuto Ctrl/Cmd per selezionare più categorie. Lascia vuoto per applicare a tutte le categorie.</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-4">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-md font-medium">
                🎯 Crea Offerta
            </button>
            <a href="{{ route('admin.offers.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-md font-medium">
                Annulla
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const discountValueField = document.getElementById('discount-value-field');
    const buyXGetYFields = document.getElementById('buy-x-get-y-fields');
    const discountUnit = document.getElementById('discount-unit');

    function toggleFields() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;

        // Hide all fields first
        discountValueField.classList.add('hidden');
        buyXGetYFields.classList.add('hidden');

        if (selectedType === 'percentage') {
            discountValueField.classList.remove('hidden');
            discountUnit.textContent = '%';
        } else if (selectedType === 'fixed_amount') {
            discountValueField.classList.remove('hidden');
            discountUnit.textContent = '€';
        } else if (selectedType === 'buy_x_get_y') {
            buyXGetYFields.classList.remove('hidden');
        }
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });

    // Initialize on page load
    toggleFields();
});
</script>
@endsection
