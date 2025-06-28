@extends('layouts.admin')

@section('title', 'Analisi Tendenze Piante')

@section('content')
<div class="py-6" id="trendsApp">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🌱 Analisi Tendenze Piante</h1>
            <p class="mt-2 text-gray-600">Panoramica completa dei trend nel settore delle piante e giardinaggio</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <a href="{{ route('admin.trends.index') }}" 
                       class="border-indigo-500 text-indigo-600 border-b-2 py-4 px-1 text-sm font-medium">
                        📊 Trends Base
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

        <!-- Filters and Controls -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="periodFilter" class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                            <select v-model="filters.period" @change="updateData" 
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="7">Ultimi 7 giorni</option>
                                <option value="30">Ultimi 30 giorni</option>
                                <option value="90">Ultimi 90 giorni</option>
                            </select>
                        </div>
                        <div>
                            <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select v-model="filters.category" @change="filterData"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all">Tutte le categorie</option>
                                <option value="indoor">Piante da interno</option>
                                <option value="outdoor">Piante da esterno</option>
                                <option value="herbs">Erbe aromatiche</option>
                                <option value="succulents">Piante grasse</option>
                            </select>
                        </div>
                        <div>
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                            <input v-model="filters.search" @input="filterData" type="text" 
                                   placeholder="Cerca piante..."
                                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button @click="refreshData" :disabled="loading" 
                                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-2 rounded text-sm font-medium">
                            <span v-if="loading">⏳ Caricamento...</span>
                            <span v-else>🔄 Aggiorna</span>
                        </button>
                        <button @click="exportData" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
                            📊 Esporta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6" v-for="metric in keyMetrics" :key="metric.key">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div :class="metric.iconClass" class="w-10 h-10 rounded-md flex items-center justify-center">
                            <span v-html="metric.icon" class="text-white text-lg"></span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">@{{ metric.label }}</h3>
                        <p class="text-2xl font-bold text-gray-900">@{{ metric.value }}</p>
                        <p class="text-sm" :class="metric.trendClass">@{{ metric.trend }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Google Trends -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">📈</span>
                        Google Trends - Italia
                    </h2>
                    
                    <div class="space-y-4">
                        <div v-for="keyword in googleTrends.keywords" :key="keyword.term" 
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <span class="font-medium">@{{ keyword.term }}</span>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                         :style="`width: ${keyword.interest}%`"></div>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <span class="text-sm font-medium">@{{ keyword.interest }}%</span>
                                <div :class="getTrendClass(keyword.trend)" class="text-xs">
                                    @{{ getTrendLabel(keyword.trend) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                @{{ key }}
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
                            
                            <div v-if="platform.hashtags" class="mt-3">
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
        </div>

        <!-- Popular Keywords Analysis -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔍</span>
                    Analisi Parole Chiave Popolari
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="(group, groupName) in keywordGroups" :key="groupName">
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

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: false,
            filters: {
                period: '30',
                category: 'all',
                search: ''
            },
            keyMetrics: [
                {
                    key: 'trending_score',
                    label: 'Punteggio Trend',
                    value: '{{ $performance["trending_score"]["total_score"] ?? "75" }}',
                    trend: '{{ $performance["trending_score"]["rating"] ?? "+12% questo mese" }}',
                    trendClass: 'text-green-600',
                    icon: '📈',
                    iconClass: 'bg-green-500'
                },
                {
                    key: 'growth_rate',
                    label: 'Tasso di Crescita',
                    value: '{{ $performance["growth_rate"]["rate"] ?? "8.2" }}%',
                    trend: '+2.3% vs mese scorso',
                    trendClass: 'text-green-600',
                    icon: '🚀',
                    iconClass: 'bg-blue-500'
                },
                {
                    key: 'engagement',
                    label: 'Engagement Social',
                    value: '{{ $performance["engagement_rate"]["rate"] ?? "6.8" }}%',
                    trend: '+0.5% vs settimana scorsa',
                    trendClass: 'text-green-600',
                    icon: '❤️',
                    iconClass: 'bg-purple-500'
                },
                {
                    key: 'conversion',
                    label: 'Tasso Conversione',
                    value: '{{ $performance["conversion_rate"]["rate"] ?? "3.4" }}%',
                    trend: '+1.2% vs mese scorso',
                    trendClass: 'text-green-600',
                    icon: '💰',
                    iconClass: 'bg-orange-500'
                }
            ],
            googleTrends: {
                keywords: [
                    @foreach($trendsData['google_trends']['keywords'] ?? [] as $index => $keyword)
                    {
                        term: '{{ $keyword["term"] ?? "Piante" }}',
                        interest: {{ $keyword["interest"] ?? 0 }},
                        trend: '{{ $keyword["trend"] ?? "stable" }}'
                    }@if(!$loop->last),@endif
                    @endforeach
                ]
            },
            socialTrends: {
                @foreach($trendsData['social_trends'] ?? [] as $platform => $data)
                {{ $platform }}: {
                    engagement_rate: {{ $data['engagement_rate'] ?? 0 }},
                    @if(isset($data['mentions']))
                    mentions: {{ $data['mentions'] }},
                    @endif
                    @if(isset($data['viral_videos']))
                    viral_videos: {{ $data['viral_videos'] }},
                    @endif
                    @if(isset($data['sentiment']))
                    sentiment: {{ $data['sentiment'] }},
                    @endif
                    @if(isset($data['hashtags']))
                    hashtags: [
                        @foreach($data['hashtags'] as $hashtag)
                        { tag: '{{ $hashtag["tag"] }}', count: {{ $hashtag["count"] }} }@if(!$loop->last),@endif
                        @endforeach
                    ]
                    @endif
                }@if(!$loop->last),@endif
                @endforeach
            },
            keywordGroups: {
                high_volume: [
                    @foreach($trendsData['plant_keywords']['high_volume'] ?? [] as $keyword)
                    {
                        keyword: '{{ $keyword["keyword"] }}',
                        volume: {{ $keyword["volume"] }},
                        cpc: '{{ $keyword["cpc"] }}'
                    }@if(!$loop->last),@endif
                    @endforeach
                ],
                trending: [
                    @foreach($trendsData['plant_keywords']['trending'] ?? [] as $keyword)
                    {
                        keyword: '{{ $keyword["keyword"] }}',
                        volume: {{ $keyword["volume"] }},
                        cpc: '{{ $keyword["cpc"] }}',
                        growth: {{ $keyword["growth"] ?? 0 }}
                    }@if(!$loop->last),@endif
                    @endforeach
                ],
                long_tail: [
                    @foreach($trendsData['plant_keywords']['long_tail'] ?? [] as $keyword)
                    {
                        keyword: '{{ $keyword["keyword"] }}',
                        volume: {{ $keyword["volume"] }},
                        cpc: '{{ $keyword["cpc"] }}'
                    }@if(!$loop->last),@endif
                    @endforeach
                ]
            },
            seasonalTrends: {
                current_season: '{{ $trendsData["seasonal_trends"]["current_season"] ?? "Primavera" }}',
                current_factor: '{{ $trendsData["seasonal_trends"]["current_factor"] ?? "1.8" }}',
                next_peak: '{{ $trendsData["seasonal_trends"]["next_peak"] ?? "Maggio 2025" }}',
                monthly_trends: {
                    @foreach($trendsData['seasonal_trends']['monthly_trends'] ?? [] as $month => $data)
                    '{{ $month }}': { factor: {{ $data['factor'] }} }@if(!$loop->last),@endif
                    @endforeach
                },
                recommendations: [
                    'Aumenta lo stock di piante da esterno per la stagione',
                    'Focus su piante fiorite per questo periodo',
                    'Promuovi kit per giardinaggio beginner'
                ]
            }
        };
    },
    methods: {
        updateData() {
            this.loading = true;
            // Simulate API call
            setTimeout(() => {
                this.loading = false;
                // Here you would typically make an API call to get new data
                window.location.href = `{{ route('admin.trends.index') }}?days=${this.filters.period}`;
            }, 1000);
        },
        
        filterData() {
            // Filter data based on category and search
            // This would filter the displayed data without making a new API call
        },
        
        refreshData() {
            this.updateData();
        },
        
        exportData() {
            // Export functionality
            window.open('{{ route("admin.trends.index") }}?export=pdf', '_blank');
        },
        
        generateReport() {
            // Generate comprehensive report
            alert('Generazione report in corso...');
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
                '9': 'Set', '10': 'Ott', '11': 'Nov', '12': 'Dic'
            };
            return months[month] || month;
        }
    },
    
    mounted() {
        // Initialize any additional functionality when component is mounted
        console.log('Trends analytics loaded with Vue.js');
    }
}).mount('#trendsApp');
</script>
@endsection
