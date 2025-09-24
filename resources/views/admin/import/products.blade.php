@extends('layouts.admin')

@section('title', 'Import Products')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📦 Import Products</h1>
                    <p class="mt-2 text-gray-600">Upload Excel file to bulk import products</p>
                </div>
                <a href="{{ route('admin.import.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    ← Back to Import Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <h4 class="font-bold">Import Successful!</h4>
                <pre class="mt-2 text-sm whitespace-pre-wrap">{{ session('success') }}</pre>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <h4 class="font-bold">Import Failed!</h4>
                <p class="mt-2">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.import.products.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Store Selection -->
                    <div class="mb-6">
                        <label for="store_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Store <span class="text-red-500">*</span>
                        </label>
                        <select name="store_id" id="store_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choose a store...</option>
                            @foreach(\App\Models\Store::orderBy('name')->get() as $store)
                                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }} ({{ $store->slug }})
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grower Selection -->
                    <div class="mb-6">
                        <label for="grower_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Grower <span class="text-red-500">*</span>
                        </label>
                        <select name="grower_id" id="grower_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choose a grower...</option>
                            @foreach(\App\Models\Grower::orderBy('name')->get() as $grower)
                                <option value="{{ $grower->id }}" {{ old('grower_id') == $grower->id ? 'selected' : '' }}>
                                    {{ $grower->name }}
                                    @if($grower->company_name)
                                        ({{ $grower->company_name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('grower_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Excel File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="excel_file" id="excel_file" required
                               accept=".xlsx,.xls,.csv"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Supported formats: .xlsx, .xls, .csv (max 10MB)
                        </p>
                        @error('excel_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expected Format -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-blue-800 mb-3">📋 Expected Excel Format</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-blue-100">
                                        <th class="px-3 py-2 text-left text-blue-800">Column</th>
                                        <th class="px-3 py-2 text-left text-blue-800">Field</th>
                                        <th class="px-3 py-2 text-left text-blue-800">Required</th>
                                        <th class="px-3 py-2 text-left text-blue-800">Example</th>
                                    </tr>
                                </thead>
                                <tbody class="text-blue-700">
                                    <tr><td class="px-3 py-1">A</td><td>Product Name</td><td>✅ Yes</td><td>Rosa Rossa Premium</td></tr>
                                    <tr><td class="px-3 py-1">B</td><td>Product Code</td><td>⚪ No</td><td>ROSA001</td></tr>
                                    <tr><td class="px-3 py-1">C</td><td>EAN Code</td><td>⚪ No</td><td>8051277781620</td></tr>
                                    <tr><td class="px-3 py-1">D</td><td>Description</td><td>⚪ No</td><td>Rosa rossa di alta qualità</td></tr>
                                    <tr><td class="px-3 py-1">E</td><td>Quantity</td><td>⚪ No</td><td>50</td></tr>
                                    <tr><td class="px-3 py-1">F</td><td>Height (cm)</td><td>⚪ No</td><td>25.5</td></tr>
                                    <tr><td class="px-3 py-1">G</td><td>Price (€)</td><td>⚪ No</td><td>15.99</td></tr>
                                    <tr><td class="px-3 py-1">H</td><td>Category</td><td>⚪ No</td><td>Fiori</td></tr>
                                    <tr><td class="px-3 py-1">I</td><td>Client</td><td>⚪ No</td><td>Garden Center Roma</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.import.template', 'products') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                            📥 Download Template
                        </a>

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            🚀 Import Products
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Tips -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">💡 Import Tips</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">✅ Best Practices</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Use the provided template for best results</li>
                            <li>• Ensure product names are unique and descriptive</li>
                            <li>• EAN codes should be 13 digits if provided</li>
                            <li>• Use decimal format for prices (e.g., 15.99)</li>
                            <li>• Test with a small file first</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">⚠️ Common Issues</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Duplicate EAN codes will be rejected</li>
                            <li>• Empty product names will be skipped</li>
                            <li>• Invalid date formats may cause errors</li>
                            <li>• Special characters might need encoding</li>
                            <li>• Large files may take time to process</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
