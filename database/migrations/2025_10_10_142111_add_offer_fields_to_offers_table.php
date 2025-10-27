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
        Schema::table('offers', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y'])->after('description');
            $table->decimal('discount_value', 10, 2)->nullable()->after('type'); // For percentage or fixed amount
            $table->integer('buy_quantity')->nullable()->after('discount_value'); // For buy X get Y offers
            $table->integer('get_quantity')->nullable()->after('buy_quantity'); // For buy X get Y offers
            $table->decimal('minimum_amount', 10, 2)->nullable()->after('get_quantity'); // Minimum order amount
            $table->integer('usage_limit')->nullable()->after('minimum_amount'); // How many times the offer can be used
            $table->integer('usage_count')->default(0)->after('usage_limit'); // How many times it has been used
            $table->boolean('is_active')->default(true)->after('usage_count');
            $table->datetime('start_date')->after('is_active');
            $table->datetime('end_date')->after('start_date');
            $table->string('code')->unique()->nullable()->after('end_date'); // Promo code
            $table->foreignId('grower_id')->nullable()->constrained()->onDelete('cascade')->after('code'); // If offer is grower-specific
            $table->json('applicable_products')->nullable()->after('grower_id'); // Array of product IDs
            $table->json('applicable_categories')->nullable()->after('applicable_products'); // Array of category names

            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index('code');
            $table->index('grower_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'start_date', 'end_date']);
            $table->dropIndex(['code']);
            $table->dropIndex(['grower_id']);

            $table->dropForeign(['grower_id']);
            $table->dropColumn([
                'title', 'description', 'type', 'discount_value', 'buy_quantity',
                'get_quantity', 'minimum_amount', 'usage_limit', 'usage_count',
                'is_active', 'start_date', 'end_date', 'code', 'grower_id',
                'applicable_products', 'applicable_categories'
            ]);
        });
    }
};
