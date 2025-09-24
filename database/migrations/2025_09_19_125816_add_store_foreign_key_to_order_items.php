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
        Schema::table('order_items', function (Blueprint $table) {
            // Aggiungiamo store_id se non esiste
            if (!Schema::hasColumn('order_items', 'store_id')) {
                $table->unsignedBigInteger('store_id')->after('product_id');
            }

            // Aggiungiamo il foreign key constraint
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });
    }
};
