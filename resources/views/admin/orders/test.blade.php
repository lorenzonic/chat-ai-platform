<!DOCTYPE html>
<html>
<head>
    <title>Test Ordine</title>
</head>
<body>
    <h1>Test Ordine {{ $order->order_number }}</h1>
    <p>Store: {{ $order->store ? $order->store->name : 'N/A' }}</p>
    <p>Prodotti: {{ $order->products->count() }}</p>
</body>
</html>
