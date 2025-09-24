<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportController extends Controller
{
    private $orderNumberCounter = 0;

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber()
    {
        $year = date('Y');
        $pattern = "ORD-{$year}-%";

        $lastOrder = Order::whereRaw('order_number LIKE ?', [$pattern])
                         ->orderByRaw('CAST(SUBSTRING(order_number, 10) AS UNSIGNED) DESC')
                         ->first();

        if ($lastOrder && preg_match('/ORD-\d{4}-(\d{6})/', $lastOrder->order_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1 + $this->orderNumberCounter;
        } else {
            $nextNumber = 1 + $this->orderNumberCounter;
        }

        $this->orderNumberCounter++;

        return 'ORD-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Show the import dashboard
     */
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_order_items' => OrderItem::count(),
            'total_stores' => Store::count(),
            'total_growers' => Grower::count(),
        ];

        return view('admin.import.index', compact('stats'));
    }

    /**
     * Show the products import form
     */
    public function showProductsImport(): View
    {
        return view('admin.import.products');
    }

    /**
     * Show the orders import form
     */
    public function showOrdersImport(): View
    {
        return view('admin.import.orders');
    }

    /**
     * Upload and preview CSV file for orders import
     */
    public function uploadOrdersFile(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $originalName = $file->getClientOriginalName();
            
            // Generate unique filename
            $timestamp = time();
            $randomString = Str::random(10);
            $extension = $file->getClientOriginalExtension();
            $storedName = "{$timestamp}_{$randomString}.{$extension}";
            
            // Create directory if it doesn't exist
            $uploadPath = storage_path('app/temp/imports');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move file manually
            $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $storedName;
            $file->move($uploadPath, $storedName);
            
            Log::info('File uploaded successfully', [
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'full_path' => $fullPath,
                'file_size' => filesize($fullPath)
            ]);

            // Read and parse CSV
            $csvData = [];
            $headers = [];
            
            if (($handle = fopen($fullPath, "r")) !== FALSE) {
                $rowIndex = 0;
                while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
                    if ($rowIndex === 0) {
                        $headers = $row;
                    } else {
                        $csvData[] = $row;
                    }
                    $rowIndex++;
                }
                fclose($handle);
            }

            $previewData = [
                'file_path' => "temp/imports/{$storedName}",
                'headers' => $headers,
                'rows' => $csvData,
                'total_rows' => count($csvData)
            ];

            return view('admin.import.orders', compact('previewData'));

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the structured orders import form (Italian format)
     */
    public function showStructuredOrdersImport(): View
    {
        return view('admin.import.structured-orders');
    }

    /**
     * Show the complete orders import form (OrderItems with auto-creation)
     */
    public function showCompleteOrdersImport(): View
    {
        return view('admin.import.complete-orders');
    }

    /**
     * Import products from Excel file
     */
    public function importProducts(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'store_id' => 'required|exists:stores,id',
            'grower_id' => 'required|exists:growers,id',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $store = Store::findOrFail($request->store_id);
            $grower = Grower::findOrFail($request->grower_id);

            // Read Excel file
            $data = Excel::toArray(new \stdClass(), $file)[0];

            // Skip header row
            array_shift($data);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        continue;
                    }

                    // Expected columns: name, code, ean, description, quantity, height, price, category, client
                    $productData = [
                        'store_id' => $store->id,
                        'grower_id' => $grower->id,
                        'name' => $row[0] ?? '',
                        'code' => $row[1] ?? '',
                        'ean' => $row[2] ?? null,
                        'description' => $row[3] ?? null,
                        'quantity' => is_numeric($row[4]) ? intval($row[4]) : 0,
                        'height' => is_numeric($row[5]) ? floatval($row[5]) : null,
                        'price' => is_numeric($row[6]) ? floatval($row[6]) : null,
                        'category' => $row[7] ?? '',
                        'client' => $row[8] ?? '',
                        'is_active' => true,
                    ];

                    // Validate required fields
                    if (empty($productData['name'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Product name is required";
                        $skipped++;
                        continue;
                    }

                    // Check for duplicate EAN if provided
                    if (!empty($productData['ean'])) {
                        $existingProduct = Product::where('ean', $productData['ean'])->first();
                        if ($existingProduct) {
                            $errors[] = "Row " . ($rowIndex + 2) . ": EAN {$productData['ean']} already exists (Product: {$existingProduct->name})";
                            $skipped++;
                            continue;
                        }
                    }

                    Product::create($productData);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Import completed! Imported: {$imported}, Skipped: {$skipped}";

            if (!empty($errors)) {
                $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... and " . (count($errors) - 10) . " more errors.";
                }
            }

            return redirect()->route('admin.import.products')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product import failed: ' . $e->getMessage());

            return redirect()->route('admin.import.products')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import orders from Excel file
     */
    public function importOrders(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'store_id' => 'required|exists:stores,id',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $store = Store::findOrFail($request->store_id);

            // Read Excel file
            $data = Excel::toArray(new \stdClass(), $file)[0];

            // Skip header row
            array_shift($data);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        continue;
                    }

                    // Expected columns: order_number, delivery_date, notes, status
                    $orderData = [
                        'store_id' => $store->id,
                        'order_number' => $this->generateOrderNumber(), // Auto-generate order number
                        'delivery_date' => !empty($row[1]) ? Carbon::parse($row[1]) : null,
                        'notes' => $row[2] ?? '',
                        'status' => $row[3] ?? 'pending',
                        'total_amount' => is_numeric($row[4]) ? floatval($row[4]) : 0,
                    ];

                    // Validate required fields - order number is now auto-generated, no need to validate

                    // Check for duplicate order number is now unnecessary since we generate unique numbers

                    Order::create($orderData);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Import completed! Imported: {$imported}, Skipped: {$skipped}";

            if (!empty($errors)) {
                $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... and " . (count($errors) - 10) . " more errors.";
                }
            }

            return redirect()->route('admin.import.orders')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order import failed: ' . $e->getMessage());

            return redirect()->route('admin.import.orders')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel templates
     */
    public function downloadTemplate($type)
    {
        if ($type === 'products') {
            $headers = [
                'Product Name', 'Product Code', 'EAN', 'Description',
                'Quantity', 'Height (cm)', 'Price (€)', 'Category', 'Client'
            ];

            $sampleData = [
                ['Rosa Rossa Premium', 'ROSA001', '8051277781620', 'Rosa rossa di alta qualità', 50, 25.5, 15.99, 'Fiori', 'Garden Center Roma'],
                ['Geranio Rosso', 'GER001', '8051277781621', 'Geranio rosso per balconi', 30, 20.0, 8.50, 'Piante', 'Vivaio Milano'],
            ];

        } elseif ($type === 'orders') {
            $headers = [
                'Order Number', 'Delivery Date', 'Notes', 'Status', 'Total Amount (€)'
            ];

            $sampleData = [
                ['ORD001', '2025-12-25', 'Consegna per Natale', 'pending', 150.00],
                ['ORD002', '2025-12-30', 'Ordine urgente', 'confirmed', 85.50],
            ];
        } elseif ($type === 'complete-orders') {
            // Return the stored template file instead of generating one
            $templatePath = storage_path('app' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'complete-orders-template.csv');

            if (!file_exists($templatePath)) {
                abort(404, 'Template file not found');
            }            $filename = "complete_orders_template_" . date('Y-m-d') . ".csv";

            return response()->download($templatePath, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        } else {
            abort(404);
        }

        $filename = "template_{$type}_" . date('Y-m-d') . ".csv";

        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Import structured orders from CSV (Italian format)
     * Format: Fornitore,Piani,Quantità,Codice,Prodotto,CODE,H,Piante per cc,Cliente,CC,PIA,PRO,Trasporto,Data,Note,EAN,€ Vendita,Indirizzo,Telefono
     */
    public function importStructuredOrders(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480', // 20MB max for large orders
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');

            // Read Excel/CSV file
            $data = Excel::toArray(new \stdClass(), $file)[0];

            // Skip header row
            array_shift($data);

            $stats = [
                'orders_created' => 0,
                'growers_created' => 0,
                'products_created' => 0,
                'products_updated' => 0,
                'order_items_created' => 0,
                'stores_created' => 0,
                'rows_processed' => 0,
                'rows_skipped' => 0,
                'errors' => []
            ];

            // Group rows by CODE (cliente) + Data to create orders
            $orderGroups = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $stats['rows_skipped']++;
                        continue;
                    }

                    // Parse the row according to the format
                    $rowData = $this->parseOrderRow($row, $rowIndex);

                    if (!$rowData) {
                        $stats['rows_skipped']++;
                        continue;
                    }

                    // Group by client code and date
                    $orderKey = $rowData['client_code'] . '_' . $rowData['delivery_date']->format('Y-m-d');

                    if (!isset($orderGroups[$orderKey])) {
                        $orderGroups[$orderKey] = [
                            'client_code' => $rowData['client_code'],
                            'client_name' => $rowData['client_name'],
                            'delivery_date' => $rowData['delivery_date'],
                            'transport' => $rowData['transport'],
                            'items' => []
                        ];
                    }

                    $orderGroups[$orderKey]['items'][] = $rowData;
                    $stats['rows_processed']++;

                } catch (\Exception $e) {
                    $stats['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    $stats['rows_skipped']++;
                }
            }

            // Process each order group
            foreach ($orderGroups as $orderGroup) {
                try {
                    $this->processOrderGroup($orderGroup, $stats);
                } catch (\Exception $e) {
                    $stats['errors'][] = "Order {$orderGroup['client_code']} on {$orderGroup['delivery_date']}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = $this->formatImportSummary($stats);

            return redirect()->route('admin.import.structured-orders')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Structured order import failed: ' . $e->getMessage());

            return redirect()->route('admin.import.structured-orders')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Parse a single row from the CSV
     */
    private function parseOrderRow(array $row, int $rowIndex): ?array
    {
        // Expected columns: Fornitore,Piani,Quantità,Codice,Prodotto,CODE,H,Piante per cc,Cliente,CC,PIA,PRO,Trasporto,Data,Note,EAN,€ Vendita,Indirizzo,Telefono

        $growerName = trim($row[0] ?? ''); // Fornitore
        $piani = intval($row[1] ?? 0); // Piani
        $quantity = intval($row[2] ?? 0); // Quantità
        $productCode = trim($row[3] ?? ''); // Codice
        $productName = trim($row[4] ?? ''); // Prodotto
        $clientCode = trim($row[5] ?? ''); // CODE
        $height = floatval($row[6] ?? 0); // H
        $piantePerCC = intval($row[7] ?? 0); // Piante per cc
        $clientName = trim($row[8] ?? ''); // Cliente
        $cc = trim($row[9] ?? ''); // CC
        $pia = trim($row[10] ?? ''); // PIA
        $pro = trim($row[11] ?? ''); // PRO
        $transport = trim($row[12] ?? ''); // Trasporto
        $dateString = trim($row[13] ?? ''); // Data
        $notes = trim($row[14] ?? ''); // Note
        $ean = trim($row[15] ?? ''); // EAN
        $priceString = str_replace(',', '.', trim($row[16] ?? '')); // € Vendita
        $address = trim($row[17] ?? ''); // Indirizzo
        $phone = trim($row[18] ?? ''); // Telefono

        // Validate required fields
        if (empty($growerName) || empty($productCode) || empty($clientCode) || empty($dateString)) {
            throw new \Exception("Missing required fields: Fornitore, Codice, CODE, or Data");
        }

        // Parse date (format: dd/MM/yyyy)
        try {
            $deliveryDate = Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: $dateString (expected dd/MM/yyyy)");
        }

        // Parse price
        $price = is_numeric($priceString) ? floatval($priceString) : null;

        return [
            'grower_name' => $growerName,
            'piani' => $piani,
            'quantity' => $quantity,
            'product_code' => $productCode,
            'product_name' => $productName,
            'client_code' => $clientCode,
            'height' => $height,
            'piante_per_cc' => $piantePerCC,
            'client_name' => $clientName,
            'cc' => $cc,
            'pia' => $pia,
            'pro' => $pro,
            'transport' => $transport,
            'transport_cost' => null, // Transport cost will be calculated or added separately
            'delivery_date' => $deliveryDate,
            'notes' => $notes,
            'ean' => $ean ?: null,
            'price' => $price,
            'address' => $address,
            'phone' => $phone
        ];
    }

    /**
     * Process a single order group (all items for same client + date)
     */
    private function processOrderGroup(array $orderGroup, array &$stats): void
    {
        // 1. Create or find store based on client
        $store = $this->createOrFindStore($orderGroup['client_code'], $orderGroup['client_name'], $stats);

        // 2. Create order
        $order = $this->createOrder($store, $orderGroup, $stats);

        // 3. Process each item in the order
        foreach ($orderGroup['items'] as $itemData) {
            // Create or find grower
            $grower = $this->createOrFindGrower($itemData['grower_name'], $stats);

            // Create or update product
            $product = $this->createOrUpdateProduct($itemData, $store, $grower, $stats);

            // Create order item
            $this->createOrderItem($order, $product, $itemData, $grower, $store, $stats);
        }
    }

    /**
     * Create or find store by client code
     */
    private function createOrFindStore(string $clientCode, string $clientName, array &$stats): Store
    {
        // Try to find store by client_code or slug
        $store = Store::where('client_code', $clientCode)
                     ->orWhere('slug', $clientCode)
                     ->first();

        if (!$store) {
            // Create new store with correct schema
            $store = Store::create([
                'name' => $clientName ?: $clientCode,
                'slug' => strtolower(str_replace(' ', '-', $clientCode)),
                'client_code' => $clientCode,
                'email' => strtolower(str_replace(' ', '', $clientCode)) . '@generated.com',
                'password' => bcrypt('password123'),
                'is_active' => true,
                'is_premium' => false,
                'chat_opening_message' => "Benvenuto da {$clientName}!",
                'chat_theme_color' => '#007bff'
            ]);
            $stats['stores_created']++;
        }

        return $store;
    }

    /**
     * Create or find grower by name
     */
    private function createOrFindGrower(string $growerName, array &$stats): Grower
    {
        // Try to find grower by name
        $grower = Grower::where('name', $growerName)->first();

        if (!$grower) {
            // Create new grower with correct schema
            $grower = Grower::create([
                'name' => $growerName,
                'email' => strtolower(str_replace(' ', '', $growerName)) . '@generated.com',
                'password' => bcrypt('password123'),
                'is_active' => true
            ]);
            $stats['growers_created']++;
        }

        return $grower;
    }

    /**
     * Create or update product using product code as unique key
     */
    private function createOrUpdateProduct(array $itemData, Store $store, Grower $grower, array &$stats): Product
    {
        // Try to find product by code
        $product = Product::where('code', $itemData['product_code'])->first();

        $productData = [
            'store_id' => $store->id,
            'grower_id' => $grower->id,
            'name' => $itemData['product_name'],
            'code' => $itemData['product_code'],
            'ean' => $itemData['ean'],
            'height' => $itemData['height'],
            'price' => $itemData['price'],
            'quantity' => $itemData['quantity'],
            'transport' => $itemData['transport'], // Keep product-specific transport
            'is_active' => true
        ];

        if ($product) {
            // Update existing product
            $product->update($productData);
            $stats['products_updated']++;
        } else {
            // Create new product
            $product = Product::create($productData);
            $stats['products_created']++;
        }

        return $product;
    }

    /**
     * Create order for the group
     */
    private function createOrder(Store $store, array $orderGroup, array &$stats): Order
    {
        $orderNumber = $this->generateOrderNumber();

        // Get order-level data from the first item
        $firstItem = $orderGroup['items'][0] ?? [];

        $order = Order::create([
            'store_id' => $store->id,
            'order_number' => $orderNumber,
            'client' => $orderGroup['client_name'],
            'cc' => $firstItem['cc'] ?? null,
            'pia' => $firstItem['pia'] ?? null,
            'pro' => $firstItem['pro'] ?? null,
            'delivery_date' => $orderGroup['delivery_date'],
            'transport' => $orderGroup['transport'],
            'transport_cost' => $firstItem['transport_cost'] ?? null,
            'address' => $firstItem['address'] ?? null,
            'phone' => $firstItem['phone'] ?? null,
            'notes' => $firstItem['notes'] ?? 'Auto-imported order',
            'status' => 'pending',
            'total_amount' => 0 // Will be calculated after items are added
        ]);

        $stats['orders_created']++;
        return $order;
    }

    /**
     * Create order item
     */
    private function createOrderItem(Order $order, Product $product, array $itemData, Grower $grower, Store $store, array &$stats): void
    {
        $unitPrice = $itemData['price'] ?? 0;
        $quantity = $itemData['quantity'];
        $totalPrice = $unitPrice * $quantity;

        OrderItem::create([
            'order_id' => $order->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'grower_id' => $grower->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'prezzo_rivendita' => $unitPrice,
            'ean' => $itemData['ean'],
            'total_price' => $totalPrice,
            'product_snapshot' => json_encode([
                'name' => $product->name,
                'code' => $product->code,
                'height' => $itemData['height'],
                'piani' => $itemData['piani'],
                'piante_per_cc' => $itemData['piante_per_cc']
            ])
        ]);

        // Update order total
        $currentTotal = $order->total_amount ? (float)$order->total_amount : 0;
        $order->setAttribute('total_amount', $currentTotal + $totalPrice);
        $order->save();

        $stats['order_items_created']++;
    }

    /**
     * Format import summary message
     */
    private function formatImportSummary(array $stats): string
    {
        $message = "🎉 Import completato!\n\n";
        $message .= "📊 Statistiche:\n";
        $message .= "• Ordini creati: {$stats['orders_created']}\n";
        $message .= "• Fornitori (grower) creati: {$stats['growers_created']}\n";
        $message .= "• Prodotti creati: {$stats['products_created']}\n";
        $message .= "• Prodotti aggiornati: {$stats['products_updated']}\n";
        $message .= "• Order items creati: {$stats['order_items_created']}\n";
        $message .= "• Store creati: {$stats['stores_created']}\n";
        $message .= "• Righe elaborate: {$stats['rows_processed']}\n";
        $message .= "• Righe saltate: {$stats['rows_skipped']}\n";

        if (!empty($stats['errors'])) {
            $message .= "\n⚠️ Errori:\n";
            foreach (array_slice($stats['errors'], 0, 10) as $error) {
                $message .= "• $error\n";
            }
            if (count($stats['errors']) > 10) {
                $message .= "• ... e altri " . (count($stats['errors']) - 10) . " errori.\n";
            }
        }

        return $message;
    }

    /**
     * Preview uploaded orders file and show column mapping
     */
    public function previewOrdersImport(Request $request)
    {
        // Log request details for debugging
        \Log::info('Preview orders import request received', [
            'has_file' => $request->hasFile('excel_file'),
            'import_type' => $request->input('import_type'),
            'all_files' => array_keys($request->allFiles()),
            'file_names' => $request->hasFile('excel_file') ? [$request->file('excel_file')->getClientOriginalName()] : [],
        ]);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');

            // Create unique filename to avoid conflicts
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $uniqueName = time() . '_' . Str::random(10) . '.' . $extension;

            // Ensure directory exists first
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Store file manually to ensure it works
            $fullPath = $tempDir . DIRECTORY_SEPARATOR . $uniqueName;
            $filePath = 'temp/imports/' . $uniqueName;

            // Move uploaded file to destination
            if (!$file->move($tempDir, $uniqueName)) {
                throw new \Exception('Failed to move uploaded file to temp directory');
            }

            // Clean old files (older than 1 hour)
            $this->cleanOldTempFiles();

            // Verify file was stored correctly
            if (!file_exists($fullPath)) {
                throw new \Exception('Failed to store uploaded file - file not found after move');
            }

            // Log successful upload
            \Log::info('File uploaded successfully', [
                'original_name' => $originalName,
                'stored_name' => $uniqueName,
                'full_path' => $fullPath,
                'file_size' => filesize($fullPath)
            ]);

            // Read file based on extension
            if (strtolower($extension) === 'csv') {
                // Handle CSV files directly
                $data = $this->readCsvFile($fullPath);
            } else {
                // Handle Excel files
                $data = Excel::toArray(new \stdClass(), $fullPath)[0];
            }

            if (empty($data)) {
                return back()->withErrors(['excel_file' => 'The file appears to be empty.']);
            }

            // Extract headers (first row)
            $headers = array_shift($data);

            // Clean headers
            $headers = array_map(function($header) {
                return trim($header);
            }, $headers);

            // Store file info in session for later use
            session(['import_file_info' => [
                'path' => $filePath,
                'original_name' => $originalName,
                'headers' => $headers,
                'total_rows' => count($data)
            ]]);

            // Debug log
            \Log::info('File stored successfully', [
                'filePath' => $filePath,
                'fullPath' => $fullPath,
                'fileExists' => file_exists($fullPath),
                'fileSize' => file_exists($fullPath) ? filesize($fullPath) : 'N/A'
            ]);

            // Prepare preview data
            $previewData = [
                'file_path' => $filePath,
                'headers' => $headers,
                'rows' => array_slice($data, 0, 20), // First 20 rows for preview
                'total_rows' => count($data),
                'original_name' => $originalName
            ];

            return view('admin.import.orders', compact('previewData'));

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error reading file: ' . $e->getMessage()]);
        }
    }

    /**
     * Read CSV file manually to avoid Excel package issues
     */
    private function readCsvFile($filePath)
    {
        $data = [];

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }    /**
     * Process orders import with column mapping
     */
    /**
     * Process the orders import
     */
    public function processOrdersImport(Request $request)
    {
        try {
            DB::beginTransaction();

            $mapping = $request->input('mapping');
            $filePath = $request->input('file_path');
            $fullPath = storage_path('app/' . $filePath);

            if (!file_exists($fullPath)) {
                throw new \Exception('File not found');
            }

            // Read CSV data
            $csvData = [];
            if (($handle = fopen($fullPath, "r")) !== FALSE) {
                $headers = fgetcsv($handle, 0, ","); // Skip headers
                while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
                    $csvData[] = $row;
                }
                fclose($handle);
            }

            $stats = [
                'orders_created' => 0,
                'order_items_created' => 0,
                'products_created' => 0,
                'growers_created' => 0,
                'stores_created' => 0,
                'errors' => []
            ];

            // Process each row
            foreach ($csvData as $rowIndex => $row) {
                try {
                    // Extract mapped data
                    $rowData = [];
                    foreach ($mapping as $field => $columnIndex) {
                        if ($columnIndex !== '' && isset($row[$columnIndex])) {
                            $rowData[$field] = trim($row[$columnIndex]);
                        }
                    }

                    // Skip rows with missing required fields
                    if (empty($rowData['fornitore']) || empty($rowData['codice']) || 
                        empty($rowData['prodotto']) || empty($rowData['quantita']) || 
                        empty($rowData['cliente'])) {
                        $stats['errors'][] = "Row " . ($rowIndex + 2) . ": Missing required fields";
                        continue;
                    }

                    // Create/find Grower
                    $grower = Grower::firstOrCreate(
                        ['name' => $rowData['fornitore']],
                        ['description' => 'Auto-created from import']
                    );
                    if ($grower->wasRecentlyCreated) {
                        $stats['growers_created']++;
                    }

                    // Create/find Store
                    $store = Store::firstOrCreate(
                        ['name' => $rowData['cliente']],
                        [
                            'slug' => Str::slug($rowData['cliente']) . '-' . time(),
                            'email' => strtolower(str_replace(' ', '', $rowData['cliente'])) . time() . '@tempstore.com',
                            'password' => Hash::make('temporary123'),
                            'client_name' => $rowData['cliente'],
                            'status' => 'active',
                            'phone' => $rowData['telefono'] ?? null,
                            'address' => $rowData['indirizzo'] ?? null,
                        ]
                    );
                    if ($store->wasRecentlyCreated) {
                        $stats['stores_created']++;
                    }

                    // Create/find Product
                    $productData = [
                        'grower_id' => $grower->id,
                        'name' => $rowData['prodotto'],
                        'code' => $rowData['codice'],
                        'ean' => $rowData['ean'] ?? null,
                        'height' => $rowData['altezza'] ?? null,
                        'price' => $this->parsePrice($rowData['prezzo'] ?? '0'),
                        'is_active' => true
                    ];

                    $product = Product::firstOrCreate(
                        ['code' => $rowData['codice']],
                        $productData
                    );
                    if ($product->wasRecentlyCreated) {
                        $stats['products_created']++;
                    }

                    // Generate group key for orders (Cliente + CC + PIA + PRO + Data)
                    $orderKey = $rowData['cliente'] . '|' . 
                               ($rowData['cc'] ?? '') . '|' . 
                               ($rowData['pia'] ?? '') . '|' . 
                               ($rowData['pro'] ?? '') . '|' . 
                               ($rowData['data'] ?? '');

                    // Create or find order
                    static $createdOrders = [];
                    
                    if (!isset($createdOrders[$orderKey])) {
                        $orderNumber = $this->generateOrderNumber();
                        
                        $order = Order::create([
                            'store_id' => $store->id,
                            'order_number' => $orderNumber,
                            'status' => 'pending',
                            'total_amount' => 0, // Will be calculated from items
                            'cc' => $rowData['cc'] ?? null,
                            'pia' => $rowData['pia'] ?? null,
                            'pro' => $rowData['pro'] ?? null,
                            'transport' => $rowData['trasporto'] ?? null,
                            'delivery_date' => $this->parseDate($rowData['data'] ?? null),
                            'phone' => $rowData['telefono'] ?? null,
                            'notes' => $rowData['note'] ?? 'Auto-imported order',
                        ]);

                        $createdOrders[$orderKey] = $order;
                        $stats['orders_created']++;
                    } else {
                        $order = $createdOrders[$orderKey];
                    }

                    // Create OrderItem
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => intval($rowData['quantita']),
                        'price' => $this->parsePrice($rowData['prezzo'] ?? '0'),
                        'total' => intval($rowData['quantita']) * $this->parsePrice($rowData['prezzo'] ?? '0'),
                        'notes' => $rowData['note'] ?? null,
                    ]);
                    $stats['order_items_created']++;

                } catch (\Exception $e) {
                    $stats['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            // Update order totals
            foreach (Order::whereIn('id', array_map(fn($o) => $o->id, $createdOrders ?? []))->get() as $order) {
                $total = $order->orderItems()->sum('total');
                $order->update(['total_amount' => $total]);
            }

            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            DB::commit();

            // Prepare success message
            $message = "✅ Import completed successfully!\n\n";
            $message .= "📊 Summary:\n";
            $message .= "• Orders created: {$stats['orders_created']}\n";
            $message .= "• OrderItems created: {$stats['order_items_created']}\n";
            $message .= "• Products created: {$stats['products_created']}\n";
            $message .= "• Growers created: {$stats['growers_created']}\n";
            $message .= "• Stores created: {$stats['stores_created']}\n";

            if (!empty($stats['errors'])) {
                $message .= "\n⚠️ Issues encountered:\n";
                foreach (array_slice($stats['errors'], 0, 10) as $error) {
                    $message .= "• {$error}\n";
                }
                if (count($stats['errors']) > 10) {
                    $message .= "• ... and " . (count($stats['errors']) - 10) . " more issues\n";
                }
            }

            return redirect()->route('admin.import.orders')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Orders import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.import.orders')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    {
        $request->validate([
            'file_path' => 'required|string',
            'mapping' => 'required|array',
            'mapping.order_number' => 'required|numeric',
            'mapping.client' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $filePath = $request->file_path;
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));
            $mapping = $request->mapping;

            // Verify file still exists
            if (!file_exists($fullPath)) {
                // Log debug info
                \Log::error('File not found at initial path', [
                    'requested_path' => $filePath,
                    'full_path' => $fullPath,
                    'storage_app_exists' => is_dir(storage_path('app')),
                    'temp_dir_exists' => is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'temp')),
                    'imports_dir_exists' => is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports'))
                ]);

                // Try to get from session as fallback
                $sessionInfo = session('import_file_info');
                if ($sessionInfo && isset($sessionInfo['path'])) {
                    $filePath = $sessionInfo['path'];
                    $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));

                    \Log::info('Trying session fallback', [
                        'session_path' => $filePath,
                        'full_path' => $fullPath,
                        'exists' => file_exists($fullPath)
                    ]);
                }

                if (!file_exists($fullPath)) {
                    // List files in imports directory for debugging
                    $importsDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');
                    $files = is_dir($importsDir) ? scandir($importsDir) : [];

                    \Log::error('File still not found after fallback', [
                        'final_path' => $fullPath,
                        'imports_dir' => $importsDir,
                        'available_files' => array_filter($files, fn($f) => $f !== '.' && $f !== '..')
                    ]);

                    throw new \Exception('Temporary file not found. Please upload the file again. Searched path: ' . $fullPath);
                }
            }

            // Determine file type and read accordingly
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            if (strtolower($extension) === 'csv') {
                $data = $this->readCsvFile($fullPath);
            } else {
                $data = Excel::toArray(new \stdClass(), $fullPath)[0];
            }

            // Skip header row
            array_shift($data);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        continue;
                    }

                    // Extract data based on mapping
                    $orderNumber = $this->generateOrderNumber(); // Auto-generate order number
                    $clientName = isset($mapping['client']) && isset($row[$mapping['client']])
                                ? trim($row[$mapping['client']]) : null;

                    // Skip if required fields are missing
                    if (empty($clientName)) {
                        $skipped++;
                        $errors[] = "Row " . ($rowIndex + 2) . ": Missing required field (Client)";
                        continue;
                    }

                    // Find or create store by client name
                    $store = Store::where('name', 'LIKE', '%' . $clientName . '%')
                                 ->orWhere('client_name', 'LIKE', '%' . $clientName . '%')
                                 ->first();

                    if (!$store) {
                        // Create basic store entry for unknown clients
                        $store = Store::create([
                            'name' => $clientName,
                            'slug' => Str::slug($clientName) . '-' . time(),
                            'email' => strtolower(str_replace(' ', '', $clientName)) . time() . '@tempstore.com',
                            'password' => Hash::make('temporary123'),
                            'client_name' => $clientName,
                            'status' => 'pending', // Mark as pending for review
                        ]);
                    }

                    // Prepare order data with mapped fields
                    $orderData = [
                        'store_id' => $store->id,
                        'order_number' => $orderNumber,
                        'status' => 'pending',
                        'total_amount' => 0, // Will be calculated later when products are added
                    ];

                    // Map optional fields
                    $optionalFields = [
                        'cc' => 'cc',
                        'pia' => 'pia',
                        'pro' => 'pro',
                        'transport' => 'transport',
                        'transport_cost' => 'transport_cost',
                        'delivery_date' => 'delivery_date',
                        'phone' => 'phone',
                        'notes' => 'notes'
                    ];

                    foreach ($optionalFields as $fieldName => $dbField) {
                        if (isset($mapping[$fieldName]) && isset($row[$mapping[$fieldName]])) {
                            $value = trim($row[$mapping[$fieldName]]);

                            if (!empty($value)) {
                                if ($fieldName === 'transport_cost') {
                                    $orderData[$dbField] = is_numeric($value) ? (float)$value : 0;
                                } elseif ($fieldName === 'delivery_date') {
                                    try {
                                        $orderData[$dbField] = Carbon::parse($value)->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        // Invalid date format, skip
                                    }
                                } else {
                                    $orderData[$dbField] = $value;
                                }
                            }
                        }
                    }

                    // Create order
                    Order::create($orderData);
                    $imported++;

                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Clear session data
            session()->forget('import_file_info');

            DB::commit();

            // Prepare success message
            $message = "✅ Import completed successfully!\n\n";
            $message .= "📊 Summary:\n";
            $message .= "• Imported: {$imported} orders\n";
            $message .= "• Skipped: {$skipped} rows\n";

            if (!empty($errors)) {
                $message .= "\n⚠️ Issues encountered:\n";
                foreach (array_slice($errors, 0, 10) as $error) {
                    $message .= "• {$error}\n";
                }
                if (count($errors) > 10) {
                    $message .= "• ... and " . (count($errors) - 10) . " more issues\n";
                }
            }

            return redirect()->route('admin.import.orders')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up temporary file
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Clear session data
            session()->forget('import_file_info');

            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Process complete orders import with OrderItems (for complex CSV format)
     */
    public function processCompleteOrdersImport(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'mapping' => 'required|array',
            'mapping.fornitore' => 'required|numeric',
            'mapping.prodotto' => 'required|numeric',
            'mapping.codice' => 'required|numeric',
            'mapping.quantita' => 'required|numeric',
            'mapping.cliente' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $filePath = $request->file_path;
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));
            $mapping = $request->mapping;

            // Verify file still exists
            if (!file_exists($fullPath)) {
                // Log debug info
                \Log::error('File not found at initial path (complete import)', [
                    'requested_path' => $filePath,
                    'full_path' => $fullPath,
                    'storage_app_exists' => is_dir(storage_path('app')),
                    'temp_dir_exists' => is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'temp')),
                    'imports_dir_exists' => is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports'))
                ]);

                $sessionInfo = session('import_file_info');
                if ($sessionInfo && isset($sessionInfo['path'])) {
                    $filePath = $sessionInfo['path'];
                    $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));

                    \Log::info('Trying session fallback (complete import)', [
                        'session_path' => $filePath,
                        'full_path' => $fullPath,
                        'exists' => file_exists($fullPath)
                    ]);
                }

                if (!file_exists($fullPath)) {
                    // List files in imports directory for debugging
                    $importsDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');
                    $files = is_dir($importsDir) ? scandir($importsDir) : [];

                    \Log::error('File still not found after fallback (complete import)', [
                        'final_path' => $fullPath,
                        'imports_dir' => $importsDir,
                        'available_files' => array_filter($files, fn($f) => $f !== '.' && $f !== '..')
                    ]);

                    throw new \Exception('Temporary file not found. Please upload the file again. Searched path: ' . $fullPath);
                }
            }

            // Determine file type and read accordingly
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            if (strtolower($extension) === 'csv') {
                $data = $this->readCsvFile($fullPath);
            } else {
                $data = Excel::toArray(new \stdClass(), $fullPath)[0];
            }

            // Skip header row
            array_shift($data);

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $createdGrowers = 0;
            $createdProducts = 0;
            $createdStores = 0;
            $createdOrders = 0;
            $createdOrderItems = 0;

            // Group rows by order (Cliente + CC + PIA + PRO + Data)
            $orderGroups = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        continue;
                    }

                    // Extract required data
                    $clientName = isset($mapping['cliente']) && isset($row[$mapping['cliente']])
                                ? trim($row[$mapping['cliente']]) : null;
                    $cc = isset($mapping['cc']) && isset($row[$mapping['cc']])
                        ? trim($row[$mapping['cc']]) : '';
                    $pia = isset($mapping['pia']) && isset($row[$mapping['pia']])
                         ? trim($row[$mapping['pia']]) : '';
                    $pro = isset($mapping['pro']) && isset($row[$mapping['pro']])
                         ? trim($row[$mapping['pro']]) : '';
                    $data_consegna = isset($mapping['data']) && isset($row[$mapping['data']])
                                   ? trim($row[$mapping['data']]) : '';

                    if (empty($clientName)) {
                        $skipped++;
                        $errors[] = "Row " . ($rowIndex + 2) . ": Missing client name";
                        continue;
                    }

                    // Create order group key
                    $orderKey = $clientName . '|' . $cc . '|' . $pia . '|' . $pro . '|' . $data_consegna;

                    if (!isset($orderGroups[$orderKey])) {
                        $orderGroups[$orderKey] = [
                            'client_name' => $clientName,
                            'cc' => $cc,
                            'pia' => $pia,
                            'pro' => $pro,
                            'data_consegna' => $data_consegna,
                            'transport' => isset($mapping['trasporto']) && isset($row[$mapping['trasporto']])
                                         ? trim($row[$mapping['trasporto']]) : '',
                            'telefono' => isset($mapping['telefono']) && isset($row[$mapping['telefono']])
                                        ? trim($row[$mapping['telefono']]) : '',
                            'note' => isset($mapping['note']) && isset($row[$mapping['note']])
                                    ? trim($row[$mapping['note']]) : '',
                            'items' => []
                        ];
                    }

                    // Add item to group
                    $orderGroups[$orderKey]['items'][] = [
                        'row_index' => $rowIndex + 2,
                        'fornitore' => isset($mapping['fornitore']) && isset($row[$mapping['fornitore']])
                                     ? trim($row[$mapping['fornitore']]) : '',
                        'prodotto' => isset($mapping['prodotto']) && isset($row[$mapping['prodotto']])
                                    ? trim($row[$mapping['prodotto']]) : '',
                        'codice' => isset($mapping['codice']) && isset($row[$mapping['codice']])
                                  ? trim($row[$mapping['codice']]) : '',
                        'quantita' => isset($mapping['quantita']) && isset($row[$mapping['quantita']])
                                    ? (int)$row[$mapping['quantita']] : 0,
                        'prezzo' => isset($mapping['prezzo']) && isset($row[$mapping['prezzo']])
                                  ? $this->parsePrice($row[$mapping['prezzo']]) : 0,
                        'ean' => isset($mapping['ean']) && isset($row[$mapping['ean']])
                               ? trim($row[$mapping['ean']]) : '',
                        'altezza' => isset($mapping['altezza']) && isset($row[$mapping['altezza']])
                                   ? (int)$row[$mapping['altezza']] : 0,
                        'code' => isset($mapping['code']) && isset($row[$mapping['code']])
                                ? trim($row[$mapping['code']]) : '',
                        'piani' => isset($mapping['piani']) && isset($row[$mapping['piani']])
                                 ? (int)$row[$mapping['piani']] : 0,
                        'raw_row' => $row
                    ];

                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            // Process each order group
            foreach ($orderGroups as $orderKey => $group) {
                try {
                    // Find or create store
                    $store = Store::where('name', 'LIKE', '%' . $group['client_name'] . '%')
                                 ->orWhere('client_name', 'LIKE', '%' . $group['client_name'] . '%')
                                 ->first();

                    if (!$store) {
                        $store = Store::create([
                            'name' => $group['client_name'],
                            'slug' => Str::slug($group['client_name']) . '-' . time(),
                            'email' => strtolower(str_replace(' ', '', $group['client_name'])) . time() . '@tempstore.com',
                            'password' => Hash::make('temporary123'),
                            'client_name' => $group['client_name'],
                            'status' => 'pending',
                        ]);
                        $createdStores++;
                    }

                    // Create order
                    $orderNumber = $this->generateOrderNumber();

                    $order = Order::create([
                        'store_id' => $store->id,
                        'order_number' => $orderNumber,
                        'status' => 'pending',
                        'total_amount' => 0, // Will be calculated from items
                        'cc' => $group['cc'],
                        'pia' => $group['pia'],
                        'pro' => $group['pro'],
                        'transport' => $group['transport'],
                        'delivery_date' => $this->parseDate($group['data_consegna']),
                        'phone' => $group['telefono'],
                        'notes' => $group['note'],
                    ]);
                    $createdOrders++;

                    $totalAmount = 0;

                    // Process each item in the order
                    foreach ($group['items'] as $item) {
                        try {
                            // Find or create grower
                            $grower = null;
                            if (!empty($item['fornitore'])) {
                                $grower = Grower::where('name', 'LIKE', '%' . $item['fornitore'] . '%')->first();

                                if (!$grower) {
                                    $grower = Grower::create([
                                        'name' => $item['fornitore'],
                                        'code' => 'GRW-' . time() . '-' . rand(100, 999),
                                        'email' => strtolower(str_replace(' ', '', $item['fornitore'])) . '@grower.com',
                                        'password' => Hash::make('temporary123'),
                                        'is_active' => true,
                                    ]);
                                    $createdGrowers++;
                                }
                            }

                            // Find or create product
                            $product = null;
                            if (!empty($item['codice'])) {
                                $product = Product::where('code', $item['codice'])->first();

                                if (!$product) {
                                    $product = Product::create([
                                        'store_id' => $store->id,
                                        'grower_id' => $grower ? $grower->id : null,
                                        'name' => $item['prodotto'],
                                        'code' => $item['codice'],
                                        'ean' => $item['ean'],
                                        'height' => $item['altezza'],
                                        'price' => $item['prezzo'],
                                        'is_active' => true,
                                    ]);
                                    $createdProducts++;
                                }
                            }

                            // Create order item
                            $unitPrice = $item['prezzo'];
                            $quantity = $item['quantita'];
                            $totalItemPrice = $unitPrice * $quantity;

                            OrderItem::create([
                                'order_id' => $order->id,
                                'store_id' => $store->id,
                                'product_id' => $product ? $product->id : null,
                                'grower_id' => $grower ? $grower->id : null,
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'total_price' => $totalItemPrice,
                                'ean' => $item['ean'],
                                'product_snapshot' => json_encode([
                                    'name' => $item['prodotto'],
                                    'code' => $item['codice'],
                                    'grower' => $item['fornitore'],
                                    'height' => $item['altezza'],
                                    'ref_code' => $item['code'],
                                    'piani' => $item['piani']
                                ]),
                                'is_active' => true,
                            ]);
                            $createdOrderItems++;
                            $totalAmount += $totalItemPrice;
                            $imported++;

                        } catch (\Exception $e) {
                            $skipped++;
                            $errors[] = "Row " . $item['row_index'] . ": " . $e->getMessage();
                        }
                    }

                    // Update order total
                    $order->update(['total_amount' => $totalAmount]);

                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Order group '$orderKey': " . $e->getMessage();
                }
            }

            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Clear session data
            session()->forget('import_file_info');

            DB::commit();

            // Prepare success message
            $message = "✅ Complete import finished successfully!\n\n";
            $message .= "📊 Summary:\n";
            $message .= "• OrderItems imported: {$imported}\n";
            $message .= "• Orders created: {$createdOrders}\n";
            $message .= "• Stores created: {$createdStores}\n";
            $message .= "• Growers created: {$createdGrowers}\n";
            $message .= "• Products created: {$createdProducts}\n";
            $message .= "• Skipped rows: {$skipped}\n";

            if (!empty($errors)) {
                $message .= "\n⚠️ Issues encountered:\n";
                foreach (array_slice($errors, 0, 10) as $error) {
                    $message .= "• {$error}\n";
                }
                if (count($errors) > 10) {
                    $message .= "• ... and " . (count($errors) - 10) . " more issues\n";
                }
            }

            return redirect()->route('admin.import.orders')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up temporary file
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Clear session data
            session()->forget('import_file_info');

            return back()->withErrors(['error' => 'Complete import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Parse price from CSV (handles European format with comma)
     */
    private function parsePrice($priceString)
    {
        if (empty($priceString)) return 0;

        // Replace comma with dot for European format
        $price = str_replace(',', '.', $priceString);

        // Remove any non-numeric characters except dots
        $price = preg_replace('/[^0-9.]/', '', $price);

        return is_numeric($price) ? (float)$price : 0;
    }

    /**
     * Parse date from CSV (handles DD/MM/YYYY format)
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) return null;

        try {
            // Try to parse DD/MM/YYYY format
            if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $dateString, $matches)) {
                return Carbon::createFromFormat('d/m/Y', $matches[0])->format('Y-m-d');
            }

            // Fallback to Carbon parsing
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean old temporary files (older than 1 hour)
     */
    private function cleanOldTempFiles()
    {
        try {
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');

            if (!is_dir($tempDir)) {
                return;
            }

            $files = glob($tempDir . DIRECTORY_SEPARATOR . '*');
            $oneHourAgo = time() - 3600; // 1 hour

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $oneHourAgo) {
                    unlink($file);
                    \Log::info('Cleaned old temp file: ' . basename($file));
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to clean old temp files: ' . $e->getMessage());
        }
    }
}
