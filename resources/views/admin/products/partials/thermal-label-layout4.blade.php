{{-- Layout 4: BARCODE DOMINANTE - Barcode ULTRA Grande dentro --}}
<div class="thermal-label thermal-label-layout4">
    <!-- Top: QR piccolo + Nome compatto -->
    <div class="layout4-top-section">
        <!-- QR Code - 12mm PICCOLO -->
        <div class="layout4-qr-mini">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 5px;">QR</div>
            @endif
        </div>

        <!-- Product Name compatto -->
        <div class="layout4-product-compact">
            {{ Str::limit($labelData['name'], 32) }}
        </div>
    </div>

    <!-- Bottom: Barcode GIGANTE CENTRATO -->
    <div class="layout4-bottom-section">
        @if($labelData['barcode'])
        <div class="layout4-barcode-container">
            <div class="barcode layout4-barcode-ultra">
                *{{ $labelData['barcode']['code'] }}*
            </div>
        </div>
        <div class="layout4-ean-text">
            EAN: {{ $orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? 'N/A') }}
        </div>
        @endif
    </div>
</div>

<style>
/* === LAYOUT 4: BARCODE DOMINANTE - THERMAL ONLY BLACK === */

.thermal-label-layout4 {
    width: 50mm;
    height: 25mm;
    display: flex;
    flex-direction: column;
    background: white;
    border: 2px solid #000;
    position: relative;
    overflow: hidden;
}

/* Top Section: QR piccolo + Nome */
.layout4-top-section {
    display: flex;
    gap: 1mm;
    padding: 1mm;
    height: 10mm;
    border-bottom: 1px solid #000;
    align-items: center;
}

/* QR Mini - 12mm COMPATTO */
.layout4-qr-mini {
    width: 12mm;
    height: 12mm;
    min-width: 12mm;
    min-height: 12mm;
    max-width: 12mm;
    max-height: 12mm;
    border: 1px solid #000;
    background: white;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.layout4-qr-mini svg {
    width: 100% !important;
    height: 100% !important;
    display: block !important;
    shape-rendering: crispEdges;
    image-rendering: pixelated;
}

/* Product Name compatto */
.layout4-product-compact {
    flex: 1;
    font-size: 8px;
    font-weight: 700;
    line-height: 1.1;
    color: #000;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    word-break: break-word;
}

/* Bottom: Barcode GIGANTE (75% etichetta) */
.layout4-bottom-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1mm 2mm;
}

.layout4-barcode-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1mm;
}

.layout4-barcode-ultra {
    font-family: 'IDAutomationHC39M', 'Libre Barcode 39', monospace;
    font-size: 22px !important; /* ULTRA GRANDE */
    line-height: 1 !important;
    letter-spacing: 0 !important;
    color: #000000;
    transform: scaleY(1.5); /* Stretch verticale */
    text-align: center;
    white-space: nowrap;
    padding: 0;
    margin: 0;
}

.layout4-ean-text {
    font-size: 7px;
    font-weight: 700;
    color: #000;
    text-align: center;
    font-family: 'Courier New', monospace;
}

/* Print Optimizations */
@media print {
    .thermal-label-layout4 {
        page-break-inside: avoid;
        background: white !important;
        border: 1px solid #000 !important;
    }

    .thermal-label-layout4 * {
        color: #000 !important;
    }

    .layout4-qr-mini svg {
        shape-rendering: crispEdges !important;
        image-rendering: pixelated !important;
    }

    .layout4-barcode-ultra {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
