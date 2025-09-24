<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('store_knowledge', function (Blueprint $table) {
            $table->longText('embedding')->nullable()->after('answer');
        });
    }

    public function down(): void
    {
        Schema::table('store_knowledge', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
