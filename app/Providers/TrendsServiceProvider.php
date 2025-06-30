<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Trends\GoogleTrendsService;
use App\Services\Trends\SocialMediaTrendsService;
use App\Services\Trends\SeasonalAnalysisService;
use App\Services\Trends\DemographicAnalysisService;
use App\Services\Trends\PerformanceMetricsService;
use App\Services\Trends\EcommerceDataService;

/**
 * Service Provider for Trends Analysis Services
 */
class TrendsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Google Trends Service
        $this->app->singleton(GoogleTrendsService::class, function ($app) {
            return new GoogleTrendsService();
        });

        // Register Social Media Trends Service
        $this->app->singleton(SocialMediaTrendsService::class, function ($app) {
            return new SocialMediaTrendsService();
        });

        // Register Seasonal Analysis Service
        $this->app->singleton(SeasonalAnalysisService::class, function ($app) {
            return new SeasonalAnalysisService();
        });

        // Register Demographic Analysis Service
        $this->app->singleton(DemographicAnalysisService::class, function ($app) {
            return new DemographicAnalysisService();
        });

        // Register Performance Metrics Service
        $this->app->singleton(PerformanceMetricsService::class, function ($app) {
            return new PerformanceMetricsService();
        });

        // Register Ecommerce Data Service
        $this->app->singleton(EcommerceDataService::class, function ($app) {
            return new EcommerceDataService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            GoogleTrendsService::class,
            SocialMediaTrendsService::class,
            SeasonalAnalysisService::class,
            DemographicAnalysisService::class,
            PerformanceMetricsService::class,
            EcommerceDataService::class,
        ];
    }
}
