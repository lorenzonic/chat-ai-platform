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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('stores', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('stores', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('stores', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (!Schema::hasColumn('stores', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('stores', 'website')) {
                $table->string('website')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'state',
                'postal_code',
                'country',
                'website'
            ]);
        });
    }
};
