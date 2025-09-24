<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Admin\ImportController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedulazione aggiornamento Google Trends
Schedule::command('trends:update')
    ->daily()
    ->at('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Log::info('Aggiornamento Google Trends schedulato completato con successo');
    })
    ->onFailure(function () {
        \Log::error('Aggiornamento Google Trends schedulato fallito');
    });

// Import ordini da CLI, riutilizzando lo stesso flusso del controller
Artisan::command('orders:import {file : Percorso del file CSV} {--dry-run : Esegue senza salvare nel DB}', function () {
    $path = $this->argument('file');
    $dryRun = (bool) $this->option('dry-run');

    if (!is_string($path) || !file_exists($path)) {
        $this->error("File non trovato: {$path}");
        return 1;
    }

    $this->info("Avvio import da: {$path}" . ($dryRun ? ' (dry-run)' : ''));

    $controller = app(ImportController::class);
    try {
        $data = $controller->cliImportFromPath($path, $dryRun);

        // Output riassunto in console
        $this->info('Import completato (simulazione: ' . ($dryRun ? 'SI' : 'NO') . ').');
        $this->line('Righe totali (incl. header): ' . ($data['total_rows'] ?? 'n/d'));
        $this->line('Intestazioni: ' . json_encode($data['headers'] ?? [], JSON_UNESCAPED_UNICODE));
        $this->line('Mappatura: ' . json_encode($data['mapping'] ?? [], JSON_UNESCAPED_UNICODE));

        if (!empty($data['stats'])) {
            $stats = $data['stats'];
            $this->line('— Stats —');
            foreach ($stats as $k => $v) {
                if ($k === 'errors' && is_array($v)) {
                    $this->line('errors: ' . count($v));
                    foreach ($v as $e) { $this->line('  - ' . $e); }
                } else {
                    $this->line("{$k}: {$v}");
                }
            }
        }

        return 0;
    } catch (\Throwable $e) {
        $this->error('Eccezione durante import: ' . $e->getMessage());
        $this->line($e->getTraceAsString());
        return 3;
    }
})->purpose('Importa ordini da CSV via CLI con output dettagliato');
