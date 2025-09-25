@extends('layouts.grower')

@section('title', 'Stampa Etichetta')

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

    /* Bottom section - Barcode + EAN + Client */
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

    /* Bottom info line - EAN left, Client right */
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
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="no-print" style="text-align:center; margin-top:30px;">
    <a href="{{ route('grower.order-items.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">&larr; Torna alle Etichette</a>
    <button onclick="printLabelOnly()" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Stampa Etichetta</button>
</div>

<div id="label-print-area" class="label-container">
    <!-- Top section: QR Code + Product Info -->
    <div class="label-top-section">
        <!-- QR Code -->
        <div class="label-qr-container">
            @if($labelData['qr_code'])
                {!! $labelData['qr_code']['svg'] !!}
            @else
                <div style="width:48px; height:48px; background:#eee; display:flex; align-items:center; justify-content:center; color:#aaa; font-size:8px;">QR</div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="label-product-info">
            <div class="label-product-name">{{ $labelData['product_name'] }}</div>
            <div class="label-price">{{ $labelData['unit_price'] }}</div>
        </div>
    </div>

    <!-- Bottom section: Barcode + EAN + Client -->
    <div class="label-bottom-section">
        <!-- Barcode -->
        <div class="label-barcode-container">
            @if($labelData['barcode'])
                <div class="barcode">{!! $labelData['barcode']['html'] !!}</div>
            @else
                <div style="height:16px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; font-size:6px; color:#999;">Nessun barcode</div>
            @endif
        </div>

        <!-- EAN + Client info -->
        <div class="label-bottom-info">
            <span class="label-ean-text">
                @if($labelData['ean'])
                    {{ $labelData['ean'] }}
                @else
                    {{ $labelData['product_code'] ?? 'N/A' }}
                @endif
            </span>
            <span class="label-client-code">{{ $labelData['store_name'] }}</span>
        </div>
    </div>
</div>

<script>
function printLabelOnly() {
    const printContents = document.getElementById('label-print-area').outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
