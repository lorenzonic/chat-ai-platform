@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Debug Ordine</h1>

    <div class="bg-white p-4 rounded shadow">
        <p><strong>ID:</strong> {{ $order->id ?? 'N/A' }}</p>
        <p><strong>Numero:</strong> {{ $order->order_number ?? 'N/A' }}</p>
        <p><strong>Store ID:</strong> {{ $order->store_id ?? 'N/A' }}</p>
        <p><strong>Status:</strong> {{ $order->status ?? 'N/A' }}</p>

        <hr class="my-4">

        <h3 class="text-lg font-semibold mb-2">Store:</h3>
        @if($order->store ?? false)
            <p>Nome: {{ $order->store->name ?? 'N/A' }}</p>
            <p>Codice: {{ $order->store->client_code ?? 'N/A' }}</p>
        @else
            <p>Store non trovato</p>
        @endif

        <hr class="my-4">

        <h3 class="text-lg font-semibold mb-2">Prodotti:</h3>
        @if($order->products ?? false)
            <p>Numero prodotti: {{ $order->products->count() }}</p>
            @foreach($order->products as $product)
                <div class="border p-2 mb-2">
                    <p>Nome: {{ $product->name ?? 'N/A' }}</p>
                    <p>Quantità: {{ $product->quantity ?? 'N/A' }}</p>
                </div>
            @endforeach
        @else
            <p>Nessun prodotto</p>
        @endif
    </div>
</div>
@endsection
