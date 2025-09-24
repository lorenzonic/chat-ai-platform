@extends('layouts.admin')

@section('title', 'Modifica Ordine ' . $order->order_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6 text-gray-900">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Modifica Ordine {{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-1">Aggiorna i dettagli dell'ordine</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna all'Ordine
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
            @csrf
            @method('PUT')

            <!-- Order Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                    <select id="status" name="status" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>In Attesa</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confermato</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>In Elaborazione</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Spedito</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Consegnato</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annullato</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Date -->
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Data Consegna</label>
                    <input type="date" id="delivery_date" name="delivery_date" value="{{ $order->delivery_date }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Transport Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Transport -->
                <div>
                    <label for="transport" class="block text-sm font-medium text-gray-700 mb-2">Trasporto</label>
                    <input type="text" id="transport" name="transport" value="{{ old('transport', $order->transport) }}" placeholder="Es. Corriere, Ritiro in sede..." class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('transport')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefono</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $order->phone) }}" placeholder="Numero di telefono" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="mb-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Indirizzo</label>
                <textarea id="address" name="address" rows="3" placeholder="Indirizzo di consegna..." class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $order->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Note aggiuntive sull'ordine..." class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $order->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                    Annulla
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Salva Modifiche
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Order Info (Read-only) -->
<div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg mt-6">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Ordine (Solo lettura)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID Ordine</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $order->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $order->store->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Prodotti</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $order->products->count() }} prodotti</dd>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<script>
    console.log('Order edit page loaded');
    console.log('Order ID: {{ $order->id }}');
</script>
@endsection
