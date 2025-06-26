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
            $table->string('chat_font_family')->default('Inter')->after('chat_theme_color');
            $table->string('chat_ai_tone')->default('professional')->after('chat_font_family');
            $table->string('chat_avatar_image')->nullable()->after('chat_ai_tone');
            $table->json('chat_suggestions')->nullable()->after('chat_avatar_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['chat_font_family', 'chat_ai_tone', 'chat_avatar_image', 'chat_suggestions']);
        });
    }
};
