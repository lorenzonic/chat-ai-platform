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
        Schema::table('leads', function (Blueprint $table) {
            // Coordinate geografiche
            $table->decimal('latitude', 10, 8)->nullable()->after('session_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Informazioni geografiche dettagliate
            $table->string('country')->nullable()->after('longitude'); // Es. "Italy"
            $table->string('country_code', 2)->nullable()->after('country'); // Es. "IT"
            $table->string('region')->nullable()->after('country_code'); // Es. "Lombardia"
            $table->string('city')->nullable()->after('region'); // Es. "Milano"
            $table->string('postal_code')->nullable()->after('city'); // Es. "20100"
            $table->string('timezone')->nullable()->after('postal_code'); // Es. "Europe/Rome"

            // IP Address per tracciamento
            $table->string('ip_address')->nullable()->after('timezone');

            // Indice per ricerche geografiche
            $table->index(['latitude', 'longitude']);
            $table->index(['country', 'region', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['country', 'region', 'city']);

            $table->dropColumn([
                'latitude',
                'longitude',
                'country',
                'country_code',
                'region',
                'city',
                'postal_code',
                'timezone',
                'ip_address'
            ]);
        });
    }
};
