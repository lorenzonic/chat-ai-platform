<template>
  <div class="analytics-dashboard-simple">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Analytics Dashboard (Vue)</h2>
      <p class="text-gray-600 mb-4">Componente Vue caricato correttamente!</p>

      <!-- Test di reattività -->
      <div class="mb-4">
        <button @click="counter++" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Clicks: {{ counter }}
        </button>
      </div>

      <!-- Statistiche di base -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg text-center">
          <div class="text-2xl font-bold text-blue-600">{{ stats.totalChats }}</div>
          <div class="text-sm text-blue-800">Chat Totali</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg text-center">
          <div class="text-2xl font-bold text-green-600">{{ stats.totalLeads }}</div>
          <div class="text-sm text-green-800">Lead Generati</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg text-center">
          <div class="text-2xl font-bold text-yellow-600">{{ stats.recentChats }}</div>
          <div class="text-sm text-yellow-800">Chat Recenti</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg text-center">
          <div class="text-2xl font-bold text-purple-600">{{ Math.round(stats.conversionRate) }}%</div>
          <div class="text-sm text-purple-800">Tasso Conversione</div>
        </div>
      </div>

      <!-- Loading state test -->
      <div class="mt-6">
        <button @click="loadData" :disabled="loading" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50">
          <span v-if="!loading">🔄 Ricarica Dati</span>
          <span v-else>⏳ Caricamento...</span>
        </button>
      </div>

      <div v-if="loading" class="mt-4 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsDashboardTest',
  data() {
    return {
      counter: 0,
      loading: false,
      stats: {
        totalChats: 0,
        totalLeads: 0,
        recentChats: 0,
        conversionRate: 0
      }
    }
  },
  mounted() {
    console.log('✅ AnalyticsDashboardTest component mounted!');
    this.loadData();
  },
  methods: {
    async loadData() {
      this.loading = true;
      console.log('🔄 Loading analytics data...');

      try {
        // Simula chiamata API
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Dati di test
        this.stats = {
          totalChats: Math.floor(Math.random() * 1000) + 100,
          totalLeads: Math.floor(Math.random() * 100) + 10,
          recentChats: Math.floor(Math.random() * 50) + 5,
          conversionRate: Math.random() * 20 + 5
        };

        console.log('✅ Analytics data loaded:', this.stats);
      } catch (error) {
        console.error('❌ Error loading analytics data:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.analytics-dashboard-simple {
  min-height: 400px;
}
</style>
