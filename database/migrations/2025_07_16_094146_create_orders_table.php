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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Numero ordine generato automaticamente
            $table->foreignId('store_id')->constrained()->onDelete('cascade'); // Cliente
            $table->date('delivery_date')->nullable(); // Data di consegna
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2)->nullable(); // Totale ordine
            $table->integer('total_items')->default(0); // Numero totale articoli
            $table->text('notes')->nullable(); // Note ordine
            $table->string('transport')->nullable(); // Tipo di trasporto
            $table->string('address')->nullable(); // Indirizzo di consegna
            $table->string('phone')->nullable(); // Telefono di contatto
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indici per performance
            $table->index(['store_id', 'delivery_date']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
