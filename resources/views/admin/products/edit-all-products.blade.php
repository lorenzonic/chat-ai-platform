@extends('layouts.admin')

@section('title', 'Modifica Prodotto - ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Navigation -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifica Prodotto</h1>
            <p class="text-gray-600">{{ $product->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.all-products.show', $product) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                👁️ Visualizza
            </a>
            <a href="{{ route('admin.all-products.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                ← Torna all'Elenco
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.all-products.update', $product) }}" class="bg-white shadow-sm rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                    Nome Prodotto *
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       value="{{ old('name', $product->name) }}"
                       required>
            </div>

            <!-- Grower -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="grower_id">
                    Produttore *
                </label>
                <select id="grower_id"
                        name="grower_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                    <option value="">Seleziona produttore</option>
                    @foreach($growers as $grower)
                        <option value="{{ $grower->id }}" {{ (old('grower_id', $product->grower_id) == $grower->id) ? 'selected' : '' }}>
                            {{ $grower->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                    Codice Prodotto
                </label>
                <input type="text"
                       id="code"
                       name="code"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       value="{{ old('code', $product->code) }}"
                       placeholder="Es: G123456">
            </div>

            <!-- EAN Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="ean">
                    Codice EAN
                </label>
                <input type="text"
                       id="ean"
                       name="ean"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       value="{{ old('ean', $product->ean) }}"
                       placeholder="Es: 8012345678901">
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="quantity">
                    Quantità Stock *
                </label>
                <input type="number"
                       id="quantity"
                       name="quantity"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       min="0"
                       value="{{ old('quantity', $product->quantity) }}"
                       required>
            </div>

            <!-- Height -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="height">
                    Altezza (cm)
                </label>
                <input type="number"
                       id="height"
                       name="height"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       step="0.01"
                       min="0"
                       value="{{ old('height', $product->height) }}"
                       placeholder="Es: 70.5">
            </div>

            <!-- Vaso -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="vaso">
                    Vaso (cm)
                </label>
                <input type="number"
                       id="vaso"
                       name="vaso"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       min="0"
                       value="{{ old('vaso', $product->vaso) }}"
                       placeholder="Es: 24">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="price">
                    Prezzo (€)
                </label>
                <input type="number"
                       id="price"
                       name="price"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       step="0.01"
                       min="0"
                       value="{{ old('price', $product->price) }}"
                       placeholder="Es: 12.50">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="category">
                    Categoria
                </label>
                <input type="text"
                       id="category"
                       name="category"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       value="{{ old('category', $product->category) }}"
                       placeholder="Es: Piante grasse">
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                Descrizione
            </label>
            <textarea id="description"
                      name="description"
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                      placeholder="Descrizione dettagliata del prodotto...">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-4 mt-8">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-md font-medium">
                💾 Salva Modifiche
            </button>
            <a href="{{ route('admin.all-products.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-md font-medium">
                Annulla
            </a>
        </div>
    </form>

    <!-- Current Product Info -->
    <div class="mt-8 bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Prodotto</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div>
                <strong>ID Prodotto:</strong> {{ $product->id }}
            </div>
            <div>
                <strong>Creato:</strong> {{ $product->created_at->format('d/m/Y H:i') }}
            </div>
            <div>
                <strong>Modificato:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>
@endsection
