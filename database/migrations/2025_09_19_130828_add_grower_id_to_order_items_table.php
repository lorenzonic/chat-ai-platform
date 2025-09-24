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
            $table->unsignedBigInteger('grower_id')->nullable()->after('product_id');
            $table->foreign('grower_id')->references('id')->on('growers');
            $table->index('grower_id');
        });

        // Popola grower_id dai prodotti associati
        \DB::statement('
            UPDATE order_items oi
            JOIN products p ON oi.product_id = p.id
            SET oi.grower_id = p.grower_id
            WHERE oi.grower_id IS NULL AND p.grower_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['grower_id']);
            $table->dropIndex(['grower_id']);
            $table->dropColumn('grower_id');
        });
    }
};
