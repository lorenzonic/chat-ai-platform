<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Analytics Dashboard - DEBUG
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Debug Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🔍 Debug Analytics</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Store:</strong> {{ $store->name }}</p>
                            <p><strong>Store ID:</strong> {{ $store->id }}</p>
                            <p><strong>Email:</strong> {{ $store->email }}</p>
                        </div>
                        <div>
                            <p><strong>Premium:</strong> {{ $store->is_premium ? 'Sì' : 'No' }}</p>
                            <p><strong>Attivo:</strong> {{ $store->is_active ? 'Sì' : 'No' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simple Analytics Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">📈 Statistiche Semplici</h3>

                    @php
                        $chatLogs = \App\Models\ChatLog::where('store_id', $store->id)->count();
                        $leads = \App\Models\Lead::where('store_id', $store->id)->count();
                        $recentChats = \App\Models\ChatLog::where('store_id', $store->id)
                            ->where('created_at', '>=', now()->subDays(7))
                            ->count();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $chatLogs }}</div>
                            <div class="text-sm text-blue-800">Chat Totali</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $leads }}</div>
                            <div class="text-sm text-green-800">Lead Generati</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $recentChats }}</div>
                            <div class="text-sm text-yellow-800">Chat Ultimi 7 Giorni</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue Component Test -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🧪 Test Vue Component</h3>
                    <p class="text-sm text-gray-600 mb-4">Se vedi contenuto qui sotto, Vue funziona:</p>

                    <div id="analytics-app">
                        <analytics-dashboard></analytics-dashboard>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded">
                        <p class="text-xs text-gray-500">
                            Se la sezione sopra è vuota, il problema è nel caricamento del componente Vue.
                            Controlla la console del browser per eventuali errori JavaScript.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>

<script>
console.log('🔍 Analytics Debug Page Loaded');
console.log('Vite assets loading check...');

// Debug script per verificare il caricamento
setTimeout(() => {
    const analyticsApp = document.getElementById('analytics-app');
    console.log('Analytics app element:', analyticsApp);
    console.log('Vue instance check:', typeof window.Vue);
    console.log('Analytics component in DOM:', analyticsApp?.innerHTML || 'Empty');
}, 2000);
</script>
