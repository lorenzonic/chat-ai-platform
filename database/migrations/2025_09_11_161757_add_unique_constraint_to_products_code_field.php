<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, remove any duplicate products by code
        // Keep only the latest created product for each code
        DB::statement("
            DELETE p1 FROM products p1
            INNER JOIN products p2
            WHERE p1.id < p2.id
            AND p1.code = p2.code
            AND p1.code IS NOT NULL
            AND p1.code != ''
        ");

        Schema::table('products', function (Blueprint $table) {
            // Add unique constraint to the code field
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique(['code']);
        });
    }
};
