<?php

namespace App\Http\Controllers\Grower;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    /**
     * Display a listing of the grower's products.
     */
    public function index()
    {
        $grower = auth('grower')->user();

        $products = Product::where('grower_id', $grower->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('grower.products.index', compact('products', 'grower'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grower = auth('grower')->user();
        return view('grower.products.create', compact('grower'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $grower = auth('grower')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'ean' => 'nullable|string|max:20|unique:products,ean',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0'
        ]);

        $product = new Product($request->all());
        $product->grower_id = $grower->id;
        $product->save();

        return redirect()->route('grower.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $grower = auth('grower')->user();

        // Load necessary relationships
        $product->load(['store', 'order', 'grower']);

        // Ensure the product belongs to this grower
        if ($product->grower_id !== $grower->id) {
            abort(404);
        }

        // Generate label data for the product
        $labelData = $this->prepareLabelData($product);

        return view('grower.products.show', compact('product', 'grower', 'labelData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $grower = auth('grower')->user();

        // Ensure the product belongs to this grower
        if ($product->grower_id !== $grower->id) {
            abort(404);
        }

        return view('grower.products.edit', compact('product', 'grower'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $grower = auth('grower')->user();

        // Ensure the product belongs to this grower
        if ($product->grower_id !== $grower->id) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'ean' => 'nullable|string|max:20|unique:products,ean,' . $product->id,
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0'
        ]);

        $product->update($request->all());

        return redirect()->route('grower.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $grower = auth('grower')->user();

        // Ensure the product belongs to this grower
        if ($product->grower_id !== $grower->id) {
            abort(404);
        }

        $product->delete();

        return redirect()->route('grower.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Prepare all data needed for the label
     */
    private function prepareLabelData(Product $product): array
    {
        // Get or create QR code for the product
        $qrCode = $this->getProductQrCode($product);

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

        return [
            'qr_code' => $qrCode,
            'barcode' => $barcode,
            'product_url' => route('store.chatbot', [
                'store' => $product->store->slug
            ]) . '?product=' . $product->id,
            'store_name' => $product->store->name ?? 'Store',
            'grower_name' => $product->grower->name ?? 'Grower',
            'order_number' => $product->order->order_number ?? null,
            'formatted_price' => $product->price ? '€' . number_format((float) $product->price, 2, ',', '.') : '€ 0,00',
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
                // Create new QR code for this product
                $productUrl = route('store.chatbot', [
                    'store' => $product->store->slug
                ]) . '?product=' . $product->id;

                $qrCode = QrCode::create([
                    'store_id' => $product->store_id,
                    'product_id' => $product->id,
                    'name' => "QR-{$product->name}",
                    'redirect_url' => $productUrl,
                    'scan_count' => 0,
                    'is_active' => true
                ]);
            }

            // Generate QR code image
            $qrCodeSvg = QrCodeGenerator::size(200)
                ->format('svg')
                ->generate($qrCode->redirect_url);

            return [
                'model' => $qrCode,
                'svg' => $qrCodeSvg,
                'url' => $qrCode->redirect_url
            ];

        } catch (\Exception $e) {
            Log::error('Error generating QR code for product: ' . $e->getMessage());
            return null;
        }
    }
}
