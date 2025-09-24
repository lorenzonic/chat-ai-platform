<?php

namespace App\Http\Controllers\Grower;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductLabelController extends Controller
{
    /**
     * Display a listing of products for label printing (grower-specific)
     */
    public function index(Request $request): View
    {
        $grower = auth('grower')->user();

        // Check if we have order_items for this grower
        $hasOrderItems = OrderItem::where('grower_id', $grower->id)->exists();

        if ($hasOrderItems && !$request->has('legacy')) {
            // Use new order_items structure
            $query = OrderItem::with(['product', 'order', 'store', 'grower'])
                ->where('grower_id', $grower->id);

            // Filter by store
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            // Filter by order
            if ($request->filled('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            // Filter by order date
            if ($request->filled('order_date_from')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->order_date_from);
                });
            }

            if ($request->filled('order_date_to')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->order_date_to);
                });
            }

            // Search by product name
            if ($request->filled('search')) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            $orderItems = $query->orderBy('created_at', 'desc')
                               ->paginate(20)
                               ->withQueryString();

            // Get stores with order items for this grower
            $stores = \App\Models\Store::whereHas('orderItems', function($q) use ($grower) {
                $q->where('grower_id', $grower->id);
            })->orderBy('name')->get();

            // Get orders with items for this grower
            $orders = Order::whereHas('orderItems', function($q) use ($grower) {
                $q->where('grower_id', $grower->id);
            })->orderBy('created_at', 'desc')->get();

            return view('grower.products.stickers-order-items', compact('orderItems', 'stores', 'orders', 'grower'));
        } else {
            // Use legacy products structure
            $query = Product::with(['order', 'store', 'grower'])
                ->where('grower_id', $grower->id);

            // Filter by store
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            // Filter by order ID (legacy)
            if ($request->filled('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            // Filter by order date (legacy)
            if ($request->filled('order_date_from')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->order_date_from);
                });
            }

            if ($request->filled('order_date_to')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->order_date_to);
                });
            }

            // Search by product name
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $products = $query->orderBy('created_at', 'desc')
                             ->paginate(20)
                             ->withQueryString();

            // Get options for filters (legacy structure)
            $stores = \App\Models\Store::whereHas('products', function($q) use ($grower) {
                $q->where('grower_id', $grower->id);
            })->orderBy('name')->get();

            // Get available orders for this grower
            $orders = Order::with('store')
                ->whereHas('orderItems', function($q) use ($grower) {
                    $q->where('grower_id', $grower->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Get selected order info if filtering by order
            $selectedOrder = null;
            if ($request->filled('order_id')) {
                $selectedOrder = \App\Models\Order::with('store')
                    ->whereHas('orderItems', function($q) use ($grower) {
                        $q->where('grower_id', $grower->id);
                    })
                    ->find($request->order_id);
            }

            return view('grower.products.stickers', compact('products', 'stores', 'orders', 'grower', 'selectedOrder'));
        }
    }

    /**
     * Show the product with label for printing
     */
    public function show(Product $product): View
    {
        $grower = auth('grower')->user();

        // Check if product belongs to this grower
        if ($product->grower_id !== $grower->id) {
            abort(403, 'Unauthorized access to product');
        }

        // Load related data
        $product->load(['order', 'store']);

        // Generate label data
        $labelData = $this->prepareLabelData($product);

        return view('grower.products.show', compact('product', 'labelData', 'grower'));
    }

    /**
     * Prepare all data needed for the label
     */
    private function prepareLabelData(Product $product): array
    {
        // Get or create QR code for the product
        $qrCode = $this->getProductQrCode($product);

        // Generate barcode for EAN - only if EAN exists
        $barcode = null;
        $barcodeGenerator = new BarcodeGeneratorHTML();

        if ($product->ean) {
            try {
                $barcode = [
                    'html' => $barcodeGenerator->getBarcode($product->ean, 'EAN13'),
                    'code' => $product->ean
                ];
            } catch (\Exception $e) {
                // Don't generate barcode if EAN is invalid
                $barcode = null;
            }
        }

        return [
            'qr_code' => $qrCode,
            'barcode' => $barcode,
            'product_url' => $qrCode['url'] ?? route('store.chatbot', [
                'store' => $product->store->slug
            ]),
            'store_name' => $product->store->name ?? 'Store',
            'grower_name' => $product->grower->name ?? 'Grower',
            'order_number' => $product->order->order_number ?? null,
            'formatted_price' => '€' . number_format((float) $product->price, 2, ',', '.'),
            'formatted_date' => $product->created_at->format('d/m/Y'),
        ];
    }

    /**
     * Get or create QR code for product
     */
    private function getProductQrCode(Product $product): ?array
    {
        try {
            // Look for existing QR code for this product
            $qrCode = QrCode::where('product_id', $product->id)->first();

            if (!$qrCode) {
                // Generate reference code
                $refCode = 'qr_' . Str::random(8) . '_' . time();

                // Create a question for this specific product
                $question = "Dimmi tutto su {$product->name}";

                // Create new QR code for this product
                $qrCode = QrCode::create([
                    'store_id' => $product->store_id,
                    'product_id' => $product->id,
                    'name' => "QR-{$product->name}",
                    'question' => $question,
                    'ref_code' => $refCode,
                    'is_active' => true
                ]);
            }

            // Generate QR code URL using the model method
            $qrUrl = $qrCode->getQrUrl();

            // Generate QR code image
            $qrCodeSvg = QrCodeGenerator::size(200)
                ->format('svg')
                ->generate($qrUrl);

            return [
                'model' => $qrCode,
                'svg' => $qrCodeSvg,
                'url' => $qrUrl,
                'question' => $qrCode->question,
                'ref_code' => $qrCode->ref_code
            ];

        } catch (\Exception $e) {
            Log::error('Error generating QR code for product: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display order items for a specific order (new structure)
     */
    public function orderItems(Request $request): View
    {
        $grower = auth('grower')->user();

        // Get order items for this grower only
        $orderItems = OrderItem::with(['product', 'order.store', 'store', 'grower'])
            ->where('grower_id', $grower->id)
            ->whereHas('product', function($query) use ($grower) {
                $query->where('grower_id', $grower->id);
            })
            ->when($request->filled('order_id'), function($query) use ($request) {
                $query->where('order_id', $request->order_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get order for context if specified
        $order = null;
        if ($request->filled('order_id')) {
            $order = Order::with(['store'])
                ->whereHas('orderItems', function($query) use ($grower) {
                    $query->where('grower_id', $grower->id);
                })
                ->find($request->order_id);
        }

        return view('grower.orders.items', compact('orderItems', 'order', 'grower'));
    }

    /**
     * Show single order item with label for printing
     */
    public function showOrderItem(OrderItem $orderItem): View
    {
        $grower = auth('grower')->user();

        // Verify ownership through grower_id in order item
        if ($orderItem->grower_id !== $grower->id) {
            abort(403, 'Unauthorized access to order item');
        }

        // Load necessary relationships
        $orderItem->load(['product', 'order.store', 'grower']);

        // Generate label data for order item
        $labelData = $this->prepareOrderItemLabelData($orderItem);

        return view('grower.orders.item-label', compact('orderItem', 'labelData', 'grower'));
    }

    /**
     * Prepare label data specifically for order items
     */
    private function prepareOrderItemLabelData(OrderItem $orderItem): array
    {
        // Get QR code for the product
        $qrCode = $this->getProductQrCode($orderItem->product);

        // Generate barcode for EAN (from snapshot or current product) - only if EAN exists
        $barcode = null;
        $barcodeGenerator = new BarcodeGeneratorHTML();
        $ean = $orderItem->product_snapshot['ean'] ?? $orderItem->product->ean ?? null;

        if ($ean) {
            try {
                $barcode = [
                    'html' => $barcodeGenerator->getBarcode($ean, 'EAN13'),
                    'code' => $ean
                ];
            } catch (\Exception $e) {
                // Don't generate barcode if EAN is invalid
                $barcode = null;
            }
        }

        $productInfo = $orderItem->product_info;

        return [
            'qr_code' => $qrCode,
            'barcode' => $barcode,
            'quantity' => $orderItem->quantity,
            'unit_price' => $orderItem->formatted_prezzo_rivendita, // Cambiato a prezzo rivendita
            'total_price' => $orderItem->formatted_total_price,
            'order_number' => $orderItem->order->order_number,
            'store_name' => $orderItem->order->store->name ?? 'Store',
            'grower_name' => $grower->name ?? 'Grower',
            'product_name' => $productInfo['name'] ?? 'Unknown Product',
            'product_code' => $productInfo['code'] ?? 'N/A',
            'product_ean' => $productInfo['ean'] ?? null,
            'ean' => $productInfo['ean'] ?? $ean ?? null, // Aggiunto per compatibilità bulk print
            'sku' => $orderItem->sku ?? $productInfo['code'] ?? 'N/A',
            'formatted_date' => $orderItem->created_at->format('d/m/Y'),
            'product_url' => $qrCode['url'] ?? route('store.chatbot', [
                'store' => $orderItem->order->store->slug
            ]),
        ];
    }

    /**
     * Show bulk printing page for all order items matching current filters
     */
    public function bulkPrint(Request $request): View
    {
        $grower = auth('grower')->user();

        // Use the same filtering logic as index method
        $hasOrderItems = OrderItem::where('grower_id', $grower->id)->exists();

        if ($hasOrderItems && !$request->has('legacy')) {
            // Get OrderItems with same filters as index
            $query = OrderItem::with(['product', 'order', 'store', 'grower'])
                ->where('grower_id', $grower->id);

            // Apply same filters as index method
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            if ($request->filled('order_date_from')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->order_date_from);
                });
            }

            if ($request->filled('order_date_to')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->order_date_to);
                });
            }

            if ($request->filled('search')) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            $orderItems = $query->orderBy('created_at', 'desc')->get();

            // Prepare label data for all items
            $labelsData = [];
            foreach ($orderItems as $orderItem) {
                $labelsData[] = $this->prepareOrderItemLabelData($orderItem);
            }

            return view('grower.products.bulk-print', compact('labelsData', 'grower'));

        } else {
            // Legacy products structure
            $query = Product::with(['order', 'store', 'grower'])
                ->where('grower_id', $grower->id);

            // Apply same filters as index method
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            if ($request->filled('order_date_from')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->order_date_from);
                });
            }

            if ($request->filled('order_date_to')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->order_date_to);
                });
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $products = $query->orderBy('created_at', 'desc')->get();

            // Prepare label data for all products (legacy)
            $labelsData = [];
            foreach ($products as $product) {
                $labelsData[] = $this->prepareLegacyProductLabelData($product);
            }

            return view('grower.products.bulk-print-legacy', compact('labelsData', 'grower'));
        }
    }

    /**
     * Prepare legacy product label data for bulk printing
     */
    private function prepareLegacyProductLabelData(Product $product): array
    {
        // Get or create QR code for the product
        $qrCode = $this->getProductQrCode($product);

        // Generate barcode for EAN - only if EAN exists
        $barcode = null;
        if ($product->ean) {
            $barcodeGenerator = new BarcodeGeneratorHTML();
            try {
                $barcode = [
                    'html' => $barcodeGenerator->getBarcode($product->ean, 'EAN13'),
                    'code' => $product->ean
                ];
            } catch (\Exception $e) {
                $barcode = null;
            }
        }

        return [
            'qr_code' => $qrCode,
            'barcode' => $barcode,
            'quantity' => 1,
            'unit_price' => '€ ' . number_format((float) $product->price, 2, ',', '.'),
            'total_price' => '€ ' . number_format((float) $product->price, 2, ',', '.'),
            'order_number' => $product->order->order_number ?? 'N/A',
            'store_name' => $product->store->name ?? 'N/A',
            'product_name' => $product->name,
            'ean' => $product->ean,
            'sku' => $product->code ?? 'N/A',
            'formatted_date' => $product->created_at->format('d/m/Y'),
            'product_url' => $qrCode['url'] ?? '#',
        ];
    }
}
