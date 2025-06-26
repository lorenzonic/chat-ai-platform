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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // per collegare alle chat_logs
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ref_code')->nullable(); // QR code reference
            $table->foreignId('qr_code_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('duration')->nullable(); // durata in secondi
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->json('metadata')->nullable(); // dati extra
            $table->timestamps();

            // Indici per performance
            $table->index(['store_id', 'created_at']);
            $table->index('session_id');
            $table->index('ref_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
