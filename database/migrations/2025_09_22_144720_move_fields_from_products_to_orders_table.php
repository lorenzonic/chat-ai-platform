<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            // Add fields that don't exist yet in orders
            if (!Schema::hasColumn('orders', 'client')) {
                $table->string('client')->nullable()->after('store_id');
            }
            if (!Schema::hasColumn('orders', 'cc')) {
                $table->string('cc')->nullable()->after('client');
            }
            if (!Schema::hasColumn('orders', 'pia')) {
                $table->string('pia')->nullable()->after('cc');
            }
            if (!Schema::hasColumn('orders', 'pro')) {
                $table->string('pro')->nullable()->after('pia');
            }
            if (!Schema::hasColumn('orders', 'transport_cost')) {
                $table->decimal('transport_cost', 10, 2)->nullable()->after('transport');
            }
        });

        // Step 2: Migrate data from products to orders
        // Note: This will group products by order_id and move common fields
        $this->migrateProductFieldsToOrders();

        // Step 3: Remove fields from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'client',
                'cc',
                'pia',
                'pro',
                'transport_cost',
                'delivery_date', // Already exists in orders
                'notes',         // Already exists in orders
                'address',       // Already exists in orders
                'phone'          // Already exists in orders
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Re-add fields to products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('client')->nullable();
            $table->string('cc')->nullable();
            $table->string('pia')->nullable();
            $table->string('pro')->nullable();
            $table->decimal('transport_cost', 10, 2)->nullable();
            $table->date('delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
        });

        // Step 2: Migrate data back from orders to products
        $this->migrateOrderFieldsToProducts();

        // Step 3: Remove fields from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'client',
                'cc',
                'pia',
                'pro',
                'transport_cost'
            ]);
        });
    }

    /**
     * Migrate product fields to orders
     */
    private function migrateProductFieldsToOrders(): void
    {
        // Get all products grouped by order_id
        $products = \DB::table('products')
            ->whereNotNull('order_id')
            ->get()
            ->groupBy('order_id');

        foreach ($products as $orderId => $orderProducts) {
            // Take the first product's values for order-level fields
            $firstProduct = $orderProducts->first();

            \DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'client' => $firstProduct->client,
                    'cc' => $firstProduct->cc,
                    'pia' => $firstProduct->pia,
                    'pro' => $firstProduct->pro,
                    'transport_cost' => $firstProduct->transport_cost,
                    // Don't overwrite existing order fields if they already have values
                    'delivery_date' => \DB::raw('COALESCE(delivery_date, "' . $firstProduct->delivery_date . '")'),
                    'notes' => \DB::raw('COALESCE(notes, "' . addslashes($firstProduct->notes ?? '') . '")'),
                    'address' => \DB::raw('COALESCE(address, "' . addslashes($firstProduct->address ?? '') . '")'),
                    'phone' => \DB::raw('COALESCE(phone, "' . ($firstProduct->phone ?? '') . '")')
                ]);
        }
    }

    /**
     * Migrate order fields back to products (for rollback)
     */
    private function migrateOrderFieldsToProducts(): void
    {
        $orders = \DB::table('orders')->get();

        foreach ($orders as $order) {
            \DB::table('products')
                ->where('order_id', $order->id)
                ->update([
                    'client' => $order->client,
                    'cc' => $order->cc,
                    'pia' => $order->pia,
                    'pro' => $order->pro,
                    'transport_cost' => $order->transport_cost,
                    'delivery_date' => $order->delivery_date,
                    'notes' => $order->notes,
                    'address' => $order->address,
                    'phone' => $order->phone
                ]);
        }
    }
};
