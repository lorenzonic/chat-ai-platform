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

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-green-600 text-white p-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold">{{ $store->name }}</h1>
                <p class="text-green-100">AI Assistant - Ask me anything!</p>
            </div>
        </header>

        <!-- Chat Container -->
        <div class="max-w-4xl mx-auto p-4">
            <div class="bg-white rounded-lg shadow-lg h-96 flex flex-col">
                <!-- Chat Messages -->
                <div class="flex-1 p-4 overflow-y-auto" id="chat-messages">
                    <div class="mb-4">
                        <div class="bg-green-100 rounded-lg p-3 max-w-xs">
                            <p class="text-sm">
                                Ciao! Sono l'assistente AI di {{ $store->name }}.
                                Come posso aiutarti oggi?
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Suggestions -->
                <div class="p-4 border-t bg-gray-50">
                    <p class="text-sm text-gray-600 mb-2">Suggerimenti veloci:</p>
                    <div class="flex flex-wrap gap-2">
                        <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-full suggestion-btn">
                            Che piante consigli?
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-full suggestion-btn">
                            Orari di apertura?
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-full suggestion-btn">
                            Come curarle?
                        </button>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="p-4 border-t">
                    <div class="flex space-x-2">
                        <input type="text"
                               id="message-input"
                               placeholder="Scrivi la tua domanda..."
                               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-green-500">
                        <button id="send-btn"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            Invia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Store Info -->
            <div class="mt-6 bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-bold mb-2">{{ $store->name }}</h2>
                @if($store->description)
                    <p class="text-gray-600">{{ $store->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        // AI Chatbot functionality with real API integration
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            const chatMessages = document.getElementById('chat-messages');
            const suggestionBtns = document.querySelectorAll('.suggestion-btn');

            // Get store slug and generate session ID
            const storeSlug = '{{ $store->slug }}';
            let sessionId = localStorage.getItem('chatbot_session_' + storeSlug) || generateSessionId();
            localStorage.setItem('chatbot_session_' + storeSlug, sessionId);

            // Check for pre-filled question from URL
            const urlParams = new URLSearchParams(window.location.search);
            const prefilledQuestion = urlParams.get('question');
            const refCode = urlParams.get('ref');

            if (prefilledQuestion) {
                messageInput.value = prefilledQuestion;
                // Track QR scan if ref code is present
                if (refCode) {
                    trackQrScan(refCode);
                }
            }

            function generateSessionId() {
                return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            }

            function addMessage(message, isUser = false, isLoading = false) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-4 ${isUser ? 'text-right' : ''}`;

                const messageContent = document.createElement('div');
                messageContent.className = `inline-block p-3 rounded-lg max-w-xs ${
                    isUser
                        ? 'bg-blue-500 text-white'
                        : isLoading
                            ? 'bg-gray-100 text-gray-600'
                            : 'bg-green-100 text-gray-800'
                }`;

                if (isLoading) {
                    messageContent.innerHTML = `<p class="text-sm">
                        <span class="inline-flex">
                            <span class="animate-pulse">Sto pensando</span>
                            <span class="animate-bounce">...</span>
                        </span>
                    </p>`;
                    messageContent.id = 'loading-message';
                } else {
                    messageContent.innerHTML = `<p class="text-sm">${message}</p>`;
                }

                messageDiv.appendChild(messageContent);
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                return messageDiv;
            }

            function removeLoadingMessage() {
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.parentElement.remove();
                }
            }

            async function sendMessage() {
                const message = messageInput.value.trim();
                if (!message) return;

                // Add user message
                addMessage(message, true);
                messageInput.value = '';
                sendBtn.disabled = true;

                // Add loading message
                addMessage('', false, true);

                try {
                    const response = await fetch(`/api/chatbot/${storeSlug}/message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({
                            message: message,
                            session_id: sessionId,
                            ref: refCode
                        })
                    });

                    const data = await response.json();

                    // Remove loading message
                    removeLoadingMessage();

                    if (data.success) {
                        addMessage(data.response, false);
                        // Update session ID if provided
                        if (data.session_id) {
                            sessionId = data.session_id;
                            localStorage.setItem('chatbot_session_' + storeSlug, sessionId);
                        }
                    } else {
                        addMessage(data.error || 'Si è verificato un errore. Riprova tra poco.', false);
                    }
                } catch (error) {
                    console.error('Chat error:', error);
                    removeLoadingMessage();
                    addMessage('Errore di connessione. Controlla la connessione internet e riprova.', false);
                } finally {
                    sendBtn.disabled = false;
                }
            }

            async function trackQrScan(refCode) {
                try {
                    await fetch(`/api/chatbot/${storeSlug}/track-scan`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({
                            ref: refCode
                        })
                    });
                } catch (error) {
                    console.log('QR tracking failed:', error);
                }
            }

            sendBtn.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            suggestionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    messageInput.value = this.textContent;
                    sendMessage();
                });
            });
        });
    </script>
</body>
</html>
