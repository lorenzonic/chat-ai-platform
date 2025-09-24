@extends('layouts.admin')

@section('title', 'Import Orders')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Import Orders from CSV</h1>

    <div class="mb-4">
        <p>Current stats:</p>
        <ul>
            <li>Orders: {{ $stats['total_orders'] ?? 'N/A' }}</li>
            <li>Products: {{ $stats['total_products'] ?? 'N/A' }}</li>
            <li>Stores: {{ $stats['total_stores'] ?? 'N/A' }}</li>
            <li>Growers: {{ $stats['total_growers'] ?? 'N/A' }}</li>
        </ul>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <form id="uploadForm" enctype="multipart/form-data" action="/admin/import/orders/upload" method="POST">
            @csrf
            <div class="mb-4">
                <label for="csv_file" class="block mb-2">Select CSV File:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" class="border p-2 w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload CSV</button>
        </form>

        <div id="result" class="mt-4"></div>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const resultDiv = document.getElementById('result');

    resultDiv.innerHTML = '<p>Uploading...</p>';

    fetch('/admin/import/orders/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div style="color: green;">Success: ' + (data.message || 'Upload completed') + '<br>Redirecting to orders page...</div>';
            // Redirect dopo successo usando l'URL dal server
            setTimeout(() => {
                window.location.href = data.redirect_url || '/admin/orders';
            }, 2000);
        } else {
            resultDiv.innerHTML = '<div style="color: red;">Error: ' + (data.error || 'Upload failed') + '</div>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div style="color: red;">Network Error: ' + error.message + '</div>';
    });
});
</script>
@endsection
