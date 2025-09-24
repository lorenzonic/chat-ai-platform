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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Nome descrittivo del QR
            $table->text('question')->nullable(); // Domanda precompilata
            $table->string('qr_code_image')->nullable(); // Path immagine QR
            $table->string('ref_code')->unique(); // Codice univoco per tracking (es. qr123)
            $table->string('ean_code')->nullable()->unique(); // EAN/GTIN for GS1 QR
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
