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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('grower_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('ean')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('height', 8, 2)->nullable(); // Altezza in cm
            $table->decimal('price', 10, 2)->nullable(); // Prezzo vendita
            $table->string('category')->nullable(); // es: "Piante per cc"
            $table->string('client')->nullable(); // Cliente
            $table->string('cc')->nullable(); // CC
            $table->string('pia')->nullable(); // PIA
            $table->string('pro')->nullable(); // PRO
            $table->decimal('transport_cost', 10, 2)->nullable(); // Costo trasporto
            $table->date('delivery_date')->nullable(); // Data consegna
            $table->text('notes')->nullable();
            $table->string('address')->nullable(); // Indirizzo cliente
            $table->string('phone')->nullable(); // Telefono cliente
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['store_id', 'code']);
            $table->index(['store_id', 'ean']);
            $table->index(['grower_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
