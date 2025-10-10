@extends('store.layouts.app-compiled')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">📊 Analytics Dashboard - Compiled Assets</h2>
                <p class="text-gray-600">Test del caricamento con assets compilati</p>
            </div>

            <!-- Test Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">🧪 Test Assets Loading</h3>
                <div id="asset-test">
                    <p>✅ HTML caricato</p>
                    <p id="js-status">⏳ Controllo JavaScript...</p>
                    <p id="vue-status">⏳ Controllo Vue...</p>
                </div>
            </div>

            <!-- Vue Analytics Component -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">📊 Analytics Component</h3>
                <div id="analytics-app">
                    <div class="p-4 bg-gray-100 rounded">
                        <p>⏳ Caricamento componente Vue...</p>
                    </div>
                    <analytics-dashboard></analytics-dashboard>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Analytics page loaded');
            document.getElementById('js-status').innerHTML = '✅ JavaScript funziona';

            setTimeout(function() {
                const vueStatus = document.getElementById('vue-status');
                if (typeof window.Vue !== 'undefined') {
                    vueStatus.innerHTML = '✅ Vue caricato correttamente';
                    console.log('✅ Vue is available');
                } else {
                    vueStatus.innerHTML = '❌ Vue non caricato';
                    console.log('❌ Vue not available');
                }

                // Check analytics app content
                const analyticsApp = document.getElementById('analytics-app');
                if (analyticsApp) {
                    console.log('Analytics app HTML length:', analyticsApp.innerHTML.length);
                    console.log('Analytics app content preview:', analyticsApp.innerHTML.substring(0, 200));
                }
            }, 2000);
        });
    </script>
@endsection
