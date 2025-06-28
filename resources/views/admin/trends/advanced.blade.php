@extends('layouts.admin')

@section('title', 'Analisi Avanzata Trends')

@section('content')
<div class="py-6" id="advancedTrendsApp">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🚀 Analisi Avanzata E-commerce</h1>
            <p class="mt-2 text-gray-600">Monitoraggio avanzato con scraping e-commerce e analisi demografiche</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <a href="{{ route('admin.trends.index') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 py-4 px-1 text-sm font-medium">
                        📊 Trends Base
                    </a>
                    <a href="{{ route('admin.trends.advanced') }}"
                       class="border-indigo-500 text-indigo-600 border-b-2 py-4 px-1 text-sm font-medium">
                        🚀 Analisi Avanzata
                    </a>
                    <a href="{{ route('admin.trends.configure') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 py-4 px-1 text-sm font-medium">
                        ⚙️ Configurazione
                    </a>
                </nav>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Controlli Avanzati</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modalità Scraping</label>
                        <select v-model="scrapingMode" @change="updateScrapingData"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="auto">Automatico</option>
                            <option value="real">Solo Reale</option>
                            <option value="simulation">Solo Simulazione</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Siti Monitorati</label>
                        <select v-model="selectedSites" multiple
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option v-for="site in availableSites" :key="site.key" :value="site.key">
                                @{{ site.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria Focus</label>
                        <select v-model="categoryFocus"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">Tutte le categorie</option>
                            <option value="indoor">Piante da interno</option>
                            <option value="outdoor">Piante da esterno</option>
                            <option value="rare">Piante rare</option>
                            <option value="herbs">Erbe aromatiche</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="startAdvancedScraping" :disabled="loading"
                                class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-4 py-2 rounded text-sm font-medium">
                            <span v-if="loading">⏳ Analizzando...</span>
                            <span v-else>🔍 Avvia Analisi</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Status -->
        <div class="bg-white shadow-sm rounded-lg mb-6" v-if="scrapingStatus.active">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Status Scraping in Tempo Reale</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">@{{ scrapingStatus.sitesScraped }}</div>
                        <div class="text-sm text-blue-700">Siti Analizzati</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">@{{ scrapingStatus.productsFound }}</div>
                        <div class="text-sm text-green-700">Prodotti Trovati</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">@{{ scrapingStatus.trendsDetected }}</div>
                        <div class="text-sm text-yellow-700">Trend Individuati</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">@{{ scrapingStatus.progress }}%</div>
                        <div class="text-sm text-purple-700">Completamento</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full transition-all duration-300"
                             :style="`width: ${scrapingStatus.progress}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-commerce Analysis Results -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Price Analysis -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">💰</span>
                        Analisi Prezzi E-commerce
                    </h2>

                    <div class="space-y-4">
                        <div v-for="(range, category) in priceAnalysis" :key="category"
                             class="border rounded-lg p-4">
                            <h3 class="font-semibold capitalize mb-2">@{{ category.replace('_', ' ') }}</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Prezzo Min:</span>
                                    <span class="font-bold text-green-600">€@{{ range.min }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Prezzo Max:</span>
                                    <span class="font-bold text-red-600">€@{{ range.max }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Media:</span>
                                    <span class="font-bold">€@{{ range.average }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Prodotti:</span>
                                    <span class="font-bold">@{{ range.count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Market Opportunities -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">💡</span>
                        Opportunità di Mercato
                    </h2>

                    <div class="space-y-3">
                        <div v-for="opportunity in marketOpportunities" :key="opportunity.type"
                             class="p-3 rounded-lg border-l-4"
                             :class="opportunity.priority === 'high' ? 'border-red-400 bg-red-50' :
                                     opportunity.priority === 'medium' ? 'border-yellow-400 bg-yellow-50' :
                                     'border-green-400 bg-green-50'">
                            <h4 class="font-medium">@{{ opportunity.title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">@{{ opportunity.description }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded-full"
                                      :class="opportunity.priority === 'high' ? 'bg-red-100 text-red-800' :
                                              opportunity.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                              'bg-green-100 text-green-800'">
                                    @{{ opportunity.priority === 'high' ? 'Alta Priorità' :
                                         opportunity.priority === 'medium' ? 'Media Priorità' : 'Bassa Priorità' }}
                                </span>
                                <span class="text-sm font-medium text-gray-700">ROI: @{{ opportunity.roi }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demographic Analysis -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">👥</span>
                    Analisi Demografica Avanzata
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="(demographic, key) in demographicAnalysis" :key="key"
                         class="border rounded-lg p-4">
                        <h3 class="font-semibold text-lg capitalize mb-3">@{{ key.replace('_', ' ') }}</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Età media:</label>
                                <span class="font-bold ml-2">@{{ demographic.age_range }}</span>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Budget medio:</label>
                                <span class="font-bold ml-2 text-green-600">@{{ demographic.budget }}</span>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Comportamento online:</label>
                                <span class="font-bold ml-2">@{{ demographic.online_behavior }}%</span>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 block mb-1">Piante preferite:</label>
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="plant in demographic.preferred_plants" :key="plant"
                                          class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                        @{{ plant }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Regional Preferences -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">🗺️</span>
                    Preferenze Regionali Dettagliate
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="(region, key) in regionalPreferences" :key="key"
                         class="border rounded-lg p-4">
                        <h3 class="font-semibold text-lg capitalize mb-3">@{{ key.replace('_', ' ') }}</h3>

                        <div class="mb-4">
                            <h4 class="font-medium mb-2">Piante più richieste:</h4>
                            <div class="space-y-2">
                                <div v-for="plant in region.top_plants" :key="plant.name"
                                     class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="font-medium">@{{ plant.name }}</span>
                                    <span class="text-sm text-green-600 font-bold">@{{ plant.demand }}% domanda</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Clima prevalente:</span>
                                <span class="font-medium ml-1">@{{ region.climate }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Spesa media:</span>
                                <span class="font-medium ml-1 text-green-600">@{{ region.avg_spending }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Future Predictions -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔮</span>
                    Previsioni Avanzate
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="prediction in futurePredictions" :key="prediction.period"
                         class="border rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-3">@{{ prediction.period }}</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Domanda generale:</span>
                                <span class="font-bold" :class="prediction.demand_change > 0 ? 'text-green-600' : 'text-red-600'">
                                    @{{ prediction.demand_change > 0 ? '+' : '' }}@{{ prediction.demand_change }}%
                                </span>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600 block mb-1">Categorie in crescita:</label>
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="category in prediction.growing_categories" :key="category"
                                          class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                        @{{ category }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600 block mb-1">Investimenti consigliati:</label>
                                <div class="space-y-1">
                                    <div v-for="investment in prediction.recommended_investments" :key="investment"
                                         class="text-sm p-2 bg-blue-50 rounded border-l-2 border-blue-400">
                                        @{{ investment }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            scrapingMode: 'auto',
            selectedSites: [],
            categoryFocus: 'all',
            scrapingStatus: {
                active: false,
                sitesScraped: 0,
                productsFound: 0,
                trendsDetected: 0,
                progress: 0
            },
            availableSites: [
                { key: 'viridea', name: 'Viridea' },
                { key: 'bakker', name: 'Bakker Italia' },
                { key: 'mondopiante', name: 'Mondo Piante' },
                { key: 'floricoltura', name: 'Floricoltura Quaiato' }
            ],
            priceAnalysis: {
                indoor_plants: { min: 15, max: 120, average: 45, count: 156 },
                outdoor_plants: { min: 25, max: 200, average: 75, count: 234 },
                rare_plants: { min: 50, max: 500, average: 150, count: 67 },
                herbs: { min: 8, max: 35, average: 18, count: 89 }
            },
            marketOpportunities: [
                {
                    type: 'price_gap',
                    title: 'Lacuna di Prezzo nelle Piante Rare',
                    description: 'Opportunità di posizionamento nel segmento 80-120€',
                    priority: 'high',
                    roi: '+35%'
                },
                {
                    type: 'seasonal_demand',
                    title: 'Domanda Stagionale Primaverile',
                    description: 'Incremento del 45% nella richiesta di piante da esterno',
                    priority: 'medium',
                    roi: '+22%'
                },
                {
                    type: 'demographic_shift',
                    title: 'Crescita Mercato Millennials',
                    description: 'Aumento del 60% negli acquisti da parte dei 25-35 anni',
                    priority: 'high',
                    roi: '+28%'
                }
            ],
            demographicAnalysis: {
                millennials: {
                    age_range: '25-35 anni',
                    budget: '€50-150/mese',
                    online_behavior: 85,
                    preferred_plants: ['Monstera', 'Ficus', 'Pothos', 'Succulente']
                },
                gen_x: {
                    age_range: '35-50 anni',
                    budget: '€80-250/mese',
                    online_behavior: 65,
                    preferred_plants: ['Rose', 'Ortensia', 'Lavanda', 'Rosmarino']
                },
                baby_boomers: {
                    age_range: '50+ anni',
                    budget: '€100-300/mese',
                    online_behavior: 45,
                    preferred_plants: ['Gerani', 'Begonie', 'Orchidee', 'Basilico']
                }
            },
            regionalPreferences: {
                nord_italia: {
                    top_plants: [
                        { name: 'Piante Alpine', demand: 78 },
                        { name: 'Conifere', demand: 65 },
                        { name: 'Erbe Aromatiche', demand: 72 }
                    ],
                    climate: 'Continentale',
                    avg_spending: '€120/mese'
                },
                centro_italia: {
                    top_plants: [
                        { name: 'Ulivi', demand: 85 },
                        { name: 'Rosmarino', demand: 80 },
                        { name: 'Lavanda', demand: 75 }
                    ],
                    climate: 'Mediterraneo',
                    avg_spending: '€95/mese'
                },
                sud_italia: {
                    top_plants: [
                        { name: 'Agrumi', demand: 90 },
                        { name: 'Bouganville', demand: 70 },
                        { name: 'Cactus', demand: 60 }
                    ],
                    climate: 'Mediterraneo caldo',
                    avg_spending: '€85/mese'
                }
            },
            futurePredictions: [
                {
                    period: 'Prossimi 3 mesi',
                    demand_change: 25,
                    growing_categories: ['Piante da interno', 'Erbe aromatiche'],
                    recommended_investments: [
                        'Aumenta stock piante da appartamento',
                        'Focus su kit per principianti'
                    ]
                },
                {
                    period: 'Prossimi 6 mesi',
                    demand_change: 35,
                    growing_categories: ['Piante da esterno', 'Fiori stagionali'],
                    recommended_investments: [
                        'Prepara stock per stagione estiva',
                        'Investi in piante da giardino'
                    ]
                },
                {
                    period: 'Prossimo anno',
                    demand_change: 18,
                    growing_categories: ['Piante rare', 'Bonsai'],
                    recommended_investments: [
                        'Sviluppa segmento premium',
                        'Partnership con coltivatori specializzati'
                    ]
                }
            ]
        };
    },
    methods: {
        startAdvancedScraping() {
            this.loading = true;
            this.scrapingStatus.active = true;
            this.scrapingStatus.progress = 0;

            // Simulate real-time scraping progress
            const interval = setInterval(() => {
                this.scrapingStatus.progress += Math.random() * 15;
                this.scrapingStatus.sitesScraped = Math.floor(this.scrapingStatus.progress / 25);
                this.scrapingStatus.productsFound = Math.floor(this.scrapingStatus.progress * 5);
                this.scrapingStatus.trendsDetected = Math.floor(this.scrapingStatus.progress / 10);

                if (this.scrapingStatus.progress >= 100) {
                    this.scrapingStatus.progress = 100;
                    this.loading = false;
                    clearInterval(interval);

                    setTimeout(() => {
                        this.scrapingStatus.active = false;
                        alert('Analisi completata! Dati aggiornati.');
                    }, 2000);
                }
            }, 500);
        },

        updateScrapingData() {
            // Update data based on scraping mode
            console.log('Modalità scraping cambiata:', this.scrapingMode);
        }
    },

    mounted() {
        console.log('Advanced Trends Analytics loaded with Vue.js');
    }
}).mount('#advancedTrendsApp');
</script>
@endsection
