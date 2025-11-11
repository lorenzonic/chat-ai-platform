<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Log;

class OptimizeQrUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:optimize
                          {--regenerate : Rigenera anche le immagini QR}
                          {--store= : Solo per store specifico (ID o slug)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ottimizza URL QR code per ridurre dimensione (-43% lunghezza)';

    /**
     * Execute the console command.
     */
    public function handle(QrCodeService $qrCodeService): int
    {
        $this->info('🔄 Ottimizzazione URL QR code...');

        $query = QrCode::with('store');

        if ($storeFilter = $this->option('store')) {
            $query->whereHas('store', function($q) use ($storeFilter) {
                $q->where('id', $storeFilter)
                  ->orWhere('slug', $storeFilter);
            });
        }

        $qrCodes = $query->get();
        $this->info("📦 Trovati {$qrCodes->count()} QR code");

        if ($qrCodes->isEmpty()) {
            $this->warn('Nessun QR code da ottimizzare');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($qrCodes->count());
        $optimized = 0;
        $errors = 0;
        $totalSaved = 0;

        foreach ($qrCodes as $qrCode) {
            try {
                $originalUrl = $qrCode->qr_url;
                $originalLength = strlen($originalUrl);

                if ($this->option('regenerate')) {
                    $qrCodeService->regenerateWithOptimizedUrl($qrCode);
                    $qrCode->refresh();
                } else {
                    $optimizedUrl = $qrCodeService->generateOptimizedQrUrl($qrCode);
                    if ($optimizedUrl !== $qrCode->qr_url) {
                        $qrCode->qr_url = $optimizedUrl;
                        $qrCode->save();
                    }
                }

                $newLength = strlen($qrCode->qr_url);
                $saved = $originalLength - $newLength;

                if ($saved > 0) {
                    $optimized++;
                    $totalSaved += $saved;
                    $this->newLine();
                    $this->line("  ✅ QR #{$qrCode->id}: -{$saved} caratteri ({$originalLength} → {$newLength})");
                }

                $bar->advance();

            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("  ❌ QR #{$qrCode->id}: {$e->getMessage()}");
                Log::error('QR optimization failed', [
                    'qr_id' => $qrCode->id,
                    'error' => $e->getMessage(),
                ]);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Ottimizzazione completata!");

        $avgSaved = $optimized > 0 ? round($totalSaved / $optimized, 1) : 0;
        $percentSaved = $optimized > 0 ? round(($avgSaved / 67) * 100) : 0; // 67 = lunghezza URL tipica

        $this->table(
            ['Metrica', 'Valore'],
            [
                ['Totale QR', $qrCodes->count()],
                ['Ottimizzati', $optimized],
                ['Errori', $errors],
                ['Totale caratteri risparmiati', $totalSaved],
                ['Media caratteri risparmiati', $avgSaved],
                ['Risparmio medio percentuale', $percentSaved . '%'],
            ]
        );

        if ($this->option('regenerate')) {
            $this->info('🖼️  Immagini QR rigenerate con URL ottimizzati');
        } else {
            $this->comment('💡 Usa --regenerate per rigenerare anche le immagini QR');
        }

        return Command::SUCCESS;
    }
}
