<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\Lead;
use App\Models\ChatLog;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard data
     */
    public function index(Request $request)
    {
        $store = auth()->guard('store')->user();

        // Get real analytics data
        $frequentQuestions = $this->getFrequentQuestions($store);
        $popularPlants = $this->getPopularPlants($store);

        // For debugging - use CDN simple version with real data
        return view('store.analytics.cdn-simple', [
            'store' => $store,
            'frequentQuestions' => $frequentQuestions,
            'popularPlants' => $popularPlants
        ]);
    }

    /**
     * Get analytics data as JSON (used by AJAX requests and map)
     */
    public function getAnalyticsData(Request $request): JsonResponse
    {
        $store = auth()->guard('store')->user();

        // Validate date range
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'period' => 'nullable|in:7d,30d,90d,custom'
        ]);

        $period = $request->get('period', '30d');
        $from = $request->get('from');
        $to = $request->get('to');

        // Set default date range based on period
        if (!$from || !$to) {
            switch ($period) {
                case '7d':
                    $from = Carbon::now()->subDays(7)->startOfDay();
                    $to = Carbon::now()->endOfDay();
                    break;
                case '30d':
                    $from = Carbon::now()->subDays(30)->startOfDay();
                    $to = Carbon::now()->endOfDay();
                    break;
                case '90d':
                    $from = Carbon::now()->subDays(90)->startOfDay();
                    $to = Carbon::now()->endOfDay();
                    break;
                default:
                    $from = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
                    $to = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
            }
        }

        // Get interactions data (contains all analytics info: device, browser, geo, UTM)
        $interactions = Interaction::where('store_id', $store->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Get leads data
        $leads = Lead::where('store_id', $store->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Get chat logs data (for conversation content)
        $chatLogs = ChatLog::where('store_id', $store->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Debug log
        \Log::info('Analytics data counts', [
            'store_id' => $store->id,
            'period' => $period,
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString(),
            'interactions_count' => $interactions->count(),
            'chat_logs_count' => $chatLogs->count(),
            'leads_count' => $leads->count(),
        ]);

        // Daily interactions
        $dailyInteractions = $interactions->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($dayInteractions) {
            return $dayInteractions->count();
        });

        // Daily leads
        $dailyLeads = $leads->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($dayLeads) {
            return $dayLeads->count();
        });

        // Top questions from interactions
        $topQuestions = $interactions->where('question', '!=', null)
            ->groupBy('question')
            ->map(function($questionGroup) {
                return $questionGroup->count();
            })
            ->sortDesc()
            ->take(10);

        // Device breakdown from interactions
        $deviceBreakdown = $interactions->groupBy('device_type')
            ->map(function($deviceGroup) {
                return $deviceGroup->count();
            });

        // Browser breakdown from interactions
        $browserBreakdown = $interactions->groupBy('browser')
            ->map(function($browserGroup) {
                return $browserGroup->count();
            });

        // QR Code performance from interactions
        $qrPerformance = $interactions->where('qr_code_id', '!=', null)
            ->groupBy('qr_code_id')
            ->map(function($qrGroup) {
                return [
                    'count' => $qrGroup->count(),
                    'qr_code' => $qrGroup->first()->qrCode
                ];
            });

        // Lead sources
        $leadSources = $leads->groupBy('source')
            ->map(function($sourceGroup) {
                return $sourceGroup->count();
            });

        // Geographic data (top cities)
        $topCities = $leads->where('city', '!=', null)
            ->groupBy('city')
            ->map(function($cityGroup) {
                return [
                    'count' => $cityGroup->count(),
                    'country' => $cityGroup->first()->country
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        // Geographic data for map
        $geographicData = $this->getGeographicData($leads, $interactions);

        // Conversion rate (leads / total interactions)
        $conversionRate = $interactions->count() > 0
            ? ($leads->count() / $interactions->count()) * 100
            : 0;

        // Premium features
        $premiumData = [];
        if ($store->is_premium) {
            $premiumData = [
                'hourly_breakdown' => $this->getHourlyBreakdown($interactions),
                'utm_analysis' => $this->getUTMAnalysis($interactions),
                'session_duration' => $this->getSessionDuration($interactions),
                'detailed_geography' => $this->getDetailedGeography($leads),
            ];
        }

        return response()->json([
            'period' => $period,
            'date_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString()
            ],
            'summary' => [
                'total_interactions' => $interactions->count(),
                'total_leads' => $leads->count(),
                'total_chats' => $chatLogs->count(),
                'conversion_rate' => round($conversionRate, 2),
                'unique_visitors' => $interactions->unique('ip')->count(),
            ],
            'daily_interactions' => $dailyInteractions,
            'daily_leads' => $dailyLeads,
            'top_questions' => $topQuestions,
            'device_breakdown' => $deviceBreakdown,
            'browser_breakdown' => $browserBreakdown,
            'qr_performance' => $qrPerformance,
            'lead_sources' => $leadSources,
            'top_cities' => $topCities,
            'geographic_data' => $geographicData,
            'is_premium' => $store->is_premium,
        ]);
    }

    /**
     * Export analytics data to CSV (Premium only)
     */
    public function exportCsv(Request $request)
    {
        $store = auth()->guard('store')->user();

        if (!$store->is_premium) {
            return response()->json(['error' => 'Feature disponibile solo per account Premium'], 403);
        }

        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'type' => 'required|in:interactions,leads,all'
        ]);

        $from = $request->get('from', Carbon::now()->subDays(30)->startOfDay());
        $to = $request->get('to', Carbon::now()->endOfDay());
        $type = $request->get('type', 'all');

        $filename = "analytics_{$store->slug}_{$type}_" . Carbon::now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($store, $from, $to, $type) {
            $handle = fopen('php://output', 'w');

            // Set UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            if ($type === 'interactions' || $type === 'all') {
                // Interactions header
                fputcsv($handle, [
                    'Data',
                    'Ora',
                    'Domanda',
                    'Risposta',
                    'IP',
                    'Device',
                    'Browser',
                    'QR Code',
                    'UTM Source',
                    'Durata (sec)'
                ]);

                Interaction::where('store_id', $store->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->orderBy('created_at', 'desc')
                    ->chunk(1000, function ($interactions) use ($handle) {
                        foreach ($interactions as $interaction) {
                            fputcsv($handle, [
                                $interaction->created_at->format('Y-m-d'),
                                $interaction->created_at->format('H:i:s'),
                                $interaction->question,
                                $interaction->answer,
                                $interaction->ip,
                                $interaction->device_type,
                                $interaction->browser,
                                $interaction->qr_code_id,
                                $interaction->utm_source,
                                $interaction->duration,
                            ]);
                        }
                    });
            }

            if ($type === 'leads' || $type === 'all') {
                // Empty row if both types
                if ($type === 'all') {
                    fputcsv($handle, []);
                }

                // Leads header
                fputcsv($handle, [
                    'Data',
                    'Nome',
                    'Email',
                    'WhatsApp',
                    'Tag',
                    'Città',
                    'Paese',
                    'Latitudine',
                    'Longitudine',
                    'Fonte'
                ]);

                Lead::where('store_id', $store->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->orderBy('created_at', 'desc')
                    ->chunk(1000, function ($leads) use ($handle) {
                        foreach ($leads as $lead) {
                            fputcsv($handle, [
                                $lead->created_at->format('Y-m-d H:i:s'),
                                $lead->name,
                                $lead->email,
                                $lead->whatsapp,
                                $lead->tag,
                                $lead->city,
                                $lead->country,
                                $lead->latitude,
                                $lead->longitude,
                                $lead->source,
                            ]);
                        }
                    });
            }

            fclose($handle);
        }, $filename);
    }

    /**
     * Get hourly breakdown (Premium feature)
     */
    private function getHourlyBreakdown($interactions)
    {
        return $interactions->groupBy(function($item) {
            return $item->created_at->format('H');
        })->map(function($hourGroup) {
            return $hourGroup->count();
        })->sortKeys();
    }

    /**
     * Get UTM analysis (Premium feature)
     */
    private function getUTMAnalysis($interactions)
    {
        return [
            'sources' => $interactions->where('utm_source', '!=', null)
                ->groupBy('utm_source')
                ->map(function($group) { return $group->count(); }),
            'mediums' => $interactions->where('utm_medium', '!=', null)
                ->groupBy('utm_medium')
                ->map(function($group) { return $group->count(); }),
            'campaigns' => $interactions->where('utm_campaign', '!=', null)
                ->groupBy('utm_campaign')
                ->map(function($group) { return $group->count(); }),
        ];
    }

    /**
     * Get session duration statistics (Premium feature)
     */
    private function getSessionDuration($interactions)
    {
        $durations = $interactions->where('duration', '!=', null)->pluck('duration');

        if ($durations->isEmpty()) {
            return null;
        }

        return [
            'average' => round($durations->avg(), 2),
            'median' => $durations->median(),
            'min' => $durations->min(),
            'max' => $durations->max(),
        ];
    }

    /**
     * Get detailed geography (Premium feature)
     */
    private function getDetailedGeography($leads)
    {
        return [
            'countries' => $leads->where('country', '!=', null)
                ->groupBy('country')
                ->map(function($group) { return $group->count(); })
                ->sortByDesc(function($count) { return $count; }),
            'regions' => $leads->where('region', '!=', null)
                ->groupBy('region')
                ->map(function($group) { return $group->count(); })
                ->sortByDesc(function($count) { return $count; }),
        ];
    }

    /**
     * Get geographic data for map visualization
     */
    private function getGeographicData($leads, $interactions)
    {
        $mapData = [];

        // Ottieni dati da leads che hanno coordinate
        $leadsWithCoords = $leads->where('latitude', '!=', null)
                                 ->where('longitude', '!=', null);

        foreach ($leadsWithCoords as $lead) {
            $key = $lead->latitude . ',' . $lead->longitude;

            if (!isset($mapData[$key])) {
                $mapData[$key] = [
                    'lat' => (float) $lead->latitude,
                    'lng' => (float) $lead->longitude,
                    'city' => $lead->city ?? 'Sconosciuta',
                    'country' => $lead->country ?? 'Sconosciuto',
                    'leads_count' => 0,
                    'interactions_count' => 0,
                    'type' => 'lead'
                ];
            }

            $mapData[$key]['leads_count']++;
        }

        // Aggiungi dati da interactions che hanno coordinate
        $interactionsWithCoords = $interactions->where('latitude', '!=', null)
                                               ->where('longitude', '!=', null);

        foreach ($interactionsWithCoords as $interaction) {
            $key = $interaction->latitude . ',' . $interaction->longitude;

            if (!isset($mapData[$key])) {
                $mapData[$key] = [
                    'lat' => (float) $interaction->latitude,
                    'lng' => (float) $interaction->longitude,
                    'city' => $interaction->city ?? 'Sconosciuta',
                    'country' => $interaction->country ?? 'Sconosciuto',
                    'leads_count' => 0,
                    'interactions_count' => 0,
                    'type' => 'interaction'
                ];
            }

            $mapData[$key]['interactions_count']++;
        }

        // Aggiungi totale per ogni punto
        foreach ($mapData as &$point) {
            $point['total'] = $point['leads_count'] + $point['interactions_count'];
        }

        return array_values($mapData);
    }

    /**
     * Test endpoint to debug analytics data
     */
    public function testData(Request $request)
    {
        $store = auth()->guard('store')->user();

        $interactionsCount = Interaction::where('store_id', $store->id)->count();
        $leadsCount = Lead::where('store_id', $store->id)->count();
        $recentInteractions = Interaction::where('store_id', $store->id)
            ->latest()
            ->take(5)
            ->get(['id', 'question', 'answer', 'created_at']);
        $recentLeads = Lead::where('store_id', $store->id)
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at']);

        return response()->json([
            'store_id' => $store->id,
            'store_name' => $store->name,
            'interactions_count' => $interactionsCount,
            'leads_count' => $leadsCount,
            'recent_interactions' => $recentInteractions,
            'recent_leads' => $recentLeads,
        ]);
    }

    /**
     * Debug geographic data (temporary - no auth required)
     */
    public function debugGeographic(Request $request)
    {
        // For debug only - use store ID 19
        $store = Store::find(19);

        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $interactions = Interaction::where('store_id', $store->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $leads = Lead::where('store_id', $store->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $geographicData = $this->getGeographicData($leads, $interactions);

        return response()->json([
            'store_id' => $store->id,
            'store_name' => $store->name,
            'interactions_with_geo' => $interactions->count(),
            'leads_with_geo' => $leads->count(),
            'geographic_data' => $geographicData,
            'sample_interaction' => $interactions->first(),
            'sample_lead' => $leads->first(),
        ]);
    }

    /**
     * Get frequent questions from interactions
     */
    private function getFrequentQuestions($store)
    {
        $questions = Interaction::where('store_id', $store->id)
            ->whereNotNull('question')
            ->where('question', '!=', '')
            ->select('question', DB::raw('count(*) as count'))
            ->groupBy('question')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return $questions->map(function($q) {
            return [
                'question' => $q->question,
                'count' => $q->count
            ];
        });
    }

    /**
     * Get popular plants from interactions and chat logs
     */
    private function getPopularPlants($store)
    {
        // Lista di nomi di piante comuni da cercare
        $plantKeywords = [
            'rosa', 'rose', 'basilico', 'lavanda', 'geranio', 'cactus', 'orchidea',
            'ficus', 'pothos', 'succulenta', 'succulente', 'petunia', 'begonia',
            'ciclamino', 'azalea', 'camelia', 'ibisco', 'gelsomino', 'gardenia',
            'margherita', 'tulipano', 'narciso', 'giglio', 'iris', 'viola',
            'primula', 'dalia', 'zinnia', 'salvia', 'rosmarino', 'timo',
            'menta', 'prezzemolo', 'ortensia', 'aloe', 'agave', 'yucca',
            'palma', 'felce', 'edera', 'monstera', 'sansevieria', 'croton',
            'dipladenia', 'mandevilla', 'bouganville', 'gerbera', 'anthurium'
        ];

        $plantCounts = [];

        // Cerca nelle domande degli interactions
        foreach ($plantKeywords as $plant) {
            $count = Interaction::where('store_id', $store->id)
                ->where(function($query) use ($plant) {
                    $query->where('question', 'LIKE', "%{$plant}%")
                          ->orWhere('answer', 'LIKE', "%{$plant}%");
                })
                ->count();

            if ($count > 0) {
                $plantCounts[$plant] = ($plantCounts[$plant] ?? 0) + $count;
            }
        }

        // Cerca anche nei chat logs
        foreach ($plantKeywords as $plant) {
            $count = ChatLog::where('store_id', $store->id)
                ->where(function($query) use ($plant) {
                    $query->where('user_message', 'LIKE', "%{$plant}%")
                          ->orWhere('ai_response', 'LIKE', "%{$plant}%");
                })
                ->count();

            if ($count > 0) {
                $plantCounts[$plant] = ($plantCounts[$plant] ?? 0) + $count;
            }
        }

        // Ordina per count e prendi i top 8
        arsort($plantCounts);
        $topPlants = array_slice($plantCounts, 0, 8, true);

        // Emoji per piante
        $plantEmojis = [
            'rosa' => '🌹', 'rose' => '🌹', 'basilico' => '🌿', 'lavanda' => '💜',
            'geranio' => '🌺', 'cactus' => '🌵', 'orchidea' => '🌸', 'ficus' => '🍃',
            'pothos' => '🌱', 'succulenta' => '🌵', 'succulente' => '🌵', 'petunia' => '🌺',
            'begonia' => '🌸', 'ciclamino' => '🌺', 'azalea' => '🌸', 'camelia' => '🌺',
            'ibisco' => '🌺', 'gelsomino' => '🌼', 'gardenia' => '🌸', 'margherita' => '🌼',
            'tulipano' => '🌷', 'narciso' => '🌼', 'giglio' => '🌸', 'iris' => '💐',
            'viola' => '💜', 'primula' => '🌼', 'dalia' => '🌺', 'zinnia' => '🌻',
            'salvia' => '🌿', 'rosmarino' => '🌿', 'timo' => '🌿', 'menta' => '🌿',
            'prezzemolo' => '🌿', 'ortensia' => '💐', 'aloe' => '🌵', 'agave' => '🌵',
            'yucca' => '🌴', 'palma' => '🌴', 'felce' => '🌿', 'edera' => '🌿',
            'monstera' => '🍃', 'sansevieria' => '🌿', 'croton' => '🍃',
            'dipladenia' => '🌺', 'mandevilla' => '🌺', 'bouganville' => '🌺',
            'gerbera' => '🌼', 'anthurium' => '🌺'
        ];

        return collect($topPlants)->map(function($count, $plant) use ($plantEmojis) {
            return [
                'name' => ucfirst($plant),
                'count' => $count,
                'emoji' => $plantEmojis[$plant] ?? '🌱'
            ];
        })->values();
    }
}
