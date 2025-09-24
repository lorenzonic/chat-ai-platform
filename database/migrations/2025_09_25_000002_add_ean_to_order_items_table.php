<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'ean')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('ean')->nullable()->after('prezzo_rivendita');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'ean')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('ean');
            });
        }
    }
};
