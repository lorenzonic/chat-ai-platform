<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ➕ Crea Newsletter
            </h2>
            <a href="{{ route('store.newsletters.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                ← Annulla
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('store.newsletters.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titolo Newsletter *
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Es: Offerta speciale piante primaverili">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Contenuto Newsletter *
                            </label>
                            <textarea name="content"
                                      id="content"
                                      rows="10"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Scrivi qui il contenuto della newsletter. Puoi usare HTML per la formattazione.">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                💡 Supporta HTML per formattazione (grassetto, corsivo, link, ecc.)
                            </p>
                        </div>

                        <!-- Images -->
                        <div class="mb-6">
                            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                Immagini (opzionale)
                            </label>
                            <input type="file"
                                   name="images[]"
                                   id="images"
                                   multiple
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                📷 Puoi caricare più immagini (JPEG, PNG, GIF - max 2MB ciascuna)
                            </p>
                        </div>

                        <!-- CTA Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">🔗 Call-to-Action (opzionale)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cta_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Testo Pulsante
                                    </label>
                                    <input type="text"
                                           name="cta_text"
                                           id="cta_text"
                                           value="{{ old('cta_text') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Es: Scopri l'offerta">
                                    @error('cta_text')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cta_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        URL Destinazione
                                    </label>
                                    <input type="url"
                                           name="cta_url"
                                           id="cta_url"
                                           value="{{ old('cta_url') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="https://esempio.com">
                                    @error('cta_url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <div class="mb-6">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                                ⏰ Programmazione (opzionale)
                            </label>
                            <input type="datetime-local"
                                   name="scheduled_at"
                                   id="scheduled_at"
                                   value="{{ old('scheduled_at') }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                🗓️ Se specificato, la newsletter verrà programmata per l'invio. Altrimenti sarà salvata come bozza.
                            </p>
                        </div>

                        <!-- Recipients Info -->
                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <div class="flex items-center space-x-2">
                                <div class="text-blue-600">👥</div>
                                <h3 class="text-sm font-medium text-blue-900">Destinatari</h3>
                            </div>
                            <p class="text-sm text-blue-700 mt-2">
                                Questa newsletter sarà inviata a <strong>{{ $leadsCount }} lead iscritti</strong> nella tua lista.
                            </p>
                            @if($leadsCount === 0)
                                <p class="text-sm text-red-600 mt-2">
                                    ⚠️ Non hai ancora lead iscritti. La newsletter non potrà essere inviata.
                                </p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('store.newsletters.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition-colors">
                                Annulla
                            </a>

                            <div class="space-x-3">
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors">
                                    💾 Salva come Bozza
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal (we can implement this later) -->
    <div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <!-- Preview content would go here -->
    </div>
</x-store-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const textarea = document.getElementById('content');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
</script>
