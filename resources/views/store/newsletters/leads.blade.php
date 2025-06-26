<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👥 Gestione Lead
            </h2>
            <a href="{{ route('store.newsletters.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                ← Torna alle Newsletter
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">👥</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Lead Totali</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">✅</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Iscritti</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['subscribed'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">📍</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Con Posizione</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        @php
                                            $withLocation = \App\Models\Lead::where('store_id', auth('store')->user()->id)
                                                ->whereNotNull('latitude')
                                                ->whereNotNull('longitude')
                                                ->count();
                                        @endphp
                                        {{ $withLocation }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">📅</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Questo Mese</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['this_month'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Cities -->
            @php
                $topCities = \App\Models\Lead::where('store_id', auth('store')->user()->id)
                    ->whereNotNull('city')
                    ->selectRaw('city, country, COUNT(*) as count')
                    ->groupBy('city', 'country')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($topCities->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🏙️ Città più Attive</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        @foreach($topCities as $cityData)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $cityData->count }}</div>
                                <div class="text-sm text-gray-600">{{ $cityData->city }}</div>
                                <div class="text-xs text-gray-400">{{ $cityData->country }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Lead List -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">I tuoi Lead</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Lead raccolti dal chatbot e altre fonti.
                    </p>
                </div>

                @if($leads->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contatto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        WhatsApp
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Posizione
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Interessi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fonte
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stato
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($leads as $lead)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <span class="text-indigo-600 font-medium">
                                                            {{ $lead->name ? strtoupper(substr($lead->name, 0, 1)) : '👤' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $lead->name ?: 'Anonimo' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $lead->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($lead->whatsapp)
                                                <a href="https://wa.me/{{ str_replace([' ', '+', '-'], '', $lead->whatsapp) }}"
                                                   target="_blank"
                                                   class="text-green-600 hover:text-green-900 flex items-center">
                                                    📱 {{ $lead->whatsapp }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($lead->full_location)
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-900">📍 {{ $lead->full_location }}</span>
                                                    @if($lead->hasCoordinates())
                                                        <a href="{{ $lead->map_url }}"
                                                           target="_blank"
                                                           class="text-blue-600 hover:text-blue-900 text-xs"
                                                           title="Vedi su Google Maps">
                                                            🗺️
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($lead->country_code)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ strtoupper($lead->country_code) }}
                                                        @if($lead->postal_code)
                                                            • {{ $lead->postal_code }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Posizione non disponibile</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($lead->tag)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $lead->tag }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $lead->source }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($lead->subscribed)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✅ Iscritto
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ❌ Non iscritto
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $lead->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $lead->created_at->format('H:i') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200">
                        {{ $leads->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">👥</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun lead ancora</h3>
                        <p class="text-gray-500 mb-6">I lead del chatbot appariranno qui automaticamente.</p>
                        <a href="{{ route('store.qr-codes.index') }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors">
                            Vai ai QR Code per iniziare
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
