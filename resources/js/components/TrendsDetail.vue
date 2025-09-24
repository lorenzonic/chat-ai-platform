<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <div>
                  <a href="/admin/trending-keywords" class="text-gray-400 hover:text-gray-500">
                    <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="sr-only">Dashboard</span>
                  </a>
                </div>
              </li>
              <li>
                <div class="flex items-center">
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <a href="/admin/trending-keywords" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Google Trends</a>
                </div>
              </li>
              <li>
                <div class="flex items-center">
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="ml-4 text-sm font-medium text-gray-900">{{ keyword }}</span>
                </div>
              </li>
            </ol>
          </nav>

          <div class="mt-4 flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                🔍 <span class="ml-2">{{ keyword }}</span>
              </h1>
              <p class="mt-1 text-sm text-gray-500">Analisi dettagliata della keyword</p>
            </div>
            <div>
              <a
                href="/admin/trending-keywords"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Torna alla Lista
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Statistiche keyword -->
      <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Totale Rilevazioni</dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900">{{ keywordStats.total_entries || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Score Medio</dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900">{{ formatNumber(keywordStats.avg_score, 1) }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Score Massimo</dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900">{{ keywordStats.max_score || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Score Minimo</dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900">{{ keywordStats.min_score || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Prima Rilevazione</dt>
              <dd class="mt-1 text-lg font-bold text-gray-900">
                {{ keywordStats.first_seen ? formatDate(keywordStats.first_seen) : 'N/A' }}
              </dd>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="text-center">
              <dt class="text-sm font-medium text-gray-500 truncate">Regioni</dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900">{{ keywordStats.regions?.length || 0 }}</dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtri -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filtri</h3>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Regione</label>
              <select v-model="filters.region" @change="applyFilters" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                <option value="all">Tutte le regioni</option>
                <option v-for="region in keywordStats.regions" :key="region" :value="region">
                  {{ getRegionName(region) }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Periodo</label>
              <select v-model="filters.days" @change="applyFilters" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                <option value="7">Ultimi 7 giorni</option>
                <option value="30">Ultimo mese</option>
                <option value="90">Ultimi 3 mesi</option>
                <option value="365">Ultimo anno</option>
              </select>
            </div>

            <div class="flex items-end">
              <button
                @click="applyFilters"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700"
              >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                Applica Filtri
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Grafico trend nel tempo -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">📈 Trend nel Tempo</h3>
            </div>
            <div class="p-6">
              <canvas ref="timeSeriesChart" height="400"></canvas>
            </div>
          </div>
        </div>

        <!-- Confronto regionale -->
        <div>
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">🗺️ Confronto Regionale</h3>
            </div>
            <div class="p-6">
              <div v-if="regionalComparison.length === 0" class="text-gray-500 text-center py-4">
                Nessun dato disponibile per il confronto regionale
              </div>
              <div v-else class="space-y-4">
                <div v-for="regionData in regionalComparison" :key="regionData.region" class="space-y-2">
                  <div class="flex justify-between items-center">
                    <div>
                      <span class="text-sm font-medium text-gray-900">{{ getRegionName(regionData.region) }}</span>
                      <span class="text-xs text-gray-500 ml-2">({{ regionData.count }} rilevazioni)</span>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">{{ formatNumber(regionData.avg_score, 1) }}</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-emerald-600 h-3 rounded-full transition-all duration-500"
                      :style="`width: ${Math.min((regionData.avg_score / keywordStats.max_score) * 100, 100)}%`"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500">
                    Ultimo score: {{ regionData.latest_score }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabella storico -->
      <div class="bg-white shadow rounded-lg mt-8">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">📊 Storico Rilevazioni</h3>
          <div class="text-sm text-gray-500">
            Mostrando {{ keywordData.length }} rilevazioni degli ultimi {{ filters.days }} giorni
          </div>
        </div>
        <div class="overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data e Ora</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Regione</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variazione</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giorni fa</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="keywordData.length === 0">
                  <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Nessuna rilevazione trovata per i filtri selezionati
                  </td>
                </tr>
                <tr v-for="(data, index) in keywordData" :key="`${data.region}-${data.collected_at}`" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatDate(data.collected_at) }}</div>
                    <div class="text-sm text-gray-500">{{ formatTime(data.collected_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getScoreBadgeClass(data.score)}`">
                      {{ data.score }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ getRegionName(data.region) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span v-if="index < keywordData.length - 1" :class="getVariationClass(getVariation(data.score, keywordData[index + 1].score))">
                      {{ getVariationText(getVariation(data.score, keywordData[index + 1].score)) }}
                    </span>
                    <span v-else class="text-gray-500">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatRelativeTime(data.collected_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  name: 'TrendsDetail',
  props: {
    keyword: {
      type: String,
      required: true
    },
    initialData: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      filters: {
        region: 'all',
        days: 30
      },
      chart: null,

      // Dati che verranno popolati dal backend
      keywordStats: this.initialData.keywordStats || {},
      keywordData: this.initialData.recentTrends || [],
      timeSeriesData: this.initialData.timeSeriesData || [],
      regionalComparison: this.initialData.regionPerformance || [],

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
    this.loadInitialData();
    this.initChart();
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy();
    }
  },
  methods: {
    loadInitialData() {
      if (this.initialData.keywordStats) {
        this.keywordStats = this.initialData.keywordStats;
      }
      if (this.initialData.keywordData) {
        this.keywordData = this.initialData.keywordData;
      }
      if (this.initialData.timeSeriesData) {
        this.timeSeriesData = this.initialData.timeSeriesData;
      }
      if (this.initialData.regionalComparison) {
        this.regionalComparison = this.initialData.regionalComparison;
      }

      if (this.chart && this.timeSeriesData.length > 0) {
        this.updateChartData();
      }
    },

    initChart() {
      const ctx = this.$refs.timeSeriesChart;
      if (!ctx) return;

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [{
            label: 'Score',
            data: [],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1,
            fill: true
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
                text: 'Score'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Data'
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: `Andamento Score per "${this.keyword}"`
            },
            legend: {
              display: false
            }
          }
        }
      });

      this.updateChartData();
    },

    updateChartData() {
      if (!this.chart || !this.timeSeriesData) return;

      const labels = Object.keys(this.timeSeriesData).map(date => this.formatDate(date));
      const scores = Object.values(this.timeSeriesData).map(data => data.avg_score);

      this.chart.data.labels = labels;
      this.chart.data.datasets[0].data = scores;
      this.chart.update();
    },

    applyFilters() {
      const params = new URLSearchParams();
      if (this.filters.region !== 'all') params.append('region', this.filters.region);
      if (this.filters.days !== 30) params.append('days', this.filters.days);

      const queryString = params.toString();
      const newUrl = queryString ? `?${queryString}` : window.location.pathname;
      window.location.href = newUrl;
    },

    getRegionName(regionCode) {
      return this.regionNames[regionCode] || regionCode;
    },

    getScoreBadgeClass(score) {
      if (score >= 70) return 'bg-green-100 text-green-800';
      if (score >= 40) return 'bg-yellow-100 text-yellow-800';
      return 'bg-gray-100 text-gray-800';
    },

    getVariation(currentScore, previousScore) {
      return currentScore - previousScore;
    },

    getVariationClass(variation) {
      if (variation > 0) return 'text-green-600';
      if (variation < 0) return 'text-red-600';
      return 'text-gray-500';
    },

    getVariationText(variation) {
      if (variation > 0) return `↗ +${variation}`;
      if (variation < 0) return `↘ ${variation}`;
      return '→ 0';
    },

    formatNumber(num, decimals = 0) {
      if (num === null || num === undefined) return 'N/A';
      return Number(num).toLocaleString('it-IT', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('it-IT');
    },

    formatTime(dateString) {
      return new Date(dateString).toLocaleTimeString('it-IT', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    formatRelativeTime(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diffMs = now - date;
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
      const diffDays = Math.floor(diffHours / 24);

      if (diffHours < 1) return 'Meno di un\'ora fa';
      if (diffHours < 24) return `${diffHours} ore fa`;
      if (diffDays === 1) return 'Ieri';
      if (diffDays < 7) return `${diffDays} giorni fa`;
      return this.formatDate(dateString);
    }
  }
}
</script>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}
</style>
