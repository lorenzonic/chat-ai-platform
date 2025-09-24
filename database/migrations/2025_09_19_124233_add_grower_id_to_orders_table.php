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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('grower_id')->nullable()->after('store_id')->constrained()->onDelete('cascade');
            $table->index(['grower_id', 'store_id']); // Indice composto per performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['grower_id']);
            $table->dropIndex(['grower_id', 'store_id']);
            $table->dropColumn('grower_id');
        });
    }
};
