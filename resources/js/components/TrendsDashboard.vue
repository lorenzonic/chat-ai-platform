<template>
  <div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
              🌱 <span class="ml-2">Google Trends Piante</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Monitoraggio e analisi delle tendenze di ricerca nel mondo delle piante con regioni ottimizzate
            </p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="updateTrends"
              :disabled="isUpdating"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
            >
              <svg v-if="isUpdating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              {{ isUpdating ? 'Aggiornamento...' : 'Aggiorna Ora' }}
            </button>
            <button
              @click="showFilters = !showFilters"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
              </svg>
              Filtri
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Panel -->
    <div v-if="showFilters" class="bg-white shadow mx-4 mt-4 rounded-lg">
      <div class="px-6 py-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtri Avanzati</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Regione</label>
            <select v-model="filters.region" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
              <option value="all">Tutte le regioni</option>
              <option v-for="region in availableRegions" :key="region" :value="region">
                {{ getRegionName(region) }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periodo</label>
            <select v-model="filters.days" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
              <option :value="1">Oggi</option>
              <option :value="7">Ultimi 7 giorni</option>
              <option :value="30">Ultimo mese</option>
              <option :value="90">Ultimi 3 mesi</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cerca Keyword</label>
            <input
              v-model="filters.keyword"
              @input="debounceSearch"
              type="text"
              placeholder="es. piante da appartamento"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Keywords Oggi</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ formatNumber(stats.total_keywords_today || 0) }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Score Medio</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ formatNumber(stats.avg_score_today || 0, 1) }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Keywords Uniche</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ formatNumber(stats.unique_keywords || 0) }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Regioni Attive</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ availableRegions.length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Daily Trends Chart -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">📈 Trends Giornalieri</h3>
            <div class="h-80">
              <canvas ref="dailyTrendsChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Regional Performance Chart -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🗺️ Performance per Regione</h3>
            <div class="h-80">
              <canvas ref="regionChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Keywords and Recent Trends -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Keywords -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🏆 Top Keywords Oggi</h3>
            <div class="space-y-3">
              <div v-for="keyword in topKeywords.slice(0, 8)" :key="keyword.id" class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">{{ keyword.keyword }}</div>
                  <div class="text-xs text-gray-500">{{ getRegionName(keyword.region) }}</div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="keyword.score >= 70 ? 'bg-green-100 text-green-800' :
                               keyword.score >= 40 ? 'bg-yellow-100 text-yellow-800' :
                               'bg-gray-100 text-gray-800'">
                    {{ keyword.score }}
                  </span>
                </div>
              </div>
              <div v-if="topKeywords.length === 0" class="text-center text-gray-500 py-4">
                Nessuna keyword trovata per oggi
              </div>
            </div>
          </div>
        </div>

        <!-- Popular Keywords -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🔥 Keywords Più Popolari</h3>
            <div class="space-y-3">
              <div v-for="keyword in popularKeywords.slice(0, 8)" :key="keyword.keyword" class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">{{ keyword.keyword }}</div>
                  <div class="text-xs text-gray-500">{{ keyword.frequency || 0 }} occorrenze</div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ formatNumber(keyword.avg_score || 0, 1) }}
                  </span>
                </div>
              </div>
              <div v-if="popularKeywords.length === 0" class="text-center text-gray-500 py-4">
                Nessun dato disponibile
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Trends Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">📊 Dettaglio Trends</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Lista completa delle keywords trending
          </p>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Regione</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="trend in trends" :key="trend.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ trend.keyword }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="trend.score >= 70 ? 'bg-green-100 text-green-800' :
                               trend.score >= 40 ? 'bg-yellow-100 text-yellow-800' :
                               'bg-gray-100 text-gray-800'">
                    {{ trend.score }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ getRegionName(trend.region) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(trend.collected_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a :href="`/admin/trending-keywords/${encodeURIComponent(trend.keyword)}`"
                     class="text-emerald-600 hover:text-emerald-900">
                    Dettagli
                  </a>
                </td>
              </tr>
              <tr v-if="trends.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                  Nessun trend trovato
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <p class="text-center text-gray-500 mt-8">Dashboard Vue.js + Chart.js con regioni ottimizzate implementata!</p>
    </div>
  </div>
</template>

<script>
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, ArcElement)

export default {
  name: 'TrendsDashboard',
  props: {
    initialData: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      // Data from backend
      stats: this.initialData.stats || {},
      trends: this.initialData.trends || [],
      topKeywords: this.initialData.topKeywords || [],
      regionStats: this.initialData.regionStats || [],
      popularKeywords: this.initialData.popularKeywords || [],
      dailyTrends: this.initialData.dailyTrends || [],
      availableRegions: this.initialData.availableRegions || [],
      pagination: this.initialData.pagination || {},

      // UI state
      showFilters: false,
      isUpdating: false,

      // Filters
      filters: {
        region: this.initialData.currentFilters?.region || 'all',
        days: this.initialData.currentFilters?.days || 7,
        keyword: this.initialData.currentFilters?.keyword || ''
      },

      // Charts
      dailyChart: null,
      regionChart: null,
      searchTimeout: null,

      // Region mapping for readable names
      regionNames: {
        'IT': 'Italia',
        'IT-25': 'Lombardia',
        'IT-21': 'Piemonte',
        'IT-62': 'Lazio',
        'IT-72': 'Campania',
        'IT-45': 'Emilia-Romagna',
        'IT-78': 'Calabria',
        'IT-52': 'Toscana',
        'IT-75': 'Puglia',
        'IT-77': 'Basilicata',
        'IT-82': 'Sicilia',
        'IT-88': 'Sardegna',
        'IT-65': 'Abruzzo',
        'IT-55': 'Umbria',
        'IT-57': 'Marche',
        'IT-42': 'Liguria',
        'IT-32': 'Trentino-Alto Adige',
        'IT-34': 'Veneto',
        'IT-36': 'Friuli-Venezia Giulia',
        'IT-23': 'Valle d\'Aosta',
        'IT-67': 'Molise'
      }
    }
  },

  mounted() {
    this.$nextTick(() => {
      this.initializeCharts()
    })
  },

  beforeUnmount() {
    if (this.dailyChart) {
      this.dailyChart.destroy()
    }
    if (this.regionChart) {
      this.regionChart.destroy()
    }
  },

  methods: {
    initializeCharts() {
      this.createDailyTrendsChart()
      this.createRegionChart()
    },

    createDailyTrendsChart() {
      const ctx = this.$refs.dailyTrendsChart?.getContext('2d')
      if (!ctx) return

      // Prepare data for daily trends
      const chartData = this.dailyTrends.map(item => ({
        date: new Date(item.date).toLocaleDateString('it-IT'),
        count: item.count || 0,
        avg_score: item.avg_score || 0
      }))

      this.dailyChart = new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: chartData.map(d => d.date),
          datasets: [
            {
              label: 'Numero Keywords',
              data: chartData.map(d => d.count),
              borderColor: 'rgb(34, 197, 94)',
              backgroundColor: 'rgba(34, 197, 94, 0.1)',
              tension: 0.3,
              yAxisID: 'y'
            },
            {
              label: 'Score Medio',
              data: chartData.map(d => d.avg_score),
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.3,
              yAxisID: 'y1'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              type: 'linear',
              display: true,
              position: 'left',
              title: {
                display: true,
                text: 'Numero Keywords'
              }
            },
            y1: {
              type: 'linear',
              display: true,
              position: 'right',
              title: {
                display: true,
                text: 'Score Medio'
              },
              grid: {
                drawOnChartArea: false,
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: 'Andamento Trends negli Ultimi Giorni'
            }
          }
        }
      })
    },

    createRegionChart() {
      const ctx = this.$refs.regionChart?.getContext('2d')
      if (!ctx) return

      // Prepare data for regional performance
      const regionData = this.regionStats.slice(0, 10) // Top 10 regions

      this.regionChart = new ChartJS(ctx, {
        type: 'bar',
        data: {
          labels: regionData.map(r => this.getRegionName(r.region)),
          datasets: [{
            label: 'Score Medio',
            data: regionData.map(r => r.avg_score || 0),
            backgroundColor: [
              'rgba(34, 197, 94, 0.8)',
              'rgba(59, 130, 246, 0.8)',
              'rgba(147, 51, 234, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(245, 158, 11, 0.8)',
              'rgba(16, 185, 129, 0.8)',
              'rgba(99, 102, 241, 0.8)',
              'rgba(236, 72, 153, 0.8)',
              'rgba(14, 165, 233, 0.8)',
              'rgba(168, 85, 247, 0.8)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Score Medio'
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: 'Performance per Regione'
            },
            legend: {
              display: false
            }
          }
        }
      })
    },

    getRegionName(regionCode) {
      return this.regionNames[regionCode] || regionCode
    },
    getRegionName(regionCode) {
      return this.regionNames[regionCode] || regionCode
    },

    async updateTrends() {
      this.isUpdating = true

      try {
        const response = await fetch('/admin/trending-keywords/update', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const data = await response.json()

        if (data.success) {
          setTimeout(() => {
            window.location.reload()
          }, 2000)
        }
      } catch (error) {
        console.error('Errore durante l\'aggiornamento:', error)
      } finally {
        this.isUpdating = false
      }
    },

    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.applyFilters()
      }, 500)
    },

    applyFilters() {
      const params = new URLSearchParams()
      if (this.filters.region !== 'all') params.set('region', this.filters.region)
      if (this.filters.days !== 7) params.set('days', this.filters.days)
      if (this.filters.keyword) params.set('keyword', this.filters.keyword)

      const url = '/admin/trending-keywords' + (params.toString() ? '?' + params.toString() : '')
      window.location.href = url
    },

    formatNumber(num, decimals = 0) {
      if (num === null || num === undefined) return '0'
      return Number(num).toLocaleString('it-IT', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      })
    },

    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>
