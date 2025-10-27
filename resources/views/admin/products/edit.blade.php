@extends('layouts.admin')

@section('title', 'Modifica Dati Prodotto')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifica Dati Prodotto</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.show', $orderItem) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                 Annulla
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

    <form method="POST" action="{{ route('admin.products.update', $orderItem) }}" class="bg-white shadow-sm rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="product_name">
                    Nome Prodotto *
                </label>
                <input type="text"
                       id="product_name"
                       name="product_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('product_name', $orderItem->product_snapshot['name'] ?? ($orderItem->product->name ?? '')) }}"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="ean">
                    Codice EAN
                </label>
                <input type="text"
                       id="ean"
                       name="ean"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('ean', $orderItem->ean ?? ($orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? ''))) }}"
                       placeholder="Es: 8012345678901">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="prezzo_rivendita">
                    Prezzo Unitario (€) *
                </label>
                <input type="number"
                       id="prezzo_rivendita"
                       name="prezzo_rivendita"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       step="0.01"
                       min="0"
                       value="{{ old('prezzo_rivendita', $orderItem->prezzo_rivendita) }}"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="vaso">
                    Vaso (cm)
                </label>
                <input type="number"
                       id="vaso"
                       name="vaso"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       min="0"
                       value="{{ old('vaso', $orderItem->product_snapshot['vaso'] ?? ($orderItem->product->vaso ?? '')) }}"
                       placeholder="Es: 24">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="link">
                    Link Prodotto
                </label>
                <input type="url"
                       id="link"
                       name="link"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('link', $orderItem->product_snapshot['link'] ?? '') }}"
                       placeholder="https://...">
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2" for="notes">
                Note Aggiuntive
            </label>
            <textarea id="notes"
                      name="notes"
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Note interne, istruzioni speciali...">{{ old('notes', $orderItem->notes) }}</textarea>
        </div>

        <div class="flex justify-center gap-4 mt-8">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium">
                 Salva Modifiche
            </button>
            <a href="{{ route('admin.products.show', $orderItem) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-md font-medium">
                Annulla
            </a>
        </div>
    </form>
</div>
@endsection
