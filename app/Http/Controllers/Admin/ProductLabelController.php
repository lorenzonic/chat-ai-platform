<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductLabelController extends Controller
{
    /**
     * Display a listing of order items for label printing
     */
    public function index(Request $request): View
    {
        $query = OrderItem::with(['order', 'store', 'grower', 'product']);

        // Filter by order ID
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by grower
        if ($request->filled('grower_id')) {
            $query->where('grower_id', $request->grower_id);
        }

        // Search by product name (in product_snapshot)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $search = $request->search;
                $q->whereJsonContains('product_snapshot->name', $search)
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->withQueryString();

        // Get options for filters
        $stores = \App\Models\Store::orderBy('name')->get();
        $growers = \App\Models\Grower::orderBy('name')->get();

        // Get selected order info if filtering by order
        $selectedOrder = null;
        if ($request->filled('order_id')) {
            $selectedOrder = \App\Models\Order::with('store')->find($request->order_id);
        }

        return view('admin.products.index', compact('orderItems', 'stores', 'growers', 'selectedOrder'));
    }

    /**
     * Show the order item with label for printing
     */
    public function show(OrderItem $orderItem): View
    {
        // Load related data
        $orderItem->load(['order', 'store', 'grower', 'product']);

        // Generate label data
        $labelData = $this->prepareLabelData($orderItem);

        // Add quantity for multiple labels
        $labelData['quantity'] = $orderItem->quantity ?? 1;

        return view('admin.products.show', compact('orderItem', 'labelData'));
    }

    /**
     * Show the form for editing the order item data
     */
    public function edit(OrderItem $orderItem): View
    {
        // Load related data
        $orderItem->load(['order', 'store', 'grower', 'product']);

        // Generate label data
        $labelData = $this->prepareLabelData($orderItem);

        // Add quantity for multiple labels
        $labelData['quantity'] = $orderItem->quantity ?? 1;

        return view('admin.products.edit', compact('orderItem', 'labelData'));
    }

    /**
     * Update the order item data
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'ean' => 'nullable|string|max:50',
            'prezzo_rivendita' => 'required|numeric|min:0',
            'vaso' => 'nullable|integer|min:0',
            'link' => 'nullable|url|max:500',
            'product_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Update the order item
        $orderItem->update([
            'ean' => $request->ean,
            'prezzo_rivendita' => $request->prezzo_rivendita,
            'notes' => $request->notes
        ]);

        // Update product snapshot if exists
        $snapshot = $orderItem->product_snapshot ?? [];
        $snapshot['ean'] = $request->ean;
        $snapshot['name'] = $request->product_name;
        $snapshot['vaso'] = $request->vaso;
        $snapshot['link'] = $request->link;

        $orderItem->update(['product_snapshot' => $snapshot]);

        // Update the related QR code if it exists and link is provided
        if ($request->link) {
            $qrCode = QrCode::where('order_id', $orderItem->order_id)
                           ->where('store_id', $orderItem->store_id)
                           ->where('product_id', $orderItem->product_id)
                           ->first();

            if ($qrCode) {
                $qrCode->update(['ean_code' => $request->ean]);
            }
        }

        return redirect()
            ->route('admin.products.show', $orderItem)
            ->with('success', 'Dati prodotto aggiornati con successo!');
    }

    /**
     * Show multiple labels for thermal printing (Godex G500 optimized)
     */
    public function thermalPrint(OrderItem $orderItem): View
    {
        // Load related data
        $orderItem->load(['order', 'store', 'grower', 'product']);

        // Generate label data
        $labelData = $this->prepareLabelData($orderItem);
        $labelData['quantity'] = $orderItem->quantity ?? 1;

        // Check if should print (conditional logic)
        $shouldPrint = $orderItem->quantity > 1;
        $printWarning = null;

        if (!$shouldPrint) {
            $printWarning = [
                'type' => 'single_item',
                'message' => 'Questo order item ha quantità = 1. Per efficienza e risparmio, normalmente si stampano etichette solo quando quantità > 1.',
                'suggestion' => 'Puoi comunque procedere con la stampa se necessario.'
            ];
        }

        return view('admin.products.thermal-print', compact('orderItem', 'labelData', 'shouldPrint', 'printWarning'));
    }

    /**
     * Prepare all data needed for the label
     */
    private function prepareLabelData(OrderItem $orderItem): array
    {
        // Get or create QR code for the order item
        $qrCode = $this->getOrderItemQrCode($orderItem);

        // Get product data from snapshot or relationship
        $product = $orderItem->product;
        $productName = $orderItem->product_snapshot['name'] ?? ($product ? $product->name : 'N/A');
        $productVariety = $orderItem->product_snapshot['variety'] ?? ($product ? $product->variety : null);
        $productEan = $orderItem->product_snapshot['ean'] ?? ($product ? $product->ean : null);

        // Generate barcode for EAN
        $barcode = null;
        $barcodeGenerator = new BarcodeGeneratorHTML();

        if ($productEan) {
            try {
                $barcode = [
                    'html' => $barcodeGenerator->getBarcode($productEan, 'EAN13'),
                    'code' => $productEan
                ];
            } catch (\Exception $e) {
                // Fallback if EAN is invalid
                $barcode = [
                    'html' => '<div class="text-xs">Invalid EAN</div>',
                    'code' => $productEan
                ];
            }
        }

        // Use OrderItem data (contains complete order information)
        $price = $orderItem->prezzo_rivendita ?? 'N/A';
        $orderNumber = $orderItem->order->order_number ?? 'N/A';
        $customer = $orderItem->order->store->name ?? ($orderItem->store->name ?? 'N/A');
        $deliveryDate = $orderItem->order->delivery_date ? date('d/m/Y', strtotime($orderItem->order->delivery_date)) : 'N/A';

        return [
            // Basic product info
            'name' => $productName,
            'variety' => $productVariety,
            'price' => $price,
            'store_name' => $orderItem->store->name ?? 'N/A',

            // QR Code and Barcode
            'qrcode' => [
                'svg' => $qrCode ? $this->generateQrCodeSvg($qrCode->getQrUrl()) : null,
                'url' => $qrCode ? $qrCode->getQrUrl() : null
            ],
            'barcode' => $barcode,
            'formatted_price' => '€ ' . number_format((float) $price, 2, ',', '.'),

            // Order information
            'order_info' => [
                'number' => $orderNumber,
                'customer' => $customer,
                'customer_short' => $this->shortenCustomerName($customer),
                'delivery_date' => $deliveryDate
            ]
        ];
    }

    /**
     * Get or create QR code for an order item
     */
    private function getOrderItemQrCode(OrderItem $orderItem): ?QrCode
    {
        if (!$orderItem) {
            return null;
        }

        // Look for existing QR code for this order item
        $qrCode = QrCode::where('order_id', $orderItem->order_id)
                       ->where('store_id', $orderItem->store_id)
                       ->where('product_id', $orderItem->product_id)
                       ->first();

        if (!$qrCode) {
            $productName = $orderItem->product_snapshot['name'] ?? ($orderItem->product ? $orderItem->product->name : 'Product');
            $productEan = $orderItem->product_snapshot['ean'] ?? ($orderItem->product ? $orderItem->product->ean : null);

            // Generate unique ref_code for the order item
            $baseRefCode = 'OI-' . strtoupper(substr(md5($productName . $orderItem->id), 0, 8));
            $refCode = $baseRefCode;
            $counter = 1;

            // Ensure ref_code is unique
            while (QrCode::where('ref_code', $refCode)->exists()) {
                $refCode = $baseRefCode . '-' . $counter;
                $counter++;
            }

            // Generate name: "nome prodotto - codice cliente"
            $clientCode = $orderItem->order->client ?? 'N/A';
            $qrName = $productName . ' - ' . $clientCode;

            // Generate question: "come si cura [nome del prodotto]"
            $question = "Come si cura " . $productName . "?";

            // Create new QR code for the order item
            $qrCode = QrCode::create([
                'store_id' => $orderItem->store_id,
                'order_id' => $orderItem->order_id,
                'product_id' => $orderItem->product_id,
                'name' => $qrName,
                'question' => $question,
                'ref_code' => $refCode,
                'is_active' => true,
                'ean_code' => $productEan,
            ]);

            // Generate QR code image
            $this->generateQrCodeImage($qrCode);
        }

        return $qrCode;
    }

    /**
     * Generate QR code image for the given QR code
     */
    private function generateQrCodeImage(QrCode $qrCode): void
    {
        try {
            $qrCodeContent = QrCodeGenerator::size(300)
                                           ->format('svg')
                                           ->style('round')
                                           ->generate($qrCode->getQrUrl());

            $filename = 'qr-codes/' . $qrCode->ref_code . '.svg';
            Storage::disk('public')->put($filename, $qrCodeContent);

            $qrCode->update(['qr_code_image' => $filename]);
        } catch (\Exception $e) {
            // Log error but don't fail
            Log::error('Failed to generate QR code image', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR code SVG
     */
    private function generateQrCodeSvg(string $url): string
    {
        return QrCodeGenerator::size(100)
                             ->style('round')
                             ->generate($url);
    }

    /**
     * Shorten customer name for label display
     */
    private function shortenCustomerName(string $customer): string
    {
        if (strlen($customer) <= 15) {
            return $customer;
        }

        // Try to extract first word and first letter of subsequent words
        $words = explode(' ', $customer);
        if (count($words) > 1) {
            $result = $words[0];
            for ($i = 1; $i < count($words); $i++) {
                $result .= ' ' . substr($words[$i], 0, 1) . '.';
                if (strlen($result) > 12) break;
            }
            return $result;
        }

        // If single word, truncate with ellipsis
        return substr($customer, 0, 12) . '...';
    }
}
