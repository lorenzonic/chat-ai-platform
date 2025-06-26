<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📧 {{ $newsletter->title }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('store.newsletters.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                    ← Torna alla Lista
                </a>

                @if($newsletter->status === 'draft')
                    <a href="{{ route('store.newsletters.edit', $newsletter) }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors">
                        ✏️ Modifica
                    </a>
                @endif

                @if($newsletter->status === 'draft' && auth('store')->user()->is_premium && $leadsCount > 0)
                    <form action="{{ route('store.newsletters.send', $newsletter) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Sei sicuro di voler inviare questa newsletter a {{ $leadsCount }} destinatari?')"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors">
                            🚀 Invia Newsletter
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Newsletter Status -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($newsletter->status === 'sent')
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 text-xl">✓</span>
                                </div>
                            @elseif($newsletter->status === 'sending')
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 text-xl">↗</span>
                                </div>
                            @elseif($newsletter->status === 'scheduled')
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <span class="text-yellow-600 text-xl">⏰</span>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 text-xl">📝</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 capitalize">{{ $newsletter->status }}</h3>
                            <p class="text-sm text-gray-500">
                                @if($newsletter->sent_at)
                                    Inviata il {{ $newsletter->sent_at->format('d/m/Y alle H:i') }}
                                @elseif($newsletter->scheduled_at)
                                    Programmata per il {{ $newsletter->scheduled_at->format('d/m/Y alle H:i') }}
                                @else
                                    Creata il {{ $newsletter->created_at->format('d/m/Y alle H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($newsletter->status === 'sent')
                        <div class="text-right">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $newsletter->recipients_count }}</div>
                                    <div class="text-xs text-gray-500">Inviati</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $newsletter->open_rate }}%</div>
                                    <div class="text-xs text-gray-500">Aperture</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $newsletter->click_rate }}%</div>
                                    <div class="text-xs text-gray-500">Click</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Newsletter Preview -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Anteprima Newsletter</h3>
                </div>

                <div class="p-6">
                    <!-- Email Preview -->
                    <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Email Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6">
                            <h1 class="text-2xl font-bold">{{ auth('store')->user()->name }}</h1>
                            <p class="text-blue-100">Newsletter</p>
                        </div>

                        <!-- Email Content -->
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $newsletter->title }}</h2>

                            <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                                {!! nl2br(e($newsletter->content)) !!}
                            </div>

                            <!-- Images -->
                            @if($newsletter->images && count($newsletter->images) > 0)
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    @foreach($newsletter->images as $image)
                                        <img src="{{ $image }}" alt="Newsletter image" class="rounded-lg shadow-sm">
                                    @endforeach
                                </div>
                            @endif

                            <!-- CTA Button -->
                            @if($newsletter->cta_text && $newsletter->cta_url)
                                <div class="text-center mb-6">
                                    <a href="{{ $newsletter->cta_url }}"
                                       target="_blank"
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                        {{ $newsletter->cta_text }}
                                    </a>
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="border-t border-gray-200 pt-4 text-xs text-gray-500">
                                <p>© {{ date('Y') }} {{ auth('store')->user()->name }}. Tutti i diritti riservati.</p>
                                <p class="mt-2">
                                    Hai ricevuto questa email perché sei iscritto alla nostra newsletter.
                                    <a href="#" class="text-blue-600 hover:underline">Cancella iscrizione</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($newsletter->status === 'draft')
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="text-yellow-600 mr-3">⚠️</div>
                        <div class="flex-1">
                            <h4 class="text-yellow-800 font-medium">Newsletter in Bozza</h4>
                            <p class="text-yellow-700 text-sm mt-1">
                                Questa newsletter è ancora in bozza.
                                @if(!auth('store')->user()->is_premium)
                                    Effettua l'upgrade a Premium per inviare newsletter.
                                @elseif($leadsCount === 0)
                                    Non ci sono lead iscritti per ricevere la newsletter.
                                @else
                                    Puoi modificarla o inviarla a {{ $leadsCount }} destinatari.
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            @if(auth('store')->user()->is_premium && $leadsCount > 0)
                                <form action="{{ route('store.newsletters.send', $newsletter) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Confermi l\'invio della newsletter?')"
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                        🚀 Invia Ora
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('store.newsletters.edit', $newsletter) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                ✏️ Modifica
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
