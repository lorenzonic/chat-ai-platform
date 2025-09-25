@extends('layouts.grower')

@section('title', 'Etichetta Prodotto')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏷️ Etichetta Prodotto</h1>
                <p class="mt-2 text-gray-600">{{ $labelData['product_name'] }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('grower.order-items.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    ← Torna alle Etichette
                </a>
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    🖨️ Stampa Etichetta
                </button>
            </div>
        </div>
    </div>

    <!-- Print Preview Container -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden print-container">
        <!-- Label Content -->
        <div class="label-content" style="width: 100mm; height: 70mm; padding: 8mm; margin: 0 auto; border: 1px solid #ddd; page-break-after: always;">

            <!-- Header Section -->
            <div class="label-header" style="text-align: center; margin-bottom: 6mm; border-bottom: 1px solid #eee; padding-bottom: 3mm;">
                <h2 style="font-size: 14pt; font-weight: bold; margin: 0; color: #2d5016;">{{ $labelData['grower_name'] }}</h2>
                <p style="font-size: 9pt; margin: 1mm 0 0 0; color: #666;">{{ $labelData['store_name'] }}</p>
            </div>

            <!-- Product Info Section -->
            <div class="product-info" style="margin-bottom: 6mm;">
                <h3 style="font-size: 12pt; font-weight: bold; margin: 0 0 2mm 0; color: #1a1a1a; line-height: 1.2;">
                    {{ $labelData['product_name'] }}
                </h3>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2mm;">
                    <div>
                        @if($labelData['product_code'])
                            <p style="font-size: 8pt; margin: 0; color: #666;">Codice: {{ $labelData['product_code'] }}</p>
                        @endif
                        @if($labelData['ean'])
                            <p style="font-size: 8pt; margin: 0; color: #666;">EAN: {{ $labelData['ean'] }}</p>
                        @endif
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 10pt; font-weight: bold; margin: 0; color: #2d5016;">
                            {{ $labelData['unit_price'] }}
                        </p>
                        @if($orderItem->quantity > 1)
                            <p style="font-size: 8pt; margin: 0; color: #666;">
                                Qtà: {{ $orderItem->quantity }} - Tot: {{ $labelData['total_price'] }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- QR Code and Barcode Section -->
            <div class="codes-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6mm;">
                <!-- QR Code -->
                <div class="qr-code" style="text-align: center;">
                    @if($labelData['qr_code'])
                        <div style="width: 20mm; height: 20mm; margin: 0 auto;">
                            {!! $labelData['qr_code']['svg'] !!}
                        </div>
                        <p style="font-size: 6pt; margin: 1mm 0 0 0; color: #999;">Scansiona per info</p>
                    @endif
                </div>

                <!-- Barcode -->
                <div class="barcode" style="text-align: center; flex-grow: 1; margin-left: 4mm;">
                    @if($labelData['barcode'])
                        <div style="transform: scale(0.8); transform-origin: center;">
                            {!! $labelData['barcode']['html'] !!}
                        </div>
                    @else
                        <div style="text-align: center; padding: 4mm;">
                            <p style="font-size: 8pt; color: #999; margin: 0;">Codice a barre non disponibile</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Section -->
            <div class="label-footer" style="border-top: 1px solid #eee; padding-top: 3mm; font-size: 7pt; color: #999; text-align: center;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Ordine: {{ $labelData['order_number'] }}</span>
                    <span>{{ $labelData['formatted_date'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details (not printed) -->
    <div class="mt-8 no-print">
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dettagli Prodotto</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Informazioni Prodotto</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Nome:</dt>
                            <dd class="font-medium">{{ $labelData['product_name'] }}</dd>
                        </div>
                        @if($labelData['product_code'])
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Codice:</dt>
                                <dd>{{ $labelData['product_code'] }}</dd>
                            </div>
                        @endif
                        @if($labelData['ean'])
                            <div class="flex justify-between">
                                <dt class="text-gray-500">EAN:</dt>
                                <dd>{{ $labelData['ean'] }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Quantità:</dt>
                            <dd>{{ $orderItem->quantity }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Informazioni Ordine</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Numero Ordine:</dt>
                            <dd>{{ $labelData['order_number'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Negozio:</dt>
                            <dd>{{ $labelData['store_name'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Prezzo Unitario:</dt>
                            <dd>{{ $labelData['unit_price'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Prezzo Totale:</dt>
                            <dd class="font-medium">{{ $labelData['total_price'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Data:</dt>
                            <dd>{{ $labelData['formatted_date'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything except the label */
    body * {
        visibility: hidden;
    }

    .print-container, .print-container * {
        visibility: visible;
    }

    .no-print {
        display: none !important;
    }

    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
    }

    /* Ensure label fits on page */
    .label-content {
        margin: 0 !important;
        border: 1px solid #000 !important;
    }

    /* Make sure QR and barcode print correctly */
    .qr-code svg {
        max-width: 20mm;
        max-height: 20mm;
    }

    /* Page settings */
    @page {
        size: A4;
        margin: 15mm;
    }
}

/* Screen styles */
.label-content {
    background: white;
    font-family: Arial, sans-serif;
}

.qr-code svg {
    width: 100%;
    height: 100%;
}

/* Responsive adjustments for preview */
@media (max-width: 768px) {
    .label-content {
        width: 90%;
        height: auto;
        min-height: 70mm;
    }
}
</style>
@endsection
