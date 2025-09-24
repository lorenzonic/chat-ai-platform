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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @auth('store')
            <!-- Navigation -->
            <nav class="bg-green-600 border-b border-green-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('store.dashboard') }}" class="text-xl font-bold text-white">
                                    {{ auth('store')->user()->name }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <span class="text-green-100 mr-4">{{ auth('store')->user()->email }}</span>
                            <form method="POST" action="{{ route('store.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-100 hover:text-white">
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
