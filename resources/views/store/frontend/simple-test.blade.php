<!DOCTYPE html>
<html>
<head>
    <title>Test Page - {{ $store->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { background: #f0f0f0; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="test">
        <h1>🎉 Laravel Works!</h1>
        <p><strong>Store:</strong> {{ $store->name }}</p>
        <p><strong>Slug:</strong> {{ $store->slug }}</p>
        <p><strong>Description:</strong> {{ $store->description ?? 'No description' }}</p>
        <p><strong>Status:</strong> {{ $store->is_active ? 'Active' : 'Inactive' }}</p>
    </div>
</body>
</html>
