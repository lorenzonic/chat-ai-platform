{{-- Layout 4: Barcode Dominante - Barcode EXTRA GRANDE --}}
<div class="thermal-label thermal-label-layout4">
    <!-- Top: QR piccolo + Nome -->
    <div class="layout4-top-section">
        <!-- QR Code piccolo - 12mm -->
        <div class="layout4-qr-container">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
            @endif
        </div>

        <!-- Product Name breve -->
        <div class="layout4-product-name">
            {{ Str::limit($labelData['name'], 35) }}
        </div>
    </div>

    <!-- Bottom: Barcode GIGANTE (70% dell'etichetta) -->
    <div class="layout4-bottom-section">
        @if($labelData['barcode'])
        <div class="layout4-barcode-container">
            <div class="barcode">
                *{{ $labelData['barcode']['code'] }}*
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Layout 4 Specific Styles - BARCODE DOMINANTE */
.thermal-label-layout4 .layout4-top-section {
    height: 45px; /* ~12mm - ridotto per dare spazio al barcode */
    display: flex;
    gap: 4px;
    margin-bottom: 1px;
}

.thermal-label-layout4 .layout4-qr-container {
    width: 45px; /* ~12mm - QR PICCOLO */
    height: 45px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #9c27b0;
    background: white;
}

.thermal-label-layout4 .layout4-qr-container svg {
    width: 43px !important;
    height: 43px !important;
    display: block;
}

/* QR optimization for thermal printing - Layout 4 */
.thermal-label-layout4 .layout4-qr-container svg path,
.thermal-label-layout4 .layout4-qr-container svg rect {
    shape-rendering: crispEdges !important;
}

@media print {
    .thermal-label-layout4 .layout4-qr-container {
        background: white !important;
        border: none !important;
    }
    
    .thermal-label-layout4 .layout4-qr-container svg {
        image-rendering: pixelated !important;
        shape-rendering: crispEdges !important;
    }
    
    .thermal-label-layout4 .layout4-qr-container svg path,
    .thermal-label-layout4 .layout4-qr-container svg rect {
        fill: black !important;
        shape-rendering: crispEdges !important;
    }
}

.thermal-label-layout4 .layout4-product-name {
    flex: 1;
    font-size: 10px;
    font-weight: bold;
    line-height: 1.2;
    color: #000;
    display: flex;
    align-items: center;
    padding: 2px;
}

/* Barcode section - EXTRA LARGE */
.thermal-label-layout4 .layout4-bottom-section {
    height: 42px; /* ~11mm - MOLTO SPAZIO per barcode */
    display: flex;
    align-items: center;
    justify-content: center;
}

.thermal-label-layout4 .layout4-barcode-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    padding: 0 1mm;
}

.thermal-label-layout4 .layout4-barcode-container .barcode {
    font-family: 'IDAutomationHC39M', 'Courier New', monospace !important;
    font-size: 20px; /* BARCODE GIGANTE! */
    letter-spacing: 0.6px;
    line-height: 1;
    text-align: center;
    font-weight: normal !important;
    color: #000000 !important;
    transform: scaleY(1.3); /* Allunga ulteriormente */
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
</style>
