<!DOCTYPE html>
<html>
<head>
    <title>TEST ORDER</title>
</head>
<body>
    <h1>TEST PAGE - WORKING</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Order Number: {{ $order->order_number }}</p>
    <p>Store: {{ $order->store->name ?? 'N/A' }}</p>
    <p>Products: {{ $order->products->count() }}</p>

    <hr>
    <p style="color: green; font-weight: bold;">If you see this, the problem is NOT in the controller or data loading!</p>
    <p style="color: red;">The problem is specifically in the admin layout or middleware.</p>

    <script>
        console.log('TEST PAGE LOADED SUCCESSFULLY');
        console.log('Order:', @json($order->toArray()));
    </script>
</body>
</html>
