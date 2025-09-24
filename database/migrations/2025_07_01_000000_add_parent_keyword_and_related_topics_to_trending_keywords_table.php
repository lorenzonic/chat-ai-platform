<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trending_keywords', function (Blueprint $table) {
            $table->string('parent_keyword')->nullable()->after('region');
            $table->text('related_topics')->nullable()->after('parent_keyword');
        });
    }

    public function down(): void
    {
        Schema::table('trending_keywords', function (Blueprint $table) {
            $table->dropColumn(['parent_keyword', 'related_topics']);
        });
    }
};
