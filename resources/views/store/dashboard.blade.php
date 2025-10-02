<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">{{ auth('store')->user()->name }} Dashboard</h1>
                        <div class="flex items-center space-x-3">
                            @if(auth('store')->user()->is_premium)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                                    Premium Account
                                </span>
                            @endif
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ auth('store')->user()->email }}
                            </div>
                        </div>
                    </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Chat Sessions</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['chat_sessions'] }}</p>
                        <p class="text-sm text-blue-700">This month</p>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-900 mb-2">Leads Generated</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['leads_generated'] }}</p>
                        <p class="text-sm text-green-700">Total leads</p>
                    </div>

                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">QR Scans</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['qr_scans'] }}</p>
                        <p class="text-sm text-yellow-700">This month</p>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">AI Responses</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['ai_responses'] }}</p>
                        <p class="text-sm text-purple-700">Total responses</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-bold mb-4">Chatbot Status</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Assistente:</span>
                                <span class="font-semibold">{{ auth('store')->user()->assistant_name ?? 'Assistente AI' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Stato:</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ auth('store')->user()->chat_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ auth('store')->user()->chat_enabled ? 'Attivo' : 'Disattivato' }}
                                </span>
                            </div>
                            <div class="pt-4">
                                <a href="{{ route('store.chatbot.edit') }}"
                                   class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center px-4 py-2 rounded block">
                                    ⚙️ Configura Chatbot
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-bold mb-4">Quick Links</h2>
                        <div class="space-y-3">
                            <a href="{{ route('store.chatbot', auth('store')->user()) }}"
                               class="block bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded">
                                🤖 Prova Chatbot
                            </a>
                            <a href="{{ route('store.chatbot.edit') }}"
                               class="block bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded">
                                🎨 Personalizza Chat
                            </a>
                            <a href="{{ route('store.analytics.index') }}"
                               class="block bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded">
                                📊 Analytics
                            </a>
                            <a href="{{ route('store.profile.show') }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-center block flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Gestisci Profilo
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-4">Store Information</h2>
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Store Name</dt>
                                <dd class="text-sm text-gray-900">{{ auth('store')->user()->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Store URL</dt>
                                <dd class="text-sm text-gray-900">{{ url('/' . auth('store')->user()->slug) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ auth('store')->user()->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                                <dd class="text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ auth('store')->user()->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ auth('store')->user()->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
