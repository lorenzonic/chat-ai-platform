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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('email')->index();
            $table->string('name')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('tag')->nullable(); // Es. "interessato_piante", "cliente_potenziale"
            $table->string('source')->default('chatbot'); // chatbot, qr_code, website
            $table->string('session_id')->nullable(); // Per collegare alla sessione chat
            $table->json('metadata')->nullable(); // Dati extra come pagina di origine, etc.
            $table->boolean('subscribed')->default(true); // Per newsletter
            $table->timestamp('last_interaction')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'email']); // Un email per store
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
