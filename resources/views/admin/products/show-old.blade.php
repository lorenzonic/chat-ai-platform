@extends('layouts.admin')

@section('title', 'Dettagli Prodotto')

@section('styles')
<style>
/* Label styling */
.label-container {
    width: 4cm;
    height: 3cm;
    font-family: Arial, sans-serif;
    position: relative;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.price-tag {
    display: inline-block;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 0.3px;
    color: black !important;
    background: white !important;
    border: 1px solid black !important;
}

/* QR Code styling */
.qr-code-container {
    overflow: hidden;
}

.qr-code-container svg {
    width: 100% !important;
    height: 100% !important;
    display: block;
}

/* Barcode styling */
.barcode-container {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.barcode-container svg {
    max-width: 100%;
    max-height: 100%;
}

.qr-code svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }

    #product-label, #product-label * {
        visibility: visible;
    }

    #product-label {
        position: absolute;
        left: 0;
        top: 0;
        width: 4cm;
        height: 3cm;
        box-shadow: none !important;
        border: 1px solid black !important;
        margin: 0 !important;
    }

    .price-tag {
        background: white !important;
        color: black !important;
        border: 1px solid black !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }    /* Hide print button in print view */
    button {
        display: none !important;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .label-container {
        width: 100%;
        max-width: 350px;
    }
}
</style>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.products.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Torna alla Lista
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Modifica
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Info -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Informazioni Prodotto</h3>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Nome</label>
                            <p class="text-gray-900">{{ $product->name }}</p>
                        </div>

                        @if($product->code)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Codice</label>
                            <p class="text-gray-900">{{ $product->code }}</p>
                        </div>
                        @endif

                        @if($product->ean)
                        <div>
                            <label class="text-sm font-medium text-gray-500">EAN</label>
                            <p class="text-gray-900">{{ $product->ean }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-500">Quantità</label>
                            <p class="text-gray-900">{{ $product->quantity }}</p>
                        </div>

                        @if($product->height)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Altezza</label>
                            <p class="text-gray-900">{{ $product->height }} cm</p>
                        </div>
                        @endif

                        @if($product->price)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Prezzo Vendita</label>
                            <p class="text-gray-900">€{{ number_format($product->price, 2) }}</p>
                        </div>
                        @endif

                        @if($product->category)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Categoria</label>
                            <p class="text-gray-900">{{ $product->category }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Supplier & Store Info -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Negozio e Fornitore</h3>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Negozio</label>
                            <p class="text-gray-900">{{ $product->store->name }}</p>
                        </div>

                        @if($product->grower)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fornitore</label>
                            <p class="text-gray-900">{{ $product->grower->name }}</p>
                            @if($product->grower->code)
                                <p class="text-sm text-gray-500">Codice: {{ $product->grower->code }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Details -->
                @if($product->client || $product->cc || $product->pia || $product->pro || $product->transport_cost || $product->delivery_date || $product->address || $product->phone)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dettagli Aggiuntivi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @if($product->client)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cliente</label>
                            <p class="text-gray-900">{{ $product->client }}</p>
                        </div>
                        @endif

                        @if($product->cc)
                        <div>
                            <label class="text-sm font-medium text-gray-500">CC</label>
                            <p class="text-gray-900">{{ $product->cc }}</p>
                        </div>
                        @endif

                        @if($product->pia)
                        <div>
                            <label class="text-sm font-medium text-gray-500">PIA</label>
                            <p class="text-gray-900">{{ $product->pia }}</p>
                        </div>
                        @endif

                        @if($product->pro)
                        <div>
                            <label class="text-sm font-medium text-gray-500">PRO</label>
                            <p class="text-gray-900">{{ $product->pro }}</p>
                        </div>
                        @endif

                        @if($product->transport_cost)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Costo Trasporto</label>
                            <p class="text-gray-900">€{{ number_format($product->transport_cost, 2) }}</p>
                        </div>
                        @endif

                        @if($product->delivery_date)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Data Consegna</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($product->delivery_date)->format('d/m/Y') }}</p>
                        </div>
                        @endif

                        @if($product->address)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Indirizzo</label>
                            <p class="text-gray-900">{{ $product->address }}</p>
                        </div>
                        @endif

                        @if($product->phone)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Telefono</label>
                            <p class="text-gray-900">{{ $product->phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($product->notes)
                <div class="mt-6">
                    <label class="text-sm font-medium text-gray-500">Note</label>
                    <p class="text-gray-900 mt-1">{{ $product->notes }}</p>
                </div>
                @endif

                <!-- Product Label Preview -->
                <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Anteprima Etichetta</h3>
                        <button onclick="printLabel()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            🖨️ Stampa Etichetta
                        </button>
                    </div>

                    <div id="product-label" class="label-container bg-white border border-gray-800 p-1">
                        <!-- Product Name - Top, very compact -->
                        <div class="text-center mb-1">
                            <h4 class="font-bold text-black uppercase leading-none" style="font-size: 8px; line-height: 1;">{{ $product->name }}</h4>
                        </div>

                        <!-- Main Content: QR Code and Barcode side by side -->
                        <div class="flex justify-between items-center mb-1">
                            <!-- QR Code - Left side, very small -->
                            <div class="flex flex-col items-center">
                                <div class="qr-code-container" style="width: 25px; height: 25px;">
                                    {!! $labelData['qrcode']['svg'] !!}
                                </div>
                            </div>

                            <!-- EAN Barcode - Right side, vertical and compact -->
                            <div class="flex flex-col items-center justify-center flex-1 ml-1">
                                <div class="barcode-container" style="transform: rotate(90deg); width: 35px; height: 20px;">
                                    {!! $labelData['barcode']['html'] !!}
                                </div>
                            </div>
                        </div>

                        <!-- Price - Bottom, very compact -->
                        <div class="text-center">
                            <span class="price-tag px-1" style="font-size: 8px; border: 1px solid black;">
                                {{ $labelData['formatted_price'] }}
                            </span>
                        </div>

                        <!-- EAN Code number - Bottom -->
                        <div class="text-center mt-1">
                            <p class="text-black" style="font-size: 6px; line-height: 1;">{{ $labelData['barcode']['code'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <strong>Creato:</strong> {{ $product->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <strong>Aggiornato:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printLabel() {
    // Create a new window for printing
    var printWindow = window.open('', '_blank');

    // Get the label content
    var labelContent = document.getElementById('product-label').outerHTML;

    // Create the print document
    var printDocument = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Etichetta - {{ $product->name }}</title>
            <style>
                @page {
                    margin: 0.5cm;
                    size: A4;
                }

                body {
                    margin: 0;
                    padding: 20px;
                    font-family: Arial, sans-serif;
                }

                .label-container {
                    width: 4cm;
                    height: 3cm;
                    border: 1px solid black;
                    padding: 2px;
                    background: white;
                    margin: 0 auto;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .price-tag {
                    background: white !important;
                    color: black !important;
                    border: 1px solid black !important;
                    display: inline-block;
                    font-size: 8px;
                    font-weight: bold;
                    letter-spacing: 0.3px;
                }

                .barcode-container {
                    height: 50px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .barcode-container svg {
                    max-width: 100%;
                    max-height: 100%;
                }

                .qr-code {
                    border: 1px solid #e5e5e5;
                    border-radius: 4px;
                }

                .text-center { text-align: center; }
                .text-lg { font-size: 1.125rem; }
                .text-base { font-size: 1rem; }
                .text-sm { font-size: 0.875rem; }
                .text-xs { font-size: 0.75rem; }
                .font-bold { font-weight: bold; }
                .font-semibold { font-weight: 600; }
                .text-gray-900 { color: #111827; }
                .text-gray-800 { color: #1f2937; }
                .text-gray-600 { color: #4b5563; }
                .text-gray-500 { color: #6b7280; }
                .mb-2 { margin-bottom: 0.5rem; }
                .mb-3 { margin-bottom: 0.75rem; }
                .mb-4 { margin-bottom: 1rem; }
                .pt-2 { padding-top: 0.5rem; }
                .border-t { border-top: 1px solid #e5e7eb; }
                .grid { display: grid; }
                .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .gap-4 { gap: 1rem; }
                .mx-auto { margin-left: auto; margin-right: auto; }

                /* SVG QR Code styles */
                .qr-code svg {
                    width: 100% !important;
                    height: 100% !important;
                    display: block;
                }
            </style>
        </head>
        <body>
            ${labelContent}
        </body>
        </html>
    `;

    // Write the document and print
    printWindow.document.write(printDocument);
    printWindow.document.close();

    // Wait for the content to load, then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}
</script>
@endsection
