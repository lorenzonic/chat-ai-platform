<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Analytics Dashboard
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistiche Principali -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">📈 Dashboard Analytics</h3>

                    @php
                        $chatLogs = \App\Models\ChatLog::where('store_id', $store->id)->count();
                        $leads = \App\Models\Lead::where('store_id', $store->id)->count();
                        $recentChats = \App\Models\ChatLog::where('store_id', $store->id)
                            ->where('created_at', '>=', now()->subDays(7))
                            ->count();
                        $todayChats = \App\Models\ChatLog::where('store_id', $store->id)
                            ->whereDate('created_at', today())
                            ->count();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-6 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="text-3xl font-bold text-blue-600">{{ $chatLogs }}</div>
                            <div class="text-sm text-blue-800 mt-1">Chat Totali</div>
                            <div class="text-xs text-blue-600 mt-2">💬 Tutte le conversazioni</div>
                        </div>
                        <div class="text-center p-6 bg-green-50 rounded-lg border border-green-200">
                            <div class="text-3xl font-bold text-green-600">{{ $leads }}</div>
                            <div class="text-sm text-green-800 mt-1">Lead Generati</div>
                            <div class="text-xs text-green-600 mt-2">👥 Contatti acquisiti</div>
                        </div>
                        <div class="text-center p-6 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="text-3xl font-bold text-yellow-600">{{ $recentChats }}</div>
                            <div class="text-sm text-yellow-800 mt-1">Ultimi 7 Giorni</div>
                            <div class="text-xs text-yellow-600 mt-2">📅 Chat recenti</div>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-lg border border-purple-200">
                            <div class="text-3xl font-bold text-purple-600">{{ $todayChats }}</div>
                            <div class="text-sm text-purple-800 mt-1">Oggi</div>
                            <div class="text-xs text-purple-600 mt-2">🕐 Chat di oggi</div>
                        </div>
                    </div>

                    @if($chatLogs > 0)
                        <!-- Chat Recenti -->
                        <div class="mt-8">
                            <h4 class="text-md font-semibold mb-4 text-gray-800">💬 Chat Recenti</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Messaggio Utente</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Risposta AI</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse(\App\Models\ChatLog::where('store_id', $store->id)->latest()->limit(10)->get() as $chat)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-600">
                                                    {{ $chat->created_at->format('d/m H:i') }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-800">
                                                    {{ Str::limit($chat->user_message, 50) }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600">
                                                    {{ Str::limit($chat->ai_response, 50) }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ $chat->ip_address }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                    Nessuna chat trovata
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <!-- Stato Vuoto -->
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🤖</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Nessuna chat ancora</h3>
                            <p class="text-gray-600 mb-4">Il tuo chatbot non ha ancora ricevuto conversazioni.</p>
                            <a href="{{ route('store.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Torna alla Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Link Utili -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🔗 Link Utili</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('store.dashboard') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-lg font-semibold text-blue-600 mb-1">📊 Dashboard</div>
                            <div class="text-sm text-gray-600">Torna alla dashboard principale</div>
                        </a>
                        <a href="{{ route('store.newsletters.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-lg font-semibold text-green-600 mb-1">📧 Newsletter</div>
                            <div class="text-sm text-gray-600">Gestisci newsletter e lead</div>
                        </a>
                        <a href="{{ route('store.knowledge.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-lg font-semibold text-purple-600 mb-1">📚 Knowledge</div>
                            <div class="text-sm text-gray-600">Gestisci la knowledge base</div>
                        </a>
                    </div>

                    <!-- Seconda riga di link -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <a href="{{ route('store.chatbot.edit') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-lg font-semibold text-indigo-600 mb-1">🤖 Chatbot</div>
                            <div class="text-sm text-gray-600">Configura il chatbot</div>
                        </a>
                        <a href="{{ route('store.profile.show') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-lg font-semibold text-gray-600 mb-1">⚙️ Profilo</div>
                            <div class="text-sm text-gray-600">Modifica il tuo profilo</div>
                        </a>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="text-lg font-semibold text-gray-400 mb-1">🚀 Features</div>
                            <div class="text-sm text-gray-500">Prossimamente disponibili</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
