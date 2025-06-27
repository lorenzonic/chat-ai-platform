<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrCode;
use App\Models\Store;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class FixQrCodeUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:fix-urls {--regenerate : Regenerate QR code images with new URLs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix QR code URLs and optionally regenerate QR code images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Checking QR code URLs...');
        
        $qrCodes = QrCode::with('store')->get();
        
        if ($qrCodes->isEmpty()) {
            $this->warn('⚠️  No QR codes found');
            return 0;
        }
        
        $this->info("Found {$qrCodes->count()} QR codes");
        
        // Display current URLs
        $this->info('📋 Current QR code URLs:');
        $tableData = [];
        
        foreach ($qrCodes as $qrCode) {
            $currentUrl = $qrCode->getQrUrl();
            $tableData[] = [
                $qrCode->id,
                $qrCode->name,
                $qrCode->store->name,
                $currentUrl
            ];
        }
        
        $this->table(['ID', 'Name', 'Store', 'Target URL'], $tableData);
        
        // Check for problematic URLs
        $problematicUrls = $qrCodes->filter(function ($qrCode) {
            $url = $qrCode->getQrUrl();
            return str_contains($url, 'localhost') || 
                   str_contains($url, '${') || 
                   !str_starts_with($url, 'http');
        });
        
        if ($problematicUrls->count() > 0) {
            $this->error("❌ Found {$problematicUrls->count()} QR codes with problematic URLs");
            foreach ($problematicUrls as $qrCode) {
                $this->line("  - {$qrCode->name}: {$qrCode->getQrUrl()}");
            }
        } else {
            $this->info('✅ All QR code URLs look correct');
        }
        
        // Regenerate images if requested
        if ($this->option('regenerate')) {
            $this->regenerateQrCodeImages($qrCodes);
        }
        
        return 0;
    }
    
    private function regenerateQrCodeImages($qrCodes)
    {
        $this->info('🔄 Regenerating QR code images...');
        
        $bar = $this->output->createProgressBar($qrCodes->count());
        $bar->start();
        
        foreach ($qrCodes as $qrCode) {
            try {
                $qrUrl = $qrCode->getQrUrl();
                
                // Generate new QR code image
                $qrCodeImage = QrCodeGenerator::format('png')
                    ->size(300)
                    ->margin(1)
                    ->generate($qrUrl);
                
                // Save the image
                $fileName = 'qr_codes/qr_' . $qrCode->id . '_' . time() . '.png';
                Storage::disk('public')->put($fileName, $qrCodeImage);
                
                // Update the QR code record
                $qrCode->update(['qr_code_image' => $fileName]);
                
                $bar->advance();
                
            } catch (\Exception $e) {
                $this->error("\n❌ Failed to regenerate QR code {$qrCode->id}: " . $e->getMessage());
            }
        }
        
        $bar->finish();
        $this->info("\n✅ QR code images regenerated successfully!");
    }
}
