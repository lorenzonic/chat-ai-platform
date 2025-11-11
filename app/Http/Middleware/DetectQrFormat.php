<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DetectQrFormat
{
    /**
     * Rileva formato QR e gestisce redirect intelligente
     * Pattern: /{short_code}/01/{gtin14}?r={ref}
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pattern: /{short_code}/01/{gtin14}
        $path = $request->path();

        if (preg_match('/^([a-z]\d+)\/01\/(\d{14})/', $path, $matches)) {
            $shortCode = $matches[1];
            $gtin14 = $matches[2];

            // Trova store
            $store = \App\Models\Store::where('short_code', $shortCode)->first();

            if (!$store) {
                abort(404, 'Store not found');
            }

            // Estrai ref code
            $refCode = $request->query('r') ?? $request->query('ref');

            // Rileva tipo di scanner
            $userAgent = $request->userAgent();
            $isScanner = $this->isRetailScanner($userAgent);

            if ($isScanner) {
                // Scanner retail → risposta GS1 standard
                return $this->handleScannerRequest($store, $gtin14, $refCode);
            }

            // Browser/smartphone → redirect a chatbot
            return $this->handleBrowserRequest($request, $store, $gtin14, $refCode);
        }

        return $next($request);
    }

    /**
     * Rileva se è uno scanner retail (non browser)
     */
    private function isRetailScanner(string $userAgent): bool
    {
        $scannerPatterns = [
            '/scanner/i',
            '/barcode/i',
            '/zebra/i',
            '/honeywell/i',
            '/datalogic/i',
            '/^curl/i',  // Scanner usano spesso curl
        ];

        foreach ($scannerPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        // Se NON è un browser comune, assume scanner
        $browserPatterns = [
            '/Mozilla/i',
            '/Chrome/i',
            '/Safari/i',
            '/Edge/i',
            '/Opera/i',
        ];

        $isBrowser = false;
        foreach ($browserPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $isBrowser = true;
                break;
            }
        }

        return !$isBrowser;
    }

    /**
     * Gestisce richiesta da scanner retail
     */
    private function handleScannerRequest($store, string $gtin14, ?string $refCode): Response
    {
        // Log scan event
        $this->logScanEvent($store, $gtin14, $refCode, 'scanner');

        // Trova prodotto da GTIN-14
        $ean13 = substr($gtin14, 1); // Rimuovi indicator digit

        $product = \App\Models\Product::where('store_id', $store->id)
            ->where('ean', $ean13)
            ->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'gtin' => $gtin14,
                'ean13' => $ean13,
                'store' => $store->name,
            ], 404);
        }

        // Risposta GS1 standard per scanner
        return response()->json([
            'productId' => $product->id,
            'gtin' => $gtin14,
            'ean13' => $ean13,
            'name' => $product->name,
            'price' => $product->price ?? 0,
            'stock' => $product->stock ?? 0,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
            ],
            'ref' => $refCode,
        ]);
    }

    /**
     * Gestisce richiesta da browser (redirect a chatbot)
     */
    private function handleBrowserRequest(Request $request, $store, string $gtin14, ?string $refCode): Response
    {
        // Log scan event
        $this->logScanEvent($store, $gtin14, $refCode, 'browser');

        // Trova QR code e prodotto per ottenere la question
        $ean13 = substr($gtin14, 1); // Rimuovi indicator digit
        $qrCode = null;
        $product = null;
        $question = null;

        // Cerca QR code tramite ref_code o EAN
        if ($refCode) {
            $qrCode = \App\Models\QrCode::where('store_id', $store->id)
                ->where('ref_code', $refCode)
                ->first();
        }

        if (!$qrCode && $ean13) {
            $qrCode = \App\Models\QrCode::where('store_id', $store->id)
                ->where('ean_code', $ean13)
                ->first();
        }

        // Ottieni question dal QR code o genera default
        if ($qrCode && $qrCode->question) {
            $question = $qrCode->question;
        } elseif ($ean13) {
            // Cerca prodotto per nome
            $product = \App\Models\Product::where('store_id', $store->id)
                ->where('ean', $ean13)
                ->first();

            if ($product) {
                $question = "Come si cura {$product->name}?";
            }
        }

        // Costruisci URL completo chatbot
        $chatbotUrl = url("/{$store->slug}");

        // Aggiungi parametri
        $params = [];

        if ($refCode) {
            $params['ref'] = $refCode;
        }

        // Aggiungi GTIN come context per chatbot
        $params['product'] = $gtin14;

        // Aggiungi question se disponibile (NON nel QR code, solo nel redirect)
        if ($question) {
            $params['question'] = $question;
        }

        if (!empty($params)) {
            $chatbotUrl .= '?' . http_build_query($params);
        }

        return redirect($chatbotUrl);
    }

    /**
     * Log evento di scansione
     */
    private function logScanEvent($store, string $gtin14, ?string $refCode, string $type): void
    {
        try {
            // Incrementa contatore QR
            \App\Models\QrCode::where('store_id', $store->id)
                ->where('qr_url', 'LIKE', "%/01/{$gtin14}%")
                ->increment('scan_count');

            // Log dettagliato
            DB::table('qr_scan_logs')->insert([
                'store_id' => $store->id,
                'gtin14' => $gtin14,
                'ref_code' => $refCode,
                'scan_type' => $type,
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('QR scan log failed', [
                'error' => $e->getMessage(),
                'store' => $store->id,
                'gtin' => $gtin14,
            ]);
        }
    }
}
