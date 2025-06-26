<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\GeminiService::class, function ($app) {
            return new \App\Services\GeminiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production and trust proxies
        if (config('app.env') === 'production' || config('app.force_https', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Trust Railway proxy headers
        if (config('app.env') === 'production') {
            request()->server->set('HTTPS', true);
        }
    }
}
