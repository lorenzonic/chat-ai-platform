@extends('layouts.admin')

@section('title', 'Import Orders from CSV')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🛒 Import Orders from CSV</h1>
            <p class="mt-2 text-gray-600">Upload your OrderItems CSV to automatically create Orders, Products, Growers and Stores</p>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <h4 class="font-bold">✅ Import Successful!</h4>
                <pre class="mt-2 text-sm whitespace-pre-wrap">{{ session('success') }}</pre>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <h4 class="font-bold">❌ Import Failed!</h4>
                <p class="mt-2">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                <h4 class="font-bold">⚠️ Warning</h4>
                <p class="mt-2">{{ session('warning') }}</p>
            </div>
        @endif

        <!-- Step 1: Upload File -->
        @if(!isset($previewData))
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📁 Step 1: Upload CSV File</h2>

                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">📋 Expected CSV Structure</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <strong>Required Columns:</strong>
                            <ul class="list-disc list-inside mt-1">
                                <li>Fornitore (Supplier/Grower)</li>
                                <li>Codice (Product Code)</li>
                                <li>Prodotto (Product Name)</li>
                                <li>Quantità or Piani (Quantity)</li>
                                <li>Cliente (Store/Client)</li>
                            </ul>
                        </div>
                        <div>
                            <strong>Optional Columns:</strong>
                            <ul class="list-disc list-inside mt-1">
                                <li>€ Vendita (Price)</li>
                                <li>EAN (Barcode)</li>
                                <li>Data (Date)</li>
                                <li>H (Height)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.import.orders.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="mb-6">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                            CSV File <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" name="csv_file" id="csv_file" required
                                   accept=".csv,.xlsx,.xls"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <div id="upload-progress" class="hidden mt-2">
                                <div class="bg-blue-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <p class="text-sm text-blue-600 mt-1">Uploading and analyzing...</p>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Supported formats: .csv, .xlsx, .xls (max 10MB)
                        </p>
                        @error('csv_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <p>✅ Auto-detection of columns</p>
                            <p>✅ Smart grouping by client and date</p>
                            <p>✅ Automatic entity creation</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.import.template') }}"
                               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                📥 Download Template
                            </a>
                            <button type="submit" id="upload-btn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium disabled:opacity-50">
                                📊 Upload & Analyze CSV
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                document.getElementById('uploadForm').addEventListener('submit', function() {
                    document.getElementById('upload-progress').classList.remove('hidden');
                    document.getElementById('upload-btn').disabled = true;

                    // Simulate progress
                    let progress = 0;
                    const progressBar = document.querySelector('#upload-progress .bg-blue-600');
                    const interval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress > 90) progress = 90;
                        progressBar.style.width = progress + '%';
                        if (progress >= 90) clearInterval(interval);
                    }, 200);
                });
                </script>
            </div>
        </div>
        @endif

        <!-- Step 2: Preview & Map Columns -->
        @if(isset($previewData))
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔗 Step 2: Map Columns & Import</h2>
                <p class="text-gray-600 mb-6">Map your CSV columns to our system fields and import the data:</p>

                <form action="{{ route('admin.import.orders.process') }}" method="POST" id="importForm">
                    @csrf
                    <input type="hidden" name="file_path" value="{{ $previewData['file_path'] }}">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Column Mapping -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Map CSV Columns</h3>

                            <div class="space-y-3">
                                @php
                                $fields = [
                                    'fornitore' => ['label' => 'Fornitore/Grower', 'required' => true, 'icon' => '🏭'],
                                    'codice' => ['label' => 'Codice Prodotto', 'required' => true, 'icon' => '🏷️'],
                                    'prodotto' => ['label' => 'Nome Prodotto', 'required' => true, 'icon' => '🌱'],
                                    'quantita' => ['label' => 'Quantità', 'required' => true, 'icon' => '📦'],
                                    'cliente' => ['label' => 'Cliente/Store', 'required' => true, 'icon' => '🏪'],
                                    'prezzo' => ['label' => 'Prezzo € Vendita', 'required' => false, 'icon' => '💰'],
                                    'code' => ['label' => 'CODE/Ref', 'required' => false, 'icon' => '🔖'],
                                    'altezza' => ['label' => 'H/Altezza', 'required' => false, 'icon' => '📏'],
                                    'ean' => ['label' => 'EAN Code', 'required' => false, 'icon' => '📊'],
                                    'cc' => ['label' => 'CC Code', 'required' => false, 'icon' => '🆔'],
                                    'pia' => ['label' => 'PIA Code', 'required' => false, 'icon' => '📋'],
                                    'pro' => ['label' => 'PRO Code', 'required' => false, 'icon' => '📄'],
                                    'trasporto' => ['label' => 'Trasporto', 'required' => false, 'icon' => '🚚'],
                                    'data' => ['label' => 'Data Consegna', 'required' => false, 'icon' => '📅'],
                                    'telefono' => ['label' => 'Telefono', 'required' => false, 'icon' => '📞'],
                                    'note' => ['label' => 'Note', 'required' => false, 'icon' => '📝'],
                                    'piani' => ['label' => 'Piani/Levels', 'required' => false, 'icon' => '📚'],
                                    'indirizzo' => ['label' => 'Indirizzo', 'required' => false, 'icon' => '📍'],
                                ];
                                @endphp

                                @foreach($fields as $fieldName => $fieldInfo)
                                <div class="flex items-center space-x-3">
                                    <div class="w-36 flex-shrink-0">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $fieldInfo['icon'] }} {{ $fieldInfo['label'] }}
                                            @if($fieldInfo['required'])
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="flex-1">
                                        <select name="mapping[{{ $fieldName }}]"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                @if($fieldInfo['required']) required @endif>
                                            <option value="">-- Select Column --</option>
                                            @foreach($previewData['headers'] as $index => $header)
                                                <option value="{{ $index }}"
                                                    @if(str_contains(strtolower($header), strtolower($fieldName)) ||
                                                        ($fieldName === 'fornitore' && str_contains(strtolower($header), 'fornitore')) ||
                                                        ($fieldName === 'codice' && str_contains(strtolower($header), 'codice')) ||
                                                        ($fieldName === 'prodotto' && str_contains(strtolower($header), 'prodotto')) ||
                                                        ($fieldName === 'quantita' && str_contains(strtolower($header), 'quantit')) ||
                                                        ($fieldName === 'cliente' && str_contains(strtolower($header), 'cliente')) ||
                                                        ($fieldName === 'prezzo' && str_contains(strtolower($header), 'vendita')) ||
                                                        ($fieldName === 'altezza' && str_contains(strtolower($header), 'h')) ||
                                                        ($fieldName === 'trasporto' && str_contains(strtolower($header), 'trasporto')) ||
                                                        ($fieldName === 'data' && str_contains(strtolower($header), 'data')) ||
                                                        ($fieldName === 'telefono' && str_contains(strtolower($header), 'telefono')) ||
                                                        ($fieldName === 'note' && str_contains(strtolower($header), 'note')) ||
                                                        ($fieldName === 'piani' && str_contains(strtolower($header), 'piani')) ||
                                                        ($fieldName === 'indirizzo' && str_contains(strtolower($header), 'indirizzo')))
                                                        selected
                                                    @endif>
                                                    {{ chr(65 + $index) }}: {{ $header }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">👁️ CSV Preview</h3>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto max-h-96">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                                @foreach($previewData['headers'] as $index => $header)
                                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase max-w-24">
                                                        {{ chr(65 + $index) }}<br>{{ Str::limit($header, 15) }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach(array_slice($previewData['rows'], 0, 5) as $rowIndex => $row)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-2 py-2 text-xs text-gray-500">{{ $rowIndex + 1 }}</td>
                                                    @foreach($row as $cell)
                                                        <td class="px-2 py-2 text-xs text-gray-900 max-w-24 truncate">
                                                            {{ Str::limit($cell, 20) }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500">
                                    Showing first 5 rows of {{ count($previewData['rows']) }} total rows
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.import.orders') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                            ← Upload Different File
                        </a>

                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                            🚀 Import Orders (Auto-Generate Order Numbers)
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Information Panel -->
        @if(!isset($previewData))
        <div class="mt-6 bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">ℹ️ How This Import Works</h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-xl">📁</span>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">1. Upload</h3>
                        <p class="text-sm text-gray-600">Upload your OrderItems CSV file</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-xl">🔗</span>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">2. Map</h3>
                        <p class="text-sm text-gray-600">Map CSV columns to fields</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-xl">⚡</span>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">3. Auto-Create</h3>
                        <p class="text-sm text-gray-600">Create everything automatically</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-xl">✅</span>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">4. Done</h3>
                        <p class="text-sm text-gray-600">View imported orders</p>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-800 mb-2">🎯 What Gets Created</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• <strong>Growers</strong>: Auto-created from "Fornitore" column</li>
                        <li>• <strong>Products</strong>: Auto-created with codes, prices, heights</li>
                        <li>• <strong>Stores</strong>: Auto-created from "Cliente" column</li>
                        <li>• <strong>Orders</strong>: Auto-generated numbers (ORD-2025-000001, etc.)</li>
                        <li>• <strong>OrderItems</strong>: One per CSV row with quantities</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const importForm = document.getElementById('importForm');

    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '⏳ Processing...';
                submitBtn.disabled = true;
            }
        });
    }

    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '⏳ Importing...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>
@endsection
