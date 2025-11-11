{{-- Layout 3: BILANCIATO PRO - QR 16mm + Barcode dentro --}}
<div class="thermal-label thermal-label-layout3">
    <!-- Top: QR + Product Info -->
    <div class="layout3-top-section">
        <!-- QR Code - 16mm -->
        <div class="layout3-qr-container">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="layout3-product-info">
            <div class="layout3-product-name">
                {{ Str::limit($labelData['name'], 45) }}
            </div>
            @if($labelData['price'] != 'N/A' && (float)$labelData['price'] > 0)
            <div class="layout3-price">
                {{ $labelData['formatted_price'] }}
            </div>
            @endif
            <div class="layout3-ean-text">
                EAN: {{ $orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? 'N/A') }}
            </div>
        </div>
    </div>

    <!-- Bottom: Barcode CENTRATO -->
    <div class="layout3-bottom-section">
        @if($labelData['barcode'])
        <div class="layout3-barcode-container">
            <div class="barcode layout3-barcode">
                *{{ $labelData['barcode']['code'] }}*
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* === LAYOUT 3: BILANCIATO PRO - THERMAL ONLY BLACK === */

.thermal-label-layout3 {
    width: 50mm;
    height: 25mm;
    display: flex;
    flex-direction: column;
    background: white;
    border: 2px solid #000;
    position: relative;
    overflow: hidden;
}

/* Top Section: QR + Product Info */
.layout3-top-section {
    display: flex;
    gap: 2mm;
    padding: 1mm;
    height: 16mm;
    border-bottom: 1px solid #000;
}

/* QR Container - 16mm */
.layout3-qr-container {
    width: 16mm;
    height: 16mm;
    min-width: 16mm;
    min-height: 16mm;
    max-width: 16mm;
    max-height: 16mm;
    border: 1px solid #000;
    background: white;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.layout3-qr-container svg {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    display: block !important;
    shape-rendering: crispEdges;
    image-rendering: pixelated;
}

/* Product Info */
.layout3-product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1mm;
    min-width: 0;
    padding: 0.5mm;
}

.layout3-product-name {
    font-size: 9px;
    font-weight: 700;
    line-height: 1.1;
    color: #000;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    word-break: break-word;
}

.layout3-price {
    font-size: 11px;
    font-weight: 800;
    color: #000;
    margin-top: 1mm;
}

.layout3-ean-text {
    font-size: 7px;
    font-weight: 600;
    color: #000;
    margin-top: auto;
    font-family: 'Courier New', monospace;
}

/* Bottom: Barcode CENTRATO */
.layout3-bottom-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 2mm;
}

.layout3-barcode-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.layout3-barcode {
    font-family: 'IDAutomationHC39M', 'Libre Barcode 39', monospace;
    font-size: 18px !important;
    line-height: 1 !important;
    letter-spacing: 0 !important;
    color: #000000;
    transform: scaleY(1.3);
    text-align: center;
    white-space: nowrap;
    padding: 0;
    margin: 0;
}

/* Print Optimizations */
@media print {
    .thermal-label-layout3 {
        page-break-inside: avoid;
        background: white !important;
        border: 1px solid #000 !important;
    }

    .thermal-label-layout3 * {
        color: #000 !important;
    }

    .layout3-qr-container svg {
        shape-rendering: crispEdges !important;
        image-rendering: pixelated !important;
    }

    .layout3-barcode {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
