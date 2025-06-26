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
        Schema::create('store_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('question', 500); // Domanda o parola chiave
            $table->text('answer'); // Risposta personalizzata
            $table->json('keywords')->nullable(); // Parole chiave per il matching
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Priorità per l'ordine di ricerca
            $table->timestamps();

            $table->index(['store_id', 'is_active']);
            $table->index(['store_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_knowledge');
    }
};
