<!DOCTYPE html>
<html>
<head>
    <title>Test Ordine</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>TEST - Ordine {{ $order->order_number }}</h1>

        <h2>Informazioni Ordine</h2>
        <p><strong>ID:</strong> {{ $order->id }}</p>
        <p><strong>Numero:</strong> {{ $order->order_number }}</p>
        <p><strong>Store ID:</strong> {{ $order->store_id }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>

        <h2>Store</h2>
        @if($order->store)
            <p><strong>Nome:</strong> {{ $order->store->name }}</p>
            <p><strong>Codice:</strong> {{ $order->store->client_code ?? 'N/A' }}</p>
        @else
            <p>Store non trovato</p>
        @endif

        <h2>Prodotti</h2>
        @if($order->products && count($order->products) > 0)
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Quantità</th>
                    <th>Prezzo</th>
                </tr>
                @foreach($order->products as $product)
                    <tr>
                        <td>{{ $product->name ?? 'N/A' }}</td>
                        <td>{{ $product->quantity ?? 'N/A' }}</td>
                        <td>{{ $product->price ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>Nessun prodotto trovato</p>
        @endif
    </div>
</body>
</html>
