<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📧 Newsletter & Lead Marketing
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('store.newsletters.leads') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                    👥 Gestisci Lead ({{ $leadsCount }})
                </a>
                @if(auth('store')->user()->is_premium)
                    <a href="{{ route('store.newsletters.create') }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors">
                        ➕ Crea Newsletter
                    </a>
                @else
                    <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-md cursor-not-allowed">
                        🔒 Crea Newsletter (Premium)
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(!auth('store')->user()->is_premium)
                <!-- Premium Feature Notice -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-6 mb-6 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="text-4xl">🚀</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold mb-2">Sblocca le Newsletter Premium!</h3>
                            <p class="text-purple-100 mb-4">
                                Con il piano Premium puoi creare e inviare newsletter personalizzate ai tuoi lead,
                                aumentare l'engagement e far crescere il tuo business.
                            </p>
                            <div class="flex space-x-4">
                                <button class="bg-white text-purple-600 px-6 py-2 rounded-md font-medium hover:bg-gray-100 transition-colors">
                                    Upgrade a Premium
                                </button>
                                <span class="text-purple-200 text-sm self-center">
                                    A partire da €19/mese
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                    <dd class="text-lg font-medium text-gray-900">{{ $leadsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">📧</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Newsletter Inviate</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $newsletters->where('status', 'sent')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">📝</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Bozze</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $newsletters->where('status', 'draft')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">📊</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tasso Apertura</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        @php
                                            $sentNewsletters = $newsletters->where('status', 'sent');
                                            $avgOpenRate = $sentNewsletters->count() > 0 ? $sentNewsletters->avg('open_rate') : 0;
                                        @endphp
                                        {{ number_format($avgOpenRate, 1) }}%
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter List -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Le tue Newsletter</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Gestisci e monitora le tue campagne newsletter.
                    </p>
                </div>

                @if($newsletters->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($newsletters as $newsletter)
                            <li>
                                <div class="px-4 py-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($newsletter->status === 'sent')
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <span class="text-green-600 text-sm">✓</span>
                                                </div>
                                            @elseif($newsletter->status === 'sending')
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-600 text-sm">↗</span>
                                                </div>
                                            @elseif($newsletter->status === 'scheduled')
                                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <span class="text-yellow-600 text-sm">⏰</span>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <span class="text-gray-600 text-sm">📝</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $newsletter->title }}
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="capitalize">{{ $newsletter->status }}</span>
                                                @if($newsletter->recipients_count)
                                                    <span>{{ $newsletter->recipients_count }} destinatari</span>
                                                @endif
                                                @if($newsletter->sent_at)
                                                    <span>Inviata {{ $newsletter->sent_at->format('d/m/Y H:i') }}</span>
                                                @else
                                                    <span>Creata {{ $newsletter->created_at->format('d/m/Y H:i') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @if($newsletter->status === 'sent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $newsletter->open_rate }}% aperture
                                            </span>
                                        @endif

                                        <a href="{{ route('store.newsletters.show', $newsletter) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Visualizza
                                        </a>

                                        @if($newsletter->status === 'draft')
                                            <a href="{{ route('store.newsletters.edit', $newsletter) }}"
                                               class="text-yellow-600 hover:text-yellow-900">
                                                Modifica
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200">
                        {{ $newsletters->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📧</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna newsletter ancora</h3>
                        <p class="text-gray-500 mb-6">Inizia a creare newsletter per coinvolgere i tuoi lead!</p>
                        @if(auth('store')->user()->is_premium)
                            <a href="{{ route('store.newsletters.create') }}"
                               class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors">
                                Crea la tua prima Newsletter
                            </a>
                        @else
                            <button disabled class="bg-gray-400 text-white px-6 py-2 rounded-md cursor-not-allowed">
                                Upgrade per creare Newsletter
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
