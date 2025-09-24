<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrendingKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrendingKeywordsController extends Controller
{
    /**
     * Mostra la dashboard dei trending keywords.
     */
    public function index(Request $request)
    {
        // Filtri dalla request
        $region = $request->get('region', 'all');
        $days = $request->get('days', 7);
        $keyword = $request->get('keyword');

        // Query base
        $query = TrendingKeyword::query();

        // Applica filtri
        if ($region !== 'all') {
            $query->where('region', $region);
        }

        if ($keyword) {
            $query->where('keyword', 'LIKE', "%{$keyword}%");
        }

        $query->where('collected_at', '>=', now()->subDays($days));

        // Dati per le statistiche
        $stats = [
            'total_keywords_today' => TrendingKeyword::whereDate('collected_at', today())->count(),
            'total_keywords_week' => TrendingKeyword::where('collected_at', '>=', now()->subWeek())->count(),
            'total_keywords_all' => TrendingKeyword::count(),
            'avg_score_today' => TrendingKeyword::whereDate('collected_at', today())->avg('score'),
            'regions_count' => TrendingKeyword::distinct('region')->count(),
            'last_update' => TrendingKeyword::latest('collected_at')->first()?->collected_at
        ];

        // Top keywords per score
        $topKeywords = TrendingKeyword::select('keyword', 'region', 'score', 'collected_at')
            ->when($region !== 'all', fn($q) => $q->where('region', $region))
            ->whereDate('collected_at', today())
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();

        // Trends per regione
        $regionStats = TrendingKeyword::select('region', DB::raw('COUNT(*) as count'), DB::raw('AVG(score) as avg_score'))
            ->whereDate('collected_at', today())
            ->groupBy('region')
            ->orderBy('avg_score', 'desc')
            ->get();

        // Dati per il grafico giornaliero (ultimi 7 giorni)
        $dailyTrends = TrendingKeyword::select(
                DB::raw('DATE(collected_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(score) as avg_score')
            )
            ->where('collected_at', '>=', now()->subDays(7))
            ->when($region !== 'all', fn($q) => $q->where('region', $region))
            ->groupBy(DB::raw('DATE(collected_at)'))
            ->orderBy('date')
            ->get();

        // Keywords più cercate (frequenza)
        $popularKeywords = TrendingKeyword::select('keyword', DB::raw('COUNT(*) as frequency'), DB::raw('AVG(score) as avg_score'))
            ->where('collected_at', '>=', now()->subDays($days))
            ->when($region !== 'all', fn($q) => $q->where('region', $region))
            ->groupBy('keyword')
            ->orderBy('frequency', 'desc')
            ->limit(15)
            ->get();

        // Lista paginata dei trends
        $trends = $query->orderBy('collected_at', 'desc')
            ->orderBy('score', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Regioni disponibili
        $availableRegions = TrendingKeyword::distinct('region')->pluck('region');

        return view('admin.trending-keywords.index', compact(
            'trends',
            'stats',
            'topKeywords',
            'regionStats',
            'dailyTrends',
            'popularKeywords',
            'availableRegions',
            'region',
            'days',
            'keyword'
        ));
    }

    /**
     * Mostra dettagli di una keyword specifica.
     */
    public function show(Request $request, $keyword)
    {
        $keyword = urldecode($keyword);
        $region = $request->get('region', 'all');
        $days = $request->get('days', 30);

        // Dati della keyword
        $keywordData = TrendingKeyword::where('keyword', $keyword)
            ->when($region !== 'all', fn($q) => $q->where('region', $region))
            ->where('collected_at', '>=', now()->subDays($days))
            ->orderBy('collected_at', 'desc')
            ->get();

        if ($keywordData->isEmpty()) {
            abort(404, 'Keyword non trovata');
        }

        // Statistiche keyword
        $keywordStats = [
            'total_entries' => $keywordData->count(),
            'avg_score' => $keywordData->avg('score'),
            'max_score' => $keywordData->max('score'),
            'min_score' => $keywordData->min('score'),
            'first_seen' => $keywordData->min('collected_at'),
            'last_seen' => $keywordData->max('collected_at'),
            'regions' => $keywordData->pluck('region')->unique()->values()
        ];

        // Trend nel tempo per grafici
        $timeSeriesData = $keywordData->groupBy(function($item) {
                return Carbon::parse($item->collected_at)->format('Y-m-d');
            })
            ->map(function($group) {
                return [
                    'date' => $group->first()->collected_at->format('Y-m-d'),
                    'avg_score' => $group->avg('score'),
                    'count' => $group->count()
                ];
            })
            ->sortBy('date');

        // Confronto regionale
        $regionalComparison = $keywordData->groupBy('region')
            ->map(function($group) {
                return [
                    'region' => $group->first()->region,
                    'avg_score' => $group->avg('score'),
                    'count' => $group->count(),
                    'latest_score' => $group->sortByDesc('collected_at')->first()->score
                ];
            })
            ->sortByDesc('avg_score');

        return view('admin.trending-keywords.show', compact(
            'keyword',
            'keywordData',
            'keywordStats',
            'timeSeriesData',
            'regionalComparison',
            'region',
            'days'
        ));
    }

    /**
     * API endpoint per dati del grafico.
     */
    public function chartData(Request $request)
    {
        $region = $request->get('region', 'all');
        $days = $request->get('days', 7);
        $type = $request->get('type', 'daily'); // daily, hourly, keyword

        switch ($type) {
            case 'daily':
                $data = TrendingKeyword::select(
                        DB::raw('DATE(collected_at) as date'),
                        DB::raw('COUNT(*) as count'),
                        DB::raw('AVG(score) as avg_score')
                    )
                    ->where('collected_at', '>=', now()->subDays($days))
                    ->when($region !== 'all', fn($q) => $q->where('region', $region))
                    ->groupBy(DB::raw('DATE(collected_at)'))
                    ->orderBy('date')
                    ->get()
                    ->map(function($item) {
                        return [
                            'date' => $item->date,
                            'count' => (int) $item->count,
                            'avg_score' => round($item->avg_score, 1)
                        ];
                    });
                break;

            case 'keyword':
                $data = TrendingKeyword::select('keyword', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as count'))
                    ->where('collected_at', '>=', now()->subDays($days))
                    ->when($region !== 'all', fn($q) => $q->where('region', $region))
                    ->groupBy('keyword')
                    ->orderBy('avg_score', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'keyword' => $item->keyword,
                            'avg_score' => round($item->avg_score, 1),
                            'count' => (int) $item->count
                        ];
                    });
                break;

            default:
                $data = collect();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Esegue manualmente l'aggiornamento trends.
     */
    public function updateTrends(Request $request)
    {
        try {
            // Esegui il comando Artisan
            $exitCode = \Artisan::call('trends:update', [
                '--force' => true,
                '--show-stats' => true
            ]);

            $output = \Artisan::output();

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aggiornamento trends completato con successo',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante l\'aggiornamento trends',
                    'output' => $output
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina trends più vecchi di X giorni.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->input('days');
        $cutoffDate = now()->subDays($days);

        $deleted = TrendingKeyword::where('collected_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "Eliminati {$deleted} record più vecchi di {$days} giorni",
            'deleted_count' => $deleted
        ]);
    }

    public function runUpdateScript(Request $request)
    {
        // Sicurezza: solo admin autenticati
        if (!auth('admin')->check()) {
            abort(403);
        }
        $output = null;
        $result = null;
        try {
            $cmd = 'python3 scripts/update_plant_trends.py';
            $result = shell_exec($cmd . ' 2>&1');
            $success = (strpos($result, 'Aggiornamento completato') !== false);
        } catch (\Exception $e) {
            $success = false;
            $result = $e->getMessage();
        }
        if ($success) {
            return redirect()->route('admin.trending-keywords.index')->with('success', 'Aggiornamento completato!');
        } else {
            return redirect()->route('admin.trending-keywords.index')->with('error', 'Errore aggiornamento: ' . $result);
        }
    }
}
