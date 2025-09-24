@extends('layouts.grower')

@section('title', 'Stampa Bulk - Etichette Legacy')

@section('content')
<style>
    /* Same styles as bulk-print.blade.php but for legacy products */
    @media print {
        body {
            background: white !important;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .no-print { display: none !important; }
        .bulk-labels-container {
            page-break-inside: avoid;
            margin: 0;
            padding: 10px;
        }
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            page-break-inside: avoid;
        }
        .label-item {
            box-shadow: none !important;
            border: 2px solid black !important;
            page-break-inside: avoid;
            margin-bottom: 8px;
        }
        .label-barcode-container .barcode .bar,
        .label-barcode-container .barcode div[style*="background"],
        .label-barcode-container .barcode div[style*="color"] {
            background-color: black !important;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .label-barcode-container .barcode {
            background-color: white !important;
        }
    }

    .label-item {
        width: 189px;
        height: 94px;
        border: 2px solid #333;
        background: white;
        margin: 10px;
        padding: 3px;
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        position: relative;
        box-sizing: border-box;
    }

    .label-top-section {
        height: 55px;
        display: flex;
        margin-bottom: 3px;
    }

    .label-qr-container {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        margin-right: 6px;
    }

    .label-qr-container svg {
        width: 48px !important;
        height: 48px !important;
    }

    .label-product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px;
    }

    .label-product-name {
        font-size: 8px;
        font-weight: bold;
        line-height: 1.1;
        max-height: 32px;
        overflow: hidden;
        text-align: left;
        margin-bottom: 4px;
    }

    .label-price {
        font-size: 12px;
        font-weight: bold;
        color: #000;
        text-align: left;
        margin: 4px 0;
    }

    .label-bottom-section {
        height: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .label-barcode-container {
        width: 100%;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2px;
        padding: 0 2px;
    }

    .label-barcode-container .barcode {
        transform: scale(0.7, 0.5);
        font-size: 3px;
        width: 90%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: white !important;
        overflow: hidden;
    }

    .label-barcode-container .barcode .bar,
    .label-barcode-container .barcode div[style*="background"],
    .label-barcode-container .barcode div[style*="color"] {
        background-color: black !important;
        color: black !important;
    }

    .label-bottom-info {
        display: flex;
        justify-content: space-between;
        font-size: 6px;
        color: black;
        line-height: 1.1;
        gap: 4px;
    }

    .label-ean-text {
        font-weight: bold;
        flex-shrink: 0;
    }

    .label-client-code {
        font-weight: bold;
        text-align: right;
        flex: 1;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        max-width: 80px;
    }

    @media screen {
        .bulk-labels-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .labels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
    }
</style>

<div class="bulk-labels-container">
    <!-- Screen-only navigation and info -->
    <div class="no-print">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">🖨️ Stampa Bulk Legacy - {{ count($labelsData) }} Etichette</h1>
            <div class="flex gap-2">
                <button onclick="printLabels()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                    🖨️ Stampa Tutte
                </button>
                <a href="{{ route('grower.products.stickers.index', ['legacy' => 1]) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    ← Torna alla Lista Legacy
                </a>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800">
                <strong>⚠️ Modalità Legacy:</strong> Stai stampando etichette dalla struttura prodotti legacy.
                I dati potrebbero essere limitati rispetto alla nuova struttura order items.
            </p>
        </div>
    </div>

    <!-- PRINTABLE LABELS GRID -->
    <div class="labels-grid">
        @foreach($labelsData as $labelData)
            <div class="label-item">
                <!-- Top section: QR + Product Info -->
                <div class="label-top-section">
                    <!-- QR Code -->
                    <div class="label-qr-container">
                        @if($labelData['qr_code']['svg'] ?? null)
                            {!! $labelData['qr_code']['svg'] !!}
                        @else
                            <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="label-product-info">
                        <!-- Product Name -->
                        <div class="label-product-name">
                            {{ $labelData['product_name'] ?? 'N/A' }}
                        </div>

                        <!-- Price -->
                        <div class="label-price">
                            {{ $labelData['unit_price'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Bottom section: Long Barcode + EAN/Client -->
                <div class="label-bottom-section">
                    <!-- Long horizontal barcode -->
                    @if($labelData['barcode'] ?? null)
                    <div class="label-barcode-container">
                        <div class="barcode">
                            {!! $labelData['barcode']['html'] !!}
                        </div>
                    </div>
                    @endif

                    <!-- Bottom info: EAN left, Store name right -->
                    <div class="label-bottom-info">
                        <div class="label-ean-text">
                            {{ $labelData['ean'] ?? 'N/A' }}
                        </div>
                        <div class="label-client-code">
                            {{ Str::limit($labelData['store_name'] ?? 'N/A', 12) }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(count($labelsData) === 0)
        <div class="no-print text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📦</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna etichetta da stampare</h3>
            <p class="text-gray-500">Non ci sono prodotti legacy che corrispondono ai filtri selezionati.</p>
            <a href="{{ route('grower.products.stickers.index', ['legacy' => 1]) }}"
               class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                ← Torna alla Lista Legacy
            </a>
        </div>
    @endif
</div>

<script>
function printLabels() {
    // Same print function as bulk-print.blade.php
    var printWindow = window.open('', '_blank');
    var labelsContent = document.querySelector('.labels-grid').outerHTML;

    var printDocument = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Stampa Bulk Etichette Legacy - {{ count($labelsData) }} etichette</title>
            <style>
                @page { margin: 5mm; size: A4 landscape; }
                body { margin: 0; padding: 10px; font-family: Arial, sans-serif; background: white; }
                .labels-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
                .label-item { width: 189px; height: 94px; border: 2px solid black; background: white; padding: 3px; font-family: Arial, sans-serif; display: flex; flex-direction: column; box-sizing: border-box; page-break-inside: avoid; margin-bottom: 8px; }
                .label-top-section { height: 55px; display: flex; margin-bottom: 3px; }
                .label-qr-container { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; margin-right: 6px; }
                .label-qr-container svg { width: 48px !important; height: 48px !important; }
                .label-product-info { flex: 1; display: flex; flex-direction: column; justify-content: space-between; padding: 2px; }
                .label-product-name { font-size: 8px; font-weight: bold; line-height: 1.1; max-height: 32px; overflow: hidden; text-align: left; margin-bottom: 4px; }
                .label-price { font-size: 12px; font-weight: bold; color: #000; text-align: left; margin: 4px 0; }
                .label-bottom-section { height: 30px; display: flex; flex-direction: column; justify-content: center; }
                .label-barcode-container { width: 100%; height: 18px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px; padding: 0 2px; }
                .label-barcode-container .barcode { transform: scale(0.7, 0.5); font-size: 3px; width: 90%; display: flex; justify-content: center; align-items: center; background-color: white !important; overflow: hidden; }
                .label-barcode-container .barcode .bar, .label-barcode-container .barcode div[style*="background"], .label-barcode-container .barcode div[style*="color"] { background-color: black !important; color: black !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                .label-bottom-info { display: flex; justify-content: space-between; font-size: 6px; color: black; line-height: 1.1; gap: 4px; }
                .label-ean-text { font-weight: bold; flex-shrink: 0; }
                .label-client-code { font-weight: bold; text-align: right; flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 80px; }
            </style>
        </head>
        <body>${labelsContent}</body>
        </html>
    `;

    printWindow.document.write(printDocument);
    printWindow.document.close();
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
}
</script>
@endsection
