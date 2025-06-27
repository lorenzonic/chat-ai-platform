@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">📊 Analytics Dashboard</h1>
            <p class="mt-2 text-gray-600">Comprehensive analytics across all stores and QR codes</p>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold mb-4">Filters & Controls</h2>
                <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex flex-wrap gap-4 items-end">
                    <!-- Store Filter -->
                    <div class="flex-1 min-w-48">
                        <label for="store_id" class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                        <select name="store_id" id="store_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" {{ request('store_id') == 'all' ? 'selected' : '' }}>All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="flex-1 min-w-36">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex-1 min-w-36">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.analytics.export') }}?{{ http_build_query(request()->only(['store_id', 'start_date', 'end_date'])) }}&format=csv"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Export CSV
                        </a>
                        <a href="{{ route('admin.analytics.export') }}?{{ http_build_query(request()->only(['store_id', 'start_date', 'end_date'])) }}&format=json"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Export JSON
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Stores</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_stores'] }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-600">
                        <span class="text-green-600 font-medium">{{ $stats['active_stores'] }}</span>
                        <span class="ml-1">active</span>
                        <span class="mx-2">•</span>
                        <span class="text-purple-600 font-medium">{{ $stats['premium_stores'] }}</span>
                        <span class="ml-1">premium</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">QR Code Scans</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_scans']) }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <span>{{ $stats['total_qr_codes'] }} QR codes active</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Chat Messages</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_chats']) }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <span>AI-powered conversations</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Interactions</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_interactions']) }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <span>{{ number_format($stats['newsletter_signups']) }} newsletter signups</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="analytics-grid grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Scans Over Time Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">QR Scans Over Time</h3>
                    <div class="chart-container">
                        <canvas id="scansChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chat Activity Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Chat Activity</h3>
                    <div class="chart-container">
                        <canvas id="chatsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Device Types Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Device Types</h3>
                    <div class="chart-container">
                        <canvas id="deviceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Store Performance Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Store Performance</h3>
                    <div class="chart-container">
                        <canvas id="storeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top QR Codes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Top Performing QR Codes</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($chartsData['top_qr_codes'] as $qrCode)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $qrCode['name'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-indigo-600">{{ number_format($qrCode['scans']) }}</div>
                                    <div class="text-sm text-gray-500">scans</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No QR code data available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full
                                    {{ $activity['type'] === 'scan' ? 'bg-blue-500' : ($activity['type'] === 'chat' ? 'bg-green-500' : 'bg-purple-500') }}">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['store'] }} • {{ $activity['timestamp']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Data Map -->
        @if(!$chartsData['geographic_data']->isEmpty())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Geographic Distribution</h3>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                            <span>Areas with Leads</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                            <span>QR Scan Areas</span>
                        </div>
                        <div class="text-gray-500">
                            <small>Size = Activity Count</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div id="map" style="height: 400px;" class="rounded-lg"></div>
            </div>
        </div>
        @endif

        <!-- Store Performance Details -->
        @if($selectedStore)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">{{ $selectedStore->name }} - Detailed Analytics</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $selectedStore->qrCodes->count() }}</div>
                        <div class="text-sm text-gray-500">QR Codes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $selectedStore->chatLogs->count() }}</div>
                        <div class="text-sm text-gray-500">Total Chats</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $selectedStore->interactions->count() }}</div>
                        <div class="text-sm text-gray-500">Total Interactions</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Custom CSS for Charts Stability -->
<style>
.chart-container {
    position: relative;
    height: 300px !important;
    max-height: 300px !important;
    min-height: 300px !important;
    overflow: hidden;
}

.chart-container canvas {
    max-width: 100% !important;
    height: auto !important;
}

#map {
    height: 400px !important;
    width: 100% !important;
    z-index: 1;
}

/* Custom map markers */
.custom-map-marker {
    background: transparent !important;
    border: none !important;
}

.marker-content {
    transition: transform 0.2s ease;
}

.marker-content:hover {
    transform: scale(1.1);
}

/* Map popup styling */
.leaflet-popup-content {
    margin: 8px 12px;
    font-family: inherit;
}

.map-popup h4 {
    color: #374151;
    border-bottom: 1px solid #E5E7EB;
    padding-bottom: 4px;
}

/* Prevent chart container resize issues */
.bg-white .chart-container {
    padding: 1rem;
}

/* Ensure charts don't break layout */
.analytics-grid {
    display: grid;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .analytics-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(!$chartsData['geographic_data']->isEmpty())
<!-- Leaflet Map Script -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<script>
// Prevent multiple script executions
if (typeof window.analyticsInitialized === 'undefined') {
    window.analyticsInitialized = true;

    document.addEventListener('DOMContentLoaded', function() {
    // Chart colors
    const colors = {
        primary: '#3B82F6',
        secondary: '#10B981',
        accent: '#F59E0B',
        purple: '#8B5CF6',
        red: '#EF4444',
        teal: '#14B8A6'
    };

    // Store chart instances to prevent recreation
    let chartInstances = {};
    let resizeTimeout;

    // Helper function to create or update chart
    function createChart(canvasId, config) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        // Destroy existing chart if it exists
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
        }

        // Create new chart
        chartInstances[canvasId] = new Chart(ctx.getContext('2d'), config);
        return chartInstances[canvasId];
    }

    // Debounced resize handler
    function handleResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            Object.values(chartInstances).forEach(chart => {
                if (chart && typeof chart.resize === 'function') {
                    chart.resize();
                }
            });
        }, 250);
    }

    // Add resize listener
    window.addEventListener('resize', handleResize);

    // Scans Over Time Chart
    createChart('scansChart', {
        type: 'line',
        data: {
            labels: {!! json_encode($chartsData['scans_over_time']->keys()) !!},
            datasets: [{
                label: 'QR Scans',
                data: {!! json_encode($chartsData['scans_over_time']->values()) !!},
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 0 // Disable animations to prevent resize loops
            }
        }
    });

    // Chat Activity Chart
    createChart('chatsChart', {
        type: 'line',
        data: {
            labels: {!! json_encode($chartsData['chats_over_time']->keys()) !!},
            datasets: [{
                label: 'Chat Messages',
                data: {!! json_encode($chartsData['chats_over_time']->values()) !!},
                borderColor: colors.secondary,
                backgroundColor: colors.secondary + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 0 // Disable animations to prevent resize loops
            }
        }
    });

    // Device Types Chart
    createChart('deviceChart', {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartsData['device_types']->keys()) !!},
            datasets: [{
                data: {!! json_encode($chartsData['device_types']->values()) !!},
                backgroundColor: [
                    colors.primary,
                    colors.secondary,
                    colors.accent,
                    colors.purple,
                    colors.red,
                    colors.teal
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            animation: {
                duration: 0 // Disable animations to prevent resize loops
            }
        }
    });

    // Store Performance Chart
    createChart('storeChart', {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartsData['store_performance']->pluck('name')) !!},
            datasets: [{
                label: 'Performance Score',
                data: {!! json_encode($chartsData['store_performance']->pluck('score')) !!},
                backgroundColor: colors.purple,
                borderColor: colors.purple,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 0 // Disable animations to prevent resize loops
            }
        }
    });

    @if(!$chartsData['geographic_data']->isEmpty())
    // Initialize Map (only if not already initialized)
    let map;
    const mapContainer = document.getElementById('map');
    if (mapContainer && !mapContainer._leaflet_id) {
        map = L.map('map').setView([41.9028, 12.4964], 6); // Default to Italy

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);        // Add markers for geographic data
        const geoData = {!! json_encode($chartsData['geographic_data']) !!};
        const markers = [];

        geoData.forEach(function(point) {
            if (point.lat && point.lng) {
                // Create custom icon based on data type
                let icon = L.divIcon({
                    className: 'custom-map-marker',
                    html: `<div class="marker-content" style="
                        background: ${point.leads > 0 ? '#10B981' : '#3B82F6'};
                        color: white;
                        border-radius: 50%;
                        width: ${Math.max(20, Math.min(50, point.count * 2))}px;
                        height: ${Math.max(20, Math.min(50, point.count * 2))}px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                        font-size: ${Math.max(10, Math.min(16, point.count))}px;
                        border: 2px solid white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    ">${point.count}</div>`,
                    iconSize: [Math.max(20, Math.min(50, point.count * 2)), Math.max(20, Math.min(50, point.count * 2))],
                    iconAnchor: [Math.max(10, Math.min(25, point.count)), Math.max(10, Math.min(25, point.count))]
                });

                // Create popup content
                let popupContent = `
                    <div class="map-popup">
                        <h4 style="margin: 0 0 8px 0; font-weight: bold;">Attività Geografica</h4>
                        <div style="margin-bottom: 4px;">
                            ${point.description || `Totale: ${point.count}`}
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            Coord: ${point.lat.toFixed(4)}, ${point.lng.toFixed(4)}
                        </div>
                    </div>
                `;

                const marker = L.marker([point.lat, point.lng], { icon: icon })
                    .addTo(map)
                    .bindPopup(popupContent);
                markers.push(marker);
            }
        });

        // Fit map to markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }
    @endif
    });
}
</script>
@endsection
