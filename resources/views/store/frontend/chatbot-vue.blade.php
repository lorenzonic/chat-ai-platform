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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div
        id="modern-chatbot"
        data-store="{{ json_encode($store) }}"
        data-prefilled-question="{{ request()->get('question') }}"
        data-ref-code="{{ request()->get('ref') }}"
    >
        <!-- Vue app will be mounted here -->
    </div>
</body>
</html>
