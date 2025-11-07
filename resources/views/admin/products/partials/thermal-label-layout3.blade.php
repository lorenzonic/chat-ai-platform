{{-- Layout 3: Bilanciato - QR 15mm + Barcode ottimizzato --}}
<div class="thermal-label thermal-label-layout3">
    <!-- Top: QR + Product Name -->
    <div class="layout3-top-section">
        <!-- QR Code - 15mm -->
        <div class="layout3-qr-container">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
            @endif
        </div>

        <!-- Product Name + Price -->
        <div class="layout3-product-info">
            <div class="layout3-product-name">
                {{ $labelData['name'] }}
            </div>
            @if($labelData['price'] != 'N/A' && (float)$labelData['price'] > 0)
            <div class="layout3-price">
                {{ $labelData['formatted_price'] }}
            </div>
            @endif
        </div>
    </div>

    <!-- Bottom: Barcode GRANDE + EAN -->
    <div class="layout3-bottom-section">
        @if($labelData['barcode'])
        <div class="layout3-barcode-container">
            <div class="barcode">
                *{{ $labelData['barcode']['code'] }}*
            </div>
        </div>
        <div class="layout3-ean-text">
            {{ $orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? '') }}
        </div>
        @endif
    </div>
</div>

<style>
/* Layout 3 Specific Styles - BILANCIATO */
.thermal-label-layout3 .layout3-top-section {
    height: 57px; /* ~15mm */
    display: flex;
    gap: 4px;
    margin-bottom: 2px;
}

.thermal-label-layout3 .layout3-qr-container {
    width: 57px; /* ~15mm - QR MEDIO */
    height: 57px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ff9800;
    background: white;
}

.thermal-label-layout3 .layout3-qr-container svg {
    width: 55px !important;
    height: 55px !important;
    display: block;
}

/* QR optimization for thermal printing - Layout 3 */
.thermal-label-layout3 .layout3-qr-container svg {
    shape-rendering: crispEdges !important;
}

@media print {
    .thermal-label-layout3 .layout3-qr-container {
        background: white !important;
        border: none !important;
    }
    
    .thermal-label-layout3 .layout3-qr-container svg {
        image-rendering: pixelated !important;
        shape-rendering: crispEdges !important;
    }
    
    /* Force crisp rendering without changing colors */
    .thermal-label-layout3 .layout3-qr-container svg * {
        shape-rendering: crispEdges !important;
    }
}

.thermal-label-layout3 .layout3-product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2px;
}

.thermal-label-layout3 .layout3-product-name {
    font-size: 11px;
    font-weight: bold;
    line-height: 1.2;
    color: #000;
    overflow: hidden;
    max-height: 40px;
}

.thermal-label-layout3 .layout3-price {
    font-size: 14px;
    font-weight: bold;
    color: #ff9800;
}

/* Barcode section */
.thermal-label-layout3 .layout3-bottom-section {
    height: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.thermal-label-layout3 .layout3-barcode-container {
    width: 100%;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    margin-bottom: 1px;
}

.thermal-label-layout3 .layout3-barcode-container .barcode {
    font-family: 'IDAutomationHC39M', 'Courier New', monospace !important;
    font-size: 16px; /* PIÙ GRANDE del layout 1 */
    letter-spacing: 0.4px;
    line-height: 1;
    text-align: center;
    font-weight: normal !important;
    color: #000000 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.thermal-label-layout3 .layout3-ean-text {
    font-size: 8px;
    font-weight: bold;
    color: #000;
    text-align: center;
}
</style>
