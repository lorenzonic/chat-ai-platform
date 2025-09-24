<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Grower;
use App\Models\Store;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $duplicateCount = 0; // Track duplicates separately
    protected $newGrowersCount = 0;
    protected $newStoresCount = 0;
    protected $newOrdersCount = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            $this->skippedCount++;
            return null;
        }

        try {
            // Find or create store based on client code
            $storeId = null;
            if (!empty($row['code'])) {
                $clientCode = trim($row['code']);

                $store = Store::firstOrCreate(
                    ['client_code' => $clientCode],
                    [
                        'name' => $clientCode,
                        'slug' => Str::slug($clientCode . '-' . time()),
                        'email' => 'temp-' . strtolower($clientCode) . '-' . time() . '@import.temp',
                        'is_active' => true,
                        'is_account_active' => false, // New stores are disabled by default
                        'password' => bcrypt('temp123'), // Temporary password
                    ]
                );

                if ($store->wasRecentlyCreated) {
                    $this->newStoresCount++;
                }

                $storeId = $store->id;
            }

            if (!$storeId) {
                $this->skippedCount++;
                return null; // Skip if no valid client code
            }

            // Find or create grower if fornitore is provided
            $growerId = null;
            if (!empty($row['fornitore'])) {
                $grower = Grower::firstOrCreate(
                    ['name' => trim($row['fornitore'])],
                    [
                        'code' => $this->generateGrowerCode($row['fornitore']),
                        'is_active' => true
                    ]
                );

                if ($grower->wasRecentlyCreated) {
                    $this->newGrowersCount++;
                }

                $growerId = $grower->id;
            }

            // Parse delivery date
            $deliveryDate = null;
            if (!empty($row['data'])) {
                try {
                    $dateStr = trim($row['data']);

                    // Try different date formats common in Italian CSV files
                    $formats = [
                        'd/m/Y',    // 15/07/2025
                        'd-m-Y',    // 15-07-2025
                        'Y-m-d',    // 2025-07-15
                        'd/m/y',    // 15/07/25
                        'd-m-y',    // 15-07-25
                    ];

                    $parsed = false;
                    foreach ($formats as $format) {
                        try {
                            $deliveryDate = Carbon::createFromFormat($format, $dateStr)->format('Y-m-d');
                            $parsed = true;
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if (!$parsed) {
                        \Log::warning('Could not parse delivery date with any format', [
                            'row_data' => $row['data'],
                            'tried_formats' => $formats
                        ]);
                    }
                } catch (\Exception $e) {
                    // If date parsing fails, leave as null
                    \Log::warning('Could not parse delivery date', [
                        'row_data' => $row['data'],
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                \Log::info('No delivery date provided in row', ['row_keys' => array_keys($row)]);
            }

            // Find or create order based on store and delivery date
            // Create order even without delivery date to group products
            $orderId = null;
            if ($storeId) {
                // Use delivery date if available, otherwise use a default date (today)
                $orderDate = $deliveryDate ?: now()->format('Y-m-d');

                $order = Order::firstOrCreate(
                    [
                        'store_id' => $storeId,
                        'delivery_date' => $orderDate
                    ],
                    [
                        'order_number' => Order::generateOrderNumber(),
                        'status' => 'pending',
                        'transport' => trim($row['trasporto'] ?? ''),
                        'address' => trim($row['indirizzo'] ?? ''),
                        'phone' => trim($row['telefono'] ?? ''),
                        'notes' => trim($row['note'] ?? ''),
                        'is_active' => true
                    ]
                );

                if ($order->wasRecentlyCreated) {
                    $this->newOrdersCount++;
                    \Log::info('Created new order', [
                        'order_number' => $order->order_number,
                        'store_id' => $storeId,
                        'delivery_date' => $orderDate
                    ]);
                }

                $orderId = $order->id;
            }

            // Parse numeric values with better error handling
            $quantity = $this->parseNumericValue($row['quantita'] ?? '', 'int', 0);
            $height = $this->parseNumericValue($row['h'] ?? '', 'float', null);
            $price = $this->parseNumericValue($row['eur_vendita'] ?? '', 'float', null);

            // Get product code
            $productCode = isset($row['codice']) ? trim((string)$row['codice']) : '';

            // Check if product with this code already exists (only if code is not empty)
            if (!empty($productCode)) {
                $existingProduct = Product::where('code', $productCode)->first();
                if ($existingProduct) {
                    // Product already exists, skip importing
                    $this->duplicateCount++;
                    \Log::info('Product with code already exists, skipping', [
                        'code' => $productCode,
                        'existing_product_id' => $existingProduct->id,
                        'existing_product_name' => $existingProduct->name
                    ]);
                    return null;
                }
            }

            $product = new Product([
                'store_id' => $storeId,
                'order_id' => $orderId,
                'grower_id' => $growerId,
                'name' => trim($row['prodotto'] ?? ''),
                'code' => $productCode,
                'ean' => trim($row['ean'] ?? ''),
                'quantity' => $quantity,
                'height' => $height,
                'price' => $price, // Prezzo di rivendita
                'category' => trim($row['categoria'] ?? $row['piante_per_cc'] ?? ''),
                'client' => trim($row['cliente'] ?? ''),
                'cc' => trim($row['cc'] ?? ''),
                'pia' => trim($row['pia'] ?? ''),
                'pro' => trim($row['pro'] ?? ''),
                'transport' => trim($row['trasporto'] ?? ''), // Tipo di trasporto (stringa)
                'delivery_date' => $deliveryDate,
                'notes' => trim($row['note'] ?? ''),
                'address' => trim($row['indirizzo'] ?? ''),
                'phone' => trim($row['telefono'] ?? ''),
                'is_active' => true
            ]);

            $this->importedCount++;
            return $product;

        } catch (\Exception $e) {
            \Log::error('Error importing product row', [
                'row' => $row,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $this->skippedCount++;
            return null;
        }
    }

    /**
     * Generate a unique code for grower
     */
    private function generateGrowerCode($name)
    {
        $baseCode = Str::upper(Str::substr(Str::slug($name, ''), 0, 3));
        $counter = 1;
        $code = $baseCode . sprintf('%03d', $counter);

        while (Grower::where('code', $code)->exists()) {
            $counter++;
            $code = $baseCode . sprintf('%03d', $counter);
        }

        return $code;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'prodotto' => 'required|string|max:255',
            'code' => 'required|string|max:100', // Client code is required
            'codice' => 'required|max:100', // Product code is required and must be unique
            'quantita' => 'nullable', // Allow any value, we'll parse it manually
            'eur_vendita' => 'nullable', // Allow any value, we'll parse it manually
            'h' => 'nullable', // Allow any value, we'll parse it manually
            'trasporto' => 'nullable|string|max:255', // Transport type (string description)
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'prodotto.required' => 'Il nome del prodotto è obbligatorio.',
            'code.required' => 'Il codice cliente (CODE) è obbligatorio.',
            'codice.required' => 'Il codice prodotto (CODICE) è obbligatorio per garantire unicità.',
            'quantita.numeric' => 'La quantità deve essere un numero.',
            'eur_vendita.numeric' => 'Il prezzo deve essere un numero.',
            'h.numeric' => 'L\'altezza deve essere un numero.',
            'trasporto.string' => 'Il trasporto deve essere una descrizione testuale.',
        ];
    }

    /**
     * Update order totals after import completion
     */
    public function updateOrderTotals(): void
    {
        // Get all orders that were created/updated during this import
        $recentOrders = Order::where('updated_at', '>=', now()->subMinutes(5))->get();

        foreach ($recentOrders as $order) {
            $order->calculateTotals();
        }
    }

    /**
     * Parse numeric values with better error handling
     */
    private function parseNumericValue($value, $type = 'float', $default = null)
    {
        // Handle null values
        if ($value === null) {
            return $default;
        }

        // Convert to string and clean up
        $cleanValue = trim((string) $value);

        // Handle empty string
        if ($cleanValue === '') {
            return $default;
        }

        // Replace comma with dot for decimal separator
        $cleanValue = str_replace(',', '.', $cleanValue);

        // Remove any non-numeric characters except dot and minus
        $cleanValue = preg_replace('/[^0-9.-]/', '', $cleanValue);

        // Check if the cleaned value is numeric
        if (!is_numeric($cleanValue)) {
            return $default;
        }

        // Convert to the requested type
        if ($type === 'int') {
            return (int) $cleanValue;
        } else {
            return (float) $cleanValue;
        }
    }

    // Getters for statistics
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getNewGrowersCount(): int
    {
        return $this->newGrowersCount;
    }

    public function getNewStoresCount(): int
    {
        return $this->newStoresCount;
    }

    public function getNewOrdersCount(): int
    {
        return $this->newOrdersCount;
    }

    public function getDuplicateCount(): int
    {
        return $this->duplicateCount;
    }
}
