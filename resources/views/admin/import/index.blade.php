@extends('layouts.admin')

@section('title', 'Sistema di Importazione')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Enhanced Header with navigation breadcrumb -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Importazione Dati</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Sistema di Importazione</h1>
                    <p class="mt-2 text-gray-600">Importa prodotti e ordini da file Excel/CSV con formati specifici</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.import.products') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                        📦 Quick Import Products
                    </a>
                    <a href="{{ route('admin.import.orders') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                        🏪 Import Orders CSV
                    </a>
                    <a href="{{ route('admin.import.template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                        � Download Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Enhanced Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Products Stats -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 overflow-hidden shadow-lg sm:rounded-lg border border-blue-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-lg">📦</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-blue-900">Prodotti</h3>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($stats['total_products']) }}</p>
                            <p class="text-sm text-blue-600">Totali nel sistema</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Stats -->
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 overflow-hidden shadow-lg sm:rounded-lg border border-purple-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-lg">📋</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-purple-900">Ordini</h3>
                            <p class="text-2xl font-bold text-purple-700">{{ number_format($stats['total_orders']) }}</p>
                            <p class="text-sm text-purple-600">Gestiti nel sistema</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stores Stats -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 overflow-hidden shadow-lg sm:rounded-lg border border-green-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-lg">🏪</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-green-900">Negozi</h3>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($stats['total_stores']) }}</p>
                            <p class="text-sm text-green-600">Clienti registrati</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Growers Stats -->
            <div class="bg-gradient-to-r from-teal-50 to-teal-100 overflow-hidden shadow-lg sm:rounded-lg border border-teal-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-lg">🌱</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-teal-900">Grower</h3>
                            <p class="text-2xl font-bold text-teal-700">{{ number_format($stats['total_growers']) }}</p>
                            <p class="text-sm text-teal-600">Fornitori attivi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Products Import -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">📦 Import Products</h2>
                        <a href="{{ route('admin.import.template', 'products') }}"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            📥 Download Template
                        </a>
                    </div>

                    <p class="text-gray-600 mb-4">
                        Upload an Excel file with product data to bulk import products into the system.
                    </p>

                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">Expected Columns:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>1. Product Name (required)</li>
                            <li>2. Product Code</li>
                            <li>3. EAN Code</li>
                            <li>4. Description</li>
                            <li>5. Quantity</li>
                            <li>6. Height (cm)</li>
                            <li>7. Price (€)</li>
                            <li>8. Category</li>
                            <li>9. Client</li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.import.products') }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center font-medium inline-block">
                        Import Products →
                    </a>
                </div>
            </div>

            <!-- Structured Orders Import -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">🏪 Import Ordini Strutturati</h2>
                        <span class="text-sm bg-purple-100 text-purple-800 px-2 py-1 rounded font-medium">Formato Italiano</span>
                    </div>

                    <p class="text-gray-600 mb-4">
                        Importa ordini dal formato CSV/Excel strutturato con 19 colonne. Gli ordini vengono raggruppati per codice cliente + data.
                    </p>

                    <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                        <h4 class="font-semibold text-purple-800 mb-2">Caratteristiche:</h4>
                        <ul class="text-sm text-purple-700 space-y-1">
                            <li>• Raggruppamento automatico per CODE + Data</li>
                            <li>• Creazione automatica di negozi, grower e prodotti</li>
                            <li>• 19 colonne specifiche (Fornitore, Piani, Quantità, etc.)</li>
                            <li>• Formato data italiano (DD/MM/YYYY)</li>
                            <li>• Gestione automatica dei prezzi e quantità</li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.import.orders') }}"
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-center font-medium inline-block">
                        Import Orders CSV →
                    </a>
                </div>
            </div>

            <!-- Complete Orders Import -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">🛒 Complete Orders Import</h2>
                        <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded font-medium">OrderItems</span>
                    </div>

                    <p class="text-gray-600 mb-4">
                        Importa OrderItems completi con auto-creazione di Growers, Prodotti, Store e Ordini. Ogni riga CSV = un OrderItem.
                    </p>

                    <div class="mb-4 p-3 bg-green-50 rounded-lg">
                        <h4 class="font-semibold text-green-800 mb-2">Caratteristiche:</h4>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Auto-creazione Growers da "Fornitore"</li>
                            <li>• Auto-creazione Prodotti con codici e prezzi</li>
                            <li>• Auto-creazione Stores da "Cliente"</li>
                            <li>• Raggruppamento Orders per Cliente+CC+PIA+PRO+Data</li>
                            <li>• Mapping colonne intelligente</li>
                            <li>• Supporta CSV reali con 19+ colonne</li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.import.orders') }}"
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center font-medium inline-block">
                        Advanced Import →
                    </a>
                </div>
            </div>
        </div>

        <!-- Enhanced Instructions -->
        <div class="mt-8 bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">📚 Guida all'Importazione</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2">1</span>
                            Formati Supportati
                        </h3>
                        <ul class="text-sm text-gray-600 space-y-1 ml-8">
                            <li>• File Excel (.xlsx, .xls)</li>
                            <li>• File CSV (.csv)</li>
                            <li>• Dimensione massima: 10MB</li>
                            <li>• Encoding UTF-8 consigliato</li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <span class="w-6 h-6 bg-purple-500 text-white rounded-full flex items-center justify-center text-xs mr-2">2</span>
                            Processo di Import
                        </h3>
                        <ul class="text-sm text-gray-600 space-y-1 ml-8">
                            <li>• Scarica il template appropriato</li>
                            <li>• Compila i dati seguendo il formato</li>
                            <li>• Carica il file tramite il form</li>
                            <li>• Verifica il riepilogo risultati</li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs mr-2">3</span>
                            Funzionalità Avanzate
                        </h3>
                        <ul class="text-sm text-gray-600 space-y-1 ml-8">
                            <li>• Auto-creazione di entità mancanti</li>
                            <li>• Rilevazione automatica duplicati</li>
                            <li>• Raggruppamento intelligente ordini</li>
                            <li>• Validazione dati in tempo reale</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-800 mb-3 flex items-center">
                        <span class="text-yellow-600 mr-2">⚠️</span>
                        Note Importanti
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• La prima riga deve contenere le intestazioni</li>
                            <li>• Le righe vuote verranno automaticamente ignorate</li>
                            <li>• I codici EAN duplicati verranno segnalati e saltati</li>
                        </ul>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Per ordini strutturati: formato data DD/MM/YYYY</li>
                            <li>• Il sistema crea automaticamente negozi e fornitori mancanti</li>
                            <li>• Ogni errore viene riportato nel riepilogo finale</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Support -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">⚡ Azioni Rapide</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 hover:text-blue-800 transition-colors">
                        <span class="font-medium">Gestisci Prodotti</span>
                        <span class="text-xl">📦</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg text-green-700 hover:text-green-800 transition-colors">
                        <span class="font-medium">Visualizza Ordini</span>
                        <span class="text-xl">📋</span>
                    </a>
                    <a href="{{ route('admin.accounts.stores.create') }}"
                       class="flex items-center justify-between p-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-purple-700 hover:text-purple-800 transition-colors">
                        <span class="font-medium">Gestisci Negozi</span>
                        <span class="text-xl">🏪</span>
                    </a>
                </div>
            </div>

            <!-- Support & Help -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">🆘 Supporto</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-800 mb-2">Problemi con l'import?</h4>
                            <p class="text-sm text-gray-600 mb-3">
                                Verifica che il file rispetti il formato richiesto e non superi i 10MB.
                            </p>
                            <div class="flex space-x-2">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Excel</span>
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">CSV</span>
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">UTF-8</span>
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Hai bisogno di aiuto?</p>
                            <div class="flex justify-center space-x-3">
                                <span class="text-2xl">📧</span>
                                <span class="text-2xl">💬</span>
                                <span class="text-2xl">📞</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
