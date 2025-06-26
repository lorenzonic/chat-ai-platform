<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chatbot Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Impostazioni Chatbot</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Stato:</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $store->chat_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $store->chat_enabled ? 'Attivo' : 'Disattivato' }}
                            </span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('store.chatbot.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Abilitazione Chat -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Abilita Chatbot</h3>
                                    <p class="text-sm text-gray-600">Attiva o disattiva il chatbot per i tuoi clienti</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="chat_enabled" value="1" class="sr-only peer"
                                           {{ $store->chat_enabled ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Nome Assistente -->
                        <div>
                            <label for="assistant_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome dell'Assistente
                            </label>
                            <input type="text"
                                   id="assistant_name"
                                   name="assistant_name"
                                   value="{{ old('assistant_name', $store->assistant_name) }}"
                                   placeholder="es. Sofia, Marco, Assistente Verde..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   maxlength="50">
                            <p class="text-sm text-gray-500 mt-1">
                                Il nome che i clienti vedranno quando chattano con il bot
                            </p>
                            @error('assistant_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Messaggio di Apertura -->
                        <div>
                            <label for="chat_opening_message" class="block text-sm font-medium text-gray-700 mb-2">
                                Messaggio di Apertura
                            </label>
                            <textarea id="chat_opening_message"
                                      name="chat_opening_message"
                                      rows="3"
                                      placeholder="{{ $store->getDefaultOpeningMessage() }}"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                      maxlength="500">{{ old('chat_opening_message', $store->chat_opening_message) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                Il primo messaggio che i clienti vedranno quando aprono la chat. Lascia vuoto per usare il messaggio predefinito.
                            </p>
                            @error('chat_opening_message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Colore Tema -->
                        <div>
                            <label for="chat_theme_color" class="block text-sm font-medium text-gray-700 mb-2">
                                Colore Tema Chat
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="color"
                                       id="chat_theme_color"
                                       name="chat_theme_color"
                                       value="{{ old('chat_theme_color', $store->chat_theme_color) }}"
                                       class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                <span class="text-sm text-gray-600">
                                    Personalizza il colore del chatbot
                                </span>
                            </div>
                            @error('chat_theme_color')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contesto Chat -->
                        <div>
                            <label for="chat_context" class="block text-sm font-medium text-gray-700 mb-2">
                                Contesto e Informazioni del Negozio
                            </label>
                            <textarea id="chat_context"
                                      name="chat_context"
                                      rows="8"
                                      placeholder="Descrivi il tuo negozio, i prodotti che vendi, i servizi offerti, le tue specialità...

Esempio:
Siamo un vivaio specializzato in piante da interno e da giardino. Offriamo:
- Piante da appartamento (pothos, monstera, ficus)
- Piante da esterno e da giardino
- Consulenza per la cura delle piante
- Servizio di rinvaso
- Fertilizzanti e accessori per giardinaggio

I nostri esperti sono sempre disponibili per consigli personalizzati!"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('chat_context', $store->chat_context) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                Queste informazioni aiuteranno l'IA a rispondere in modo più accurato e personalizzato per il tuo business
                            </p>
                            @error('chat_context')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Orari di Apertura -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Orari di Apertura
                            </label>
                            <div class="space-y-3">
                                @php
                                    $days = [
                                        'monday' => 'Lunedì',
                                        'tuesday' => 'Martedì',
                                        'wednesday' => 'Mercoledì',
                                        'thursday' => 'Giovedì',
                                        'friday' => 'Venerdì',
                                        'saturday' => 'Sabato',
                                        'sunday' => 'Domenica'
                                    ];
                                    $currentHours = old('opening_hours', $store->opening_hours ?? []);
                                @endphp

                                @foreach($days as $day => $dayName)
                                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-20 text-sm font-medium text-gray-700">
                                            {{ $dayName }}
                                        </div>

                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   name="opening_hours[{{ $day }}][closed]"
                                                   value="1"
                                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                   {{ isset($currentHours[$day]['closed']) && $currentHours[$day]['closed'] ? 'checked' : '' }}
                                                   onchange="toggleDayInputs('{{ $day }}', this.checked)">
                                            <span class="ml-2 text-sm text-gray-600">Chiuso</span>
                                        </label>

                                        <div id="hours-{{ $day }}" class="flex items-center space-x-2"
                                             style="{{ isset($currentHours[$day]['closed']) && $currentHours[$day]['closed'] ? 'display: none;' : '' }}">
                                            <input type="time"
                                                   name="opening_hours[{{ $day }}][open]"
                                                   value="{{ $currentHours[$day]['open'] ?? '09:00' }}"
                                                   class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                            <span class="text-gray-500">-</span>
                                            <input type="time"
                                                   name="opening_hours[{{ $day }}][close]"
                                                   value="{{ $currentHours[$day]['close'] ?? '18:00' }}"
                                                   class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                Gli orari verranno mostrati ai clienti nel chatbot
                            </p>
                            @error('opening_hours')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Font Family -->
                        <div>
                            <label for="chat_font_family" class="block text-sm font-medium text-gray-700 mb-2">
                                Font del Chatbot
                            </label>
                            <select id="chat_font_family"
                                    name="chat_font_family"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach(\App\Models\Store::getAvailableFonts() as $value => $label)
                                    <option value="{{ $value }}"
                                            {{ old('chat_font_family', $store->chat_font_family ?? 'Inter') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                Scegli il font Google Fonts per il tuo chatbot
                            </p>
                            @error('chat_font_family')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- AI Tone -->
                        <div>
                            <label for="chat_ai_tone" class="block text-sm font-medium text-gray-700 mb-2">
                                Tono dell'Assistente AI
                            </label>
                            <select id="chat_ai_tone"
                                    name="chat_ai_tone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach(\App\Models\Store::getAvailableAiTones() as $value => $label)
                                    <option value="{{ $value }}"
                                            {{ old('chat_ai_tone', $store->chat_ai_tone ?? 'professional') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                Scegli come deve comunicare il tuo assistente AI
                            </p>
                            @error('chat_ai_tone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avatar Image -->
                        <div>
                            <label for="chat_avatar_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Immagine Avatar (URL)
                            </label>
                            <input type="url"
                                   id="chat_avatar_image"
                                   name="chat_avatar_image"
                                   value="{{ old('chat_avatar_image', $store->chat_avatar_image) }}"
                                   placeholder="https://esempio.com/avatar.jpg"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <p class="text-sm text-gray-500 mt-1">
                                URL di un'immagine da usare come avatar. Lascia vuoto per usare le iniziali del nome.
                            </p>
                            @error('chat_avatar_image')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Chat Suggestions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Suggerimenti di Chat
                            </label>
                            <div id="suggestions-container">
                                @php
                                    $suggestions = old('chat_suggestions', $store->getChatSuggestions());
                                @endphp
                                @foreach($suggestions as $index => $suggestion)
                                    <div class="suggestion-item flex items-center space-x-2 mb-2">
                                        <input type="text"
                                               name="chat_suggestions[]"
                                               value="{{ $suggestion }}"
                                               placeholder="Inserisci un suggerimento..."
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                               maxlength="100">
                                        <button type="button" onclick="removeSuggestion(this)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button"
                                    onclick="addSuggestion()"
                                    class="mt-2 text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                + Aggiungi Suggerimento
                            </button>
                            <p class="text-sm text-gray-500 mt-1">
                                Suggerimenti che appariranno nel chatbot per aiutare i clienti a iniziare la conversazione
                            </p>
                            @error('chat_suggestions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <h3 class="text-lg font-medium text-emerald-900 mb-3">Anteprima Chat</h3>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                         style="background-color: {{ $store->chat_theme_color }}">
                                        <span id="preview-name">{{ substr($store->assistant_name, 0, 1) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900" id="preview-full-name">{{ $store->assistant_name }}</span>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-3 text-sm text-gray-700">
                                    Ciao! Sono <span id="preview-assistant-name">{{ $store->assistant_name }}</span>. Come posso aiutarti oggi?
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                                Salva Impostazioni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Update preview when assistant name changes
    document.getElementById('assistant_name').addEventListener('input', function() {
        const name = this.value || 'Assistente AI';
        document.getElementById('preview-name').textContent = name.charAt(0).toUpperCase();
        document.getElementById('preview-full-name').textContent = name;
        document.getElementById('preview-assistant-name').textContent = name;
    });

    // Toggle day inputs for opening hours
    function toggleDayInputs(day, isClosed) {
        const hoursDiv = document.getElementById('hours-' + day);
        if (isClosed) {
            hoursDiv.style.display = 'none';
        } else {
            hoursDiv.style.display = 'flex';
        }
    }

    // Add new suggestion
    function addSuggestion() {
        const container = document.getElementById('suggestions-container');
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'suggestion-item flex items-center space-x-2 mb-2';
        suggestionDiv.innerHTML = `
            <input type="text"
                   name="chat_suggestions[]"
                   placeholder="Inserisci un suggerimento..."
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                   maxlength="100">
            <button type="button" onclick="removeSuggestion(this)" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(suggestionDiv);
    }

    // Remove suggestion
    function removeSuggestion(button) {
        button.closest('.suggestion-item').remove();
    }
    </script>
</x-store-layout>
