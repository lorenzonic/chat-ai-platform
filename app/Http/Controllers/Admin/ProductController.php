<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Grower;
use App\Imports\ProductsImport;
use App\Services\LabelService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['store', 'grower']);

        // Filter by store if provided
        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $stores = Store::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'stores'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $growers = Grower::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('stores', 'growers'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'grower_id' => 'nullable|exists:growers,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'ean' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        Product::create($request->all());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto creato con successo!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['store', 'grower']);

        // Generate label data with barcode and QR code
        $labelService = new LabelService();
        $labelData = $labelService->generateLabelData($product, $product->store);

        return view('admin.products.show', compact('product', 'labelData'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $growers = Grower::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'stores', 'growers'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'grower_id' => 'nullable|exists:growers,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'ean' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Prodotto aggiornato con successo!');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto eliminato con successo!');
    }

    /**
     * Show import form
     */
    public function importForm(): View
    {
        return view('admin.products.import');
    }

    /**
     * Import products from CSV/Excel file
     */
    public function import(Request $request): RedirectResponse
    {
        // Log initial request info
        Log::info('Import attempt started', [
            'has_file' => $request->hasFile('file'),
            'file_uploaded' => $request->file('file') ? true : false,
        ]);

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:20480', // Max 20MB
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['csv', 'xlsx', 'xls'];
                    $allowedMimes = [
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];

                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();

                    // Log file details for debugging
                    Log::info('File validation', [
                        'name' => $value->getClientOriginalName(),
                        'extension' => $extension,
                        'mime_type' => $mimeType,
                        'size' => $value->getSize(),
                        'extension_allowed' => in_array($extension, $allowedExtensions),
                        'mime_allowed' => in_array($mimeType, $allowedMimes),
                    ]);

                    if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimes)) {
                        $fail('Il file deve essere di tipo CSV (.csv), Excel (.xlsx) o Excel 97-2003 (.xls). File caricato: ' . $extension . ' (MIME: ' . $mimeType . ')');
                    }
                }
            ]
        ], [
            'file.required' => 'È necessario selezionare un file.',
            'file.file' => 'Il file selezionato non è valido.',
            'file.max' => 'Il file non può essere più grande di 20MB.',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');

            // Log file information for debugging
            Log::info('File upload attempt', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
            ]);

            $import = new ProductsImport();
            Excel::import($import, $request->file('file'));

            // Update order totals after import
            $import->updateOrderTotals();

            DB::commit();

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $duplicates = $import->getDuplicateCount();
            $growers = $import->getNewGrowersCount();
            $stores = $import->getNewStoresCount();
            $orders = $import->getNewOrdersCount();

            $message = "Import completato! Prodotti importati: {$imported}";
            if ($orders > 0) {
                $message .= ", Ordini creati: {$orders}";
            }
            if ($duplicates > 0) {
                $message .= ", Duplicati saltati: {$duplicates} (codice già esistente)";
            }
            if ($skipped > 0) {
                $message .= ", Altri saltati: {$skipped}";
            }
            if ($growers > 0) {
                $message .= ", Nuovi fornitori aggiunti: {$growers}";
            }
            if ($stores > 0) {
                $message .= ", Nuovi clienti creati: {$stores} (account disattivati)";
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Products import failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_prodotti.csv"',
        ];

        $csvData = [
            // Headers - CODE for client, CODICE for product
            ['Fornitore', 'Prodotto', 'Quantità', 'CODE', 'CODICE', 'EAN', 'H', 'Categoria', 'Cliente', 'CC', 'PIA', 'PRO', 'Trasporto', 'Data', 'Note', '€ Vendita', 'Indirizzo', 'Telefono'],
            // Example row - CODE is client code, CODICE is product code
            ['Fornitore Esempio', 'Rosa Rossa', '50', 'CLI001', 'PROD001', '1234567890123', '25.5', 'Piante per cc', 'Cliente XYZ', 'CC001', 'PIA001', 'PRO001', '15.50', '2025-08-01', 'Note prodotto', '12.99', 'Via Roma 123', '+39 123 456 7890']
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';'); // Use semicolon as delimiter for Italian Excel
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate sticker for a single product
     */
    public function generateSticker(Product $product)
    {
        try {
            // Load relationships
            $product->load('store');

            // Check if EAN exists for barcode
            if (!$product->ean) {
                return response()->json(['error' => 'EAN code is required for barcode generation'], 400);
            }

            // Generate QR code for the product care question
            $qrCodeUrl = $this->generateProductQrUrl($product);

            // Generate the sticker PDF
            $pdf = $this->createStickerPDF($product, $qrCodeUrl);

            $filename = 'sticker-' . $product->id . '-' . time() . '.pdf';

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error generating product sticker', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to generate sticker'], 500);
        }
    }

    /**
     * Generate QR code URL for product care information
     */
    private function generateProductQrUrl(Product $product): string
    {
        // Get base URL
        $baseUrl = config('app.url');

        // If APP_URL is not properly set, use request URL
        if (empty($baseUrl) || str_contains($baseUrl, '${') || $baseUrl === 'http://localhost') {
            $baseUrl = request()->getSchemeAndHttpHost();
        }

        // Ensure HTTPS in production
        if (app()->environment('production')) {
            $baseUrl = str_replace('http://', 'https://', $baseUrl);
        }

        // Build URL based on store slug
        $storeSlug = $product->store ? $product->store->slug : 'default';
        $question = 'Come si cura ' . $product->name;

        $url = "{$baseUrl}/{$storeSlug}?question=" . urlencode($question);

        return $url;
    }

    /**
     * Create PDF with sticker containing barcode and QR code
     */
    private function createStickerPDF(Product $product, string $qrCodeUrl): string
    {
        // We'll use TCPDF for PDF generation
        $pdf = new \TCPDF('P', 'mm', [50, 30], true, 'UTF-8', false); // Small sticker size

        $pdf->SetCreator('Plant Care System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Product Sticker - ' . $product->name);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(2, 2, 2);
        $pdf->SetAutoPageBreak(false);

        // Add page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 6);

        // Product name (truncated if too long)
        $productName = strlen($product->name) > 20 ? substr($product->name, 0, 17) . '...' : $product->name;
        $pdf->Cell(0, 3, $productName, 0, 1, 'C');

        // EAN/Barcode
        if ($product->ean) {
            $pdf->Ln(1);
            $pdf->write1DBarcode($product->ean, 'EAN13', 5, $pdf->GetY(), 40, 8, 0.4, ['position' => 'S']);
            $pdf->Ln(10);
        }

        // QR Code
        $pdf->write2DBarcode($qrCodeUrl, 'QRCODE,H', 15, $pdf->GetY(), 20, 20, ['position' => 'C']);

        return $pdf->Output('', 'S'); // Return as string
    }
}
