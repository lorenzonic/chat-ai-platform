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

    <!-- Vite assets with intelligent fallback -->
    @php
        $viteManifestExists = file_exists(public_path('build/manifest.json'));
        $useVite = $viteManifestExists;
    @endphp

    @if ($useVite)
        <!-- Use Vite assets when manifest exists -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            window.ViteAssetsLoaded = true;
            console.log('✅ Using Vite assets');
        </script>
    @else
        <!-- Fallback to CDN when manifest not found -->
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            window.ViteAssetsLoaded = false;
            console.log('⚠️ Using CDN fallback - Vite manifest not found');
        </script>
        <style>
            /* Essential Tailwind-like styles for fallback */
            .bg-gradient-to-br { background: linear-gradient(to bottom right, #f8fafc, #e0f2fe, #e0e7ff); }
            .min-h-screen { min-height: 100vh; }
            .text-white { color: white; }
            .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
            .rounded-3xl { border-radius: 1.5rem; }
            .p-6 { padding: 1.5rem; }
            .bg-white { background-color: white; }
            .rounded-xl { border-radius: 0.75rem; }
            .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
            .border { border-width: 1px; }
            .border-gray-200 { border-color: #e5e7eb; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
            .flex { display: flex; }
            .items-center { align-items: center; }
            .justify-center { justify-content: center; }
            .space-x-4 > * + * { margin-left: 1rem; }
            .space-y-4 > * + * { margin-top: 1rem; }
            .max-w-4xl { max-width: 56rem; }
            .mx-auto { margin-left: auto; margin-right: auto; }
            .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
            .font-bold { font-weight: 700; }
            .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
            .font-semibold { font-weight: 600; }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .text-xs { font-size: 0.75rem; line-height: 1rem; }
            .text-gray-800 { color: #1f2937; }
            .text-gray-600 { color: #4b5563; }
            .text-gray-500 { color: #6b7280; }
            .bg-gray-300 { background-color: #d1d5db; }
            .bg-opacity-20 { --tw-bg-opacity: 0.2; }
            .backdrop-blur-md { backdrop-filter: blur(12px); }
            .rounded-2xl { border-radius: 1rem; }
            .w-14 { width: 3.5rem; }
            .h-14 { width: 3.5rem; }
            .w-10 { width: 2.5rem; }
            .h-10 { height: 2.5rem; }
            .w-8 { width: 2rem; }
            .h-8 { height: 2rem; }
            .w-5 { width: 1.25rem; }
            .h-5 { height: 1.25rem; }
            .w-4 { width: 1rem; }
            .h-4 { height: 1rem; }
            .w-3 { width: 0.75rem; }
            .h-3 { height: 0.75rem; }
            .w-2 { width: 0.5rem; }
            .h-2 { height: 0.5rem; }
            .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
            .animate-bounce { animation: bounce 1s infinite; }
            .animate-spin { animation: spin 1s linear infinite; }
            @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
            @keyframes bounce { 0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); } 50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); } }
            @keyframes spin { to { transform: rotate(360deg); } }
        </style>
    @endif

    <!-- Vue initialization script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking Vue availability...');
        
        // Function to initialize the chatbot
        function initializeChatbot() {
            const chatbotElement = document.getElementById('modern-chatbot');
            if (!chatbotElement) {
                console.error('Chatbot element not found');
                return;
            }

            console.log('Initializing Vue chatbot...');
            
            // Get data from element
            const storeData = chatbotElement.dataset.store ? JSON.parse(chatbotElement.dataset.store) : null;
            const prefilledQuestion = chatbotElement.dataset.prefilledQuestion || null;
            const refCode = chatbotElement.dataset.refCode || null;

            console.log('Store data:', storeData);
            
            if (!storeData) {
                console.error('Store data not found');
                return;
            }

            // Check if Vue is loaded from Vite or CDN
            const Vue = window.Vue;
            if (!Vue) {
                console.error('Vue not loaded');
                showError('Vue.js non caricato');
                return;
            }

            const { createApp } = Vue;
            
            // Create Vue app with inline template for maximum compatibility
            const ChatbotApp = {
                data() {
                    return {
                        store: storeData,
                        messages: [],
                        currentMessage: prefilledQuestion || '',
                        isLoading: false,
                        isInitialized: false
                    }
                },
                async mounted() {
                    console.log('Vue app mounted');
                    this.isInitialized = true;
                    
                    // Add welcome message
                    this.messages.push({
                        type: 'bot',
                        content: `Ciao! Sono ${this.store.assistant_name || 'l\'assistente AI'} di ${this.store.name}. Come posso aiutarti?`,
                        timestamp: new Date()
                    });
                    
                    // Auto-send prefilled question if present
                    if (this.currentMessage) {
                        setTimeout(() => {
                            this.sendMessage();
                        }, 1000);
                    }
                },
                methods: {
                    async sendMessage() {
                        if (!this.currentMessage.trim() || this.isLoading) return;

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
                                ref_code: refCode
                            }, {
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
                    },
                    formatTime(date) {
                        return date.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
                    }
                },
                template: \`
                    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100" 
                         :style="{ background: 'linear-gradient(to bottom right, #f8fafc, #e0f2fe, #e0e7ff)' }">
                        
                        <!-- Header -->
                        <header class="text-white shadow-xl" 
                                :style="{ background: store.chat_theme_color || '#10b981' }">
                            <div class="max-w-4xl mx-auto px-6 py-8">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center shadow-lg">
                                        <img v-if="store.chat_avatar_image" 
                                             :src="store.chat_avatar_image" 
                                             :alt="store.name"
                                             class="w-12 h-12 rounded-xl object-cover">
                                        <svg v-else class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-bold tracking-tight" 
                                            :style="{ fontFamily: store.chat_font_family || 'Inter' }">
                                            {{ store.name }}
                                        </h1>
                                        <p class="text-white text-opacity-90 text-base mt-1">
                                            {{ store.assistant_name || 'AI Assistant' }} • Assistente Virtuale
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </header>

                        <!-- Chat Container -->
                        <div class="max-w-4xl mx-auto p-6 -mt-4 relative z-10">
                            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-3xl shadow-2xl border border-white border-opacity-30 overflow-hidden">
                                
                                <!-- Chat Header -->
                                <div class="text-white p-5 relative overflow-hidden rounded-t-3xl"
                                     :style="{ background: store.chat_theme_color || '#10b981' }">
                                    <div class="relative z-10 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="text-lg font-semibold">{{ store.assistant_name || 'Assistente AI' }}</h2>
                                                <p class="text-sm text-white text-opacity-75">Online ora</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                            <span class="text-sm">Attivo</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages Area -->
                                <div ref="messagesContainer" class="h-96 overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-white to-gray-50">
                                    <div v-for="(message, index) in messages" :key="index"
                                         :class="message.type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                        
                                        <!-- Bot Message -->
                                        <div v-if="message.type === 'bot'" class="flex items-start space-x-3 max-w-xs lg:max-w-md">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                 :style="{ background: store.chat_theme_color || '#10b981' }">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                                </svg>
                                            </div>
                                            <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-md border border-gray-200">
                                                <p class="text-gray-800 text-sm leading-relaxed">{{ message.content }}</p>
                                                <p class="text-xs text-gray-500 mt-2">{{ formatTime(message.timestamp) }}</p>
                                            </div>
                                        </div>

                                        <!-- User Message -->
                                        <div v-else class="flex items-start space-x-3 max-w-xs lg:max-w-md">
                                            <div class="rounded-2xl rounded-tr-md px-4 py-3 shadow-md text-white"
                                                 :style="{ background: store.chat_theme_color || '#10b981' }">
                                                <p class="text-sm leading-relaxed">{{ message.content }}</p>
                                                <p class="text-xs text-white text-opacity-75 mt-2">{{ formatTime(message.timestamp) }}</p>
                                            </div>
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loading indicator -->
                                    <div v-if="isLoading" class="flex justify-start">
                                        <div class="flex items-start space-x-3 max-w-xs lg:max-w-md">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                 :style="{ background: store.chat_theme_color || '#10b981' }">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                                </svg>
                                            </div>
                                            <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-md border border-gray-200">
                                                <div class="flex space-x-1">
                                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Area -->
                                <div class="p-6 bg-white border-t border-gray-200">
                                    <div class="flex space-x-4">
                                        <input v-model="currentMessage"
                                               @keyup.enter="sendMessage"
                                               :disabled="isLoading"
                                               type="text"
                                               placeholder="Scrivi il tuo messaggio..."
                                               class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:border-transparent text-gray-700 placeholder-gray-500"
                                               :style="{ '--tw-ring-color': store.chat_theme_color || '#10b981' }">
                                        <button @click="sendMessage"
                                                :disabled="isLoading || !currentMessage.trim()"
                                                class="text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg"
                                                :style="{ background: store.chat_theme_color || '#10b981' }">
                                            <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span v-else>Invia</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                \`
            };

            try {
                const app = createApp(ChatbotApp);
                app.mount('#modern-chatbot');
                console.log('Vue chatbot mounted successfully');
            } catch (error) {
                console.error('Error mounting Vue app:', error);
                showError('Errore nell\'inizializzazione del chatbot');
            }
        }

        function showError(message) {
            const chatbotElement = document.getElementById('modern-chatbot');
            if (chatbotElement) {
                chatbotElement.innerHTML = \`
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="text-center bg-red-50 border border-red-200 rounded-lg p-8 max-w-md">
                            <div class="text-red-600 mb-4">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Errore</h3>
                            <p class="text-red-600 mb-4">\${message}</p>
                            <button onclick="window.location.reload()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                Ricarica pagina
                            </button>
                        </div>
                    </div>
                \`;
            }
        }

        // Try to initialize immediately if Vue is loaded
        if (window.Vue) {
            initializeChatbot();
        } else {
            // Wait for Vue to load (CDN case)
            let attempts = 0;
            const maxAttempts = 50; // 5 seconds
            const interval = setInterval(() => {
                attempts++;
                if (window.Vue) {
                    clearInterval(interval);
                    initializeChatbot();
                } else if (attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.error('Vue failed to load');
                    showError('Vue.js non è riuscito a caricarsi');
                }
            }, 100);
        }
    });
    </script>
</head>
<body class="font-sans antialiased">
    <div id="modern-chatbot"
         data-store='@json($store)'
         data-prefilled-question="{{ request('q') }}"
         data-ref-code="{{ request('ref') }}">
        <!-- Vue app will mount here -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Caricamento chatbot...</p>
            </div>
        </div>
    </div>
</body>
</html>
