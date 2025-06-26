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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Debug Page</h1>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-2">Laravel is Working!</h2>
                <p class="text-gray-600">Store: {{ $store->name }}</p>
                <p class="text-gray-600">Description: {{ $store->description ?? 'No description' }}</p>
            </div>

            <div class="bg-blue-100 rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2">Vue Component Test</h3>
                <div id="modern-chatbot">
                    <TestChatbot
                        :store="{{ json_encode($store) }}"
                        :prefilled-question="{{ json_encode(request()->get('question')) }}"
                        :ref-code="{{ json_encode(request()->get('ref')) }}"
                    />
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('Laravel page loaded');
        console.log('Store data:', @json($store));
        console.log('DOM element exists:', !!document.getElementById('modern-chatbot'));
    </script>
</body>
</html>
