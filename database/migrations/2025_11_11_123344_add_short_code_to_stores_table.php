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
            $table->string('short_code', 5)->nullable()->unique()->after('slug');
            $table->index('short_code');
        });

        // Genera short code per store esistenti
        $this->generateShortCodes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['short_code']);
            $table->dropColumn('short_code');
        });
    }

    /**
     * Genera short code per tutti gli store esistenti
     */
    private function generateShortCodes(): void
    {
        $stores = \App\Models\Store::all();

        foreach ($stores as $store) {
            $store->short_code = $this->generateUniqueShortCode($store);
            $store->save();
        }
    }

    /**
     * Genera short code univoco (iniziale + ID)
     */
    private function generateUniqueShortCode(\App\Models\Store $store): string
    {
        // Iniziale + ID (es: "f1", "g2", "b3")
        $initial = strtolower(substr($store->slug, 0, 1));
        $shortCode = $initial . $store->id;

        // Verifica unicità
        $counter = 0;
        while (\App\Models\Store::where('short_code', $shortCode)->where('id', '!=', $store->id)->exists()) {
            $counter++;
            $shortCode = $initial . $store->id . $counter;
        }

        return $shortCode;
    }
};
