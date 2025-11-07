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

            /* .print-only visibility is controlled by JavaScript */

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
            background: white;  /* White background for QR contrast */
        }

        .thermal-qr-container svg {
            width: 48px !important;
            height: 48px !important;
            display: block;  /* Remove inline spacing */
        }

        /* QR Code SVG optimization for thermal printing */
        .thermal-qr-container svg {
            shape-rendering: crispEdges !important;  /* Sharp edges, no anti-aliasing */
        }

        @media print {
            .thermal-qr-container {
                background: white !important;
                border: none !important;
            }

            .thermal-qr-container svg {
                image-rendering: pixelated !important;  /* Prevent smoothing on print */
                shape-rendering: crispEdges !important;
            }

            /* Force crisp rendering without changing colors */
            .thermal-qr-container svg * {
                shape-rendering: crispEdges !important;
            }
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

        /* Barcode styling - OTTIMIZZATO PER STAMPANTE TERMICA */
        .thermal-barcode-container {
            height: 35px; /* Ridotto per evitare overflow su etichetta 50mm */


            overflow: hidden; /* Permetti espansione verticale */
            background: white;
            padding: 0 2mm; /* Quiet zones laterali per scansione */
        }

        .thermal-barcode-container .barcode {
            font-family: 'IDAutomationHC39M', 'Courier New', monospace;
            font-size: 11px; /* Bilanciato: leggibile ma compatto */
            letter-spacing: 0; /* Nessuna spaziatura - font barcode la gestisce */
            line-height: 1.2; /* Migliore definizione verticale */
            height: 20px;
            overflow: visible;
            text-align: left;
            font-weight: 900; /* Extra-bold per stampante termica */
            color: #000000; /* Nero puro per massimo contrasto */
            transform: scaleY(1.1); /* Allunga verticalmente senza allargare */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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

        <h3>🔍 Scegli il Layout Migliore - Confronta e Stampa</h3>

        <!-- Layout 1: Originale (QR piccolo + Barcode) -->
        <div style="margin-bottom: 40px;">
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h4 style="color: #495057; margin-bottom: 8px;">📐 Layout 1: Originale - QR 13mm + Barcode</h4>
                <p style="color: #6c757d; margin: 0; font-size: 13px;">QR Code 13mm | Barcode Code39 11px | Include EAN + Cliente</p>
                <button onclick="printLayout('layout1')" class="btn" style="background: #28a745; color: white; margin-top: 10px;">
                    🖨️ Stampa Solo Layout 1
                </button>
            </div>
            <div class="labels-grid" id="layout1">
                @for ($i = 1; $i <= min($labelData['quantity'], 4); $i++)
                    @include('admin.products.partials.thermal-label', ['labelData' => $labelData, 'orderItem' => $orderItem])
                @endfor
            </div>
        </div>

        <!-- Layout 2: QR Grande (solo QR + Nome + Prezzo) -->
        <div style="margin-bottom: 40px;">
            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h4 style="color: #1976d2; margin-bottom: 8px;">📐 Layout 2: QR Grande - Minimalista</h4>
                <p style="color: #0d47a1; margin: 0; font-size: 13px;">QR Code 20mm (grande) | Solo Nome Prodotto + Prezzo | Nessun barcode</p>
                <button onclick="printLayout('layout2')" class="btn" style="background: #1976d2; color: white; margin-top: 10px;">
                    🖨️ Stampa Solo Layout 2
                </button>
            </div>
            <div class="labels-grid" id="layout2">
                @for ($i = 1; $i <= min($labelData['quantity'], 4); $i++)
                    @include('admin.products.partials.thermal-label-layout2', ['labelData' => $labelData, 'orderItem' => $orderItem])
                @endfor
            </div>
        </div>

        <!-- Layout 3: Bilanciato (QR 15mm + Barcode ottimizzato) -->
        <div style="margin-bottom: 40px;">
            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h4 style="color: #e65100; margin-bottom: 8px;">📐 Layout 3: Bilanciato - QR 15mm + Barcode Leggibile</h4>
                <p style="color: #bf360c; margin: 0; font-size: 13px;">QR Code 15mm | Barcode 16px (più grande) | Solo EAN sotto</p>
                <button onclick="printLayout('layout3')" class="btn" style="background: #ff9800; color: white; margin-top: 10px;">
                    🖨️ Stampa Solo Layout 3
                </button>
            </div>
            <div class="labels-grid" id="layout3">
                @for ($i = 1; $i <= min($labelData['quantity'], 4); $i++)
                    @include('admin.products.partials.thermal-label-layout3', ['labelData' => $labelData, 'orderItem' => $orderItem])
                @endfor
            </div>
        </div>

        <!-- Layout 4: Barcode Dominante (QR piccolo + Barcode EXTRA grande) -->
        <div style="margin-bottom: 40px;">
            <div style="background: #f3e5f5; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h4 style="color: #6a1b9a; margin-bottom: 8px;">📐 Layout 4: Barcode Dominante - Massima Scansionabilità</h4>
                <p style="color: #4a148c; margin: 0; font-size: 13px;">QR Code 12mm | Barcode 20px GRANDE (70% etichetta) | Solo nome breve</p>
                <button onclick="printLayout('layout4')" class="btn" style="background: #9c27b0; color: white; margin-top: 10px;">
                    🖨️ Stampa Solo Layout 4
                </button>
            </div>
            <div class="labels-grid" id="layout4">
                @for ($i = 1; $i <= min($labelData['quantity'], 4); $i++)
                    @include('admin.products.partials.thermal-label-layout4', ['labelData' => $labelData, 'orderItem' => $orderItem])
                @endfor
            </div>
        </div>

        <div style="background: #fffde7; padding: 15px; border-radius: 8px; border-left: 4px solid #f57f17;">
            <h4 style="color: #f57f17; margin-bottom: 8px;">💡 Suggerimenti per la Scelta</h4>
            <ul style="margin: 8px 0 0 20px; line-height: 1.8; color: #827717;">
                <li><strong>Layout 1:</strong> Buono per chi usa sia QR che barcode scanner classici</li>
                <li><strong>Layout 2:</strong> Ideale se usi principalmente smartphone per QR, massima leggibilità QR</li>
                <li><strong>Layout 3:</strong> Compromesso perfetto tra QR e barcode, entrambi leggibili</li>
                <li><strong>Layout 4:</strong> Ottimo per scanner barcode professionali, barcode gigante</li>
            </ul>
        </div>
    </div>

    <!-- Print Versions per ogni layout -->
    <div class="print-only" id="print-layout1" style="display: none;">
        @for ($i = 1; $i <= $labelData['quantity']; $i++)
            @include('admin.products.partials.thermal-label', ['labelData' => $labelData, 'orderItem' => $orderItem])
        @endfor
    </div>

    <div class="print-only" id="print-layout2" style="display: none;">
        @for ($i = 1; $i <= $labelData['quantity']; $i++)
            @include('admin.products.partials.thermal-label-layout2', ['labelData' => $labelData, 'orderItem' => $orderItem])
        @endfor
    </div>

    <div class="print-only" id="print-layout3" style="display: none;">
        @for ($i = 1; $i <= $labelData['quantity']; $i++)
            @include('admin.products.partials.thermal-label-layout3', ['labelData' => $labelData, 'orderItem' => $orderItem])
        @endfor
    </div>

    <div class="print-only" id="print-layout4" style="display: none;">
        @for ($i = 1; $i <= $labelData['quantity']; $i++)
            @include('admin.products.partials.thermal-label-layout4', ['labelData' => $labelData, 'orderItem' => $orderItem])
        @endfor
    </div>

    <script>
        let currentLayout = 'all';

        function printLayout(layoutId) {
            currentLayout = layoutId;
            window.print();
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🏷️ Thermal printing system ready - 4 layouts available');
            console.log('📊 Labels configured: {{ $labelData['quantity'] }}');
            console.log('📐 Format: 50mm x 25mm');

            // Keyboard shortcut for print
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });

        // Show correct print version based on selected layout
        window.addEventListener('beforeprint', function() {
            console.log('🖨️ Starting thermal print job - Layout:', currentLayout);

            // Hide all print layouts first
            document.querySelectorAll('.print-only').forEach(el => el.style.display = 'none');

            // Show selected layout
            if (currentLayout !== 'all') {
                document.getElementById('print-' + currentLayout).style.display = 'block';
            } else {
                // Print all layouts (default button)
                document.querySelectorAll('.print-only').forEach(el => el.style.display = 'block');
            }
        });

        // Hide print version after printing
        window.addEventListener('afterprint', function() {
            console.log('✅ Print job sent to printer');
            document.querySelectorAll('.print-only').forEach(el => el.style.display = 'none');
            currentLayout = 'all'; // Reset
        });
    </script>
</body>
</html>
