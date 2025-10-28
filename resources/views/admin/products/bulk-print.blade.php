@extends('layouts.admin')

@section('title', 'Stampa Bulk Etichette')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🖨️ Stampa Bulk Etichette</h1>
        <p class="mt-2 text-gray-600">Stampa tutte le etichette filtrate in un'unica operazione</p>
        <div class="mt-4 flex items-center space-x-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                📦 {{ $orderItems->count() }} Order Items
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                🏷️ {{ collect($bulkLabels)->sum('quantity') }} Etichette Totali
            </span>
        </div>
    </div>

    <!-- Bulk Print Actions -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">⚙️ Opzioni di Stampa</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input id="print_all" name="print_option" type="radio" checked
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="print_all" class="ml-3 block text-sm font-medium text-gray-700">
                            🖨️ Stampa Tutto
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="print_quantity" name="print_option" type="radio"
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="print_quantity" class="ml-3 block text-sm font-medium text-gray-700">
                            🔢 Rispetta Quantità ({{ collect($bulkLabels)->sum('quantity') }} etichette)
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        🖨️ Avvia Stampa
                    </button>
                    <a href="{{ route('admin.products.index', request()->query()) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        ← Torna alla Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Labels Preview -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">👁️ Anteprima Etichette</h2>
            <p class="mt-1 text-sm text-gray-600">Le etichette saranno stampate nell'ordine mostrato</p>
        </div>

        <div class="p-6">
            @if(count($bulkLabels) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bulkLabels as $index => $labelData)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 print-label"
                             data-quantity="{{ $labelData['quantity'] ?? 1 }}">
                            <!-- Label Header -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-gray-500">Etichetta #{{ $index + 1 }}</span>
                                <span class="text-xs font-medium text-blue-600">Qty: {{ $labelData['quantity'] ?? 1 }}</span>
                            </div>

                            <!-- Product Info -->
                            <div class="space-y-2">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ Str::limit($labelData['name'] ?? 'N/A', 30) }}
                                </div>
                                @if(isset($labelData['variety']) && $labelData['variety'])
                                    <div class="text-xs text-gray-600">
                                        Varietà: {{ $labelData['variety'] }}
                                    </div>
                                @endif
                                <div class="text-sm font-medium text-green-700">
                                    €{{ number_format((float) ($labelData['price'] ?? 0), 2, ',', '.') }}
                                </div>
                            </div>

                            <!-- Store & Order Info -->
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div>Store: {{ $labelData['store_name'] ?? 'N/A' }}</div>
                                    <div>Ordine: {{ $labelData['order_info']['number'] ?? 'N/A' }}</div>
                                    <div>Data: {{ $labelData['order_info']['delivery_date'] ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- QR Code Placeholder -->
                            <div class="mt-3 flex justify-center">
                                <div class="w-16 h-16 bg-white border border-gray-300 rounded flex items-center justify-center">
                                    <span class="text-xs text-gray-400">QR</span>
                                </div>
                            </div>

                            <!-- Barcode Placeholder -->
                            @if(isset($labelData['barcode']) && $labelData['barcode'])
                                <div class="mt-2 text-center">
                                    <div class="text-xs text-gray-500 mb-1">EAN: {{ $labelData['barcode']['code'] ?? 'N/A' }}</div>
                                    <div class="bg-white p-1 rounded border">
                                        {!! $labelData['barcode']['html'] ?? '<div class="text-xs text-gray-400">Barcode</div>' !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">🏷️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna etichetta da stampare</h3>
                    <p class="text-gray-600">Non ci sono etichette disponibili per la stampa bulk con i filtri attuali.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.products.index', request()->query()) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            ← Torna ai Filtri
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .print-label {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* Hide non-print elements */
    .no-print {
        display: none !important;
    }

    /* Ensure labels print properly */
    .print-label {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}

/* Quantity-based printing styles */
.print-label[data-quantity="1"] {
    /* Single label - normal size */
}

.print-label[data-quantity]:not([data-quantity="1"]) {
    /* Multiple labels - could add special styling */
    border: 2px solid #10b981;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle print options
    const printOptions = document.querySelectorAll('input[name="print_option"]');

    printOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.id === 'print_quantity') {
                // For quantity-based printing, we might need to duplicate labels
                console.log('Quantity-based printing selected');
            } else {
                console.log('Print all selected');
            }
        });
    });

    // Print functionality
    window.addEventListener('beforeprint', function() {
        console.log('Starting bulk print...');
    });

    window.addEventListener('afterprint', function() {
        console.log('Bulk print completed');
    });
});
</script>
@endsection