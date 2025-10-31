<div class="thermal-label">
    <!-- Top section: QR + Product Info -->
    <div class="thermal-top-section">
        <!-- QR Code (Product-specific) -->
        <div class="thermal-qr-container">
            @if($labelData['qrcode']['svg'])
                {!! $labelData['qrcode']['svg'] !!}
            @else
                <div style="font-size: 6px; text-align: center;">QR<br>N/A</div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="thermal-product-info">
            <!-- Product Name -->
            <div class="thermal-product-name">
                {{ $labelData['name'] }}
            </div>

            <!-- Price -->
            @if($labelData['price'] != 'N/A' && (float)$labelData['price'] > 0)
            <div class="thermal-price">
                {{ $labelData['formatted_price'] }}
            </div>
            @endif
        </div>
    </div>

    <!-- Bottom section: Long Barcode + EAN/Client -->
    <div class="thermal-bottom-section">
        <!-- Long horizontal barcode -->
        @if($labelData['barcode'])
        <div class="thermal-barcode-container">
            <div class="barcode">
                *{{ $labelData['barcode']['code'] }}*
            </div>
        </div>
        @endif

        <!-- Bottom info: EAN left, Client right -->
        <div class="thermal-bottom-info">
            <div class="thermal-ean-text">
                {{ $orderItem->product_snapshot['ean'] ?? ($orderItem->product->ean ?? '') }}
            </div>
            <div class="thermal-client-code">
                {{ $labelData['order_info']['customer_short'] ?: 'N/A' }}
            </div>
        </div>
    </div>
</div>
