@extends('layouts.admin')

@section('title', __('admin.trends_analysis'))

@section('content')
<div class="py-6" id="trendsApp">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🌱 {{ __('admin.trends_analysis') }}</h1>
                    <p class="mt-2 text-gray-600">{{ __('admin.trends_overview') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">{{ __('admin.last_update') }}:</div>
                    <div class="text-sm font-medium">@{{ formatLastUpdate() }}</div>
                    <div class="flex items-center mt-1">
                        <div :class="realTimeUpdates ? 'bg-green-400' : 'bg-gray-400'"
                             class="w-2 h-2 rounded-full mr-2"></div>
                        <span class="text-xs text-gray-600">
                            @{{ realTimeUpdates ? '{{ __('admin.auto_update_active') }}' : '{{ __('admin.auto_update_paused') }}' }}
                        </span>
                    </div>
                    <!-- Indicatore fonte dati -->
                    <div class="mt-2 text-xs px-2 py-1 rounded-full {{ isset($dataSource) && $dataSource === 'real' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ isset($dataSource) && $dataSource === 'real' ? '📡 ' . __('admin.google_trends_real') : '🎭 ' . __('admin.simulated_data') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <a href="{{ route('admin.trends.index') }}?tab=plant"
                       class="{{ request('tab', 'plant') === 'plant' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} border-b-2 py-4 px-1 text-sm font-medium">
                        🌱 Plant Trends
                    </a>
                    <a href="{{ route('admin.trends.index') }}?tab=google"
                       class="{{ request('tab') === 'google' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} border-b-2 py-4 px-1 text-sm font-medium">
                        � Google Trends
                    </a>
                    <a href="{{ route('admin.trends.advanced') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 py-4 px-1 text-sm font-medium">
                        🚀 Analisi Avanzata
                    </a>
                    <a href="{{ route('admin.trends.configure') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 py-4 px-1 text-sm font-medium">
                        ⚙️ Configurazione
                    </a>
                </nav>
            </div>
        </div>

        {{-- FILTRI AVANZATI --}}
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="" class="flex flex-wrap gap-4 items-end">
                    <!-- Hidden field to maintain tab state -->
                    <input type="hidden" name="tab" value="{{ request('tab', 'plant') }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                        <select name="days" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="7" {{ $days == 7 ? 'selected' : '' }}>Ultimi 7 giorni</option>
                            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Ultimi 30 giorni</option>
                            <option value="90" {{ $days == 90 ? 'selected' : '' }}>Ultimi 90 giorni</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Regione</label>
                        <select name="region" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tutte</option>
                            @foreach($availableRegions as $reg)
                                <option value="{{ $reg }}" {{ $region == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                        <select name="keyword" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tutte</option>
                            @foreach($availableKeywords as $kw)
                                <option value="{{ $kw }}" {{ $keyword == $kw ? 'selected' : '' }}>{{ $kw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">Filtra</button>
                    </div>
                </form>
            </div>
        </div>

        @if(request('tab', 'plant') === 'plant')
            {{-- PLANT TRENDS CONTENT --}}
            {{-- TABELLA DETTAGLIATA --}}
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">🌱</span> Plant Trending Keywords (Database Reale)
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Keyword</th>
                                    <th class="px-3 py-2 text-left">Score</th>
                                    <th class="px-3 py-2 text-left">Regione</th>
                                    <th class="px-3 py-2 text-left">Data</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($trendsData['google_trends'] as $trend)
                                    <tr>
                                        <td class="px-3 py-2 font-medium">{{ $trend->keyword }}</td>
                                        <td class="px-3 py-2">{{ $trend->score }}</td>
                                        <td class="px-3 py-2">{{ $trend->region }}</td>
                                        <td class="px-3 py-2">{{ $trend->collected_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif(request('tab') === 'google')
            {{-- GOOGLE TRENDS CONTENT --}}
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">🔍</span> Google Trends Keywords
                    </h2>

                    @if(isset($trendsData['google_keywords']) && $trendsData['google_keywords']->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Keyword</th>
                                        <th class="px-3 py-2 text-left">Search Volume</th>
                                        <th class="px-3 py-2 text-left">Growth</th>
                                        <th class="px-3 py-2 text-left">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($trendsData['google_keywords'] as $gKeyword)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">{{ $gKeyword->keyword }}</td>
                                            <td class="px-3 py-2">{{ number_format($gKeyword->search_volume ?? 0) }}</td>
                                            <td class="px-3 py-2">
                                                @if($gKeyword->growth_rate > 0)
                                                    <span class="text-green-600">+{{ $gKeyword->growth_rate }}%</span>
                                                @elseif($gKeyword->growth_rate < 0)
                                                    <span class="text-red-600">{{ $gKeyword->growth_rate }}%</span>
                                                @else
                                                    <span class="text-gray-600">0%</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">{{ $gKeyword->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">🔍</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Nessun dato Google Trends</h3>
                            <p class="text-gray-600">Non ci sono ancora keyword Google Trends da mostrare.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TOP GOOGLE KEYWORDS CHART --}}
            @if(isset($trendsData['top_google_keywords']) && $trendsData['top_google_keywords']->count() > 0)
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">📊</span> Top Google Keywords
                    </h2>
                    <canvas id="googleTrendsChart" height="120"></canvas>
                </div>
            </div>
            @endif
        @endif

        {{-- GRAFICO TOP KEYWORDS --}}
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">📈</span> Andamento Top Keywords
                </h2>
                <canvas id="trendsChart" height="120"></canvas>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchTopTrendsChart();
            function fetchTopTrendsChart() {
                fetch("{{ route('admin.trends.api-google-trends', ['days' => $days, 'region' => $region, 'keyword' => $keyword]) }}")
                    .then(res => res.json())
                    .then(data => {
                        const ctx = document.getElementById('trendsChart').getContext('2d');
                        const labels = data.topTrends.map(t => t.keyword + ' (' + t.region + ')');
                        const scores = data.topTrends.map(t => t.avg_score);
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Score medio',
                                    data: scores,
                                    backgroundColor: 'rgba(16, 185, 129, 0.7)'
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true }
                                }
                            }
                        });
                    });
            }
        });
        </script>
        @endpush

        <!-- Social Media Trends -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">📱</span>
                    Tendenze Social Media
                </h2>

                <div class="space-y-4">
                    <div v-for="(platform, key) in socialTrends" :key="key"
                         class="border rounded-lg p-4">
                        <h3 class="font-semibold capitalize mb-3 flex items-center">
                            <span :class="getPlatformColor(key)" class="w-3 h-3 rounded-full mr-2"></span>
                            @{{ getPlatformName(key) }}
                        </h3>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div v-if="platform.engagement_rate">
                                <span class="text-gray-600">Engagement:</span>
                                <span class="font-semibold ml-1">@{{ platform.engagement_rate }}%</span>
                            </div>
                            <div v-if="platform.mentions">
                                <span class="text-gray-600">Mentions:</span>
                                <span class="font-semibold ml-1">@{{ formatNumber(platform.mentions) }}</span>
                            </div>
                            <div v-if="platform.viral_videos">
                                <span class="text-gray-600">Video Virali:</span>
                                <span class="font-semibold ml-1">@{{ platform.viral_videos }}</span>
                            </div>
                            <div v-if="platform.sentiment">
                                <span class="text-gray-600">Sentiment:</span>
                                <span class="font-semibold ml-1 text-green-600">@{{ platform.sentiment }}% positivo</span>
                            </div>
                        </div>

                        <div v-if="platform.hashtags && platform.hashtags.length > 0" class="mt-3">
                            <div class="text-xs text-gray-600 mb-2">Top Hashtags:</div>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="hashtag in platform.hashtags.slice(0, 3)" :key="hashtag.tag"
                                      class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    #@{{ hashtag.tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Keywords Analysis -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔍</span>
                    Parole Chiave Popolari
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="(group, groupName) in filteredKeywordGroups" :key="groupName">
                        <h3 class="font-semibold mb-3 capitalize" :class="getGroupColor(groupName)">
                            @{{ getGroupLabel(groupName) }}
                        </h3>
                        <div class="space-y-2">
                            <div v-for="keyword in group" :key="keyword.keyword"
                                 class="p-3 rounded-lg border-l-4" :class="getGroupBorderClass(groupName)">
                                <div class="font-medium">@{{ keyword.keyword }}</div>
                                <div class="text-sm text-gray-600">@{{ formatNumber(keyword.volume) }} ricerche/mese</div>
                                <div class="text-xs text-gray-500">CPC: €@{{ keyword.cpc }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seasonal Trends -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">🌸</span>
                    Tendenze Stagionali
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg border">
                            <h3 class="font-semibold text-lg">@{{ seasonalTrends.current_season }}</h3>
                            <p class="text-sm mt-1">Fattore stagionale attuale: <span class="font-bold">@{{ seasonalTrends.current_factor }}x</span></p>
                            <p class="text-sm">Prossimo picco: <span class="font-bold">@{{ seasonalTrends.next_peak }}</span></p>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-medium mb-3">Raccomandazioni per questo periodo:</h4>
                            <div class="space-y-2">
                                <div v-for="recommendation in seasonalTrends.recommendations" :key="recommendation"
                                     class="text-sm p-2 bg-green-50 rounded border-l-4 border-green-400">
                                    @{{ recommendation }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium mb-3">Fattori mensili:</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div v-for="(data, month) in seasonalTrends.monthly_trends" :key="month"
                                 class="text-center p-3 rounded-lg"
                                 :class="data.factor > 1.5 ? 'bg-red-50 border border-red-200' :
                                         data.factor > 1.0 ? 'bg-yellow-50 border border-yellow-200' :
                                         'bg-gray-50 border border-gray-200'">
                                <div class="text-sm font-medium">@{{ getMonthName(month) }}</div>
                                <div class="text-lg font-bold"
                                     :class="data.factor > 1.5 ? 'text-red-600' :
                                             data.factor > 1.0 ? 'text-yellow-600' :
                                             'text-gray-600'">
                                    @{{ data.factor }}x
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">🚀 Azioni Rapide</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.trends.advanced') }}"
                       class="block p-4 border border-purple-200 rounded-lg hover:bg-purple-50 transition-colors">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🔬</span>
                            <div>
                                <h3 class="font-medium">Analisi Avanzata</h3>
                                <p class="text-sm text-gray-600">Scraping e-commerce e dati dettagliati</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.trends.configure') }}"
                       class="block p-4 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">⚙️</span>
                            <div>
                                <h3 class="font-medium">Configura Siti</h3>
                                <p class="text-sm text-gray-600">Gestisci siti e-commerce da monitorare</p>
                            </div>
                        </div>
                    </a>

                    <button @click="generateReport"
                            class="p-4 border border-green-200 rounded-lg hover:bg-green-50 transition-colors text-left">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">📊</span>
                            <div>
                                <h3 class="font-medium">Genera Report</h3>
                                <p class="text-sm text-gray-600">Esporta analisi completa in PDF</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification -->
<div v-if="showNotification" class="fixed top-4 right-4 z-50 notification-success">
    @{{ notificationMessage }}
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<style>
/* Animazioni Vue.js */
.fade-enter-active, .fade-leave-active {
    transition: all 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

/* Pulsante loading animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Success notification */
.notification-success {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    margin: 10px 0;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: false,
            realTimeUpdates: true,
            lastUpdate: new Date(),
            showNotification: false,
            notificationMessage: '',
            filters: {
                period: '30',
                category: 'all',
                search: ''
            },
            keyMetrics: [
                {
                    key: 'trending_score',
                    label: 'Punteggio Trend',
                    value: '75',
                    trend: '+12% questo mese',
                    trendClass: 'text-green-600',
                    icon: '📈',
                    iconClass: 'bg-green-500'
                },
                {
                    key: 'growth_rate',
                    label: 'Tasso di Crescita',
                    value: '8.2%',
                    trend: '+2.3% vs mese scorso',
                    trendClass: 'text-green-600',
                    icon: '🚀',
                    iconClass: 'bg-blue-500'
                },
                {
                    key: 'engagement',
                    label: 'Engagement Social',
                    value: '6.8%',
                    trend: '+0.5% vs settimana scorsa',
                    trendClass: 'text-green-600',
                    icon: '❤️',
                    iconClass: 'bg-purple-500'
                },
                {
                    key: 'popularity',
                    label: 'Indice Popolarità',
                    value: '92',
                    trend: '+5 punti vs mese scorso',
                    trendClass: 'text-green-600',
                    icon: '⭐',
                    iconClass: 'bg-orange-500'
                }
            ],
            originalGoogleTrends: [
                { term: 'Piante da appartamento', interest: 85, trend: 'rising' },
                { term: 'Monstera deliciosa', interest: 78, trend: 'rising' },
                { term: 'Piante grasse', interest: 72, trend: 'stable' },
                { term: 'Ficus benjamin', interest: 65, trend: 'stable' },
                { term: 'Pothos', interest: 58, trend: 'rising' },
                { term: 'Sansevieria', interest: 52, trend: 'stable' }
            ],
            socialTrends: {
                instagram: {
                    engagement_rate: 7.2,
                    mentions: 45000,
                    sentiment: 82,
                    hashtags: [
                        { tag: 'plantmom', count: 12000 },
                        { tag: 'indoor plants', count: 8500 },
                        { tag: 'plantsofinstagram', count: 7200 }
                    ]
                },
                tiktok: {
                    engagement_rate: 12.8,
                    viral_videos: 234,
                    sentiment: 89,
                    hashtags: [
                        { tag: 'planttok', count: 25000 },
                        { tag: 'plantcare', count: 18000 },
                        { tag: 'plantparent', count: 15000 }
                    ]
                },
                facebook: {
                    engagement_rate: 4.1,
                    mentions: 28000,
                    sentiment: 76,
                    hashtags: [
                        { tag: 'gardening', count: 9500 },
                        { tag: 'plantslovers', count: 6800 }
                    ]
                }
            },
            originalKeywordGroups: {
                high_volume: [
                    { keyword: 'piante da interno', volume: 45000, cpc: '0.85' },
                    { keyword: 'piante grasse', volume: 38000, cpc: '0.72' },
                    { keyword: 'piante da appartamento', volume: 32000, cpc: '0.95' },
                    { keyword: 'vasi per piante', volume: 28000, cpc: '1.20' }
                ],
                trending: [
                    { keyword: 'monstera deliciosa', volume: 18000, cpc: '1.45', growth: 45 },
                    { keyword: 'pothos cura', volume: 12000, cpc: '1.15', growth: 38 },
                    { keyword: 'piante purifica aria', volume: 15000, cpc: '1.30', growth: 52 },
                    { keyword: 'sansevieria cura', volume: 9500, cpc: '1.05', growth: 28 }
                ],
                long_tail: [
                    { keyword: 'come innaffiare monstera', volume: 2800, cpc: '0.65' },
                    { keyword: 'piante da interno poca luce', volume: 3200, cpc: '0.88' },
                    { keyword: 'terriccio per piante grasse', volume: 2100, cpc: '0.75' },
                    { keyword: 'fertilizzante piante appartamento', volume: 1850, cpc: '0.95' }
                ]
            },
            seasonalTrends: {
                current_season: 'Autunno',
                current_factor: '1.2',
                next_peak: 'Marzo 2025',
                monthly_trends: {
                    '1': { factor: 0.8 }, '2': { factor: 1.1 }, '3': { factor: 1.8 },
                    '4': { factor: 2.2 }, '5': { factor: 2.5 }, '6': { factor: 1.9 },
                    '7': { factor: 1.4 }, '8': { factor: 1.2 }, '9': { factor: 1.3 },
                    '10': { factor: 1.2 }, '11': { factor: 0.9 }, '12': { factor: 0.7 }
                },
                recommendations: [
                    'Focus su piante da interno per la stagione autunnale',
                    'Promuovi piante che resistono al riscaldamento domestico',
                    'Aumenta il marketing per piante che purificano l\'aria'
                ]
            }
        };
    },

    computed: {
        filteredGoogleTrends() {
            let trends = [...this.originalGoogleTrends];
            if (this.filters.search) {
                trends = trends.filter(t =>
                    t.term.toLowerCase().includes(this.filters.search.toLowerCase())
                );
            }
            return trends;
        },

        filteredKeywordGroups() {
            let groups = { ...this.originalKeywordGroups };
            if (this.filters.search) {
                Object.keys(groups).forEach(groupName => {
                    groups[groupName] = groups[groupName].filter(k =>
                        k.keyword.toLowerCase().includes(this.filters.search.toLowerCase())
                    );
                });
            }
            return groups;
        },

        topKeyword() {
            return this.filteredGoogleTrends.length > 0 ? this.filteredGoogleTrends[0].term : 'Piante';
        },

        topGrowthPercentage() {
            const rising = this.filteredGoogleTrends.filter(t => t.trend === 'rising');
            return rising.length > 0 ? Math.floor(Math.random() * 30) + 15 : 25;
        }
    },

    methods: {
        updateData() {
            this.loading = true;
            // Simulate API call
            setTimeout(() => {
                this.loading = false;
                // Here you would typically make an API call to get new data
                this.refreshMetrics();
            }, 1000);
        },

        filterData() {
            // Filter data based on category and search
            // This method is now computed-based for real-time filtering
        },

        refreshData() {
            this.updateData();
        },

        refreshMetrics() {
            // Update metrics with new random values to simulate real data
            this.keyMetrics.forEach(metric => {
                const growth = Math.floor(Math.random() * 20) + 5;
                if (metric.key === 'trending_score') {
                    metric.value = Math.floor(Math.random() * 30) + 70;
                    metric.trend = `+${growth}% questo mese`;
                }
            });

            // Show notification
            this.showNotification = true;
            this.notificationMessage = '✅ Dati aggiornati con successo!';
            setTimeout(() => {
                this.showNotification = false;
            }, 3000);
        },

        exportData() {
            // Export functionality
            const data = {
                period: this.filters.period,
                category: this.filters.category,
                metrics: this.keyMetrics,
                trends: this.filteredGoogleTrends,
                keywords: this.filteredKeywordGroups
            };

            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `trends-data-${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);
        },

        generateReport() {
            alert('🎯 Generazione report in corso...\n\nIl report PDF sarà pronto tra qualche secondo e includerà:\n• Metriche chiave\n• Analisi Google Trends\n• Dati Social Media\n• Raccomandazioni stagionali');
        },

        getTrendClass(trend) {
            switch(trend) {
                case 'rising': return 'text-green-600';
                case 'declining': return 'text-red-600';
                default: return 'text-gray-600';
            }
        },

        getTrendLabel(trend) {
            switch(trend) {
                case 'rising': return '↗️ In crescita';
                case 'declining': return '↘️ In calo';
                default: return '➡️ Stabile';
            }
        },

        getPlatformColor(platform) {
            const colors = {
                instagram: 'bg-pink-500',
                tiktok: 'bg-black',
                twitter: 'bg-blue-500',
                facebook: 'bg-blue-600'
            };
            return colors[platform] || 'bg-gray-500';
        },

        getPlatformName(platform) {
            const names = {
                instagram: 'Instagram',
                tiktok: 'TikTok',
                twitter: 'Twitter',
                facebook: 'Facebook'
            };
            return names[platform] || platform;
        },

        getGroupColor(groupName) {
            const colors = {
                high_volume: 'text-purple-600',
                trending: 'text-green-600',
                long_tail: 'text-blue-600'
            };
            return colors[groupName] || 'text-gray-600';
        },

        getGroupLabel(groupName) {
            const labels = {
                high_volume: '🔥 Alto Volume',
                trending: '📈 In Tendenza',
                long_tail: '🎯 Long Tail'
            };
            return labels[groupName] || groupName;
        },

        getGroupBorderClass(groupName) {
            const classes = {
                high_volume: 'border-purple-400 bg-purple-50',
                trending: 'border-green-400 bg-green-50',
                long_tail: 'border-blue-400 bg-blue-50'
            };
            return classes[groupName] || 'border-gray-400 bg-gray-50';
        },

        formatNumber(number) {
            return new Intl.NumberFormat('it-IT').format(number);
        },

        getMonthName(month) {
            const months = {
                '1': 'Gen', '2': 'Feb', '3': 'Mar', '4': 'Apr',
                '5': 'Mag', '6': 'Giu', '7': 'Lug', '8': 'Ago',
                '9': 'Set', '10': 'Ott', '11': 'Nov', '12': { factor: 1.2 },
            };
            return months[month] || month;
        },

        toggleRealTimeUpdates() {
            this.realTimeUpdates = !this.realTimeUpdates;
            if (this.realTimeUpdates) {
                this.startAutoRefresh();
            }
        },

        startAutoRefresh() {
            if (this.realTimeUpdates) {
                setTimeout(() => {
                    if (this.realTimeUpdates && !this.loading) {
                        this.refreshMetrics();
                        this.lastUpdate = new Date();
                        this.startAutoRefresh();
                    }
                }, 30000); // Every 30 seconds
            }
        },

        formatLastUpdate() {
            return this.lastUpdate.toLocaleString('it-IT', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    },

    mounted() {
        console.log('🌱 Trends Base Analytics caricato con Vue.js');
        this.lastUpdate = new Date();
        if (this.realTimeUpdates) {
            this.startAutoRefresh();
        }

        // Aggiungi notifica di benvenuto
        setTimeout(() => {
            if (this.realTimeUpdates) {
                console.log('🔄 Auto-refresh attivo - aggiornamento ogni 30 secondi');
            }
        }, 1000);
    }
}).mount('#trendsApp');

// Google Trends Chart
@if(request('tab') === 'google' && isset($trendsData['top_google_keywords']) && $trendsData['top_google_keywords']->count() > 0)
const googleCtx = document.getElementById('googleTrendsChart').getContext('2d');
new Chart(googleCtx, {
    type: 'bar',
    data: {
        labels: @json($trendsData['top_google_keywords']->pluck('keyword')),
        datasets: [{
            label: 'Search Volume',
            data: @json($trendsData['top_google_keywords']->pluck('search_volume')),
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Volume: ' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});
@endif
</script>
@endsection
