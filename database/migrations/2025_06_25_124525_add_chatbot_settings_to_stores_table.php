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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('assistant_name')->default('Assistente AI');
            $table->text('chat_context')->nullable(); // Contesto per addestrare la chat
            $table->json('opening_hours')->nullable(); // Orari di apertura
            $table->string('chat_theme_color')->default('#10b981'); // Colore tema
            $table->boolean('chat_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'assistant_name',
                'chat_context',
                'opening_hours',
                'chat_theme_color',
                'chat_enabled'
            ]);
        });
    }
};
