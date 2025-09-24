<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class LabelService
{
    private $barcodeGenerator;
    private $barcodeGeneratorHTML;

    public function __construct()
    {
        $this->barcodeGenerator = new BarcodeGeneratorPNG();
        $this->barcodeGeneratorHTML = new BarcodeGeneratorHTML();
    }

    /**
     * Generate EAN-13 barcode for product
     */
    public function generateEANBarcode($product)
    {
        // Generate EAN-13 code based on product ID
        $eanCode = $this->generateEANCode($product->id);

        // Generate barcode as base64 image
        $barcode = base64_encode($this->barcodeGenerator->getBarcode($eanCode, $this->barcodeGenerator::TYPE_EAN_13));

        return [
            'code' => $eanCode,
            'image' => 'data:image/png;base64,' . $barcode
        ];
    }

    /**
     * Generate EAN-13 barcode as HTML (for better scaling)
     */
    public function generateEANBarcodeHTML($product)
    {
        $eanCode = $this->generateEANCode($product->id);

        $barcodeHTML = $this->barcodeGeneratorHTML->getBarcode($eanCode, $this->barcodeGeneratorHTML::TYPE_EAN_13);

        return [
            'code' => $eanCode,
            'html' => $barcodeHTML
        ];
    }

    /**
     * Generate QR code for plant care chat
     */
    public function generateCareQRCode($product, $store)
    {
        // Create the chat URL with pre-filled question
        $careQuestion = "Come si cura " . $product->name . "?";
        $chatUrl = url("/{$store->slug}") . "?q=" . urlencode($careQuestion);

        // Generate QR code as SVG (doesn't require Imagick)
        $qrCodeSvg = QrCode::format('svg')->size(200)->generate($chatUrl);

        return [
            'url' => $chatUrl,
            'svg' => $qrCodeSvg,
            'question' => $careQuestion
        ];
    }

    /**
     * Generate EAN-13 code from product ID
     */
    private function generateEANCode($productId)
    {
        // Start with country code (80 for Italy)
        $code = '80';

        // Add company code (5 digits) - using store/company identifier
        $code .= str_pad('12345', 5, '0', STR_PAD_LEFT);

        // Add product code (5 digits) - based on product ID
        $code .= str_pad($productId, 5, '0', STR_PAD_LEFT);

        // Calculate check digit
        $checkDigit = $this->calculateEANCheckDigit($code);
        $code .= $checkDigit;

        return $code;
    }

    /**
     * Calculate EAN-13 check digit
     */
    private function calculateEANCheckDigit($code)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $code[$i] * (($i % 2 == 0) ? 1 : 3);
        }
        return (10 - ($sum % 10)) % 10;
    }

    /**
     * Format price for label display
     */
    public function formatPrice($price)
    {
        return '€ ' . number_format($price, 2, ',', '.');
    }

    /**
     * Generate complete label data for a product
     */
    public function generateLabelData($product, $store)
    {
        return [
            'product' => $product,
            'store' => $store,
            'barcode' => $this->generateEANBarcodeHTML($product),
            'qrcode' => $this->generateCareQRCode($product, $store),
            'price' => $this->formatPrice($product->price),
            'formatted_price' => $this->formatPrice($product->price)
        ];
    }
}
