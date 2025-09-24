<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Store Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @auth('store')
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('store.dashboard') }}" class="text-xl font-bold text-gray-800">
                                    {{ auth('store')->user()->name }}
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex sm:items-center">
                                <a href="{{ route('store.dashboard') }}"
                                   class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200
                                          {{ request()->routeIs('store.dashboard') ? 'text-green-900 border-black' : 'border-gray-300' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('store.chatbot.edit') }}"
                                   class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200
                                          {{ request()->routeIs('store.chatbot.*') ? 'text-blue-900 border-black' : 'border-gray-300' }}">
                                    Chatbot
                                </a>
                                <a href="{{ route('store.newsletters.leads') }}"
                                   class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200
                                          {{ request()->routeIs('store.newsletters.*') ? 'text-purple-900 border-black' : 'border-gray-300' }}">
                                    Newsletters
                                </a>
                                <a href="{{ route('store.analytics.index') }}"
                                   class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200
                                          {{ request()->routeIs('store.analytics.*') ? 'text-teal-900 border-black' : 'border-gray-300' }}">
                                    Analytics
                                </a>
                                <a href="{{ route('store.profile.show') }}"
                                   class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200
                                          {{ request()->routeIs('store.profile.*') ? 'text-indigo-900 border-black' : 'border-gray-300' }}">
                                    Profile
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <span class="text-gray-700 mr-4">{{ auth('store')->user()->email }}</span>
                            <form method="POST" action="{{ route('store.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
