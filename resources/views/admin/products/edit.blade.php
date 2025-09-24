@extends('layouts.admin')

@section('title', 'Modifica Prodotto')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Modifica Prodotto: {{ $product->name }}</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.products.show', $product) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Visualizza
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Lista Prodotti
                        </a>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.products.update', $product) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="store_id" class="block text-sm font-medium text-gray-700">Negozio *</label>
                                <select name="store_id" id="store_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Seleziona negozio</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}"
                                                {{ (old('store_id', $product->store_id) == $store->id) ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="grower_id" class="block text-sm font-medium text-gray-700">Fornitore</label>
                                <select name="grower_id" id="grower_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Seleziona fornitore</option>
                                    @foreach($growers as $grower)
                                        <option value="{{ $grower->id }}"
                                                {{ (old('grower_id', $product->grower_id) == $grower->id) ? 'selected' : '' }}>
                                            {{ $grower->name }} @if($grower->code)({{ $grower->code }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome Prodotto *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">Codice</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="ean" class="block text-sm font-medium text-gray-700">EAN</label>
                                <input type="text" name="ean" id="ean" value="{{ old('ean', $product->ean) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                                <input type="text" name="category" id="category" value="{{ old('category', $product->category) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantità</label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700">Altezza (cm)</label>
                                <input type="number" name="height" id="height" value="{{ old('height', $product->height) }}" step="0.01" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Prezzo Vendita (€)</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="transport_cost" class="block text-sm font-medium text-gray-700">Costo Trasporto (€)</label>
                                <input type="number" name="transport_cost" id="transport_cost" value="{{ old('transport_cost', $product->transport_cost) }}" step="0.01" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700">Data Consegna</label>
                                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', $product->delivery_date) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="client" class="block text-sm font-medium text-gray-700">Cliente</label>
                                <input type="text" name="client" id="client" value="{{ old('client', $product->client) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Fields -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="cc" class="block text-sm font-medium text-gray-700">CC</label>
                            <input type="text" name="cc" id="cc" value="{{ old('cc', $product->cc) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="pia" class="block text-sm font-medium text-gray-700">PIA</label>
                            <input type="text" name="pia" id="pia" value="{{ old('pia', $product->pia) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="pro" class="block text-sm font-medium text-gray-700">PRO</label>
                            <input type="text" name="pro" id="pro" value="{{ old('pro', $product->pro) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Indirizzo</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $product->address) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefono</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $product->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Note</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $product->notes) }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.products.show', $product) }}"
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                            Annulla
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Aggiorna Prodotto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
