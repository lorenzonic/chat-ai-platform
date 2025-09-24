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
        Schema::create('trending_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->index(); // La query di tendenza
            $table->integer('score')->nullable(); // Valore tendenza (può essere null per "rising")
            $table->string('region', 10)->default('IT'); // Regione (es. 'IT')
            $table->timestamp('collected_at')->nullable(); // Timestamp di raccolta
            $table->string('parent_keyword')->nullable();
            $table->text('related_topics')->nullable();
            $table->timestamps();

            // Indice composto per evitare duplicati della stessa keyword nello stesso giorno
            $table->unique(['keyword', 'region', 'collected_at'], 'unique_keyword_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trending_keywords');
    }
};
