<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔍 Analytics Debug - Diagnostica Vue
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Debug statico (dovrebbe sempre funzionare) -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">🟢 Contenuto Statico (Laravel)</h3>
                <p>Se vedi questo, Laravel funziona correttamente.</p>
                <p><strong>Store:</strong> {{ $store->name }}</p>
                <p><strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>

            <!-- Debug JavaScript -->
            <div class="bg-yellow-50 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">🟡 Test JavaScript Base</h3>
                <p id="js-test">❌ JavaScript non caricato</p>
                <button onclick="testJS()" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded">Test Click</button>
            </div>

            <!-- Debug Vue -->
            <div class="bg-blue-50 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">🔵 Test Vue Component</h3>
                <div id="vue-test-simple">
                    <p>❌ Vue non montato</p>
                </div>
            </div>

            <!-- Analytics App -->
            <div class="bg-red-50 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">🔴 Analytics Component</h3>
                <div id="analytics-app">
                    <p>❌ Analytics component non caricato</p>
                    <analytics-dashboard></analytics-dashboard>
                </div>
            </div>

        </div>
    </div>
</x-store-layout>

<script>
// Test JavaScript base
function testJS() {
    document.getElementById('js-test').innerHTML = '✅ JavaScript funziona!';
    console.log('✅ JavaScript test passed');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('js-test').innerHTML = '✅ JavaScript caricato';
    console.log('🔍 Page loaded, running diagnostics...');

    // Check Vue availability
    setTimeout(function() {
        console.log('=== VUE DIAGNOSTICS ===');
        console.log('window.Vue:', typeof window.Vue);
        console.log('window.Alpine:', typeof window.Alpine);
        console.log('Vite loaded:', typeof window.__viteLoaded);

        // Check elements
        const analyticsApp = document.getElementById('analytics-app');
        const vueTestSimple = document.getElementById('vue-test-simple');

        console.log('Analytics app element:', analyticsApp);
        console.log('Vue test element:', vueTestSimple);

        // Try to mount simple Vue component
        if (window.Vue && vueTestSimple) {
            try {
                const { createApp } = window.Vue;
                const app = createApp({
                    data() {
                        return {
                            message: '✅ Vue component montato con successo!'
                        }
                    },
                    template: '<p v-text="message"></p>'
                });
                app.mount('#vue-test-simple');
                console.log('✅ Simple Vue component mounted');
            } catch (error) {
                console.error('❌ Error mounting simple Vue:', error);
                vueTestSimple.innerHTML = '<p>❌ Errore Vue: ' + error.message + '</p>';
            }
        }

        // Check if analytics app has content
        if (analyticsApp) {
            const content = analyticsApp.innerHTML;
            console.log('Analytics app content length:', content.length);
            console.log('Analytics app content:', content.substring(0, 200));
        }

    }, 3000);
});

// Set page loaded flag
window.__viteLoaded = true;
</script>

@push('styles')
<style>
.diagnostic-box {
    border: 2px solid #e2e8f0;
    margin-bottom: 1rem;
}
</style>
@endpush
