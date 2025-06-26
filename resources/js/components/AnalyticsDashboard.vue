<template>
  <div class="analytics-dashboard p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">📊 Analytics Dashboard</h1>
      <p class="text-gray-600">Analizza le performance del tuo chatbot</p>
    </div>

    <!-- Controls -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex flex-wrap gap-4 items-center">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
          <select v-model="selectedPeriod" @change="fetchData" class="border border-gray-300 rounded-md px-3 py-2">
            <option value="7d">Ultimi 7 giorni</option>
            <option value="30d">Ultimi 30 giorni</option>
            <option value="90d">Ultimi 90 giorni</option>
            <option value="custom">Personalizzato</option>
          </select>
        </div>

        <div v-if="selectedPeriod === 'custom'" class="flex gap-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Da</label>
            <input v-model="customFrom" @change="fetchData" type="date" class="border border-gray-300 rounded-md px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">A</label>
            <input v-model="customTo" @change="fetchData" type="date" class="border border-gray-300 rounded-md px-3 py-2">
          </div>
        </div>

        <div v-if="isPremium" class="ml-auto">
          <button @click="exportData" :disabled="isExporting"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50">
            <span v-if="!isExporting">📥 Esporta CSV</span>
            <span v-else>⏳ Esportando...</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Caricamento dati...</p>
    </div>

    <!-- Summary Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Totale Interazioni</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(summary.total_interactions) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Lead Generati</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(summary.total_leads) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tasso Conversione</p>
            <p class="text-2xl font-bold text-gray-900">{{ summary.conversion_rate }}%</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Visitatori Unici</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(summary.unique_visitors) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Daily Interactions Chart -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Interazioni Giornaliere</h3>
        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
          <canvas ref="dailyChart" width="400" height="200"></canvas>
        </div>
      </div>

      <!-- Device Breakdown -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dispositivi</h3>
        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
          <canvas ref="deviceChart" width="300" height="300"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Questions -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Domande Più Frequenti</h3>
      <div v-if="Object.keys(topQuestions).length > 0" class="space-y-3">
        <div v-for="(count, question) in topQuestions" :key="question"
             class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
          <span class="text-gray-800">{{ question }}</span>
          <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
            {{ count }} volte
          </span>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500">
        Nessuna domanda registrata nel periodo selezionato
      </div>
    </div>

    <!-- Premium Features -->
    <div v-if="isPremium && premiumData" class="space-y-6">
      <!-- Hourly Breakdown -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Distribuzione Oraria (Premium)</h3>
        <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
          <canvas ref="hourlyChart" width="800" height="320"></canvas>
        </div>
      </div>

      <!-- UTM Analysis -->
      <div v-if="premiumData.utm_analysis" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h4 class="font-semibold text-gray-900 mb-3">UTM Sources</h4>
          <div v-for="(count, source) in premiumData.utm_analysis.sources" :key="source"
               class="flex justify-between py-2">
            <span>{{ source }}</span>
            <span class="font-medium">{{ count }}</span>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <h4 class="font-semibold text-gray-900 mb-3">UTM Mediums</h4>
          <div v-for="(count, medium) in premiumData.utm_analysis.mediums" :key="medium"
               class="flex justify-between py-2">
            <span>{{ medium }}</span>
            <span class="font-medium">{{ count }}</span>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <h4 class="font-semibold text-gray-900 mb-3">UTM Campaigns</h4>
          <div v-for="(count, campaign) in premiumData.utm_analysis.campaigns" :key="campaign"
               class="flex justify-between py-2">
            <span>{{ campaign }}</span>
            <span class="font-medium">{{ count }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Premium Upgrade CTA -->
    <div v-else-if="!isPremium" class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-8 text-white text-center">
      <h3 class="text-2xl font-bold mb-4">🚀 Sblocca Analytics Avanzate</h3>
      <p class="mb-6">Accedi a report dettagliati, esportazione dati, analisi UTM e molto altro!</p>
      <button class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
        Upgrade a Premium
      </button>
    </div>

    <!-- Map Section -->
    <div v-if="isPremium" class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Mappa Geografica (Premium)</h3>
      <div ref="mapContainer" class="h-80 rounded-lg"></div>
    </div>

    <!-- Geographic Map -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">🌍 Provenienza Geografica</h3>

        <!-- Map Filters -->
        <div v-if="geographicData.length > 0" class="flex items-center space-x-3">
          <span class="text-sm text-gray-600">Mostra:</span>
          <div class="flex items-center space-x-2">
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="mapFilters.showInteractions"
                @change="updateMap"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              >
              <span class="ml-1 text-sm text-blue-600 font-medium">Interazioni</span>
            </label>
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="mapFilters.showLeads"
                @change="updateMap"
                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
              >
              <span class="ml-1 text-sm text-green-600 font-medium">Lead</span>
            </label>
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="mapFilters.showConverted"
                @change="updateMap"
                class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
              >
              <span class="ml-1 text-sm text-yellow-600 font-medium">Convertiti</span>
            </label>
          </div>
        </div>
      </div>

      <div v-if="geographicData.length > 0" class="space-y-4">
        <div class="text-sm text-gray-600 mb-3 bg-gray-50 p-3 rounded-lg">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
              <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
              <span><strong>Blu</strong>: Solo interazioni ({{ getFilteredCount('interactions') }})</span>
            </div>
            <div class="flex items-center">
              <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
              <span><strong>Verde</strong>: Solo lead ({{ getFilteredCount('leads') }})</span>
            </div>
            <div class="flex items-center">
              <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
              <span><strong>Giallo</strong>: Conversioni ({{ getFilteredCount('converted') }})</span>
            </div>
          </div>
        </div>
        <div ref="mapContainer" class="w-full h-96 border border-gray-200 rounded-lg"></div>
      </div>
      <div v-else class="text-center py-12 text-gray-500">
        <div class="text-4xl mb-4">🗺️</div>
        <p>Nessun dato geografico disponibile</p>
        <p class="text-sm mt-2">I dati di localizzazione vengono raccolti automaticamente dalle interazioni future</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  LineController,
  BarController,
  DoughnutController
} from 'chart.js'

// Register Chart.js components
Chart.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  LineController,
  BarController,
  DoughnutController
)

// Reactive data
const loading = ref(true)
const selectedPeriod = ref('30d')
const customFrom = ref('')
const customTo = ref('')
const isExporting = ref(false)

// Map filters
const mapFilters = ref({
  showInteractions: true,
  showLeads: true,
  showConverted: true
})

const summary = ref({
  total_interactions: 0,
  total_leads: 0,
  conversion_rate: 0,
  unique_visitors: 0
})

const dailyInteractions = ref({})
const dailyLeads = ref({})
const topQuestions = ref({})
const deviceBreakdown = ref({})
const browserBreakdown = ref({})
const geographicData = ref([])
const isPremium = ref(false)
const premiumData = ref(null)

// Chart instances
let dailyChartInstance = null
let deviceChartInstance = null
let hourlyChartInstance = null
let mapInstance = null

// Chart refs
const dailyChart = ref(null)
const deviceChart = ref(null)
const hourlyChart = ref(null)
const mapContainer = ref(null)

// Methods
const fetchData = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      period: selectedPeriod.value
    })

    if (selectedPeriod.value === 'custom') {
      if (customFrom.value) params.append('from', customFrom.value)
      if (customTo.value) params.append('to', customTo.value)
    }

    const response = await fetch(`/store/analytics?${params}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    console.log('Analytics data received:', data) // Debug log

    // Update reactive data
    summary.value = data.summary || summary.value
    dailyInteractions.value = data.daily_interactions || {}
    dailyLeads.value = data.daily_leads || {}
    topQuestions.value = data.top_questions || {}
    deviceBreakdown.value = data.device_breakdown || {}
    browserBreakdown.value = data.browser_breakdown || {}
    geographicData.value = data.geographic_data || []
    isPremium.value = data.is_premium || false
    premiumData.value = data.premium_data || null

    // Update charts
    await nextTick()
    updateCharts()
    updateMap()

  } catch (error) {
    console.error('Error fetching analytics:', error)
    // Show some dummy data for testing
    summary.value = {
      total_interactions: 0,
      total_leads: 0,
      conversion_rate: 0,
      unique_visitors: 0
    }
  } finally {
    loading.value = false
  }
}

const updateCharts = () => {
  // Update daily interactions chart
  updateDailyChart()
  updateDeviceChart()

  if (isPremium.value && premiumData.value?.hourly_breakdown) {
    updateHourlyChart()
  }
}

const updateDailyChart = () => {
  if (dailyChartInstance) {
    dailyChartInstance.destroy()
  }

  if (!dailyChart.value) {
    console.warn('Daily chart canvas not found')
    return
  }

  const ctx = dailyChart.value.getContext('2d')
  const dates = Object.keys(dailyInteractions.value).sort()

  dailyChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [
        {
          label: 'Interazioni',
          data: dates.map(date => dailyInteractions.value[date] || 0),
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4
        },
        {
          label: 'Lead',
          data: dates.map(date => dailyLeads.value[date] || 0),
          borderColor: 'rgb(16, 185, 129)',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: false,
      maintainAspectRatio: true,
      aspectRatio: 2,
      interaction: {
        intersect: false,
      },
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          position: 'top',
        }
      }
    }
  })
}

const updateDeviceChart = () => {
  if (deviceChartInstance) {
    deviceChartInstance.destroy()
  }

  if (!deviceChart.value) {
    console.warn('Device chart canvas not found')
    return
  }

  const ctx = deviceChart.value.getContext('2d')

  deviceChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: Object.keys(deviceBreakdown.value),
      datasets: [{
        data: Object.values(deviceBreakdown.value),
        backgroundColor: [
          'rgb(59, 130, 246)',
          'rgb(16, 185, 129)',
          'rgb(245, 158, 11)',
          'rgb(239, 68, 68)'
        ]
      }]
    },
    options: {
      responsive: false,
      maintainAspectRatio: true,
      aspectRatio: 1,
      plugins: {
        legend: {
          position: 'bottom',
        }
      }
    }
  })
}

const updateHourlyChart = () => {
  if (hourlyChartInstance) {
    hourlyChartInstance.destroy()
  }

  if (!hourlyChart.value) {
    console.warn('Hourly chart canvas not found')
    return
  }

  const ctx = hourlyChart.value.getContext('2d')
  const hours = Array.from({length: 24}, (_, i) => i.toString().padStart(2, '0'))

  hourlyChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: hours.map(h => `${h}:00`),
      datasets: [{
        label: 'Interazioni per ora',
        data: hours.map(hour => premiumData.value.hourly_breakdown[hour] || 0),
        backgroundColor: 'rgba(147, 51, 234, 0.7)'
      }]
    },
    options: {
      responsive: false,
      maintainAspectRatio: true,
      aspectRatio: 2.5,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          position: 'top',
        }
      }
    }
  })
}

const updateMap = async () => {
  // Import Leaflet dinamicamente
  const L = await import('leaflet')

  // Destroy existing map
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }

  if (!mapContainer.value || !geographicData.value.length) {
    return
  }

  // Create map
  mapInstance = L.default.map(mapContainer.value, {
    center: [41.9028, 12.4964], // Roma come centro default
    zoom: 6,
    zoomControl: true
  })

  // Add tile layer
  L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(mapInstance)

  // Add markers for each location
  const bounds = []

  // Filter data based on current filters
  const filteredData = geographicData.value.filter(point => {
    const hasOnlyInteractions = point.interactions_count > 0 && point.leads_count === 0
    const hasOnlyLeads = point.leads_count > 0 && point.interactions_count === 0
    const hasConverted = point.leads_count > 0 && point.interactions_count > 0

    return (
      (mapFilters.value.showInteractions && hasOnlyInteractions) ||
      (mapFilters.value.showLeads && hasOnlyLeads) ||
      (mapFilters.value.showConverted && hasConverted)
    )
  })

  filteredData.forEach(point => {
    const { lat, lng, city, country, leads_count, interactions_count, total } = point

    bounds.push([lat, lng])

    // Determine marker type and color
    const hasOnlyInteractions = interactions_count > 0 && leads_count === 0
    const hasOnlyLeads = leads_count > 0 && interactions_count === 0
    const hasConverted = leads_count > 0 && interactions_count > 0

    let markerColor, pointType, pointIcon

    if (hasConverted) {
      markerColor = '#eab308' // Yellow for converted
      pointType = 'Conversione'
      pointIcon = '🎯'
    } else if (hasOnlyLeads) {
      markerColor = '#16a34a' // Green for leads only
      pointType = 'Lead'
      pointIcon = '👤'
    } else {
      markerColor = '#3b82f6' // Blue for interactions only
      pointType = 'Interazione'
      pointIcon = '💬'
    }

    // Create custom icon based on activity level
    const getMarkerSize = (total) => {
      if (total >= 10) return 28
      if (total >= 5) return 22
      return 16
    }

    const markerSize = getMarkerSize(total)

    // Create custom icon
    const customIcon = L.default.divIcon({
      html: `<div style="
        width: ${markerSize}px;
        height: ${markerSize}px;
        background: ${markerColor};
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: ${markerSize > 20 ? '12px' : '10px'};
        font-weight: bold;
        position: relative;
      ">${total}</div>`,
      className: 'custom-marker',
      iconSize: [markerSize, markerSize],
      iconAnchor: [markerSize/2, markerSize/2]
    })

    const marker = L.default.marker([lat, lng], { icon: customIcon }).addTo(mapInstance)

    // Create detailed popup content
    const conversionRate = interactions_count > 0 ? ((leads_count / interactions_count) * 100).toFixed(1) : 0

    // Add popup
    marker.bindPopup(`
      <div style="text-align: center; min-width: 200px;">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
          <span style="font-size: 20px; margin-right: 8px;">${pointIcon}</span>
          <h4 style="margin: 0; color: #1f2937; font-size: 16px;">${city}</h4>
        </div>
        <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px;">${country}</p>
        <div style="background: #f9fafb; padding: 8px; border-radius: 6px; margin-bottom: 8px;">
          <div style="color: ${markerColor}; font-weight: bold; font-size: 14px; margin-bottom: 4px;">
            ${pointType}
          </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
          <div style="text-align: center; padding: 6px; background: #eff6ff; border-radius: 4px;">
            <div style="color: #3b82f6; font-weight: bold; font-size: 16px;">${interactions_count}</div>
            <div style="font-size: 11px; color: #6b7280;">Interazioni</div>
          </div>
          <div style="text-align: center; padding: 6px; background: #f0fdf4; border-radius: 4px;">
            <div style="color: #16a34a; font-weight: bold; font-size: 16px;">${leads_count}</div>
            <div style="font-size: 11px; color: #6b7280;">Lead</div>
          </div>
        </div>
        ${hasConverted ? `
          <div style="text-align: center; padding: 4px; background: #fef3c7; border-radius: 4px; font-size: 12px;">
            <strong>Tasso conversione: ${conversionRate}%</strong>
          </div>
        ` : ''}
      </div>
    `)
  })

  // Fit map to show all markers
  if (bounds.length > 0) {
    mapInstance.fitBounds(bounds, { padding: [20, 20] })
  }
}

const exportData = async () => {
  isExporting.value = true
  try {
    const params = new URLSearchParams({
      type: 'all',
      period: selectedPeriod.value
    })

    if (selectedPeriod.value === 'custom') {
      if (customFrom.value) params.append('from', customFrom.value)
      if (customTo.value) params.append('to', customTo.value)
    }

    const response = await fetch(`/store/analytics/export?${params}`)
    const blob = await response.blob()

    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `analytics_export_${new Date().toISOString().split('T')[0]}.csv`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)

  } catch (error) {
    console.error('Export error:', error)
    alert('Errore durante l\'esportazione')
  } finally {
    isExporting.value = false
  }
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('it-IT').format(num)
}

const getFilteredCount = (type) => {
  if (!geographicData.value.length) return 0

  switch (type) {
    case 'interactions':
      return geographicData.value.filter(point =>
        point.interactions_count > 0 && point.leads_count === 0
      ).length
    case 'leads':
      return geographicData.value.filter(point =>
        point.leads_count > 0 && point.interactions_count === 0
      ).length
    case 'converted':
      return geographicData.value.filter(point =>
        point.leads_count > 0 && point.interactions_count > 0
      ).length
    default:
      return 0
  }
}

// Lifecycle
onMounted(async () => {
  console.log('AnalyticsDashboard mounted')

  // Import Leaflet CSS
  const leafletCSS = document.createElement('link')
  leafletCSS.rel = 'stylesheet'
  leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
  leafletCSS.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY='
  leafletCSS.crossOrigin = ''
  document.head.appendChild(leafletCSS)

  fetchData()
})
</script>

<style scoped>
.analytics-dashboard {
  font-family: 'Inter', sans-serif;
}

.chart-container {
  position: relative;
  overflow: hidden;
}

.chart-container canvas {
  max-width: 100% !important;
  height: auto !important;
  display: block;
}

/* Prevent chart.js from interfering with layout */
canvas {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Leaflet map styles */
.leaflet-container {
  font-family: 'Inter', sans-serif;
}

.custom-marker {
  background: transparent !important;
  border: none !important;
}

.leaflet-popup-content-wrapper {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
