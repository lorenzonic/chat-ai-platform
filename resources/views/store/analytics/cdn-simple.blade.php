<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - ChatAI Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet MarkerCluster for grouping markers -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
</head>
<body class="bg-gray-100">
    @include('store.layouts.navigation')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">📊 Analytics Dashboard</h1>
                <p class="text-gray-600">Dashboard completa per {{ $store->name }}</p>
            </div>

            <!-- Analytics Component -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">📊 Analytics Data</h2>
                <div id="analytics-app">
                    <simple-analytics></simple-analytics>
                </div>
            </div>

            <!-- Geographic Map -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">🗺️ Mappa Utilizzi</h2>
                <p class="text-gray-600 mb-4">Visualizza da dove provengono le chat e le scansioni QR code</p>
                <div id="map" style="height: 500px; border-radius: 8px;" class="border"></div>
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                        <span>Chat/Interazioni</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                        <span>Lead Generati</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-purple-500 rounded-full mr-2"></div>
                        <span>Entrambi</span>
                    </div>
                </div>
            </div>

            <!-- Domande Frequenti -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">❓ Domande Più Frequenti</h2>
                <div id="frequent-questions">
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-lg">📊</div>
                        <p class="text-gray-500">Caricamento domande frequenti...</p>
                    </div>
                </div>
            </div>

            <!-- Piante Più Ricercate -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">🌱 Piante Più Ricercate</h2>
                <div id="popular-plants">
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-lg">🌿</div>
                        <p class="text-gray-500">Caricamento piante più ricercate...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        console.log('🔍 CDN Analytics page loading...');

        // Check Vue
        if (typeof Vue !== 'undefined') {
            console.log('✅ Vue loaded:', Vue.version);

            // Initialize Vue app
            const { createApp } = Vue;

            // Analytics component with real data
            const analyticsApp = createApp({
                data() {
                    return {
                        loading: false,
                        stats: {
                            totalChats: {{ \App\Models\ChatLog::where('store_id', $store->id)->count() }},
                            totalLeads: {{ \App\Models\Lead::where('store_id', $store->id)->count() }},
                            recentChats: {{ \App\Models\ChatLog::where('store_id', $store->id)->where('created_at', '>=', now()->subDays(7))->count() }},
                            todayChats: {{ \App\Models\ChatLog::where('store_id', $store->id)->whereDate('created_at', today())->count() }}
                        }
                    }
                },
                computed: {
                    conversionRate() {
                        if (this.stats.totalChats === 0) return 0;
                        return ((this.stats.totalLeads / this.stats.totalChats) * 100).toFixed(1);
                    }
                },
                template: `
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600">@{{ stats.totalChats }}</div>
                                <div class="text-sm text-blue-800">Chat Totali</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">@{{ stats.totalLeads }}</div>
                                <div class="text-sm text-green-800">Lead Generati</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-yellow-600">@{{ stats.recentChats }}</div>
                                <div class="text-sm text-yellow-800">Ultimi 7 Giorni</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-600">@{{ conversionRate }}%</div>
                                <div class="text-sm text-purple-800">Tasso Conversione</div>
                            </div>
                        </div>

                        <div v-if="stats.totalChats === 0" class="text-center py-8">
                            <div class="text-4xl mb-4">🤖</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Nessuna chat ancora</h3>
                            <p class="text-gray-600">Il tuo chatbot non ha ancora ricevuto conversazioni.</p>
                        </div>

                        <div v-else class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">📈 Statistiche</h3>
                            <p class="text-gray-600">Il tuo chatbot ha gestito <strong>@{{ stats.totalChats }}</strong> conversazioni e generato <strong>@{{ stats.totalLeads }}</strong> lead.</p>
                        </div>
                    </div>
                `,
                components: {
                    'simple-analytics': {
                        template: '<div></div>' // Placeholder
                    }
                }
            });
            analyticsApp.mount('#analytics-app');

        } else {
            document.getElementById('vue-status').innerHTML = `
                <div class="font-semibold text-red-800">❌ Vue.js</div>
                <div class="text-sm text-red-600">Errore caricamento CDN</div>
            `;
        }

        // Initialize map (always, regardless of Vue status)
        initializeMap();

        // Load additional analytics data
        loadFrequentQuestions();
        loadPopularPlants();

        // Initialize Geographic Map
        function initializeMap() {
            console.log('🗺️ Initializing map...');

            // Create map centered on Italy
            const map = L.map('map').setView([41.9028, 12.4964], 6);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create marker cluster group
            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50
            });

            // Load geographic data from server (using debug endpoint for now)
            fetch('/debug/geographic', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('📍 Geographic data loaded:', data.geographic_data);

                if (data.geographic_data && data.geographic_data.length > 0) {
                    addMarkersToMap(map, markers, data.geographic_data);
                } else {
                    // Show message if no geographic data
                    const noDataDiv = L.divIcon({
                        html: '<div style="background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;"><strong>📍 Nessun dato geografico</strong><br>Le interazioni non hanno ancora dati di posizione</div>',
                        className: 'custom-div-icon',
                        iconSize: [200, 60],
                        iconAnchor: [100, 30]
                    });

                    L.marker([41.9028, 12.4964], { icon: noDataDiv }).addTo(map);
                }
            })
            .catch(error => {
                console.error('❌ Error loading geographic data:', error);

                // Show error message
                const errorDiv = L.divIcon({
                    html: '<div style="background: #fee; padding: 10px; border-radius: 8px; border: 1px solid #fcc; text-align: center;"><strong>⚠️ Errore caricamento dati</strong><br>Impossibile caricare i dati geografici</div>',
                    className: 'custom-div-icon',
                    iconSize: [200, 60],
                    iconAnchor: [100, 30]
                });

                L.marker([41.9028, 12.4964], { icon: errorDiv }).addTo(map);
            });
        }

        function addMarkersToMap(map, markers, geographicData) {
            geographicData.forEach(point => {
                let color, size;

                // Determine marker color and size based on data
                if (point.leads_count > 0 && point.interactions_count > 0) {
                    color = '#8b5cf6'; // Purple for both
                    size = Math.max(15, Math.min(35, point.total * 3));
                } else if (point.leads_count > 0) {
                    color = '#10b981'; // Green for leads
                    size = Math.max(12, Math.min(28, point.leads_count * 4));
                } else {
                    color = '#3b82f6'; // Blue for interactions
                    size = Math.max(10, Math.min(25, point.interactions_count * 3));
                }

                // Create custom marker
                const marker = L.circleMarker([point.lat, point.lng], {
                    radius: size,
                    fillColor: color,
                    color: '#fff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.8
                });

                // Create popup content
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: bold;">
                            📍 ${point.city}
                        </h3>
                        <p style="margin: 0 0 4px 0; color: #666; font-size: 14px;">
                            ${point.country}
                        </p>
                        <hr style="margin: 8px 0;">
                        <div style="font-size: 14px;">
                            ${point.interactions_count > 0 ? `<div>💬 <strong>${point.interactions_count}</strong> interazioni</div>` : ''}
                            ${point.leads_count > 0 ? `<div>🎯 <strong>${point.leads_count}</strong> lead generati</div>` : ''}
                            <div style="margin-top: 8px; font-weight: bold;">
                                📊 Totale: ${point.total}
                            </div>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markers.addLayer(marker);
            });

            // Add marker cluster to map
            map.addLayer(markers);

            // Fit map to show all markers
            if (geographicData.length > 0) {
                const group = new L.featureGroup(map._layers);
                if (Object.keys(group._layers).length > 0) {
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }
        }

        // Load frequent questions
        function loadFrequentQuestions() {
            console.log('❓ Loading frequent questions...');

            // Simulate loading frequent questions from interactions
            fetch('/debug/geographic')
            .then(response => response.json())
            .then(data => {
                // For now, create some sample questions based on plant-related topics
                const sampleQuestions = [
                    { question: "Come curare le piante grasse?", count: 15 },
                    { question: "Quando annaffiare le rose?", count: 12 },
                    { question: "Quali piante per il giardino?", count: 10 },
                    { question: "Piante che resistono al sole?", count: 8 },
                    { question: "Come potare le siepi?", count: 7 },
                    { question: "Fertilizzante per piante da appartamento", count: 6 },
                    { question: "Piante per balcone piccolo", count: 5 },
                    { question: "Malattie delle piante", count: 4 }
                ];

                displayFrequentQuestions(sampleQuestions);
            })
            .catch(error => {
                console.error('Error loading questions:', error);
                document.getElementById('frequent-questions').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-400 text-lg">⚠️</div>
                        <p class="text-red-500">Errore nel caricamento delle domande</p>
                    </div>
                `;
            });
        }

        // Load popular plants
        function loadPopularPlants() {
            console.log('🌱 Loading popular plants...');

            // Simulate loading plant names from interactions/chats
            const samplePlants = [
                { name: "Rosa", count: 25, emoji: "🌹" },
                { name: "Basilico", count: 20, emoji: "🌿" },
                { name: "Lavanda", count: 18, emoji: "💜" },
                { name: "Geranio", count: 15, emoji: "🌺" },
                { name: "Cactus", count: 12, emoji: "🌵" },
                { name: "Orchidea", count: 10, emoji: "🌸" },
                { name: "Ficus", count: 8, emoji: "🍃" },
                { name: "Pothos", count: 6, emoji: "🌱" }
            ];

            displayPopularPlants(samplePlants);
        }

        // Display frequent questions
        function displayFrequentQuestions(questions) {
            const container = document.getElementById('frequent-questions');

            const html = questions.map((q, index) => `
                <div class="flex items-center justify-between p-3 ${index < questions.length - 1 ? 'border-b border-gray-100' : ''}">
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-800">${q.question}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">${q.count} volte</span>
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600">${q.count}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;
        }

        // Display popular plants
        function displayPopularPlants(plants) {
            const container = document.getElementById('popular-plants');

            const html = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    ${plants.map(plant => `
                        <div class="text-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="text-2xl mb-2">${plant.emoji}</div>
                            <div class="font-semibold text-gray-800">${plant.name}</div>
                            <div class="text-sm text-green-600">${plant.count} ricerche</div>
                        </div>
                    `).join('')}
                </div>
            `;

            container.innerHTML = html;
        }
    </script>
</body>
</html>
