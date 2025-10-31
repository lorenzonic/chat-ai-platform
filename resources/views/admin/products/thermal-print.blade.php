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

        /* ==========================================
           UNIVERSAL THERMAL PRINTER CONFIGURATION
           Compatible: Godex, Zebra, Dymo, Brother, TSC, etc.
           ========================================== */
        @page {
            size: 50mm 25mm;           /* Standard label size */
            margin: 0mm;               /* Zero margins */
        }

        @media print {
            html, body {
                width: 50mm !important;
                height: 25mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                overflow: hidden !important;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .thermal-label {
                width: 50mm !important;
                height: 25mm !important;
                border: none !important;
                margin: 0 !important;
                padding: 2mm !important;
                background: white !important;
                overflow: hidden !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
            }

            .thermal-label:last-child {
                page-break-after: auto !important;
            }

            /* Force black colors for all thermal printers */
            .thermal-label * {
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .barcode {
                font-family: 'IDAutomationHC39M', 'Courier New', monospace !important;
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
            font-size: 11px;
            font-weight: bold;
            line-height: 1.2;
            max-height: 35px;
            overflow: hidden;
            text-align: left;
            margin-bottom: 3px;
            word-wrap: break-word;
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
            height: 18px;
            margin-bottom: 2px;
            overflow: hidden;
        }

        .thermal-barcode-container .barcode {
            font-family: 'IDAutomationHC39M', 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 0;
            line-height: 1;
            height: 18px;
            overflow: hidden;
            text-align: center;
            font-weight: normal;
        }

        /* Bottom info line - EAN left, Client right */
        .thermal-bottom-info {
            display: flex;
            justify-content: space-between;
            font-size: 7px;
            color: black;
            line-height: 1.1;
            gap: 4px;
        }

        .thermal-ean-text {
            font-size: 9px;
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
            <h1>🏷️ Stampa Etichette Termiche</h1>
            <p><strong>Prodotto:</strong> {{ $labelData['name'] }}</p>
            <p><strong>Ordine:</strong> {{ $labelData['order_info']['number'] }} - {{ $labelData['order_info']['customer'] }}</p>

            <div class="quantity-info">
                <strong>📦 Quantità: {{ $labelData['quantity'] }} pezzi</strong>
                <span>→ Verranno stampate {{ $labelData['quantity'] }} etichette</span>
            </div>

            <div style="background: #e3f2fd; padding: 12px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
                <strong>📋 Configurazione stampante:</strong>
                <ul style="margin: 8px 0 0 20px; line-height: 1.6;">
                    <li><strong>Formato etichetta:</strong> 50mm x 25mm</li>
                    <li><strong>Margini:</strong> 0mm (tutti i lati)</li>
                    <li><strong>Scala:</strong> 100% (non ridimensionare)</li>
                    <li><strong>Compatibile:</strong> Godex, Zebra, Dymo, Brother, TSC</li>
                </ul>
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
                @include('admin.products.partials.thermal-label', ['labelData' => $labelData, 'orderItem' => $orderItem])
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
            @include('admin.products.partials.thermal-label', ['labelData' => $labelData, 'orderItem' => $orderItem])
        @endfor
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🏷️ Thermal printing system ready');
            console.log('📊 Labels configured: {{ $labelData['quantity'] }}');
            console.log('� Format: 50mm x 25mm');

            // Keyboard shortcut for print
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });

        // Show print version when printing starts
        window.addEventListener('beforeprint', function() {
            console.log('🖨️ Starting thermal print job');
            document.querySelector('.print-only').style.display = 'block';
        });

        // Hide print version after printing
        window.addEventListener('afterprint', function() {
            console.log('✅ Print job sent to printer');
            document.querySelector('.print-only').style.display = 'none';
        });
    </script>
</body>
</html>
