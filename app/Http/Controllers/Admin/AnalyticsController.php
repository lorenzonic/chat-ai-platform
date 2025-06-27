<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\ChatLog;
use App\Models\Interaction;
use App\Models\Newsletter;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $selectedStore = null;
        $storeId = $request->get('store_id');

        if ($storeId && $storeId !== 'all') {
            $selectedStore = Store::find($storeId);
        }

        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Get all stores for filter
        $stores = Store::orderBy('name')->get();

        // Build base queries with optional store filter
        $baseQuery = function($model) use ($storeId, $startDate, $endDate) {
            $query = $model::whereBetween('created_at', [$startDate, $endDate]);
            if ($storeId && $storeId !== 'all') {
                $query->where('store_id', $storeId);
            }
            return $query;
        };

        // Overview statistics
        $stats = [
            'total_stores' => $selectedStore ? 1 : Store::count(),
            'active_stores' => $selectedStore ? ($selectedStore->is_active ? 1 : 0) : Store::where('is_active', true)->count(),
            'premium_stores' => $selectedStore ? ($selectedStore->is_premium ? 1 : 0) : Store::where('is_premium', true)->count(),
            'total_qr_codes' => $this->getQrCodeCount($storeId),
            'total_scans' => $baseQuery(QrScan::class)->count(),
            'total_chats' => $baseQuery(ChatLog::class)->count(),
            'total_interactions' => $baseQuery(Interaction::class)->count(),
            'newsletter_signups' => $baseQuery(Newsletter::class)->count(),
        ];

        // Recent activity
        $recentActivity = $this->getRecentActivity($storeId, $startDate, $endDate);

        // Charts data
        $chartsData = [
            'scans_over_time' => $this->getScansOverTime($storeId, $startDate, $endDate),
            'chats_over_time' => $this->getChatsOverTime($storeId, $startDate, $endDate),
            'interactions_over_time' => $this->getInteractionsOverTime($storeId, $startDate, $endDate),
            'device_types' => $this->getDeviceTypeStats($storeId, $startDate, $endDate),
            'top_qr_codes' => $this->getTopQrCodes($storeId, $startDate, $endDate),
            'geographic_data' => $this->getGeographicData($storeId, $startDate, $endDate),
            'store_performance' => $selectedStore ? collect() : $this->getStorePerformance($startDate, $endDate),
        ];

        return view('admin.analytics.index', compact(
            'stats',
            'stores',
            'selectedStore',
            'recentActivity',
            'chartsData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get QR code count for store filter
     */
    private function getQrCodeCount($storeId)
    {
        $query = QrCode::query();
        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }
        return $query->count();
    }

    /**
     * Get recent activity across all models
     */
    private function getRecentActivity($storeId, $startDate, $endDate)
    {
        $activities = collect();

        // QR Scans
        $scans = QrScan::with(['store', 'qrCode'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($scan) {
                return [
                    'type' => 'scan',
                    'description' => "QR Code scanned: {$scan->qrCode->name}",
                    'store' => $scan->store->name,
                    'timestamp' => $scan->created_at,
                    'details' => [
                        'qr_code' => $scan->qrCode->name,
                        'device_type' => $scan->device_type,
                        'ip_address' => $scan->ip_address
                    ]
                ];
            });

        // Chat Logs
        $chats = ChatLog::with(['store', 'qrCode'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($chat) {
                return [
                    'type' => 'chat',
                    'description' => "Chat message: " . substr($chat->user_message, 0, 50) . "...",
                    'store' => $chat->store->name,
                    'timestamp' => $chat->created_at,
                    'details' => [
                        'user_message' => substr($chat->user_message, 0, 100),
                        'response_length' => strlen($chat->bot_response ?? ''),
                        'qr_code' => $chat->qrCode ? $chat->qrCode->name : 'Direct'
                    ]
                ];
            });

        // Interactions
        $interactions = Interaction::with('store')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($interaction) {
                return [
                    'type' => 'interaction',
                    'description' => "User interaction: {$interaction->interaction_type}",
                    'store' => $interaction->store->name,
                    'timestamp' => $interaction->created_at,
                    'details' => [
                        'type' => $interaction->interaction_type,
                        'data' => $interaction->interaction_data,
                        'location' => isset($interaction->latitude) ? "Lat: {$interaction->latitude}, Lng: {$interaction->longitude}" : 'No location'
                    ]
                ];
            });

        // Newsletter signups
        $newsletters = Newsletter::with('store')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($newsletter) {
                return [
                    'type' => 'newsletter',
                    'description' => "Newsletter signup: {$newsletter->email}",
                    'store' => $newsletter->store->name,
                    'timestamp' => $newsletter->created_at,
                    'details' => [
                        'email' => $newsletter->email,
                        'name' => $newsletter->name,
                        'status' => $newsletter->is_confirmed ? 'Confirmed' : 'Pending'
                    ]
                ];
            });

        return $activities
            ->merge($scans)
            ->merge($chats)
            ->merge($interactions)
            ->merge($newsletters)
            ->sortByDesc('timestamp')
            ->take(20);
    }

    /**
     * Get scans over time for chart
     */
    private function getScansOverTime($storeId, $startDate, $endDate)
    {
        $query = QrScan::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }

        return $query->get()->pluck('count', 'date');
    }

    /**
     * Get chats over time for chart
     */
    private function getChatsOverTime($storeId, $startDate, $endDate)
    {
        $query = ChatLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }

        return $query->get()->pluck('count', 'date');
    }

    /**
     * Get interactions over time for chart
     */
    private function getInteractionsOverTime($storeId, $startDate, $endDate)
    {
        $query = Interaction::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }

        return $query->get()->pluck('count', 'date');
    }

    /**
     * Get device type statistics
     */
    private function getDeviceTypeStats($storeId, $startDate, $endDate)
    {
        $query = QrScan::selectRaw('device_type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count');

        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }

        return $query->get()->pluck('count', 'device_type');
    }

    /**
     * Get top performing QR codes
     */
    private function getTopQrCodes($storeId, $startDate, $endDate)
    {
        $query = QrScan::with('qrCode')
            ->selectRaw('qr_code_id, COUNT(*) as scan_count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('qr_code_id')
            ->orderByDesc('scan_count')
            ->take(10);

        if ($storeId && $storeId !== 'all') {
            $query->where('store_id', $storeId);
        }

        return $query->get()->map(function($item) {
            return [
                'name' => $item->qrCode ? $item->qrCode->name : 'Unknown',
                'scans' => $item->scan_count
            ];
        });
    }

    /**
     * Get geographic data for map (from leads and QR scans)
     */
    private function getGeographicData($storeId, $startDate, $endDate)
    {
        $locations = collect();

        // Get data from Leads (with latitude/longitude)
        $leadsQuery = \App\Models\Lead::selectRaw('latitude, longitude, COUNT(*) as count, "lead" as type')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('latitude', 'longitude');

        if ($storeId && $storeId !== 'all') {
            $leadsQuery->where('store_id', $storeId);
        }

        $leads = $leadsQuery->get()->map(function($item) {
            return [
                'lat' => (float) $item->latitude,
                'lng' => (float) $item->longitude,
                'count' => $item->count,
                'type' => 'lead',
                'description' => 'Leads: ' . $item->count
            ];
        });

        // Get data from QR Scans (with geo_location array)
        $qrScansQuery = QrScan::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('geo_location');

        if ($storeId && $storeId !== 'all') {
            $qrScansQuery->where('store_id', $storeId);
        }

        $qrScans = $qrScansQuery->get()
            ->filter(function($scan) {
                $geo = $scan->geo_location;
                return isset($geo['latitude']) && isset($geo['longitude']) &&
                       is_numeric($geo['latitude']) && is_numeric($geo['longitude']);
            })
            ->groupBy(function($scan) {
                $geo = $scan->geo_location;
                return $geo['latitude'] . ',' . $geo['longitude'];
            })
            ->map(function($group, $key) {
                $coords = explode(',', $key);
                return [
                    'lat' => (float) $coords[0],
                    'lng' => (float) $coords[1],
                    'count' => $group->count(),
                    'type' => 'scan',
                    'description' => 'QR Scans: ' . $group->count()
                ];
            })
            ->values();

        // Combine all locations
        $locations = $locations->concat($leads)->concat($qrScans);

        // Merge locations with same coordinates
        $merged = $locations->groupBy(function($item) {
                return $item['lat'] . ',' . $item['lng'];
            })
            ->map(function($group, $key) {
                $coords = explode(',', $key);
                $totalLeads = $group->where('type', 'lead')->sum('count');
                $totalScans = $group->where('type', 'scan')->sum('count');

                $descriptions = [];
                if ($totalLeads > 0) $descriptions[] = "Leads: {$totalLeads}";
                if ($totalScans > 0) $descriptions[] = "QR Scans: {$totalScans}";

                return [
                    'lat' => (float) $coords[0],
                    'lng' => (float) $coords[1],
                    'count' => $totalLeads + $totalScans,
                    'leads' => $totalLeads,
                    'scans' => $totalScans,
                    'description' => implode('<br>', $descriptions)
                ];
            })
            ->values();

        return $merged;
    }

    /**
     * Get store performance comparison
     */
    private function getStorePerformance($startDate, $endDate)
    {
        return Store::with(['qrScans' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }, 'chatLogs' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }, 'interactions' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }, 'newsletters' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function($store) {
            $scans = $store->qrScans->count();
            $chats = $store->chatLogs->count();
            $interactions = $store->interactions->count();
            $newsletters = $store->newsletters->count();
            $totalActivity = $scans + $chats + $interactions + $newsletters;

            // Calculate performance score (weighted)
            $score = ($scans * 1) + ($chats * 2) + ($interactions * 1.5) + ($newsletters * 3);

            return [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'is_active' => $store->is_active,
                'is_premium' => $store->is_premium,
                'scans' => $scans,
                'chats' => $chats,
                'interactions' => $interactions,
                'newsletters' => $newsletters,
                'total_activity' => $totalActivity,
                'score' => $score
            ];
        })
        ->sortByDesc('score')
        ->take(10);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $storeId = $request->get('store_id');
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'json');

        $data = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'store_filter' => $storeId
            ],
            'summary' => [
                'total_scans' => QrScan::whereBetween('created_at', [$startDate, $endDate])
                    ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                        return $q->where('store_id', $storeId);
                    })->count(),
                'total_chats' => ChatLog::whereBetween('created_at', [$startDate, $endDate])
                    ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                        return $q->where('store_id', $storeId);
                    })->count(),
                'total_interactions' => Interaction::whereBetween('created_at', [$startDate, $endDate])
                    ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                        return $q->where('store_id', $storeId);
                    })->count(),
                'newsletter_signups' => Newsletter::whereBetween('created_at', [$startDate, $endDate])
                    ->when($storeId && $storeId !== 'all', function($q) use ($storeId) {
                        return $q->where('store_id', $storeId);
                    })->count(),
            ],
            'details' => [
                'scans_by_date' => $this->getScansOverTime($storeId, $startDate, $endDate),
                'chats_by_date' => $this->getChatsOverTime($storeId, $startDate, $endDate),
                'interactions_by_date' => $this->getInteractionsOverTime($storeId, $startDate, $endDate),
                'device_types' => $this->getDeviceTypeStats($storeId, $startDate, $endDate),
                'top_qr_codes' => $this->getTopQrCodes($storeId, $startDate, $endDate),
                'geographic_data' => $this->getGeographicData($storeId, $startDate, $endDate)
            ]
        ];

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="analytics_' . date('Y-m-d') . '.json"'
        ]);
    }

    /**
     * Export data to CSV format
     */
    private function exportToCsv($data)
    {
        $filename = 'analytics_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['ANALYTICS SUMMARY']);
            fputcsv($file, ['Period', $data['period']['start_date'] . ' to ' . $data['period']['end_date']]);
            fputcsv($file, ['Store Filter', $data['period']['store_filter'] ?? 'All Stores']);
            fputcsv($file, []);

            fputcsv($file, ['Metric', 'Count']);
            foreach ($data['summary'] as $metric => $count) {
                fputcsv($file, [ucfirst(str_replace('_', ' ', $metric)), $count]);
            }

            fputcsv($file, []);
            fputcsv($file, ['DEVICE TYPES']);
            fputcsv($file, ['Device Type', 'Count']);
            foreach ($data['details']['device_types'] as $device => $count) {
                fputcsv($file, [$device, $count]);
            }

            fputcsv($file, []);
            fputcsv($file, ['TOP QR CODES']);
            fputcsv($file, ['QR Code Name', 'Scans']);
            foreach ($data['details']['top_qr_codes'] as $qr) {
                fputcsv($file, [$qr['name'], $qr['scans']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
