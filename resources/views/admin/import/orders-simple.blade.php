@extends('layouts.admin')

@section('title', 'Import Orders from CSV')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🛒 Import Orders from CSV</h1>
            <p class="mt-2 text-gray-600">Upload your CSV file to import orders</p>
        </div>

        <!-- Stats -->
        @if(!empty($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-indigo-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Orders</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_orders'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Products</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_products'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-store text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Stores</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_stores'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-seedling text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Growers</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_growers'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Upload Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📁 Upload CSV File</h2>
                
                <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700">Select CSV File</label>
                        <input type="file" 
                               name="csv_file" 
                               id="csv_file" 
                               accept=".csv,.txt"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-upload mr-2"></i>
                        Upload and Process
                    </button>
                </form>

                <div id="uploadProgress" class="mt-4 hidden">
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                        <p>Processing your file...</p>
                    </div>
                </div>

                <div id="uploadResult" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const fileInput = document.getElementById('csv_file');
    
    if (!fileInput.files[0]) {
        alert('Please select a file');
        return;
    }
    
    formData.append('csv_file', fileInput.files[0]);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    // Show progress
    document.getElementById('uploadProgress').classList.remove('hidden');
    document.getElementById('uploadResult').innerHTML = '';
    
    fetch('/admin/import/orders/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('uploadProgress').classList.add('hidden');
        
        if (data.success) {
            document.getElementById('uploadResult').innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <h4 class="font-bold">✅ Success!</h4>
                    <pre class="mt-2 text-sm whitespace-pre-wrap">${data.message || 'Import completed successfully'}</pre>
                </div>
            `;
        } else {
            document.getElementById('uploadResult').innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <h4 class="font-bold">❌ Error!</h4>
                    <p class="mt-2">${data.error || 'Upload failed'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('uploadProgress').classList.add('hidden');
        document.getElementById('uploadResult').innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <h4 class="font-bold">❌ Network Error!</h4>
                <p class="mt-2">${error.message}</p>
            </div>
        `;
    });
});
</script>
@endsection