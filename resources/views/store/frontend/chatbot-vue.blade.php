<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $store->name }} - AI Chatbot</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google Fonts for chatbot -->
    @php
        $fontFamily = $store->chat_font_family ?? 'Inter';
        $fontUrl = 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', $fontFamily) . ':wght@300;400;500;600;700&display=swap';
    @endphp
    <link href="{{ $fontUrl }}" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Vue 3 and Axios from CDN - Always reliable -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- Initialize chatbot -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for Vue to load
        setTimeout(function() {
            if (window.Vue) {
                initChatbot();
            } else {
                console.error('Vue not loaded');
            }
        }, 100);
    });

    function initChatbot() {
        const { createApp } = Vue;

        const ChatbotApp = {
            data() {
                return {
                    messages: [],
                    currentMessage: '',
                    isLoading: false,
                    store: null
                }
            },
            async mounted() {
                // Get store data from element
                const element = document.getElementById('modern-chatbot');
                this.store = element.dataset.store ? JSON.parse(element.dataset.store) : null;

                // Prefilled question
                const prefilledQuestion = element.dataset.prefilledQuestion;
                if (prefilledQuestion) {
                    this.currentMessage = prefilledQuestion;
                }

                // Welcome message
                this.messages.push({
                    type: 'bot',
                    content: `Ciao! Sono l'assistente AI di ${this.store?.name || 'il nostro negozio'}. Come posso aiutarti?`,
                    timestamp: new Date()
                });
            },
            methods: {
                async sendMessage() {
                    if (!this.currentMessage.trim()) return;

                    const userMessage = this.currentMessage;
                    this.messages.push({
                        type: 'user',
                        content: userMessage,
                        timestamp: new Date()
                    });

                    this.currentMessage = '';
                    this.isLoading = true;

                    try {
                        const response = await axios.post(`/api/chatbot/${this.store.slug}`, {
                            message: userMessage,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        this.messages.push({
                            type: 'bot',
                            content: response.data.response || 'Risposta ricevuta',
                            timestamp: new Date()
                        });
                    } catch (error) {
                        console.error('Chat error:', error);
                        this.messages.push({
                            type: 'bot',
                            content: 'Mi dispiace, c\'è stato un errore. Riprova tra qualche istante.',
                            timestamp: new Date()
                        });
                    }

                    this.isLoading = false;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                },
                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }
            },
            template: \`
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden" style="height: 500px;">
                    <div class="bg-blue-600 text-white p-4">
                        <h3 class="font-semibold">{{ $store->name }} - AI Chat</h3>
                        <p class="text-sm opacity-90">Powered by Gemini AI</p>
                    </div>
                    <div ref="messagesContainer" class="h-80 overflow-y-auto p-4 space-y-3">
                        <div v-for="message in messages" :key="message.timestamp"
                             :class="message.type === 'user' ? 'text-right' : 'text-left'">
                            <div :class="message.type === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'"
                                 class="inline-block rounded-lg px-3 py-2 max-w-xs text-sm">
                                \{\{ message.content \}\}
                            </div>
                        </div>
                        <div v-if="isLoading" class="text-left">
                            <div class="bg-gray-200 text-gray-800 inline-block rounded-lg px-3 py-2">
                                <span class="animate-pulse">Sto scrivendo...</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex space-x-2">
                            <input v-model="currentMessage"
                                   @keyup.enter="sendMessage"
                                   type="text"
                                   placeholder="Scrivi un messaggio..."
                                   class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <button @click="sendMessage"
                                    :disabled="isLoading || !currentMessage.trim()"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm">
                                Invia
                            </button>
                        </div>
                    </div>
                </div>
            \`
        };

        createApp(ChatbotApp).mount('#modern-chatbot');
        console.log('Vue 3 Chatbot initialized successfully');
    }
    </script>
</head>
<body>
    <div
        id="modern-chatbot"
        data-store="{{ json_encode($store) }}"
        data-prefilled-question="{{ request()->get('question') }}"
        data-ref-code="{{ request()->get('ref') }}"
    >
        <!-- Vue app will be mounted here -->
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden mt-10" style="height: 500px;">
            <div class="bg-blue-600 text-white p-4">
                <h3 class="font-semibold">{{ $store->name }} - Caricamento...</h3>
            </div>
            <div class="h-80 overflow-y-auto p-4 flex items-center justify-center">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Caricamento chat...</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
