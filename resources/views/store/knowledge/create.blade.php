<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aggiungi Informazione') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Aggiungi Nuova Informazione</h2>
                        <p class="text-gray-600 mt-1">Crea una risposta personalizzata per il tuo chatbot</p>
                    </div>

                    <form method="POST" action="{{ route('store.knowledge.store') }}" class="space-y-6">
                        @csrf

                        <!-- Domanda/Trigger -->
                        <div>
                            <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                Domanda o Frase Trigger
                            </label>
                            <input type="text"
                                   id="question"
                                   name="question"
                                   value="{{ old('question') }}"
                                   placeholder="es. Quali sono i vostri orari?, Come posso prenotare?, Quanto costa..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   maxlength="500"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">
                                La domanda principale che dovrebbe attivare questa risposta
                            </p>
                            @error('question')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keywords -->
                        <div>
                            <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                Parole Chiave (opzionale)
                            </label>
                            <input type="text"
                                   id="keywords"
                                   name="keywords"
                                   value="{{ old('keywords') }}"
                                   placeholder="es. orari, prenotazione, prezzo, costo, servizio"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <p class="text-sm text-gray-500 mt-1">
                                Parole chiave separate da virgola che possono attivare questa risposta
                            </p>
                            @error('keywords')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Risposta -->
                        <div>
                            <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                                Risposta Personalizzata
                            </label>
                            <textarea id="answer"
                                      name="answer"
                                      rows="8"
                                      placeholder="Inserisci la risposta che il chatbot dovrebbe dare quando questa domanda viene posta...

Esempio:
I nostri orari di apertura sono:
- Lunedì-Venerdì: 9:00-18:00
- Sabato: 9:00-17:00
- Domenica: Chiuso

Per informazioni aggiuntive puoi chiamarci al 123-456-7890."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                      maxlength="2000"
                                      required>{{ old('answer') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                La risposta esatta che il chatbot darà ai clienti
                            </p>
                            @error('answer')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priorità e Stato -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Priorità
                                </label>
                                <select id="priority"
                                        name="priority"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>1 - Bassa</option>
                                    <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>3 - Normale</option>
                                    <option value="5" {{ old('priority', '5') == '5' ? 'selected' : '' }}>5 - Media</option>
                                    <option value="7" {{ old('priority') == '7' ? 'selected' : '' }}>7 - Alta</option>
                                    <option value="10" {{ old('priority') == '10' ? 'selected' : '' }}>10 - Massima</option>
                                </select>
                                <p class="text-sm text-gray-500 mt-1">
                                    Priorità più alta = risposta mostrata per prima
                                </p>
                                @error('priority')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stato
                                </label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio"
                                               name="is_active"
                                               value="1"
                                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Attivo</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio"
                                               name="is_active"
                                               value="0"
                                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                                               {{ old('is_active') == '0' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Disattivo</span>
                                    </label>
                                </div>
                                @error('is_active')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Anteprima -->
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <h3 class="text-lg font-medium text-emerald-900 mb-3">💡 Come funziona</h3>
                            <div class="text-sm text-emerald-700 space-y-2">
                                <p><strong>1. Ricerca Intelligente:</strong> Il chatbot cercherà prima nelle tue informazioni personalizzate</p>
                                <p><strong>2. Priorità:</strong> Le risposte con priorità più alta vengono mostrate per prime</p>
                                <p><strong>3. Fallback AI:</strong> Se non trova una risposta personalizzata, userà l'AI generale</p>
                                <p><strong>4. Parole Chiave:</strong> Anche variazioni della domanda attiveranno la risposta giusta</p>
                            </div>
                        </div>

                        <!-- Informazioni Profilo -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h3 class="text-lg font-medium text-blue-900 mb-3">📋 Informazioni Automatiche</h3>
                            <div class="text-sm text-blue-700 space-y-2">
                                <p><strong>Il chatbot conosce già:</strong> Le informazioni del tuo profilo (telefono, indirizzo, sito web) e può usarle automaticamente quando necessario.</p>
                                <p><strong>Esempio:</strong> Se un cliente chiede "Come posso contattarvi?", l'AI userà automaticamente telefono e indirizzo dal tuo profilo.</p>
                                <p><strong>Nota:</strong> Per sicurezza, email e password non sono mai condivise con il chatbot.</p>
                                <p><strong>Suggerimento:</strong> Completa il tuo <a href="{{ route('store.profile.edit') }}" class="underline font-medium">profilo store</a> per risposte più dettagliate!</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-between">
                            <a href="{{ route('store.knowledge.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-6 rounded-md transition duration-200">
                                Annulla
                            </a>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                                Salva Informazione
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
