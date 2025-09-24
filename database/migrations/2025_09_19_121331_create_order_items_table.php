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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // Prezzo al momento dell'ordine
            $table->decimal('total_price', 10, 2); // quantity * unit_price
            $table->json('product_snapshot')->nullable(); // Snapshot del prodotto al momento dell'ordine
            $table->string('sku')->nullable(); // SKU specifico se diverso dal prodotto
            $table->text('notes')->nullable(); // Note specifiche per questo item
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indici per performance
            $table->index(['order_id', 'product_id']);
            $table->index(['sku']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
