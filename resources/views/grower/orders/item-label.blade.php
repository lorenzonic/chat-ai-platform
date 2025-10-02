@extends('layouts.grower')

@section('title', 'Order Item Label: ' . $orderItem->product_info['name'])

@section('content')
<style>
    /* Print-specific styles */
    @media print {
        body { background: white !important; margin: 0; padding: 0; }
        .no-print { display: none !important; }
        .label-container {
            box-shadow: none !important;
            border: 2px solid black !important;
            page-break-inside: avoid;
        }
        /* Force barcode bars black, background white for print */
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

    /* Label container - Format 50x25mm (5cm x 2.5cm) */
    .label-container {
        width: 189px;  /* 50mm = ~189px (5cm) */
        height: 94px;  /* 25mm = ~94px (2.5cm) */
        border: 2px solid #333;
        background: white;
        margin: 20px auto;
        padding: 3px;
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        position: relative;
        box-sizing: border-box;
    }

    /* Top section - QR + Product Info */
    .label-top-section {
        height: 55px;
        display: flex;
        margin-bottom: 3px;
    }

    /* QR Code - Top left */
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

    /* Product info - Top right */
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

    /* Bottom section - Barcode + EAN + Store */
    .label-bottom-section {
        height: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Long horizontal barcode */
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

    /* Force barcode bars to be black, background white */
    .label-barcode-container .barcode .bar,
    .label-barcode-container .barcode div[style*="background"],
    .label-barcode-container .barcode div[style*="color"] {
        background-color: black !important;
        color: black !important;
    }

    /* Bottom info line - EAN left, Store right */
    .label-bottom-info {
        display: flex;
        justify-content: space-between;
        font-size: 5px;
        color: black;
        line-height: 1.1;
    }

    .label-ean-text {
        font-weight: bold;
    }

    .label-order-info {
        font-weight: normal;
    }

    /* Screen-only styles */
    @media screen {
        .product-details {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .product-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
        }

        .info-value {
            color: #212529;
            margin-top: 4px;
        }
    }
</style>

<div class="product-details">
    <!-- Screen-only navigation and info -->
    <div class="no-print">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $labelData['product_name'] }}</h1>
            <div class="flex gap-2">
                <button onclick="printLabel()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    🖨️ Stampa Etichetta
                </button>
                <a href="{{ route('grower.products.stickers.index', ['order_id' => $orderItem->order_id]) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    ← Torna agli Items
                </a>
            </div>
        </div>

        <!-- Order Item Information Grid -->
        <div class="product-info-grid">
            <div class="info-card">
                <div class="info-label">Prodotto</div>
                <div class="info-value font-medium">{{ $labelData['product_name'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Codice Prodotto</div>
                <div class="info-value font-mono">{{ $labelData['product_code'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">SKU</div>
                <div class="info-value font-mono">{{ $labelData['sku'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">EAN</div>
                <div class="info-value font-mono">{{ $labelData['product_ean'] ?: '—' }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Quantità</div>
                <div class="info-value text-lg font-semibold">{{ $orderItem->quantity }} pz</div>
            </div>

            <div class="info-card">
                <div class="info-label">Prezzo Rivendita</div>
                <div class="info-value text-lg font-semibold">{{ $labelData['unit_price'] ?? '' }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Totale Item</div>
                <div class="info-value text-lg font-bold text-green-600">{{ $labelData['total_price'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Negozio</div>
                <div class="info-value font-mono">{{ $labelData['store_name'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Ordine</div>
                <div class="info-value font-mono">{{ $labelData['order_number'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Grower</div>
                <div class="info-value">{{ $labelData['grower_name'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Data</div>
                <div class="info-value">{{ $labelData['formatted_date'] }}</div>
            </div>

            @if($orderItem->notes)
            <div class="info-card">
                <div class="info-label">Note</div>
                <div class="info-value">{{ $orderItem->notes }}</div>
            </div>
            @endif
        </div>

        <!-- QR Code Information -->
        @if($labelData['qr_code'] && isset($labelData['qr_code']['url']))
        <div class="info-card mb-6">
            <div class="info-label">QR Code URL</div>
            <div class="info-value">
                <a href="{{ $labelData['qr_code']['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                    {{ $labelData['qr_code']['url'] }}
                </a>
                <p class="text-sm text-gray-600 mt-1">Questo QR code porta al chatbot del negozio con informazioni sul prodotto</p>
            </div>
        </div>
        @endif
    </div>

    <!-- PRINTABLE LABEL -->
    <div id="product-label" class="label-container">
        <!-- Top section: QR + Product Info -->
        <div class="label-top-section">
            <!-- QR Code (Product-specific) -->
            <div class="label-qr-container">
                @if($labelData['qr_code'] && isset($labelData['qr_code']['svg']))
                    {!! $labelData['qr_code']['svg'] !!}
                @else
                    <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="label-product-info">
                <!-- Product Name -->
                <div class="label-product-name">
                    {{ Str::limit($labelData['product_name'], 25) }}
                </div>

                <!-- Price (unit price from order item) -->
                <div class="label-price">
                    {{ $labelData['unit_price'] }}
                </div>
            </div>
        </div>

        <!-- Bottom section: Long Barcode + EAN/Store -->
        <div class="label-bottom-section">
            <!-- Long horizontal barcode - only if EAN exists -->
            @if($labelData['barcode'])
            <div class="label-barcode-container">
                <div class="barcode">
                    {!! $labelData['barcode']['html'] !!}
                </div>
            </div>
            @endif

            <!-- Bottom info: EAN left, Store name right -->
            <div class="label-bottom-info">
                <div class="label-ean-text">
                    {{ $labelData['product_ean'] ?: '' }}
                </div>
                <div class="label-order-info">
                    {{ $labelData['store_name'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Timestamps (screen only) -->
    <div class="no-print mt-8 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
            <div>
                <strong>Item creato:</strong> {{ $orderItem->created_at->format('d/m/Y H:i') }}
            </div>
            <div>
                <strong>Ultima modifica:</strong> {{ $orderItem->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<script>
function printLabel() {
    // Create a new window for printing
    var printWindow = window.open('', '_blank');

    // Get the label content
    var labelContent = document.getElementById('product-label').outerHTML;

    // Create the print document
    var printDocument = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Etichetta Order Item - {{ $labelData['product_name'] }}</title>
            <style>
                @page {
                    margin: 5mm;
                    size: A4;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    background: white;
                }

                .label-container {
                    width: 189px;
                    height: 94px;
                    border: 2px solid #333;
                    background: white;
                    margin: 20px auto;
                    padding: 3px;
                    font-family: Arial, sans-serif;
                    display: flex;
                    flex-direction: column;
                    position: relative;
                    box-sizing: border-box;
                    page-break-inside: avoid;
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
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                .label-bottom-info {
                    display: flex;
                    justify-content: space-between;
                    font-size: 5px;
                    color: black;
                    line-height: 1.1;
                }

                .label-ean-text {
                    font-weight: bold;
                }

                .label-order-info {
                    font-weight: normal;
                }
            </style>
        </head>
        <body>
            ${labelContent}
        </body>
        </html>
    `;

    // Write the document and print
    printWindow.document.write(printDocument);
    printWindow.document.close();

    // Wait for the content to load, then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}
</script>
@endsection
