<?php

namespace App\Services;

use App\Models\Store;
use App\Models\QrCode;
use App\Models\OrderItem;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate QR code SVG for thermal printing
     * Optimized for 50mm x 25mm labels with maximum scannability
     *
     * @param string $url URL to encode in QR
     * @param string|null $logoPath Optional path to logo image (absolute path or storage path)
     * @param int $logoSize Logo size in pixels (default 120 = 30% of 400px QR)
     */
    public function generateThermalPrintQrSvg(string $url, ?string $logoPath = null, int $logoSize = 120): string
    {
        try {
            // Check if logo exists and is valid
            $hasValidLogo = $logoPath && file_exists($logoPath);

            // Use Low error correction for optimized QR code density
            // Less black dots = easier scanning
            $errorCorrection = $hasValidLogo
                ? ErrorCorrectionLevel::Medium  // 15% recovery with logo
                : ErrorCorrectionLevel::Low;     // 7% recovery - minimal density

            $builder = Builder::create()
                ->writer(new SvgWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel($errorCorrection)
                ->size(400) // High resolution for crisp thermal printing
                ->margin(1) // Minimal margin to maximize QR size on label
                ->roundBlockSizeMode(RoundBlockSizeMode::Enlarge) // Sharp pixel edges, no anti-aliasing
                ->validateResult(false); // Skip validation for performance

            // Add logo only if valid
            if ($hasValidLogo) {
                $builder->logoPath($logoPath)
                       ->logoResizeToWidth($logoSize)
                       ->logoResizeToHeight($logoSize);
            }

            $result = $builder->build();
            return $result->getString();
        } catch (\Exception $e) {
            Log::error('QR Code generation failed (thermal print)', [
                'url' => $url,
                'logo_path' => $logoPath,
                'error' => $e->getMessage()
            ]);

            // Fallback to simple QR
            return $this->generateSimpleQrSvg($url, 400);
        }
    }

    /**
     * Generate QR code PNG for storage/downloads
     */
    public function generateThermalPrintQrPng(string $url): string
    {
        try {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(600) // Extra large for thermal printer
                ->margin(0)
                ->roundBlockSizeMode(RoundBlockSizeMode::Enlarge)
                ->build();

            return $result->getString();
        } catch (\Exception $e) {
            Log::error('QR Code PNG generation failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return $this->generateSimpleQrSvg($url, 600);
        }
    }

    /**
     * Generate QR code for store chatbot with optional logo
     */
    public function generateStoreChatbotQr(Store $store, string $refCode, bool $withLogo = false): string
    {
        $url = route('store.chatbot', ['store' => $store->slug]) . '?ref_code=' . $refCode;

        try {
            $builder = Builder::create()
                ->writer(new SvgWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High) // High needed for logo
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin);

            // Add store logo if available and requested
            if ($withLogo && $store->logo) {
                $logoPath = storage_path('app/public/' . $store->logo);
                if (file_exists($logoPath)) {
                    $builder->logoPath($logoPath)
                           ->logoResizeToWidth(60) // 20% of QR size
                           ->logoResizeToHeight(60);
                }
            }

            $result = $builder->build();
            return $result->getString();
        } catch (\Exception $e) {
            Log::error('Store chatbot QR generation failed', [
                'store_id' => $store->id,
                'error' => $e->getMessage()
            ]);

            return $this->generateSimpleQrSvg($url, 300);
        }
    }

    /**
     * Generate QR code image file and save to storage
     */
    public function generateAndSaveQrImage(string $url, string $filename, string $format = 'svg'): string
    {
        try {
            $writer = $format === 'png' ? new PngWriter() : new SvgWriter();

            $result = Builder::create()
                ->writer($writer)
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(1)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->build();

            $filepath = 'qr-codes/' . $filename . '.' . $format;
            Storage::disk('public')->put($filepath, $result->getString());

            return $filepath;
        } catch (\Exception $e) {
            Log::error('QR Code file generation failed', [
                'url' => $url,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate QR code for product with optional grower logo (future marketplace)
     */
    public function generateProductQr(string $url, ?string $logoPath = null): string
    {
        try {
            $builder = Builder::create()
                ->writer(new SvgWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10);

            // Add grower logo if provided (marketplace feature)
            if ($logoPath && file_exists($logoPath)) {
                $builder->logoPath($logoPath)
                       ->logoResizeToWidth(60)
                       ->logoResizeToHeight(60);
            }

            $result = $builder->build();
            return $result->getString();
        } catch (\Exception $e) {
            Log::error('Product QR generation failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return $this->generateSimpleQrSvg($url, 300);
        }
    }

    /**
     * Fallback: Simple QR code generation without advanced features
     */
    private function generateSimpleQrSvg(string $url, int $size): string
    {
        try {
            $result = Builder::create()
                ->writer(new SvgWriter())
                ->data($url)
                ->size($size)
                ->margin(0)
                ->build();

            return $result->getString();
        } catch (\Exception $e) {
            Log::error('Simple QR fallback also failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            // Ultimate fallback - return placeholder SVG
            return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '"><rect width="100%" height="100%" fill="#ddd"/><text x="50%" y="50%" text-anchor="middle" fill="#999">QR Error</text></svg>';
        }
    }

    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Genera URL ottimizzato per QR code
     * Riduce lunghezza URL mantenendo compatibilità GS1
     */
    public function generateOptimizedQrUrl(QrCode $qrCode): string
    {
        $store = $qrCode->store;

        if (!$store) {
            return $qrCode->qr_url ?: ''; // Fallback
        }

        // Se URL è vuoto, prova a generare da EAN code
        if (empty($qrCode->qr_url) && $qrCode->ean_code) {
            $gtin14 = '0' . $qrCode->ean_code; // Indicator 0 = consumer unit
            return $store->getShortQrUrl($gtin14, $qrCode->ref_code);
        }

        // Estrai GTIN-14 dall'URL originale (formato GS1 Digital Link)
        if (preg_match('/\/01\/(\d{14})/', $qrCode->qr_url, $matches)) {
            $gtin14 = $matches[1];

            // Estrai ref code se presente
            $refCode = null;
            if (preg_match('/[?&]ref=([^&]+)/', $qrCode->qr_url, $refMatches)) {
                $refCode = $refMatches[1];
            } elseif ($qrCode->ref_code) {
                $refCode = $qrCode->ref_code;
            }

            // Usa short URL del store
            return $store->getShortQrUrl($gtin14, $refCode);
        }

        return $qrCode->qr_url ?: ''; // Fallback
    }

    /**
     * Rigenera QR con URL ottimizzato
     * Aggiorna database e rigenera immagine
     */
    public function regenerateWithOptimizedUrl(QrCode $qrCode): void
    {
        $optimizedUrl = $this->generateOptimizedQrUrl($qrCode);

        if ($optimizedUrl !== $qrCode->qr_url) {
            $oldUrl = $qrCode->qr_url;
            $qrCode->qr_url = $optimizedUrl;
            $qrCode->save();

            Log::info('QR URL optimized', [
                'qr_id' => $qrCode->id,
                'old_length' => strlen($oldUrl),
                'new_length' => strlen($optimizedUrl),
                'saved' => strlen($oldUrl) - strlen($optimizedUrl),
            ]);
        }
    }
}
