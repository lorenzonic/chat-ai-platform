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
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->text('qr_url')->nullable()->after('ref_code');
        });

        // Popola qr_url per QR esistenti
        $this->populateQrUrls();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn('qr_url');
        });
    }

    /**
     * Popola qr_url per QR codes esistenti
     */
    private function populateQrUrls(): void
    {
        $qrCodes = \App\Models\QrCode::with('store')->get();

        foreach ($qrCodes as $qrCode) {
            if (!$qrCode->store) {
                continue;
            }

            // Genera URL basato su EAN o ref_code
            if ($qrCode->ean_code) {
                $gtin14 = '0' . $qrCode->ean_code;
                $qrCode->qr_url = url("/{$qrCode->store->slug}/01/{$gtin14}") .
                                ($qrCode->ref_code ? "?ref={$qrCode->ref_code}" : '');
            } else {
                $qrCode->qr_url = url("/{$qrCode->store->slug}") .
                                ($qrCode->ref_code ? "?ref={$qrCode->ref_code}" : '');
            }

            $qrCode->save();
        }
    }
};
