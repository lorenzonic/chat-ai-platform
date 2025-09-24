<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
     * Display a listing of products for label printing
     */
    public function index(Request $request): View
    {
        $query = Product::with(['order', 'store', 'grower']);

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

        // Search by product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('created_at', 'desc')
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

        return view('admin.products.index', compact('products', 'stores', 'growers', 'selectedOrder'));
    }

    /**
     * Show the product with label for printing
     */
    public function show(Product $product): View
    {
        // Load related data
        $product->load(['order', 'store']);

        // Generate label data
        $labelData = $this->prepareLabelData($product);

        return view('admin.products.show', compact('product', 'labelData'));
    }

    /**
     * Prepare all data needed for the label
     */
    private function prepareLabelData(Product $product): array
    {
        // Get or create QR code for the product
        $qrCode = $this->getProductQrCode($product);

        // Try to get data from OrderItem if available (contains complete order data)
        $orderItem = \App\Models\OrderItem::where('product_id', $product->id)
                                          ->with(['order'])
                                          ->first();

        // Generate barcode for EAN
        $barcode = null;
        $barcodeGenerator = new BarcodeGeneratorHTML();

        if ($product->ean) {
            try {
                $barcode = [
                    'html' => $barcodeGenerator->getBarcode($product->ean, 'EAN13'),
                    'code' => $product->ean
                ];
            } catch (\Exception $e) {
                // Fallback if EAN is invalid
                $barcode = [
                    'html' => '<div class="text-xs">Invalid EAN</div>',
                    'code' => $product->ean
                ];
            }
        }

        // Determine price and order data source
        if ($orderItem) {
            // Use OrderItem data (contains complete order information)
            $price = $orderItem->prezzo_rivendita ?: $orderItem->unit_price ?: $product->price;
            $orderNumber = $orderItem->order->order_number ?? 'N/A';

            // For customer, try order client first, then fallback to store name
            $customer = $orderItem->order->client ?? $product->store->name ?? 'N/A';
            $deliveryDate = $orderItem->order->delivery_date ? date('d/m/Y', strtotime($orderItem->order->delivery_date)) : 'N/A';
        } else {
            // Fallback to Product data
            $price = $product->price;
            $orderNumber = $product->order->order_number ?? 'N/A';
            $customer = $product->client ?? $product->store->name ?? 'N/A';
            $deliveryDate = $product->delivery_date ? date('d/m/Y', strtotime($product->delivery_date)) : 'N/A';
        }

        return [
            // Basic product info
            'name' => $product->name,
            'variety' => $product->variety,
            'price' => $price,
            'store_name' => $product->store->name ?? 'N/A',

            // QR Code and Barcode
            'qrcode' => [
                'svg' => $qrCode ? $this->generateQrCodeSvg($qrCode->getQrUrl()) : null,
                'url' => $qrCode ? $qrCode->getQrUrl() : null
            ],
            'barcode' => $barcode,
            'formatted_price' => '€ ' . number_format((float) $price, 2, ',', '.'),

            // Order information (from OrderItem when available)
            'order_info' => [
                'number' => $orderNumber,
                'customer' => $customer,
                'customer_short' => $this->shortenCustomerName($customer),
                'delivery_date' => $deliveryDate
            ]
        ];
    }

    /**
     * Get or create QR code for a product
     */
    private function getProductQrCode(Product $product): ?QrCode
    {
        if (!$product) {
            return null;
        }

        // Look for existing QR code for this product
        $qrCode = QrCode::where('product_id', $product->id)
                       ->where('store_id', $product->store_id)
                       ->first();

        if (!$qrCode) {
            // Generate unique ref_code for the product
            $baseRefCode = 'PROD-' . strtoupper(substr(md5($product->name . $product->id), 0, 8));
            $refCode = $baseRefCode;
            $counter = 1;

            // Ensure ref_code is unique
            while (QrCode::where('ref_code', $refCode)->exists()) {
                $refCode = $baseRefCode . '-' . $counter;
                $counter++;
            }

            // Generate name: "nome prodotto - codice cliente"
            $clientCode = $product->client ?? 'N/A';
            $qrName = $product->name . ' - ' . $clientCode;

            // Generate question: "come si cura [nome del prodotto]"
            $question = "Come si cura " . $product->name . "?";

            // Create new QR code for the product
            $qrCode = QrCode::create([
                'store_id' => $product->store_id,
                'order_id' => $product->order_id, // Keep order reference
                'product_id' => $product->id,     // Add product reference
                'name' => $qrName,
                'question' => $question,
                'ref_code' => $refCode,
                'is_active' => true,
                'ean_code' => $product->ean,      // Save EAN code from product
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
