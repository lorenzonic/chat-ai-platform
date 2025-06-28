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
        Schema::create('nlp_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('chat_log_id')->nullable();
            $table->text('user_message');
            $table->string('detected_intent', 50);
            $table->json('keywords');
            $table->json('entities');
            $table->json('suggestions');
            $table->string('source', 20)->default('spacy'); // spacy, fallback
            $table->float('confidence')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('chat_log_id')->references('id')->on('chat_logs')->onDelete('set null');
            $table->index(['store_id', 'detected_intent']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nlp_analyses');
    }
};
