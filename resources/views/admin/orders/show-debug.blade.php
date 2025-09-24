<!DOCTYPE html>
<html>
<head>
    <title>Debug Order - {{ $order->id ?? 'N/A' }}</title>
</head>
<body>
    <h1>DEBUG PAGE - Order {{ $order->id ?? 'N/A' }}</h1>

    <div>
        <h2>Order Data</h2>
        <p>ID: {{ $order->id ?? 'N/A' }}</p>
        <p>Order Number: {{ $order->order_number ?? 'N/A' }}</p>
        <p>Store: {{ $order->store->name ?? 'N/A' }}</p>
        <p>Status: {{ $order->status ?? 'N/A' }}</p>
        <p>Products Count: {{ $order->products->count() ?? 0 }}</p>
    </div>

    <div>
        <h2>Raw Order Object</h2>
        <pre>{{ print_r($order->toArray(), true) }}</pre>
    </div>

    <script>
        console.log('Debug page loaded successfully');
        console.log('Order ID from Blade: {{ $order->id ?? "undefined" }}');
    </script>
</body>
</html>
