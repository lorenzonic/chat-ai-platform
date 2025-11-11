<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stampa Bulk Etichette Termiche</title>
    <style>
        /* Universal Thermal Printing CSS - Optimized for 50mm x 25mm labels */
        @page {
            size: 50mm 25mm;
            margin: 0mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        /* Screen preview container (hidden on print) */
        .screen-only {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .preview-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .preview-header h1 {
            font-size: 24px;
            color: #111827;
            margin-bottom: 8px;
        }

        .preview-stats {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-badge.blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .stat-badge.green {
            background: #d1fae5;
            color: #065f46;
        }

        .print-actions {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .preview-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            background: #fafafa;
        }

        .preview-card-header {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .preview-card-content {
            font-size: 12px;
            color: #111827;
        }

        /* Thermal label styling */
        .thermal-label {
            width: 50mm;
            height: 25mm;
            padding: 2mm;
            background: white;
            position: relative;
            page-break-after: always;
            page-break-inside: avoid;
            display: none; /* Hidden in screen view */
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        /* Print-specific styles */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .screen-only {
                display: none !important;
            }

            .thermal-label {
                display: block !important;
            }

            /* Ensure color accuracy */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
        }

        /* Barcode font */
        @font-face {
            font-family: 'IDAutomationHC39M';
            src: url('/fonts/IDAutomationHC39M.ttf') format('truetype');
        }
    </style>
</head>
<body>
    <!-- Screen Preview (visible only on screen) -->
    <div class="screen-only">
        <!-- Header -->
        <div class="preview-header">
            <h1>🖨️ Stampa Bulk Etichette Termiche</h1>
            <p style="color: #6b7280; margin-top: 4px;">Formato: 50mm x 25mm - Stampante termica universale</p>
            <div class="preview-stats">
                <span class="stat-badge blue">📦 {{ $orderItems->count() }} Order Items</span>
                <span class="stat-badge green">🏷️ {{ collect($bulkLabels)->sum('quantity') }} Etichette Totali</span>
            </div>
        </div>

        <!-- Print Actions -->
        <div class="print-actions">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 16px; color: #111827; margin-bottom: 4px;">⚙️ Stampa etichette filtrate</h2>
                    <p style="font-size: 13px; color: #6b7280;">Verranno stampate {{ collect($bulkLabels)->sum('quantity') }} etichette in formato termico</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button onclick="window.print()" class="btn btn-primary">
                        🖨️ Avvia Stampa
                    </button>
                    <a href="{{ route('admin.products.index', request()->query()) }}" class="btn btn-secondary">
                        ← Torna alla Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Preview Grid -->
        @if(count($bulkLabels) > 0)
            <div class="preview-grid">
                @foreach($bulkLabels as $index => $labelData)
                    <div class="preview-card">
                        <div class="preview-card-header">
                            <span>#{{ $index + 1 }}</span>
                            <span style="color: #2563eb; font-weight: 600;">Qty: {{ $labelData['quantity'] ?? 1 }}</span>
                        </div>
                        <div class="preview-card-content">
                            <div style="font-weight: 600; margin-bottom: 4px;">
                                {{ Str::limit($labelData['name'] ?? 'N/A', 25) }}
                            </div>
                            <div style="font-size: 11px; color: #6b7280;">
                                {{ $labelData['store_name'] ?? 'N/A' }}
                            </div>
                            <div style="font-size: 11px; color: #059669; margin-top: 4px;">
                                €{{ number_format((float) ($labelData['price'] ?? 0), 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🏷️</div>
                <h3 style="font-size: 18px; color: #111827; margin-bottom: 8px;">Nessuna etichetta da stampare</h3>
                <p style="color: #6b7280; margin-bottom: 24px;">Non ci sono etichette disponibili per la stampa bulk con i filtri attuali.</p>
                <a href="{{ route('admin.products.index', request()->query()) }}" class="btn btn-primary">
                    ← Torna ai Filtri
                </a>
            </div>
        @endif
    </div>

    <!-- Thermal Labels for Print (hidden on screen, visible on print) -->
    @if(count($bulkLabels) > 0)
        @foreach($bulkLabels as $labelData)
            @php
                $quantity = $labelData['quantity'] ?? 1;
            @endphp
            @for($i = 0; $i < $quantity; $i++)
                <div class="thermal-label">
                    @include('admin.products.partials.thermal-label', ['labelData' => $labelData])
                </div>
            @endfor
        @endforeach
    @endif

    <script>
        console.log('Bulk thermal print ready - {{ count($bulkLabels) }} prodotti, {{ collect($bulkLabels)->sum("quantity") }} etichette totali');

        window.addEventListener('beforeprint', function() {
            console.log('Avvio stampa termica bulk...');
        });

        window.addEventListener('afterprint', function() {
            console.log('Stampa completata!');
        });
    </script>
</body>
</html>
