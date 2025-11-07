{{-- Layout 2: QR Grande - Solo QR + Nome + Prezzo (NESSUN BARCODE) --}}
<div class="thermal-label thermal-label-layout2">
    <div class="layout2-container">
        <!-- QR Code GRANDE - 20mm -->
        <div class="layout2-qr-container">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 8px; text-align: center;">QR<br>N/A</div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="layout2-product-info">
            <!-- Product Name -->
            <div class="layout2-product-name">
                {{ $labelData['name'] }}
            </div>

            <!-- Price -->
            @if($labelData['price'] != 'N/A' && (float)$labelData['price'] > 0)
            <div class="layout2-price">
                {{ $labelData['formatted_price'] }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Layout 2 Specific Styles - QR GRANDE */
.thermal-label-layout2 .layout2-container {
    display: flex;
    gap: 4px;
    height: 100%;
    align-items: center;
}

.thermal-label-layout2 .layout2-qr-container {
    width: 75px; /* ~20mm - QR GRANDE */
    height: 75px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #1976d2;
    background: white;
}

.thermal-label-layout2 .layout2-qr-container svg {
    width: 73px !important;
    height: 73px !important;
    display: block;
}

/* QR optimization for thermal printing - Layout 2 */
.thermal-label-layout2 .layout2-qr-container svg path,
.thermal-label-layout2 .layout2-qr-container svg rect {
    shape-rendering: crispEdges !important;
}

@media print {
    .thermal-label-layout2 .layout2-qr-container {
        background: white !important;
        border: none !important;
    }
    
    .thermal-label-layout2 .layout2-qr-container svg {
        image-rendering: pixelated !important;
        shape-rendering: crispEdges !important;
    }
    
    .thermal-label-layout2 .layout2-qr-container svg path,
    .thermal-label-layout2 .layout2-qr-container svg rect {
        fill: black !important;
        shape-rendering: crispEdges !important;
    }
}
    height: 73px !important;
}

.thermal-label-layout2 .layout2-product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 8px;
}

.thermal-label-layout2 .layout2-product-name {
    font-size: 13px;
    font-weight: bold;
    line-height: 1.3;
    color: #000;
    text-align: left;
}

.thermal-label-layout2 .layout2-price {
    font-size: 18px;
    font-weight: bold;
    color: #1976d2;
    text-align: left;
}
</style>
