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
        Schema::create('order_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->decimal('discount_amount', 10, 2); // The actual discount applied
            $table->string('offer_code')->nullable(); // The promo code used
            $table->json('offer_snapshot')->nullable(); // Snapshot of offer details at time of application
            $table->timestamps();

            $table->unique(['order_id', 'offer_id']);
            $table->index('order_id');
            $table->index('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_offers');
    }
};
