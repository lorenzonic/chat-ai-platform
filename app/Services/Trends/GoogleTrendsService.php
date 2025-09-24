<?php

namespace App\Services\Trends;

use App\Models\TrendingKeyword;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Service per gestire i dati di Google Trends
 */
class GoogleTrendsService
{
    /**
     * Ottieni i trends dal database, filtrabili per giorni, regione, keyword
     */
    public function getTrends($days = 30, $region = null, $keyword = null)
    {
        $query = TrendingKeyword::query();
        $query->where('collected_at', '>=', Carbon::now()->subDays($days));
        if ($region) {
            $query->where('region', $region);
        }
        if ($keyword) {
            $query->where('keyword', $keyword);
        }
        return $query->orderByDesc('score')->get();
    }

    /**
     * Ottieni le top trends per score medio negli ultimi $days giorni
     */
    public function getTopTrends($days = 30, $limit = 10)
    {
        return TrendingKeyword::selectRaw('keyword, region, AVG(score) as avg_score')
            ->where('collected_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('keyword', 'region')
            ->orderByDesc('avg_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Ottieni l'andamento storico di una keyword in una regione
     */
    public function getTrendHistory($keyword, $region, $days = 30)
    {
        return TrendingKeyword::where('keyword', $keyword)
            ->where('region', $region)
            ->where('collected_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('collected_at')
            ->get();
    }

    /**
     * Placeholder: dati demo marketplace trends (Amazon, eBay, Etsy)
     */
    public function getMarketplaceTrends($days = 30)
    {
        return [
            'amazon' => [
                'trending_products' => [
                    ['name' => 'Vasi per succulente', 'sales_rank' => '#1', 'growth' => 45],
                    ['name' => 'Fertilizzante organico', 'sales_rank' => '#3', 'growth' => 32],
                    ['name' => 'Lampade per piante', 'sales_rank' => '#5', 'growth' => 28],
                    ['name' => 'Terriccio specifico cactus', 'sales_rank' => '#8', 'growth' => 22],
                ],
                'categories' => [
                    ['name' => 'Giardinaggio', 'growth' => 18.5],
                    ['name' => 'Piante da interno', 'growth' => 35.2],
                    ['name' => 'Attrezzi giardinaggio', 'growth' => 12.8],
                ],
            ],
            'ebay' => [
                'hot_searches' => [
                    ['term' => 'cactus rari', 'searches' => 15000, 'growth' => 55],
                    ['term' => 'piante grasse', 'searches' => 25000, 'growth' => 42],
                    ['term' => 'bonsai', 'searches' => 18000, 'growth' => 28],
                ],
            ],
            'etsy' => [
                'trending_tags' => [
                    ['tag' => 'plant decor', 'listings' => 45000, 'growth' => 38],
                    ['tag' => 'botanical art', 'listings' => 32000, 'growth' => 25],
                    ['tag' => 'plant accessories', 'listings' => 28000, 'growth' => 41],
                ],
            ],
        ];
    }

    /**
     * Restituisce keywords piante reali dal database (ultimi 30 giorni), fallback demo se vuoto
     */
    public function getPlantKeywords()
    {
        $days = 30;
        $now = now();
        $startDate = $now->copy()->subDays($days);

        // Recupera le keyword reali dal database
        $keywords = \DB::table('trending_keywords')
            ->select('keyword', 'score', 'region', 'collected_at')
            ->where('collected_at', '>=', $startDate)
            ->orderByDesc('score')
            ->get();

        if ($keywords->isEmpty()) {
            // Fallback demo
            return [
                'high_volume' => [
                    ['keyword' => 'piante', 'volume' => 250000, 'difficulty' => 'high', 'cpc' => 0.85],
                    ['keyword' => 'giardinaggio', 'volume' => 180000, 'difficulty' => 'medium', 'cpc' => 1.20],
                    ['keyword' => 'fiori', 'volume' => 220000, 'difficulty' => 'high', 'cpc' => 0.95],
                    ['keyword' => 'verde', 'volume' => 300000, 'difficulty' => 'high', 'cpc' => 0.75],
                ],
                'medium_volume' => [
                    ['keyword' => 'cactus', 'volume' => 95000, 'difficulty' => 'medium', 'cpc' => 1.15],
                    ['keyword' => 'succulente', 'volume' => 85000, 'difficulty' => 'low', 'cpc' => 1.35],
                    ['keyword' => 'bonsai', 'volume' => 78000, 'difficulty' => 'medium', 'cpc' => 1.50],
                    ['keyword' => 'orchidee', 'volume' => 65000, 'difficulty' => 'medium', 'cpc' => 1.80],
                ],
                'long_tail' => [
                    ['keyword' => 'come curare piante grasse', 'volume' => 15000, 'difficulty' => 'low', 'cpc' => 0.95],
                    ['keyword' => 'piante da appartamento facili', 'volume' => 12000, 'difficulty' => 'low', 'cpc' => 1.10],
                    ['keyword' => 'quando innaffiare cactus', 'volume' => 8000, 'difficulty' => 'low', 'cpc' => 0.80],
                    ['keyword' => 'migliori piante purificatrici aria', 'volume' => 18000, 'difficulty' => 'low', 'cpc' => 1.25],
                ],
                'trending' => [
                    ['keyword' => 'plant parent', 'volume' => 35000, 'growth' => 45, 'cpc' => 1.40],
                    ['keyword' => 'urban jungle', 'volume' => 28000, 'growth' => 38, 'cpc' => 1.60],
                    ['keyword' => 'plant therapy', 'volume' => 22000, 'growth' => 52, 'cpc' => 1.80],
                ],
            ];
        }

        // Raggruppa le keyword per score (volume) e trend
        $high_volume = $keywords->take(5)->map(function($k) {
            return [
                'keyword' => $k->keyword,
                'volume' => $k->score * 1000, // Stima fittizia volume
                'difficulty' => 'high',
                'cpc' => 1.0 + ($k->score % 100) / 100
            ];
        })->toArray();

        $trending = $keywords->sortByDesc('score')->take(5)->map(function($k) {
            return [
                'keyword' => $k->keyword,
                'volume' => $k->score * 1000,
                'growth' => rand(20, 80),
                'cpc' => 1.0 + ($k->score % 100) / 100
            ];
        })->toArray();

        $long_tail = $keywords->sortBy('score')->take(5)->map(function($k) {
            return [
                'keyword' => $k->keyword,
                'volume' => $k->score * 100,
                'cpc' => 0.5 + ($k->score % 50) / 100
            ];
        })->toArray();

        return [
            'high_volume' => $high_volume,
            'trending' => $trending,
            'long_tail' => $long_tail,
        ];
    }
}
