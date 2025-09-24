<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        // Get current statistics for the dashboard
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_stores' => Store::count(),
            'total_growers' => Grower::count(),
            'total_order_items' => OrderItem::count(),
        ];

        return view('admin.import.index', compact('stats'));
    }

    public function showProductsImport(): View
    {
        return view('admin.import.products');
    }

    public function showOrdersImport(): View
    {
        try {
            // Get some basic stats for the view
            $stats = [
                'total_orders' => Order::count(),
                'total_products' => Product::count(),
                'total_stores' => Store::count(),
                'total_growers' => Grower::count(),
            ];

            return view('admin.import.orders-simple', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Import Orders View Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a simple view without data if there's an error
            return view('admin.import.orders-simple', ['stats' => []]);
        }
    }

    public function uploadOrdersFile(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        try {
            $file = $request->file('csv_file');

            // Check if file was uploaded successfully
            if (!$file || !$file->isValid()) {
                throw new \Exception('File upload failed or file is invalid');
            }

            // Read CSV data directly from uploaded file (no need to save to disk)
            $csvData = $this->readCsvFromUploadedFile($file);

            if (empty($csvData)) {
                throw new \Exception('The CSV file appears to be empty or unreadable.');
            }

            $headers = array_shift($csvData);
            $preview = array_slice($csvData, 0, 5);
            $mapping = $this->autoDetectColumns($headers);

            // Process the CSV data directly (no file needed)
            $result = $this->processAdvancedOrderImport($csvData, $mapping);

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully!',
                'stats' => $result,
                'headers' => $headers,
                'preview' => $preview,
                'mapping' => $mapping,
                'total_rows' => count($csvData) + 1, // +1 for headers
            ]);

        } catch (\Exception $e) {
            Log::error('CSV Upload Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processOrdersImport(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'mapping' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $filePath = $request->file_path;
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));
            $mapping = $request->mapping;

            // Process the CSV with intelligent order creation
            $result = $this->processAdvancedOrderImport($fullPath, $mapping);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully!',
                'stats' => $result
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order import failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Import failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Advanced order import with automatic entity creation
     */
    private function processAdvancedOrderImport($csvData, $mapping)
    {
        // csvData already has headers removed
        // No need to shift again if we handle it in the calling method

        $stats = [
            'orders_created' => 0,
            'products_created' => 0,
            'growers_created' => 0,
            'stores_created' => 0,
            'order_items_created' => 0,
            'errors' => []
        ];

        // Group by client/store and date to create logical orders
        $orderGroups = $this->groupByClientAndDate($csvData, $mapping);

        foreach ($orderGroups as $orderGroup) {
            try {
                $this->processOrderGroup($orderGroup, $mapping, $stats);
            } catch (\Exception $e) {
                $stats['errors'][] = "Error processing order group: " . $e->getMessage();
                Log::error('Order group processing failed', ['error' => $e->getMessage()]);
            }
        }

        return $stats;
    }

    /**
     * Group CSV rows by client and date to form logical orders
     */
    private function groupByClientAndDate($csvData, $mapping)
    {
        $groups = [];

        foreach ($csvData as $row) {
            $client = $this->getColumnValue($row, $mapping, 'cliente') ?: 'Unknown Client';
            $date = $this->getColumnValue($row, $mapping, 'data') ?: now()->format('Y-m-d');

            // Normalize date
            $date = $this->normalizeDate($date);
            $groupKey = md5($client . '_' . $date);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'client' => $client,
                    'date' => $date,
                    'items' => []
                ];
            }

            $groups[$groupKey]['items'][] = $row;
        }

        return $groups;
    }

    /**
     * Process a single order group (client + date)
     */
    private function processOrderGroup($orderGroup, $mapping, &$stats)
    {
        // 1. Find or create store
        $store = $this->findOrCreateStore($orderGroup['client'], $stats);

        // 2. Get client code from first item (all items in group should have same client code)
        $clientCode = null;
        if (!empty($orderGroup['items'])) {
            $firstItem = $orderGroup['items'][0];
            $clientCode = $this->getColumnValue($firstItem, $mapping, 'codice_cliente') ?: null;
        }

        // 3. Create order
        $order = Order::create([
            'store_id' => $store->id,
            'client' => $clientCode, // Save client code in order
            'order_date' => $orderGroup['date'],
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'total_amount' => 0 // Will be calculated
        ]);

        $stats['orders_created']++;
        $totalAmount = 0;

        // 4. Process each item in the order
        foreach ($orderGroup['items'] as $item) {
            $orderItem = $this->processOrderItem($item, $mapping, $order, $store, $stats);
            if ($orderItem) {
                $totalAmount += ($orderItem->price * $orderItem->quantity);
            }
        }

        // 5. Update order total
        $order->update(['total_amount' => $totalAmount]);
    }

    /**
     * Process a single order item
     */
    private function processOrderItem($row, $mapping, $order, $store, &$stats)
    {
        try {
            // Extract data from row
            $supplierName = $this->getColumnValue($row, $mapping, 'fornitore');
            $productCode = $this->getColumnValue($row, $mapping, 'codice');
            $productName = $this->getColumnValue($row, $mapping, 'prodotto');
            $quantity = intval($this->getColumnValue($row, $mapping, 'quantita') ?: 1);
            $price = $this->parsePrice($this->getColumnValue($row, $mapping, 'prezzo'));
            $prezzoRivendita = $this->parsePrice($this->getColumnValue($row, $mapping, 'prezzo_rivendita'));
            $ean = $this->getColumnValue($row, $mapping, 'ean');
            $height = $this->getColumnValue($row, $mapping, 'altezza');

            // Debug logging
            Log::info('Processing Order Item', [
                'row' => $row,
                'mapping' => $mapping,
                'supplier_name' => $supplierName,
                'product_code' => $productCode,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price' => $price,
                'prezzo_rivendita' => $prezzoRivendita
            ]);

            // 1. Find or create grower
            $grower = $this->findOrCreateGrower($supplierName, $stats);

            // 2. Find or create product
            $product = $this->findOrCreateProduct([
                'code' => $productCode,
                'name' => $productName,
                'ean' => $ean,
                'height' => $height,
                'grower_id' => $grower->id,
                'store_id' => $store->id
            ], $stats);

            // 3. Create order item
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'grower_id' => $grower->id,
                'store_id' => $store->id,
                'quantity' => $quantity,
                'unit_price' => $price,
                'prezzo_rivendita' => $prezzoRivendita ?: ($price * 1.3), // Usa prezzo dal CSV o 30% markup come fallback
                'total_price' => $price * $quantity,
                'ean' => $ean
            ]);

            $stats['order_items_created']++;

            return $orderItem;

        } catch (\Exception $e) {
            $stats['errors'][] = "Error processing item: " . $e->getMessage();
            Log::error('Order item processing failed', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find or create store by name
     */
    private function findOrCreateStore($clientName, &$stats)
    {
        if (empty($clientName)) {
            $clientName = 'Unknown Store';
        }

        // Try to find existing store
        $store = Store::where('name', $clientName)->first();

        if (!$store) {
            // Create new store
            $slug = Str::slug($clientName) . '-' . time();

            $store = Store::create([
                'name' => $clientName,
                'slug' => $slug,
                'email' => strtolower(str_replace(' ', '', $clientName)) . '@store.local',
                'password' => Hash::make(Str::random(12)),
                'is_active' => true
            ]);

            $stats['stores_created']++;
            Log::info('Created new store', ['name' => $clientName, 'id' => $store->id]);
        }

        return $store;
    }

    /**
     * Find or create grower by supplier name
     */
    private function findOrCreateGrower($supplierName, &$stats)
    {
        if (empty($supplierName)) {
            $supplierName = 'Unknown Supplier';
        }

        // Try to find existing grower by name only (company_name column doesn't exist)
        $grower = Grower::where('name', $supplierName)->first();

        if (!$grower) {
            // Generate a unique code
            $code = 'GR' . time() . rand(100, 999);

            $grower = Grower::create([
                'name' => $supplierName,
                'code' => $code,
                'email' => strtolower(str_replace(' ', '', $supplierName)) . '@grower.local',
                'password' => Hash::make(Str::random(12)),
                'is_active' => true
            ]);

            $stats['growers_created']++;
            Log::info('Created new grower', ['name' => $supplierName, 'id' => $grower->id]);
        }

        return $grower;
    }

    /**
     * Find or create product
     */
    private function findOrCreateProduct($productData, &$stats)
    {
        // Try to find existing product by code/EAN AND grower_id (products are grower-specific)
        $product = null;

        if (!empty($productData['code']) && !empty($productData['grower_id'])) {
            $product = Product::where('code', $productData['code'])
                             ->where('grower_id', $productData['grower_id'])
                             ->first();
        }

        if (!$product && !empty($productData['ean']) && !empty($productData['grower_id'])) {
            $product = Product::where('ean', $productData['ean'])
                             ->where('grower_id', $productData['grower_id'])
                             ->first();
        }

        if (!$product) {
            $product = Product::create([
                'code' => $productData['code'] ?: 'PRD' . time() . rand(100, 999),
                'name' => $productData['name'] ?: 'Unknown Product',
                'ean' => $productData['ean'],
                'height' => $this->parseNumber($productData['height']),
                'grower_id' => $productData['grower_id'],
                'store_id' => $productData['store_id'],
                'is_active' => true
            ]);

            $stats['products_created']++;
            Log::info('Created new product', [
                'name' => $productData['name'],
                'id' => $product->id,
                'code' => $productData['code'],
                'grower_id' => $productData['grower_id']
            ]);
        } else {
            Log::info('Found existing product', [
                'name' => $product->name,
                'id' => $product->id,
                'code' => $product->code,
                'grower_id' => $product->grower_id
            ]);
        }

        return $product;
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad((Order::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get column value from CSV row based on mapping
     */
    private function getColumnValue($row, $mapping, $key)
    {
        if (!isset($mapping[$key]) || !isset($row[$mapping[$key]])) {
            return null;
        }

        return trim($row[$mapping[$key]]);
    }

    /**
     * Parse price from string (handles €, commas, etc.)
     */
    private function parsePrice($priceString)
    {
        if (empty($priceString)) return 0.00;

        // Remove currency symbols and normalize
        $price = preg_replace('/[€$£]/', '', $priceString);
        $price = str_replace(',', '.', $price);
        $price = preg_replace('/[^\d.]/', '', $price);

        return floatval($price);
    }

    /**
     * Parse number from string
     */
    private function parseNumber($numberString)
    {
        if (empty($numberString)) return null;

        $number = preg_replace('/[^\d.]/', '', $numberString);
        return is_numeric($number) ? floatval($number) : null;
    }

    /**
     * Normalize date format
     */
    private function normalizeDate($dateString)
    {
        if (empty($dateString)) return now()->format('Y-m-d');

        try {
            // Try to parse different date formats
            $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-m-Y'];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Fallback to today
            return now()->format('Y-m-d');

        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }

    /**
     * Read CSV file and return data array
     */
    private function readCsvFromUploadedFile($uploadedFile)
    {
        $data = [];

        // Get the file content as string
        $content = file_get_contents($uploadedFile->getPathname());
        
        if (empty($content)) {
            throw new \Exception("Uploaded file is empty");
        }

        // Auto-detect delimiter by reading first line
        $lines = explode("\n", $content);
        $firstLine = $lines[0] ?? '';
        
        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        // Parse CSV content
        $rows = str_getcsv($content, "\n");
        foreach ($rows as $row) {
            if (!empty(trim($row))) {
                $data[] = str_getcsv($row, $delimiter);
            }
        }

        return $data;
    }

    private function readCsvFile($filePath)
    {
        $data = [];

        if (!file_exists($filePath)) {
            Log::error("CSV file not found: {$filePath}");
            throw new \Exception("File not found: {$filePath}");
        }

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            // Auto-detect delimiter by reading first line
            $firstLine = fgets($handle);
            rewind($handle);

            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            }

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        } else {
            throw new \Exception("Cannot open file: {$filePath}");
        }

        return $data;
    }

    /**
     * Auto-detect column mappings based on headers
     */
    private function autoDetectColumns($headers)
    {
        $mapping = [];
        $headerMaps = [
            'cliente' => ['cliente', 'client', 'negozio', 'store'],
            'prodotto' => ['prodotto', 'product', 'articolo', 'item'],
            'quantita' => ['quantità', 'piani', 'quantity', 'qta', 'qty'],
            'prezzo' => ['prezzo', 'price', 'costo', 'cost'],
            'prezzo_rivendita' => ['€ vendita', 'vendita', 'retail_price', 'selling_price'],
            'fornitore' => ['fornitore', 'grower', 'supplier', 'produttore'],
            'codice' => ['codice', 'product_code'], // 'code' rimosso perché è il codice cliente
            'codice_cliente' => ['code'], // Codice del cliente/store
            'ean' => ['ean', 'barcode', 'gtin'],
            'data' => ['data', 'date', 'delivery_date'],
            'altezza' => ['h', 'height', 'altezza', 'cm']
        ];

        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            foreach ($headerMaps as $field => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($header, $keyword) !== false) {
                        $mapping[$field] = $index;
                        break 2;
                    }
                }
            }
        }

        // Debug logging
        Log::info('Column Mapping Debug', [
            'headers' => $headers,
            'mapping' => $mapping,
            'header_maps' => $headerMaps
        ]);

        return $mapping;
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ordini_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV headers - Italian format matching the expected structure
            fputcsv($file, [
                'Fornitore',
                'Codice',
                'Prodotto',
                'Quantità',
                'Cliente',
                '€ Vendita',
                'EAN',
                'Data',
                'H'
            ], ';');

            // Example data rows
            $examples = [
                [
                    'Vivai Rossi SRL',
                    'PLT001',
                    'Rosa Rossa 18cm',
                    '50',
                    'Garden Center Milano',
                    '5.50',
                    '8033533158423',
                    '2024-01-15',
                    '30'
                ],
                [
                    'Floricoltura Bianchi',
                    'PLT002',
                    'Geranio Bianco 14cm',
                    '75',
                    'Verde Paradiso Roma',
                    '3.20',
                    '8033533158430',
                    '2024-01-15',
                    '25'
                ],
                [
                    'Vivai Verdi & Co',
                    'PLT003',
                    'Basilico Genovese vaso 12',
                    '100',
                    'Orto Felice Napoli',
                    '2.80',
                    '8033533158447',
                    '2024-01-16',
                    '15'
                ]
            ];

            foreach ($examples as $row) {
                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function debugUpload($filename = null)
    {
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');

        $info = [
            'temp_dir_path' => $tempDir,
            'temp_dir_exists' => is_dir($tempDir),
            'temp_dir_writable' => is_writable($tempDir),
            'storage_app_path' => storage_path('app'),
            'directory_separator' => DIRECTORY_SEPARATOR,
            'files' => [],
            'php_version' => PHP_VERSION,
            'session_info' => session('import_file_info'),
        ];

        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $tempDir . DIRECTORY_SEPARATOR . $file;
                    $info['files'][] = [
                        'name' => $file,
                        'path' => $filePath,
                        'size' => filesize($filePath),
                        'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'exists' => file_exists($filePath),
                        'readable' => is_readable($filePath),
                    ];
                }
            }
        }

        if ($filename) {
            $testPath = $tempDir . DIRECTORY_SEPARATOR . $filename;
            $info['test_file'] = [
                'filename' => $filename,
                'full_path' => $testPath,
                'exists' => file_exists($testPath),
                'readable' => is_readable($testPath),
            ];

            if (file_exists($testPath)) {
                try {
                    $csvData = $this->readCsvFile($testPath);
                    $info['test_file']['csv_rows'] = count($csvData);
                    $info['test_file']['csv_headers'] = $csvData[0] ?? [];
                } catch (\Exception $e) {
                    $info['test_file']['csv_error'] = $e->getMessage();
                }
            }
        }

        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
    }
}
