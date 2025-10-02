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

    <!-- Always use Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback script per debug in production -->
    <script>
        // Check if Vue loaded after 5 seconds
        setTimeout(function() {
            const chatbotElement = document.getElementById('modern-chatbot');
            if (chatbotElement && chatbotElement.innerHTML.includes('Caricamento chatbot')) {
                console.error('Vue chatbot failed to load - showing error message');
                chatbotElement.innerHTML = `
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="text-center bg-red-50 border border-red-200 rounded-lg p-8 max-w-md">
                            <div class="text-red-600 mb-4">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Errore di caricamento</h3>
                            <p class="text-red-600 mb-4">Il chatbot non è riuscito a caricarsi correttamente.</p>
                            <button onclick="window.location.reload()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                Ricarica pagina
                            </button>
                        </div>
                    </div>
                `;
            }
        }, 5000);
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
