<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'prezzo_rivendita')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('prezzo_rivendita', 10, 2)->default(0)->after('unit_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'prezzo_rivendita')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('prezzo_rivendita');
            });
        }
    }
};
