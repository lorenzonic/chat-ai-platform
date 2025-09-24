<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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
