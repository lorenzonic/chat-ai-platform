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
        Schema::table('interactions', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('metadata');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('city')->nullable()->after('longitude');
            $table->string('region')->nullable()->after('city');
            $table->string('country')->nullable()->after('region');
            $table->string('country_code', 2)->nullable()->after('country');
            $table->string('postal_code')->nullable()->after('country_code');
            $table->string('timezone')->nullable()->after('postal_code');

            // Add index for geographic queries
            $table->index(['latitude', 'longitude']);
            $table->index('country');
            $table->index(['store_id', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['interactions_country_index']);
            $table->dropIndex(['interactions_store_id_country_index']);

            $table->dropColumn([
                'latitude',
                'longitude',
                'city',
                'region',
                'country',
                'country_code',
                'postal_code',
                'timezone'
            ]);
        });
    }
};
