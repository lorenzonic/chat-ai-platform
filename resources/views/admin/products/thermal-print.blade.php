<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stampa Etichette Termiche - {{ $labelData['name'] }}</title>
    <style>
        /* Import IDAutomation barcode font */
        @font-face {
            font-family: 'IDAutomationHC39M';
            src: url('{{ asset('fonts/IDAutomationHC39M.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        /* R        <div class="no-print" style="margin-bottom: 30px;">
            <h1>🏷️ Stampa Etichette Termiche - Godex G500</h1>
            <p><strong>Prodotto:</strong> {{ $labelData['name'] }}</p>
            <p><strong>Ordine:</strong> {{ $labelData['order_info']['number'] }} - {{ $labelData['order_info']['customer'] }}</p>

            @if(isset($printWarning))
                <div class="alert-warning" style="margin: 20px 0; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
                    <h4 style="color: #856404; margin: 0 0 10px 0;">⚠️ Attenzione - Stampa per singolo pezzo</h4>
                    <p style="color: #856404; margin: 5px 0;"><strong>{{ $printWarning['message'] }}</strong></p>
                    <p style="color: #856404; margin: 5px 0; font-style: italic;">{{ $printWarning['suggestion'] }}</p>

                    <div style="margin-top: 15px;">
                        <label style="color: #856404;">
                            <input type="checkbox" id="force-print" style="margin-right: 8px;">
                            Confermo di voler stampare comunque l'etichetta per questo singolo pezzo
                        </label>
                    </div>
                </div>
            @endif

            <div class="quantity-info">
                <strong>📦 Quantità: {{ $labelData['quantity'] }} pezzi</strong>
                @if($shouldPrint)
                    <span style="color: green;">→ ✅ Verranno stampate {{ $labelData['quantity'] }} etichette (consigliato)</span>
                @else
                    <span style="color: orange;">→ ⚠️ Stampa singola etichetta (solo se necessario)</span>
                @endif
            </div>

            <div class="print-controls">
                @if($shouldPrint)
                    <button onclick="window.print()" class="btn btn-primary">🖨️ Stampa {{ $labelData['quantity'] }} Etichette</button>
                @else
                    <button onclick="checkAndPrint()" class="btn btn-warning" id="print-btn" disabled>
                        🖨️ Stampa 1 Etichetta (Conferma Richiesto)
                    </button>
                @endif
                <a href="{{ route('admin.products.show', $orderItem) }}" class="btn btn-secondary">← Anteprima Standard</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">📋 Lista Prodotti</a>
            </div>
        </div>yles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            line-height: 1;
        }

        /* Print styles - Ottimizzato per Godex G500 */
        @media print {
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .thermal-label {
                width: 50mm;
                height: 25mm;
                border: none !important;
                margin: 0 0 0 3mm !important; /* Added 3mm left margin */
                padding: 1mm !important;
                background: white !important;
                overflow: hidden;
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Force black text for thermal printing */
            .thermal-label * {
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Barcode optimization for thermal */
            .thermal-barcode .barcode div {
                background-color: black !important;
            }
        }

        /* Screen preview styles */
        .preview-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .preview-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .labels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Thermal label design - SAME AS ORIGINAL */
        .thermal-label {
            width: 189px;  /* 50mm = ~189px (5cm) */
            height: 94px;  /* 25mm = ~94px (2.5cm) */
            border: 2px solid #333;
            background: white;
            margin: 10px;
            padding: 3px;
            font-family: Arial, sans-serif;
            display: inline-block;
            vertical-align: top;
            position: relative;
            box-sizing: border-box;
        }

        /* Top section - QR + Product Info */
        .thermal-top-section {
            height: 55px;
            display: flex;
            margin-bottom: 3px;
        }

        /* QR Code - Top left */
        .thermal-qr-container {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ddd;
            margin-right: 6px;
        }

        .thermal-qr-container svg {
            width: 48px !important;
            height: 48px !important;
        }

        /* Product info - Top right */
        .thermal-product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2px;
        }

        .thermal-product-name {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.1;
            max-height: 32px;
            overflow: hidden;
            text-align: left;
            margin-bottom: 4px;
        }

        .thermal-price {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            text-align: left;
            margin: 4px 0;
        }

        /* Bottom section - Barcode + EAN + Client */
        .thermal-bottom-section {
            height: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Barcode styling */
        .thermal-barcode-container {
            height: 20px;
            margin-bottom: 2px;
            overflow: hidden;
        }

        .thermal-barcode-container .barcode {
            font-family: 'IDAutomationHC39M', monospace;
            font-size: 24px;
            letter-spacing: 0;
            line-height: 1;
            height: 20px;
            overflow: hidden;
            text-align: left;
        }

        /* Bottom info line - EAN left, Client right */
        .thermal-bottom-info {
            display: flex;
            justify-content: space-between;
            font-size: 6px;
            color: black;
            line-height: 1.1;
            gap: 4px;
        }

        .thermal-ean-text {
            font-size: 10px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .thermal-client-code {
            font-weight: bold;
            text-align: right;
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 80px;
        }

        /* Controls */
        .print-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .quantity-info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Preview Container -->
    <div class="preview-container no-print">
        <div class="preview-header">
            <h1>🏷️ Stampa Etichette Termiche - Godex G500</h1>
            <p><strong>Prodotto:</strong> {{ $labelData['name'] }}</p>
            <p><strong>Ordine:</strong> {{ $labelData['order_info']['number'] }} - {{ $labelData['order_info']['customer'] }}</p>

            <div class="quantity-info">
                <strong>📦 Quantità: {{ $labelData['quantity'] }} pezzi</strong>
                <span>→ Verranno stampate {{ $labelData['quantity'] }} etichette</span>
            </div>

            <div class="print-controls">
                <button onclick="window.print()" class="btn btn-primary">🖨️ Stampa {{ $labelData['quantity'] }} Etichette</button>
                <a href="{{ route('admin.products.show', $orderItem) }}" class="btn btn-secondary">← Anteprima Standard</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">📋 Lista Prodotti</a>
            </div>
        </div>

        <h3>🔍 Anteprima Etichette ({{ $labelData['quantity'] }} pz)</h3>
        <div class="labels-grid">
            @for ($i = 1; $i <= min($labelData['quantity'], 8); $i++)
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
            @endfor

            @if($labelData['quantity'] > 8)
                <div style="grid-column: 1/-1; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 4px;">
                    <p><strong>... e altre {{ $labelData['quantity'] - 8 }} etichette</strong></p>
                    <p class="text-muted">L'anteprima mostra solo le prime 8 etichette. Tutte le {{ $labelData['quantity'] }} etichette verranno stampate.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Print Version - All Labels in continuous flow -->
    <div class="print-only" style="display: none;">

        @for ($i = 1; $i <= $labelData['quantity']; $i++)
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
                        <div class="thermal-price">
                            {{ $labelData['formatted_price'] }}
                        </div>
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
        @endfor
    </div>

    <script>
        // Auto-setup for thermal printing
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🏷️ Thermal label printing ready');
            console.log('📊 Labels to print: {{ $labelData['quantity'] }}');

            @if(!$shouldPrint)
                // Enable print button when checkbox is checked for single items
                const checkbox = document.getElementById('force-print');
                const printBtn = document.getElementById('print-btn');

                if (checkbox && printBtn) {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            printBtn.disabled = false;
                            printBtn.className = 'btn btn-primary';
                            printBtn.innerHTML = '🖨️ Stampa 1 Etichetta (Confermato)';
                        } else {
                            printBtn.disabled = true;
                            printBtn.className = 'btn btn-warning';
                            printBtn.innerHTML = '🖨️ Stampa 1 Etichetta (Conferma Richiesto)';
                        }
                    });
                }
            @endif

            // Setup keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    @if($shouldPrint)
                        window.print();
                    @else
                        checkAndPrint();
                    @endif
                }
            });
        });

        @if(!$shouldPrint)
        // Function to check confirmation before printing single items
        function checkAndPrint() {
            const checkbox = document.getElementById('force-print');
            if (checkbox && checkbox.checked) {
                console.log('🖨️ User confirmed single item printing');
                window.print();
            } else {
                alert('⚠️ Per stampare un singolo pezzo, devi confermare spuntando la casella sopra.');
            }
        }
        @endif

        // Print optimization
        window.addEventListener('beforeprint', function() {
            console.log('🖨️ Starting thermal print job for {{ $labelData['quantity'] }} labels');
            @if(!$shouldPrint)
                console.log('⚠️ Warning: Single item print (quantity = 1)');
            @endif
            // Show print version
            document.querySelector('.print-only').style.display = 'block';
        });

        window.addEventListener('afterprint', function() {
            console.log('✅ Print job completed');
            // Hide print version
            document.querySelector('.print-only').style.display = 'none';
        });
    </script>
</body>
</html>
