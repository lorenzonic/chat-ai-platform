<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
                                Profilo Store
                            </h1>
                            <p class="mt-1 text-sm text-gray-600">
                                Visualizza e gestisci le informazioni del tuo store.
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('store.profile.edit') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifica Profilo
                            </a>
                            <a href="{{ route('store.profile.password.edit') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Cambia Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                    <!-- Informazioni Base -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Informazioni Store
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome Store</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $store->name ?: 'Non specificato' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $store->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Slug URL</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('store.chatbot', $store->slug) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-500">
                                        /{{ $store->slug }}
                                    </a>
                                </p>
                            </div>
                            @if($store->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descrizione</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $store->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informazioni Contatto -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contatti
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefono</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $store->phone ?: 'Non specificato' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Indirizzo</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $store->address ?: 'Non specificato' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Città</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->city ?: 'Non specificata' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CAP</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->postal_code ?: 'Non specificato' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stato/Provincia</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->state ?: 'Non specificato' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Paese</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->country ?: 'Non specificato' }}</p>
                                </div>
                            </div>
                            @if($store->website)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sito Web</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="{{ $store->website }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-500">
                                        {{ $store->website }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stato Account -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Stato Account
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Stato</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $store->is_active ? 'Attivo' : 'Disattivato' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Piano</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->is_premium ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $store->is_premium ? 'Premium' : 'Standard' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Chatbot</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->chat_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $store->chat_enabled ? 'Abilitato' : 'Disabilitato' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Data Registrazione</span>
                                <span class="text-sm text-gray-900">{{ $store->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Azioni Account -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Zona Pericolosa
                        </h3>
                        <div class="space-y-3">
                            <p class="text-sm text-gray-600">
                                Una volta eliminato l'account, tutti i dati verranno cancellati definitivamente.
                                Prima di eliminare l'account, scarica eventuali dati che desideri conservare.
                            </p>
                            <form method="POST" action="{{ route('store.profile.destroy') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Sei sicuro di voler eliminare il tuo account? Questa azione non può essere annullata.')"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Elimina Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
